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
        Schema::table('users', function (Blueprint $table) {
            $table->string('jid_no')->nullable()->unique()->after('name');
            $table->string('username')->nullable()->unique()->after('jid_no');
            $table->string('position')->nullable()->after('email');
            $table->string('team')->nullable()->after('position');
            $table->string('jobdesc')->nullable()->after('team');       // unit
            $table->string('rank')->nullable()->after('jobdesc');        // jdesc (MTC rank)
            $table->string('repair')->nullable()->after('rank');         // repair team name (MTC only)
            $table->string('status')->default('Active')->after('repair'); // Active / Not Active
            $table->string('photo')->nullable()->after('status');        // profile photo path
            $table->boolean('is_admin')->default(false)->after('photo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['jid_no', 'username', 'position', 'team', 'jobdesc', 'rank', 'repair', 'status', 'photo', 'is_admin']);
        });
    }
};
