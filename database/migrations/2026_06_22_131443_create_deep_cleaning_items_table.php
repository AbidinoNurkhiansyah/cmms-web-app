<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create the new items table
        Schema::create('cmms_deep_cleaning_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deep_cleaning_id')->constrained('cmms_deep_cleanings')->onDelete('cascade');
            $table->string('itemcheck')->nullable();
            $table->string('description')->nullable(); // Finding / problem
            $table->string('action')->nullable();
            $table->string('before_photo')->nullable();
            $table->string('after_photo')->nullable();
            $table->string('sparepart_id')->nullable();
            $table->integer('sparepart_qty')->nullable();
            $table->timestamps();
        });

        // 2. Migrate existing data from cmms_deep_cleanings to cmms_deep_cleaning_items
        $oldRecords = DB::table('cmms_deep_cleanings')
            ->whereNotNull('itemcheck')
            ->orWhereNotNull('description')
            ->orWhereNotNull('before_photo')
            ->orWhereNotNull('after_photo')
            ->get();

        foreach ($oldRecords as $record) {
            DB::table('cmms_deep_cleaning_items')->insert([
                'deep_cleaning_id' => $record->id,
                'itemcheck' => $record->itemcheck,
                'description' => $record->description,
                'action' => $record->action,
                'before_photo' => $record->before_photo,
                'after_photo' => $record->after_photo,
                'sparepart_id' => $record->sparepart_id,
                'sparepart_qty' => $record->sparepart_qty,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 3. Drop the old columns from cmms_deep_cleanings
        Schema::table('cmms_deep_cleanings', function (Blueprint $table) {
            $table->dropColumn([
                'itemcheck',
                'description',
                'action',
                'before_photo',
                'after_photo',
                'sparepart_id',
                'sparepart_qty'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Re-add columns to cmms_deep_cleanings
        Schema::table('cmms_deep_cleanings', function (Blueprint $table) {
            $table->string('itemcheck')->nullable();
            $table->string('description')->nullable();
            $table->string('action')->nullable();
            $table->string('before_photo')->nullable();
            $table->string('after_photo')->nullable();
            $table->string('sparepart_id')->nullable();
            $table->integer('sparepart_qty')->nullable();
        });

        // 2. Try to restore data back (only 1 item per deep_cleaning can be restored)
        $items = DB::table('cmms_deep_cleaning_items')
            ->orderBy('id')
            ->get()
            ->groupBy('deep_cleaning_id');

        foreach ($items as $deepCleaningId => $itemGroup) {
            $firstItem = $itemGroup->first();
            DB::table('cmms_deep_cleanings')->where('id', $deepCleaningId)->update([
                'itemcheck' => $firstItem->itemcheck,
                'description' => $firstItem->description,
                'action' => $firstItem->action,
                'before_photo' => $firstItem->before_photo,
                'after_photo' => $firstItem->after_photo,
                'sparepart_id' => $firstItem->sparepart_id,
                'sparepart_qty' => $firstItem->sparepart_qty,
            ]);
        }

        // 3. Drop the items table
        Schema::dropIfExists('cmms_deep_cleaning_items');
    }
};
