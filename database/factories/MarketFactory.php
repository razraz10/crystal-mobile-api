<?php

namespace Database\Factories;

use App\Models\Market;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Market>
 */
class MarketFactory extends Factory
{
    protected $model = Market::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        $updated_by_user = User::inRandomOrder()->first();
        $create_by_user = User::inRandomOrder()->first();
        // //create  a random year between 2000 and 2024 month and day
        $year = rand(2000, 2024);
        $month = rand(1, 12);
        $day = rand(1, 28);

        // Create a Carbon object with the generated date
        $date = Carbon::create($year, $month, $day);
        return [
            'id_num' => $this->faker->unique()->numberBetween(1000, 9999),
            'name_meshek' => $this->faker->unique()->company,
            'year' => $this->faker->year,
            'color_comment' => $this->faker->numberBetween(0, 3),
            'expired_agreement' => $date, ///generate for now
            'comment' => $this->faker->paragraph,
            'created_by' => $create_by_user->id,
            'updated_by' => $updated_by_user->id,
            'is_open' => $this->faker->boolean,
            'month_id' => \App\Models\Month::factory()->create()->id,
        ];
    }
}
