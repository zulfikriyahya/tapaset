# Laravel Project Blueprint

## 📁 Directory: Root Files

Configuration and setup files in project root.

### 📄 File: `./composer.json`

_PHP dependencies and project metadata._

```json
{
    "$schema": "https://getcomposer.org/schema.json",
    "name": "laravel/laravel",
    "type": "project",
    "description": "The skeleton application for the Laravel framework.",
    "keywords": ["laravel", "framework"],
    "license": "MIT",
    "require": {
        "php": "^8.2",
        "awcodes/filament-quick-create": "^3.6",
        "filament/filament": "^3.2",
        "filament/spatie-laravel-settings-plugin": "^3.2",
        "laravel/framework": "^12.0",
        "laravel/tinker": "^2.10.1",
        "pxlrbt/filament-excel": "^2.5"
    },
    "require-dev": {
        "fakerphp/faker": "^1.23",
        "laravel-shift/blueprint": "^2.13",
        "laravel/pail": "^1.2.2",
        "laravel/pint": "^1.24",
        "laravel/sail": "^1.41",
        "mockery/mockery": "^1.6",
        "nunomaduro/collision": "^8.6",
        "phpunit/phpunit": "^11.5.3"
    },
    "autoload": {
        "psr-4": {
            "App\\": "app/",
            "Database\\Factories\\": "database/factories/",
            "Database\\Seeders\\": "database/seeders/"
        }
    },
    "autoload-dev": {
        "psr-4": {
            "Tests\\": "tests/"
        }
    },
    "scripts": {
        "setup": [
            "composer install",
            "@php -r \"file_exists('.env') || copy('.env.example', '.env');\"",
            "@php artisan key:generate",
            "@php artisan migrate --force",
            "npm install",
            "npm run build"
        ],
        "dev": [
            "Composer\\Config::disableProcessTimeout",
            "npx concurrently -c \"#93c5fd,#c4b5fd,#fb7185,#fdba74\" \"php artisan serve\" \"php artisan queue:listen --tries=1\" \"php artisan pail --timeout=0\" \"npm run dev\" --names=server,queue,logs,vite --kill-others"
        ],
        "test": ["@php artisan config:clear --ansi", "@php artisan test"],
        "post-autoload-dump": [
            "Illuminate\\Foundation\\ComposerScripts::postAutoloadDump",
            "@php artisan package:discover --ansi",
            "@php artisan filament:upgrade"
        ],
        "post-update-cmd": [
            "@php artisan vendor:publish --tag=laravel-assets --ansi --force"
        ],
        "post-root-package-install": [
            "@php -r \"file_exists('.env') || copy('.env.example', '.env');\""
        ],
        "post-create-project-cmd": [
            "@php artisan key:generate --ansi",
            "@php -r \"file_exists('database/database.sqlite') || touch('database/database.sqlite');\"",
            "@php artisan migrate --graceful --ansi"
        ],
        "pre-package-uninstall": [
            "Illuminate\\Foundation\\ComposerScripts::prePackageUninstall"
        ]
    },
    "extra": {
        "laravel": {
            "dont-discover": []
        }
    },
    "config": {
        "optimize-autoloader": true,
        "preferred-install": "dist",
        "sort-packages": true,
        "allow-plugins": {
            "pestphp/pest-plugin": true,
            "php-http/discovery": true
        }
    },
    "minimum-stability": "stable",
    "prefer-stable": true
}
```

---

### 📄 File: `./package.json`

_Node.js dependencies and build scripts._

```json
{
    "$schema": "https://www.schemastore.org/package.json",
    "private": true,
    "type": "module",
    "scripts": {
        "build": "vite build",
        "dev": "vite"
    },
    "devDependencies": {
        "@tailwindcss/vite": "^4.0.0",
        "axios": "^1.11.0",
        "concurrently": "^9.0.1",
        "laravel-vite-plugin": "^2.0.0",
        "tailwindcss": "^4.0.0",
        "vite": "^7.0.7"
    }
}
```

---

### 📄 File: `./phpunit.xml`

_PHPUnit testing configuration._

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="vendor/phpunit/phpunit/phpunit.xsd"
         bootstrap="vendor/autoload.php"
         colors="true"
>
    <testsuites>
        <testsuite name="Unit">
            <directory>tests/Unit</directory>
        </testsuite>
        <testsuite name="Feature">
            <directory>tests/Feature</directory>
        </testsuite>
    </testsuites>
    <source>
        <include>
            <directory>app</directory>
        </include>
    </source>
    <php>
        <env name="APP_ENV" value="testing"/>
        <env name="APP_MAINTENANCE_DRIVER" value="file"/>
        <env name="BCRYPT_ROUNDS" value="4"/>
        <env name="BROADCAST_CONNECTION" value="null"/>
        <env name="CACHE_STORE" value="array"/>
        <env name="DB_CONNECTION" value="sqlite"/>
        <env name="DB_DATABASE" value=":memory:"/>
        <env name="MAIL_MAILER" value="array"/>
        <env name="QUEUE_CONNECTION" value="sync"/>
        <env name="SESSION_DRIVER" value="array"/>
        <env name="PULSE_ENABLED" value="false"/>
        <env name="TELESCOPE_ENABLED" value="false"/>
        <env name="NIGHTWATCH_ENABLED" value="false"/>
    </php>
</phpunit>

```

---

### 📄 File: `./vite.config.js`

_Vite build tool configuration._

```javascript
import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ["**/storage/framework/views/**"],
        },
    },
});
```

---

## 📁 Directory: app (Application Core)

Contains models, controllers, services, and business logic.

### 📄 File: `./app/Enums/ItemCondition.php`

```php
<?php

namespace App\Enums;

enum ItemCondition: string
{
    case GOOD = 'good';
    case FAIR = 'fair';
    case DAMAGED = 'damaged';

    public function label(): string
    {
        return match ($this) {
            self::GOOD => 'Baik',
            self::FAIR => 'Cukup',
            self::DAMAGED => 'Rusak',
        };
    }
}

```

---

### 📄 File: `./app/Enums/ItemStatus.php`

```php
<?php

namespace App\Enums;

enum ItemStatus: string
{
    case AVAILABLE = 'available';
    case LOANED = 'loaned';
    case MAINTENANCE = 'maintenance';
    case RETIRED = 'retired';

    public function label(): string
    {
        return match ($this) {
            self::AVAILABLE => 'Tersedia',
            self::LOANED => 'Dipinjam',
            self::MAINTENANCE => 'Maintenance',
            self::RETIRED => 'Tidak Aktif',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::AVAILABLE => 'success',
            self::LOANED => 'warning',
            self::MAINTENANCE => 'info',
            self::RETIRED => 'danger',
        };
    }
}

```

---

### 📄 File: `./app/Enums/LoanStatus.php`

```php
<?php

namespace App\Enums;

enum LoanStatus: string
{
    case ACTIVE = 'active';
    case RETURNED = 'returned';
    case OVERDUE = 'overdue';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Aktif',
            self::RETURNED => 'Dikembalikan',
            self::OVERDUE => 'Terlambat',
            self::CANCELLED => 'Dibatalkan',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::ACTIVE => 'info',
            self::RETURNED => 'success',
            self::OVERDUE => 'danger',
            self::CANCELLED => 'secondary',
        };
    }
}

```

---

### 📄 File: `./app/Enums/UserRole.php`

```php
<?php

namespace App\Enums;

enum UserRole: string
{
    case STUDENT = 'student';
    case TEACHER = 'teacher';
    case STAFF = 'staff';
    case ADMIN = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::STUDENT => 'Siswa',
            self::TEACHER => 'Guru',
            self::STAFF => 'Staff',
            self::ADMIN => 'Admin',
        };
    }
}

```

---

### 📄 File: `./app/Filament/Resources/CategoryResource.php`

```php
<?php
// app/Filament/Resources/CategoryResource.php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Category;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\CategoryResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationLabel = 'Kategori';

    protected static ?string $modelLabel = 'Kategori';

    protected static ?string $pluralModelLabel = 'Kategori';

    protected static ?string $navigationGroup = 'Manajemen Inventaris';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Kategori')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Kategori')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(
                                fn(Forms\Set $set, ?string $state) =>
                                $set('slug', Str::slug($state))
                            ),

                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('URL-friendly identifier'),

                        Forms\Components\Toggle::make('is_consumable')
                            ->label('Barang Habis Pakai?')
                            ->default(false)
                            ->helperText('Untuk barang yang dapat berkurang jumlahnya')
                            ->live(),

                        Forms\Components\TextInput::make('min_stock')
                            ->label('Stok Minimum Default')
                            ->numeric()
                            ->minValue(0)
                            ->visible(fn(Forms\Get $get): bool => $get('is_consumable'))
                            ->helperText('Stok minimum untuk semua item dalam kategori ini'),

                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Kategori')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold),

                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\IconColumn::make('is_consumable')
                    ->label('Habis Pakai')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('info')
                    ->falseColor('gray')
                    ->sortable(),

                Tables\Columns\TextColumn::make('min_stock')
                    ->label('Min. Stok')
                    ->numeric()
                    ->sortable()
                    ->placeholder('-')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('items_count')
                    ->label('Jumlah Barang')
                    ->counts('items')
                    ->badge()
                    ->color('success')
                    ->sortable(),

                Tables\Columns\TextColumn::make('description')
                    ->label('Deskripsi')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_consumable')
                    ->label('Tipe')
                    ->placeholder('Semua')
                    ->trueLabel('Habis Pakai')
                    ->falseLabel('Tidak Habis Pakai'),

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
            ->defaultSort('name', 'asc');
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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            // 'view' => Pages\ViewCategory::route('/{record}'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
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

```

---

### 📄 File: `./app/Filament/Resources/ItemResource.php`

```php
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

```

---

### 📄 File: `./app/Filament/Resources/ItemResource/RelationManagers/LoansRelationManager.php`

```php
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

```

---

### 📄 File: `./app/Filament/Resources/ItemResource/RelationManagers/MaintenanceHistoriesRelationManager.php`

```php
<?php
// app/Filament/Resources/ItemResource/RelationManagers/MaintenanceHistoriesRelationManager.php

namespace App\Filament\Resources\ItemResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

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
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'repair' => 'Perbaikan',
                        'service' => 'Servis',
                        'inspection' => 'Inspeksi',
                        'cleaning' => 'Pembersihan',
                        'calibration' => 'Kalibrasi',
                        default => $state,
                    })
                    ->color(fn(string $state): string => match ($state) {
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
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'pending' => 'Pending',
                        'in_progress' => 'Dikerjakan',
                        'completed' => 'Selesai',
                        'cancelled' => 'Dibatalkan',
                        default => $state,
                    })
                    ->color(fn(string $state): string => match ($state) {
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
            ->modifyQueryUsing(fn(Builder $query) => $query->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]))
            ->defaultSort('performed_at', 'desc');
    }
}

```

---

### 📄 File: `./app/Filament/Resources/ItemResource/RelationManagers/StockMovementsRelationManager.php`

```php
<?php
// app/Filament/Resources/ItemResource/RelationManagers/StockMovementsRelationManager.php

namespace App\Filament\Resources\ItemResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\RelationManagers\RelationManager;

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
                        fn(Forms\Get $get): bool =>
                        in_array($get('movement_type'), ['out', 'transfer'])
                    ),

                Forms\Components\Select::make('to_location_id')
                    ->label('Ke Lokasi')
                    ->relationship('toLocation', 'name')
                    ->searchable()
                    ->preload()
                    ->visible(
                        fn(Forms\Get $get): bool =>
                        in_array($get('movement_type'), ['in', 'transfer'])
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
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'in' => 'Masuk',
                        'out' => 'Keluar',
                        'adjustment' => 'Penyesuaian',
                        'transfer' => 'Transfer',
                        default => $state,
                    })
                    ->color(fn(string $state): string => match ($state) {
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

```

---

### 📄 File: `./app/Filament/Resources/LoanResource.php`

```php
<?php
// app/Filament/Resources/LoanResource.php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use App\Models\Item;
use App\Models\Loan;
use Filament\Tables;
use App\Models\Setting;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use App\Enums\ItemStatus;
use App\Enums\LoanStatus;
use Filament\Tables\Table;
use App\Enums\ItemCondition;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Support\Enums\FontWeight;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\LoanResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LoanResource extends Resource
{
    protected static ?string $model = Loan::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path-rounded-square';

    protected static ?string $navigationLabel = 'Peminjaman';

    protected static ?string $modelLabel = 'Peminjaman';

    protected static ?string $pluralModelLabel = 'Peminjaman';

    protected static ?string $navigationGroup = 'Transaksi';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Peminjaman')
                    ->schema([
                        Forms\Components\TextInput::make('loan_number')
                            ->label('No. Peminjaman')
                            ->default(fn() => 'LN-' . date('Ymd') . '-' . str_pad(Loan::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT))
                            ->disabled()
                            ->dehydrated()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        Forms\Components\Select::make('user_id')
                            ->label('Peminjam')
                            ->relationship('user', 'name')
                            ->searchable(['name', 'identity_number', 'email'])
                            ->getOptionLabelFromRecordUsing(fn($record) => "{$record->name} - {$record->identity_number}")
                            ->required()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function (Set $set, ?string $state) {
                                if ($state) {
                                    $user = \App\Models\User::find($state);
                                    if ($user && $user->is_suspended) {
                                        Notification::make()
                                            ->warning()
                                            ->title('User Disuspend')
                                            ->body("User ini sedang disuspend hingga " . ($user->suspended_until ? $user->suspended_until->format('d/m/Y') : 'tanpa batas'))
                                            ->persistent()
                                            ->send();
                                    }

                                    // Check max loan limit
                                    $activeLoanCount = Loan::where('user_id', $state)
                                        ->where('status', LoanStatus::ACTIVE)
                                        ->count();

                                    $maxLoans = $user->max_loan_items ?? 3;

                                    if ($activeLoanCount >= $maxLoans) {
                                        Notification::make()
                                            ->danger()
                                            ->title('Batas Peminjaman')
                                            ->body("User sudah mencapai batas maksimal peminjaman ({$maxLoans} item)")
                                            ->persistent()
                                            ->send();
                                    }

                                    // Set default loan duration
                                    $duration = $user->loan_duration_days ?? Setting::where('key', 'loan_duration_days')->value('value') ?? 7;
                                    $set('due_date', now()->addDays($duration));
                                }
                            }),

                        Forms\Components\Select::make('item_id')
                            ->label('Barang')
                            ->options(function () {
                                return Item::where('status', ItemStatus::AVAILABLE)
                                    ->get()
                                    ->mapWithKeys(fn($item) => [$item->id => "{$item->name} ({$item->item_code})"]);
                            })
                            ->searchable()
                            ->required()
                            ->preload()
                            ->helperText('Hanya menampilkan barang yang tersedia')
                            ->live(),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Jadwal Peminjaman')
                    ->schema([
                        Forms\Components\DateTimePicker::make('loan_date')
                            ->label('Tanggal Pinjam')
                            ->default(now())
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y H:i')
                            ->seconds(false),

                        Forms\Components\DateTimePicker::make('due_date')
                            ->label('Tanggal Jatuh Tempo')
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y H:i')
                            ->seconds(false)
                            ->minDate(fn(Get $get) => $get('loan_date'))
                            ->helperText('Tanggal maksimal pengembalian'),

                        Forms\Components\DateTimePicker::make('return_date')
                            ->label('Tanggal Kembali')
                            ->native(false)
                            ->displayFormat('d/m/Y H:i')
                            ->seconds(false)
                            ->hidden(fn(string $operation): bool => $operation === 'create'),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Status & Kondisi')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options(LoanStatus::class)
                            ->default(LoanStatus::ACTIVE)
                            ->required()
                            ->native(false)
                            ->live(),

                        Forms\Components\Select::make('returned_condition')
                            ->label('Kondisi Saat Kembali')
                            ->options(ItemCondition::class)
                            ->native(false)
                            ->visible(fn(Get $get): bool => in_array($get('status'), [LoanStatus::RETURNED->value, 'returned']))
                            ->required(fn(Get $get): bool => in_array($get('status'), [LoanStatus::RETURNED->value, 'returned'])),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Denda & Pembayaran')
                    ->schema([
                        Forms\Components\TextInput::make('penalty_amount')
                            ->label('Jumlah Denda')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0)
                            ->inputMode('decimal')
                            ->helperText('Denda keterlambatan akan dihitung otomatis'),

                        Forms\Components\Toggle::make('is_paid')
                            ->label('Sudah Dibayar?')
                            ->default(false)
                            ->visible(fn(Get $get): bool => (float) ($get('penalty_amount') ?? 0) > 0),
                    ])
                    ->columns(2)
                    ->visible(fn(string $operation): bool => $operation === 'edit'),

                Forms\Components\Section::make('Catatan')
                    ->schema([
                        Forms\Components\Textarea::make('loan_notes')
                            ->label('Catatan Peminjaman')
                            ->rows(2)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('return_notes')
                            ->label('Catatan Pengembalian')
                            ->rows(2)
                            ->columnSpanFull()
                            ->hidden(fn(string $operation): bool => $operation === 'create'),
                    ]),

                Forms\Components\Section::make('Audit Trail')
                    ->schema([
                        Forms\Components\Select::make('created_by')
                            ->label('Diproses Oleh')
                            ->relationship('createdBy', 'name')
                            ->default(Auth::id())
                            ->required()
                            ->disabled()
                            ->dehydrated(),

                        Forms\Components\Select::make('returned_by')
                            ->label('Dikembalikan Ke')
                            ->relationship('returnedBy', 'name')
                            ->searchable()
                            ->preload()
                            ->hidden(fn(string $operation): bool => $operation === 'create'),

                        Forms\Components\Select::make('approved_by')
                            ->label('Disetujui Oleh')
                            ->relationship('approvedBy', 'name')
                            ->searchable()
                            ->preload()
                            ->hidden(fn(string $operation): bool => $operation === 'create'),
                    ])
                    ->columns(3)
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('loan_number')
                    ->label('No. Pinjam')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight(FontWeight::Bold),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Peminjam')
                    ->searchable()
                    ->sortable()
                    ->description(fn(Loan $record): string => $record->user->identity_number ?? '-'),

                Tables\Columns\TextColumn::make('item.name')
                    ->label('Barang')
                    ->searchable()
                    ->sortable()
                    ->description(fn(Loan $record): string => $record->item->item_code ?? '-')
                    ->wrap(),

                Tables\Columns\TextColumn::make('loan_date')
                    ->label('Tgl Pinjam')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('due_date')
                    ->label('Jatuh Tempo')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->color(
                        fn(Loan $record): string =>
                        $record->status === LoanStatus::ACTIVE && $record->due_date < now()
                            ? 'danger'
                            : 'gray'
                    )
                    ->description(
                        fn(Loan $record): ?string =>
                        $record->status === LoanStatus::ACTIVE && $record->due_date < now()
                            ? 'Terlambat ' . now()->diffInDays($record->due_date) . ' hari'
                            : null
                    ),

                Tables\Columns\TextColumn::make('return_date')
                    ->label('Tgl Kembali')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('penalty_amount')
                    ->label('Denda')
                    ->money('IDR')
                    ->sortable()
                    ->color(
                        fn(Loan $record): string =>
                        $record->penalty_amount > 0 && !$record->is_paid ? 'danger' : 'success'
                    )
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->money('IDR')
                            ->label('Total'),
                    ]),

                Tables\Columns\IconColumn::make('is_paid')
                    ->label('Lunas')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('createdBy.name')
                    ->label('Diproses')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options(LoanStatus::class)
                    ->multiple()
                    ->preload(),

                Tables\Filters\SelectFilter::make('user_id')
                    ->label('Peminjam')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('overdue')
                    ->label('Terlambat')
                    ->query(
                        fn(Builder $query): Builder =>
                        $query->where('status', LoanStatus::ACTIVE)
                            ->where('due_date', '<', now())
                    )
                    ->toggle(),

                Tables\Filters\Filter::make('unpaid_penalty')
                    ->label('Denda Belum Lunas')
                    ->query(
                        fn(Builder $query): Builder =>
                        $query->where('penalty_amount', '>', 0)
                            ->where('is_paid', false)
                    )
                    ->toggle(),

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
                                fn(Builder $query, $date): Builder => $query->whereDate('loan_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('loan_date', '<=', $date),
                            );
                    }),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),

                Tables\Actions\Action::make('return')
                    ->label('Kembalikan')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('success')
                    ->visible(fn(Loan $record): bool => $record->status === LoanStatus::ACTIVE)
                    ->form([
                        Forms\Components\DateTimePicker::make('return_date')
                            ->label('Tanggal Kembali')
                            ->default(now())
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y H:i')
                            ->seconds(false),

                        Forms\Components\Select::make('returned_condition')
                            ->label('Kondisi Barang')
                            ->options(ItemCondition::class)
                            ->required()
                            ->native(false)
                            ->default(ItemCondition::GOOD),

                        Forms\Components\Textarea::make('return_notes')
                            ->label('Catatan')
                            ->rows(3),
                    ])
                    ->action(function (Loan $record, array $data): void {
                        // Calculate penalty if overdue
                        $penaltyAmount = 0;
                        if (Carbon::parse($data['return_date'])->gt($record->due_date)) {
                            $daysLate = Carbon::parse($data['return_date'])->diffInDays($record->due_date);
                            $penaltyPerDay = Setting::where('key', 'penalty_per_day')->value('value') ?? 5000;
                            $penaltyAmount = $daysLate * $penaltyPerDay;
                        }

                        $record->update([
                            'return_date' => $data['return_date'],
                            'returned_condition' => $data['returned_condition'],
                            'return_notes' => $data['return_notes'],
                            'status' => LoanStatus::RETURNED,
                            'returned_by' => Auth::id(),
                            'penalty_amount' => $penaltyAmount,
                        ]);

                        // Update item status and condition
                        $record->item->update([
                            'status' => ItemStatus::AVAILABLE,
                            'condition' => $data['returned_condition'],
                        ]);

                        Notification::make()
                            ->success()
                            ->title('Barang Berhasil Dikembalikan')
                            ->body(
                                $penaltyAmount > 0
                                    ? "Denda keterlambatan: Rp " . number_format($penaltyAmount, 0, ',', '.')
                                    : 'Tidak ada denda'
                            )
                            ->send();
                    })
                    ->successNotificationTitle('Barang berhasil dikembalikan'),

                Tables\Actions\Action::make('markAsPaid')
                    ->label('Tandai Lunas')
                    ->icon('heroicon-o-currency-dollar')
                    ->color('warning')
                    ->visible(fn(Loan $record): bool => $record->penalty_amount > 0 && !$record->is_paid)
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Pembayaran Denda')
                    ->modalDescription(
                        fn(Loan $record): string =>
                        'Apakah denda sebesar Rp ' . number_format($record->penalty_amount, 0, ',', '.') . ' sudah dibayar?'
                    )
                    ->action(function (Loan $record): void {
                        $record->update(['is_paid' => true]);

                        Notification::make()
                            ->success()
                            ->title('Pembayaran Dikonfirmasi')
                            ->body('Denda telah ditandai sebagai lunas')
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListLoans::route('/'),
            'create' => Pages\CreateLoan::route('/create'),
            // 'view' => Pages\ViewLoan::route('/{record}'),
            'edit' => Pages\EditLoan::route('/{record}/edit'),
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
        return static::getModel()::where('status', LoanStatus::ACTIVE)->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        $overdueCount = static::getModel()::where('status', LoanStatus::ACTIVE)
            ->where('due_date', '<', now())
            ->count();

        return $overdueCount > 0 ? 'danger' : 'info';
    }
}

```

---

### 📄 File: `./app/Filament/Resources/LocationResource.php`

```php
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

```

---

### 📄 File: `./app/Filament/Resources/MaintenanceHistoryResource.php`

```php
<?php
// app/Filament/Resources/MaintenanceHistoryResource.php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\MaintenanceHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\MaintenanceHistoryResource\Pages;

class MaintenanceHistoryResource extends Resource
{
    protected static ?string $model = MaintenanceHistory::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static ?string $navigationLabel = 'Maintenance';

    protected static ?string $modelLabel = 'Riwayat Maintenance';

    protected static ?string $pluralModelLabel = 'Riwayat Maintenance';

