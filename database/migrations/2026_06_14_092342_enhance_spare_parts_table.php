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
        Schema::table('spare_parts', function (Blueprint $table) {
            $table->string('part_name')->nullable()->after('part_number');
            $table->string('no_rack', 50)->nullable()->after('part_name');   // rack location
            $table->integer('last_stock')->default(0)->after('no_rack');     // current stock
            $table->string('maker', 100)->nullable()->after('last_stock');
            $table->string('machine')->nullable()->after('maker');            // associated machine(s)
            $table->string('status', 10)->default('Y')->after('machine');    // Y = active, N = discontinued
            $table->string('part_photo')->nullable()->after('status');        // image path
            $table->integer('repair_stock')->default(0)->after('part_photo');
            $table->string('repair_rack', 50)->nullable()->after('repair_stock');
            $table->string('manual_doc')->nullable()->after('repair_rack'); // PDF path
        });
    }

    public function down(): void
    {
        Schema::table('spare_parts', function (Blueprint $table) {
            $table->dropColumn(['part_name','no_rack','last_stock','maker','machine','status','part_photo','repair_stock','repair_rack','manual_doc']);
        });
    }
};
