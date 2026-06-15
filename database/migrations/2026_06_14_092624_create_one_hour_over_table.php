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
        Schema::create('one_hour_over', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable()->index();
            $table->string('group_name', 50)->nullable();
            $table->string('line', 50)->nullable()->index();
            $table->string('machine', 100)->nullable();
            $table->text('problem')->nullable();
            $table->string('file_rsa', 200)->nullable();
            $table->string('file_rca', 200)->nullable();
            $table->string('status', 20)->default('Open')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('one_hour_over');
    }
};
