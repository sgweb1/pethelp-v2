<?php

namespace Database\Factories;

use App\Models\{AdvertisementCategory, User};
use Illuminate\Database\Eloquent\Factories\Factory;

class AdvertisementFactory extends Factory
{
    public function definition(): array
    {
        $cities = ['Warszawa', 'Kraków', 'Gdańsk', 'Wrocław', 'Poznań'];
        $city = $this->faker->randomElement($cities);

        return [
            'user_id' => User::factory(),
            'advertisement_category_id' => AdvertisementCategory::factory(),
            'title' => $this->faker->randomElement([
                'Piękny Golden Retriever szuka domu',
                'Kot perski do adopcji',
                'Młody labrador na sprzedaż',
                'Zaginął pies rasy mieszanej',
            ]),
            'description' => $this->faker->paragraphs(2, true),
            'price' => $this->faker->optional(0.7, 0.00)->randomFloat(2, 50, 2000),
            'currency' => 'PLN',
            'price_negotiable' => $this->faker->boolean(40),
            'city' => $city,
            'voivodeship' => 'mazowieckie',
            'full_address' => $this->faker->streetAddress() . ', ' . $city,
            'latitude' => $this->faker->latitude(49, 55),
            'longitude' => $this->faker->longitude(14, 24),
            'pet_name' => $this->faker->optional(0.8)->firstName(),
            'pet_type' => $this->faker->randomElement(['dog', 'cat', 'rabbit', 'bird']),
            'pet_breed' => $this->faker->optional(0.7)->word(),
            'pet_gender' => $this->faker->randomElement(['male', 'female']),
            'pet_birth_date' => $this->faker->optional(0.6)->dateTimeBetween('-10 years', '-1 month'),
            'pet_weight' => $this->faker->optional(0.5)->randomFloat(1, 0.5, 50),
            'pet_vaccinated' => $this->faker->boolean(80),
            'pet_sterilized' => $this->faker->boolean(60),
            'pet_health_info' => $this->faker->optional(0.3)->sentence(),
            'status' => $this->faker->randomElement(['draft', 'pending', 'published', 'rejected', 'expired']),
            'is_featured' => $this->faker->boolean(10),
            'is_urgent' => $this->faker->boolean(15),
            'expires_at' => $this->faker->optional(0.6)->dateTimeBetween('now', '+3 months'),
            'contact_phone' => $this->faker->phoneNumber(),
            'contact_email' => $this->faker->email(),
            'show_phone' => $this->faker->boolean(70),
            'show_email' => $this->faker->boolean(80),
            'preferred_contact' => $this->faker->randomElement(['phone', 'email', 'both']),
            'view_count' => $this->faker->numberBetween(0, 100),
            'contact_count' => $this->faker->numberBetween(0, 20),
            'favorite_count' => $this->faker->numberBetween(0, 15),
        ];
    }

    public function published(): static
    {
        return $this->state(fn () => ['status' => 'published']);
    }

    public function featured(): static
    {
        return $this->state(fn () => ['is_featured' => true]);
    }

    public function urgent(): static
    {
        return $this->state(fn () => ['is_urgent' => true]);
    }

    public function free(): static
    {
        return $this->state(fn () => ['price' => 0.00]);
    }

    public function forDog(): static
    {
        return $this->state(fn () => ['pet_type' => 'dog']);
    }

    public function forCat(): static
    {
        return $this->state(fn () => ['pet_type' => 'cat']);
    }
}
