<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call Seeders
        $this->call([
            UserSeeder::class,
        ]);

        $this->call([
            MasterAssetSeeder::class,
        ]);

        // Dummy Spare Part
        \App\Models\SparePart::updateOrCreate(
            ['part_number' => 'BRG-001'],
            [
                'part_name' => 'BEARING 6205',
                'group' => 'BEARING',
                'group_id' => 1,
                'use_qty' => 5,
                'price_idr' => 250000,
            ]
        );
        // Call Maintenance Seeder
        $this->call([
            SparePartSeeder::class,
            CartySeeder::class,
            OneHourOverSeeder::class,
            SparePartStockTakingSeeder::class,
            DeepCleaningSeeder::class,
            TpmChecksheetSeeder::class,
            DeepCleaningScheduleSeeder::class,
            SparePartRepairSeeder::class,
        ]);
    }
}
