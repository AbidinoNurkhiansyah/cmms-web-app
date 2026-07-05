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
        Schema::create('spare_part_stock_takings', function (Blueprint $table) {
            $table->id();
            $table->date('date_stock')->index();
            $table->foreignId('spare_part_id')->constrained('spare_parts')->cascadeOnDelete();
            
            $table->integer('in_qty')->default(0);
            $table->integer('out_qty')->default(0);
            
            $table->integer('last_stock')->default(0)->comment('System stock');
            $table->integer('check_stock')->default(0)->comment('Physical stock');
            
            $table->string('remark')->nullable();
            
            $table->timestamps();
            
            // Add index for fast querying by status
            $table->index(['date_stock', 'spare_part_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spare_part_stock_takings');
    }
};
