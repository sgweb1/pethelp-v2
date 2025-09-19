<?php

use App\Models\{ProfessionalService, AdvertisementCategory, User};
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->category = AdvertisementCategory::factory()->create();
});

test('can create professional service with factory', function () {
    $service = ProfessionalService::factory()->create();

    expect($service)->toBeInstanceOf(ProfessionalService::class);
    expect($service->business_name)->toBeString();
    expect($service->user_id)->toBeInt();
    expect($service->advertisement_category_id)->toBeInt();
});

test('professional service belongs to user', function () {
    $service = ProfessionalService::factory()->for($this->user)->create();

    expect($service->user)->toBeInstanceOf(User::class);
    expect($service->user->id)->toBe($this->user->id);
});

test('professional service belongs to category', function () {
    $service = ProfessionalService::factory()->for($this->category, 'advertisementCategory')->create();

    expect($service->advertisementCategory)->toBeInstanceOf(AdvertisementCategory::class);
    expect($service->advertisementCategory->id)->toBe($this->category->id);
});

test('published scope returns only published services', function () {
    ProfessionalService::factory()->create(['status' => 'published']);
    ProfessionalService::factory()->create(['status' => 'pending']);
    ProfessionalService::factory()->create(['status' => 'draft']);

    $publishedServices = ProfessionalService::published()->get();

    expect($publishedServices)->toHaveCount(1);
    expect($publishedServices->first()->status)->toBe('published');
});

test('in city scope filters by city', function () {
    ProfessionalService::factory()->create(['city' => 'Warszawa']);
    ProfessionalService::factory()->create(['city' => 'Kraków']);

    $warsawServices = ProfessionalService::inCity('Warszawa')->get();

    expect($warsawServices)->toHaveCount(1);
    expect($warsawServices->first()->city)->toBe('Warszawa');
});

test('with rating scope filters by minimum rating', function () {
    ProfessionalService::factory()->create(['average_rating' => 4.5]);
    ProfessionalService::factory()->create(['average_rating' => 3.2]);
    ProfessionalService::factory()->create(['average_rating' => 4.8]);

    $highRatedServices = ProfessionalService::withRating(4.0)->get();

    expect($highRatedServices)->toHaveCount(2);
    $highRatedServices->each(function ($service) {
        expect($service->average_rating)->toBeGreaterThanOrEqual(4.0);
    });
});

test('featured scope returns only featured services', function () {
    ProfessionalService::factory()->create(['is_featured' => true]);
    ProfessionalService::factory()->create(['is_featured' => false]);

    $featuredServices = ProfessionalService::featured()->get();

    expect($featuredServices)->toHaveCount(1);
    expect($featuredServices->first()->is_featured)->toBeTrue();
});

test('with emergency services scope returns correct services', function () {
    ProfessionalService::factory()->create(['offers_emergency_services' => true]);
    ProfessionalService::factory()->create(['offers_emergency_services' => false]);

    $emergencyServices = ProfessionalService::withEmergencyServices()->get();

    expect($emergencyServices)->toHaveCount(1);
    expect($emergencyServices->first()->offers_emergency_services)->toBeTrue();
});

test('with online booking scope returns correct services', function () {
    ProfessionalService::factory()->create(['accepts_online_booking' => true]);
    ProfessionalService::factory()->create(['accepts_online_booking' => false]);

    $onlineBookingServices = ProfessionalService::withOnlineBooking()->get();

    expect($onlineBookingServices)->toHaveCount(1);
    expect($onlineBookingServices->first()->accepts_online_booking)->toBeTrue();
});

test('can increment view count', function () {
    $service = ProfessionalService::factory()->create(['view_count' => 10]);

    $service->incrementViewCount();

    expect($service->fresh()->view_count)->toBe(11);
});

test('can increment contact count', function () {
    $service = ProfessionalService::factory()->create(['contact_count' => 5]);

    $service->incrementContactCount();

    expect($service->fresh()->contact_count)->toBe(6);
});

