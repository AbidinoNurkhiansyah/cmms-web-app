<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SparePart;
use App\Models\MachineSparePart;

class MachineSparePartSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first spare part to add some dummy relations
        $sparePart = SparePart::first();

        if ($sparePart) {
            MachineSparePart::firstOrCreate([
                'spare_part_id' => $sparePart->id,
                'line' => 'LINE 1',
                'asset_no' => 'AST-001',
                'machine' => 'CONVEYOR A'
            ]);

            MachineSparePart::firstOrCreate([
                'spare_part_id' => $sparePart->id,
                'line' => 'LINE 1',
                'asset_no' => 'AST-002',
                'machine' => 'CONVEYOR B'
            ]);

            MachineSparePart::firstOrCreate([
                'spare_part_id' => $sparePart->id,
                'line' => 'LINE 2',
                'asset_no' => 'AST-005',
                'machine' => 'PACKAGING MACHINE'
            ]);
        }
    }
}
