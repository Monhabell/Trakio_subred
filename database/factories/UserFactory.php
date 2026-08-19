<?php

namespace Database\Factories;

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
        return [
            'name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'environment_id' => \App\Models\Entorno::all()->random()->id,
            'role_id' => \App\Models\Role::all()->random()->id,
            'email_verified_at' => $this->faker->boolean ? now() : null,
            'password' => bcrypt('12345678'),
            'is_active' => $this->faker->boolean(50),
            'is_admin' => $this->faker->boolean(50),
            'terms_accepted' => $this->faker->boolean(40),
            'remember_token' => Str::random(10),
            'subnet_id' => \App\Models\Subnets::all()->random()->id
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