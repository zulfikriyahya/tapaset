<?php

// app/Filament/Resources/StockMovementResource.php

namespace App\Filament\Resources;

use App\Filament\Resources\StockMovementResource\Pages;
use App\Models\StockMovement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class StockMovementResource extends Resource
{
    protected static ?string $model = StockMovement::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';

    protected static ?string $navigationLabel = 'Pergerakan Stok';

    protected static ?string $modelLabel = 'Pergerakan Stok';

    protected static ?string $pluralModelLabel = 'Pergerakan Stok';

    protected static ?string $navigationGroup = 'Laporan';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Pergerakan')
                    ->schema([
                        Forms\Components\Select::make('item_id')
                            ->label('Barang')
                            ->relationship('item', 'name')
                            ->searchable(['name', 'item_code'])
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name} ({$record->item_code})")
                            ->required()
                            ->preload(),

                        Forms\Components\Select::make('movement_type')
                            ->label('Jenis Pergerakan')
                            ->options([
                                'in' => 'Masuk (Stock In)',
                                'out' => 'Keluar (Stock Out)',
                                'adjustment' => 'Penyesuaian',
                                'transfer' => 'Transfer Lokasi',
                            ])
                            ->required()
                            ->native(false)
                            ->live(),

                        Forms\Components\TextInput::make('quantity')
                            ->label('Jumlah')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->helperText('Jumlah barang yang bergerak'),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Lokasi')
                    ->schema([
                        Forms\Components\Select::make('from_location_id')
                            ->label('Dari Lokasi')
                            ->relationship('fromLocation', 'name')
                            ->searchable()
                            ->preload()
                            ->visible(
                                fn (Forms\Get $get): bool => in_array($get('movement_type'), ['out', 'transfer'])
                            ),

                        Forms\Components\Select::make('to_location_id')
                            ->label('Ke Lokasi')
                            ->relationship('toLocation', 'name')
                            ->searchable()
                            ->preload()
                            ->visible(
                                fn (Forms\Get $get): bool => in_array($get('movement_type'), ['in', 'transfer'])
                            ),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Detail Tambahan')
                    ->schema([
                        Forms\Components\TextInput::make('reference_number')
                            ->label('No. Referensi')
                            ->maxLength(255)
                            ->helperText('Nomor dokumen pendukung (PO, DO, dll)'),

                        Forms\Components\DateTimePicker::make('performed_at')
                            ->label('Tanggal Pergerakan')
                            ->default(now())
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y H:i')
                            ->seconds(false),

                        Forms\Components\Textarea::make('reason')
                            ->label('Alasan/Keterangan')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Select::make('performed_by')
                            ->label('Dilakukan Oleh')
                            ->relationship('performedBy', 'name')
                            ->default(Auth::id())
                            ->required()
                            ->disabled()
                            ->dehydrated(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('performed_at')
                    ->label('Tanggal')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('item.name')
                    ->label('Barang')
                    ->searchable()
                    ->sortable()
                    ->description(fn (StockMovement $record): string => $record->item->item_code),

                Tables\Columns\TextColumn::make('movement_type')
                    ->label('Jenis')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'in' => 'Masuk',
                        'out' => 'Keluar',
                        'adjustment' => 'Penyesuaian',
                        'transfer' => 'Transfer',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'in' => 'success',
                        'out' => 'danger',
                        'adjustment' => 'warning',
                        'transfer' => 'info',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'in' => 'heroicon-o-arrow-down-circle',
                        'out' => 'heroicon-o-arrow-up-circle',
                        'adjustment' => 'heroicon-o-pencil-square',
                        'transfer' => 'heroicon-o-arrow-right-circle',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('quantity')
                    ->label('Jumlah')
                    ->numeric()
                    ->alignCenter()
                    ->sortable()
                    ->color(
                        fn (StockMovement $record): string => $record->movement_type === 'in' ? 'success' : 'danger'
                    ),

                Tables\Columns\TextColumn::make('fromLocation.name')
                    ->label('Dari')
                    ->badge()
                    ->color('gray')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('toLocation.name')
                    ->label('Ke')
                    ->badge()
                    ->color('info')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('reference_number')
                    ->label('No. Ref')
                    ->searchable()
                    ->placeholder('-')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('reason')
                    ->label('Keterangan')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('performedBy.name')
                    ->label('Oleh')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('movement_type')
                    ->label('Jenis Pergerakan')
                    ->options([
                        'in' => 'Masuk',
                        'out' => 'Keluar',
                        'adjustment' => 'Penyesuaian',
                        'transfer' => 'Transfer',
                    ])
                    ->multiple(),

                Tables\Filters\SelectFilter::make('item_id')
                    ->label('Barang')
                    ->relationship('item', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('from_location_id')
                    ->label('Dari Lokasi')
                    ->relationship('fromLocation', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('to_location_id')
                    ->label('Ke Lokasi')
                    ->relationship('toLocation', 'name')
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
                                fn (Builder $query, $date): Builder => $query->whereDate('performed_at', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('performed_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListStockMovements::route('/'),
            'create' => Pages\CreateStockMovement::route('/create'),
            // 'view' => Pages\ViewStockMovement::route('/{record}'),
            'edit' => Pages\EditStockMovement::route('/{record}/edit'),
        ];
    }
}
