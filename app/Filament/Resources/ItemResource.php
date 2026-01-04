<?php
// app/Filament/Resources/ItemResource.php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Item;
use Filament\Tables;
use Filament\Forms\Form;
use App\Enums\ItemStatus;
use Filament\Tables\Table;
use App\Enums\ItemCondition;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Infolists\Components;
use Filament\Support\Enums\FontWeight;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ItemResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ItemResource\RelationManagers;

class ItemResource extends Resource
{
    protected static ?string $model = Item::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationLabel = 'Barang';

    protected static ?string $modelLabel = 'Barang';

    protected static ?string $pluralModelLabel = 'Barang';

    protected static ?string $navigationGroup = 'Manajemen Inventaris';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Dasar')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Barang')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('item_code')
                            ->label('Kode Barang')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->suffixIcon('heroicon-m-qr-code')
                            ->helperText('Barcode/QR Code unik untuk barang ini'),

                        Forms\Components\TextInput::make('serial_number')
                            ->label('Nomor Seri')
                            ->maxLength(255)
                            ->helperText('Opsional: Nomor seri dari manufaktur'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Kategori & Lokasi')
                    ->schema([
                        Forms\Components\Select::make('category_id')
                            ->label('Kategori')
                            ->relationship('category', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nama Kategori')
                                    ->required(),
                                Forms\Components\Toggle::make('is_consumable')
                                    ->label('Barang Habis Pakai?')
                                    ->default(false),
                            ]),

                        Forms\Components\Select::make('location_id')
                            ->label('Lokasi')
                            ->relationship('location', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nama Lokasi')
                                    ->required(),
                                Forms\Components\TextInput::make('code')
                                    ->label('Kode Lokasi')
                                    ->required(),
                            ]),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Status & Kondisi')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options(ItemStatus::class)
                            ->default(ItemStatus::AVAILABLE)
                            ->required()
                            ->native(false),

                        Forms\Components\Select::make('condition')
                            ->label('Kondisi')
                            ->options(ItemCondition::class)
                            ->default(ItemCondition::GOOD)
                            ->required()
                            ->native(false),

                        Forms\Components\TextInput::make('quantity')
                            ->label('Jumlah')
                            ->numeric()
                            ->default(1)
                            ->minValue(0)
                            ->required()
                            ->helperText('Untuk barang habis pakai'),

                        Forms\Components\TextInput::make('min_quantity')
                            ->label('Stok Minimum')
                            ->numeric()
                            ->minValue(0)
                            ->helperText('Alert jika stok di bawah angka ini'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Informasi Pembelian')
                    ->schema([
                        Forms\Components\DatePicker::make('purchase_date')
                            ->label('Tanggal Pembelian')
                            ->native(false)
                            ->displayFormat('d/m/Y'),

                        Forms\Components\TextInput::make('price')
                            ->label('Harga Pembelian')
                            ->numeric()
                            ->prefix('Rp')
                            ->inputMode('decimal'),

                        Forms\Components\DatePicker::make('warranty_expired_at')
                            ->label('Garansi Hingga')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->helperText('Tanggal berakhirnya garansi'),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Detail Lainnya')
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->label('Foto Barang')
                            ->image()
                            ->directory('items')
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '16:9',
                                '4:3',
                                '1:1',
                            ])
                            ->maxSize(2048)
                            ->helperText('Max 2MB. Format: JPG, PNG')
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Foto')
                    ->circular()
                    ->defaultImageUrl(url('/images/no-image.png'))
                    ->size(40),

                Tables\Columns\TextColumn::make('item_code')
                    ->label('Kode')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->copyable()
                    ->copyMessage('Kode berhasil disalin')
                    ->copyMessageDuration(1500),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Barang')
                    ->searchable()
                    ->sortable()
                    ->description(fn(Item $record): string => $record->serial_number ? "SN: {$record->serial_number}" : '')
                    ->wrap(),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Kategori')
                    ->badge()
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('location.name')
                    ->label('Lokasi')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('condition')
                    ->label('Kondisi')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('quantity')
                    ->label('Jumlah')
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->color(
                        fn(Item $record): string =>
                        $record->min_quantity && $record->quantity <= $record->min_quantity
                            ? 'danger'
                            : 'success'
                    ),

                Tables\Columns\TextColumn::make('price')
                    ->label('Harga')
                    ->money('IDR')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('purchase_date')
                    ->label('Tgl Beli')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options(ItemStatus::class)
                    ->multiple()
                    ->preload(),

                Tables\Filters\SelectFilter::make('condition')
                    ->label('Kondisi')
                    ->options(ItemCondition::class)
                    ->multiple()
                    ->preload(),

                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Kategori')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                Tables\Filters\SelectFilter::make('location_id')
                    ->label('Lokasi')
                    ->relationship('location', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                Tables\Filters\Filter::make('low_stock')
                    ->label('Stok Rendah')
                    ->query(fn(Builder $query): Builder => $query->whereColumn('quantity', '<=', 'min_quantity'))
                    ->toggle(),

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
                    // Tables\Actions\ExportBulkAction::make()
                    //     ->exporter(\App\Filament\Exports\ItemExporter::class),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->persistFiltersInSession()
            ->persistSearchInSession()
            ->persistColumnSearchesInSession();
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\LoansRelationManager::class,
            RelationManagers\MaintenanceHistoriesRelationManager::class,
            RelationManagers\StockMovementsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListItems::route('/'),
            'create' => Pages\CreateItem::route('/create'),
            // 'view' => Pages\ViewItem::route('/{record}'),
            'edit' => Pages\EditItem::route('/{record}/edit'),
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
        return static::getModel()::where('status', ItemStatus::AVAILABLE)->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }
}
