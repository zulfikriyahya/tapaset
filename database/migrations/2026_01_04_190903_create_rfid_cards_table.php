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
