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
        // 1. Create the new spareparts table
        Schema::create('cmms_deep_cleaning_spareparts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deep_cleaning_id')->constrained('cmms_deep_cleanings')->onDelete('cascade');
            $table->string('sparepart_id');
            $table->integer('qty')->default(1);
            $table->string('itemcheck')->nullable(); // Optional reference to which check it was for
            $table->timestamps();
        });

        // 2. Migrate existing sparepart data from items to the new table
        $itemsWithSpareparts = DB::table('cmms_deep_cleaning_items')
            ->whereNotNull('sparepart_id')
            ->where('sparepart_id', '!=', '')
            ->get();

        foreach ($itemsWithSpareparts as $item) {
            DB::table('cmms_deep_cleaning_spareparts')->insert([
                'deep_cleaning_id' => $item->deep_cleaning_id,
                'sparepart_id' => $item->sparepart_id,
                'qty' => $item->sparepart_qty ?: 1,
                'itemcheck' => $item->itemcheck,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 3. Drop columns from items
        Schema::table('cmms_deep_cleaning_items', function (Blueprint $table) {
            $table->dropColumn(['sparepart_id', 'sparepart_qty']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-add columns
        Schema::table('cmms_deep_cleaning_items', function (Blueprint $table) {
            $table->string('sparepart_id')->nullable();
            $table->integer('sparepart_qty')->nullable();
        });

        // Try to restore data (will only be able to map roughly)
        $spareparts = DB::table('cmms_deep_cleaning_spareparts')->get();
        foreach ($spareparts as $sp) {
            DB::table('cmms_deep_cleaning_items')
                ->where('deep_cleaning_id', $sp->deep_cleaning_id)
                ->where('itemcheck', $sp->itemcheck)
                ->update([
                    'sparepart_id' => $sp->sparepart_id,
                    'sparepart_qty' => $sp->qty
                ]);
        }

        Schema::dropIfExists('cmms_deep_cleaning_spareparts');
    }
};
