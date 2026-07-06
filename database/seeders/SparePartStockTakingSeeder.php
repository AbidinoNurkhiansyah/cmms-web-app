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
