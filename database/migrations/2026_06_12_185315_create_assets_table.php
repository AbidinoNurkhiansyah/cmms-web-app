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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('asset_no', 50)->unique()->index();
            $table->string('line_name', 100)->nullable()->index();
            $table->string('machine_name')->nullable();
            $table->string('maker')->nullable();
            $table->integer('manufacture_year')->nullable();
            $table->string('machine_rank', 50)->nullable()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
