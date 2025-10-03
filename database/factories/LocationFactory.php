<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Location>
 */
class LocationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'name' => fake()->randomElement(['Dom', 'Praca', 'Mieszkanie', 'Biuro']),
            'address' => fake()->streetAddress(),
            'city' => fake()->randomElement(['Warszawa', 'Kraków', 'Gdańsk', 'Wrocław', 'Poznań']),
            'postal_code' => fake()->postcode(),
            'country' => 'Polska',
            'latitude' => fake()->latitude(49, 55), // Poland coordinates range
            'longitude' => fake()->longitude(14, 24),
            'is_active' => true,
        ];
    }
}
