<?php

// database/migrations/2024_01_01_000004_add_inventory_fields_to_users_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('identity_number')->unique()->nullable()->after('email');
            $table->string('phone')->nullable()->after('identity_number');
            $table->string('role')->default('student')->after('phone');
            $table->string('department')->nullable()->after('role');
            $table->string('class')->nullable()->after('department');
            $table->boolean('is_suspended')->default(false)->after('class');
            $table->timestamp('suspended_until')->nullable()->after('is_suspended');
            $table->text('suspension_reason')->nullable()->after('suspended_until');
            $table->integer('max_loan_items')->default(3)->after('suspension_reason');
            $table->integer('loan_duration_days')->default(7)->after('max_loan_items');
            $table->softDeletes();

            $table->index('identity_number');
            $table->index('role');
            $table->index('department');
            $table->index('is_suspended');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['identity_number']);
            $table->dropIndex(['role']);
            $table->dropIndex(['department']);
            $table->dropIndex(['is_suspended']);

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
