<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Mission>
 */
class MissionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $updated_by_user = User::inRandomOrder()->first();
        $create_by_user = User::inRandomOrder()->first();
        return [
            'platform' => $this->faker->word,
            'comment' => $this->faker->sentence,
            'color_comment' => $this->faker->numberBetween(0, 3),
            'month' => $this->faker->month,
            'cumulative_per_month' => $this->faker->numberBetween(100, 1000),
            'plan_week_per_month' => $this->faker->numberBetween(1, 4),
            'year' => $this->faker->year,
            'plan_week_per_year' => $this->faker->numberBetween(1, 52),
            'cumulative_per_year' => $this->faker->numberBetween(1000, 50000),
            'created_by' => $create_by_user->id,
            'updated_by' => $updated_by_user->id,
        ];
    }
}
