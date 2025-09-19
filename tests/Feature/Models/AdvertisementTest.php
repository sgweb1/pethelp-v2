<?php

use App\Models\{Advertisement, AdvertisementCategory, User};
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->category = AdvertisementCategory::factory()->adoption()->create();
});

test('can create advertisement with factory', function () {
    $advertisement = Advertisement::factory()->create();

    expect($advertisement)->toBeInstanceOf(Advertisement::class);
    expect($advertisement->title)->toBeString();
    expect($advertisement->user_id)->toBeInt();
    expect($advertisement->advertisement_category_id)->toBeInt();
});

test('advertisement belongs to user', function () {
    $advertisement = Advertisement::factory()->for($this->user)->create();

    expect($advertisement->user)->toBeInstanceOf(User::class);
    expect($advertisement->user->id)->toBe($this->user->id);
});

test('advertisement belongs to category', function () {
    $advertisement = Advertisement::factory()->for($this->category, 'advertisementCategory')->create();

    expect($advertisement->advertisementCategory)->toBeInstanceOf(AdvertisementCategory::class);
    expect($advertisement->advertisementCategory->id)->toBe($this->category->id);
});

test('published scope returns only published advertisements', function () {
    Advertisement::factory()->create(['status' => 'published']);
    Advertisement::factory()->create(['status' => 'draft']);
    Advertisement::factory()->create(['status' => 'pending']);

    $publishedAds = Advertisement::published()->get();

    expect($publishedAds)->toHaveCount(1);
    expect($publishedAds->first()->status)->toBe('published');
});

test('active scope returns published and non-expired advertisements', function () {
    Advertisement::factory()->create(['status' => 'published', 'expires_at' => now()->addDays(1)]);
    Advertisement::factory()->create(['status' => 'published', 'expires_at' => now()->subDays(1)]);
    Advertisement::factory()->create(['status' => 'draft', 'expires_at' => now()->addDays(1)]);

    $activeAds = Advertisement::active()->get();

    expect($activeAds)->toHaveCount(1);
    expect($activeAds->first()->status)->toBe('published');
    expect($activeAds->first()->expires_at->timestamp)->toBeGreaterThan(now()->timestamp);
});

test('in city scope filters by city', function () {
    Advertisement::factory()->create(['city' => 'Warszawa']);
    Advertisement::factory()->create(['city' => 'Kraków']);

    $warsawAds = Advertisement::inCity('Warszawa')->get();

    expect($warsawAds)->toHaveCount(1);
    expect($warsawAds->first()->city)->toBe('Warszawa');
});

test('by type scope filters by pet type', function () {
    Advertisement::factory()->create(['pet_type' => 'dog']);
    Advertisement::factory()->create(['pet_type' => 'cat']);

    $dogAds = Advertisement::byType('dog')->get();

    expect($dogAds)->toHaveCount(1);
    expect($dogAds->first()->pet_type)->toBe('dog');
});

test('price range scope filters correctly', function () {
    Advertisement::factory()->create(['price' => 100]);
    Advertisement::factory()->create(['price' => 500]);
    Advertisement::factory()->create(['price' => 1000]);

    $midRangeAds = Advertisement::priceRange(200, 800)->get();

    expect($midRangeAds)->toHaveCount(1);
    expect($midRangeAds->first()->price)->toBe('500.00'); // Decimal cast returns string
});

test('featured scope returns only featured advertisements', function () {
    Advertisement::factory()->create(['is_featured' => true]);
    Advertisement::factory()->create(['is_featured' => false]);

    $featuredAds = Advertisement::featured()->get();

    expect($featuredAds)->toHaveCount(1);
    expect($featuredAds->first()->is_featured)->toBeTrue();
});

test('urgent scope returns only urgent advertisements', function () {
    Advertisement::factory()->create(['is_urgent' => true]);
    Advertisement::factory()->create(['is_urgent' => false]);

    $urgentAds = Advertisement::urgent()->get();

    expect($urgentAds)->toHaveCount(1);
    expect($urgentAds->first()->is_urgent)->toBeTrue();
});

test('can increment view count', function () {
    $advertisement = Advertisement::factory()->create(['view_count' => 5]);

    $advertisement->incrementViewCount();

    expect($advertisement->fresh()->view_count)->toBe(6);
});

test('can increment contact count', function () {
    $advertisement = Advertisement::factory()->create(['contact_count' => 3]);

    $advertisement->incrementContactCount();

    expect($advertisement->fresh()->contact_count)->toBe(4);
});

test('pet age attribute returns correct format', function () {
    $advertisement = Advertisement::factory()->create([
        'pet_birth_date' => now()->subYears(2),
    ]);

    $petAge = $advertisement->pet_age;

    expect($petAge)->toBeString();
    expect($petAge)->toContain('years ago');
});

test('contact info attribute returns correct data', function () {
    $advertisement = Advertisement::factory()->create([
        'contact_phone' => '123456789',
        'contact_email' => 'test@example.com',
        'show_phone' => true,
        'show_email' => true,
    ]);

    $contactInfo = $advertisement->contact_info;

    expect($contactInfo)->toBeArray();
    expect($contactInfo)->toHaveKeys(['phone', 'email']);
    expect($contactInfo['phone'])->toBe('123456789');
    expect($contactInfo['email'])->toBe('test@example.com');
});

test('contact info hides private data when show flags are false', function () {
    $advertisement = Advertisement::factory()->create([
        'contact_phone' => '123456789',
        'contact_email' => 'test@example.com',
        'show_phone' => false,
        'show_email' => false,
    ]);

    $contactInfo = $advertisement->contact_info;

    expect($contactInfo)->toBeArray();
    expect($contactInfo)->toBeEmpty();
});

test('isExpired returns correct status', function () {
    $expiredAd = Advertisement::factory()->create(['expires_at' => now()->subDays(1)]);
    $activeAd = Advertisement::factory()->create(['expires_at' => now()->addDays(1)]);

    expect($expiredAd->isExpired())->toBeTrue();
    expect($activeAd->isExpired())->toBeFalse();
});

test('advertisement category has proper attributes', function () {
    $category = AdvertisementCategory::factory()->create([
        'name' => 'Test Category',
        'type' => 'adoption',
        'is_active' => true,
    ]);

    expect($category->name)->toBe('Test Category');
    expect($category->type)->toBe('adoption');
    expect($category->is_active)->toBeTrue();
});

test('advertisement category active scope works', function () {
    AdvertisementCategory::query()->delete();

    AdvertisementCategory::factory()->create(['is_active' => true]);
    AdvertisementCategory::factory()->create(['is_active' => false]);

    $activeCategories = AdvertisementCategory::active()->get();

    expect($activeCategories)->toHaveCount(1);
    expect($activeCategories->first()->is_active)->toBeTrue();
});

test('casts work properly for advertisements', function () {
    $advertisement = Advertisement::factory()->create([
        'price' => 25.50,
        'is_featured' => true,
        'pet_vaccinated' => true,
        'pet_birth_date' => '2020-01-15',
    ]);

    expect($advertisement->price)->toBe('25.50'); // Decimal cast returns string
    expect($advertisement->is_featured)->toBeTrue();
    expect($advertisement->pet_vaccinated)->toBeTrue();
    expect($advertisement->pet_birth_date)->toBeInstanceOf(\Carbon\Carbon::class);
});