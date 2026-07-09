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
            $table->decimal('target_new', 5, 1)->nullable()->comment('Target overtime per day/period (new)');
            $table->decimal('target_last', 5, 1)->nullable()->comment('Target overtime per day/period (last)');
            $table->decimal('target_month_new', 6, 1)->nullable()->comment('Target overtime per month (new)');
            $table->decimal('target_month_last', 6, 1)->nullable()->comment('Target overtime per month (last)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['target_new', 'target_last', 'target_month_new', 'target_month_last']);
        });
    }
};
