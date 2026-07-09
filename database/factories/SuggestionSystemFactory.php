<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\SuggestionSystem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SuggestionSystem>
 */
class SuggestionSystemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tgl' => $this->faker->dateTimeBetween('-2 months', '+1 month')->format('Y-m-d'),
            'user_id' => User::inRandomOrder()->first()->id ?? User::factory(),
            'ss_title' => $this->faker->sentence(4),
            'score' => $this->faker->numberBetween(50, 100),
        ];
    }
}
