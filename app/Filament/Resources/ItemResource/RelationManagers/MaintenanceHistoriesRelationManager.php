<?php

// app/Filament/Resources/ItemResource/RelationManagers/MaintenanceHistoriesRelationManager.php

namespace App\Filament\Resources\ItemResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class MaintenanceHistoriesRelationManager extends RelationManager
{
    protected static string $relationship = 'maintenanceHistories';

    protected static ?string $title = 'Riwayat Maintenance';

    protected static ?string $recordTitleAttribute = 'maintenance_type';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
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
                    ->native(false),

                Forms\Components\Textarea::make('description')
                    ->label('Deskripsi')
                    ->required()
                    ->rows(3)
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('performed_by')
                    ->label('Dikerjakan Oleh')
                    ->maxLength(255),

                Forms\Components\TextInput::make('cost')
                    ->label('Biaya')
                    ->numeric()
                    ->prefix('Rp'),

                Forms\Components\DateTimePicker::make('performed_at')
                    ->label('Tanggal Mulai')
                    ->default(now())
                    ->required()
                    ->native(false),

                Forms\Components\DateTimePicker::make('completed_at')
                    ->label('Tanggal Selesai')
                    ->native(false),

                Forms\Components\Textarea::make('notes')
                    ->label('Catatan')
                    ->rows(2)
                    ->columnSpanFull(),

                Forms\Components\Hidden::make('created_by')
                    ->default(Auth::id()),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('maintenance_type')
            ->columns([
                Tables\Columns\TextColumn::make('maintenance_type')
                    ->label('Jenis')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'repair' => 'Perbaikan',
                        'service' => 'Servis',
                        'inspection' => 'Inspeksi',
                        'cleaning' => 'Pembersihan',
                        'calibration' => 'Kalibrasi',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'repair' => 'danger',
                        'service' => 'info',
                        'inspection' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('description')
                    ->label('Deskripsi')
                    ->limit(40),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Pending',
                        'in_progress' => 'Dikerjakan',
                        'completed' => 'Selesai',
                        'cancelled' => 'Dibatalkan',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'in_progress' => 'info',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('performed_by')
                    ->label('Teknisi')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('cost')
                    ->label('Biaya')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('performed_at')
                    ->label('Tanggal')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('maintenance_type')
                    ->label('Jenis')
                    ->options([
                        'repair' => 'Perbaikan',
                        'service' => 'Servis',
                        'inspection' => 'Inspeksi',
                        'cleaning' => 'Pembersihan',
                        'calibration' => 'Kalibrasi',
                    ]),

                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'in_progress' => 'Dikerjakan',
                        'completed' => 'Selesai',
                        'cancelled' => 'Dibatalkan',
                    ]),

                Tables\Filters\TrashedFilter::make(),
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
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]))
            ->defaultSort('performed_at', 'desc');
    }
}
