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
        // TPM (Deep Cleaning) before/after records
        Schema::create('cmms_tpm', function (Blueprint $table) {
            $table->id();
            $table->date('Date')->index();
            $table->string('LineName', 50)->nullable();
            $table->string('MachineNo', 30)->nullable();
            $table->string('MachineName', 150)->nullable();
            $table->string('PIC', 100)->nullable();
            $table->string('pic_jid', 30)->nullable();
            $table->string('before_photo')->nullable();
            $table->string('after_photo')->nullable();
            $table->text('description')->nullable();
            $table->string('status', 20)->default('Done');
            $table->timestamps();
        });

        // TPM Checksheet items master
        Schema::create('cmms_items_tpm', function (Blueprint $table) {
            $table->id();
            $table->string('machineName', 150)->nullable();
            $table->string('lineName', 50)->nullable();
            $table->string('itemCheck')->nullable();
            $table->string('standard')->nullable();
            $table->timestamps();
        });

        // TPM Schedule
        Schema::create('cmms_tpm_schedule', function (Blueprint $table) {
            $table->id();
            $table->date('planDate')->index();
            $table->string('NameMachine', 150)->nullable();
            $table->string('LineName', 50)->nullable();
            $table->json('items')->nullable(); // completed item names
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cmms_tpm_schedule');
        Schema::dropIfExists('cmms_items_tpm');
        Schema::dropIfExists('cmms_tpm');
    }
};