test('service area attribute returns correct format', function () {
    $service = ProfessionalService::factory()->create([
        'city' => 'Warszawa',
        'service_radius_km' => 15,
    ]);

    expect($service->service_area)->toBe('Warszawa + 15 km');
});

test('price range attribute formats correctly with base price only', function () {
    $service = ProfessionalService::factory()->create([
        'base_price' => 100.50,
        'hourly_rate' => null,
        'currency' => 'PLN',
    ]);

    expect($service->price_range)->toBe('od 100.50 PLN');
});

test('price range attribute formats correctly with hourly rate only', function () {
    $service = ProfessionalService::factory()->create([
        'base_price' => null,
        'hourly_rate' => 80.00,
        'currency' => 'PLN',
    ]);

    expect($service->price_range)->toBe('80.00 PLN/h');
});

test('price range attribute formats correctly with both prices', function () {
    $service = ProfessionalService::factory()->create([
        'base_price' => 100.00,
        'hourly_rate' => 80.00,
        'currency' => 'PLN',
    ]);

    expect($service->price_range)->toBe('od 100.00 PLN, 80.00 PLN/h');
});

test('price range attribute returns null when no prices set', function () {
    $service = ProfessionalService::factory()->create([
        'base_price' => null,
        'hourly_rate' => null,
    ]);

    expect($service->price_range)->toBeNull();
});

test('services list attribute parses string correctly', function () {
    $service = ProfessionalService::factory()->create([
        'services_offered' => 'badania ogólne,szczepienia,konsultacje',
    ]);

    $servicesList = $service->services_list;

    expect($servicesList)->toBeArray();
    expect($servicesList)->toHaveCount(3);
    expect($servicesList)->toContain('badania ogólne');
    expect($servicesList)->toContain('szczepienia');
    expect($servicesList)->toContain('konsultacje');
});

test('specialization list attribute handles array correctly', function () {
    $specializations = ['psy małych ras', 'koty perskie'];
    $service = ProfessionalService::factory()->create([
        'specializations' => $specializations,
    ]);

    expect($service->specialization_list)->toBe($specializations);
});

test('rating display attribute formats correctly with ratings', function () {
    $service = ProfessionalService::factory()->create([
        'average_rating' => 4.7,
        'review_count' => 12,
    ]);

    expect($service->rating_display)->toBe('4.7/5.0 (12 ocen)');
});

test('rating display attribute handles single review', function () {
    $service = ProfessionalService::factory()->create([
        'average_rating' => 5.0,
        'review_count' => 1,
    ]);

    expect($service->rating_display)->toBe('5.0/5.0 (1 ocena)');
});

test('rating display attribute handles no ratings', function () {
    $service = ProfessionalService::factory()->create([
        'average_rating' => 0.00,
        'review_count' => 0,
    ]);

    expect($service->rating_display)->toBe('Brak ocen');
});

test('service can sync to map with correct data structure', function () {
    $category = AdvertisementCategory::factory()->create([
        'name' => 'Usługi weterynaryjne',
        'icon' => 'heart',
        'color' => 'red',
    ]);

    $service = ProfessionalService::factory()->published()->create([
        'advertisement_category_id' => $category->id,
        'business_name' => 'Przychodnia Weterynaryjna Test',
        'latitude' => 52.2297,
        'longitude' => 21.0122,
        'city' => 'Warszawa',
        'base_price' => 150.00,
        'currency' => 'PLN',
        'is_featured' => true,
        'offers_emergency_services' => true,
        'view_count' => 25,
        'contact_count' => 8,
        'average_rating' => 4.5,
        'review_count' => 10,
    ]);

    // Use reflection to access protected method
    $reflection = new ReflectionClass($service);
    $method = $reflection->getMethod('getMapData');
    $method->setAccessible(true);
    $mapData = $method->invoke($service);

    expect($mapData)->toBeArray();
    expect($mapData['title'])->toBe('Przychodnia Weterynaryjna Test');
    expect($mapData['content_type'])->toBe('service');
    expect($mapData['category_name'])->toBe('Usługi weterynaryjne');
    expect($mapData['category_icon'])->toBe('heart');
    expect($mapData['category_color'])->toBe('red');
    expect($mapData['price_from'])->toBe('150.00');
    expect($mapData['currency'])->toBe('PLN');
    expect($mapData['is_featured'])->toBeTrue();
    expect($mapData['is_urgent'])->toBeTrue(); // emergency services
    expect($mapData['view_count'])->toBe(25);
    expect($mapData['interaction_count'])->toBe(8);
    expect($mapData['rating_avg'])->toBe('4.50');
    expect($mapData['rating_count'])->toBe(10);
    expect($mapData['zoom_level_min'])->toBe(11);
});

