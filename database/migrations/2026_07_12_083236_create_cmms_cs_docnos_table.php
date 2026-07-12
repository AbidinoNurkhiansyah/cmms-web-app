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
        Schema::create('cmms_cs_docnos', function (Blueprint $table) {
            $table->id();
            $table->string('asset_no', 50)->index();
            $table->string('doc_no')->nullable();
            $table->string('item_revisi')->nullable();
            $table->text('keterangan')->nullable();
            $table->date('tanggal_revisi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cmms_cs_docnos');
    }
};
