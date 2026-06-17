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
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('Operator (Produksi)')->after('is_admin');
        });

        // Set all existing users to Maintenance Technician, except user ID 1 to Manager
        \Illuminate\Support\Facades\DB::table('users')->where('id', '!=', 1)->update(['role' => 'Maintenance Technician']);
        \Illuminate\Support\Facades\DB::table('users')->where('id', 1)->update(['role' => 'Manager']);

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_admin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false)->after('photo');
        });

        // Best effort to revert back
        \Illuminate\Support\Facades\DB::table('users')->where('role', 'Manager')->update(['is_admin' => true]);

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
