<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MapItem>
 */
class MapItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $polishCities = [
            ['name' => 'Warszawa', 'voivodeship' => 'mazowieckie', 'lat' => 52.2297, 'lng' => 21.0122],
            ['name' => 'Kraków', 'voivodeship' => 'małopolskie', 'lat' => 50.0647, 'lng' => 19.9450],
            ['name' => 'Łódź', 'voivodeship' => 'łódzkie', 'lat' => 51.7592, 'lng' => 19.4560],
            ['name' => 'Wrocław', 'voivodeship' => 'dolnośląskie', 'lat' => 51.1079, 'lng' => 17.0385],
            ['name' => 'Poznań', 'voivodeship' => 'wielkopolskie', 'lat' => 52.4064, 'lng' => 16.9252],
            ['name' => 'Gdańsk', 'voivodeship' => 'pomorskie', 'lat' => 54.3520, 'lng' => 18.6466],
            ['name' => 'Szczecin', 'voivodeship' => 'zachodniopomorskie', 'lat' => 53.4285, 'lng' => 14.5528],
            ['name' => 'Bydgoszcz', 'voivodeship' => 'kujawsko-pomorskie', 'lat' => 53.1235, 'lng' => 18.0084],
            ['name' => 'Lublin', 'voivodeship' => 'lubelskie', 'lat' => 51.2465, 'lng' => 22.5684],
            ['name' => 'Katowice', 'voivodeship' => 'śląskie', 'lat' => 50.2649, 'lng' => 19.0238],
            ['name' => 'Białystok', 'voivodeship' => 'podlaskie', 'lat' => 53.1325, 'lng' => 23.1688],
            ['name' => 'Gdynia', 'voivodeship' => 'pomorskie', 'lat' => 54.5189, 'lng' => 18.5305],
            ['name' => 'Częstochowa', 'voivodeship' => 'śląskie', 'lat' => 50.7971, 'lng' => 19.1204],
            ['name' => 'Radom', 'voivodeship' => 'mazowieckie', 'lat' => 51.4027, 'lng' => 21.1471],
            ['name' => 'Sosnowiec', 'voivodeship' => 'śląskie', 'lat' => 50.2862, 'lng' => 19.1040],
            ['name' => 'Toruń', 'voivodeship' => 'kujawsko-pomorskie', 'lat' => 53.0138, 'lng' => 18.5984],
            ['name' => 'Kielce', 'voivodeship' => 'świętokrzyskie', 'lat' => 50.8661, 'lng' => 20.6286],
            ['name' => 'Rzeszów', 'voivodeship' => 'podkarpackie', 'lat' => 50.0412, 'lng' => 21.9991],
            ['name' => 'Gliwice', 'voivodeship' => 'śląskie', 'lat' => 50.2945, 'lng' => 18.6714],
            ['name' => 'Olsztyn', 'voivodeship' => 'warmińsko-mazurskie', 'lat' => 53.7784, 'lng' => 20.4801],
        ];

        $city = $this->faker->randomElement($polishCities);

        // Generate coordinates within city radius (±0.1 degrees)
        $latitude = $city['lat'] + $this->faker->randomFloat(4, -0.1, 0.1);
        $longitude = $city['lng'] + $this->faker->randomFloat(4, -0.1, 0.1);

        return [
            'user_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'mappable_type' => 'standalone',
            'mappable_id' => $this->faker->unique()->numberBetween(1, 10000),
            'latitude' => $latitude,
            'longitude' => $longitude,
            'city' => $city['name'],
            'voivodeship' => $city['voivodeship'],
            'full_address' => $this->faker->streetAddress() . ', ' . $city['name'],
            'title' => $this->faker->firstName() . ' - Opieka nad zwierzętami',
            'description_short' => $this->faker->sentence(10),
            'content_type' => 'pet_sitter',
            'category_name' => 'Pet Sitter',
            'category_icon' => 'dog',
            'category_color' => '#8B5CF6',
            'price_from' => $this->faker->randomFloat(2, 20, 50),
            'price_to' => $this->faker->randomFloat(2, 60, 120),
            'rating_avg' => $this->faker->randomFloat(1, 3.5, 5.0),
            'rating_count' => $this->faker->numberBetween(5, 200),
            'view_count' => $this->faker->numberBetween(50, 2000),
            'interaction_count' => $this->faker->numberBetween(10, 300),
            'status' => 'published',
            'is_featured' => $this->faker->boolean(10), // 10% chance of being featured
        ];
    }

    /**
     * Configure the model factory for pet sitters specifically
     */
    public function petSitter(): static
    {
        return $this->state(fn (array $attributes) => [
            'content_type' => 'pet_sitter',
            'category_name' => $this->faker->randomElement([
                'Pet Sitter - Psy',
                'Pet Sitter - Koty',
                'Pet Sitter - Małe zwierzęta',
                'Pet Sitter - Uniwersalny',
                'Opieka w domu opiekuna',
                'Opieka w domu właściciela',
                'Spacery z psami',
                'Overnight pet sitting',
            ]),
            'title' => $this->faker->firstName() . ' ' . $this->faker->lastName() . ' - ' . $this->faker->randomElement([
                'Profesjonalna opieka nad pupilami',
                'Kochający zwierzęta pet sitter',
                'Doświadczony opiekun zwierząt',
                'Niezawodna opieka nad Twoim pupilem',
                'Pet sitter z pasją',
                'Opiekunka zwierząt domowych',
            ]),
        ]);
    }
}
