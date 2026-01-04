// app/Http/Controllers/Api/RfidController.php
<?php

namespace App\Http\Controllers\Api;

use App\Models\Item;
use App\Models\Loan;
use App\Models\RfidLog;
use App\Models\RfidCard;
use App\Enums\ItemStatus;
use App\Enums\LoanStatus;
use App\Enums\ItemCondition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class RfidController extends Controller
{
    public function tap(Request $request)
    {
        $request->validate([
            'uid' => 'required|string',
        ]);

        $card = RfidCard::where('uid', $request->uid)->first();

        if (!$card) {
            RfidLog::create([
                'rfid_card_id' => null,
                'action' => 'tap',
                'status' => 'failed',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'response_message' => 'Card not found',
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Kartu tidak terdaftar',
            ], 404);
        }

        if (!$card->is_active || $card->isExpired()) {
            RfidLog::create([
                'rfid_card_id' => $card->id,
                'user_id' => $card->user_id,
                'action' => 'tap',
                'status' => 'failed',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'response_message' => 'Card inactive or expired',
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Kartu tidak aktif atau kadaluarsa',
            ], 403);
        }

        if ($card->user && $card->user->is_suspended) {
            RfidLog::create([
                'rfid_card_id' => $card->id,
                'user_id' => $card->user_id,
                'action' => 'tap',
                'status' => 'suspended',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'response_message' => 'User suspended',
            ]);

            return response()->json([
                'success' => false,
                'message' => 'User sedang disuspend',
                'suspended_until' => $card->user->suspended_until,
            ], 403);
        }

        // Update last used
        $card->update([
            'last_used_at' => now(),
            'last_used_for' => 'tap',
        ]);

        RfidLog::create([
            'rfid_card_id' => $card->id,
            'user_id' => $card->user_id,
            'action' => 'tap',
            'status' => 'success',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'response_message' => 'Tap successful',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tap berhasil',
            'user' => $card->user ? [
                'name' => $card->user->name,
                'identity_number' => $card->user->identity_number,
                'role' => $card->user->role->label(),
            ] : null,
        ]);
    }

    public function loan(Request $request)
    {
        $request->validate([
            'uid' => 'required|string',
            'item_code' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            $card = RfidCard::where('uid', $request->uid)
                ->where('is_active', true)
                ->first();

            if (!$card || !$card->user) {
                throw new \Exception('Kartu tidak valid atau tidak terhubung dengan user');
            }

            $user = $card->user;

            if ($user->is_suspended) {
                throw new \Exception('User sedang disuspend');
            }

            $item = Item::where('item_code', $request->item_code)
                ->where('status', ItemStatus::AVAILABLE)
                ->first();

            if (!$item) {
                throw new \Exception('Barang tidak tersedia');
            }

            // Check active loans
            $activeLoans = Loan::where('user_id', $user->id)
                ->where('status', LoanStatus::ACTIVE)
                ->count();

            if ($activeLoans >= $user->max_loan_items) {
                throw new \Exception('Batas peminjaman maksimal tercapai');
            }

            // Create loan
            $loan = Loan::create([
                'loan_number' => 'LN-' . date('Ymd') . '-' . str_pad(Loan::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT),
                'user_id' => $user->id,
                'item_id' => $item->id,
                'loan_date' => now(),
                'due_date' => now()->addDays($user->loan_duration_days),
                'status' => LoanStatus::ACTIVE,
                'created_by' => $user->id,
            ]);

            // Update item status
            $item->update(['status' => ItemStatus::LOANED]);

            // Update card
            $card->update([
                'last_used_at' => now(),
                'last_used_for' => 'loan',
            ]);

            // Log
            RfidLog::create([
                'rfid_card_id' => $card->id,
                'user_id' => $user->id,
                'action' => 'loan',
                'status' => 'success',
                'item_id' => $item->id,
                'loan_id' => $loan->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'response_message' => 'Loan created successfully',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Peminjaman berhasil',
                'loan' => [
                    'loan_number' => $loan->loan_number,
                    'item' => $item->name,
                    'due_date' => $loan->due_date->format('d/m/Y'),
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            if (isset($card)) {
                RfidLog::create([
                    'rfid_card_id' => $card->id,
                    'user_id' => $card->user_id ?? null,
                    'action' => 'loan',
                    'status' => 'failed',
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'response_message' => $e->getMessage(),
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function return(Request $request)
    {
        $request->validate([
            'uid' => 'required|string',
            'loan_number' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            $card = RfidCard::where('uid', $request->uid)
                ->where('is_active', true)
                ->first();

            if (!$card) {
                throw new \Exception('Kartu tidak valid');
            }

            $loan = Loan::where('loan_number', $request->loan_number)
                ->where('status', LoanStatus::ACTIVE)
                ->first();

            if (!$loan) {
                throw new \Exception('Peminjaman tidak ditemukan atau sudah dikembalikan');
            }

            // Calculate penalty if overdue
            $penaltyAmount = 0;
            if (now()->gt($loan->due_date)) {
                $daysLate = now()->diffInDays($loan->due_date);
                $penaltyPerDay = \App\Models\Setting::getValue('penalty_per_day', 5000);
                $penaltyAmount = $daysLate * $penaltyPerDay;
            }

            // Update loan
            $loan->update([
                'return_date' => now(),
                'returned_condition' => $request->condition ?? ItemCondition::GOOD,
                'status' => LoanStatus::RETURNED,
                'returned_by' => $card->user_id,
                'penalty_amount' => $penaltyAmount,
            ]);

            // Update item
            $loan->item->update([
                'status' => ItemStatus::AVAILABLE,
                'condition' => $request->condition ?? ItemCondition::GOOD,
            ]);

            // Update card
            $card->update([
                'last_used_at' => now(),
                'last_used_for' => 'return',
            ]);

            // Log
            RfidLog::create([
                'rfid_card_id' => $card->id,
                'user_id' => $card->user_id,
                'action' => 'return',
                'status' => 'success',
                'item_id' => $loan->item_id,
                'loan_id' => $loan->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'response_message' => 'Return successful',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pengembalian berhasil',
                'penalty' => $penaltyAmount > 0 ? [
                    'amount' => $penaltyAmount,
                    'days_late' => now()->diffInDays($loan->due_date),
                ] : null,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            if (isset($card)) {
                RfidLog::create([
                    'rfid_card_id' => $card->id,
                    'user_id' => $card->user_id ?? null,
                    'action' => 'return',
                    'status' => 'failed',
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'response_message' => $e->getMessage(),
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
