<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('identity_number')->unique()->nullable()->index()->after('email');
            $table->string('phone')->nullable()->after('identity_number');
            $table->string('role')->default('student')->index()->after('phone');
            $table->string('department')->nullable()->index()->after('role');
            $table->string('class')->nullable()->after('department');
            $table->boolean('is_suspended')->default(false)->index()->after('class');
            $table->timestamp('suspended_until')->nullable()->after('is_suspended');
            $table->text('suspension_reason')->nullable()->after('suspended_until');
            $table->integer('max_loan_items')->default(3)->after('suspension_reason');
            $table->integer('loan_duration_days')->default(7)->after('max_loan_items');
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'identity_number',
                'phone',
                'role',
                'department',
                'class',
                'is_suspended',
                'suspended_until',
                'suspension_reason',
                'max_loan_items',
                'loan_duration_days',
            ]);
            $table->dropSoftDeletes();
        });
    }
};
