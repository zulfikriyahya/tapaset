<?php
// database/migrations/2024_01_01_000007_create_rfid_logs_table.php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rfid_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rfid_card_id')->constrained('rfid_cards')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('action');
            $table->string('status');
            $table->foreignId('item_id')->nullable()->constrained('items')->onDelete('set null');
            $table->foreignId('loan_id')->nullable()->constrained('loans')->onDelete('set null');
            $table->string('location')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->text('response_message')->nullable();
            $table->timestamps();

            $table->index('rfid_card_id');
            $table->index('user_id');
            $table->index('action');
            $table->index('status');
            $table->index(['rfid_card_id', 'created_at']);
            $table->index(['user_id', 'action', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rfid_logs');
    }
};
