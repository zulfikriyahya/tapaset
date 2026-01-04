<?php

// app/Filament/Resources/SettingResource.php

namespace App\Filament\Resources;

use App\Filament\Resources\SettingResource\Pages;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Pengaturan';

    protected static ?string $modelLabel = 'Pengaturan';

    protected static ?string $pluralModelLabel = 'Pengaturan';

    protected static ?string $navigationGroup = 'Sistem';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Setting')
                    ->schema([
                        Forms\Components\TextInput::make('key')
                            ->label('Key')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->helperText('Unique identifier untuk setting')
                            ->disabled(fn (string $operation): bool => $operation === 'edit'),

                        Forms\Components\Select::make('group')
                            ->label('Grup')
                            ->options([
                                'general' => 'Umum',
                                'loan' => 'Peminjaman',
                                'penalty' => 'Denda',
                                'notification' => 'Notifikasi',
                                'system' => 'Sistem',
                            ])
                            ->searchable()
                            ->native(false),

                        Forms\Components\Select::make('type')
                            ->label('Tipe Data')
                            ->options([
                                'string' => 'String',
                                'integer' => 'Integer',
                                'boolean' => 'Boolean',
                                'json' => 'JSON',
                                'decimal' => 'Decimal',
                            ])
                            ->default('string')
                            ->required()
                            ->native(false)
                            ->live(),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Nilai Setting')
                    ->schema([
                        // String input
                        Forms\Components\TextInput::make('value')
                            ->label('Nilai')
                            ->maxLength(255)
                            ->visible(
                                fn (Forms\Get $get): bool => in_array($get('type'), ['string', null])
                            ),

                        // Integer input
                        Forms\Components\TextInput::make('value')
                            ->label('Nilai')
                            ->numeric()
                            ->visible(fn (Forms\Get $get): bool => $get('type') === 'integer'),

                        // Decimal input
                        Forms\Components\TextInput::make('value')
                            ->label('Nilai')
                            ->numeric()
                            ->inputMode('decimal')
                            ->visible(fn (Forms\Get $get): bool => $get('type') === 'decimal'),

                        // Boolean input
                        Forms\Components\Toggle::make('value')
                            ->label('Nilai')
                            ->onColor('success')
                            ->offColor('danger')
                            ->visible(fn (Forms\Get $get): bool => $get('type') === 'boolean')
                            ->afterStateHydrated(function (Forms\Components\Toggle $component, $state) {
                                // Convert string 'true'/'false' to boolean
                                if (is_string($state)) {
                                    $component->state($state === 'true' || $state === '1');
                                }
                            })
                            ->dehydrateStateUsing(fn ($state) => $state ? 'true' : 'false'),

                        // JSON input
                        Forms\Components\Textarea::make('value')
                            ->label('Nilai JSON')
                            ->rows(5)
                            ->visible(fn (Forms\Get $get): bool => $get('type') === 'json')
                            ->helperText('Format JSON valid'),

                        Forms\Components\Toggle::make('is_public')
                            ->label('Public')
                            ->default(false)
                            ->helperText('Setting bisa diakses tanpa autentikasi'),
                    ]),

                Forms\Components\Section::make('Deskripsi')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi')
                            ->rows(3)
                            ->helperText('Penjelasan tentang fungsi setting ini')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('group')
                    ->label('Grup')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'general' => 'Umum',
                        'loan' => 'Peminjaman',
                        'penalty' => 'Denda',
                        'notification' => 'Notifikasi',
                        'system' => 'Sistem',
                        default => $state ?? 'Lainnya',
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'general' => 'gray',
                        'loan' => 'info',
                        'penalty' => 'danger',
                        'notification' => 'warning',
                        'system' => 'primary',
                        default => 'gray',
                    })
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('key')
                    ->label('Key')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight(FontWeight::Bold),

                Tables\Columns\TextColumn::make('value')
                    ->label('Nilai')
                    ->searchable()
                    ->limit(40)
                    ->formatStateUsing(function ($state, Setting $record) {
                        if ($record->type === 'boolean') {
                            return $state === 'true' || $state === '1' ? '✓ Ya' : '✗ Tidak';
                        }
                        if ($record->type === 'json') {
                            return substr($state, 0, 40).(strlen($state) > 40 ? '...' : '');
                        }

                        return $state;
                    }),

                Tables\Columns\TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'string' => 'String',
                        'integer' => 'Integer',
                        'boolean' => 'Boolean',
                        'json' => 'JSON',
                        'decimal' => 'Decimal',
                        default => $state,
                    })
                    ->color('info')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_public')
                    ->label('Public')
                    ->boolean()
                    ->trueIcon('heroicon-o-globe-alt')
                    ->falseIcon('heroicon-o-lock-closed')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->sortable(),

                Tables\Columns\TextColumn::make('description')
                    ->label('Deskripsi')
                    ->limit(50)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Terakhir Update')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('group')
                    ->label('Grup')
                    ->options([
                        'general' => 'Umum',
                        'loan' => 'Peminjaman',
                        'penalty' => 'Denda',
                        'notification' => 'Notifikasi',
                        'system' => 'Sistem',
                    ])
                    ->multiple(),

                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipe')
                    ->options([
                        'string' => 'String',
                        'integer' => 'Integer',
                        'boolean' => 'Boolean',
                        'json' => 'JSON',
                        'decimal' => 'Decimal',
                    ])
                    ->multiple(),

                Tables\Filters\TernaryFilter::make('is_public')
                    ->label('Aksesibilitas')
                    ->placeholder('Semua')
                    ->trueLabel('Public')
                    ->falseLabel('Private'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->visible(
                        fn (Setting $record): bool => ! in_array($record->key, [
                            'loan_duration_days',
                            'penalty_per_day',
                            'max_loan_items_student',
                            'max_loan_items_teacher',
                        ])
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('group', 'asc');
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
            'index' => Pages\ListSettings::route('/'),
            'create' => Pages\CreateSetting::route('/create'),
            // 'view' => Pages\ViewSetting::route('/{record}'),
            'edit' => Pages\EditSetting::route('/{record}/edit'),
        ];
    }
}
