<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RollingBreakSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = \App\Models\User::whereNotNull('jid_no')->get();
        if ($users->isEmpty()) {
            return;
        }

        $faker = \Faker\Factory::create('id_ID');

        for ($i = 0; $i < 25; $i++) {
            $user = $users->random();
            $shift = $faker->randomElement(['1', '2']);
            $breakTime = $shift == '1' 
                ? $faker->randomElement(['10:00', '11:35', '12:00', '15:30', '18:00'])
                : $faker->randomElement(['22:00', '23:30', '23:45', '02:50', '03:05']);
            
            \App\Models\RollingBreak::create([
                'date_input' => $faker->dateTimeBetween('-1 month', 'now'),
                'shift' => $shift,
                'break_time' => $breakTime,
                'fullname' => $user->name,
                'jid_no' => $user->jid_no,
                'notes' => $faker->sentence(),
            ]);
        }
    }
}
