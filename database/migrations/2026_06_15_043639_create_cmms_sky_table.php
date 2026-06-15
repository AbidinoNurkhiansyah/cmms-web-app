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
        Schema::create('cmms_sky', function (Blueprint $table) {
            $table->id('no');
            $table->date('date')->nullable();
            $table->string('userId')->nullable();
            $table->string('lokasi')->nullable();
            $table->text('bahaya')->nullable();
            $table->text('countermeasure')->nullable();
            $table->string('resiko')->nullable();
            $table->string('img')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cmms_sky');
    }
};
