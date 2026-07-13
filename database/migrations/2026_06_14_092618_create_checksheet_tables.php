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
        // Master checksheet items (template per machine)
        Schema::create('cmms_cs_items', function (Blueprint $table) {
            $table->id();
            $table->string('asset_no', 50)->nullable()->index();
            $table->string('machine_name')->nullable();
            $table->string('line_name', 100)->nullable();
            $table->string('item_check')->nullable();
            $table->string('standard')->nullable();
            $table->string('method')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Checksheet transaction (daily execution)
        Schema::create('cmms_cs_trx', function (Blueprint $table) {
            $table->id();
            $table->string('doc_no', 30)->nullable()->index();
            $table->date('date')->index();
            $table->string('asset_no', 50)->index();
            $table->string('pic_sl', 100)->nullable();    // inspector
            $table->foreignId('cs_item_id')->nullable()->constrained('cmms_cs_items')->nullOnDelete();
            $table->string('result', 100)->nullable();   // OK/NG/...
            $table->string('approval_stl', 100)->nullable();
            $table->boolean('apv_prod')->default(false);
            $table->boolean('apv_week')->default(false);
            $table->boolean('apv_month')->default(false);
            $table->string('approval_mtc', 100)->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // Checksheet photos
        Schema::create('cmms_cs_photo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cs_trx_id')->constrained('cmms_cs_trx')->cascadeOnDelete();
            $table->string('photo_path');
            $table->string('photo_type', 20)->default('evidence'); // evidence / before / after
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cmms_cs_photo');
        Schema::dropIfExists('cmms_cs_trx');
        Schema::dropIfExists('cmms_cs_items');
    }
};
