<?php

// app/Filament/Resources/ItemResource/RelationManagers/StockMovementsRelationManager.php

namespace App\Filament\Resources\ItemResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class StockMovementsRelationManager extends RelationManager
{
    protected static string $relationship = 'stockMovements';

    protected static ?string $title = 'Pergerakan Stok';

    protected static ?string $recordTitleAttribute = 'movement_type';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('movement_type')
                    ->label('Jenis Pergerakan')
                    ->options([
                        'in' => 'Masuk',
                        'out' => 'Keluar',
                        'adjustment' => 'Penyesuaian',
                        'transfer' => 'Transfer',
                    ])
                    ->required()
                    ->native(false)
                    ->live(),

                Forms\Components\TextInput::make('quantity')
                    ->label('Jumlah')
                    ->numeric()
                    ->required()
                    ->minValue(1),

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

                Forms\Components\TextInput::make('reference_number')
                    ->label('No. Referensi')
                    ->maxLength(255),

                Forms\Components\DateTimePicker::make('performed_at')
                    ->label('Tanggal')
                    ->default(now())
                    ->required()
                    ->native(false),

                Forms\Components\Textarea::make('reason')
                    ->label('Keterangan')
                    ->rows(3)
                    ->columnSpanFull(),

                Forms\Components\Hidden::make('performed_by')
                    ->default(Auth::id()),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('movement_type')
            ->columns([
                Tables\Columns\TextColumn::make('performed_at')
                    ->label('Tanggal')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

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
                    }),

                Tables\Columns\TextColumn::make('quantity')
                    ->label('Jumlah')
                    ->numeric()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('fromLocation.name')
                    ->label('Dari')
                    ->badge()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('toLocation.name')
                    ->label('Ke')
                    ->badge()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('reference_number')
                    ->label('No. Ref')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('performedBy.name')
                    ->label('Oleh')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('movement_type')
                    ->label('Jenis')
                    ->options([
                        'in' => 'Masuk',
                        'out' => 'Keluar',
                        'adjustment' => 'Penyesuaian',
                        'transfer' => 'Transfer',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
}
