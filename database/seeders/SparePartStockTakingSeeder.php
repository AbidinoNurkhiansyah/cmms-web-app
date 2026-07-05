<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SparePartStockTaking;
use App\Models\SparePart;
use Carbon\Carbon;

class SparePartStockTakingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $spareParts = SparePart::all();
        
        if ($spareParts->isEmpty()) {
            return;
        }

        $dates = [
            Carbon::today(),
            Carbon::yesterday(),
            Carbon::now()->subDays(2),
            Carbon::now()->subDays(3),
            Carbon::now()->subDays(4),
        ];

        foreach ($dates as $date) {
            $amountToSeed = min(rand(5, 10), $spareParts->count());
            $dailyParts = $spareParts->random($amountToSeed);
            
            foreach ($dailyParts as $part) {
                // Decide if it's OK or NG
                // Mostly OK (90%)
                $isOk = rand(1, 100) <= 90;
                
                $lastStock = rand(10, 100);
                $checkStock = $isOk ? $lastStock : $lastStock - rand(1, 5); // If NG, stock differs
                
                // For Not Counted, we just don't create a record. So the total in system vs checked will be handled by the UI logic (if we know total parts).
                // Wait, the legacy system calculates Not Counted as `Total Parts - OK - NG`.
                // Actually `COUNT(NoIDPartNo)` is `totalStock` (total checked parts).
                // Wait! In legacy: `totalStock` = `COUNT(NoIDPartNo)` which means total CHECKED!
                // `ng = laststock<>checkstock`. `okCount = laststock=checkstock`.
                // `totalNg = totalStock - okCount - ng`.
                // But wait! If `totalStock` is just `COUNT(NoIDPartNo)` and each row is a record where `last_stock` and `check_stock` are compared, `totalNg` (Not Counted) is always 0 if all rows have `last_stock` and `check_stock` filled!
                // Let's just create records.
                
                SparePartStockTaking::create([
                    'date_stock' => $date->format('Y-m-d'),
                    'spare_part_id' => $part->id,
                    'in_qty' => rand(0, 10),
                    'out_qty' => rand(0, 10),
                    'last_stock' => $lastStock,
                    'check_stock' => $checkStock,
                    'remark' => $isOk ? null : 'Stock mismatch',
                ]);
            }
        }
    }
}
