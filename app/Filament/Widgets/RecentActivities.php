<?php
// app/Filament/Widgets/RecentActivities.php

namespace App\Filament\Widgets;

use Filament\Tables;
use App\Models\RfidLog;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentActivities extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Aktivitas RFID Terbaru';

    protected static ?string $pollingInterval = '10s';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                RfidLog::query()
                    ->with(['rfidCard', 'user', 'item'])
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('H:i:s')
                    ->since()
                    ->sortable(),

                Tables\Columns\TextColumn::make('rfidCard.uid')
                    ->label('Kartu')
                    ->limit(15)
                    ->searchable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->description(
                        fn(RfidLog $record): string =>
                        $record->user ? $record->user->identity_number : '-'
                    )
                    ->placeholder('Unknown')
                    ->searchable(),

                Tables\Columns\TextColumn::make('action')
                    ->label('Aksi')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'tap' => 'Tap',
                        'loan' => 'Pinjam',
                        'return' => 'Kembali',
                        'failed' => 'Gagal',
                        default => $state,
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'tap' => 'gray',
                        'loan' => 'info',
                        'return' => 'success',
                        'failed' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'success' => 'Sukses',
                        'failed' => 'Gagal',
                        'suspended' => 'Suspended',
                        default => $state,
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'success' => 'success',
                        'failed' => 'danger',
                        'suspended' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('item.name')
                    ->label('Barang')
                    ->limit(30)
                    ->placeholder('-')
                    ->searchable(),

                Tables\Columns\TextColumn::make('response_message')
                    ->label('Pesan')
                    ->limit(40)
                    ->wrap(),
            ])
            ->paginated(false);
    }
}
