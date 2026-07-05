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
        Schema::create('spare_part_repairs', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            
            // Replaced NoIDPartNo with spare_part_id
            $table->foreignId('spare_part_id')->nullable()->constrained('spare_parts')->nullOnDelete();
            
            $table->integer('qty')->default(0);
            $table->string('item_repair')->nullable();
            $table->string('rack')->nullable();
            
            // User references for PIC
            $table->foreignId('pic1_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('pic2_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('pic3_id')->nullable()->constrained('users')->nullOnDelete();
            
            $table->string('file_before')->nullable();
            $table->string('file_after')->nullable();
            $table->string('part_usage')->nullable();
            $table->string('review')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spare_part_repairs');
    }
};
