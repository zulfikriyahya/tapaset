<?php
// app/Filament/Resources/LocationResource.php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Location;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\LocationResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LocationResource extends Resource
{
    protected static ?string $model = Location::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    protected static ?string $navigationLabel = 'Lokasi';

    protected static ?string $modelLabel = 'Lokasi';

    protected static ?string $pluralModelLabel = 'Lokasi';

    protected static ?string $navigationGroup = 'Manajemen Inventaris';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Lokasi')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Lokasi')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Contoh: Lab Komputer, Perpustakaan, Ruang Guru'),

                        Forms\Components\TextInput::make('code')
                            ->label('Kode Lokasi')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->helperText('Kode singkat untuk lokasi. Contoh: LAB-KOMP, PERP, RG-01'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Lokasi Aktif')
                            ->default(true)
                            ->helperText('Nonaktifkan jika lokasi tidak digunakan'),

                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi')
                            ->rows(3)
                            ->columnSpanFull()
                            ->helperText('Detail tambahan tentang lokasi'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Kode')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->copyable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Lokasi')
                    ->searchable()
                    ->sortable()
                    ->description(fn(Location $record): string => $record->description ?? ''),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),

                Tables\Columns\TextColumn::make('items_count')
                    ->label('Jumlah Barang')
                    ->counts('items')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                Tables\Columns\TextColumn::make('available_items_count')
                    ->label('Barang Tersedia')
                    ->counts([
                        'items' => fn(Builder $query) => $query->where('status', \App\Enums\ItemStatus::AVAILABLE)
                    ])
                    ->badge()
                    ->color('success')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->placeholder('Semua')
                    ->trueLabel('Aktif')
                    ->falseLabel('Nonaktif'),

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
            ->defaultSort('code', 'asc');
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
            'index' => Pages\ListLocations::route('/'),
            'create' => Pages\CreateLocation::route('/create'),
            // 'view' => Pages\ViewLocation::route('/{record}'),
            'edit' => Pages\EditLocation::route('/{record}/edit'),
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
