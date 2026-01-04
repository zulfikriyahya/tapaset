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
