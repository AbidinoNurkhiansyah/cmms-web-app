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
        Schema::table('cmms_work_order_request', function (Blueprint $table) {
            $table->date('target_date')->nullable()->after('date');
            $table->string('order_type', 50)->nullable()->after('target_date'); // Install, Repair, Kaizen
            $table->string('requester', 100)->nullable()->after('order_type');
            $table->string('department', 50)->nullable()->after('requester');
            $table->string('foto_req')->nullable()->after('problem_description');
            
            $table->text('confirmation_note')->nullable()->after('status');
            $table->string('foto_confirm1')->nullable()->after('confirmation_note');
            $table->string('foto_confirm2')->nullable()->after('foto_confirm1');
            $table->date('actual_date')->nullable()->after('foto_confirm2');
            
            $table->string('pic1', 50)->nullable()->after('pic');
            $table->string('pic2', 50)->nullable()->after('pic1');
            $table->string('pic3', 50)->nullable()->after('pic2');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cmms_work_order_request', function (Blueprint $table) {
            $table->dropColumn([
                'target_date',
                'order_type',
                'requester',
                'department',
                'foto_req',
                'confirmation_note',
                'foto_confirm1',
                'foto_confirm2',
                'actual_date',
                'pic1',
                'pic2',
                'pic3',
            ]);
        });
    }
};
