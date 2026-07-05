<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SparePartRepairSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = \App\Models\User::all();
        $spareParts = \App\Models\SparePart::all();

        if ($users->isEmpty() || $spareParts->isEmpty()) {
            $this->command->warn('Tabel User atau SparePart kosong. Seeder dibatalkan.');
            return;
        }

        $usageMappings = [
            'Rack A-01' => ['Bearing 6204', 'Bearing 6205'],
            'Rack B-02' => ['O-Ring 20mm', 'Seal Kit'],
            'Rack C-03' => ['Grease / Pelumas'],
            'Rack D-04' => ['Baut L M8'],
        ];

        for ($i = 0; $i < 30; $i++) {
            $rack = fake()->randomElement(array_keys($usageMappings));
            $partUsage = fake()->randomElement($usageMappings[$rack]);

            \App\Models\SparePartRepair::create([
                'date' => fake()->dateTimeBetween('-1 year', 'now'),
                'spare_part_id' => $spareParts->random()->id,
                'qty' => fake()->numberBetween(1, 10),
                'item_repair' => fake()->sentence(3),
                'rack' => $rack,
                'pic1_id' => $users->random()->id,
                'pic2_id' => fake()->boolean(50) ? $users->random()->id : null,
                'pic3_id' => fake()->boolean(20) ? $users->random()->id : null,
                'part_usage' => $partUsage,
                'review' => fake()->optional()->sentence(),
            ]);
        }
    }
}
