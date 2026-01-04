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
