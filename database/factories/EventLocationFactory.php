<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventLocationFactory extends Factory
{
    public function definition(): array
    {
        $cities = [
            ['name' => 'Warszawa', 'lat' => 52.2297, 'lng' => 21.0122],
            ['name' => 'Kraków', 'lat' => 50.0647, 'lng' => 19.9450],
            ['name' => 'Gdańsk', 'lat' => 54.3520, 'lng' => 18.6466],
            ['name' => 'Wrocław', 'lat' => 51.1079, 'lng' => 17.0385],
            ['name' => 'Poznań', 'lat' => 52.4064, 'lng' => 16.9252],
        ];

        $city = $this->faker->randomElement($cities);
        $street = $this->faker->streetAddress();

        return [
            'event_id' => Event::factory(),
            'full_address' => $street . ', ' . $city['name'],
            'street' => $street,
            'city' => $city['name'],
            'postal_code' => $this->faker->regexify('[0-9]{2}-[0-9]{3}'),
            'country' => 'PL',
            'latitude' => $this->faker->latitude($city['lat'] - 0.1, $city['lat'] + 0.1),
            'longitude' => $this->faker->longitude($city['lng'] - 0.1, $city['lng'] + 0.1),
            'public_location' => $this->faker->optional(0.3)->company . ' ' . $city['name'],
            'location_notes' => $this->faker->optional(0.5)->sentence(),
        ];
    }

    public function forEvent(Event $event): static
    {
        return $this->state(fn () => ['event_id' => $event->id]);
    }
}