test('map data returns null for unpublished service', function () {
    $service = ProfessionalService::factory()->create([
        'status' => 'pending',
        'latitude' => 52.2297,
        'longitude' => 21.0122,
    ]);

    $reflection = new ReflectionClass($service);
    $method = $reflection->getMethod('getMapData');
    $method->setAccessible(true);
    $mapData = $method->invoke($service);

    expect($mapData)->toBeNull();
});

test('map data returns null for service without location', function () {
    $service = ProfessionalService::factory()->published()->create([
        'latitude' => null,
        'longitude' => null,
    ]);

    $reflection = new ReflectionClass($service);
    $method = $reflection->getMethod('getMapData');
    $method->setAccessible(true);
    $mapData = $method->invoke($service);

    expect($mapData)->toBeNull();
});

test('casts work properly for professional services', function () {
    $service = ProfessionalService::factory()->create([
        'base_price' => 125.75,
        'hourly_rate' => 80.50,
        'latitude' => 52.2297,
        'longitude' => 21.0122,
        'is_featured' => true,
        'is_licensed' => true,
        'accepts_online_booking' => true,
        'offers_emergency_services' => false,
        'average_rating' => 4.25,
        'pricing_details' => ['consultation' => 100, 'emergency' => 200],
        'availability' => ['monday' => ['09:00-17:00']],
        'social_media' => ['facebook' => 'https://facebook.com/test'],
        'certifications' => ['Lekarz weterynarii'],
        'specializations' => ['psy małych ras'],
    ]);

    expect($service->base_price)->toBe('125.75');
    expect($service->hourly_rate)->toBe('80.50');
    expect($service->latitude)->toBe('52.22970000');
    expect($service->longitude)->toBe('21.01220000');
    expect($service->is_featured)->toBeTrue();
    expect($service->is_licensed)->toBeTrue();
    expect($service->accepts_online_booking)->toBeTrue();
    expect($service->offers_emergency_services)->toBeFalse();
    expect($service->average_rating)->toBe('4.25');
    expect($service->pricing_details)->toBeArray();
    expect($service->availability)->toBeArray();
    expect($service->social_media)->toBeArray();
    expect($service->certifications)->toBeArray();
    expect($service->specializations)->toBeArray();
});

test('factory states work correctly', function () {
    $publishedService = ProfessionalService::factory()->published()->create();
    expect($publishedService->status)->toBe('published');

    $featuredService = ProfessionalService::factory()->featured()->create();
    expect($featuredService->is_featured)->toBeTrue();

    $emergencyService = ProfessionalService::factory()->withEmergencyServices()->create();
    expect($emergencyService->offers_emergency_services)->toBeTrue();

    $onlineBookingService = ProfessionalService::factory()->withOnlineBooking()->create();
    expect($onlineBookingService->accepts_online_booking)->toBeTrue();

    $veterinaryService = ProfessionalService::factory()->veterinary()->create();
    expect($veterinaryService->business_name)->toContain('Przychodnia Weterynaryjna');
    expect($veterinaryService->is_licensed)->toBeTrue();

    $groomingService = ProfessionalService::factory()->grooming()->create();
    expect($groomingService->business_name)->toContain('Salon Groomerski');
    expect($groomingService->services_offered)->toContain('strzyżenie');
});