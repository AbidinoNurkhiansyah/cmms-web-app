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
        Schema::create('carty_equipment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('carty_id')->constrained('carty')->cascadeOnDelete();
            $table->string('equipment_name', 150)->nullable();
            $table->string('part_number', 100)->nullable();
            $table->integer('qty')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carty_equipment');
    }
};
