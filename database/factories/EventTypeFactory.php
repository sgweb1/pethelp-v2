<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class EventTypeFactory extends Factory
{
    public function definition(): array
    {
        $name = $this->faker->unique()->randomElement([
            'Spotkania z psami',
            'Warsztaty szkoleniowe',
            'Wydarzenia adopcyjne',
            'Konkursy piękności',
            'Spacery grupowe',
            'Pikniki z pupilem',
            'Konferencje weterynarne',
            'Akcje charytatywne',
            'Rehabilitacja zwierząt',
            'Terapia z psami',
            'Koty w potrzebie',
            'Adopcje ekspresy',
            'Zwierzęce parady',
            'Konkursy fotograficzne',
            'Weekendy z pupilem',
        ]);

        return [
            'name' => $name,
            'slug' => Str::slug($name) . '-' . $this->faker->unique()->numberBetween(1000, 9999),
            'description' => $this->faker->sentence(),
            'icon' => $this->faker->randomElement(['calendar', 'users', 'heart', 'star', 'map-pin']),
            'color' => $this->faker->randomElement(['blue', 'green', 'red', 'purple', 'yellow', 'indigo']),
            'is_active' => true,
            'sort_order' => $this->faker->numberBetween(1, 100),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
