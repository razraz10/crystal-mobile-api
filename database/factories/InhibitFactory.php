<?php

namespace Database\Factories;

use App\Models\Month;
use App\Models\User;
use App\Models\Year;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Inhibit>
 */
class InhibitFactory extends Factory
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
            'inhibit_ta' => $this->faker->text(),
            'inhibit_mrahs' => $this->faker->text(),
            'activ_required' => $this->faker->text(),
            'impacted_tasks' => $this->faker->text(),
            'comment' => $this->faker->paragraph(),
            'color_comment' => $this->faker->numberBetween(0, 3),
            'year' => $this->faker->year,
            'month' => $this->faker->month,
            'created_by' => $create_by_user->id,
            'updated_by' => $updated_by_user->id,

        ];
    }
}
