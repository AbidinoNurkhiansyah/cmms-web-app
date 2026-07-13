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
        Schema::create('cmms_work_order_spareparts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_order_id')->constrained('cmms_work_order_request')->cascadeOnDelete();
            $table->foreignId('sparepart_id')->constrained('spare_parts')->cascadeOnDelete();
            $table->integer('qty')->default(1);
            $table->string('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cmms_work_order_spareparts');
    }
};
