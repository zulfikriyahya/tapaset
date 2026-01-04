<?php

// app/Filament/Widgets/LowStockAlert.php

namespace App\Filament\Widgets;

use App\Models\Item;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LowStockAlert extends BaseWidget
{
    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = '⚠️ Alert Stok Rendah';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Item::query()
                    ->whereColumn('quantity', '<=', 'min_quantity')
                    ->whereNotNull('min_quantity')
                    ->with(['category', 'location'])
                    ->orderBy('quantity', 'asc')
            )
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Foto')
                    ->circular()
                    ->defaultImageUrl(url('/images/no-image.png'))
                    ->size(40),

                Tables\Columns\TextColumn::make('item_code')
                    ->label('Kode')
                    ->searchable()
                    ->weight(FontWeight::Bold)
                    ->copyable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Barang')
                    ->searchable()
                    ->description(
                        fn (Item $record): string => $record->category->name.' - '.$record->location->name
                    ),

                Tables\Columns\TextColumn::make('quantity')
                    ->label('Stok Saat Ini')
                    ->numeric()
                    ->alignCenter()
                    ->badge()
                    ->color('danger'),

                Tables\Columns\TextColumn::make('min_quantity')
                    ->label('Min. Stok')
                    ->numeric()
                    ->alignCenter()
                    ->badge()
                    ->color('warning'),

                Tables\Columns\TextColumn::make('difference')
                    ->label('Kekurangan')
                    ->state(fn (Item $record): int => $record->min_quantity - $record->quantity)
                    ->numeric()
                    ->alignCenter()
                    ->badge()
                    ->color('danger')
                    ->suffix(' unit'),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Kategori')
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('location.name')
                    ->label('Lokasi')
                    ->badge()
                    ->color('info')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Tables\Actions\Action::make('restock')
                    ->label('Restock')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('success')
                    ->url(
                        fn (Item $record): string => route('filament.admin.resources.stock-movements.create', [
                            'item_id' => $record->id,
                            'movement_type' => 'in',
                        ])
                    ),
            ])
            ->emptyStateHeading('Tidak ada stok yang rendah')
            ->emptyStateDescription('Semua barang memiliki stok yang cukup')
            ->emptyStateIcon('heroicon-o-check-circle');
    }
}
