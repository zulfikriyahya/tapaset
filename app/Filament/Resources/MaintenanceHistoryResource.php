<?php
// app/Filament/Resources/MaintenanceHistoryResource.php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\MaintenanceHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\MaintenanceHistoryResource\Pages;

class MaintenanceHistoryResource extends Resource
{
    protected static ?string $model = MaintenanceHistory::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static ?string $navigationLabel = 'Maintenance';

    protected static ?string $modelLabel = 'Riwayat Maintenance';

    protected static ?string $pluralModelLabel = 'Riwayat Maintenance';

    protected static ?string $navigationGroup = 'Laporan';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Maintenance')
                    ->schema([
                        Forms\Components\Select::make('item_id')
                            ->label('Barang')
                            ->relationship('item', 'name')
                            ->searchable(['name', 'item_code'])
                            ->getOptionLabelFromRecordUsing(fn($record) => "{$record->name} ({$record->item_code})")
                            ->required()
                            ->preload(),

                        Forms\Components\Select::make('maintenance_type')
                            ->label('Jenis Maintenance')
                            ->options([
                                'repair' => 'Perbaikan',
                                'service' => 'Servis Rutin',
                                'inspection' => 'Inspeksi',
                                'cleaning' => 'Pembersihan',
                                'calibration' => 'Kalibrasi',
                            ])
                            ->required()
                            ->native(false),

                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'pending' => 'Pending',
                                'in_progress' => 'Sedang Dikerjakan',
                                'completed' => 'Selesai',
                                'cancelled' => 'Dibatalkan',
                            ])
                            ->default('pending')
                            ->required()
                            ->native(false)
                            ->live(),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Detail Maintenance')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi Masalah/Pekerjaan')
                            ->required()
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('performed_by')
                            ->label('Dikerjakan Oleh')
                            ->maxLength(255)
                            ->helperText('Nama teknisi/vendor yang mengerjakan'),

                        Forms\Components\TextInput::make('cost')
                            ->label('Biaya')
                            ->numeric()
                            ->prefix('Rp')
                            ->inputMode('decimal'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Jadwal')
                    ->schema([
                        Forms\Components\DateTimePicker::make('performed_at')
                            ->label('Tanggal Mulai')
                            ->default(now())
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y H:i')
                            ->seconds(false),

                        Forms\Components\DateTimePicker::make('completed_at')
                            ->label('Tanggal Selesai')
                            ->native(false)
                            ->displayFormat('d/m/Y H:i')
                            ->seconds(false)
                            ->visible(
                                fn(Forms\Get $get): bool =>
                                in_array($get('status'), ['completed', 'cancelled'])
                            ),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Catatan Tambahan')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Select::make('created_by')
                            ->label('Dicatat Oleh')
                            ->relationship('creator', 'name')
                            ->default(Auth::id())
                            ->required()
                            ->disabled()
                            ->dehydrated(),
                    ])
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('item.name')
                    ->label('Barang')
                    ->searchable()
                    ->sortable()
                    ->description(fn(MaintenanceHistory $record): string => $record->item->item_code),

                Tables\Columns\TextColumn::make('maintenance_type')
                    ->label('Jenis')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'repair' => 'Perbaikan',
                        'service' => 'Servis Rutin',
                        'inspection' => 'Inspeksi',
                        'cleaning' => 'Pembersihan',
                        'calibration' => 'Kalibrasi',
                        default => $state,
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'repair' => 'danger',
                        'service' => 'info',
                        'inspection' => 'warning',
                        'cleaning' => 'success',
                        'calibration' => 'primary',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('description')
                    ->label('Deskripsi')
                    ->limit(40)
                    ->wrap(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'pending' => 'Pending',
                        'in_progress' => 'Dikerjakan',
                        'completed' => 'Selesai',
                        'cancelled' => 'Dibatalkan',
                        default => $state,
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'in_progress' => 'info',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('performed_by')
                    ->label('Teknisi')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('cost')
                    ->label('Biaya')
                    ->money('IDR')
                    ->sortable()
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->money('IDR')
                            ->label('Total'),
                    ]),

                Tables\Columns\TextColumn::make('performed_at')
                    ->label('Tgl Mulai')
                    ->dateTime('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('completed_at')
                    ->label('Tgl Selesai')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('creator.name')
                    ->label('Dicatat Oleh')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('maintenance_type')
                    ->label('Jenis')
                    ->options([
                        'repair' => 'Perbaikan',
                        'service' => 'Servis Rutin',
                        'inspection' => 'Inspeksi',
                        'cleaning' => 'Pembersihan',
                        'calibration' => 'Kalibrasi',
                    ])
                    ->multiple(),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'in_progress' => 'Sedang Dikerjakan',
                        'completed' => 'Selesai',
                        'cancelled' => 'Dibatalkan',
                    ])
                    ->multiple(),

                Tables\Filters\SelectFilter::make('item_id')
                    ->label('Barang')
                    ->relationship('item', 'name')
                    ->searchable()
                    ->preload(),

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
                                fn(Builder $query, $date): Builder => $query->whereDate('performed_at', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('performed_at', '<=', $date),
                            );
                    }),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('performed_at', 'desc');
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
            'index' => Pages\ListMaintenanceHistories::route('/'),
            'create' => Pages\CreateMaintenanceHistory::route('/create'),
            // 'view' => Pages\ViewMaintenanceHistory::route('/{record}'),
            'edit' => Pages\EditMaintenanceHistory::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
