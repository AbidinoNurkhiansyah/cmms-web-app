<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InformationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create();
        $users = \App\Models\User::pluck('id')->toArray();

        if (empty($users)) {
            $users = [\App\Models\User::factory()->create()->id];
        }

        $records = [];
        for ($i = 0; $i < 25; $i++) {
            $records[] = [
                'date' => $faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
                'user_id' => $faker->randomElement($users),
                'source' => $faker->randomElement(['HR Dept', 'Management', 'IT Support', 'Maintenance', 'External']),
                'title' => $faker->sentence(mt_rand(3, 6)),
                'file_path' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        \App\Models\Information::insert($records);
    }
}
