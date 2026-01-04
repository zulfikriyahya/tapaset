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
