<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\ServiceCategory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sitter_id' => User::factory(),
            'category_id' => ServiceCategory::factory(),
            'title' => fake()->randomElement([
                'Opieka nad psami',
                'Wyprowadzanie psów',
                'Pet sitting',
                'Wizyta domowa',
                'Opieka nad kotami'
            ]),
            'description' => fake()->paragraph(),
            'price_per_hour' => fake()->randomFloat(2, 15, 80),
            'price_per_day' => fake()->randomFloat(2, 80, 300),
            'pet_types' => fake()->randomElement([
                ['psy'],
                ['koty'],
                ['psy', 'koty'],
                ['gryzonie'],
                ['ptaki']
            ]),
            'pet_sizes' => fake()->randomElement([
                ['małe'],
                ['średnie'],
                ['duże'],
                ['małe', 'średnie'],
                ['średnie', 'duże']
            ]),
            'home_service' => fake()->boolean(),
            'sitter_home' => fake()->boolean(),
            'max_pets' => fake()->numberBetween(1, 5),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function withSitter($sitterId): static
    {
        return $this->state(fn (array $attributes) => [
            'sitter_id' => $sitterId,
        ]);
    }

    public function withCategory($categoryId): static
    {
        return $this->state(fn (array $attributes) => [
            'category_id' => $categoryId,
        ]);
    }
}
