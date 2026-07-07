<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DeepCleaningSchedule;
use App\Models\DeepCleaningMachineItem;
use App\Models\Asset;
use Carbon\Carbon;
use Faker\Factory as Faker;

class DeepCleaningScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        $assets = Asset::whereNotNull('line_name')
            ->whereNotNull('asset_no')
            ->whereNotNull('machine_name')
            ->get();

        if ($assets->isEmpty()) {
            return;
        }

        // Buat Dummy Master Data Machine Items
        $dummyItems = [
            ['itemCheck' => 'Pembersihan filter udara mesin utama.', 'standard' => 'Filter bersih dari debu dan kotoran, aliran udara lancar.'],
            ['itemCheck' => 'Pengecekan dan pembersihan jalur oli pelumas.', 'standard' => 'Jalur oli tidak tersumbat, aliran oli normal.'],
            ['itemCheck' => 'Penggantian seal karet dan deep cleaning area putaran rotor.', 'standard' => 'Seal baru terpasang sempurna, area putaran bersih.'],
            ['itemCheck' => 'Pembersihan sensor optic pada conveyor.', 'standard' => 'Sensor bersih, pembacaan akurat tanpa delay.'],
            ['itemCheck' => 'Scrubbing area panel kontrol dan vacuum debu.', 'standard' => 'Panel kontrol bebas debu, tombol dan layar responsif.'],
        ];

        foreach ($assets->random(min(10, $assets->count())) as $asset) {
            $randomKeys = array_rand($dummyItems, 3);
            foreach ($randomKeys as $index) {
                DeepCleaningMachineItem::firstOrCreate([
                    'machineName' => $asset->machine_name,
                    'lineName' => $asset->line_name,
                    'itemCheck' => $dummyItems[$index]['itemCheck'],
                ], [
                    'standard' => $dummyItems[$index]['standard'],
                ]);
            }
        }

        // Buat 15 Schedule dummy untuk bulan ini dan bulan depan
        for ($i = 0; $i < 15; $i++) {
            $asset = $assets->random();
            // planDate antara bulan ini atau bulan depan
            $monthOffset = $faker->boolean(70) ? 0 : 1; 
            $planDate = Carbon::now()->addMonths($monthOffset)->startOfMonth()->addDays(rand(0, 27));
            $isExecuted = $monthOffset === 0 && $faker->boolean(50);
            
            $actDate = $isExecuted ? $planDate->copy()->addDays(rand(0, 2)) : null;
            
            // Ambil master items untuk mesin ini, jika belum ada buat dummy on the fly
            $machineItems = DeepCleaningMachineItem::where('machineName', $asset->machine_name)
                ->where('lineName', $asset->line_name)
                ->get();
            
            if ($machineItems->isEmpty()) {
                $randomKeys = array_rand($dummyItems, 3);
                foreach ($randomKeys as $index) {
                    $machineItems->push(DeepCleaningMachineItem::create([
                        'machineName' => $asset->machine_name,
                        'lineName' => $asset->line_name,
                        'itemCheck' => $dummyItems[$index]['itemCheck'],
                        'standard' => $dummyItems[$index]['standard'],
                    ]));
                }
            }
                
            $items = [];
            foreach ($machineItems as $item) {
                $items[] = [
                    'id' => $item->id,
                    'itemCheck' => $item->itemCheck,
                    'standard' => $item->standard,
                    'status' => $isExecuted ? $faker->randomElement(['OK', 'NG', 'OK']) : null, // Lebih sering OK
                ];
            }

            DeepCleaningSchedule::create([
                'planDate' => $planDate,
                'act_date' => $actDate,
                'NameMachine' => $asset->machine_name,
                'machine_no' => $asset->asset_no,
                'LineName' => $asset->line_name,
                'items' => $items,
                'is_approved' => $isExecuted ? true : false,
                'postponed' => $faker->boolean(10),
            ]);
        }
    }
}
