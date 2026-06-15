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
        Schema::create('cmms_work_order_request', function (Blueprint $table) {
            $table->id();
            $table->date('date')->index();
            $table->string('LineName', 50)->nullable();
            $table->string('MachineNo', 30)->nullable();
            $table->string('MachineName', 150)->nullable();
            $table->text('problem_description')->nullable();
            $table->string('pic', 100)->nullable();
            $table->string('status', 20)->default('Open')->index(); // Open/Progress/Closed
            $table->string('priority', 20)->default('Normal'); // Low/Normal/High/Critical
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cmms_work_order_request');
    }
};
