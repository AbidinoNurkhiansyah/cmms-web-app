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
        Schema::rename('cmms_tpm', 'cmms_deep_cleanings');

        Schema::table('cmms_deep_cleanings', function (Blueprint $table) {
            $table->dropColumn(['PIC', 'pic_jid']);
            $table->json('pics')->nullable();
            $table->string('itemcheck')->nullable();
            $table->string('action')->nullable();
            $table->string('sparepart_id')->nullable();
            $table->integer('sparepart_qty')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cmms_deep_cleanings', function (Blueprint $table) {
            $table->dropColumn(['pics', 'itemcheck', 'action', 'sparepart_id', 'sparepart_qty']);
            $table->string('PIC', 100)->nullable();
            $table->string('pic_jid', 30)->nullable();
        });

        Schema::rename('cmms_deep_cleanings', 'cmms_tpm');
    }
};
