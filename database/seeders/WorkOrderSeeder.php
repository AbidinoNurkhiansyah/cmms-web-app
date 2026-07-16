<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WorkOrder;
use App\Models\Asset;
use Faker\Factory as Faker;
use Carbon\Carbon;

class WorkOrderSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('id_ID');

        // Fetch valid assets to use as references for Line and Machine
        $assets = Asset::whereNotNull('line_name')
            ->whereNotNull('asset_no')
            ->whereNotNull('machine_name')
            ->get();

        if ($assets->isEmpty()) {
            $this->command->warn('Tabel assets masih kosong atau tidak memiliki data line_name/machine_name. Tidak dapat men-generate data Work Order yang valid.');
            return;
        }

        $orderTypes = ['Preventive', 'Breakdown', 'Improvement', 'Safety'];
        $departments = ['Production', 'Engineering', 'Maintenance', 'Quality'];
        $statuses = ['Open', 'In Progress', 'Completed', 'Closed'];
        $priorities = ['Low', 'Medium', 'High', 'Urgent'];

        for ($i = 0; $i < 100; $i++) {
            $asset = $assets->random();

            $start = Carbon::create(date('Y'), 2, 1);
            $end = Carbon::create(date('Y'), 5, 31);
            $randomDate = Carbon::createFromTimestamp(rand($start->timestamp, $end->timestamp))->setTime(rand(8, 15), rand(0, 59));
            $targetDate = (clone $randomDate)->addDays(rand(1, 14));
            
            $status = $faker->randomElement($statuses);
            $isCompleted = in_array($status, ['Completed', 'Closed']);
            $actualDate = $isCompleted ? (clone $randomDate)->addDays(rand(1, 15)) : null;

            WorkOrder::create([
                'date' => $randomDate->format('Y-m-d'),
                'target_date' => $targetDate->format('Y-m-d'),
                'order_type' => $faker->randomElement($orderTypes),
                'requester' => $faker->name,
                'department' => $faker->randomElement($departments),
                'LineName' => $asset->line_name,
                'MachineNo' => $asset->asset_no,
                'MachineName' => $asset->machine_name,
                'problem_description' => $faker->sentence(8),
                'status' => $status,
                'priority' => $faker->randomElement($priorities),
                'pic' => $faker->numberBetween(1000, 9999),
                'confirmation_note' => $isCompleted ? $faker->sentence(5) : null,
                'actual_date' => $actualDate ? $actualDate->format('Y-m-d') : null,
            ]);
        }
        
        $this->command->info('100 data dummy Work Order berhasil di-generate!');
    }
}
