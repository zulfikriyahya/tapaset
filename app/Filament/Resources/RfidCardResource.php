<?php

// app/Filament/Resources/RfidCardResource.php

namespace App\Filament\Resources;

use App\Filament\Resources\RfidCardResource\Pages;
use App\Models\RfidCard;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;

class RfidCardResource extends Resource
{
    protected static ?string $model = RfidCard::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationLabel = 'Kartu RFID';

    protected static ?string $modelLabel = 'Kartu RFID';

    protected static ?string $pluralModelLabel = 'Kartu RFID';

    protected static ?string $navigationGroup = 'Manajemen Akses';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Kartu')
                    ->schema([
                        Forms\Components\TextInput::make('uid')
                            ->label('UID Kartu')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->helperText('UID unik dari kartu RFID')
                            ->suffixIcon('heroicon-m-identification')
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('card_number')
                            ->label('Nomor Kartu')
                            ->maxLength(255)
                            ->helperText('Nomor yang tercetak di kartu fisik (opsional)'),

                        Forms\Components\Select::make('user_id')
                            ->label('Pemilik Kartu')
                            ->relationship('user', 'name')
                            ->searchable(['name', 'identity_number', 'email'])
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name} ({$record->identity_number})")
                            ->preload()
                            ->nullable()
                            ->helperText('Kosongkan jika kartu belum di-assign'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Status & Validitas')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Kartu Aktif')
                            ->default(true)
                            ->helperText('Nonaktifkan untuk memblokir kartu'),

                        Forms\Components\DateTimePicker::make('issued_at')
                            ->label('Tanggal Terbit')
                            ->default(now())
                            ->native(false)
                            ->displayFormat('d/m/Y H:i')
                            ->seconds(false),

                        Forms\Components\DateTimePicker::make('expired_at')
                            ->label('Tanggal Kadaluarsa')
                            ->native(false)
                            ->displayFormat('d/m/Y H:i')
                            ->seconds(false)
                            ->helperText('Kosongkan untuk masa berlaku tanpa batas'),

                        Forms\Components\TextInput::make('failed_attempts')
                            ->label('Percobaan Gagal')
                            ->numeric()
                            ->default(0)
                            ->disabled()
                            ->dehydrated()
                            ->helperText('Counter untuk deteksi aktivitas mencurigakan'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Riwayat Penggunaan')
                    ->schema([
                        Forms\Components\DateTimePicker::make('last_used_at')
                            ->label('Terakhir Digunakan')
                            ->native(false)
                            ->displayFormat('d/m/Y H:i')
                            ->disabled()
                            ->dehydrated(),

                        Forms\Components\TextInput::make('last_used_for')
                            ->label('Digunakan Untuk')
                            ->maxLength(255)
                            ->disabled()
                            ->dehydrated()
                            ->helperText('Aktivitas terakhir: loan/return'),
                    ])
                    ->columns(2)
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('uid')
                    ->label('UID Kartu')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight(FontWeight::Bold),

                Tables\Columns\TextColumn::make('card_number')
                    ->label('No. Kartu')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Pemilik')
                    ->searchable()
                    ->sortable()
                    ->description(
                        fn (RfidCard $record): string => $record->user ? "{$record->user->identity_number} - {$record->user->role->label()}" : 'Belum di-assign'
                    )
                    ->placeholder('Belum di-assign')
                    ->color(fn (RfidCard $record): string => $record->user ? 'success' : 'gray'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),

                Tables\Columns\TextColumn::make('issued_at')
                    ->label('Tgl Terbit')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('expired_at')
                    ->label('Kadaluarsa')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->placeholder('Tidak terbatas')
                    ->color(
                        fn (RfidCard $record): string => $record->expired_at && $record->expired_at < now() ? 'danger' : 'success'
                    )
                    ->toggleable(),

                Tables\Columns\TextColumn::make('last_used_at')
                    ->label('Terakhir Digunakan')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->since()
                    ->placeholder('Belum pernah'),

                Tables\Columns\TextColumn::make('failed_attempts')
                    ->label('Gagal')
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->color(
                        fn (RfidCard $record): string => $record->failed_attempts > 5 ? 'danger' : ($record->failed_attempts > 0 ? 'warning' : 'success')
                    )
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user_id')
                    ->label('Pemilik')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Kartu')
                    ->placeholder('Semua')
                    ->trueLabel('Aktif')
                    ->falseLabel('Nonaktif'),

                Tables\Filters\TernaryFilter::make('assigned')
                    ->label('Assignment')
                    ->placeholder('Semua')
                    ->trueLabel('Sudah di-assign')
                    ->falseLabel('Belum di-assign')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('user_id'),
                        false: fn (Builder $query) => $query->whereNull('user_id'),
                    ),

                Tables\Filters\Filter::make('expired')
                    ->label('Kadaluarsa')
                    ->query(
                        fn (Builder $query): Builder => $query->whereNotNull('expired_at')
                            ->where('expired_at', '<', now())
                    )
                    ->toggle(),

                Tables\Filters\Filter::make('suspicious')
                    ->label('Aktivitas Mencurigakan')
                    ->query(
                        fn (Builder $query): Builder => $query->where('failed_attempts', '>', 5)
                    )
                    ->toggle(),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),

                Tables\Actions\Action::make('assign')
                    ->label('Assign User')
                    ->icon('heroicon-o-user-plus')
                    ->color('info')
                    ->visible(fn (RfidCard $record): bool => ! $record->user_id)
                    ->form([
                        Forms\Components\Select::make('user_id')
                            ->label('Pilih User')
                            ->relationship('user', 'name')
                            ->searchable(['name', 'identity_number', 'email'])
                            ->getOptionLabelFromRecordUsing(
                                fn ($record) => "{$record->name} ({$record->identity_number}) - {$record->role->label()}"
                            )
                            ->required()
                            ->preload(),
                    ])
                    ->action(function (RfidCard $record, array $data): void {
                        $record->update($data);
                    }),

                Tables\Actions\Action::make('unassign')
                    ->label('Lepas User')
                    ->icon('heroicon-o-user-minus')
                    ->color('warning')
                    ->visible(fn (RfidCard $record): bool => $record->user_id !== null)
                    ->requiresConfirmation()
                    ->modalHeading('Lepas User dari Kartu')
                    ->modalDescription('Apakah Anda yakin ingin melepas user dari kartu ini?')
                    ->action(function (RfidCard $record): void {
                        $record->update(['user_id' => null]);
                    }),

                Tables\Actions\Action::make('resetFailedAttempts')
                    ->label('Reset Counter')
                    ->icon('heroicon-o-arrow-path')
                    ->color('success')
                    ->visible(fn (RfidCard $record): bool => $record->failed_attempts > 0)
                    ->requiresConfirmation()
                    ->action(function (RfidCard $record): void {
                        $record->update(['failed_attempts' => 0]);
                    }),

                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Aktifkan')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn (Collection $records) => $records->each->update(['is_active' => true])),

                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Nonaktifkan')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(fn (Collection $records) => $records->each->update(['is_active' => false])),

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
            'index' => Pages\ListRfidCards::route('/'),
            'create' => Pages\CreateRfidCard::route('/create'),
            // 'view' => Pages\ViewRfidCard::route('/{record}'),
            'edit' => Pages\EditRfidCard::route('/{record}/edit'),
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
        return static::getModel()::whereNull('user_id')->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}
