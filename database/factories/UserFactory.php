<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $fn = fake()->firstName();
        $ln = fake()->lastName();
        return [
            'first_name' => $fn,
            'last_name' => $ln,
            'email' => $fn.fake()->unique()->numberBetween(0,200)."@gmail.com",
            'phone' => fake()->unique()->numerify('##########'),
            'password' => fake()->unique()->password(),
            // 'account_name' => $fn." ".$ln,
            // 'account_number' => fake()->unique()->numerify('##########'),
            'email_verified_at' => now(),
            'email_verification_code' => Str::random(40),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return static
     */
    public function unverified()
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
