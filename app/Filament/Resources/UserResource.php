<?php
// app/Filament/Resources/UserResource.php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Enums\UserRole;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\UserResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UserResource\RelationManagers;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Pengguna';

    protected static ?string $modelLabel = 'Pengguna';

    protected static ?string $pluralModelLabel = 'Pengguna';

    protected static ?string $navigationGroup = 'Manajemen Akses';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Akun')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        Forms\Components\TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->dehydrateStateUsing(fn($state) => Hash::make($state))
                            ->dehydrated(fn($state) => filled($state))
                            ->required(fn(string $operation): bool => $operation === 'create')
                            ->maxLength(255)
                            ->revealable(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Informasi Identitas')
                    ->schema([
                        Forms\Components\TextInput::make('identity_number')
                            ->label('NISN / NIP')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->helperText('NISN untuk siswa, NIP untuk guru/staff'),

                        Forms\Components\Select::make('role')
                            ->label('Role')
                            ->options(UserRole::class)
                            ->default(UserRole::STUDENT)
                            ->required()
                            ->native(false)
                            ->live(),

                        Forms\Components\TextInput::make('phone')
                            ->label('Nomor Telepon')
                            ->tel()
                            ->maxLength(255),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Informasi Akademik')
                    ->schema([
                        Forms\Components\TextInput::make('department')
                            ->label('Jurusan / Jabatan')
                            ->maxLength(255)
                            ->helperText('Jurusan untuk siswa, Jabatan untuk guru/staff'),

                        Forms\Components\TextInput::make('class')
                            ->label('Kelas')
                            ->maxLength(255)
                            ->visible(fn(Forms\Get $get): bool => $get('role') === UserRole::STUDENT->value)
                            ->helperText('Contoh: X IPA 1'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Pengaturan Peminjaman')
                    ->schema([
                        Forms\Components\TextInput::make('max_loan_items')
                            ->label('Maksimal Peminjaman')
                            ->numeric()
                            ->default(3)
                            ->minValue(1)
                            ->maxValue(10)
                            ->required()
                            ->helperText('Jumlah maksimal barang yang dapat dipinjam'),

                        Forms\Components\TextInput::make('loan_duration_days')
                            ->label('Durasi Peminjaman (Hari)')
                            ->numeric()
                            ->default(7)
                            ->minValue(1)
                            ->maxValue(30)
                            ->required()
                            ->helperText('Durasi default peminjaman'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Status Suspend')
                    ->schema([
                        Forms\Components\Toggle::make('is_suspended')
                            ->label('Suspend User')
                            ->default(false)
                            ->live()
                            ->helperText('Blokir user dari melakukan peminjaman'),

                        Forms\Components\DateTimePicker::make('suspended_until')
                            ->label('Suspend Hingga')
                            ->native(false)
                            ->displayFormat('d/m/Y H:i')
                            ->seconds(false)
                            ->visible(fn(Forms\Get $get): bool => $get('is_suspended'))
                            ->helperText('Kosongkan untuk suspend permanent'),

                        Forms\Components\Textarea::make('suspension_reason')
                            ->label('Alasan Suspend')
                            ->rows(3)
                            ->visible(fn(Forms\Get $get): bool => $get('is_suspended'))
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('identity_number')
                    ->label('NISN/NIP')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable()
                    ->description(fn(User $record): string => $record->email),

                Tables\Columns\TextColumn::make('role')
                    ->label('Role')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('department')
                    ->label('Jurusan/Jabatan')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('class')
                    ->label('Kelas')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Telepon')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\IconColumn::make('rfidCard.is_active')
                    ->label('RFID Card')
                    ->boolean()
                    ->trueIcon('heroicon-o-credit-card')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->default(false)
                    ->getStateUsing(fn(User $record): bool => $record->rfidCard !== null && $record->rfidCard->is_active),

                Tables\Columns\TextColumn::make('active_loans_count')
                    ->label('Pinjaman Aktif')
                    ->counts('loans', fn(Builder $query) => $query->where('status', \App\Enums\LoanStatus::ACTIVE))
                    ->badge()
                    ->color(
                        fn(int $state, User $record): string =>
                        $state >= $record->max_loan_items ? 'danger' : ($state > 0 ? 'warning' : 'success')
                    )
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_suspended')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-no-symbol')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('danger')
                    ->falseColor('success')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Terdaftar')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->label('Role')
                    ->options(UserRole::class)
                    ->multiple()
                    ->preload(),

                Tables\Filters\TernaryFilter::make('is_suspended')
                    ->label('Status')
                    ->placeholder('Semua')
                    ->trueLabel('Suspended')
                    ->falseLabel('Aktif'),

                Tables\Filters\Filter::make('has_rfid')
                    ->label('Punya Kartu RFID')
                    ->query(fn(Builder $query): Builder => $query->has('rfidCard'))
                    ->toggle(),

                Tables\Filters\Filter::make('has_active_loans')
                    ->label('Punya Pinjaman Aktif')
                    ->query(
                        fn(Builder $query): Builder =>
                        $query->whereHas('loans', fn($q) => $q->where('status', \App\Enums\LoanStatus::ACTIVE))
                    )
                    ->toggle(),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),

                Tables\Actions\Action::make('suspend')
                    ->label('Suspend')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->visible(fn(User $record): bool => !$record->is_suspended)
                    ->form([
                        Forms\Components\DateTimePicker::make('suspended_until')
                            ->label('Suspend Hingga')
                            ->native(false)
                            ->displayFormat('d/m/Y H:i')
                            ->helperText('Kosongkan untuk suspend permanent'),

                        Forms\Components\Textarea::make('suspension_reason')
                            ->label('Alasan Suspend')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (User $record, array $data): void {
                        $record->update([
                            'is_suspended' => true,
                            'suspended_until' => $data['suspended_until'] ?? null,
                            'suspension_reason' => $data['suspension_reason'],
                        ]);
                    }),

                Tables\Actions\Action::make('unsuspend')
                    ->label('Aktifkan')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn(User $record): bool => $record->is_suspended)
                    ->requiresConfirmation()
                    ->action(function (User $record): void {
                        $record->update([
                            'is_suspended' => false,
                            'suspended_until' => null,
                            'suspension_reason' => null,
                        ]);
                    }),

                Tables\Actions\DeleteAction::make(),
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
            RelationManagers\LoansRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            // 'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
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
        return static::getModel()::where('is_suspended', true)->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $count = static::getModel()::where('is_suspended', true)->count();
        return $count > 0 ? 'danger' : null;
    }
}
