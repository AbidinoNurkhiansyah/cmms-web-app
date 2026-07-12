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
        Schema::table('cmms_cs_items', function (Blueprint $table) {
            $table->string('periode', 20)->nullable()->after('method');
            $table->string('photo_path')->nullable()->after('periode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cmms_cs_items', function (Blueprint $table) {
            $table->dropColumn(['periode', 'photo_path']);
        });
    }
};
