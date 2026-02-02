<?php

namespace Database\Factories;

use App\Models\Player;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Player>
 */
class PlayerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'user_id' => null, // Not registered users
            'organization_id' => null, // Will be set when creating
            'date_of_birth' => fake()->dateTimeBetween('-50 years', '-16 years'),
            'position' => fake()->randomElement(['Forward', 'Midfielder', 'Defender', 'Goalkeeper']),
            'jersey_number' => fake()->numberBetween(1, 99),
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the player is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Set the organization for the player.
     */
    public function forOrganization($organizationId): static
    {
        return $this->state(fn (array $attributes) => [
            'organization_id' => $organizationId,
        ]);
    }
}