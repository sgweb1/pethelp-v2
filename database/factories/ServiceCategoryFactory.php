<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ServiceCategory>
 */
class ServiceCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = [
            'Wyprowadzanie psów',
            'Opieka nad zwierzętami',
            'Petsitting',
            'Boarding',
            'Wizyta domowa',
            'Transport zwierząt',
            'Szkolenie psów',
            'Grooming'
        ];

        $name = fake()->randomElement($categories);

        return [
            'name' => $name,
            'slug' => \Illuminate\Support\Str::slug($name),
            'description' => fake()->sentence(),
            'icon' => fake()->randomElement(['🐕', '🐱', '🐾', '🏠', '🚗', '✂️']),
            'is_active' => true,
            'sort_order' => fake()->numberBetween(1, 10),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