    protected static ?string $navigationGroup = 'Laporan';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Maintenance')
                    ->schema([
                        Forms\Components\Select::make('item_id')
                            ->label('Barang')
                            ->relationship('item', 'name')
                            ->searchable(['name', 'item_code'])
                            ->getOptionLabelFromRecordUsing(fn($record) => "{$record->name} ({$record->item_code})")
                            ->required()
                            ->preload(),

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
                            ->native(false)
                            ->live(),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Detail Maintenance')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi Masalah/Pekerjaan')
                            ->required()
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('performed_by')
                            ->label('Dikerjakan Oleh')
                            ->maxLength(255)
                            ->helperText('Nama teknisi/vendor yang mengerjakan'),

                        Forms\Components\TextInput::make('cost')
                            ->label('Biaya')
                            ->numeric()
                            ->prefix('Rp')
                            ->inputMode('decimal'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Jadwal')
                    ->schema([
                        Forms\Components\DateTimePicker::make('performed_at')
                            ->label('Tanggal Mulai')
                            ->default(now())
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y H:i')
                            ->seconds(false),

                        Forms\Components\DateTimePicker::make('completed_at')
                            ->label('Tanggal Selesai')
                            ->native(false)
                            ->displayFormat('d/m/Y H:i')
                            ->seconds(false)
                            ->visible(
                                fn(Forms\Get $get): bool =>
                                in_array($get('status'), ['completed', 'cancelled'])
                            ),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Catatan Tambahan')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Select::make('created_by')
                            ->label('Dicatat Oleh')
                            ->relationship('creator', 'name')
                            ->default(Auth::id())
                            ->required()
                            ->disabled()
                            ->dehydrated(),
                    ])
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('item.name')
                    ->label('Barang')
                    ->searchable()
                    ->sortable()
                    ->description(fn(MaintenanceHistory $record): string => $record->item->item_code),

                Tables\Columns\TextColumn::make('maintenance_type')
                    ->label('Jenis')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'repair' => 'Perbaikan',
                        'service' => 'Servis Rutin',
                        'inspection' => 'Inspeksi',
                        'cleaning' => 'Pembersihan',
                        'calibration' => 'Kalibrasi',
                        default => $state,
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'repair' => 'danger',
                        'service' => 'info',
                        'inspection' => 'warning',
                        'cleaning' => 'success',
                        'calibration' => 'primary',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('description')
                    ->label('Deskripsi')
                    ->limit(40)
                    ->wrap(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'pending' => 'Pending',
                        'in_progress' => 'Dikerjakan',
                        'completed' => 'Selesai',
                        'cancelled' => 'Dibatalkan',
                        default => $state,
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'in_progress' => 'info',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('performed_by')
                    ->label('Teknisi')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('cost')
                    ->label('Biaya')
                    ->money('IDR')
                    ->sortable()
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->money('IDR')
                            ->label('Total'),
                    ]),

                Tables\Columns\TextColumn::make('performed_at')
                    ->label('Tgl Mulai')
                    ->dateTime('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('completed_at')
                    ->label('Tgl Selesai')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('creator.name')
                    ->label('Dicatat Oleh')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('maintenance_type')
                    ->label('Jenis')
                    ->options([
                        'repair' => 'Perbaikan',
                        'service' => 'Servis Rutin',
                        'inspection' => 'Inspeksi',
                        'cleaning' => 'Pembersihan',
                        'calibration' => 'Kalibrasi',
                    ])
                    ->multiple(),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'in_progress' => 'Sedang Dikerjakan',
                        'completed' => 'Selesai',
                        'cancelled' => 'Dibatalkan',
                    ])
                    ->multiple(),

                Tables\Filters\SelectFilter::make('item_id')
                    ->label('Barang')
                    ->relationship('item', 'name')
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
                                fn(Builder $query, $date): Builder => $query->whereDate('performed_at', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('performed_at', '<=', $date),
                            );
                    }),

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
            'index' => Pages\ListMaintenanceHistories::route('/'),
            'create' => Pages\CreateMaintenanceHistory::route('/create'),
            // 'view' => Pages\ViewMaintenanceHistory::route('/{record}'),
            'edit' => Pages\EditMaintenanceHistory::route('/{record}/edit'),
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

```

---

### 📄 File: `./app/Filament/Resources/RfidCardResource.php`

```php
<?php
// app/Filament/Resources/RfidCardResource.php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\RfidCard;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Collection;
use Filament\Support\Enums\FontWeight;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\RfidCardResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RfidCardResource extends Resource
{
    protected static ?string $model = RfidCard::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationLabel = 'Kartu RFID';

    protected static ?string $modelLabel = 'Kartu RFID';

    protected static ?string $pluralModelLabel = 'Kartu RFID';

    protected static ?string $navigationGroup = 'Manajemen Akses';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Kartu')
                    ->schema([
                        Forms\Components\TextInput::make('uid')
                            ->label('UID Kartu')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->helperText('UID unik dari kartu RFID')
                            ->suffixIcon('heroicon-m-identification')
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('card_number')
                            ->label('Nomor Kartu')
                            ->maxLength(255)
                            ->helperText('Nomor yang tercetak di kartu fisik (opsional)'),

                        Forms\Components\Select::make('user_id')
                            ->label('Pemilik Kartu')
                            ->relationship('user', 'name')
                            ->searchable(['name', 'identity_number', 'email'])
                            ->getOptionLabelFromRecordUsing(fn($record) => "{$record->name} ({$record->identity_number})")
                            ->preload()
                            ->nullable()
                            ->helperText('Kosongkan jika kartu belum di-assign'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Status & Validitas')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Kartu Aktif')
                            ->default(true)
                            ->helperText('Nonaktifkan untuk memblokir kartu'),

                        Forms\Components\DateTimePicker::make('issued_at')
                            ->label('Tanggal Terbit')
                            ->default(now())
                            ->native(false)
                            ->displayFormat('d/m/Y H:i')
                            ->seconds(false),

                        Forms\Components\DateTimePicker::make('expired_at')
                            ->label('Tanggal Kadaluarsa')
                            ->native(false)
                            ->displayFormat('d/m/Y H:i')
                            ->seconds(false)
                            ->helperText('Kosongkan untuk masa berlaku tanpa batas'),

                        Forms\Components\TextInput::make('failed_attempts')
                            ->label('Percobaan Gagal')
                            ->numeric()
                            ->default(0)
                            ->disabled()
                            ->dehydrated()
                            ->helperText('Counter untuk deteksi aktivitas mencurigakan'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Riwayat Penggunaan')
                    ->schema([
                        Forms\Components\DateTimePicker::make('last_used_at')
                            ->label('Terakhir Digunakan')
                            ->native(false)
                            ->displayFormat('d/m/Y H:i')
                            ->disabled()
                            ->dehydrated(),

                        Forms\Components\TextInput::make('last_used_for')
                            ->label('Digunakan Untuk')
                            ->maxLength(255)
                            ->disabled()
                            ->dehydrated()
                            ->helperText('Aktivitas terakhir: loan/return'),
                    ])
                    ->columns(2)
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('uid')
                    ->label('UID Kartu')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight(FontWeight::Bold),

                Tables\Columns\TextColumn::make('card_number')
                    ->label('No. Kartu')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Pemilik')
                    ->searchable()
                    ->sortable()
                    ->description(
                        fn(RfidCard $record): string =>
                        $record->user ? "{$record->user->identity_number} - {$record->user->role->label()}" : 'Belum di-assign'
                    )
                    ->placeholder('Belum di-assign')
                    ->color(fn(RfidCard $record): string => $record->user ? 'success' : 'gray'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),

                Tables\Columns\TextColumn::make('issued_at')
                    ->label('Tgl Terbit')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('expired_at')
                    ->label('Kadaluarsa')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->placeholder('Tidak terbatas')
                    ->color(
                        fn(RfidCard $record): string =>
                        $record->expired_at && $record->expired_at < now() ? 'danger' : 'success'
                    )
                    ->toggleable(),

                Tables\Columns\TextColumn::make('last_used_at')
                    ->label('Terakhir Digunakan')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->since()
                    ->placeholder('Belum pernah'),

                Tables\Columns\TextColumn::make('failed_attempts')
                    ->label('Gagal')
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->color(
                        fn(RfidCard $record): string =>
                        $record->failed_attempts > 5 ? 'danger' : ($record->failed_attempts > 0 ? 'warning' : 'success')
                    )
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user_id')
                    ->label('Pemilik')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Kartu')
                    ->placeholder('Semua')
                    ->trueLabel('Aktif')
                    ->falseLabel('Nonaktif'),

                Tables\Filters\TernaryFilter::make('assigned')
                    ->label('Assignment')
                    ->placeholder('Semua')
                    ->trueLabel('Sudah di-assign')
                    ->falseLabel('Belum di-assign')
                    ->queries(
                        true: fn(Builder $query) => $query->whereNotNull('user_id'),
                        false: fn(Builder $query) => $query->whereNull('user_id'),
                    ),

                Tables\Filters\Filter::make('expired')
                    ->label('Kadaluarsa')
                    ->query(
                        fn(Builder $query): Builder =>
                        $query->whereNotNull('expired_at')
                            ->where('expired_at', '<', now())
                    )
                    ->toggle(),

                Tables\Filters\Filter::make('suspicious')
                    ->label('Aktivitas Mencurigakan')
                    ->query(
                        fn(Builder $query): Builder =>
                        $query->where('failed_attempts', '>', 5)
                    )
                    ->toggle(),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),

                Tables\Actions\Action::make('assign')
                    ->label('Assign User')
                    ->icon('heroicon-o-user-plus')
                    ->color('info')
                    ->visible(fn(RfidCard $record): bool => !$record->user_id)
                    ->form([
                        Forms\Components\Select::make('user_id')
                            ->label('Pilih User')
                            ->relationship('user', 'name')
                            ->searchable(['name', 'identity_number', 'email'])
                            ->getOptionLabelFromRecordUsing(
                                fn($record) =>
                                "{$record->name} ({$record->identity_number}) - {$record->role->label()}"
                            )
                            ->required()
                            ->preload(),
                    ])
                    ->action(function (RfidCard $record, array $data): void {
                        $record->update($data);
                    }),

                Tables\Actions\Action::make('unassign')
                    ->label('Lepas User')
                    ->icon('heroicon-o-user-minus')
                    ->color('warning')
                    ->visible(fn(RfidCard $record): bool => $record->user_id !== null)
                    ->requiresConfirmation()
                    ->modalHeading('Lepas User dari Kartu')
                    ->modalDescription('Apakah Anda yakin ingin melepas user dari kartu ini?')
                    ->action(function (RfidCard $record): void {
                        $record->update(['user_id' => null]);
                    }),

                Tables\Actions\Action::make('resetFailedAttempts')
                    ->label('Reset Counter')
                    ->icon('heroicon-o-arrow-path')
                    ->color('success')
                    ->visible(fn(RfidCard $record): bool => $record->failed_attempts > 0)
                    ->requiresConfirmation()
                    ->action(function (RfidCard $record): void {
                        $record->update(['failed_attempts' => 0]);
                    }),

                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Aktifkan')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn(Collection $records) => $records->each->update(['is_active' => true])),

                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Nonaktifkan')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(fn(Collection $records) => $records->each->update(['is_active' => false])),

                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListRfidCards::route('/'),
            'create' => Pages\CreateRfidCard::route('/create'),
            // 'view' => Pages\ViewRfidCard::route('/{record}'),
            'edit' => Pages\EditRfidCard::route('/{record}/edit'),
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
        return static::getModel()::whereNull('user_id')->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}

```

---

### 📄 File: `./app/Filament/Resources/RfidLogResource.php`

```php
<?php
// app/Filament/Resources/RfidLogResource.php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\RfidLog;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\RfidLogResource\Pages;

class RfidLogResource extends Resource
{
    protected static ?string $model = RfidLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'Log RFID';

    protected static ?string $modelLabel = 'Log RFID';

    protected static ?string $pluralModelLabel = 'Log RFID';

    protected static ?string $navigationGroup = 'Laporan';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('rfid_card_id')
                    ->label('Kartu RFID')
                    ->relationship('rfidCard', 'uid')
                    ->required()
                    ->disabled(),

                Forms\Components\Select::make('user_id')
                    ->label('User')
                    ->relationship('user', 'name')
                    ->disabled(),

                Forms\Components\TextInput::make('action')
                    ->label('Aksi')
                    ->required()
                    ->disabled(),

                Forms\Components\TextInput::make('status')
                    ->label('Status')
                    ->required()
                    ->disabled(),

                Forms\Components\Textarea::make('response_message')
                    ->label('Pesan Respon')
                    ->disabled()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),

                Tables\Columns\TextColumn::make('rfidCard.uid')
                    ->label('UID Kartu')
                    ->searchable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->description(
                        fn(RfidLog $record): string =>
                        $record->user ? $record->user->identity_number : '-'
                    )
                    ->placeholder('Unknown'),

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
                    })
                    ->sortable(),

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
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('item.name')
                    ->label('Barang')
                    ->searchable()
                    ->limit(30)
                    ->placeholder('-')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('location')
                    ->label('Lokasi Reader')
                    ->searchable()
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('response_message')
                    ->label('Pesan')
                    ->limit(40)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('action')
                    ->label('Aksi')
                    ->options([
                        'tap' => 'Tap',
                        'loan' => 'Pinjam',
                        'return' => 'Kembali',
                        'failed' => 'Gagal',
                    ])
                    ->multiple(),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'success' => 'Sukses',
                        'failed' => 'Gagal',
                        'suspended' => 'Suspended',
                    ])
                    ->multiple(),

                Tables\Filters\SelectFilter::make('user_id')
                    ->label('User')
                    ->relationship('user', 'name')
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
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('10s'); // Auto-refresh setiap 10 detik
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
            'index' => Pages\ListRfidLogs::route('/'),
            // 'view' => Pages\ViewRfidLog::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false; // Log tidak bisa dibuat manual
    }
}

```

---

### 📄 File: `./app/Filament/Resources/SettingResource.php`

```php
<?php
// app/Filament/Resources/SettingResource.php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Setting;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\SettingResource\Pages;

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
                            ->disabled(fn(string $operation): bool => $operation === 'edit'),

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
                                fn(Forms\Get $get): bool =>
                                in_array($get('type'), ['string', null])
                            ),

                        // Integer input
                        Forms\Components\TextInput::make('value')
                            ->label('Nilai')
                            ->numeric()
                            ->visible(fn(Forms\Get $get): bool => $get('type') === 'integer'),

                        // Decimal input
                        Forms\Components\TextInput::make('value')
                            ->label('Nilai')
                            ->numeric()
                            ->inputMode('decimal')
                            ->visible(fn(Forms\Get $get): bool => $get('type') === 'decimal'),

                        // Boolean input
                        Forms\Components\Toggle::make('value')
                            ->label('Nilai')
                            ->onColor('success')
                            ->offColor('danger')
                            ->visible(fn(Forms\Get $get): bool => $get('type') === 'boolean')
                            ->afterStateHydrated(function (Forms\Components\Toggle $component, $state) {
                                // Convert string 'true'/'false' to boolean
                                if (is_string($state)) {
                                    $component->state($state === 'true' || $state === '1');
                                }
                            })
                            ->dehydrateStateUsing(fn($state) => $state ? 'true' : 'false'),

                        // JSON input
                        Forms\Components\Textarea::make('value')
                            ->label('Nilai JSON')
                            ->rows(5)
                            ->visible(fn(Forms\Get $get): bool => $get('type') === 'json')
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
                    ->formatStateUsing(fn(?string $state): string => match ($state) {
                        'general' => 'Umum',
                        'loan' => 'Peminjaman',
                        'penalty' => 'Denda',
                        'notification' => 'Notifikasi',
                        'system' => 'Sistem',
                        default => $state ?? 'Lainnya',
                    })
                    ->color(fn(?string $state): string => match ($state) {
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
                            return substr($state, 0, 40) . (strlen($state) > 40 ? '...' : '');
                        }
                        return $state;
                    }),

                Tables\Columns\TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
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
                        fn(Setting $record): bool =>
                        !in_array($record->key, [
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

```

---

### 📄 File: `./app/Filament/Resources/StockMovementResource.php`

```php
<?php
// app/Filament/Resources/StockMovementResource.php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\StockMovement;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\StockMovementResource\Pages;

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
                            ->getOptionLabelFromRecordUsing(fn($record) => "{$record->name} ({$record->item_code})")
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
                                fn(Forms\Get $get): bool =>
                                in_array($get('movement_type'), ['out', 'transfer'])
                            ),

                        Forms\Components\Select::make('to_location_id')
                            ->label('Ke Lokasi')
                            ->relationship('toLocation', 'name')
                            ->searchable()
                            ->preload()
                            ->visible(
                                fn(Forms\Get $get): bool =>
                                in_array($get('movement_type'), ['in', 'transfer'])
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
                    ->description(fn(StockMovement $record): string => $record->item->item_code),

                Tables\Columns\TextColumn::make('movement_type')
                    ->label('Jenis')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'in' => 'Masuk',
                        'out' => 'Keluar',
                        'adjustment' => 'Penyesuaian',
                        'transfer' => 'Transfer',
                        default => $state,
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'in' => 'success',
                        'out' => 'danger',
                        'adjustment' => 'warning',
                        'transfer' => 'info',
                        default => 'gray',
                    })
                    ->icon(fn(string $state): string => match ($state) {
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
                        fn(StockMovement $record): string =>
                        $record->movement_type === 'in' ? 'success' : 'danger'
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
                                fn(Builder $query, $date): Builder => $query->whereDate('performed_at', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('performed_at', '<=', $date),
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

```

---

### 📄 File: `./app/Filament/Resources/UserResource.php`

```php
<?php
// app/Filament/Resources/UserResource.php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Enums\UserRole;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\UserResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UserResource\RelationManagers;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Pengguna';

    protected static ?string $modelLabel = 'Pengguna';

    protected static ?string $pluralModelLabel = 'Pengguna';

    protected static ?string $navigationGroup = 'Manajemen Akses';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Akun')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        Forms\Components\TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->dehydrateStateUsing(fn($state) => Hash::make($state))
                            ->dehydrated(fn($state) => filled($state))
                            ->required(fn(string $operation): bool => $operation === 'create')
                            ->maxLength(255)
                            ->revealable(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Informasi Identitas')
                    ->schema([
                        Forms\Components\TextInput::make('identity_number')
                            ->label('NISN / NIP')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->helperText('NISN untuk siswa, NIP untuk guru/staff'),

                        Forms\Components\Select::make('role')
                            ->label('Role')
                            ->options(UserRole::class)
                            ->default(UserRole::STUDENT)
                            ->required()
                            ->native(false)
                            ->live(),

                        Forms\Components\TextInput::make('phone')
                            ->label('Nomor Telepon')
                            ->tel()
                            ->maxLength(255),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Informasi Akademik')
                    ->schema([
                        Forms\Components\TextInput::make('department')
                            ->label('Jurusan / Jabatan')
                            ->maxLength(255)
                            ->helperText('Jurusan untuk siswa, Jabatan untuk guru/staff'),

                        Forms\Components\TextInput::make('class')
                            ->label('Kelas')
                            ->maxLength(255)
                            ->visible(fn(Forms\Get $get): bool => $get('role') === UserRole::STUDENT->value)
                            ->helperText('Contoh: X IPA 1'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Pengaturan Peminjaman')
                    ->schema([
                        Forms\Components\TextInput::make('max_loan_items')
                            ->label('Maksimal Peminjaman')
                            ->numeric()
                            ->default(3)
                            ->minValue(1)
                            ->maxValue(10)
                            ->required()
                            ->helperText('Jumlah maksimal barang yang dapat dipinjam'),

                        Forms\Components\TextInput::make('loan_duration_days')
                            ->label('Durasi Peminjaman (Hari)')
                            ->numeric()
                            ->default(7)
                            ->minValue(1)
                            ->maxValue(30)
                            ->required()
                            ->helperText('Durasi default peminjaman'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Status Suspend')
                    ->schema([
                        Forms\Components\Toggle::make('is_suspended')
                            ->label('Suspend User')
                            ->default(false)
                            ->live()
                            ->helperText('Blokir user dari melakukan peminjaman'),

                        Forms\Components\DateTimePicker::make('suspended_until')
                            ->label('Suspend Hingga')
                            ->native(false)
                            ->displayFormat('d/m/Y H:i')
                            ->seconds(false)
                            ->visible(fn(Forms\Get $get): bool => $get('is_suspended'))
                            ->helperText('Kosongkan untuk suspend permanent'),

                        Forms\Components\Textarea::make('suspension_reason')
                            ->label('Alasan Suspend')
                            ->rows(3)
                            ->visible(fn(Forms\Get $get): bool => $get('is_suspended'))
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('identity_number')
                    ->label('NISN/NIP')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable()
                    ->description(fn(User $record): string => $record->email),

                Tables\Columns\TextColumn::make('role')
                    ->label('Role')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('department')
                    ->label('Jurusan/Jabatan')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('class')
                    ->label('Kelas')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Telepon')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\IconColumn::make('rfidCard.is_active')
                    ->label('RFID Card')
                    ->boolean()
                    ->trueIcon('heroicon-o-credit-card')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->default(false)
                    ->getStateUsing(fn(User $record): bool => $record->rfidCard !== null && $record->rfidCard->is_active),

                Tables\Columns\TextColumn::make('active_loans_count')
                    ->label('Pinjaman Aktif')
                    ->counts('loans', fn(Builder $query) => $query->where('status', \App\Enums\LoanStatus::ACTIVE))
                    ->badge()
                    ->color(
                        fn(int $state, User $record): string =>
                        $state >= $record->max_loan_items ? 'danger' : ($state > 0 ? 'warning' : 'success')
                    )
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_suspended')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-no-symbol')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('danger')
                    ->falseColor('success')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Terdaftar')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->label('Role')
                    ->options(UserRole::class)
                    ->multiple()
                    ->preload(),

                Tables\Filters\TernaryFilter::make('is_suspended')
                    ->label('Status')
                    ->placeholder('Semua')
                    ->trueLabel('Suspended')
                    ->falseLabel('Aktif'),

                Tables\Filters\Filter::make('has_rfid')
                    ->label('Punya Kartu RFID')
                    ->query(fn(Builder $query): Builder => $query->has('rfidCard'))
                    ->toggle(),

                Tables\Filters\Filter::make('has_active_loans')
                    ->label('Punya Pinjaman Aktif')
                    ->query(
                        fn(Builder $query): Builder =>
                        $query->whereHas('loans', fn($q) => $q->where('status', \App\Enums\LoanStatus::ACTIVE))
                    )
                    ->toggle(),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),

                Tables\Actions\Action::make('suspend')
                    ->label('Suspend')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->visible(fn(User $record): bool => !$record->is_suspended)
                    ->form([
                        Forms\Components\DateTimePicker::make('suspended_until')
                            ->label('Suspend Hingga')
                            ->native(false)
                            ->displayFormat('d/m/Y H:i')
                            ->helperText('Kosongkan untuk suspend permanent'),

                        Forms\Components\Textarea::make('suspension_reason')
                            ->label('Alasan Suspend')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (User $record, array $data): void {
                        $record->update([
                            'is_suspended' => true,
                            'suspended_until' => $data['suspended_until'] ?? null,
                            'suspension_reason' => $data['suspension_reason'],
                        ]);
                    }),

                Tables\Actions\Action::make('unsuspend')
                    ->label('Aktifkan')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn(User $record): bool => $record->is_suspended)
                    ->requiresConfirmation()
                    ->action(function (User $record): void {
                        $record->update([
                            'is_suspended' => false,
                            'suspended_until' => null,
                            'suspension_reason' => null,
                        ]);
                    }),

                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\LoansRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            // 'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
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
        return static::getModel()::where('is_suspended', true)->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $count = static::getModel()::where('is_suspended', true)->count();
        return $count > 0 ? 'danger' : null;
    }
}

```

---

### 📄 File: `./app/Filament/Resources/UserResource/RelationManagers/LoansRelationManager.php`

```php
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

```

---

### 📄 File: `./app/Filament/Widgets/LoanStatusDistribution.php`

```php
<?php
// app/Filament/Widgets/LoanStatusDistribution.php

namespace App\Filament\Widgets;

use App\Models\Loan;
use App\Enums\LoanStatus;
use Filament\Widgets\ChartWidget;

class LoanStatusDistribution extends ChartWidget
{
    protected static ?string $heading = 'Distribusi Status Peminjaman';

    protected static ?int $sort = 6;

    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $statusCounts = [
            'active' => Loan::where('status', LoanStatus::ACTIVE)->count(),
            'returned' => Loan::where('status', LoanStatus::RETURNED)->count(),
            'overdue' => Loan::where('status', LoanStatus::ACTIVE)
                ->where('due_date', '<', now())
                ->count(),
            'cancelled' => Loan::where('status', LoanStatus::CANCELLED)->count(),
        ];

        return [
            'datasets' => [
                [
                    'data' => array_values($statusCounts),
                    'backgroundColor' => [
                        'rgba(59, 130, 246, 0.8)',   // Active - Blue
                        'rgba(16, 185, 129, 0.8)',   // Returned - Green
                        'rgba(239, 68, 68, 0.8)',    // Overdue - Red
                        'rgba(156, 163, 175, 0.8)',  // Cancelled - Gray
                    ],
                ],
            ],
            'labels' => ['Aktif', 'Dikembalikan', 'Terlambat', 'Dibatalkan'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
            ],
        ];
    }
}

```

---

### 📄 File: `./app/Filament/Widgets/LowStockAlert.php`

```php
<?php
// app/Filament/Widgets/LowStockAlert.php

namespace App\Filament\Widgets;

use App\Models\Item;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Support\Enums\FontWeight;
use Filament\Widgets\TableWidget as BaseWidget;

class LowStockAlert extends BaseWidget
{
    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 'full';

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
                        fn(Item $record): string =>
                        $record->category->name . ' - ' . $record->location->name
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
                    ->state(fn(Item $record): int => $record->min_quantity - $record->quantity)
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
                        fn(Item $record): string =>
                        route('filament.admin.resources.stock-movements.create', [
                            'item_id' => $record->id,
                            'movement_type' => 'in'
                        ])
                    ),
            ])
            ->emptyStateHeading('Tidak ada stok yang rendah')
            ->emptyStateDescription('Semua barang memiliki stok yang cukup')
            ->emptyStateIcon('heroicon-o-check-circle');
    }
}

```

---

### 📄 File: `./app/Filament/Widgets/OverdueLoansChart.php`

```php
<?php
// app/Filament/Widgets/OverdueLoansChart.php

namespace App\Filament\Widgets;

use App\Models\Loan;
use App\Enums\LoanStatus;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class OverdueLoansChart extends ChartWidget
{
    protected static ?string $heading = 'Tren Peminjaman (30 Hari Terakhir)';

    protected static ?int $sort = 2;

    protected static ?string $pollingInterval = '60s';

    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        // Get last 30 days data
        $data = Loan::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as total'),
            DB::raw('SUM(CASE WHEN status = "' . LoanStatus::ACTIVE->value . '" AND due_date < NOW() THEN 1 ELSE 0 END) as overdue')
        )
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $labels = [];
        $totalLoans = [];
        $overdueLoans = [];

        foreach ($data as $item) {
            $labels[] = date('d M', strtotime($item->date));
            $totalLoans[] = $item->total;
            $overdueLoans[] = $item->overdue;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Peminjaman',
                    'data' => $totalLoans,
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                ],
                [
                    'label' => 'Terlambat',
                    'data' => $overdueLoans,
                    'borderColor' => 'rgb(239, 68, 68)',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                    'fill' => true,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                ],
            ],
        ];
    }
}

```

---

### 📄 File: `./app/Filament/Widgets/PopularItemsChart.php`

```php
<?php
// app/Filament/Widgets/PopularItemsChart.php

namespace App\Filament\Widgets;

use App\Models\Item;
use App\Models\Loan;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class PopularItemsChart extends ChartWidget
{
    protected static ?string $heading = 'Top 10 Barang Paling Sering Dipinjam';

    protected static ?int $sort = 5;

    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $popularItems = Loan::select('item_id', DB::raw('COUNT(*) as loan_count'))
            ->with('item')
            ->groupBy('item_id')
            ->orderByDesc('loan_count')
            ->limit(10)
            ->get();

        $labels = [];
        $data = [];

        foreach ($popularItems as $item) {
            $labels[] = $item->item->name ?? 'Unknown';
            $data[] = $item->loan_count;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Peminjaman',
                    'data' => $data,
                    'backgroundColor' => [
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(139, 92, 246, 0.8)',
                        'rgba(236, 72, 153, 0.8)',
                        'rgba(14, 165, 233, 0.8)',
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(251, 146, 60, 0.8)',
                        'rgba(168, 85, 247, 0.8)',
                    ],
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'precision' => 0,
                    ],
                ],
            ],
        ];
    }
}

