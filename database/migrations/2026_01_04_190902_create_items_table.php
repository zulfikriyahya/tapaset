<?php

// database/migrations/2024_01_01_000003_create_items_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
