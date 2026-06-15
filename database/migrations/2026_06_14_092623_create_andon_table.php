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
        Schema::create('mtc_call', function (Blueprint $table) {
            $table->id();
            $table->date('date_shift')->nullable()->index();
            $table->date('date_in')->nullable()->index();
            $table->dateTime('time_in')->nullable();
            $table->string('line_name', 20)->nullable()->index();
            $table->string('machine', 50)->nullable();
            $table->string('shift', 2)->nullable();
            $table->string('status', 25)->default('CALL')->index();
            $table->text('stop_info');
            $table->time('stop_time')->nullable();
            $table->string('call_code', 20)->nullable();
            $table->dateTime('finish_time')->nullable();
            $table->string('name_pic', 80)->nullable();
            $table->string('remark', 200)->nullable();
            $table->boolean('mechanic')->default(false);
            $table->boolean('electric')->default(false);
            $table->smallInteger('w_total')->default(0);
            $table->smallInteger('w_stop')->default(0);
            $table->float('man_hours')->default(0);
            $table->string('cause_actual', 100)->nullable();
            $table->string('preventive', 100)->nullable();
            $table->string('hasil_repair', 150)->nullable();
            $table->timestamp('update_time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mtc_call');
    }
};