```

---

### 📄 File: `./app/Filament/Widgets/RecentActivities.php`

```php
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

```

---

### 📄 File: `./app/Filament/Widgets/StatsOverview.php`

```php
<?php
// app/Filament/Widgets/StatsOverview.php

namespace App\Filament\Widgets;

use App\Models\Item;
use App\Models\Loan;
use App\Models\User;
use App\Models\RfidCard;
use App\Enums\ItemStatus;
use App\Enums\LoanStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected static ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        return Cache::remember(
            'dashboard-stats',
            300,
            function () {
                // Total Items
                $totalItems = Item::count();
                $availableItems = Item::where('status', ItemStatus::AVAILABLE)->count();
                $itemsPercentage = $totalItems > 0 ? round(($availableItems / $totalItems) * 100) : 0;

                // Active Loans
                $activeLoans = Loan::where('status', LoanStatus::ACTIVE)->count();
                $overdueLoans = Loan::where('status', LoanStatus::ACTIVE)
                    ->where('due_date', '<', now())
                    ->count();

                // Loans trend (last 7 days vs previous 7 days)
                $currentWeekLoans = Loan::whereBetween('created_at', [now()->subDays(7), now()])->count();
                $previousWeekLoans = Loan::whereBetween('created_at', [now()->subDays(14), now()->subDays(7)])->count();
                $loansTrend = $previousWeekLoans > 0
                    ? round((($currentWeekLoans - $previousWeekLoans) / $previousWeekLoans) * 100)
                    : 0;

                // Total Users
                $totalUsers = User::count();
                $activeUsers = User::where('is_suspended', false)->count();
                $suspendedUsers = User::where('is_suspended', true)->count();

                // RFID Cards
                $totalCards = RfidCard::count();
                $assignedCards = RfidCard::whereNotNull('user_id')->count();
                $activeCards = RfidCard::where('is_active', true)->count();

                // Unpaid Penalties
                $unpaidPenalties = Loan::where('penalty_amount', '>', 0)
                    ->where('is_paid', false)
                    ->sum('penalty_amount');

                // Low Stock Items
                $lowStockItems = Item::whereColumn('quantity', '<=', 'min_quantity')
                    ->whereNotNull('min_quantity')
                    ->count();

                return [
                    Stat::make('Total Barang', $totalItems)
                        ->description("{$availableItems} tersedia ({$itemsPercentage}%)")
                        ->descriptionIcon('heroicon-m-cube')
                        ->color('success')
                        ->chart([7, 3, 4, 5, 6, 3, 5, 3])
                        ->url(route('filament.admin.resources.items.index')),

                    Stat::make('Peminjaman Aktif', $activeLoans)
                        ->description($overdueLoans > 0 ? "{$overdueLoans} terlambat" : 'Semua tepat waktu')
                        ->descriptionIcon($overdueLoans > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-circle')
                        ->color($overdueLoans > 0 ? 'danger' : 'info')
                        ->chart([3, 5, 4, 6, 5, 7, 8])
                        ->url(route('filament.admin.resources.loans.index')),

                    Stat::make('Pengguna', $totalUsers)
                        ->description("{$activeUsers} aktif, {$suspendedUsers} suspended")
                        ->descriptionIcon('heroicon-m-users')
                        ->color($suspendedUsers > 0 ? 'warning' : 'success')
                        ->url(route('filament.admin.resources.users.index')),

                    Stat::make('Kartu RFID', $totalCards)
                        ->description("{$assignedCards} ter-assign, {$activeCards} aktif")
                        ->descriptionIcon('heroicon-m-credit-card')
                        ->color('info')
                        ->url(route('filament.admin.resources.rfid-cards.index')),

                    Stat::make('Denda Belum Lunas', 'Rp ' . number_format($unpaidPenalties, 0, ',', '.'))
                        ->description('Total denda keterlambatan')
                        ->descriptionIcon('heroicon-m-currency-dollar')
                        ->color($unpaidPenalties > 0 ? 'danger' : 'success')
                        ->url(route('filament.admin.resources.loans.index', ['tableFilters[unpaid_penalty][value]' => true])),

                    Stat::make('Stok Rendah', $lowStockItems)
                        ->description('Barang perlu restock')
                        ->descriptionIcon('heroicon-m-arrow-trending-down')
                        ->color($lowStockItems > 0 ? 'warning' : 'success')
                        ->url(route('filament.admin.resources.items.index', ['tableFilters[low_stock][value]' => true])),
                ];
            }
        );
    }
}

```

---

### 📄 File: `./app/Http/Controllers/Controller.php`

```php
<?php

namespace App\Http\Controllers;

abstract class Controller
{
    //
}

```

---

### 📄 File: `./app/Models/Category.php`

```php
<?php
// app/Models/Category.php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'is_consumable',
        'min_stock',
        'description',
    ];

    protected $casts = [
        'is_consumable' => 'boolean',
    ];

    // Auto-generate slug from name
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);

                // Ensure unique slug
                $originalSlug = $category->slug;
                $count = 1;

                while (static::where('slug', $category->slug)->exists()) {
                    $category->slug = $originalSlug . '-' . $count;
                    $count++;
                }
            }
        });

        static::updating(function ($category) {
            if ($category->isDirty('name') && empty($category->slug)) {
                $category->slug = Str::slug($category->name);

                // Ensure unique slug (exclude current record)
                $originalSlug = $category->slug;
                $count = 1;

                while (static::where('slug', $category->slug)->where('id', '!=', $category->id)->exists()) {
                    $category->slug = $originalSlug . '-' . $count;
                    $count++;
                }
            }
        });
    }

    // Relationships
    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }
}

```

---

### 📄 File: `./app/Models/Item.php`

```php
<?php
// app/Models/Item.php

namespace App\Models;

use App\Enums\ItemStatus;
use App\Enums\ItemCondition;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Item extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'item_code',
        'serial_number',
        'description',
        'purchase_date',
        'price',
        'warranty_expired_at',
        'condition',
        'status',
        'quantity',
        'min_quantity',
        'location_id',
        'category_id',
        'image',
    ];

    protected $casts = [
        'condition' => ItemCondition::class,
        'status' => ItemStatus::class,
        'purchase_date' => 'date',
        'warranty_expired_at' => 'date',
        'price' => 'decimal:2',
    ];

    // Relationships
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    public function maintenanceHistories(): HasMany
    {
        return $this->hasMany(MaintenanceHistory::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function rfidLogs(): HasMany
    {
        return $this->hasMany(RfidLog::class);
    }

    // Helper Methods
    public function getNameWithCodeAttribute(): string
    {
        return "{$this->name} ({$this->item_code})";
    }

    public function isLowStock(): bool
    {
        return $this->min_quantity && $this->quantity <= $this->min_quantity;
    }

    public function isWarrantyExpired(): bool
    {
        return $this->warranty_expired_at && $this->warranty_expired_at->isPast();
    }
}

```

---

### 📄 File: `./app/Models/Loan.php`

```php
<?php
// app/Models/Loan.php

namespace App\Models;

use App\Enums\LoanStatus;
use App\Enums\ItemCondition;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Loan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'loan_number',
        'user_id',
        'item_id',
        'loan_date',
        'due_date',
        'return_date',
        'returned_condition',
        'status',
        'loan_notes',
        'return_notes',
        'created_by',
        'returned_by',
        'approved_by',
        'penalty_amount',
        'is_paid',
    ];

    protected $casts = [
        'status' => LoanStatus::class,
        'returned_condition' => ItemCondition::class,
        'loan_date' => 'datetime',
        'due_date' => 'datetime',
        'return_date' => 'datetime',
        'penalty_amount' => 'decimal:2',
        'is_paid' => 'boolean',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function returnedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'returned_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rfidLogs(): HasMany
    {
        return $this->hasMany(RfidLog::class);
    }

    // Helper Methods
    public function isOverdue(): bool
    {
        return $this->status === LoanStatus::ACTIVE && $this->due_date < now();
    }

    public function getDaysLateAttribute(): int
    {
        if (!$this->isOverdue()) {
            return 0;
        }

        return now()->diffInDays($this->due_date);
    }
}

```

---

### 📄 File: `./app/Models/Location.php`

```php
<?php
// app/Models/Location.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Location extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

    public function stockMovementsFrom(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'from_location_id');
    }

    public function stockMovementsTo(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'to_location_id');
    }
}

```

---

### 📄 File: `./app/Models/MaintenanceHistory.php`

```php
<?php
// app/Models/MaintenanceHistory.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MaintenanceHistory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'item_id',
        'maintenance_type',
        'description',
        'cost',
        'performed_by',
        'performed_at',
        'completed_at',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'performed_at' => 'datetime',
        'completed_at' => 'datetime',
        'cost' => 'decimal:2',
    ];

    // Relationships
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

```

---

### 📄 File: `./app/Models/RfidCard.php`

```php
<?php
// app/Models/RfidCard.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RfidCard extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uid',
        'card_number',
        'is_active',
        'issued_at',
        'expired_at',
        'last_used_at',
        'last_used_for',
        'failed_attempts',
        'user_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'issued_at' => 'datetime',
        'expired_at' => 'datetime',
        'last_used_at' => 'datetime',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function rfidLogs(): HasMany
    {
        return $this->hasMany(RfidLog::class);
    }

    // Helper Methods
    public function isExpired(): bool
    {
        return $this->expired_at && $this->expired_at->isPast();
    }

    public function isValid(): bool
    {
        return $this->is_active && !$this->isExpired();
    }
}

```

---

### 📄 File: `./app/Models/RfidLog.php`

```php
<?php
// app/Models/RfidLog.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RfidLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'rfid_card_id',
        'user_id',
        'action',
        'status',
        'item_id',
        'loan_id',
        'location',
        'ip_address',
        'user_agent',
        'response_message',
    ];

    // Relationships
    public function rfidCard(): BelongsTo
    {
        return $this->belongsTo(RfidCard::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }
}

```

---

### 📄 File: `./app/Models/Setting.php`

```php
<?php
// app/Models/Setting.php

namespace App\Models;

use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'description',
        'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    // Cache settings untuk performa
    protected static function booted()
    {
        static::saved(function () {
            Cache::forget('settings');
        });

        static::deleted(function () {
            Cache::forget('settings');
        });
    }

    // Helper method untuk get setting dengan cache
    public static function getValue(string $key, $default = null)
    {
        $settings = Cache::rememberForever('settings', function () {
            return static::all()->pluck('value', 'key');
        });

        $value = $settings->get($key, $default);

        // Convert berdasarkan type
        $setting = static::where('key', $key)->first();

        if ($setting) {
            return match ($setting->type) {
                'integer' => (int) $value,
                'boolean' => in_array($value, ['true', '1', 1, true], true),
                'decimal' => (float) $value,
                'json' => json_decode($value, true),
                default => $value,
            };
        }

        return $value;
    }

    // Helper method untuk set setting
    public static function setValue(string $key, $value): void
    {
        $setting = static::firstOrCreate(['key' => $key]);

        // Convert value ke string based on type
        if (is_bool($value)) {
            $value = $value ? 'true' : 'false';
        } elseif (is_array($value)) {
            $value = json_encode($value);
        }

        $setting->update(['value' => $value]);
    }
}

```

---

### 📄 File: `./app/Models/StockMovement.php`

```php
<?php
// app/Models/StockMovement.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'movement_type',
        'quantity',
        'from_location_id',
        'to_location_id',
        'reference_number',
        'reason',
        'performed_by',
        'performed_at',
    ];

    protected $casts = [
        'performed_at' => 'datetime',
    ];

    // Relationships
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function fromLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'from_location_id');
    }

    public function toLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'to_location_id');
    }

    public function performedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}

```

---

### 📄 File: `./app/Models/User.php`

```php
<?php
// app/Models/User.php

namespace App\Models;

use App\Enums\UserRole;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'identity_number',
        'phone',
        'role',
        'department',
        'class',
        'is_suspended',
        'suspended_until',
        'suspension_reason',
        'max_loan_items',
        'loan_duration_days',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'role' => UserRole::class,
        'is_suspended' => 'boolean',
        'suspended_until' => 'datetime',
    ];

    // Relationships
    public function rfidCard(): HasOne
    {
        return $this->hasOne(RfidCard::class);
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    public function createdLoans(): HasMany
    {
        return $this->hasMany(Loan::class, 'created_by');
    }

    public function returnedLoans(): HasMany
    {
        return $this->hasMany(Loan::class, 'returned_by');
    }

    public function approvedLoans(): HasMany
    {
        return $this->hasMany(Loan::class, 'approved_by');
    }

    public function rfidLogs(): HasMany
    {
        return $this->hasMany(RfidLog::class);
    }

    public function createdMaintenanceHistories(): HasMany
    {
        return $this->hasMany(MaintenanceHistory::class, 'created_by');
    }

    public function performedStockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'performed_by');
    }
}

```

---

### 📄 File: `./app/Providers/AppServiceProvider.php`

```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

```

---

### 📄 File: `./app/Providers/Filament/AdminPanelProvider.php`

```php
<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
// use Filament\Widgets;
use App\Filament\Widgets;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Filament\Http\Middleware\AuthenticateSession;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\StatsOverview::class,
                Widgets\OverdueLoansChart::class,
                Widgets\LoanStatusDistribution::class,
                Widgets\PopularItemsChart::class,
                Widgets\RecentActivities::class,
                Widgets\LowStockAlert::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}

```

---

## 📁 Directory: bootstrap

### 📄 File: `./bootstrap/app.php`

```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

```

---

### 📄 File: `./bootstrap/providers.php`

```php
<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\Filament\AdminPanelProvider::class,
];

```

---

## 📁 Directory: config (Configuration)

Application configuration files.

### 📄 File: `./config/app.php`

```php
<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application, which will be used when the
    | framework needs to place the application's name in a notification or
    | other UI elements where an application name needs to be displayed.
    |
    */

    'name' => env('APP_NAME', 'Laravel'),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | the application so that it's available within Artisan commands.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. The timezone
    | is set to "UTC" by default as it is suitable for most use cases.
    |
    */

    'timezone' => 'UTC',

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by Laravel's translation / localization methods. This option can be
    | set to any locale for which you plan to have translation strings.
    |
    */

    'locale' => env('APP_LOCALE', 'en'),

    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),

    'faker_locale' => env('APP_FAKER_LOCALE', 'en_US'),

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is utilized by Laravel's encryption services and should be set
    | to a random, 32 character string to ensure that all encrypted values
    | are secure. You should do this prior to deploying the application.
    |
    */

    'cipher' => 'AES-256-CBC',

    'key' => env('APP_KEY'),

    'previous_keys' => [
        ...array_filter(
            explode(',', (string) env('APP_PREVIOUS_KEYS', ''))
        ),
    ],

    /*
    |--------------------------------------------------------------------------
    | Maintenance Mode Driver
    |--------------------------------------------------------------------------
    |
    | These configuration options determine the driver used to determine and
    | manage Laravel's "maintenance mode" status. The "cache" driver will
    | allow maintenance mode to be controlled across multiple machines.
    |
    | Supported drivers: "file", "cache"
    |
    */

    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE', 'database'),
    ],

];

```

---

### 📄 File: `./config/auth.php`

```php
<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | This option defines the default authentication "guard" and password
    | reset "broker" for your application. You may change these values
    | as required, but they're a perfect start for most applications.
    |
    */

    'defaults' => [
        'guard' => env('AUTH_GUARD', 'web'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'users'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | Next, you may define every authentication guard for your application.
    | Of course, a great default configuration has been defined for you
    | which utilizes session storage plus the Eloquent user provider.
    |
    | All authentication guards have a user provider, which defines how the
    | users are actually retrieved out of your database or other storage
    | system used by the application. Typically, Eloquent is utilized.
    |
    | Supported: "session"
    |
    */

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    |
    | All authentication guards have a user provider, which defines how the
    | users are actually retrieved out of your database or other storage
    | system used by the application. Typically, Eloquent is utilized.
    |
    | If you have multiple user tables or models you may configure multiple
    | providers to represent the model / table. These providers may then
    | be assigned to any extra authentication guards you have defined.
    |
    | Supported: "database", "eloquent"
    |
    */

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => env('AUTH_MODEL', App\Models\User::class),
        ],

        // 'users' => [
        //     'driver' => 'database',
        //     'table' => 'users',
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    |
    | These configuration options specify the behavior of Laravel's password
    | reset functionality, including the table utilized for token storage
    | and the user provider that is invoked to actually retrieve users.
    |
    | The expiry time is the number of minutes that each reset token will be
    | considered valid. This security feature keeps tokens short-lived so
    | they have less time to be guessed. You may change this as needed.
    |
    | The throttle setting is the number of seconds a user must wait before
    | generating more password reset tokens. This prevents the user from
    | quickly generating a very large amount of password reset tokens.
    |
    */

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Confirmation Timeout
    |--------------------------------------------------------------------------
    |
    | Here you may define the number of seconds before a password confirmation
    | window expires and users are asked to re-enter their password via the
    | confirmation screen. By default, the timeout lasts for three hours.
    |
    */

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];

