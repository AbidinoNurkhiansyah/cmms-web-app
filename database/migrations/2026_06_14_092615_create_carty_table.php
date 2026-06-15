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
        Schema::create('carty', function (Blueprint $table) {
            $table->id();
            $table->date('Date')->index();
            $table->string('groupline', 20)->nullable();
            $table->string('LineName', 50)->nullable()->index();
            $table->string('MachineNo', 30)->nullable();
            $table->string('MachineName', 150)->nullable();
            $table->integer('DownTime')->default(0)->comment('minutes');
            $table->text('Problem')->nullable();
            $table->text('Action')->nullable();
            $table->string('Status', 20)->default('Open')->index(); // Open/Progress/Closed/TPM
            $table->tinyInteger('Shift')->default(1);
            $table->string('PIC', 100)->nullable();
            $table->string('pic_repair', 100)->nullable();
            $table->time('start_time')->nullable();
            $table->time('finish_time')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carty');
    }
};
