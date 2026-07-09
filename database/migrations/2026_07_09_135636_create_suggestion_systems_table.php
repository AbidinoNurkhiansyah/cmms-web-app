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
        Schema::create('suggestion_systems', function (Blueprint $table) {
            $table->id();
            $table->date('tgl');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('ss_title');
            $table->integer('score')->default(0);
            $table->timestamps();

            $table->index(['tgl', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suggestion_systems');
    }
};
