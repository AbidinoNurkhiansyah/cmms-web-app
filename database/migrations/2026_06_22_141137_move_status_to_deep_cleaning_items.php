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
        Schema::table('cmms_deep_cleaning_items', function (Blueprint $table) {
            $table->string('status')->nullable()->default('Undone')->after('action');
        });

        Schema::table('cmms_deep_cleanings', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cmms_deep_cleanings', function (Blueprint $table) {
            $table->string('status')->nullable()->default('Scheduled');
        });

        Schema::table('cmms_deep_cleaning_items', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
