<?php

namespace Database\Factories;

use App\Models\Month;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Month>
 */
class MonthFactory extends Factory
{
    protected $model = Month::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'DEC' => $this->faker->numberBetween(0, 3),
            'NOV' => $this->faker->numberBetween(0, 3),
            'OCT' => $this->faker->numberBetween(0, 3),
            'SEP' => $this->faker->numberBetween(0, 3),
            'AUG' => $this->faker->numberBetween(0, 3),
            'JUL' => $this->faker->numberBetween(0, 3),
            'JUN' => $this->faker->numberBetween(0, 3),
            'MAY' => $this->faker->numberBetween(0, 3),
            'APR' => $this->faker->numberBetween(0, 3),
            'MAR' => $this->faker->numberBetween(0, 3),
            'FEB' => $this->faker->numberBetween(0, 3),
            'JAN' => $this->faker->numberBetween(0, 3),
        ];
    }
}
