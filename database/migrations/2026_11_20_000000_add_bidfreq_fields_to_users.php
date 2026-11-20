<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('name', 'first_name');
            $table->string('last_name')->default('');
            $table->string('phone', 40)->nullable();
            $table->string('company_name')->nullable();
            $table->enum('role', ['super_admin', 'admin', 'customer'])->default('customer');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamp('last_login_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn([
            'last_name', 'phone', 'company_name', 'role', 'status', 'last_login_at',
        ]));
        Schema::table('users', fn (Blueprint $table) => $table->renameColumn('first_name', 'name'));
    }
};