```

---

### 📄 File: `./config/cache.php`

```php
<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Cache Store
    |--------------------------------------------------------------------------
    |
    | This option controls the default cache store that will be used by the
    | framework. This connection is utilized if another isn't explicitly
    | specified when running a cache operation inside the application.
    |
    */

    'default' => env('CACHE_STORE', 'database'),

    /*
    |--------------------------------------------------------------------------
    | Cache Stores
    |--------------------------------------------------------------------------
    |
    | Here you may define all of the cache "stores" for your application as
    | well as their drivers. You may even define multiple stores for the
    | same cache driver to group types of items stored in your caches.
    |
    | Supported drivers: "array", "database", "file", "memcached",
    |                    "redis", "dynamodb", "octane",
    |                    "failover", "null"
    |
    */

    'stores' => [

        'array' => [
            'driver' => 'array',
            'serialize' => false,
        ],

        'database' => [
            'driver' => 'database',
            'connection' => env('DB_CACHE_CONNECTION'),
            'table' => env('DB_CACHE_TABLE', 'cache'),
            'lock_connection' => env('DB_CACHE_LOCK_CONNECTION'),
            'lock_table' => env('DB_CACHE_LOCK_TABLE'),
        ],

        'file' => [
            'driver' => 'file',
            'path' => storage_path('framework/cache/data'),
            'lock_path' => storage_path('framework/cache/data'),
        ],

        'memcached' => [
            'driver' => 'memcached',
            'persistent_id' => env('MEMCACHED_PERSISTENT_ID'),
            'sasl' => [
                env('MEMCACHED_USERNAME'),
                env('MEMCACHED_PASSWORD'),
            ],
            'options' => [
                // Memcached::OPT_CONNECT_TIMEOUT => 2000,
            ],
            'servers' => [
                [
                    'host' => env('MEMCACHED_HOST', '127.0.0.1'),
                    'port' => env('MEMCACHED_PORT', 11211),
                    'weight' => 100,
                ],
            ],
        ],

        'redis' => [
            'driver' => 'redis',
            'connection' => env('REDIS_CACHE_CONNECTION', 'cache'),
            'lock_connection' => env('REDIS_CACHE_LOCK_CONNECTION', 'default'),
        ],

        'dynamodb' => [
            'driver' => 'dynamodb',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
            'table' => env('DYNAMODB_CACHE_TABLE', 'cache'),
            'endpoint' => env('DYNAMODB_ENDPOINT'),
        ],

        'octane' => [
            'driver' => 'octane',
        ],

        'failover' => [
            'driver' => 'failover',
            'stores' => [
                'database',
                'array',
            ],
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Key Prefix
    |--------------------------------------------------------------------------
    |
    | When utilizing the APC, database, memcached, Redis, and DynamoDB cache
    | stores, there might be other applications using the same cache. For
    | that reason, you may prefix every cache key to avoid collisions.
    |
    */

    'prefix' => env('CACHE_PREFIX', Str::slug((string) env('APP_NAME', 'laravel')).'-cache-'),

];

```

---

### 📄 File: `./config/database.php`

```php
<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for database operations. This is
    | the connection which will be utilized unless another connection
    | is explicitly specified when you execute a query / statement.
    |
    */

    'default' => env('DB_CONNECTION', 'sqlite'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Below are all of the database connections defined for your application.
    | An example configuration is provided for each database system which
    | is supported by Laravel. You're free to add / remove connections.
    |
    */

    'connections' => [

        'sqlite' => [
            'driver' => 'sqlite',
            'url' => env('DB_URL'),
            'database' => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix' => '',
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
            'busy_timeout' => null,
            'journal_mode' => null,
            'synchronous' => null,
            'transaction_mode' => 'DEFERRED',
        ],

        'mysql' => [
            'driver' => 'mysql',
            'url' => env('DB_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'laravel'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => env('DB_CHARSET', 'utf8mb4'),
            'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                (PHP_VERSION_ID >= 80500 ? \Pdo\Mysql::ATTR_SSL_CA : \PDO::MYSQL_ATTR_SSL_CA) => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'mariadb' => [
            'driver' => 'mariadb',
            'url' => env('DB_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'laravel'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => env('DB_CHARSET', 'utf8mb4'),
            'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                (PHP_VERSION_ID >= 80500 ? \Pdo\Mysql::ATTR_SSL_CA : \PDO::MYSQL_ATTR_SSL_CA) => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'pgsql' => [
            'driver' => 'pgsql',
            'url' => env('DB_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'laravel'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => env('DB_CHARSET', 'utf8'),
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => 'public',
            'sslmode' => 'prefer',
        ],

        'sqlsrv' => [
            'driver' => 'sqlsrv',
            'url' => env('DB_URL'),
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', '1433'),
            'database' => env('DB_DATABASE', 'laravel'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => env('DB_CHARSET', 'utf8'),
            'prefix' => '',
            'prefix_indexes' => true,
            // 'encrypt' => env('DB_ENCRYPT', 'yes'),
            // 'trust_server_certificate' => env('DB_TRUST_SERVER_CERTIFICATE', 'false'),
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run on the database.
    |
    */

    'migrations' => [
        'table' => 'migrations',
        'update_date_on_publish' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer body of commands than a typical key-value system
    | such as Memcached. You may define your connection settings here.
    |
    */

    'redis' => [

        'client' => env('REDIS_CLIENT', 'phpredis'),

        'options' => [
            'cluster' => env('REDIS_CLUSTER', 'redis'),
            'prefix' => env('REDIS_PREFIX', Str::slug((string) env('APP_NAME', 'laravel')).'-database-'),
            'persistent' => env('REDIS_PERSISTENT', false),
        ],

        'default' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_DB', '0'),
            'max_retries' => env('REDIS_MAX_RETRIES', 3),
            'backoff_algorithm' => env('REDIS_BACKOFF_ALGORITHM', 'decorrelated_jitter'),
            'backoff_base' => env('REDIS_BACKOFF_BASE', 100),
            'backoff_cap' => env('REDIS_BACKOFF_CAP', 1000),
        ],

        'cache' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_CACHE_DB', '1'),
            'max_retries' => env('REDIS_MAX_RETRIES', 3),
            'backoff_algorithm' => env('REDIS_BACKOFF_ALGORITHM', 'decorrelated_jitter'),
            'backoff_base' => env('REDIS_BACKOFF_BASE', 100),
            'backoff_cap' => env('REDIS_BACKOFF_CAP', 1000),
        ],

    ],

];

```

---

### 📄 File: `./config/filesystems.php`

```php
<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application for file storage.
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Below you may configure as many filesystem disks as necessary, and you
    | may even configure multiple disks for the same driver. Examples for
    | most supported storage drivers are configured here for reference.
    |
    | Supported drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'serve' => true,
            'throw' => false,
            'report' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'throw' => false,
            'report' => false,
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
            'report' => false,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];

```

---

### 📄 File: `./config/logging.php`

```php
<?php

use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;
use Monolog\Processor\PsrLogMessageProcessor;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that is utilized to write
    | messages to your logs. The value provided here should match one of
    | the channels present in the list of "channels" configured below.
    |
    */

    'default' => env('LOG_CHANNEL', 'stack'),

    /*
    |--------------------------------------------------------------------------
    | Deprecations Log Channel
    |--------------------------------------------------------------------------
    |
    | This option controls the log channel that should be used to log warnings
    | regarding deprecated PHP and library features. This allows you to get
    | your application ready for upcoming major versions of dependencies.
    |
    */

    'deprecations' => [
        'channel' => env('LOG_DEPRECATIONS_CHANNEL', 'null'),
        'trace' => env('LOG_DEPRECATIONS_TRACE', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. Laravel
    | utilizes the Monolog PHP logging library, which includes a variety
    | of powerful log handlers and formatters that you're free to use.
    |
    | Available drivers: "single", "daily", "slack", "syslog",
    |                    "errorlog", "monolog", "custom", "stack"
    |
    */

    'channels' => [

        'stack' => [
            'driver' => 'stack',
            'channels' => explode(',', (string) env('LOG_STACK', 'single')),
            'ignore_exceptions' => false,
        ],

        'single' => [
            'driver' => 'single',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => env('LOG_DAILY_DAYS', 14),
            'replace_placeholders' => true,
        ],

        'slack' => [
            'driver' => 'slack',
            'url' => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => env('LOG_SLACK_USERNAME', 'Laravel Log'),
            'emoji' => env('LOG_SLACK_EMOJI', ':boom:'),
            'level' => env('LOG_LEVEL', 'critical'),
            'replace_placeholders' => true,
        ],

        'papertrail' => [
            'driver' => 'monolog',
            'level' => env('LOG_LEVEL', 'debug'),
            'handler' => env('LOG_PAPERTRAIL_HANDLER', SyslogUdpHandler::class),
            'handler_with' => [
                'host' => env('PAPERTRAIL_URL'),
                'port' => env('PAPERTRAIL_PORT'),
                'connectionString' => 'tls://'.env('PAPERTRAIL_URL').':'.env('PAPERTRAIL_PORT'),
            ],
            'processors' => [PsrLogMessageProcessor::class],
        ],

        'stderr' => [
            'driver' => 'monolog',
            'level' => env('LOG_LEVEL', 'debug'),
            'handler' => StreamHandler::class,
            'handler_with' => [
                'stream' => 'php://stderr',
            ],
            'formatter' => env('LOG_STDERR_FORMATTER'),
            'processors' => [PsrLogMessageProcessor::class],
        ],

        'syslog' => [
            'driver' => 'syslog',
            'level' => env('LOG_LEVEL', 'debug'),
            'facility' => env('LOG_SYSLOG_FACILITY', LOG_USER),
            'replace_placeholders' => true,
        ],

        'errorlog' => [
            'driver' => 'errorlog',
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'null' => [
            'driver' => 'monolog',
            'handler' => NullHandler::class,
        ],

        'emergency' => [
            'path' => storage_path('logs/laravel.log'),
        ],

    ],

];

```

---

### 📄 File: `./config/mail.php`

```php
<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Mailer
    |--------------------------------------------------------------------------
    |
    | This option controls the default mailer that is used to send all email
    | messages unless another mailer is explicitly specified when sending
    | the message. All additional mailers can be configured within the
    | "mailers" array. Examples of each type of mailer are provided.
    |
    */

    'default' => env('MAIL_MAILER', 'log'),

    /*
    |--------------------------------------------------------------------------
    | Mailer Configurations
    |--------------------------------------------------------------------------
    |
    | Here you may configure all of the mailers used by your application plus
    | their respective settings. Several examples have been configured for
    | you and you are free to add your own as your application requires.
    |
    | Laravel supports a variety of mail "transport" drivers that can be used
    | when delivering an email. You may specify which one you're using for
    | your mailers below. You may also add additional mailers if needed.
    |
    | Supported: "smtp", "sendmail", "mailgun", "ses", "ses-v2",
    |            "postmark", "resend", "log", "array",
    |            "failover", "roundrobin"
    |
    */

    'mailers' => [

        'smtp' => [
            'transport' => 'smtp',
            'scheme' => env('MAIL_SCHEME'),
            'url' => env('MAIL_URL'),
            'host' => env('MAIL_HOST', '127.0.0.1'),
            'port' => env('MAIL_PORT', 2525),
            'username' => env('MAIL_USERNAME'),
            'password' => env('MAIL_PASSWORD'),
            'timeout' => null,
            'local_domain' => env('MAIL_EHLO_DOMAIN', parse_url((string) env('APP_URL', 'http://localhost'), PHP_URL_HOST)),
        ],

        'ses' => [
            'transport' => 'ses',
        ],

        'postmark' => [
            'transport' => 'postmark',
            // 'message_stream_id' => env('POSTMARK_MESSAGE_STREAM_ID'),
            // 'client' => [
            //     'timeout' => 5,
            // ],
        ],

        'resend' => [
            'transport' => 'resend',
        ],

        'sendmail' => [
            'transport' => 'sendmail',
            'path' => env('MAIL_SENDMAIL_PATH', '/usr/sbin/sendmail -bs -i'),
        ],

        'log' => [
            'transport' => 'log',
            'channel' => env('MAIL_LOG_CHANNEL'),
        ],

        'array' => [
            'transport' => 'array',
        ],

        'failover' => [
            'transport' => 'failover',
            'mailers' => [
                'smtp',
                'log',
            ],
            'retry_after' => 60,
        ],

        'roundrobin' => [
            'transport' => 'roundrobin',
            'mailers' => [
                'ses',
                'postmark',
            ],
            'retry_after' => 60,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Global "From" Address
    |--------------------------------------------------------------------------
    |
    | You may wish for all emails sent by your application to be sent from
    | the same address. Here you may specify a name and address that is
    | used globally for all emails that are sent by your application.
    |
    */

    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
        'name' => env('MAIL_FROM_NAME', 'Example'),
    ],

];

```

---

### 📄 File: `./config/queue.php`

```php
<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Queue Connection Name
    |--------------------------------------------------------------------------
    |
    | Laravel's queue supports a variety of backends via a single, unified
    | API, giving you convenient access to each backend using identical
    | syntax for each. The default queue connection is defined below.
    |
    */

    'default' => env('QUEUE_CONNECTION', 'database'),

    /*
    |--------------------------------------------------------------------------
    | Queue Connections
    |--------------------------------------------------------------------------
    |
    | Here you may configure the connection options for every queue backend
    | used by your application. An example configuration is provided for
    | each backend supported by Laravel. You're also free to add more.
    |
    | Drivers: "sync", "database", "beanstalkd", "sqs", "redis",
    |          "deferred", "background", "failover", "null"
    |
    */

    'connections' => [

        'sync' => [
            'driver' => 'sync',
        ],

        'database' => [
            'driver' => 'database',
            'connection' => env('DB_QUEUE_CONNECTION'),
            'table' => env('DB_QUEUE_TABLE', 'jobs'),
            'queue' => env('DB_QUEUE', 'default'),
            'retry_after' => (int) env('DB_QUEUE_RETRY_AFTER', 90),
            'after_commit' => false,
        ],

        'beanstalkd' => [
            'driver' => 'beanstalkd',
            'host' => env('BEANSTALKD_QUEUE_HOST', 'localhost'),
            'queue' => env('BEANSTALKD_QUEUE', 'default'),
            'retry_after' => (int) env('BEANSTALKD_QUEUE_RETRY_AFTER', 90),
            'block_for' => 0,
            'after_commit' => false,
        ],

        'sqs' => [
            'driver' => 'sqs',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'prefix' => env('SQS_PREFIX', 'https://sqs.us-east-1.amazonaws.com/your-account-id'),
            'queue' => env('SQS_QUEUE', 'default'),
            'suffix' => env('SQS_SUFFIX'),
            'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
            'after_commit' => false,
        ],

        'redis' => [
            'driver' => 'redis',
            'connection' => env('REDIS_QUEUE_CONNECTION', 'default'),
            'queue' => env('REDIS_QUEUE', 'default'),
            'retry_after' => (int) env('REDIS_QUEUE_RETRY_AFTER', 90),
            'block_for' => null,
            'after_commit' => false,
        ],

        'deferred' => [
            'driver' => 'deferred',
        ],

        'background' => [
            'driver' => 'background',
        ],

        'failover' => [
            'driver' => 'failover',
            'connections' => [
                'database',
                'deferred',
            ],
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Job Batching
    |--------------------------------------------------------------------------
    |
    | The following options configure the database and table that store job
    | batching information. These options can be updated to any database
    | connection and table which has been defined by your application.
    |
    */

    'batching' => [
        'database' => env('DB_CONNECTION', 'sqlite'),
        'table' => 'job_batches',
    ],

    /*
    |--------------------------------------------------------------------------
    | Failed Queue Jobs
    |--------------------------------------------------------------------------
    |
    | These options configure the behavior of failed queue job logging so you
    | can control how and where failed jobs are stored. Laravel ships with
    | support for storing failed jobs in a simple file or in a database.
    |
    | Supported drivers: "database-uuids", "dynamodb", "file", "null"
    |
    */

    'failed' => [
        'driver' => env('QUEUE_FAILED_DRIVER', 'database-uuids'),
        'database' => env('DB_CONNECTION', 'sqlite'),
        'table' => 'failed_jobs',
    ],

];

```

---

### 📄 File: `./config/services.php`

```php
<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

];

```

---

### 📄 File: `./config/session.php`

```php
<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Session Driver
    |--------------------------------------------------------------------------
    |
    | This option determines the default session driver that is utilized for
    | incoming requests. Laravel supports a variety of storage options to
    | persist session data. Database storage is a great default choice.
    |
    | Supported: "file", "cookie", "database", "memcached",
    |            "redis", "dynamodb", "array"
    |
    */

    'driver' => env('SESSION_DRIVER', 'database'),

    /*
    |--------------------------------------------------------------------------
    | Session Lifetime
    |--------------------------------------------------------------------------
    |
    | Here you may specify the number of minutes that you wish the session
    | to be allowed to remain idle before it expires. If you want them
    | to expire immediately when the browser is closed then you may
    | indicate that via the expire_on_close configuration option.
    |
    */

    'lifetime' => (int) env('SESSION_LIFETIME', 120),

    'expire_on_close' => env('SESSION_EXPIRE_ON_CLOSE', false),

    /*
    |--------------------------------------------------------------------------
    | Session Encryption
    |--------------------------------------------------------------------------
    |
    | This option allows you to easily specify that all of your session data
    | should be encrypted before it's stored. All encryption is performed
    | automatically by Laravel and you may use the session like normal.
    |
    */

    'encrypt' => env('SESSION_ENCRYPT', false),

    /*
    |--------------------------------------------------------------------------
    | Session File Location
    |--------------------------------------------------------------------------
    |
    | When utilizing the "file" session driver, the session files are placed
    | on disk. The default storage location is defined here; however, you
    | are free to provide another location where they should be stored.
    |
    */

    'files' => storage_path('framework/sessions'),

    /*
    |--------------------------------------------------------------------------
    | Session Database Connection
    |--------------------------------------------------------------------------
    |
    | When using the "database" or "redis" session drivers, you may specify a
    | connection that should be used to manage these sessions. This should
    | correspond to a connection in your database configuration options.
    |
    */

    'connection' => env('SESSION_CONNECTION'),

    /*
    |--------------------------------------------------------------------------
    | Session Database Table
    |--------------------------------------------------------------------------
    |
    | When using the "database" session driver, you may specify the table to
    | be used to store sessions. Of course, a sensible default is defined
    | for you; however, you're welcome to change this to another table.
    |
    */

    'table' => env('SESSION_TABLE', 'sessions'),

    /*
    |--------------------------------------------------------------------------
    | Session Cache Store
    |--------------------------------------------------------------------------
    |
    | When using one of the framework's cache driven session backends, you may
    | define the cache store which should be used to store the session data
    | between requests. This must match one of your defined cache stores.
    |
    | Affects: "dynamodb", "memcached", "redis"
    |
    */

    'store' => env('SESSION_STORE'),

    /*
    |--------------------------------------------------------------------------
    | Session Sweeping Lottery
    |--------------------------------------------------------------------------
    |
    | Some session drivers must manually sweep their storage location to get
    | rid of old sessions from storage. Here are the chances that it will
    | happen on a given request. By default, the odds are 2 out of 100.
    |
    */

    'lottery' => [2, 100],

    /*
    |--------------------------------------------------------------------------
    | Session Cookie Name
    |--------------------------------------------------------------------------
    |
    | Here you may change the name of the session cookie that is created by
    | the framework. Typically, you should not need to change this value
    | since doing so does not grant a meaningful security improvement.
    |
    */

    'cookie' => env(
        'SESSION_COOKIE',
        Str::slug((string) env('APP_NAME', 'laravel')).'-session'
    ),

    /*
    |--------------------------------------------------------------------------
    | Session Cookie Path
    |--------------------------------------------------------------------------
    |
    | The session cookie path determines the path for which the cookie will
    | be regarded as available. Typically, this will be the root path of
    | your application, but you're free to change this when necessary.
    |
    */

    'path' => env('SESSION_PATH', '/'),

    /*
    |--------------------------------------------------------------------------
    | Session Cookie Domain
    |--------------------------------------------------------------------------
    |
    | This value determines the domain and subdomains the session cookie is
    | available to. By default, the cookie will be available to the root
    | domain without subdomains. Typically, this shouldn't be changed.
    |
    */

    'domain' => env('SESSION_DOMAIN'),

    /*
    |--------------------------------------------------------------------------
    | HTTPS Only Cookies
    |--------------------------------------------------------------------------
    |
    | By setting this option to true, session cookies will only be sent back
    | to the server if the browser has a HTTPS connection. This will keep
    | the cookie from being sent to you when it can't be done securely.
    |
    */

    'secure' => env('SESSION_SECURE_COOKIE'),

    /*
    |--------------------------------------------------------------------------
    | HTTP Access Only
    |--------------------------------------------------------------------------
    |
    | Setting this value to true will prevent JavaScript from accessing the
    | value of the cookie and the cookie will only be accessible through
    | the HTTP protocol. It's unlikely you should disable this option.
    |
    */

    'http_only' => env('SESSION_HTTP_ONLY', true),

    /*
    |--------------------------------------------------------------------------
    | Same-Site Cookies
    |--------------------------------------------------------------------------
    |
    | This option determines how your cookies behave when cross-site requests
    | take place, and can be used to mitigate CSRF attacks. By default, we
    | will set this value to "lax" to permit secure cross-site requests.
    |
    | See: https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Set-Cookie#samesitesamesite-value
    |
    | Supported: "lax", "strict", "none", null
    |
    */

    'same_site' => env('SESSION_SAME_SITE', 'lax'),

    /*
    |--------------------------------------------------------------------------
    | Partitioned Cookies
    |--------------------------------------------------------------------------
    |
    | Setting this value to true will tie the cookie to the top-level site for
    | a cross-site context. Partitioned cookies are accepted by the browser
    | when flagged "secure" and the Same-Site attribute is set to "none".
    |
    */

    'partitioned' => env('SESSION_PARTITIONED_COOKIE', false),

];

```

---

## 📁 Directory: database (Database)

Migrations, seeders, and factories.

### 📄 File: `./database/.gitignore`

```
*.sqlite*

```

---

### 📄 File: `./database/factories/CategoryFactory.php`

```php
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'slug' => fake()->slug(),
            'is_consumable' => fake()->boolean(),
            'min_stock' => fake()->numberBetween(-10000, 10000),
            'description' => fake()->text(),
        ];
    }
}

```

---

### 📄 File: `./database/factories/ItemFactory.php`

```php
<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'item_code' => fake()->word(),
            'serial_number' => fake()->word(),
            'description' => fake()->text(),
            'purchase_date' => fake()->date(),
            'price' => fake()->randomFloat(2, 0, 9999999999999.99),
            'warranty_expired_at' => fake()->date(),
            'condition' => fake()->word(),
            'status' => fake()->word(),
            'quantity' => fake()->numberBetween(-10000, 10000),
            'min_quantity' => fake()->numberBetween(-10000, 10000),
            'location_id' => Location::factory(),
            'category_id' => Category::factory(),
            'image' => fake()->word(),
        ];
    }
}

```

---

### 📄 File: `./database/factories/LoanFactory.php`

```php
<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LoanFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'loan_number' => fake()->word(),
            'user_id' => User::factory(),
            'item_id' => Item::factory(),
            'loan_date' => fake()->dateTime(),
            'due_date' => fake()->dateTime(),
            'return_date' => fake()->dateTime(),
            'returned_condition' => fake()->word(),
            'status' => fake()->word(),
            'loan_notes' => fake()->text(),
            'return_notes' => fake()->text(),
            'created_by' => User::factory()->create()->created_by,
            'returned_by' => User::factory()->create()->returned_by,
            'approved_by' => User::factory()->create()->approved_by,
            'penalty_amount' => fake()->randomFloat(2, 0, 9999999999999.99),
            'is_paid' => fake()->boolean(),
        ];
    }
}

```

---

### 📄 File: `./database/factories/LocationFactory.php`

```php
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class LocationFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'code' => fake()->word(),
            'description' => fake()->text(),
            'is_active' => fake()->boolean(),
        ];
    }
}

```

---

### 📄 File: `./database/factories/MaintenanceHistoryFactory.php`

```php
<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MaintenanceHistoryFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'item_id' => Item::factory(),
            'maintenance_type' => fake()->word(),
            'description' => fake()->text(),
            'cost' => fake()->randomFloat(2, 0, 9999999999999.99),
            'performed_by' => fake()->word(),
            'performed_at' => fake()->dateTime(),
            'completed_at' => fake()->dateTime(),
            'status' => fake()->word(),
            'notes' => fake()->text(),
            'created_by' => User::factory()->create()->created_by,
        ];
    }
}

```

---

### 📄 File: `./database/factories/RfidCardFactory.php`

```php
<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class RfidCardFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'uid' => fake()->word(),
            'card_number' => fake()->word(),
            'is_active' => fake()->boolean(),
            'issued_at' => fake()->dateTime(),
            'expired_at' => fake()->dateTime(),
            'last_used_at' => fake()->dateTime(),
            'last_used_for' => fake()->word(),
            'failed_attempts' => fake()->numberBetween(-10000, 10000),
            'user_id' => User::factory(),
        ];
    }
}

```

---

### 📄 File: `./database/factories/RfidLogFactory.php`

```php
<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\Loan;
use App\Models\RfidCard;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class RfidLogFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'rfid_card_id' => RfidCard::factory(),
            'user_id' => User::factory(),
            'action' => fake()->word(),
            'status' => fake()->word(),
            'item_id' => Item::factory(),
            'loan_id' => Loan::factory(),
            'location' => fake()->word(),
            'ip_address' => fake()->word(),
            'user_agent' => fake()->text(),
            'response_message' => fake()->text(),
        ];
    }
}

```

---

### 📄 File: `./database/factories/SettingFactory.php`

```php
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SettingFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'key' => fake()->word(),
            'value' => fake()->text(),
            'type' => fake()->word(),
            'group' => fake()->word(),
            'description' => fake()->text(),
            'is_public' => fake()->boolean(),
        ];
    }
}

```

---

### 📄 File: `./database/factories/StockMovementFactory.php`

```php
<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\Location;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class StockMovementFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'item_id' => Item::factory(),
            'movement_type' => fake()->word(),
            'quantity' => fake()->numberBetween(-10000, 10000),
            'from_location_id' => Location::factory(),
            'to_location_id' => Location::factory(),
            'reference_number' => fake()->word(),
            'reason' => fake()->text(),
            'performed_by' => User::factory()->create()->performed_by,
            'performed_at' => fake()->dateTime(),
        ];
    }
}

```

---

### 📄 File: `./database/factories/UserFactory.php`

```php
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}

```

---

### 📄 File: `./database/migrations/0001_01_01_000000_create_users_table.php`

```php
<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};

```

---

### 📄 File: `./database/migrations/0001_01_01_000001_create_cache_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });

        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cache');
        Schema::dropIfExists('cache_locks');
    }
};

```

---

### 📄 File: `./database/migrations/0001_01_01_000002_create_jobs_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });

        Schema::create('job_batches', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->integer('total_jobs');
            $table->integer('pending_jobs');
            $table->integer('failed_jobs');
            $table->longText('failed_job_ids');
            $table->mediumText('options')->nullable();
            $table->integer('cancelled_at')->nullable();
            $table->integer('created_at');
            $table->integer('finished_at')->nullable();
        });

        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('failed_jobs');
    }
};

```

---

### 📄 File: `./database/migrations/2026_01_04_190900_create_locations_table.php`

```php
<?php
// database/migrations/2024_01_01_000001_create_locations_table.php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();

            $table->index('code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};

```

---

### 📄 File: `./database/migrations/2026_01_04_190901_create_categories_table.php`

```php
<?php
// database/migrations/2024_01_01_000002_create_categories_table.php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->boolean('is_consumable')->default(false);
            $table->integer('min_stock')->nullable();
            $table->text('description')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index('slug');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};

```

---

### 📄 File: `./database/migrations/2026_01_04_190902_create_items_table.php`

```php
<?php
// database/migrations/2024_01_01_000003_create_items_table.php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('item_code')->unique();
            $table->string('serial_number')->nullable();
            $table->text('description')->nullable();
            $table->date('purchase_date')->nullable();
            $table->decimal('price', 15, 2)->nullable();
            $table->date('warranty_expired_at')->nullable();
            $table->string('condition')->default('good');
            $table->string('status')->default('available');
            $table->integer('quantity')->default(1);
            $table->integer('min_quantity')->nullable();
            $table->foreignId('location_id')->constrained('locations')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->string('image')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index('name');
            $table->index('item_code');
            $table->index('serial_number');
            $table->index('condition');
            $table->index('status');
            $table->index(['status', 'location_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};

```

---

### 📄 File: `./database/migrations/2026_01_04_190903_create_rfid_cards_table.php`

```php
<?php
// database/migrations/2024_01_01_000005_create_rfid_cards_table.php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rfid_cards', function (Blueprint $table) {
            $table->id();
            $table->string('uid')->unique();
            $table->string('card_number')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('issued_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->string('last_used_for')->nullable();
            $table->integer('failed_attempts')->default(0);
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->softDeletes();
            $table->timestamps();

            $table->index('uid');
            $table->index('card_number');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rfid_cards');
    }
};

```

---

### 📄 File: `./database/migrations/2026_01_04_190904_create_loans_table.php`

```php
<?php
// database/migrations/2024_01_01_000006_create_loans_table.php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->string('loan_number')->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade');
            $table->timestamp('loan_date')->useCurrent();
            $table->timestamp('due_date');
            $table->timestamp('return_date')->nullable();
            $table->string('returned_condition')->nullable();
            $table->string('status')->default('active');
            $table->text('loan_notes')->nullable();
            $table->text('return_notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('returned_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->decimal('penalty_amount', 15, 2)->default(0);
            $table->boolean('is_paid')->default(false);
            $table->softDeletes();
            $table->timestamps();

            $table->index('loan_number');
            $table->index('user_id');
            $table->index('item_id');
            $table->index('due_date');
            $table->index('status');
            $table->index(['status', 'due_date']);
            $table->index(['user_id', 'status']);
            $table->index(['item_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};

```

---

### 📄 File: `./database/migrations/2026_01_04_190905_create_rfid_logs_table.php`

```php
<?php
// database/migrations/2024_01_01_000007_create_rfid_logs_table.php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rfid_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rfid_card_id')->constrained('rfid_cards')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('action');
            $table->string('status');
            $table->foreignId('item_id')->nullable()->constrained('items')->onDelete('set null');
            $table->foreignId('loan_id')->nullable()->constrained('loans')->onDelete('set null');
            $table->string('location')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->text('response_message')->nullable();
            $table->timestamps();

            $table->index('rfid_card_id');
            $table->index('user_id');
            $table->index('action');
            $table->index('status');
            $table->index(['rfid_card_id', 'created_at']);
            $table->index(['user_id', 'action', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rfid_logs');
    }
};

```

---

### 📄 File: `./database/migrations/2026_01_04_190906_create_maintenance_histories_table.php`

```php
<?php
// database/migrations/2024_01_01_000008_create_maintenance_histories_table.php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade');
            $table->string('maintenance_type');
            $table->text('description');
            $table->decimal('cost', 15, 2)->nullable();
            $table->string('performed_by')->nullable();
            $table->timestamp('performed_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            $table->string('status')->default('pending');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();

            $table->index('item_id');
            $table->index('maintenance_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_histories');
    }
};

```

---

### 📄 File: `./database/migrations/2026_01_04_190907_create_stock_movements_table.php`

```php
<?php
// database/migrations/2024_01_01_000009_create_stock_movements_table.php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade');
            $table->string('movement_type');
            $table->integer('quantity');
            $table->foreignId('from_location_id')->nullable()->constrained('locations')->onDelete('set null');
            $table->foreignId('to_location_id')->nullable()->constrained('locations')->onDelete('set null');
            $table->string('reference_number')->nullable();
            $table->text('reason')->nullable();
            $table->foreignId('performed_by')->constrained('users')->onDelete('cascade');
            $table->timestamp('performed_at')->useCurrent();
            $table->timestamps();

            $table->index('item_id');
            $table->index('movement_type');
            $table->index('reference_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};

```

---

### 📄 File: `./database/migrations/2026_01_04_190908_create_settings_table.php`

```php
<?php
// database/migrations/2024_01_01_000010_create_settings_table.php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string');
            $table->string('group')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_public')->default(false);
            $table->timestamps();

            $table->index('key');
            $table->index('group');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};

```

---

### 📄 File: `./database/migrations/2026_01_04_192219_add_inventory_fields_to_users_table.php`

```php
<?php
// database/migrations/2024_01_01_000004_add_inventory_fields_to_users_table.php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('identity_number')->unique()->nullable()->after('email');
            $table->string('phone')->nullable()->after('identity_number');
            $table->string('role')->default('student')->after('phone');
            $table->string('department')->nullable()->after('role');
            $table->string('class')->nullable()->after('department');
            $table->boolean('is_suspended')->default(false)->after('class');
            $table->timestamp('suspended_until')->nullable()->after('is_suspended');
            $table->text('suspension_reason')->nullable()->after('suspended_until');
            $table->integer('max_loan_items')->default(3)->after('suspension_reason');
            $table->integer('loan_duration_days')->default(7)->after('max_loan_items');
            $table->softDeletes();

            $table->index('identity_number');
            $table->index('role');
            $table->index('department');
            $table->index('is_suspended');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['identity_number']);
            $table->dropIndex(['role']);
            $table->dropIndex(['department']);
            $table->dropIndex(['is_suspended']);

            $table->dropColumn([
                'identity_number',
                'phone',
                'role',
                'department',
                'class',
                'is_suspended',
                'suspended_until',
                'suspension_reason',
                'max_loan_items',
                'loan_duration_days',
            ]);
            $table->dropSoftDeletes();
        });
    }
};

