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
        Schema::create('training_skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('category'); // ELECTRICAL, MECHANICAL, ADV ELECTRICAL, OFFICE, GENBA
            $table->string('skill_name');
            $table->integer('actual_level')->default(0);
            $table->integer('target_level')->default(4); // default target is usually 4 for these skills
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_skills');
    }
};
