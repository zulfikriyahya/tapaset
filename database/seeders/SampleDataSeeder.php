<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Category;
use App\Models\Location;
use App\Enums\ItemStatus;
use App\Enums\ItemCondition;
use Illuminate\Database\Seeder;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        // Locations
        $locations = [
            ['name' => 'Lab Komputer 1', 'code' => 'LAB-KOMP-1', 'is_active' => true],
            ['name' => 'Perpustakaan', 'code' => 'PERP', 'is_active' => true],
            ['name' => 'Ruang Guru', 'code' => 'RG-01', 'is_active' => true],
            ['name' => 'Lab Fisika', 'code' => 'LAB-FIS', 'is_active' => true],
        ];

        foreach ($locations as $location) {
            Location::create($location);
        }

        // Categories
        $categories = [
            [
                'name' => 'Komputer & Laptop',
                'slug' => 'komputer-laptop',
                'is_consumable' => false,
            ],
            [
                'name' => 'Buku Pelajaran',
                'slug' => 'buku-pelajaran',
                'is_consumable' => false,
            ],
            [
                'name' => 'Alat Tulis',
                'slug' => 'alat-tulis',
                'is_consumable' => true,
                'min_stock' => 10,
            ],
            [
                'name' => 'Peralatan Lab',
                'slug' => 'peralatan-lab',
                'is_consumable' => false,
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        // Sample Items
        $items = [
            [
                'name' => 'Laptop Dell Latitude 5420',
                'item_code' => 'LAP-001',
                'serial_number' => 'DL5420-2023-001',
                'category_id' => 1,
                'location_id' => 1,
                'status' => ItemStatus::AVAILABLE,
                'condition' => ItemCondition::GOOD,
                'quantity' => 1,
                'purchase_date' => now()->subMonths(6),
                'price' => 8500000,
            ],
            [
                'name' => 'Buku Matematika Kelas XII',
                'item_code' => 'BK-MTK-001',
                'category_id' => 2,
                'location_id' => 2,
                'status' => ItemStatus::AVAILABLE,
                'condition' => ItemCondition::GOOD,
                'quantity' => 25,
                'min_quantity' => 5,
            ],
            [
                'name' => 'Mikroskop Digital',
                'item_code' => 'LAB-MKR-001',
                'serial_number' => 'MKR-2023-001',
                'category_id' => 4,
                'location_id' => 4,
                'status' => ItemStatus::AVAILABLE,
                'condition' => ItemCondition::GOOD,
                'quantity' => 1,
                'purchase_date' => now()->subYears(1),
                'price' => 4500000,
            ],
        ];

        foreach ($items as $item) {
            Item::create($item);
        }
    }
}
