<?php

namespace Database\Factories;

use App\Models\Year;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Year>
 */
class YearFactory extends Factory
{
    protected $model = Year::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'year_num' => $this->faker->year,
            'plan_week' => $this->faker->numberBetween(1, 52),
            'cumulative' => $this->faker->numberBetween(1000, 50000),
        ];
    }
}
