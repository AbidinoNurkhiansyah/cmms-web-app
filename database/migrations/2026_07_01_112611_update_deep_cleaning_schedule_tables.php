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
        Schema::rename('cmms_tpm_schedule', 'cmms_deep_cleaning_schedules');
        Schema::rename('cmms_items_tpm', 'cmms_deep_cleaning_machine_items');

        Schema::table('cmms_deep_cleaning_schedules', function (Blueprint $table) {
            $table->date('act_date')->nullable()->after('planDate');
            $table->string('machine_no', 30)->nullable()->after('NameMachine');
            $table->boolean('is_approved')->default(false)->after('items');
            $table->boolean('postponed')->default(false)->after('is_approved');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cmms_deep_cleaning_schedules', function (Blueprint $table) {
            $table->dropColumn(['act_date', 'machine_no', 'is_approved', 'postponed']);
        });

        Schema::rename('cmms_deep_cleaning_schedules', 'cmms_tpm_schedule');
        Schema::rename('cmms_deep_cleaning_machine_items', 'cmms_items_tpm');
    }
};
