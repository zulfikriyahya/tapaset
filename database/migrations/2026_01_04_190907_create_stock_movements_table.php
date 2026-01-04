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
