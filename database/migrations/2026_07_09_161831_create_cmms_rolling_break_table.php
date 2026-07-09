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
        Schema::create('cmms_rolling_break', function (Blueprint $table) {
            $table->id();
            $table->dateTime('date_input')->nullable();
            $table->string('shift', 50)->nullable();
            $table->string('break_time', 50)->nullable();
            $table->string('fullname', 100)->nullable();
            $table->string('jid_no', 50)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cmms_rolling_break');
    }
};
