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

        Schema::create('rfid_cards', function (Blueprint $table) {
            $table->id();
            $table->string('uid')->unique()->index();
            $table->string('card_number')->nullable()->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamp('issued_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->string('last_used_for')->nullable();
            $table->integer('failed_attempts')->default(0);
            $table->foreignId('user_id')->nullable()->constrained();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rfid_cards');
    }
};
