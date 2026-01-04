<?php
// app/Filament/Resources/ItemResource/RelationManagers/LoansRelationManager.php

namespace App\Filament\Resources\ItemResource\RelationManagers;

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
                    ->label('No. Peminjaman')
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
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Peminjam')
                    ->searchable()
                    ->description(fn($record): string => $record->user->identity_number ?? ''),

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
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(LoanStatus::class)
                    ->multiple(),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(), // Disable create dari sini
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
            ->modifyQueryUsing(fn(Builder $query) => $query->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]))
            ->defaultSort('loan_date', 'desc');
    }
}
