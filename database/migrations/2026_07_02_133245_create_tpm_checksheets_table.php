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
        Schema::create('cmms_tpm_checksheet', function (Blueprint $table) {
            $table->id();
            $table->string('type', 50)->index(); // 'GATA-GATA', 'CLAMP ARBOR', 'RUN OUT'
            $table->string('machineNo', 50)->index(); // relates to assets.asset_no
            $table->date('checked_date')->index();
            $table->string('pic')->nullable();
            $table->text('remark')->nullable();
            $table->decimal('gata_mm', 8, 2)->nullable();
            $table->decimal('clamp_kN', 8, 2)->nullable();
            $table->decimal('run_out_kelurusan', 8, 2)->nullable();
            $table->decimal('run_out_putaran', 8, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cmms_tpm_checksheet');
    }
};
