<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AdvertisementCategoryFactory extends Factory
{
    public function definition(): array
    {
        $categories = [
            ['name' => 'Psy na adopcję', 'type' => 'adoption'],
            ['name' => 'Koty na adopcję', 'type' => 'adoption'],
            ['name' => 'Sprzedaż psów', 'type' => 'sales'],
            ['name' => 'Sprzedaż kotów', 'type' => 'sales'],
            ['name' => 'Zaginione psy', 'type' => 'lost_found'],
            ['name' => 'Znalezione koty', 'type' => 'lost_found'],
            ['name' => 'Karma i przysmaki', 'type' => 'supplies'],
            ['name' => 'Akcesoria dla psów', 'type' => 'supplies'],
            ['name' => 'Zabawki', 'type' => 'supplies'],
        ];

        $category = $this->faker->randomElement($categories);

        return [
            'parent_id' => null,
            'name' => $category['name'],
            'slug' => Str::slug($category['name']) . '-' . $this->faker->unique()->numberBetween(1000, 9999),
            'description' => $this->faker->sentence(),
            'icon' => $this->faker->randomElement(['heart', 'currency-dollar', 'exclamation-triangle', 'shopping-bag', 'star']),
            'color' => $this->faker->randomElement(['red', 'blue', 'green', 'purple', 'yellow', 'orange']),
            'type' => $category['type'],
            'is_active' => true,
            'sort_order' => $this->faker->numberBetween(1, 100),
            'requires_approval' => $this->faker->boolean(30),
            'allows_images' => true,
            'max_images' => $this->faker->numberBetween(3, 10),
            'advertisement_count' => 0,
        ];
    }

    public function adoption(): static
    {
        return $this->state(fn () => ['type' => 'adoption']);
    }

    public function sales(): static
    {
        return $this->state(fn () => ['type' => 'sales']);
    }

    public function supplies(): static
    {
        return $this->state(fn () => ['type' => 'supplies']);
    }

    public function lostFound(): static
    {
        return $this->state(fn () => ['type' => 'lost_found']);
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
