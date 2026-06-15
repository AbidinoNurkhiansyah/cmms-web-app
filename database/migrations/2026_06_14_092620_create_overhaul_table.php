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
        Schema::create('cmms_oh_web', function (Blueprint $table) {
            $table->id();
            $table->date('date')->index();
            $table->string('LineName', 50)->nullable();
            $table->string('MachineNo', 30)->nullable();
            $table->string('MachineName', 150)->nullable();
            $table->string('PIC', 100)->nullable();
            $table->text('description')->nullable();
            $table->string('status', 30)->default('Open'); // Open/Progress/Done
            $table->date('start_date')->nullable();
            $table->date('finish_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cmms_oh_web');
    }
};
