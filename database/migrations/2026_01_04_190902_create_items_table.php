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
        Schema::disableForeignKeyConstraints();

        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index();
            $table->string('item_code')->unique()->index();
            $table->string('serial_number')->nullable()->index();
            $table->text('description')->nullable();
            $table->date('purchase_date')->nullable();
            $table->decimal('price', 15, 2)->nullable();
            $table->date('warranty_expired_at')->nullable();
            $table->string('condition')->default('good')->index();
            $table->string('status')->default('available')->index();
            $table->integer('quantity')->default(1);
            $table->integer('min_quantity')->nullable();
            $table->foreignId('location_id')->constrained();
            $table->foreignId('category_id')->constrained();
            $table->string('image')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['status', 'location_id']);
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
