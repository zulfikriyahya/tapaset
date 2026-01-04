<?php
// app/Filament/Resources/UserResource/RelationManagers/LoansRelationManager.php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Enums\LoanStatus;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class LoansRelationManager extends RelationManager
{
    protected static string $relationship = 'loans';

    protected static ?string $title = 'Riwayat Peminjaman';

    protected static ?string $recordTitleAttribute = 'loan_number';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('loan_number')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('loan_number')
            ->columns([
                Tables\Columns\TextColumn::make('loan_number')
                    ->label('No. Pinjam')
                    ->searchable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('item.name')
                    ->label('Barang')
                    ->searchable()
                    ->description(fn($record): string => $record->item->item_code),

                Tables\Columns\TextColumn::make('loan_date')
                    ->label('Tgl Pinjam')
                    ->dateTime('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('due_date')
                    ->label('Jatuh Tempo')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->color(
                        fn($record): string =>
                        $record->status === LoanStatus::ACTIVE && $record->due_date < now()
                            ? 'danger'
                            : 'gray'
                    )
                    ->description(
                        fn($record): string =>
                        $record->status === LoanStatus::ACTIVE && $record->due_date < now()
                            ? 'Terlambat ' . now()->diffInDays($record->due_date) . ' hari'
                            : ''
                    ),

                Tables\Columns\TextColumn::make('return_date')
                    ->label('Tgl Kembali')
                    ->dateTime('d/m/Y')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('penalty_amount')
                    ->label('Denda')
                    ->money('IDR')
                    ->color(
                        fn($record): string =>
                        $record->penalty_amount > 0 && !$record->is_paid ? 'danger' : 'success'
                    ),

                Tables\Columns\IconColumn::make('is_paid')
                    ->label('Lunas')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(LoanStatus::class)
                    ->multiple(),

                Tables\Filters\Filter::make('overdue')
                    ->label('Terlambat')
                    ->query(
                        fn(Builder $query): Builder =>
                        $query->where('status', LoanStatus::ACTIVE)
                            ->where('due_date', '<', now())
                    )
                    ->toggle(),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(fn(Builder $query) => $query->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]))
            ->defaultSort('loan_date', 'desc')
            ->description('Riwayat peminjaman barang oleh user ini');
    }
}
