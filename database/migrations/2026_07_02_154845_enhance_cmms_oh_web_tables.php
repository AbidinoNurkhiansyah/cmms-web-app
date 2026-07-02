<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cmms_oh_web', function (Blueprint $table) {
            $table->dateTime('start_time')->nullable()->after('date');
            $table->dateTime('end_time')->nullable()->after('start_time');
            
            $table->string('pic1', 100)->nullable()->after('PIC');
            $table->string('pic2', 100)->nullable()->after('pic1');
            $table->string('pic3', 100)->nullable()->after('pic2');
            
            $table->string('asset_no', 50)->nullable()->after('MachineName');
            $table->text('problem')->nullable()->after('description');
            
            $table->double('repair_time')->nullable()->after('end_time');
            $table->double('work_time')->nullable()->after('repair_time');
            
            $table->text('explanation')->nullable();
            $table->text('next_improvement')->nullable();
            $table->text('yokotenkai')->nullable();
            
            $table->string('photo_before_1')->nullable();
            $table->string('photo_after_1')->nullable();
            $table->string('photo_before_2')->nullable();
            $table->string('photo_after_2')->nullable();
        });

        Schema::create('cmms_oh_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('overhaul_id')->constrained('cmms_oh_web')->cascadeOnDelete();
            $table->text('step_repair')->nullable();
            $table->integer('minutes')->nullable();
            $table->text('obstacle')->nullable();
            $table->timestamps();
        });

        Schema::create('cmms_oh_spareparts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('overhaul_id')->constrained('cmms_oh_web')->cascadeOnDelete();
            $table->string('type')->nullable();
            $table->integer('qty')->nullable();
            $table->string('maker')->nullable();
            $table->string('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cmms_oh_spareparts');
        Schema::dropIfExists('cmms_oh_steps');
        
        Schema::table('cmms_oh_web', function (Blueprint $table) {
            $table->dropColumn([
                'start_time', 'end_time', 'pic1', 'pic2', 'pic3', 
                'asset_no', 'problem', 'repair_time', 'work_time',
                'explanation', 'next_improvement', 'yokotenkai',
                'photo_before_1', 'photo_after_1', 'photo_before_2', 'photo_after_2'
            ]);
        });
    }
};
