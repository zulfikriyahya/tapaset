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
        Schema::disableForeignKeyConstraints();

        Schema::create('rfid_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rfid_card_id')->constrained()->index();
            $table->foreignId('user_id')->nullable()->constrained()->index();
            $table->string('action')->index();
            $table->string('status')->index();
            $table->foreignId('item_id')->nullable()->constrained();
            $table->foreignId('loan_id')->nullable()->constrained();
            $table->string('location')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->text('response_message')->nullable();
            $table->timestamps();
            $table->index(['rfid_card_id', 'created_at']);
            $table->index(['user_id', 'action', 'created_at']);
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rfid_logs');
    }
};
