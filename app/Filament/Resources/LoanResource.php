<?php

// app/Filament/Resources/LoanResource.php

namespace App\Filament\Resources;

use App\Enums\ItemCondition;
use App\Enums\ItemStatus;
use App\Enums\LoanStatus;
use App\Filament\Resources\LoanResource\Pages;
use App\Models\Item;
use App\Models\Loan;
use App\Models\Setting;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class LoanResource extends Resource
{
    protected static ?string $model = Loan::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path-rounded-square';

    protected static ?string $navigationLabel = 'Peminjaman';

    protected static ?string $modelLabel = 'Peminjaman';

    protected static ?string $pluralModelLabel = 'Peminjaman';

    protected static ?string $navigationGroup = 'Transaksi';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Peminjaman')
                    ->schema([
                        Forms\Components\TextInput::make('loan_number')
                            ->label('No. Peminjaman')
                            ->default(fn () => 'LN-'.date('Ymd').'-'.str_pad(Loan::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT))
                            ->disabled()
                            ->dehydrated()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        Forms\Components\Select::make('user_id')
                            ->label('Peminjam')
                            ->relationship('user', 'name')
                            ->searchable(['name', 'identity_number', 'email'])
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name} - {$record->identity_number}")
                            ->required()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function (Set $set, ?string $state) {
                                if ($state) {
                                    $user = \App\Models\User::find($state);
                                    if ($user && $user->is_suspended) {
                                        Notification::make()
                                            ->warning()
                                            ->title('User Disuspend')
                                            ->body('User ini sedang disuspend hingga '.($user->suspended_until ? $user->suspended_until->format('d/m/Y') : 'tanpa batas'))
                                            ->persistent()
                                            ->send();
                                    }

                                    // Check max loan limit
                                    $activeLoanCount = Loan::where('user_id', $state)
                                        ->where('status', LoanStatus::ACTIVE)
                                        ->count();

                                    $maxLoans = $user->max_loan_items ?? 3;

                                    if ($activeLoanCount >= $maxLoans) {
                                        Notification::make()
                                            ->danger()
                                            ->title('Batas Peminjaman')
                                            ->body("User sudah mencapai batas maksimal peminjaman ({$maxLoans} item)")
                                            ->persistent()
                                            ->send();
                                    }

                                    // Set default loan duration
                                    $duration = $user->loan_duration_days ?? Setting::where('key', 'loan_duration_days')->value('value') ?? 7;
                                    $set('due_date', now()->addDays($duration));
                                }
                            }),

                        Forms\Components\Select::make('item_id')
                            ->label('Barang')
                            ->options(function () {
                                return Item::where('status', ItemStatus::AVAILABLE)
                                    ->get()
                                    ->mapWithKeys(fn ($item) => [$item->id => "{$item->name} ({$item->item_code})"]);
                            })
                            ->searchable()
                            ->required()
                            ->preload()
                            ->helperText('Hanya menampilkan barang yang tersedia')
                            ->live(),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Jadwal Peminjaman')
                    ->schema([
                        Forms\Components\DateTimePicker::make('loan_date')
                            ->label('Tanggal Pinjam')
                            ->default(now())
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y H:i')
                            ->seconds(false),

                        Forms\Components\DateTimePicker::make('due_date')
                            ->label('Tanggal Jatuh Tempo')
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y H:i')
                            ->seconds(false)
                            ->minDate(fn (Get $get) => $get('loan_date'))
                            ->helperText('Tanggal maksimal pengembalian'),

                        Forms\Components\DateTimePicker::make('return_date')
                            ->label('Tanggal Kembali')
                            ->native(false)
                            ->displayFormat('d/m/Y H:i')
                            ->seconds(false)
                            ->hidden(fn (string $operation): bool => $operation === 'create'),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Status & Kondisi')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options(LoanStatus::class)
                            ->default(LoanStatus::ACTIVE)
                            ->required()
                            ->native(false)
                            ->live(),

                        Forms\Components\Select::make('returned_condition')
                            ->label('Kondisi Saat Kembali')
                            ->options(ItemCondition::class)
                            ->native(false)
                            ->visible(fn (Get $get): bool => in_array($get('status'), [LoanStatus::RETURNED->value, 'returned']))
                            ->required(fn (Get $get): bool => in_array($get('status'), [LoanStatus::RETURNED->value, 'returned'])),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Denda & Pembayaran')
                    ->schema([
                        Forms\Components\TextInput::make('penalty_amount')
                            ->label('Jumlah Denda')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0)
                            ->inputMode('decimal')
                            ->helperText('Denda keterlambatan akan dihitung otomatis'),

                        Forms\Components\Toggle::make('is_paid')
                            ->label('Sudah Dibayar?')
                            ->default(false)
                            ->visible(fn (Get $get): bool => (float) ($get('penalty_amount') ?? 0) > 0),
                    ])
                    ->columns(2)
                    ->visible(fn (string $operation): bool => $operation === 'edit'),

                Forms\Components\Section::make('Catatan')
                    ->schema([
                        Forms\Components\Textarea::make('loan_notes')
                            ->label('Catatan Peminjaman')
                            ->rows(2)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('return_notes')
                            ->label('Catatan Pengembalian')
                            ->rows(2)
                            ->columnSpanFull()
                            ->hidden(fn (string $operation): bool => $operation === 'create'),
                    ]),

                Forms\Components\Section::make('Audit Trail')
                    ->schema([
                        Forms\Components\Select::make('created_by')
                            ->label('Diproses Oleh')
                            ->relationship('createdBy', 'name')
                            ->default(Auth::id())
                            ->required()
                            ->disabled()
                            ->dehydrated(),

                        Forms\Components\Select::make('returned_by')
                            ->label('Dikembalikan Ke')
                            ->relationship('returnedBy', 'name')
                            ->searchable()
                            ->preload()
                            ->hidden(fn (string $operation): bool => $operation === 'create'),

                        Forms\Components\Select::make('approved_by')
                            ->label('Disetujui Oleh')
                            ->relationship('approvedBy', 'name')
                            ->searchable()
                            ->preload()
                            ->hidden(fn (string $operation): bool => $operation === 'create'),
                    ])
                    ->columns(3)
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('loan_number')
                    ->label('No. Pinjam')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight(FontWeight::Bold),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Peminjam')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Loan $record): string => $record->user->identity_number ?? '-'),

                Tables\Columns\TextColumn::make('item.name')
                    ->label('Barang')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Loan $record): string => $record->item->item_code ?? '-')
                    ->wrap(),

                Tables\Columns\TextColumn::make('loan_date')
                    ->label('Tgl Pinjam')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('due_date')
                    ->label('Jatuh Tempo')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->color(
                        fn (Loan $record): string => $record->status === LoanStatus::ACTIVE && $record->due_date < now()
                            ? 'danger'
                            : 'gray'
                    )
                    ->description(
                        fn (Loan $record): ?string => $record->status === LoanStatus::ACTIVE && $record->due_date < now()
                            ? 'Terlambat '.now()->diffInDays($record->due_date).' hari'
                            : null
                    ),

                Tables\Columns\TextColumn::make('return_date')
                    ->label('Tgl Kembali')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('penalty_amount')
                    ->label('Denda')
                    ->money('IDR')
                    ->sortable()
                    ->color(
                        fn (Loan $record): string => $record->penalty_amount > 0 && ! $record->is_paid ? 'danger' : 'success'
                    )
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->money('IDR')
                            ->label('Total'),
                    ]),

                Tables\Columns\IconColumn::make('is_paid')
                    ->label('Lunas')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('createdBy.name')
                    ->label('Diproses')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options(LoanStatus::class)
                    ->multiple()
                    ->preload(),

                Tables\Filters\SelectFilter::make('user_id')
                    ->label('Peminjam')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('overdue')
                    ->label('Terlambat')
                    ->query(
                        fn (Builder $query): Builder => $query->where('status', LoanStatus::ACTIVE)
                            ->where('due_date', '<', now())
                    )
                    ->toggle(),

                Tables\Filters\Filter::make('unpaid_penalty')
                    ->label('Denda Belum Lunas')
                    ->query(
                        fn (Builder $query): Builder => $query->where('penalty_amount', '>', 0)
                            ->where('is_paid', false)
                    )
                    ->toggle(),

                Tables\Filters\Filter::make('date_range')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('loan_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('loan_date', '<=', $date),
                            );
                    }),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),

                Tables\Actions\Action::make('return')
                    ->label('Kembalikan')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('success')
                    ->visible(fn (Loan $record): bool => $record->status === LoanStatus::ACTIVE)
                    ->form([
                        Forms\Components\DateTimePicker::make('return_date')
                            ->label('Tanggal Kembali')
                            ->default(now())
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y H:i')
                            ->seconds(false),

                        Forms\Components\Select::make('returned_condition')
                            ->label('Kondisi Barang')
                            ->options(ItemCondition::class)
                            ->required()
                            ->native(false)
                            ->default(ItemCondition::GOOD),

                        Forms\Components\Textarea::make('return_notes')
                            ->label('Catatan')
                            ->rows(3),
                    ])
                    ->action(function (Loan $record, array $data): void {
                        // Calculate penalty if overdue
                        $penaltyAmount = 0;
                        if (Carbon::parse($data['return_date'])->gt($record->due_date)) {
                            $daysLate = Carbon::parse($data['return_date'])->diffInDays($record->due_date);
                            $penaltyPerDay = Setting::where('key', 'penalty_per_day')->value('value') ?? 5000;
                            $penaltyAmount = $daysLate * $penaltyPerDay;
                        }

                        $record->update([
                            'return_date' => $data['return_date'],
                            'returned_condition' => $data['returned_condition'],
                            'return_notes' => $data['return_notes'],
                            'status' => LoanStatus::RETURNED,
                            'returned_by' => Auth::id(),
                            'penalty_amount' => $penaltyAmount,
                        ]);

                        // Update item status and condition
                        $record->item->update([
                            'status' => ItemStatus::AVAILABLE,
                            'condition' => $data['returned_condition'],
                        ]);

                        Notification::make()
                            ->success()
                            ->title('Barang Berhasil Dikembalikan')
                            ->body(
                                $penaltyAmount > 0
                                    ? 'Denda keterlambatan: Rp '.number_format($penaltyAmount, 0, ',', '.')
                                    : 'Tidak ada denda'
                            )
                            ->send();
                    })
                    ->successNotificationTitle('Barang berhasil dikembalikan'),

                Tables\Actions\Action::make('markAsPaid')
                    ->label('Tandai Lunas')
                    ->icon('heroicon-o-currency-dollar')
                    ->color('warning')
                    ->visible(fn (Loan $record): bool => $record->penalty_amount > 0 && ! $record->is_paid)
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Pembayaran Denda')
                    ->modalDescription(
                        fn (Loan $record): string => 'Apakah denda sebesar Rp '.number_format($record->penalty_amount, 0, ',', '.').' sudah dibayar?'
                    )
                    ->action(function (Loan $record): void {
                        $record->update(['is_paid' => true]);

                        Notification::make()
                            ->success()
                            ->title('Pembayaran Dikonfirmasi')
                            ->body('Denda telah ditandai sebagai lunas')
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLoans::route('/'),
            'create' => Pages\CreateLoan::route('/create'),
            // 'view' => Pages\ViewLoan::route('/{record}'),
            'edit' => Pages\EditLoan::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', LoanStatus::ACTIVE)->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        $overdueCount = static::getModel()::where('status', LoanStatus::ACTIVE)
            ->where('due_date', '<', now())
            ->count();

        return $overdueCount > 0 ? 'danger' : 'info';
    }
}
