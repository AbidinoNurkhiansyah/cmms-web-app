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

        \App\Models\Asset::updateOrCreate(
            ['asset_no' => '13XQID014'],
            ['line_name' => '1ST GRD', 'machine_name' => 'COOLANT SYSTEM', 'maker' => 'CNK.CO.LTD', 'manufacture_year' => 2013, 'machine_rank' => 'D']
        );

        \App\Models\Asset::updateOrCreate(
            ['asset_no' => '11GEID001'],
            ['line_name' => 'HT', 'machine_name' => 'HEAT TREATMENT', 'maker' => 'KOYO', 'manufacture_year' => 2011, 'machine_rank' => 'B']
        );

        \App\Models\Asset::updateOrCreate(
            ['asset_no' => '12HSID018'],
            ['line_name' => 'BRG DAC', 'machine_name' => 'OR SUPER FINISH MACHINE 1', 'maker' => 'TOYOTA', 'manufacture_year' => 2015, 'machine_rank' => 'A']
        );

        \App\Models\Asset::updateOrCreate(
            ['asset_no' => '11ORID002'],
            ['line_name' => 'STC 2', 'machine_name' => 'BROACHING 2', 'maker' => 'HONDA', 'manufacture_year' => 2018, 'machine_rank' => 'A']
        );

        \App\Models\Asset::updateOrCreate(
            ['asset_no' => '11OMID003'],
            ['line_name' => 'EPS 1', 'machine_name' => 'MACHINING CENTER OP20.1', 'maker' => 'MAZAK', 'manufacture_year' => 2019, 'machine_rank' => 'C']
        );

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
        ]);
    }
}