```

---

### 📄 File: `./database/seeders/DatabaseSeeder.php`

```php
<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}

```

---

### 📄 File: `./database/seeders/SettingSeeder.php`

```php
<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            [
                'key' => 'loan_duration_days',
                'value' => '7',
                'type' => 'integer',
                'group' => 'loan',
                'description' => 'Durasi peminjaman default (hari)',
                'is_public' => true,
            ],
            [
                'key' => 'penalty_per_day',
                'value' => '5000',
                'type' => 'integer',
                'group' => 'penalty',
                'description' => 'Denda per hari keterlambatan (Rupiah)',
                'is_public' => true,
            ],
            [
                'key' => 'max_loan_items_student',
                'value' => '3',
                'type' => 'integer',
                'group' => 'loan',
                'description' => 'Maksimal item yang bisa dipinjam siswa',
                'is_public' => true,
            ],
            [
                'key' => 'max_loan_items_teacher',
                'value' => '5',
                'type' => 'integer',
                'group' => 'loan',
                'description' => 'Maksimal item yang bisa dipinjam guru',
                'is_public' => true,
            ],
            [
                'key' => 'overdue_notification_days',
                'value' => '1',
                'type' => 'integer',
                'group' => 'notification',
                'description' => 'Kirim notifikasi H-x sebelum jatuh tempo',
                'is_public' => false,
            ],
            [
                'key' => 'auto_suspend_after_days',
                'value' => '7',
                'type' => 'integer',
                'group' => 'loan',
                'description' => 'Auto suspend user setelah x hari terlambat',
                'is_public' => false,
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}

```

---

## 📁 Directory: resources (Frontend Resources)

Views, CSS, JavaScript, and frontend assets.

### 📄 File: `./resources/css/app.css`

```css
@import "tailwindcss";

@source '../../vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php';
@source '../../storage/framework/views/*.php';
@source '../**/*.blade.php';
@source '../**/*.js';

@theme {
    --font-sans: "Instrument Sans", ui-sans-serif, system-ui, sans-serif,
        "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol",
        "Noto Color Emoji";
}
```

---

### 📄 File: `./resources/js/app.js`

```javascript
import "./bootstrap";
```

---

### 📄 File: `./resources/js/bootstrap.js`

```javascript
import axios from "axios";
window.axios = axios;

