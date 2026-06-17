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
            $table->tinyInteger('Shift')->default(1);
            $table->string('groupline', 20)->nullable();
            $table->string('LineName', 50)->nullable()->index();
            $table->string('MachineNo', 30)->nullable();
            $table->string('MachineName', 150)->nullable();
            
            // Legacy Detailed Fields
            $table->enum('typeofproblem', ['Electrical', 'Mechanical', 'Other'])->nullable();
            $table->string('sparepartName', 100)->nullable();
            $table->integer('sparepartQty')->nullable()->default(0);
            
            $table->time('start_time')->nullable();
            $table->time('finish_time')->nullable();
            $table->integer('DownTime')->default(0)->comment('minutes');
            $table->integer('worktime')->default(0)->comment('minutes');
            $table->integer('stopline')->default(0)->comment('minutes');
            
            $table->text('Problem')->nullable();
            $table->text('Cause')->nullable();
            $table->text('Action')->nullable();
            
            $table->string('Status', 20)->default('Temporary')->index(); // Temporary/Permanent/etc
            
            // Pictures
            $table->string('filebefore1')->nullable();
            $table->string('filebefore2')->nullable();
            $table->string('fileafter1')->nullable();
            $table->string('fileafter2')->nullable();
            
            // Personnel
            $table->json('pics')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carty');
    }
};
