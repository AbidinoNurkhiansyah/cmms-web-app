<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\SparePart;

class SparePartSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $spareparts = [
            ['part_name' => 'BEARING 6205', 'part_number' => 'BRG-6205-01', 'last_stock' => 50, 'maker' => 'SKF', 'price_idr' => 125000, 'no_rack' => 'RCK-A-01', 'machine' => 'CONVEYOR A'],
            ['part_name' => 'BEARING 6206', 'part_number' => 'BRG-6206-02', 'last_stock' => 45, 'maker' => 'SKF', 'price_idr' => 145000, 'no_rack' => 'RCK-A-02', 'machine' => 'CONVEYOR B'],
            ['part_name' => 'O-RING 12', 'part_number' => 'ORG-012-00', 'last_stock' => 200, 'maker' => 'NOK', 'price_idr' => 15000, 'no_rack' => 'RCK-B-01', 'machine' => 'HYDRAULIC PRESS'],
            ['part_name' => 'O-RING 14', 'part_number' => 'ORG-014-00', 'last_stock' => 180, 'maker' => 'NOK', 'price_idr' => 18000, 'no_rack' => 'RCK-B-02', 'machine' => 'HYDRAULIC PRESS'],
            ['part_name' => 'V-BELT A-45', 'part_number' => 'VBL-A45-01', 'last_stock' => 30, 'maker' => 'BANDO', 'price_idr' => 85000, 'no_rack' => 'RCK-C-01', 'machine' => 'MOTOR PUMP'],
            ['part_name' => 'SENSOR PROXIMITY M12', 'part_number' => 'SNR-PRX-M12', 'last_stock' => 15, 'maker' => 'OMRON', 'price_idr' => 450000, 'no_rack' => 'RCK-D-01', 'machine' => 'PACKAGING MACHINE'],
            ['part_name' => 'CONTACTOR S-N10', 'part_number' => 'CNT-SN10', 'last_stock' => 20, 'maker' => 'MITSUBISHI', 'price_idr' => 320000, 'no_rack' => 'RCK-E-01', 'machine' => 'MAIN PANEL'],
            ['part_name' => 'PNEUMATIC CYLINDER', 'part_number' => 'PNM-CYL-50', 'last_stock' => 5, 'maker' => 'SMC', 'price_idr' => 1200000, 'no_rack' => 'RCK-F-01', 'machine' => 'ASSEMBLY LINE'],
            ['part_name' => 'LUBRICANT OIL 10W40', 'part_number' => 'OIL-10W40', 'last_stock' => 100, 'maker' => 'SHELL', 'price_idr' => 65000, 'no_rack' => 'RCK-G-01', 'machine' => 'GENERAL'],
            ['part_name' => 'LIMIT SWITCH', 'part_number' => 'LMT-SW-01', 'last_stock' => 25, 'maker' => 'OMRON', 'price_idr' => 150000, 'no_rack' => 'RCK-H-01', 'machine' => 'PACKAGING MACHINE'],
        ];

        foreach ($spareparts as $item) {
            SparePart::create([
                'group' => 'MECHANICAL', // default
                'part_number' => $item['part_number'],
                'part_name' => $item['part_name'],
                'last_stock' => $item['last_stock'],
                'maker' => $item['maker'],
                'status' => 'ACTIVE',
                'price_idr' => $item['price_idr'],
                'no_rack' => $item['no_rack'],
                'machine' => $item['machine'],
            ]);
        }
    }
}
