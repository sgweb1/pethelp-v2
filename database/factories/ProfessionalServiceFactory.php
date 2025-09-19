<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProfessionalService>
 */
class ProfessionalServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $businessTypes = [
            ['name' => 'Przychodnia Weterynaryjna', 'services' => ['badania ogólne', 'szczepienia', 'konsultacje', 'diagnostyka'], 'base_price' => [80, 200]],
            ['name' => 'Salon Groomerski', 'services' => ['strzyżenie', 'kąpiel', 'pielęgnacja pazurów', 'czyszczenie uszu'], 'base_price' => [50, 150]],
            ['name' => 'Trener Behawioralny', 'services' => ['szkolenie podstawowe', 'korekcja zachowania', 'socjalizacja', 'tresura'], 'base_price' => [100, 300]],
            ['name' => 'Opieka nad Zwierzętami', 'services' => ['petsitting', 'wyprowadzanie psów', 'opieka w domu', 'żłobek dla psów'], 'base_price' => [30, 100]],
            ['name' => 'Sklep Zoologiczny', 'services' => ['karma', 'akcesoria', 'zabawki', 'doradztwo'], 'base_price' => [20, 500]],
        ];

        $business = $this->faker->randomElement($businessTypes);
        $cities = ['Warszawa', 'Kraków', 'Gdańsk', 'Wrocław', 'Poznań', 'Łódź', 'Katowice'];
        $city = $this->faker->randomElement($cities);

        return [
            'user_id' => \App\Models\User::factory(),
            'advertisement_category_id' => \App\Models\AdvertisementCategory::factory(),
            'business_name' => $business['name'] . ' "' . $this->faker->firstName() . '"',
            'contact_person' => $this->faker->name(),
            'description' => $this->faker->paragraphs(2, true),
            'services_offered' => implode(',', $business['services']),
            'base_price' => $this->faker->optional(0.8)->randomFloat(2, $business['base_price'][0], $business['base_price'][1]),
            'hourly_rate' => $this->faker->optional(0.6)->randomFloat(2, 50, 200),
            'currency' => 'PLN',
            'pricing_details' => [
                'consultation' => $this->faker->randomFloat(2, 50, 150),
                'emergency_fee' => $this->faker->randomFloat(2, 100, 300),
            ],
            'availability' => [
                'monday' => ['09:00-17:00'],
                'tuesday' => ['09:00-17:00'],
                'wednesday' => ['09:00-17:00'],
                'thursday' => ['09:00-17:00'],
                'friday' => ['09:00-17:00'],
                'saturday' => ['09:00-14:00'],
                'sunday' => ['closed'],
            ],
            'city' => $city,
            'voivodeship' => 'mazowieckie',
            'full_address' => $this->faker->streetAddress() . ', ' . $city,
            'latitude' => $this->faker->latitude(49, 55),
            'longitude' => $this->faker->longitude(14, 24),
            'service_radius_km' => $this->faker->numberBetween(5, 50),
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->companyEmail(),
            'website' => $this->faker->optional(0.4)->url(),
            'social_media' => [
                'facebook' => $this->faker->optional(0.5)->url(),
                'instagram' => $this->faker->optional(0.3)->url(),
            ],
            'certifications' => $this->faker->optional(0.7)->randomElements([
                'Lekarz weterynarii',
                'Certyfikowany groomer',
                'Trener behawioralny',
                'Kurs pierwszej pomocy',
                'Specjalizacja w dermatologii',
            ], $this->faker->numberBetween(1, 3)),
            'specializations' => $this->faker->optional(0.6)->randomElements([
                'psy małych ras',
                'koty perskie',
                'zwierzęta egzotyczne',
                'senior pets',
                'szczenięta',
            ], $this->faker->numberBetween(1, 2)),
            'experience_years' => $this->faker->numberBetween(1, 25),
            'is_insured' => $this->faker->boolean(80),
            'is_licensed' => $this->faker->boolean(90),
            'status' => $this->faker->randomElement(['published', 'pending', 'draft']),
            'is_featured' => $this->faker->boolean(15),
            'accepts_online_booking' => $this->faker->boolean(60),
            'offers_emergency_services' => $this->faker->boolean(30),
            'approved_by' => null,
            'approved_at' => null,
            'rejection_reason' => null,
            'view_count' => $this->faker->numberBetween(0, 500),
            'contact_count' => $this->faker->numberBetween(0, 50),
            'average_rating' => $this->faker->randomFloat(2, 3.0, 5.0),
            'review_count' => $this->faker->numberBetween(0, 25),
        ];
    }

    public function published(): static
    {
        return $this->state(fn() => ['status' => 'published']);
    }

    public function featured(): static
    {
        return $this->state(fn() => ['is_featured' => true]);
    }

    public function withEmergencyServices(): static
    {
        return $this->state(fn() => ['offers_emergency_services' => true]);
    }

    public function withOnlineBooking(): static
    {
        return $this->state(fn() => ['accepts_online_booking' => true]);
    }

    public function veterinary(): static
    {
        return $this->state(fn() => [
            'business_name' => 'Przychodnia Weterynaryjna "' . $this->faker->firstName() . '"',
            'services_offered' => 'badania ogólne,szczepienia,konsultacje,diagnostyka',
            'base_price' => $this->faker->randomFloat(2, 80, 200),
            'is_licensed' => true,
        ]);
    }

    public function grooming(): static
    {
        return $this->state(fn() => [
            'business_name' => 'Salon Groomerski "' . $this->faker->firstName() . '"',
            'services_offered' => 'strzyżenie,kąpiel,pielęgnacja pazurów,czyszczenie uszu',
            'base_price' => $this->faker->randomFloat(2, 50, 150),
        ]);
    }
}