window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
```

---

### 📄 File: `./resources/views/welcome.blade.php`

```blade
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <style>
                /*! tailwindcss v4.0.7 | MIT License | https://tailwindcss.com */@layer theme{:root,:host{--font-sans:'Instrument Sans',ui-sans-serif,system-ui,sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol","Noto Color Emoji";--font-serif:ui-serif,Georgia,Cambria,"Times New Roman",Times,serif;--font-mono:ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,"Liberation Mono","Courier New",monospace;--color-red-50:oklch(.971 .013 17.38);--color-red-100:oklch(.936 .032 17.717);--color-red-200:oklch(.885 .062 18.334);--color-red-300:oklch(.808 .114 19.571);--color-red-400:oklch(.704 .191 22.216);--color-red-500:oklch(.637 .237 25.331);--color-red-600:oklch(.577 .245 27.325);--color-red-700:oklch(.505 .213 27.518);--color-red-800:oklch(.444 .177 26.899);--color-red-900:oklch(.396 .141 25.723);--color-red-950:oklch(.258 .092 26.042);--color-orange-50:oklch(.98 .016 73.684);--color-orange-100:oklch(.954 .038 75.164);--color-orange-200:oklch(.901 .076 70.697);--color-orange-300:oklch(.837 .128 66.29);--color-orange-400:oklch(.75 .183 55.934);--color-orange-500:oklch(.705 .213 47.604);--color-orange-600:oklch(.646 .222 41.116);--color-orange-700:oklch(.553 .195 38.402);--color-orange-800:oklch(.47 .157 37.304);--color-orange-900:oklch(.408 .123 38.172);--color-orange-950:oklch(.266 .079 36.259);--color-amber-50:oklch(.987 .022 95.277);--color-amber-100:oklch(.962 .059 95.617);--color-amber-200:oklch(.924 .12 95.746);--color-amber-300:oklch(.879 .169 91.605);--color-amber-400:oklch(.828 .189 84.429);--color-amber-500:oklch(.769 .188 70.08);--color-amber-600:oklch(.666 .179 58.318);--color-amber-700:oklch(.555 .163 48.998);--color-amber-800:oklch(.473 .137 46.201);--color-amber-900:oklch(.414 .112 45.904);--color-amber-950:oklch(.279 .077 45.635);--color-yellow-50:oklch(.987 .026 102.212);--color-yellow-100:oklch(.973 .071 103.193);--color-yellow-200:oklch(.945 .129 101.54);--color-yellow-300:oklch(.905 .182 98.111);--color-yellow-400:oklch(.852 .199 91.936);--color-yellow-500:oklch(.795 .184 86.047);--color-yellow-600:oklch(.681 .162 75.834);--color-yellow-700:oklch(.554 .135 66.442);--color-yellow-800:oklch(.476 .114 61.907);--color-yellow-900:oklch(.421 .095 57.708);--color-yellow-950:oklch(.286 .066 53.813);--color-lime-50:oklch(.986 .031 120.757);--color-lime-100:oklch(.967 .067 122.328);--color-lime-200:oklch(.938 .127 124.321);--color-lime-300:oklch(.897 .196 126.665);--color-lime-400:oklch(.841 .238 128.85);--color-lime-500:oklch(.768 .233 130.85);--color-lime-600:oklch(.648 .2 131.684);--color-lime-700:oklch(.532 .157 131.589);--color-lime-800:oklch(.453 .124 130.933);--color-lime-900:oklch(.405 .101 131.063);--color-lime-950:oklch(.274 .072 132.109);--color-green-50:oklch(.982 .018 155.826);--color-green-100:oklch(.962 .044 156.743);--color-green-200:oklch(.925 .084 155.995);--color-green-300:oklch(.871 .15 154.449);--color-green-400:oklch(.792 .209 151.711);--color-green-500:oklch(.723 .219 149.579);--color-green-600:oklch(.627 .194 149.214);--color-green-700:oklch(.527 .154 150.069);--color-green-800:oklch(.448 .119 151.328);--color-green-900:oklch(.393 .095 152.535);--color-green-950:oklch(.266 .065 152.934);--color-emerald-50:oklch(.979 .021 166.113);--color-emerald-100:oklch(.95 .052 163.051);--color-emerald-200:oklch(.905 .093 164.15);--color-emerald-300:oklch(.845 .143 164.978);--color-emerald-400:oklch(.765 .177 163.223);--color-emerald-500:oklch(.696 .17 162.48);--color-emerald-600:oklch(.596 .145 163.225);--color-emerald-700:oklch(.508 .118 165.612);--color-emerald-800:oklch(.432 .095 166.913);--color-emerald-900:oklch(.378 .077 168.94);--color-emerald-950:oklch(.262 .051 172.552);--color-teal-50:oklch(.984 .014 180.72);--color-teal-100:oklch(.953 .051 180.801);--color-teal-200:oklch(.91 .096 180.426);--color-teal-300:oklch(.855 .138 181.071);--color-teal-400:oklch(.777 .152 181.912);--color-teal-500:oklch(.704 .14 182.503);--color-teal-600:oklch(.6 .118 184.704);--color-teal-700:oklch(.511 .096 186.391);--color-teal-800:oklch(.437 .078 188.216);--color-teal-900:oklch(.386 .063 188.416);--color-teal-950:oklch(.277 .046 192.524);--color-cyan-50:oklch(.984 .019 200.873);--color-cyan-100:oklch(.956 .045 203.388);--color-cyan-200:oklch(.917 .08 205.041);--color-cyan-300:oklch(.865 .127 207.078);--color-cyan-400:oklch(.789 .154 211.53);--color-cyan-500:oklch(.715 .143 215.221);--color-cyan-600:oklch(.609 .126 221.723);--color-cyan-700:oklch(.52 .105 223.128);--color-cyan-800:oklch(.45 .085 224.283);--color-cyan-900:oklch(.398 .07 227.392);--color-cyan-950:oklch(.302 .056 229.695);--color-sky-50:oklch(.977 .013 236.62);--color-sky-100:oklch(.951 .026 236.824);--color-sky-200:oklch(.901 .058 230.902);--color-sky-300:oklch(.828 .111 230.318);--color-sky-400:oklch(.746 .16 232.661);--color-sky-500:oklch(.685 .169 237.323);--color-sky-600:oklch(.588 .158 241.966);--color-sky-700:oklch(.5 .134 242.749);--color-sky-800:oklch(.443 .11 240.79);--color-sky-900:oklch(.391 .09 240.876);--color-sky-950:oklch(.293 .066 243.157);--color-blue-50:oklch(.97 .014 254.604);--color-blue-100:oklch(.932 .032 255.585);--color-blue-200:oklch(.882 .059 254.128);--color-blue-300:oklch(.809 .105 251.813);--color-blue-400:oklch(.707 .165 254.624);--color-blue-500:oklch(.623 .214 259.815);--color-blue-600:oklch(.546 .245 262.881);--color-blue-700:oklch(.488 .243 264.376);--color-blue-800:oklch(.424 .199 265.638);--color-blue-900:oklch(.379 .146 265.522);--color-blue-950:oklch(.282 .091 267.935);--color-indigo-50:oklch(.962 .018 272.314);--color-indigo-100:oklch(.93 .034 272.788);--color-indigo-200:oklch(.87 .065 274.039);--color-indigo-300:oklch(.785 .115 274.713);--color-indigo-400:oklch(.673 .182 276.935);--color-indigo-500:oklch(.585 .233 277.117);--color-indigo-600:oklch(.511 .262 276.966);--color-indigo-700:oklch(.457 .24 277.023);--color-indigo-800:oklch(.398 .195 277.366);--color-indigo-900:oklch(.359 .144 278.697);--color-indigo-950:oklch(.257 .09 281.288);--color-violet-50:oklch(.969 .016 293.756);--color-violet-100:oklch(.943 .029 294.588);--color-violet-200:oklch(.894 .057 293.283);--color-violet-300:oklch(.811 .111 293.571);--color-violet-400:oklch(.702 .183 293.541);--color-violet-500:oklch(.606 .25 292.717);--color-violet-600:oklch(.541 .281 293.009);--color-violet-700:oklch(.491 .27 292.581);--color-violet-800:oklch(.432 .232 292.759);--color-violet-900:oklch(.38 .189 293.745);--color-violet-950:oklch(.283 .141 291.089);--color-purple-50:oklch(.977 .014 308.299);--color-purple-100:oklch(.946 .033 307.174);--color-purple-200:oklch(.902 .063 306.703);--color-purple-300:oklch(.827 .119 306.383);--color-purple-400:oklch(.714 .203 305.504);--color-purple-500:oklch(.627 .265 303.9);--color-purple-600:oklch(.558 .288 302.321);--color-purple-700:oklch(.496 .265 301.924);--color-purple-800:oklch(.438 .218 303.724);--color-purple-900:oklch(.381 .176 304.987);--color-purple-950:oklch(.291 .149 302.717);--color-fuchsia-50:oklch(.977 .017 320.058);--color-fuchsia-100:oklch(.952 .037 318.852);--color-fuchsia-200:oklch(.903 .076 319.62);--color-fuchsia-300:oklch(.833 .145 321.434);--color-fuchsia-400:oklch(.74 .238 322.16);--color-fuchsia-500:oklch(.667 .295 322.15);--color-fuchsia-600:oklch(.591 .293 322.896);--color-fuchsia-700:oklch(.518 .253 323.949);--color-fuchsia-800:oklch(.452 .211 324.591);--color-fuchsia-900:oklch(.401 .17 325.612);--color-fuchsia-950:oklch(.293 .136 325.661);--color-pink-50:oklch(.971 .014 343.198);--color-pink-100:oklch(.948 .028 342.258);--color-pink-200:oklch(.899 .061 343.231);--color-pink-300:oklch(.823 .12 346.018);--color-pink-400:oklch(.718 .202 349.761);--color-pink-500:oklch(.656 .241 354.308);--color-pink-600:oklch(.592 .249 .584);--color-pink-700:oklch(.525 .223 3.958);--color-pink-800:oklch(.459 .187 3.815);--color-pink-900:oklch(.408 .153 2.432);--color-pink-950:oklch(.284 .109 3.907);--color-rose-50:oklch(.969 .015 12.422);--color-rose-100:oklch(.941 .03 12.58);--color-rose-200:oklch(.892 .058 10.001);--color-rose-300:oklch(.81 .117 11.638);--color-rose-400:oklch(.712 .194 13.428);--color-rose-500:oklch(.645 .246 16.439);--color-rose-600:oklch(.586 .253 17.585);--color-rose-700:oklch(.514 .222 16.935);--color-rose-800:oklch(.455 .188 13.697);--color-rose-900:oklch(.41 .159 10.272);--color-rose-950:oklch(.271 .105 12.094);--color-slate-50:oklch(.984 .003 247.858);--color-slate-100:oklch(.968 .007 247.896);--color-slate-200:oklch(.929 .013 255.508);--color-slate-300:oklch(.869 .022 252.894);--color-slate-400:oklch(.704 .04 256.788);--color-slate-500:oklch(.554 .046 257.417);--color-slate-600:oklch(.446 .043 257.281);--color-slate-700:oklch(.372 .044 257.287);--color-slate-800:oklch(.279 .041 260.031);--color-slate-900:oklch(.208 .042 265.755);--color-slate-950:oklch(.129 .042 264.695);--color-gray-50:oklch(.985 .002 247.839);--color-gray-100:oklch(.967 .003 264.542);--color-gray-200:oklch(.928 .006 264.531);--color-gray-300:oklch(.872 .01 258.338);--color-gray-400:oklch(.707 .022 261.325);--color-gray-500:oklch(.551 .027 264.364);--color-gray-600:oklch(.446 .03 256.802);--color-gray-700:oklch(.373 .034 259.733);--color-gray-800:oklch(.278 .033 256.848);--color-gray-900:oklch(.21 .034 264.665);--color-gray-950:oklch(.13 .028 261.692);--color-zinc-50:oklch(.985 0 0);--color-zinc-100:oklch(.967 .001 286.375);--color-zinc-200:oklch(.92 .004 286.32);--color-zinc-300:oklch(.871 .006 286.286);--color-zinc-400:oklch(.705 .015 286.067);--color-zinc-500:oklch(.552 .016 285.938);--color-zinc-600:oklch(.442 .017 285.786);--color-zinc-700:oklch(.37 .013 285.805);--color-zinc-800:oklch(.274 .006 286.033);--color-zinc-900:oklch(.21 .006 285.885);--color-zinc-950:oklch(.141 .005 285.823);--color-neutral-50:oklch(.985 0 0);--color-neutral-100:oklch(.97 0 0);--color-neutral-200:oklch(.922 0 0);--color-neutral-300:oklch(.87 0 0);--color-neutral-400:oklch(.708 0 0);--color-neutral-500:oklch(.556 0 0);--color-neutral-600:oklch(.439 0 0);--color-neutral-700:oklch(.371 0 0);--color-neutral-800:oklch(.269 0 0);--color-neutral-900:oklch(.205 0 0);--color-neutral-950:oklch(.145 0 0);--color-stone-50:oklch(.985 .001 106.423);--color-stone-100:oklch(.97 .001 106.424);--color-stone-200:oklch(.923 .003 48.717);--color-stone-300:oklch(.869 .005 56.366);--color-stone-400:oklch(.709 .01 56.259);--color-stone-500:oklch(.553 .013 58.071);--color-stone-600:oklch(.444 .011 73.639);--color-stone-700:oklch(.374 .01 67.558);--color-stone-800:oklch(.268 .007 34.298);--color-stone-900:oklch(.216 .006 56.043);--color-stone-950:oklch(.147 .004 49.25);--color-black:#000;--color-white:#fff;--spacing:.25rem;--breakpoint-sm:40rem;--breakpoint-md:48rem;--breakpoint-lg:64rem;--breakpoint-xl:80rem;--breakpoint-2xl:96rem;--container-3xs:16rem;--container-2xs:18rem;--container-xs:20rem;--container-sm:24rem;--container-md:28rem;--container-lg:32rem;--container-xl:36rem;--container-2xl:42rem;--container-3xl:48rem;--container-4xl:56rem;--container-5xl:64rem;--container-6xl:72rem;--container-7xl:80rem;--text-xs:.75rem;--text-xs--line-height:calc(1/.75);--text-sm:.875rem;--text-sm--line-height:calc(1.25/.875);--text-base:1rem;--text-base--line-height: 1.5 ;--text-lg:1.125rem;--text-lg--line-height:calc(1.75/1.125);--text-xl:1.25rem;--text-xl--line-height:calc(1.75/1.25);--text-2xl:1.5rem;--text-2xl--line-height:calc(2/1.5);--text-3xl:1.875rem;--text-3xl--line-height: 1.2 ;--text-4xl:2.25rem;--text-4xl--line-height:calc(2.5/2.25);--text-5xl:3rem;--text-5xl--line-height:1;--text-6xl:3.75rem;--text-6xl--line-height:1;--text-7xl:4.5rem;--text-7xl--line-height:1;--text-8xl:6rem;--text-8xl--line-height:1;--text-9xl:8rem;--text-9xl--line-height:1;--font-weight-thin:100;--font-weight-extralight:200;--font-weight-light:300;--font-weight-normal:400;--font-weight-medium:500;--font-weight-semibold:600;--font-weight-bold:700;--font-weight-extrabold:800;--font-weight-black:900;--tracking-tighter:-.05em;--tracking-tight:-.025em;--tracking-normal:0em;--tracking-wide:.025em;--tracking-wider:.05em;--tracking-widest:.1em;--leading-tight:1.25;--leading-snug:1.375;--leading-normal:1.5;--leading-relaxed:1.625;--leading-loose:2;--radius-xs:.125rem;--radius-sm:.25rem;--radius-md:.375rem;--radius-lg:.5rem;--radius-xl:.75rem;--radius-2xl:1rem;--radius-3xl:1.5rem;--radius-4xl:2rem;--shadow-2xs:0 1px #0000000d;--shadow-xs:0 1px 2px 0 #0000000d;--shadow-sm:0 1px 3px 0 #0000001a,0 1px 2px -1px #0000001a;--shadow-md:0 4px 6px -1px #0000001a,0 2px 4px -2px #0000001a;--shadow-lg:0 10px 15px -3px #0000001a,0 4px 6px -4px #0000001a;--shadow-xl:0 20px 25px -5px #0000001a,0 8px 10px -6px #0000001a;--shadow-2xl:0 25px 50px -12px #00000040;--inset-shadow-2xs:inset 0 1px #0000000d;--inset-shadow-xs:inset 0 1px 1px #0000000d;--inset-shadow-sm:inset 0 2px 4px #0000000d;--drop-shadow-xs:0 1px 1px #0000000d;--drop-shadow-sm:0 1px 2px #00000026;--drop-shadow-md:0 3px 3px #0000001f;--drop-shadow-lg:0 4px 4px #00000026;--drop-shadow-xl:0 9px 7px #0000001a;--drop-shadow-2xl:0 25px 25px #00000026;--ease-in:cubic-bezier(.4,0,1,1);--ease-out:cubic-bezier(0,0,.2,1);--ease-in-out:cubic-bezier(.4,0,.2,1);--animate-spin:spin 1s linear infinite;--animate-ping:ping 1s cubic-bezier(0,0,.2,1)infinite;--animate-pulse:pulse 2s cubic-bezier(.4,0,.6,1)infinite;--animate-bounce:bounce 1s infinite;--blur-xs:4px;--blur-sm:8px;--blur-md:12px;--blur-lg:16px;--blur-xl:24px;--blur-2xl:40px;--blur-3xl:64px;--perspective-dramatic:100px;--perspective-near:300px;--perspective-normal:500px;--perspective-midrange:800px;--perspective-distant:1200px;--aspect-video:16/9;--default-transition-duration:.15s;--default-transition-timing-function:cubic-bezier(.4,0,.2,1);--default-font-family:var(--font-sans);--default-font-feature-settings:var(--font-sans--font-feature-settings);--default-font-variation-settings:var(--font-sans--font-variation-settings);--default-mono-font-family:var(--font-mono);--default-mono-font-feature-settings:var(--font-mono--font-feature-settings);--default-mono-font-variation-settings:var(--font-mono--font-variation-settings)}}@layer base{*,:after,:before,::backdrop{box-sizing:border-box;border:0 solid;margin:0;padding:0}::file-selector-button{box-sizing:border-box;border:0 solid;margin:0;padding:0}html,:host{-webkit-text-size-adjust:100%;-moz-tab-size:4;tab-size:4;line-height:1.5;font-family:var(--default-font-family,ui-sans-serif,system-ui,sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol","Noto Color Emoji");font-feature-settings:var(--default-font-feature-settings,normal);font-variation-settings:var(--default-font-variation-settings,normal);-webkit-tap-highlight-color:transparent}body{line-height:inherit}hr{height:0;color:inherit;border-top-width:1px}abbr:where([title]){-webkit-text-decoration:underline dotted;text-decoration:underline dotted}h1,h2,h3,h4,h5,h6{font-size:inherit;font-weight:inherit}a{color:inherit;-webkit-text-decoration:inherit;text-decoration:inherit}b,strong{font-weight:bolder}code,kbd,samp,pre{font-family:var(--default-mono-font-family,ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,"Liberation Mono","Courier New",monospace);font-feature-settings:var(--default-mono-font-feature-settings,normal);font-variation-settings:var(--default-mono-font-variation-settings,normal);font-size:1em}small{font-size:80%}sub,sup{vertical-align:baseline;font-size:75%;line-height:0;position:relative}sub{bottom:-.25em}sup{top:-.5em}table{text-indent:0;border-color:inherit;border-collapse:collapse}:-moz-focusring{outline:auto}progress{vertical-align:baseline}summary{display:list-item}ol,ul,menu{list-style:none}img,svg,video,canvas,audio,iframe,embed,object{vertical-align:middle;display:block}img,video{max-width:100%;height:auto}button,input,select,optgroup,textarea{font:inherit;font-feature-settings:inherit;font-variation-settings:inherit;letter-spacing:inherit;color:inherit;opacity:1;background-color:#0000;border-radius:0}::file-selector-button{font:inherit;font-feature-settings:inherit;font-variation-settings:inherit;letter-spacing:inherit;color:inherit;opacity:1;background-color:#0000;border-radius:0}:where(select:is([multiple],[size])) optgroup{font-weight:bolder}:where(select:is([multiple],[size])) optgroup option{padding-inline-start:20px}::file-selector-button{margin-inline-end:4px}::placeholder{opacity:1;color:color-mix(in oklab,currentColor 50%,transparent)}textarea{resize:vertical}::-webkit-search-decoration{-webkit-appearance:none}::-webkit-date-and-time-value{min-height:1lh;text-align:inherit}::-webkit-datetime-edit{display:inline-flex}::-webkit-datetime-edit-fields-wrapper{padding:0}::-webkit-datetime-edit{padding-block:0}::-webkit-datetime-edit-year-field{padding-block:0}::-webkit-datetime-edit-month-field{padding-block:0}::-webkit-datetime-edit-day-field{padding-block:0}::-webkit-datetime-edit-hour-field{padding-block:0}::-webkit-datetime-edit-minute-field{padding-block:0}::-webkit-datetime-edit-second-field{padding-block:0}::-webkit-datetime-edit-millisecond-field{padding-block:0}::-webkit-datetime-edit-meridiem-field{padding-block:0}:-moz-ui-invalid{box-shadow:none}button,input:where([type=button],[type=reset],[type=submit]){-webkit-appearance:button;-moz-appearance:button;appearance:button}::file-selector-button{-webkit-appearance:button;-moz-appearance:button;appearance:button}::-webkit-inner-spin-button{height:auto}::-webkit-outer-spin-button{height:auto}[hidden]:where(:not([hidden=until-found])){display:none!important}}@layer components;@layer utilities{.absolute{position:absolute}.relative{position:relative}.static{position:static}.inset-0{inset:calc(var(--spacing)*0)}.-mt-\[4\.9rem\]{margin-top:-4.9rem}.-mb-px{margin-bottom:-1px}.mb-1{margin-bottom:calc(var(--spacing)*1)}.mb-2{margin-bottom:calc(var(--spacing)*2)}.mb-4{margin-bottom:calc(var(--spacing)*4)}.mb-6{margin-bottom:calc(var(--spacing)*6)}.-ml-8{margin-left:calc(var(--spacing)*-8)}.flex{display:flex}.hidden{display:none}.inline-block{display:inline-block}.inline-flex{display:inline-flex}.table{display:table}.aspect-\[335\/376\]{aspect-ratio:335/376}.h-1{height:calc(var(--spacing)*1)}.h-1\.5{height:calc(var(--spacing)*1.5)}.h-2{height:calc(var(--spacing)*2)}.h-2\.5{height:calc(var(--spacing)*2.5)}.h-3{height:calc(var(--spacing)*3)}.h-3\.5{height:calc(var(--spacing)*3.5)}.h-14{height:calc(var(--spacing)*14)}.h-14\.5{height:calc(var(--spacing)*14.5)}.min-h-screen{min-height:100vh}.w-1{width:calc(var(--spacing)*1)}.w-1\.5{width:calc(var(--spacing)*1.5)}.w-2{width:calc(var(--spacing)*2)}.w-2\.5{width:calc(var(--spacing)*2.5)}.w-3{width:calc(var(--spacing)*3)}.w-3\.5{width:calc(var(--spacing)*3.5)}.w-\[448px\]{width:448px}.w-full{width:100%}.max-w-\[335px\]{max-width:335px}.max-w-none{max-width:none}.flex-1{flex:1}.shrink-0{flex-shrink:0}.translate-y-0{--tw-translate-y:calc(var(--spacing)*0);translate:var(--tw-translate-x)var(--tw-translate-y)}.transform{transform:var(--tw-rotate-x)var(--tw-rotate-y)var(--tw-rotate-z)var(--tw-skew-x)var(--tw-skew-y)}.flex-col{flex-direction:column}.flex-col-reverse{flex-direction:column-reverse}.items-center{align-items:center}.justify-center{justify-content:center}.justify-end{justify-content:flex-end}.gap-3{gap:calc(var(--spacing)*3)}.gap-4{gap:calc(var(--spacing)*4)}:where(.space-x-1>:not(:last-child)){--tw-space-x-reverse:0;margin-inline-start:calc(calc(var(--spacing)*1)*var(--tw-space-x-reverse));margin-inline-end:calc(calc(var(--spacing)*1)*calc(1 - var(--tw-space-x-reverse)))}.overflow-hidden{overflow:hidden}.rounded-full{border-radius:3.40282e38px}.rounded-sm{border-radius:var(--radius-sm)}.rounded-t-lg{border-top-left-radius:var(--radius-lg);border-top-right-radius:var(--radius-lg)}.rounded-br-lg{border-bottom-right-radius:var(--radius-lg)}.rounded-bl-lg{border-bottom-left-radius:var(--radius-lg)}.border{border-style:var(--tw-border-style);border-width:1px}.border-\[\#19140035\]{border-color:#19140035}.border-\[\#e3e3e0\]{border-color:#e3e3e0}.border-black{border-color:var(--color-black)}.border-transparent{border-color:#0000}.bg-\[\#1b1b18\]{background-color:#1b1b18}.bg-\[\#FDFDFC\]{background-color:#fdfdfc}.bg-\[\#dbdbd7\]{background-color:#dbdbd7}.bg-\[\#fff2f2\]{background-color:#fff2f2}.bg-white{background-color:var(--color-white)}.p-6{padding:calc(var(--spacing)*6)}.px-5{padding-inline:calc(var(--spacing)*5)}.py-1{padding-block:calc(var(--spacing)*1)}.py-1\.5{padding-block:calc(var(--spacing)*1.5)}.py-2{padding-block:calc(var(--spacing)*2)}.pb-12{padding-bottom:calc(var(--spacing)*12)}.text-sm{font-size:var(--text-sm);line-height:var(--tw-leading,var(--text-sm--line-height))}.text-\[13px\]{font-size:13px}.leading-\[20px\]{--tw-leading:20px;line-height:20px}.leading-normal{--tw-leading:var(--leading-normal);line-height:var(--leading-normal)}.font-medium{--tw-font-weight:var(--font-weight-medium);font-weight:var(--font-weight-medium)}.text-\[\#1b1b18\]{color:#1b1b18}.text-\[\#706f6c\]{color:#706f6c}.text-\[\#F53003\],.text-\[\#f53003\]{color:#f53003}.text-white{color:var(--color-white)}.underline{text-decoration-line:underline}.underline-offset-4{text-underline-offset:4px}.opacity-100{opacity:1}.shadow-\[0px_0px_1px_0px_rgba\(0\,0\,0\,0\.03\)\,0px_1px_2px_0px_rgba\(0\,0\,0\,0\.06\)\]{--tw-shadow:0px 0px 1px 0px var(--tw-shadow-color,#00000008),0px 1px 2px 0px var(--tw-shadow-color,#0000000f);box-shadow:var(--tw-inset-shadow),var(--tw-inset-ring-shadow),var(--tw-ring-offset-shadow),var(--tw-ring-shadow),var(--tw-shadow)}.shadow-\[inset_0px_0px_0px_1px_rgba\(26\,26\,0\,0\.16\)\]{--tw-shadow:inset 0px 0px 0px 1px var(--tw-shadow-color,#1a1a0029);box-shadow:var(--tw-inset-shadow),var(--tw-inset-ring-shadow),var(--tw-ring-offset-shadow),var(--tw-ring-shadow),var(--tw-shadow)}.\!filter{filter:var(--tw-blur,)var(--tw-brightness,)var(--tw-contrast,)var(--tw-grayscale,)var(--tw-hue-rotate,)var(--tw-invert,)var(--tw-saturate,)var(--tw-sepia,)var(--tw-drop-shadow,)!important}.filter{filter:var(--tw-blur,)var(--tw-brightness,)var(--tw-contrast,)var(--tw-grayscale,)var(--tw-hue-rotate,)var(--tw-invert,)var(--tw-saturate,)var(--tw-sepia,)var(--tw-drop-shadow,)}.transition-all{transition-property:all;transition-timing-function:var(--tw-ease,var(--default-transition-timing-function));transition-duration:var(--tw-duration,var(--default-transition-duration))}.transition-opacity{transition-property:opacity;transition-timing-function:var(--tw-ease,var(--default-transition-timing-function));transition-duration:var(--tw-duration,var(--default-transition-duration))}.delay-300{transition-delay:.3s}.duration-750{--tw-duration:.75s;transition-duration:.75s}.not-has-\[nav\]\:hidden:not(:has(:is(nav))){display:none}.before\:absolute:before{content:var(--tw-content);position:absolute}.before\:top-0:before{content:var(--tw-content);top:calc(var(--spacing)*0)}.before\:top-1\/2:before{content:var(--tw-content);top:50%}.before\:bottom-0:before{content:var(--tw-content);bottom:calc(var(--spacing)*0)}.before\:bottom-1\/2:before{content:var(--tw-content);bottom:50%}.before\:left-\[0\.4rem\]:before{content:var(--tw-content);left:.4rem}.before\:border-l:before{content:var(--tw-content);border-left-style:var(--tw-border-style);border-left-width:1px}.before\:border-\[\#e3e3e0\]:before{content:var(--tw-content);border-color:#e3e3e0}@media (hover:hover){.hover\:border-\[\#1915014a\]:hover{border-color:#1915014a}.hover\:border-\[\#19140035\]:hover{border-color:#19140035}.hover\:border-black:hover{border-color:var(--color-black)}.hover\:bg-black:hover{background-color:var(--color-black)}}@media (width>=64rem){.lg\:-mt-\[6\.6rem\]{margin-top:-6.6rem}.lg\:mb-0{margin-bottom:calc(var(--spacing)*0)}.lg\:mb-6{margin-bottom:calc(var(--spacing)*6)}.lg\:-ml-px{margin-left:-1px}.lg\:ml-0{margin-left:calc(var(--spacing)*0)}.lg\:block{display:block}.lg\:aspect-auto{aspect-ratio:auto}.lg\:w-\[438px\]{width:438px}.lg\:max-w-4xl{max-width:var(--container-4xl)}.lg\:grow{flex-grow:1}.lg\:flex-row{flex-direction:row}.lg\:justify-center{justify-content:center}.lg\:rounded-t-none{border-top-left-radius:0;border-top-right-radius:0}.lg\:rounded-tl-lg{border-top-left-radius:var(--radius-lg)}.lg\:rounded-r-lg{border-top-right-radius:var(--radius-lg);border-bottom-right-radius:var(--radius-lg)}.lg\:rounded-br-none{border-bottom-right-radius:0}.lg\:p-8{padding:calc(var(--spacing)*8)}.lg\:p-20{padding:calc(var(--spacing)*20)}}@media (prefers-color-scheme:dark){.dark\:block{display:block}.dark\:hidden{display:none}.dark\:border-\[\#3E3E3A\]{border-color:#3e3e3a}.dark\:border-\[\#eeeeec\]{border-color:#eeeeec}.dark\:bg-\[\#0a0a0a\]{background-color:#0a0a0a}.dark\:bg-\[\#1D0002\]{background-color:#1d0002}.dark\:bg-\[\#3E3E3A\]{background-color:#3e3e3a}.dark\:bg-\[\#161615\]{background-color:#161615}.dark\:bg-\[\#eeeeec\]{background-color:#eeeeec}.dark\:text-\[\#1C1C1A\]{color:#1c1c1a}.dark\:text-\[\#A1A09A\]{color:#a1a09a}.dark\:text-\[\#EDEDEC\]{color:#ededec}.dark\:text-\[\#F61500\]{color:#f61500}.dark\:text-\[\#FF4433\]{color:#f43}.dark\:shadow-\[inset_0px_0px_0px_1px_\#fffaed2d\]{--tw-shadow:inset 0px 0px 0px 1px var(--tw-shadow-color,#fffaed2d);box-shadow:var(--tw-inset-shadow),var(--tw-inset-ring-shadow),var(--tw-ring-offset-shadow),var(--tw-ring-shadow),var(--tw-shadow)}.dark\:before\:border-\[\#3E3E3A\]:before{content:var(--tw-content);border-color:#3e3e3a}@media (hover:hover){.dark\:hover\:border-\[\#3E3E3A\]:hover{border-color:#3e3e3a}.dark\:hover\:border-\[\#62605b\]:hover{border-color:#62605b}.dark\:hover\:border-white:hover{border-color:var(--color-white)}.dark\:hover\:bg-white:hover{background-color:var(--color-white)}}}@starting-style{.starting\:translate-y-4{--tw-translate-y:calc(var(--spacing)*4);translate:var(--tw-translate-x)var(--tw-translate-y)}}@starting-style{.starting\:translate-y-6{--tw-translate-y:calc(var(--spacing)*6);translate:var(--tw-translate-x)var(--tw-translate-y)}}@starting-style{.starting\:opacity-0{opacity:0}}}@keyframes spin{to{transform:rotate(360deg)}}@keyframes ping{75%,to{opacity:0;transform:scale(2)}}@keyframes pulse{50%{opacity:.5}}@keyframes bounce{0%,to{animation-timing-function:cubic-bezier(.8,0,1,1);transform:translateY(-25%)}50%{animation-timing-function:cubic-bezier(0,0,.2,1);transform:none}}@property --tw-translate-x{syntax:"*";inherits:false;initial-value:0}@property --tw-translate-y{syntax:"*";inherits:false;initial-value:0}@property --tw-translate-z{syntax:"*";inherits:false;initial-value:0}@property --tw-rotate-x{syntax:"*";inherits:false;initial-value:rotateX(0)}@property --tw-rotate-y{syntax:"*";inherits:false;initial-value:rotateY(0)}@property --tw-rotate-z{syntax:"*";inherits:false;initial-value:rotateZ(0)}@property --tw-skew-x{syntax:"*";inherits:false;initial-value:skewX(0)}@property --tw-skew-y{syntax:"*";inherits:false;initial-value:skewY(0)}@property --tw-space-x-reverse{syntax:"*";inherits:false;initial-value:0}@property --tw-border-style{syntax:"*";inherits:false;initial-value:solid}@property --tw-leading{syntax:"*";inherits:false}@property --tw-font-weight{syntax:"*";inherits:false}@property --tw-shadow{syntax:"*";inherits:false;initial-value:0 0 #0000}@property --tw-shadow-color{syntax:"*";inherits:false}@property --tw-inset-shadow{syntax:"*";inherits:false;initial-value:0 0 #0000}@property --tw-inset-shadow-color{syntax:"*";inherits:false}@property --tw-ring-color{syntax:"*";inherits:false}@property --tw-ring-shadow{syntax:"*";inherits:false;initial-value:0 0 #0000}@property --tw-inset-ring-color{syntax:"*";inherits:false}@property --tw-inset-ring-shadow{syntax:"*";inherits:false;initial-value:0 0 #0000}@property --tw-ring-inset{syntax:"*";inherits:false}@property --tw-ring-offset-width{syntax:"<length>";inherits:false;initial-value:0}@property --tw-ring-offset-color{syntax:"*";inherits:false;initial-value:#fff}@property --tw-ring-offset-shadow{syntax:"*";inherits:false;initial-value:0 0 #0000}@property --tw-blur{syntax:"*";inherits:false}@property --tw-brightness{syntax:"*";inherits:false}@property --tw-contrast{syntax:"*";inherits:false}@property --tw-grayscale{syntax:"*";inherits:false}@property --tw-hue-rotate{syntax:"*";inherits:false}@property --tw-invert{syntax:"*";inherits:false}@property --tw-opacity{syntax:"*";inherits:false}@property --tw-saturate{syntax:"*";inherits:false}@property --tw-sepia{syntax:"*";inherits:false}@property --tw-drop-shadow{syntax:"*";inherits:false}@property --tw-duration{syntax:"*";inherits:false}@property --tw-content{syntax:"*";inherits:false;initial-value:""}
            </style>
        @endif
    </head>
    <body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] flex p-6 lg:p-8 items-center lg:justify-center min-h-screen flex-col">
        <header class="w-full lg:max-w-4xl max-w-[335px] text-sm mb-6 not-has-[nav]:hidden">
            @if (Route::has('login'))
                <nav class="flex items-center justify-end gap-4">
                    @auth
                        <a
                            href="{{ url('/dashboard') }}"
                            class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal"
                        >
                            Dashboard
                        </a>
                    @else
                        <a
                            href="{{ route('login') }}"
                            class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] text-[#1b1b18] border border-transparent hover:border-[#19140035] dark:hover:border-[#3E3E3A] rounded-sm text-sm leading-normal"
                        >
                            Log in
                        </a>

                        @if (Route::has('register'))
                            <a
                                href="{{ route('register') }}"
                                class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal">
                                Register
                            </a>
                        @endif
                    @endauth
                </nav>
            @endif
        </header>
        <div class="flex items-center justify-center w-full transition-opacity opacity-100 duration-750 lg:grow starting:opacity-0">
            <main class="flex max-w-[335px] w-full flex-col-reverse lg:max-w-4xl lg:flex-row">
                <div class="text-[13px] leading-[20px] flex-1 p-6 pb-12 lg:p-20 bg-white dark:bg-[#161615] dark:text-[#EDEDEC] shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] rounded-bl-lg rounded-br-lg lg:rounded-tl-lg lg:rounded-br-none">
                    <h1 class="mb-1 font-medium">Let's get started</h1>
                    <p class="mb-2 text-[#706f6c] dark:text-[#A1A09A]">Laravel has an incredibly rich ecosystem. <br>We suggest starting with the following.</p>
                    <ul class="flex flex-col mb-4 lg:mb-6">
                        <li class="flex items-center gap-4 py-2 relative before:border-l before:border-[#e3e3e0] dark:before:border-[#3E3E3A] before:top-1/2 before:bottom-0 before:left-[0.4rem] before:absolute">
                            <span class="relative py-1 bg-white dark:bg-[#161615]">
                                <span class="flex items-center justify-center rounded-full bg-[#FDFDFC] dark:bg-[#161615] shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_1px_2px_0px_rgba(0,0,0,0.06)] w-3.5 h-3.5 border dark:border-[#3E3E3A] border-[#e3e3e0]">
                                    <span class="rounded-full bg-[#dbdbd7] dark:bg-[#3E3E3A] w-1.5 h-1.5"></span>
                                </span>
                            </span>
                            <span>
                                Read the
                                <a href="https://laravel.com/docs" target="_blank" class="inline-flex items-center space-x-1 font-medium underline underline-offset-4 text-[#f53003] dark:text-[#FF4433] ml-1">
                                    <span>Documentation</span>
                                    <svg
                                        width="10"
                                        height="11"
                                        viewBox="0 0 10 11"
                                        fill="none"
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="w-2.5 h-2.5"
                                    >
                                        <path
                                            d="M7.70833 6.95834V2.79167H3.54167M2.5 8L7.5 3.00001"
                                            stroke="currentColor"
                                            stroke-linecap="square"
                                        />
                                    </svg>
                                </a>
                            </span>
                        </li>
                        <li class="flex items-center gap-4 py-2 relative before:border-l before:border-[#e3e3e0] dark:before:border-[#3E3E3A] before:bottom-1/2 before:top-0 before:left-[0.4rem] before:absolute">
                            <span class="relative py-1 bg-white dark:bg-[#161615]">
                                <span class="flex items-center justify-center rounded-full bg-[#FDFDFC] dark:bg-[#161615] shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_1px_2px_0px_rgba(0,0,0,0.06)] w-3.5 h-3.5 border dark:border-[#3E3E3A] border-[#e3e3e0]">
                                    <span class="rounded-full bg-[#dbdbd7] dark:bg-[#3E3E3A] w-1.5 h-1.5"></span>
                                </span>
                            </span>
                            <span>
                                Watch video tutorials at
                                <a href="https://laracasts.com" target="_blank" class="inline-flex items-center space-x-1 font-medium underline underline-offset-4 text-[#f53003] dark:text-[#FF4433] ml-1">
                                    <span>Laracasts</span>
                                    <svg
                                        width="10"
                                        height="11"
                                        viewBox="0 0 10 11"
                                        fill="none"
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="w-2.5 h-2.5"
                                    >
                                        <path
                                            d="M7.70833 6.95834V2.79167H3.54167M2.5 8L7.5 3.00001"
                                            stroke="currentColor"
                                            stroke-linecap="square"
                                        />
                                    </svg>
                                </a>
                            </span>
                        </li>
                    </ul>
                    <ul class="flex gap-3 text-sm leading-normal">
                        <li>
                            <a href="https://cloud.laravel.com" target="_blank" class="inline-block dark:bg-[#eeeeec] dark:border-[#eeeeec] dark:text-[#1C1C1A] dark:hover:bg-white dark:hover:border-white hover:bg-black hover:border-black px-5 py-1.5 bg-[#1b1b18] rounded-sm border border-black text-white text-sm leading-normal">
                                Deploy now
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="bg-[#fff2f2] dark:bg-[#1D0002] relative lg:-ml-px -mb-px lg:mb-0 rounded-t-lg lg:rounded-t-none lg:rounded-r-lg aspect-[335/376] lg:aspect-auto w-full lg:w-[438px] shrink-0 overflow-hidden">
                    {{-- Laravel Logo --}}
                    <svg class="w-full text-[#F53003] dark:text-[#F61500] transition-all translate-y-0 opacity-100 max-w-none duration-750 starting:opacity-0 starting:translate-y-6" viewBox="0 0 438 104" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M17.2036 -3H0V102.197H49.5189V86.7187H17.2036V-3Z" fill="currentColor" />
                        <path d="M110.256 41.6337C108.061 38.1275 104.945 35.3731 100.905 33.3681C96.8667 31.3647 92.8016 30.3618 88.7131 30.3618C83.4247 30.3618 78.5885 31.3389 74.201 33.2923C69.8111 35.2456 66.0474 37.928 62.9059 41.3333C59.7643 44.7401 57.3198 48.6726 55.5754 53.1293C53.8287 57.589 52.9572 62.274 52.9572 67.1813C52.9572 72.1925 53.8287 76.8995 55.5754 81.3069C57.3191 85.7173 59.7636 89.6241 62.9059 93.0293C66.0474 96.4361 69.8119 99.1155 74.201 101.069C78.5885 103.022 83.4247 103.999 88.7131 103.999C92.8016 103.999 96.8667 102.997 100.905 100.994C104.945 98.9911 108.061 96.2359 110.256 92.7282V102.195H126.563V32.1642H110.256V41.6337ZM108.76 75.7472C107.762 78.4531 106.366 80.8078 104.572 82.8112C102.776 84.8161 100.606 86.4183 98.0637 87.6206C95.5202 88.823 92.7004 89.4238 89.6103 89.4238C86.5178 89.4238 83.7252 88.823 81.2324 87.6206C78.7388 86.4183 76.5949 84.8161 74.7998 82.8112C73.004 80.8078 71.6319 78.4531 70.6856 75.7472C69.7356 73.0421 69.2644 70.1868 69.2644 67.1821C69.2644 64.1758 69.7356 61.3205 70.6856 58.6154C71.6319 55.9102 73.004 53.5571 74.7998 51.5522C76.5949 49.5495 78.738 47.9451 81.2324 46.7427C83.7252 45.5404 86.5178 44.9396 89.6103 44.9396C92.7012 44.9396 95.5202 45.5404 98.0637 46.7427C100.606 47.9451 102.776 49.5487 104.572 51.5522C106.367 53.5571 107.762 55.9102 108.76 58.6154C109.756 61.3205 110.256 64.1758 110.256 67.1821C110.256 70.1868 109.756 73.0421 108.76 75.7472Z" fill="currentColor" />
                        <path d="M242.805 41.6337C240.611 38.1275 237.494 35.3731 233.455 33.3681C229.416 31.3647 225.351 30.3618 221.262 30.3618C215.974 30.3618 211.138 31.3389 206.75 33.2923C202.36 35.2456 198.597 37.928 195.455 41.3333C192.314 44.7401 189.869 48.6726 188.125 53.1293C186.378 57.589 185.507 62.274 185.507 67.1813C185.507 72.1925 186.378 76.8995 188.125 81.3069C189.868 85.7173 192.313 89.6241 195.455 93.0293C198.597 96.4361 202.361 99.1155 206.75 101.069C211.138 103.022 215.974 103.999 221.262 103.999C225.351 103.999 229.416 102.997 233.455 100.994C237.494 98.9911 240.611 96.2359 242.805 92.7282V102.195H259.112V32.1642H242.805V41.6337ZM241.31 75.7472C240.312 78.4531 238.916 80.8078 237.122 82.8112C235.326 84.8161 233.156 86.4183 230.614 87.6206C228.07 88.823 225.251 89.4238 222.16 89.4238C219.068 89.4238 216.275 88.823 213.782 87.6206C211.289 86.4183 209.145 84.8161 207.35 82.8112C205.554 80.8078 204.182 78.4531 203.236 75.7472C202.286 73.0421 201.814 70.1868 201.814 67.1821C201.814 64.1758 202.286 61.3205 203.236 58.6154C204.182 55.9102 205.554 53.5571 207.35 51.5522C209.145 49.5495 211.288 47.9451 213.782 46.7427C216.275 45.5404 219.068 44.9396 222.16 44.9396C225.251 44.9396 228.07 45.5404 230.614 46.7427C233.156 47.9451 235.326 49.5487 237.122 51.5522C238.917 53.5571 240.312 55.9102 241.31 58.6154C242.306 61.3205 242.806 64.1758 242.806 67.1821C242.805 70.1868 242.305 73.0421 241.31 75.7472Z" fill="currentColor" />
                        <path d="M438 -3H421.694V102.197H438V-3Z" fill="currentColor" />
                        <path d="M139.43 102.197H155.735V48.2834H183.712V32.1665H139.43V102.197Z" fill="currentColor" />
                        <path d="M324.49 32.1665L303.995 85.794L283.498 32.1665H266.983L293.748 102.197H314.242L341.006 32.1665H324.49Z" fill="currentColor" />
                        <path d="M376.571 30.3656C356.603 30.3656 340.797 46.8497 340.797 67.1828C340.797 89.6597 356.094 104 378.661 104C391.29 104 399.354 99.1488 409.206 88.5848L398.189 80.0226C398.183 80.031 389.874 90.9895 377.468 90.9895C363.048 90.9895 356.977 79.3111 356.977 73.269H411.075C413.917 50.1328 398.775 30.3656 376.571 30.3656ZM357.02 61.0967C357.145 59.7487 359.023 43.3761 376.442 43.3761C393.861 43.3761 395.978 59.7464 396.099 61.0967H357.02Z" fill="currentColor" />
                    </svg>

                    {{-- Light Mode 12 SVG --}}
                    <svg class="w-[448px] max-w-none relative -mt-[4.9rem] -ml-8 lg:ml-0 lg:-mt-[6.6rem] dark:hidden" viewBox="0 0 440 376" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g class="transition-all delay-300 translate-y-0 opacity-100 duration-750 starting:opacity-0 starting:translate-y-4">
                            <path d="M188.263 355.73L188.595 355.73C195.441 348.845 205.766 339.761 219.569 328.477C232.93 317.193 242.978 308.205 249.714 301.511C256.34 294.626 260.867 287.358 263.296 279.708C265.725 272.058 264.565 264.121 259.816 255.896C254.516 246.716 247.062 239.352 237.454 233.805C227.957 228.067 217.908 225.198 207.307 225.198C196.927 225.197 190.136 227.97 186.934 233.516C183.621 238.872 184.726 246.331 190.247 255.894L125.647 255.891C116.371 239.825 112.395 225.481 113.72 212.858C115.265 200.235 121.559 190.481 132.602 183.596C143.754 176.52 158.607 172.982 177.159 172.983C196.594 172.984 215.863 176.523 234.968 183.6C253.961 190.486 271.299 200.241 286.98 212.864C302.661 225.488 315.14 239.833 324.416 255.899C333.03 270.817 336.841 283.918 335.847 295.203C335.075 306.487 331.376 316.336 324.75 324.751C318.346 333.167 308.408 343.494 294.936 355.734L377.094 355.737L405.917 405.656L217.087 405.649L188.263 355.73Z" fill="black" />
                            <path d="M9.11884 226.339L-13.7396 226.338L-42.7286 176.132L43.0733 176.135L175.595 405.649L112.651 405.647L9.11884 226.339Z" fill="black" />
                            <path d="M188.263 355.73L188.595 355.73C195.441 348.845 205.766 339.761 219.569 328.477C232.93 317.193 242.978 308.205 249.714 301.511C256.34 294.626 260.867 287.358 263.296 279.708C265.725 272.058 264.565 264.121 259.816 255.896C254.516 246.716 247.062 239.352 237.454 233.805C227.957 228.067 217.908 225.198 207.307 225.198C196.927 225.197 190.136 227.97 186.934 233.516C183.621 238.872 184.726 246.331 190.247 255.894L125.647 255.891C116.371 239.825 112.395 225.481 113.72 212.858C115.265 200.235 121.559 190.481 132.602 183.596C143.754 176.52 158.607 172.982 177.159 172.983C196.594 172.984 215.863 176.523 234.968 183.6C253.961 190.486 271.299 200.241 286.98 212.864C302.661 225.488 315.14 239.833 324.416 255.899C333.03 270.817 336.841 283.918 335.847 295.203C335.075 306.487 331.376 316.336 324.75 324.751C318.346 333.167 308.408 343.494 294.936 355.734L377.094 355.737L405.917 405.656L217.087 405.649L188.263 355.73Z" stroke="#1B1B18" stroke-width="1" />
                            <path d="M9.11884 226.339L-13.7396 226.338L-42.7286 176.132L43.0733 176.135L175.595 405.649L112.651 405.647L9.11884 226.339Z" stroke="#1B1B18" stroke-width="1" />
                            <path d="M204.592 327.449L204.923 327.449C211.769 320.564 222.094 311.479 235.897 300.196C249.258 288.912 259.306 279.923 266.042 273.23C272.668 266.345 277.195 259.077 279.624 251.427C282.053 243.777 280.893 235.839 276.145 227.615C270.844 218.435 263.39 211.071 253.782 205.524C244.285 199.786 234.236 196.917 223.635 196.916C213.255 196.916 206.464 199.689 203.262 205.235C199.949 210.59 201.054 218.049 206.575 227.612L141.975 227.61C132.699 211.544 128.723 197.2 130.048 184.577C131.593 171.954 137.887 162.2 148.93 155.315C160.083 148.239 174.935 144.701 193.487 144.702C212.922 144.703 232.192 148.242 251.296 155.319C270.289 162.205 287.627 171.96 303.308 184.583C318.989 197.207 331.468 211.552 340.745 227.618C349.358 242.536 353.169 255.637 352.175 266.921C351.403 278.205 347.704 288.055 341.078 296.47C334.674 304.885 324.736 315.213 311.264 327.453L393.422 327.456L422.246 377.375L233.415 377.368L204.592 327.449Z" fill="#F8B803" />
                            <path d="M25.447 198.058L2.58852 198.057L-26.4005 147.851L59.4015 147.854L191.923 377.368L128.979 377.365L25.447 198.058Z" fill="#F8B803" />
                            <path d="M204.592 327.449L204.923 327.449C211.769 320.564 222.094 311.479 235.897 300.196C249.258 288.912 259.306 279.923 266.042 273.23C272.668 266.345 277.195 259.077 279.624 251.427C282.053 243.777 280.893 235.839 276.145 227.615C270.844 218.435 263.39 211.071 253.782 205.524C244.285 199.786 234.236 196.917 223.635 196.916C213.255 196.916 206.464 199.689 203.262 205.235C199.949 210.59 201.054 218.049 206.575 227.612L141.975 227.61C132.699 211.544 128.723 197.2 130.048 184.577C131.593 171.954 137.887 162.2 148.93 155.315C160.083 148.239 174.935 144.701 193.487 144.702C212.922 144.703 232.192 148.242 251.296 155.319C270.289 162.205 287.627 171.96 303.308 184.583C318.989 197.207 331.468 211.552 340.745 227.618C349.358 242.536 353.169 255.637 352.175 266.921C351.403 278.205 347.704 288.055 341.078 296.47C334.674 304.885 324.736 315.213 311.264 327.453L393.422 327.456L422.246 377.375L233.415 377.368L204.592 327.449Z" stroke="#1B1B18" stroke-width="1" />
                            <path d="M25.447 198.058L2.58852 198.057L-26.4005 147.851L59.4015 147.854L191.923 377.368L128.979 377.365L25.447 198.058Z" stroke="#1B1B18" stroke-width="1" />
                        </g>
                        <g style="mix-blend-mode: hard-light" class="transition-all delay-300 translate-y-0 opacity-100 duration-750 starting:opacity-0 starting:translate-y-4">
                            <path d="M217.342 305.363L217.673 305.363C224.519 298.478 234.844 289.393 248.647 278.11C262.008 266.826 272.056 257.837 278.792 251.144C285.418 244.259 289.945 236.991 292.374 229.341C294.803 221.691 293.643 213.753 288.895 205.529C283.594 196.349 276.14 188.985 266.532 183.438C257.035 177.7 246.986 174.831 236.385 174.83C226.005 174.83 219.214 177.603 216.012 183.149C212.699 188.504 213.804 195.963 219.325 205.527L154.725 205.524C145.449 189.458 141.473 175.114 142.798 162.491C144.343 149.868 150.637 140.114 161.68 133.229C172.833 126.153 187.685 122.615 206.237 122.616C225.672 122.617 244.942 126.156 264.046 133.233C283.039 140.119 300.377 149.874 316.058 162.497C331.739 175.121 344.218 189.466 353.495 205.532C362.108 220.45 365.919 233.551 364.925 244.835C364.153 256.12 360.454 265.969 353.828 274.384C347.424 282.799 337.486 293.127 324.014 305.367L406.172 305.37L434.996 355.289L246.165 355.282L217.342 305.363Z" fill="#F0ACB8" />
                            <path d="M38.197 175.972L15.3385 175.971L-13.6505 125.765L72.1515 125.768L204.673 355.282L141.729 355.279L38.197 175.972Z" fill="#F0ACB8" />
                            <path d="M217.342 305.363L217.673 305.363C224.519 298.478 234.844 289.393 248.647 278.11C262.008 266.826 272.056 257.837 278.792 251.144C285.418 244.259 289.945 236.991 292.374 229.341C294.803 221.691 293.643 213.753 288.895 205.529C283.594 196.349 276.14 188.985 266.532 183.438C257.035 177.7 246.986 174.831 236.385 174.83C226.005 174.83 219.214 177.603 216.012 183.149C212.699 188.504 213.804 195.963 219.325 205.527L154.725 205.524C145.449 189.458 141.473 175.114 142.798 162.491C144.343 149.868 150.637 140.114 161.68 133.229C172.833 126.153 187.685 122.615 206.237 122.616C225.672 122.617 244.942 126.156 264.046 133.233C283.039 140.119 300.377 149.874 316.058 162.497C331.739 175.121 344.218 189.466 353.495 205.532C362.108 220.45 365.919 233.551 364.925 244.835C364.153 256.12 360.454 265.969 353.828 274.384C347.424 282.799 337.486 293.127 324.014 305.367L406.172 305.37L434.996 355.289L246.165 355.282L217.342 305.363Z" stroke="#1B1B18" stroke-width="1" />
                            <path d="M38.197 175.972L15.3385 175.971L-13.6505 125.765L72.1515 125.768L204.673 355.282L141.729 355.279L38.197 175.972Z" stroke="#1B1B18" stroke-width="1" />
                        </g>
                        <g style="mix-blend-mode: plus-darker" class="transition-all delay-300 translate-y-0 opacity-100 duration-750 starting:opacity-0 starting:translate-y-4">
                            <path d="M230.951 281.792L231.282 281.793C238.128 274.907 248.453 265.823 262.256 254.539C275.617 243.256 285.666 234.267 292.402 227.573C299.027 220.688 303.554 213.421 305.983 205.771C308.412 198.12 307.253 190.183 302.504 181.959C297.203 172.778 289.749 165.415 280.142 159.868C270.645 154.13 260.596 151.26 249.995 151.26C239.615 151.26 232.823 154.033 229.621 159.579C226.309 164.934 227.413 172.393 232.935 181.956L168.335 181.954C159.058 165.888 155.082 151.543 156.407 138.92C157.953 126.298 164.247 116.544 175.289 109.659C186.442 102.583 201.294 99.045 219.846 99.0457C239.281 99.0464 258.551 102.585 277.655 109.663C296.649 116.549 313.986 126.303 329.667 138.927C345.349 151.551 357.827 165.895 367.104 181.961C375.718 196.88 379.528 209.981 378.535 221.265C377.762 232.549 374.063 242.399 367.438 250.814C361.033 259.229 351.095 269.557 337.624 281.796L419.782 281.8L448.605 331.719L259.774 331.712L230.951 281.792Z" fill="#F3BEC7" />
                            <path d="M51.8063 152.402L28.9479 152.401L-0.0411453 102.195L85.7608 102.198L218.282 331.711L155.339 331.709L51.8063 152.402Z" fill="#F3BEC7" />
                            <path d="M230.951 281.792L231.282 281.793C238.128 274.907 248.453 265.823 262.256 254.539C275.617 243.256 285.666 234.267 292.402 227.573C299.027 220.688 303.554 213.421 305.983 205.771C308.412 198.12 307.253 190.183 302.504 181.959C297.203 172.778 289.749 165.415 280.142 159.868C270.645 154.13 260.596 151.26 249.995 151.26C239.615 151.26 232.823 154.033 229.621 159.579C226.309 164.934 227.413 172.393 232.935 181.956L168.335 181.954C159.058 165.888 155.082 151.543 156.407 138.92C157.953 126.298 164.247 116.544 175.289 109.659C186.442 102.583 201.294 99.045 219.846 99.0457C239.281 99.0464 258.551 102.585 277.655 109.663C296.649 116.549 313.986 126.303 329.667 138.927C345.349 151.551 357.827 165.895 367.104 181.961C375.718 196.88 379.528 209.981 378.535 221.265C377.762 232.549 374.063 242.399 367.438 250.814C361.033 259.229 351.095 269.557 337.624 281.796L419.782 281.8L448.605 331.719L259.774 331.712L230.951 281.792Z" stroke="#1B1B18" stroke-width="1" />
                            <path d="M51.8063 152.402L28.9479 152.401L-0.0411453 102.195L85.7608 102.198L218.282 331.711L155.339 331.709L51.8063 152.402Z" stroke="#1B1B18" stroke-width="1" />
                        </g>
                        <g class="transition-all delay-300 translate-y-0 opacity-100 duration-750 starting:opacity-0 starting:translate-y-4">
                            <path d="M188.467 355.363L188.798 355.363C195.644 348.478 205.969 339.393 219.772 328.11C233.133 316.826 243.181 307.837 249.917 301.144C253.696 297.217 256.792 293.166 259.205 288.991C261.024 285.845 262.455 282.628 263.499 279.341C265.928 271.691 264.768 263.753 260.02 255.529C254.719 246.349 247.265 238.985 237.657 233.438C228.16 227.7 218.111 224.831 207.51 224.83C197.13 224.83 190.339 227.603 187.137 233.149C183.824 238.504 184.929 245.963 190.45 255.527L125.851 255.524C116.574 239.458 112.598 225.114 113.923 212.491C114.615 206.836 116.261 201.756 118.859 197.253C122.061 191.704 126.709 187.03 132.805 183.229C143.958 176.153 158.81 172.615 177.362 172.616C196.797 172.617 216.067 176.156 235.171 183.233C254.164 190.119 271.502 199.874 287.183 212.497C302.864 225.121 315.343 239.466 324.62 255.532C333.233 270.45 337.044 283.551 336.05 294.835C335.46 303.459 333.16 311.245 329.151 318.194C327.915 320.337 326.515 322.4 324.953 324.384C318.549 332.799 308.611 343.127 295.139 355.367L377.297 355.37L406.121 405.289L217.29 405.282L188.467 355.363Z" stroke="#1B1B18" stroke-width="1" stroke-linejoin="bevel" />
                            <path d="M9.32197 225.972L-13.5365 225.971L-42.5255 175.765L43.2765 175.768L175.798 405.282L112.854 405.279L9.32197 225.972Z" stroke="#1B1B18" stroke-width="1" stroke-linejoin="bevel" />
                            <path d="M345.247 111.915C329.566 99.2919 312.229 89.5371 293.235 82.6512L235.167 183.228C254.161 190.114 271.498 199.869 287.179 212.492L345.247 111.915Z" stroke="#1B1B18" stroke-width="1" stroke-linejoin="bevel" />
                            <path d="M382.686 154.964C373.41 138.898 360.931 124.553 345.25 111.93L287.182 212.506C302.863 225.13 315.342 239.475 324.618 255.541L382.686 154.964Z" stroke="#1B1B18" stroke-width="1" stroke-linejoin="bevel" />
                            <path d="M293.243 82.6472C274.139 75.57 254.869 72.031 235.434 72.0303L177.366 172.607C196.801 172.608 216.071 176.147 235.175 183.224L293.243 82.6472Z" stroke="#1B1B18" stroke-width="1" stroke-linejoin="bevel" />
                            <path d="M394.118 194.257C395.112 182.973 391.301 169.872 382.688 154.953L324.619 255.53C333.233 270.448 337.044 283.55 336.05 294.834L394.118 194.257Z" stroke="#1B1B18" stroke-width="1" stroke-linejoin="bevel" />
                            <path d="M235.432 72.0311C216.88 72.0304 202.027 75.5681 190.875 82.6442L132.806 183.221C143.959 176.145 158.812 172.607 177.363 172.608L235.432 72.0311Z" stroke="#1B1B18" stroke-width="1" stroke-linejoin="bevel" />
                            <path d="M265.59 124.25C276.191 124.251 286.24 127.12 295.737 132.858L237.669 233.435C228.172 227.697 218.123 224.828 207.522 224.827L265.59 124.25Z" stroke="#1B1B18" stroke-width="1" stroke-linejoin="bevel" />
                            <path d="M295.719 132.859C305.326 138.406 312.78 145.77 318.081 154.95L260.013 255.527C254.712 246.347 247.258 238.983 237.651 233.436L295.719 132.859Z" stroke="#1B1B18" stroke-width="1" stroke-linejoin="bevel" />
                            <path d="M387.218 217.608C391.227 210.66 393.527 202.874 394.117 194.25L336.049 294.827C335.459 303.451 333.159 311.237 329.15 318.185L387.218 217.608Z" stroke="#1B1B18" stroke-width="1" stroke-linejoin="bevel" />
                            <path d="M245.211 132.577C248.413 127.03 255.204 124.257 265.584 124.258L207.516 224.835C197.136 224.834 190.345 227.607 187.143 233.154L245.211 132.577Z" stroke="#1B1B18" stroke-width="1" stroke-linejoin="bevel" />
                            <path d="M318.094 154.945C322.842 163.17 324.002 171.107 321.573 178.757L263.505 279.334C265.934 271.684 264.774 263.746 260.026 255.522L318.094 154.945Z" stroke="#1B1B18" stroke-width="1" stroke-linejoin="bevel" />
                            <path d="M176.925 96.6737C180.127 91.1249 184.776 86.4503 190.871 82.6499L132.803 183.227C126.708 187.027 122.059 191.702 118.857 197.25L176.925 96.6737Z" stroke="#1B1B18" stroke-width="1" stroke-linejoin="bevel" />
                            <path d="M387.226 217.606C385.989 219.749 384.59 221.813 383.028 223.797L324.96 324.373C326.522 322.39 327.921 320.326 329.157 318.183L387.226 217.606Z" stroke="#1B1B18" stroke-width="1" stroke-linejoin="bevel" />
                            <path d="M317.269 188.408C319.087 185.262 320.519 182.045 321.562 178.758L263.494 279.335C262.451 282.622 261.019 285.839 259.201 288.985L317.269 188.408Z" stroke="#1B1B18" stroke-width="1" stroke-linejoin="bevel" />
                            <path d="M245.208 132.573C241.895 137.928 243 145.387 248.522 154.95L190.454 255.527C184.932 245.964 183.827 238.505 187.14 233.15L245.208 132.573Z" stroke="#1B1B18" stroke-width="1" stroke-linejoin="bevel" />
                            <path d="M176.93 96.6719C174.331 101.175 172.686 106.255 171.993 111.91L113.925 212.487C114.618 206.831 116.263 201.752 118.862 197.249L176.93 96.6719Z" stroke="#1B1B18" stroke-width="1" stroke-linejoin="bevel" />
                            <path d="M317.266 188.413C314.853 192.589 311.757 196.64 307.978 200.566L249.91 301.143C253.689 297.216 256.785 293.166 259.198 288.99L317.266 188.413Z" stroke="#1B1B18" stroke-width="1" stroke-linejoin="bevel" />
                            <path d="M464.198 304.708L435.375 254.789L377.307 355.366L406.13 405.285L464.198 304.708Z" stroke="#1B1B18" stroke-width="1" stroke-linejoin="bevel" />
                            <path d="M353.209 254.787C366.68 242.548 376.618 232.22 383.023 223.805L324.955 324.382C318.55 332.797 308.612 343.124 295.141 355.364L353.209 254.787Z" stroke="#1B1B18" stroke-width="1" stroke-linejoin="bevel" />
                            <path d="M435.37 254.787L353.212 254.784L295.144 355.361L377.302 355.364L435.37 254.787Z" stroke="#1B1B18" stroke-width="1" stroke-linejoin="bevel" />
                            <path d="M183.921 154.947L248.521 154.95L190.453 255.527L125.853 255.524L183.921 154.947Z" stroke="#1B1B18" stroke-width="1" stroke-linejoin="bevel" />
                            <path d="M171.992 111.914C170.668 124.537 174.643 138.881 183.92 154.947L125.852 255.524C116.575 239.458 112.599 225.114 113.924 212.491L171.992 111.914Z" stroke="#1B1B18" stroke-width="1" stroke-linejoin="bevel" />
                            <path d="M307.987 200.562C301.251 207.256 291.203 216.244 277.842 227.528L219.774 328.105C233.135 316.821 243.183 307.832 249.919 301.139L307.987 200.562Z" stroke="#1B1B18" stroke-width="1" stroke-linejoin="bevel" />
                            <path d="M15.5469 75.1797L44.5359 125.386L-13.5321 225.963L-42.5212 175.756L15.5469 75.1797Z" stroke="#1B1B18" stroke-width="1" stroke-linejoin="bevel" />
                            <path d="M277.836 227.536C264.033 238.82 253.708 247.904 246.862 254.789L188.794 355.366C195.64 348.481 205.965 339.397 219.768 328.113L277.836 227.536Z" stroke="#1B1B18" stroke-width="1" stroke-linejoin="bevel" />
                            <path d="M275.358 304.706L464.189 304.713L406.12 405.29L217.29 405.283L275.358 304.706Z" stroke="#1B1B18" stroke-width="1" stroke-linejoin="bevel" />
                            <path d="M44.5279 125.39L67.3864 125.39L9.31834 225.967L-13.5401 225.966L44.5279 125.39Z" stroke="#1B1B18" stroke-width="1" stroke-linejoin="bevel" />
                            <path d="M101.341 75.1911L233.863 304.705L175.795 405.282L43.2733 175.768L101.341 75.1911ZM15.5431 75.19L-42.525 175.767L43.277 175.77L101.345 75.1932L15.5431 75.19Z" stroke="#1B1B18" stroke-width="1" stroke-linejoin="bevel" />
                            <path d="M246.866 254.784L246.534 254.784L188.466 355.361L188.798 355.361L246.866 254.784Z" stroke="#1B1B18" stroke-width="1" stroke-linejoin="bevel" />
                            <path d="M246.539 254.781L275.362 304.701L217.294 405.277L188.471 355.358L246.539 254.781Z" stroke="#1B1B18" stroke-width="1" stroke-linejoin="bevel" />
                            <path d="M67.3906 125.391L170.923 304.698L112.855 405.275L9.32257 225.967L67.3906 125.391Z" stroke="#1B1B18" stroke-width="1" stroke-linejoin="bevel" />
                            <path d="M170.921 304.699L233.865 304.701L175.797 405.278L112.853 405.276L170.921 304.699Z" stroke="#1B1B18" stroke-width="1" stroke-linejoin="bevel" />
                        </g>
                        <g style="mix-blend-mode: hard-light" class="transition-all delay-300 translate-y-0 opacity-100 duration-750 starting:opacity-0 starting:translate-y-4">
                            <path d="M246.544 254.79L246.875 254.79C253.722 247.905 264.046 238.82 277.849 227.537C291.21 216.253 301.259 207.264 307.995 200.57C314.62 193.685 319.147 186.418 321.577 178.768C324.006 171.117 322.846 163.18 318.097 154.956C312.796 145.775 305.342 138.412 295.735 132.865C286.238 127.127 276.189 124.258 265.588 124.257C255.208 124.257 248.416 127.03 245.214 132.576C241.902 137.931 243.006 145.39 248.528 154.953L183.928 154.951C174.652 138.885 170.676 124.541 172 111.918C173.546 99.2946 179.84 89.5408 190.882 82.6559C202.035 75.5798 216.887 72.0421 235.439 72.0428C254.874 72.0435 274.144 75.5825 293.248 82.6598C312.242 89.5457 329.579 99.3005 345.261 111.924C360.942 124.548 373.421 138.892 382.697 154.958C391.311 169.877 395.121 182.978 394.128 194.262C393.355 205.546 389.656 215.396 383.031 223.811C376.627 232.226 366.688 242.554 353.217 254.794L435.375 254.797L464.198 304.716L275.367 304.709L246.544 254.79Z" fill="#F0ACB8" />
                            <path d="M246.544 254.79L246.875 254.79C253.722 247.905 264.046 238.82 277.849 227.537C291.21 216.253 301.259 207.264 307.995 200.57C314.62 193.685 319.147 186.418 321.577 178.768C324.006 171.117 322.846 163.18 318.097 154.956C312.796 145.775 305.342 138.412 295.735 132.865C286.238 127.127 276.189 124.258 265.588 124.257C255.208 124.257 248.416 127.03 245.214 132.576C241.902 137.931 243.006 145.39 248.528 154.953L183.928 154.951C174.652 138.885 170.676 124.541 172 111.918C173.546 99.2946 179.84 89.5408 190.882 82.6559C202.035 75.5798 216.887 72.0421 235.439 72.0428C254.874 72.0435 274.144 75.5825 293.248 82.6598C312.242 89.5457 329.579 99.3005 345.261 111.924C360.942 124.548 373.421 138.892 382.697 154.958C391.311 169.877 395.121 182.978 394.128 194.262C393.355 205.546 389.656 215.396 383.031 223.811C376.627 232.226 366.688 242.554 353.217 254.794L435.375 254.797L464.198 304.716L275.367 304.709L246.544 254.79Z" stroke="#1B1B18" stroke-width="1" stroke-linejoin="round" />
                        </g>
                        <g style="mix-blend-mode: hard-light" class="transition-all delay-300 translate-y-0 opacity-100 duration-750 starting:opacity-0 starting:translate-y-4">
                            <path d="M67.41 125.402L44.5515 125.401L15.5625 75.1953L101.364 75.1985L233.886 304.712L170.942 304.71L67.41 125.402Z" fill="#F0ACB8" />
                            <path d="M67.41 125.402L44.5515 125.401L15.5625 75.1953L101.364 75.1985L233.886 304.712L170.942 304.71L67.41 125.402Z" stroke="#1B1B18" stroke-width="1" />
                        </g>
                    </svg>

                    {{-- Dark Mode 12 SVG --}}
                    <svg class="w-[448px] max-w-none relative -mt-[4.9rem] -ml-8 lg:ml-0 lg:-mt-[6.6rem] hidden dark:block" viewBox="0 0 440 376" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g class="transition-all delay-300 translate-y-0 opacity-100 duration-750 starting:opacity-0 starting:translate-y-4">
                            <path d="M188.263 355.73L188.595 355.73C195.441 348.845 205.766 339.761 219.569 328.477C232.93 317.193 242.978 308.205 249.714 301.511C256.34 294.626 260.867 287.358 263.296 279.708C265.725 272.058 264.565 264.121 259.816 255.896C254.516 246.716 247.062 239.352 237.454 233.805C227.957 228.067 217.908 225.198 207.307 225.198C196.927 225.197 190.136 227.97 186.934 233.516C183.621 238.872 184.726 246.331 190.247 255.894L125.647 255.891C116.371 239.825 112.395 225.481 113.72 212.858C115.265 200.235 121.559 190.481 132.602 183.596C143.754 176.52 158.607 172.982 177.159 172.983C196.594 172.984 215.863 176.523 234.968 183.6C253.961 190.486 271.299 200.241 286.98 212.864C302.661 225.488 315.14 239.833 324.416 255.899C333.03 270.817 336.841 283.918 335.847 295.203C335.075 306.487 331.376 316.336 324.75 324.751C318.346 333.167 308.408 343.494 294.936 355.734L377.094 355.737L405.917 405.656L217.087 405.649L188.263 355.73Z" fill="black"/>
                            <path d="M9.11884 226.339L-13.7396 226.338L-42.7286 176.132L43.0733 176.135L175.595 405.649L112.651 405.647L9.11884 226.339Z" fill="black"/>
                            <path d="M188.263 355.73L188.595 355.73C195.441 348.845 205.766 339.761 219.569 328.477C232.93 317.193 242.978 308.205 249.714 301.511C256.34 294.626 260.867 287.358 263.296 279.708C265.725 272.058 264.565 264.121 259.816 255.896C254.516 246.716 247.062 239.352 237.454 233.805C227.957 228.067 217.908 225.198 207.307 225.198C196.927 225.197 190.136 227.97 186.934 233.516C183.621 238.872 184.726 246.331 190.247 255.894L125.647 255.891C116.371 239.825 112.395 225.481 113.72 212.858C115.265 200.235 121.559 190.481 132.602 183.596C143.754 176.52 158.607 172.982 177.159 172.983C196.594 172.984 215.863 176.523 234.968 183.6C253.961 190.486 271.299 200.241 286.98 212.864C302.661 225.488 315.14 239.833 324.416 255.899C333.03 270.817 336.841 283.918 335.847 295.203C335.075 306.487 331.376 316.336 324.75 324.751C318.346 333.167 308.408 343.494 294.936 355.734L377.094 355.737L405.917 405.656L217.087 405.649L188.263 355.73Z" stroke="#FF750F" stroke-width="1"/>
                            <path d="M9.11884 226.339L-13.7396 226.338L-42.7286 176.132L43.0733 176.135L175.595 405.649L112.651 405.647L9.11884 226.339Z" stroke="#FF750F" stroke-width="1"/>
                            <path d="M204.592 327.449L204.923 327.449C211.769 320.564 222.094 311.479 235.897 300.196C249.258 288.912 259.306 279.923 266.042 273.23C272.668 266.345 277.195 259.077 279.624 251.427C282.053 243.777 280.893 235.839 276.145 227.615C270.844 218.435 263.39 211.071 253.782 205.524C244.285 199.786 234.236 196.917 223.635 196.916C213.255 196.916 206.464 199.689 203.262 205.235C199.949 210.59 201.054 218.049 206.575 227.612L141.975 227.61C132.699 211.544 128.723 197.2 130.048 184.577C131.593 171.954 137.887 162.2 148.93 155.315C160.083 148.239 174.935 144.701 193.487 144.702C212.922 144.703 232.192 148.242 251.296 155.319C270.289 162.205 287.627 171.96 303.308 184.583C318.989 197.207 331.468 211.552 340.745 227.618C349.358 242.536 353.169 255.637 352.175 266.921C351.403 278.205 347.704 288.055 341.078 296.47C334.674 304.885 324.736 315.213 311.264 327.453L393.422 327.456L422.246 377.375L233.415 377.368L204.592 327.449Z" fill="#391800"/>
                            <path d="M25.447 198.058L2.58852 198.057L-26.4005 147.851L59.4015 147.854L191.923 377.368L128.979 377.365L25.447 198.058Z" fill="#391800"/>
                            <path d="M204.592 327.449L204.923 327.449C211.769 320.564 222.094 311.479 235.897 300.196C249.258 288.912 259.306 279.923 266.042 273.23C272.668 266.345 277.195 259.077 279.624 251.427C282.053 243.777 280.893 235.839 276.145 227.615C270.844 218.435 263.39 211.071 253.782 205.524C244.285 199.786 234.236 196.917 223.635 196.916C213.255 196.916 206.464 199.689 203.262 205.235C199.949 210.59 201.054 218.049 206.575 227.612L141.975 227.61C132.699 211.544 128.723 197.2 130.048 184.577C131.593 171.954 137.887 162.2 148.93 155.315C160.083 148.239 174.935 144.701 193.487 144.702C212.922 144.703 232.192 148.242 251.296 155.319C270.289 162.205 287.627 171.96 303.308 184.583C318.989 197.207 331.468 211.552 340.745 227.618C349.358 242.536 353.169 255.637 352.175 266.921C351.403 278.205 347.704 288.055 341.078 296.47C334.674 304.885 324.736 315.213 311.264 327.453L393.422 327.456L422.246 377.375L233.415 377.368L204.592 327.449Z" stroke="#FF750F" stroke-width="1"/>
                            <path d="M25.447 198.058L2.58852 198.057L-26.4005 147.851L59.4015 147.854L191.923 377.368L128.979 377.365L25.447 198.058Z" stroke="#FF750F" stroke-width="1"/>
                        </g>
                        <g class="transition-all delay-300 translate-y-0 opacity-100 duration-750 starting:opacity-0 starting:translate-y-4" style="mix-blend-mode:hard-light">
                            <path d="M217.342 305.363L217.673 305.363C224.519 298.478 234.844 289.393 248.647 278.11C262.008 266.826 272.056 257.837 278.792 251.144C285.418 244.259 289.945 236.991 292.374 229.341C294.803 221.691 293.643 213.753 288.895 205.529C283.594 196.349 276.14 188.985 266.532 183.438C257.035 177.7 246.986 174.831 236.385 174.83C226.005 174.83 219.214 177.603 216.012 183.149C212.699 188.504 213.804 195.963 219.325 205.527L154.725 205.524C145.449 189.458 141.473 175.114 142.798 162.491C144.343 149.868 150.637 140.114 161.68 133.229C172.833 126.153 187.685 122.615 206.237 122.616C225.672 122.617 244.942 126.156 264.046 133.233C283.039 140.119 300.377 149.874 316.058 162.497C331.739 175.121 344.218 189.466 353.495 205.532C362.108 220.45 365.919 233.551 364.925 244.835C364.153 256.12 360.454 265.969 353.828 274.384C347.424 282.799 337.486 293.127 324.014 305.367L406.172 305.37L434.996 355.289L246.165 355.282L217.342 305.363Z" fill="#733000"/>
                            <path d="M38.197 175.972L15.3385 175.971L-13.6505 125.765L72.1515 125.768L204.673 355.282L141.729 355.279L38.197 175.972Z" fill="#733000"/>
                            <path d="M217.342 305.363L217.673 305.363C224.519 298.478 234.844 289.393 248.647 278.11C262.008 266.826 272.056 257.837 278.792 251.144C285.418 244.259 289.945 236.991 292.374 229.341C294.803 221.691 293.643 213.753 288.895 205.529C283.594 196.349 276.14 188.985 266.532 183.438C257.035 177.7 246.986 174.831 236.385 174.83C226.005 174.83 219.214 177.603 216.012 183.149C212.699 188.504 213.804 195.963 219.325 205.527L154.725 205.524C145.449 189.458 141.473 175.114 142.798 162.491C144.343 149.868 150.637 140.114 161.68 133.229C172.833 126.153 187.685 122.615 206.237 122.616C225.672 122.617 244.942 126.156 264.046 133.233C283.039 140.119 300.377 149.874 316.058 162.497C331.739 175.121 344.218 189.466 353.495 205.532C362.108 220.45 365.919 233.551 364.925 244.835C364.153 256.12 360.454 265.969 353.828 274.384C347.424 282.799 337.486 293.127 324.014 305.367L406.172 305.37L434.996 355.289L246.165 355.282L217.342 305.363Z" stroke="#FF750F" stroke-width="1"/>
                            <path d="M38.197 175.972L15.3385 175.971L-13.6505 125.765L72.1515 125.768L204.673 355.282L141.729 355.279L38.197 175.972Z" stroke="#FF750F" stroke-width="1"/>
                        </g>
                        <g class="transition-all delay-300 translate-y-0 opacity-100 duration-750 starting:opacity-0 starting:translate-y-4">
                            <path d="M217.342 305.363L217.673 305.363C224.519 298.478 234.844 289.393 248.647 278.11C262.008 266.826 272.056 257.837 278.792 251.144C285.418 244.259 289.945 236.991 292.374 229.341C294.803 221.691 293.643 213.753 288.895 205.529C283.594 196.349 276.14 188.985 266.532 183.438C257.035 177.7 246.986 174.831 236.385 174.83C226.005 174.83 219.214 177.603 216.012 183.149C212.699 188.504 213.804 195.963 219.325 205.527L154.726 205.524C145.449 189.458 141.473 175.114 142.798 162.491C144.343 149.868 150.637 140.114 161.68 133.229C172.833 126.153 187.685 122.615 206.237 122.616C225.672 122.617 244.942 126.156 264.046 133.233C283.039 140.119 300.377 149.874 316.058 162.497C331.739 175.121 344.218 189.466 353.495 205.532C362.108 220.45 365.919 233.551 364.925 244.835C364.153 256.12 360.454 265.969 353.828 274.384C347.424 282.799 337.486 293.127 324.014 305.367L406.172 305.37L434.996 355.289L246.165 355.282L217.342 305.363Z" stroke="#FF750F" stroke-width="1"/>
                            <path d="M38.197 175.972L15.3385 175.971L-13.6505 125.765L72.1515 125.768L204.673 355.282L141.729 355.279L38.197 175.972Z" stroke="#FF750F" stroke-width="1"/>
                        </g>
                        <g class="transition-all delay-300 translate-y-0 opacity-100 duration-750 starting:opacity-0 starting:translate-y-4">
                            <path d="M188.467 355.363L188.798 355.363C195.644 348.478 205.969 339.393 219.772 328.11C233.133 316.826 243.181 307.837 249.917 301.144C253.696 297.217 256.792 293.166 259.205 288.991C261.024 285.845 262.455 282.628 263.499 279.341C265.928 271.691 264.768 263.753 260.02 255.529C254.719 246.349 247.265 238.985 237.657 233.438C228.16 227.7 218.111 224.831 207.51 224.83C197.13 224.83 190.339 227.603 187.137 233.149C183.824 238.504 184.929 245.963 190.45 255.527L125.851 255.524C116.574 239.458 112.598 225.114 113.923 212.491C114.615 206.836 116.261 201.756 118.859 197.253C122.061 191.704 126.709 187.03 132.805 183.229C143.958 176.153 158.81 172.615 177.362 172.616C196.797 172.617 216.067 176.156 235.171 183.233C254.164 190.119 271.502 199.874 287.183 212.497C302.864 225.121 315.343 239.466 324.62 255.532C333.233 270.45 337.044 283.551 336.05 294.835C335.46 303.459 333.16 311.245 329.151 318.194C327.915 320.337 326.515 322.4 324.953 324.384C318.549 332.799 308.611 343.127 295.139 355.367L377.297 355.37L406.121 405.289L217.29 405.282L188.467 355.363Z" stroke="#FF750F" stroke-width="1" stroke-linejoin="bevel"/>
                            <path d="M9.32197 225.972L-13.5365 225.971L-42.5255 175.765L43.2765 175.768L175.798 405.282L112.854 405.279L9.32197 225.972Z" stroke="#FF750F" stroke-width="1" stroke-linejoin="bevel"/>
                            <path d="M345.247 111.915C329.566 99.2919 312.229 89.5371 293.235 82.6512L235.167 183.228C254.161 190.114 271.498 199.869 287.179 212.492L345.247 111.915Z" stroke="#FF750F" stroke-width="1" stroke-linejoin="bevel"/>
                            <path d="M382.686 154.964C373.41 138.898 360.931 124.553 345.25 111.93L287.182 212.506C302.863 225.13 315.342 239.475 324.618 255.541L382.686 154.964Z" stroke="#FF750F" stroke-width="1" stroke-linejoin="bevel"/>
                            <path d="M293.243 82.6472C274.139 75.57 254.869 72.031 235.434 72.0303L177.366 172.607C196.801 172.608 216.071 176.147 235.175 183.224L293.243 82.6472Z" stroke="#FF750F" stroke-width="1" stroke-linejoin="bevel"/>
                            <path d="M394.118 194.257C395.112 182.973 391.301 169.872 382.688 154.953L324.619 255.53C333.233 270.448 337.044 283.55 336.05 294.834L394.118 194.257Z" stroke="#FF750F" stroke-width="1" stroke-linejoin="bevel"/>
                            <path d="M235.432 72.0311C216.88 72.0304 202.027 75.5681 190.875 82.6442L132.806 183.221C143.959 176.145 158.812 172.607 177.363 172.608L235.432 72.0311Z" stroke="#FF750F" stroke-width="1" stroke-linejoin="bevel"/>
                            <path d="M265.59 124.25C276.191 124.251 286.24 127.12 295.737 132.858L237.669 233.435C228.172 227.697 218.123 224.828 207.522 224.827L265.59 124.25Z" stroke="#FF750F" stroke-width="1" stroke-linejoin="bevel"/>
                            <path d="M295.719 132.859C305.326 138.406 312.78 145.77 318.081 154.95L260.013 255.527C254.712 246.347 247.258 238.983 237.651 233.436L295.719 132.859Z" stroke="#FF750F" stroke-width="1" stroke-linejoin="bevel"/>
                            <path d="M387.218 217.608C391.227 210.66 393.527 202.874 394.117 194.25L336.049 294.827C335.459 303.451 333.159 311.237 329.15 318.185L387.218 217.608Z" stroke="#FF750F" stroke-width="1" stroke-linejoin="bevel"/>
                            <path d="M245.211 132.577C248.413 127.03 255.204 124.257 265.584 124.258L207.516 224.835C197.136 224.834 190.345 227.607 187.143 233.154L245.211 132.577Z" stroke="#FF750F" stroke-width="1" stroke-linejoin="bevel"/>
                            <path d="M318.094 154.945C322.842 163.17 324.002 171.107 321.573 178.757L263.505 279.334C265.934 271.684 264.774 263.746 260.026 255.522L318.094 154.945Z" stroke="#FF750F" stroke-width="1" stroke-linejoin="bevel"/>
                            <path d="M176.925 96.6737C180.127 91.1249 184.776 86.4503 190.871 82.6499L132.803 183.227C126.708 187.027 122.059 191.702 118.857 197.25L176.925 96.6737Z" stroke="#FF750F" stroke-width="1" stroke-linejoin="bevel"/>
                            <path d="M387.226 217.606C385.989 219.749 384.59 221.813 383.028 223.797L324.96 324.373C326.522 322.39 327.921 320.326 329.157 318.183L387.226 217.606Z" stroke="#FF750F" stroke-width="1" stroke-linejoin="bevel"/>
                            <path d="M317.269 188.408C319.087 185.262 320.519 182.045 321.562 178.758L263.494 279.335C262.451 282.622 261.019 285.839 259.201 288.985L317.269 188.408Z" stroke="#FF750F" stroke-width="1" stroke-linejoin="bevel"/>
                            <path d="M245.208 132.573C241.895 137.928 243 145.387 248.522 154.95L190.454 255.527C184.932 245.964 183.827 238.505 187.14 233.15L245.208 132.573Z" stroke="#FF750F" stroke-width="1" stroke-linejoin="bevel"/>
                            <path d="M176.93 96.6719C174.331 101.175 172.686 106.255 171.993 111.91L113.925 212.487C114.618 206.831 116.263 201.752 118.862 197.249L176.93 96.6719Z" stroke="#FF750F" stroke-width="1" stroke-linejoin="bevel"/>
                            <path d="M317.266 188.413C314.853 192.589 311.757 196.64 307.978 200.566L249.91 301.143C253.689 297.216 256.785 293.166 259.198 288.99L317.266 188.413Z" stroke="#FF750F" stroke-width="1" stroke-linejoin="bevel"/>
                            <path d="M464.198 304.708L435.375 254.789L377.307 355.366L406.13 405.285L464.198 304.708Z" stroke="#FF750F" stroke-width="1" stroke-linejoin="bevel"/>
                            <path d="M353.209 254.787C366.68 242.548 376.618 232.22 383.023 223.805L324.955 324.382C318.55 332.797 308.612 343.124 295.141 355.364L353.209 254.787Z" stroke="#FF750F" stroke-width="1" stroke-linejoin="bevel"/>
                            <path d="M435.37 254.787L353.212 254.784L295.144 355.361L377.302 355.364L435.37 254.787Z" stroke="#FF750F" stroke-width="1" stroke-linejoin="bevel"/>
                            <path d="M183.921 154.947L248.521 154.95L190.453 255.527L125.853 255.524L183.921 154.947Z" stroke="#FF750F" stroke-width="1" stroke-linejoin="bevel"/>
                            <path d="M171.992 111.914C170.668 124.537 174.643 138.881 183.92 154.947L125.852 255.524C116.575 239.458 112.599 225.114 113.924 212.491L171.992 111.914Z" stroke="#FF750F" stroke-width="1" stroke-linejoin="bevel"/>
                            <path d="M307.987 200.562C301.251 207.256 291.203 216.244 277.842 227.528L219.774 328.105C233.135 316.821 243.183 307.832 249.919 301.139L307.987 200.562Z" stroke="#FF750F" stroke-width="1" stroke-linejoin="bevel"/>
                            <path d="M15.5469 75.1797L44.5359 125.386L-13.5321 225.963L-42.5212 175.756L15.5469 75.1797Z" stroke="#FF750F" stroke-width="1" stroke-linejoin="bevel"/>
                            <path d="M277.836 227.536C264.033 238.82 253.708 247.904 246.862 254.789L188.794 355.366C195.64 348.481 205.965 339.397 219.768 328.113L277.836 227.536Z" stroke="#FF750F" stroke-width="1" stroke-linejoin="bevel"/>
                            <path d="M275.358 304.706L464.189 304.713L406.12 405.29L217.29 405.283L275.358 304.706Z" stroke="#FF750F" stroke-width="1" stroke-linejoin="bevel"/>
                            <path d="M44.5279 125.39L67.3864 125.39L9.31834 225.967L-13.5401 225.966L44.5279 125.39Z" stroke="#FF750F" stroke-width="1" stroke-linejoin="bevel"/>
                            <path d="M101.341 75.1911L233.863 304.705L175.795 405.282L43.2733 175.768L101.341 75.1911ZM15.5431 75.19L-42.525 175.767L43.277 175.77L101.345 75.1932L15.5431 75.19Z" stroke="#FF750F" stroke-width="1" stroke-linejoin="bevel"/>
                            <path d="M246.866 254.784L246.534 254.784L188.466 355.361L188.798 355.361L246.866 254.784Z" stroke="#FF750F" stroke-width="1" stroke-linejoin="bevel"/>
                            <path d="M246.539 254.781L275.362 304.701L217.294 405.277L188.471 355.358L246.539 254.781Z" stroke="#FF750F" stroke-width="1" stroke-linejoin="bevel"/>
                            <path d="M67.3906 125.391L170.923 304.698L112.855 405.275L9.32257 225.967L67.3906 125.391Z" stroke="#FF750F" stroke-width="1" stroke-linejoin="bevel"/>
                            <path d="M170.921 304.699L233.865 304.701L175.797 405.278L112.853 405.276L170.921 304.699Z" stroke="#FF750F" stroke-width="1" stroke-linejoin="bevel"/>
                        </g>
                        <g class="transition-all delay-300 translate-y-0 opacity-100 duration-750 starting:opacity-0 starting:translate-y-4" style="mix-blend-mode:hard-light">
                            <path d="M246.544 254.79L246.875 254.79C253.722 247.905 264.046 238.82 277.849 227.537C291.21 216.253 301.259 207.264 307.995 200.57C314.62 193.685 319.147 186.418 321.577 178.768C324.006 171.117 322.846 163.18 318.097 154.956C312.796 145.775 305.342 138.412 295.735 132.865C286.238 127.127 276.189 124.258 265.588 124.257C255.208 124.257 248.416 127.03 245.214 132.576C241.902 137.931 243.006 145.39 248.528 154.953L183.928 154.951C174.652 138.885 170.676 124.541 172 111.918C173.546 99.2946 179.84 89.5408 190.882 82.6559C202.035 75.5798 216.887 72.0421 235.439 72.0428C254.874 72.0435 274.144 75.5825 293.248 82.6598C312.242 89.5457 329.579 99.3005 345.261 111.924C360.942 124.548 373.421 138.892 382.697 154.958C391.311 169.877 395.121 182.978 394.128 194.262C393.355 205.546 389.656 215.396 383.031 223.811C376.627 232.226 366.688 242.554 353.217 254.794L435.375 254.797L464.198 304.716L275.367 304.709L246.544 254.79Z" fill="#4B0600"/>
                            <path d="M246.544 254.79L246.875 254.79C253.722 247.905 264.046 238.82 277.849 227.537C291.21 216.253 301.259 207.264 307.995 200.57C314.62 193.685 319.147 186.418 321.577 178.768C324.006 171.117 322.846 163.18 318.097 154.956C312.796 145.775 305.342 138.412 295.735 132.865C286.238 127.127 276.189 124.258 265.588 124.257C255.208 124.257 248.416 127.03 245.214 132.576C241.902 137.931 243.006 145.39 248.528 154.953L183.928 154.951C174.652 138.885 170.676 124.541 172 111.918C173.546 99.2946 179.84 89.5408 190.882 82.6559C202.035 75.5798 216.887 72.0421 235.439 72.0428C254.874 72.0435 274.144 75.5825 293.248 82.6598C312.242 89.5457 329.579 99.3005 345.261 111.924C360.942 124.548 373.421 138.892 382.697 154.958C391.311 169.877 395.121 182.978 394.128 194.262C393.355 205.546 389.656 215.396 383.031 223.811C376.627 232.226 366.688 242.554 353.217 254.794L435.375 254.797L464.198 304.716L275.367 304.709L246.544 254.79Z" stroke="#FF750F" stroke-width="1" stroke-linejoin="round"/>
                        </g>
                        <g class="transition-all delay-300 translate-y-0 opacity-100 duration-750 starting:opacity-0 starting:translate-y-4" style="mix-blend-mode:hard-light">
                            <path d="M67.41 125.402L44.5515 125.401L15.5625 75.1953L101.364 75.1985L233.886 304.712L170.942 304.71L67.41 125.402Z" fill="#4B0600"/>
                            <path d="M67.41 125.402L44.5515 125.401L15.5625 75.1953L101.364 75.1985L233.886 304.712L170.942 304.71L67.41 125.402Z" stroke="#FF750F" stroke-width="1"/>
                        </g>
                    </svg>
                    <div class="absolute inset-0 rounded-t-lg lg:rounded-t-none lg:rounded-r-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d]"></div>
                </div>
            </main>
        </div>

        @if (Route::has('login'))
            <div class="h-14.5 hidden lg:block"></div>
        @endif
    </body>
</html>

```

---

## 📁 Directory: routes (Routes)

Application routing definitions.

### 📄 File: `./routes/console.php`

```php
<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

```

---

### 📄 File: `./routes/web.php`

```php
<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

```

---
