<?php

namespace Database\Factories;

use App\Models\Permission;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        ///get random permission.
        $randomPermission = Permission::inRandomOrder()->first();

        $faker = \Faker\Factory::create('he_IL');


        $pn = $faker->unique()->regexify('[scm]\d{7}');
        return [
            'name' => fake()->name(),
            'email' => "$pn@army.idf.il",
            'phone_number' => $faker->unique()->regexify('05\d{8}'),
            'personal_number' => $pn,
            ///choose emp randomaly.
            'employee_type' => $this->faker->numberBetween(1, 4),
            // 'employee_type' => $faker->randomElement(['sadir', 'keva', 'miluim', 'oved_tzahal']),
            'permission_id' => $randomPermission->id,
            // 'role' => $faker->randomElement(['sgan_aluf', 'head_department', 'first_sergeant', 'base_commander', 'sadir', 'keva', 'oved_tzahal', 'miluim']),

            // 'email_verified_at' => now(),
            // 'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
