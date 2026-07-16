<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Overhaul;
use Faker\Factory as Faker;
use Carbon\Carbon;

class OverhaulSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('id_ID');

        // Some sample lines and machines to make it look realistic
        $lines = ['Line Assy A', 'Line Assy B', 'Line Machining C', 'Line Injection D'];
        $machines = ['Press Machine', 'CNC Lathe', 'Injection Molding', 'Conveyor Belt'];

        for ($i = 0; $i < 100; $i++) {
            $start = Carbon::create(date('Y'), 2, 1);
            $end = Carbon::create(date('Y'), 5, 31);
            $startDate = Carbon::createFromTimestamp(rand($start->timestamp, $end->timestamp))->setTime(rand(8, 15), rand(0, 59));
            $endDate = (clone $startDate)->addMinutes(rand(30, 240));

            $overhaul = Overhaul::create([
                'date' => $startDate->format('Y-m-d'),
                'start_time' => $startDate,
                'end_time' => $endDate,
                'LineName' => $faker->randomElement($lines),
                'MachineNo' => 'MC-' . $faker->numberBetween(100, 999),
                'MachineName' => $faker->randomElement($machines),
                'asset_no' => 'AST-' . $faker->bothify('??-####'),
                'problem' => $faker->sentence(6),
                'status' => 'Done',
                'repair_time' => $startDate->diffInMinutes($endDate),
                'work_time' => $startDate->diffInMinutes($endDate) + rand(10, 30), // Work time is usually slightly longer
                'PIC' => $faker->numberBetween(1000, 9999), // Assuming JID is numeric
                'pic1' => $faker->numberBetween(1000, 9999),
                'explanation' => $faker->paragraph(1),
                'next_improvement' => $faker->sentence(),
                'yokotenkai' => $faker->sentence(),
            ]);

            // Seed Steps
            $numSteps = rand(2, 5);
            for ($j = 0; $j < $numSteps; $j++) {
                $overhaul->steps()->create([
                    'step_repair' => 'Melakukan ' . $faker->word() . ' pada komponen ' . $faker->word(),
                    'minutes' => rand(10, 60),
                    'obstacle' => $faker->randomElement(['Sulit dibongkar', 'Baut karat', 'Posisi sempit', 'Tidak ada kendala']),
                ]);
            }

            // Seed Spareparts (sometimes 0)
            $numParts = rand(0, 3);
            for ($k = 0; $k < $numParts; $k++) {
                $overhaul->spareparts()->create([
                    'type' => 'Part ' . $faker->word(),
                    'qty' => rand(1, 5),
                    'maker' => $faker->randomElement(['Omron', 'SMC', 'Keyence', 'Festo', 'Mitsubishi']),
                    'remarks' => $faker->randomElement(['Baru', 'Kanibal', 'Modifikasi']),
                ]);
            }
        }
    }
}
