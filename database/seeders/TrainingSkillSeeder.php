<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TrainingSkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = \App\Models\User::all(); // Provide dummy data to all users for testing

        $categories = [
            'OFFICE' => ["CPT", "EN", "PRT", "PSV", "LDS", "SFT", "IATF", "14001"],
            'GENBA' => ["ELE", "MEC", "MCH", "TBS", "UTL", "5S", "MTC", "CRN"],
            'ELECTRICAL' => ["Basic Electrical", "Reading Diagram", "Motor Control", "Sensor Checking"],
            'MECHANICAL' => ["Basic Mechanical", "Pneumatic System", "Hydraulic System", "Bearing Setup"],
            'ADV ELECTRICAL' => ["PLC Programming", "Servo Drive", "HMI Display", "Vision System"]
        ];

        foreach ($users as $user) {
            foreach ($categories as $category => $skills) {
                foreach ($skills as $skill) {
                    \App\Models\TrainingSkill::updateOrCreate(
                        [
                            'user_id' => $user->id,
                            'category' => $category,
                            'skill_name' => $skill,
                        ],
                        [
                            'actual_level' => rand(1, 4),
                            'target_level' => 4,
                        ]
                    );
                }
            }
        }
    }
}
