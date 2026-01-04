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

        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->string('loan_number')->unique()->index();
            $table->foreignId('user_id')->constrained()->index();
            $table->foreignId('item_id')->constrained()->index();
            $table->timestamp('loan_date')->useCurrent();
            $table->timestamp('due_date')->index();
            $table->timestamp('return_date')->nullable();
            $table->string('returned_condition')->nullable();
            $table->string('status')->default('active')->index();
            $table->text('loan_notes')->nullable();
            $table->text('return_notes')->nullable();
            $table->foreignId('created_by')->constrained('users', 'by');
            $table->foreignId('returned_by')->nullable()->constrained('users', 'by');
            $table->foreignId('approved_by')->nullable()->constrained('users', 'by');
            $table->decimal('penalty_amount', 15, 2)->default(0);
            $table->boolean('is_paid')->default(false);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['status', 'due_date']);
            $table->index(['user_id', 'status']);
            $table->index(['item_id', 'status']);
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
