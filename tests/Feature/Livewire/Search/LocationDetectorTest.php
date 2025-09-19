<?php

use App\Livewire\Search\LocationDetector;
use Livewire\Livewire;

test('component renders successfully', function () {
    Livewire::test(LocationDetector::class)
        ->assertStatus(200)
        ->assertSet('latitude', null)
        ->assertSet('longitude', null)
        ->assertSet('location_detected', false)
        ->assertSet('location_address', '')
        ->assertSet('detecting', false);
});

test('starts detection correctly', function () {
    Livewire::test(LocationDetector::class)
        ->call('startDetection')
        ->assertSet('detecting', true)
        ->assertDispatched('start-geolocation');
});

test('sets location correctly', function () {
    $lat = 52.2297;
    $lng = 21.0122;
    $address = 'Warsaw, Poland';

    Livewire::test(LocationDetector::class)
        ->call('setLocation', $lat, $lng, $address)
        ->assertSet('latitude', $lat)
        ->assertSet('longitude', $lng)
        ->assertSet('location_address', $address)
        ->assertSet('location_detected', true)
        ->assertSet('detecting', false)
        ->assertDispatched('location-set', [
            'latitude' => $lat,
            'longitude' => $lng,
            'address' => $address,
        ]);
});

test('can clear location', function () {
    Livewire::test(LocationDetector::class)
        ->set('latitude', 52.2297)
        ->set('longitude', 21.0122)
        ->set('location_address', 'Warsaw')
        ->set('location_detected', true)
        ->call('clearLocation')
        ->assertSet('latitude', null)
        ->assertSet('longitude', null)
        ->assertSet('location_address', '')
        ->assertSet('location_detected', false)
        ->assertDispatched('location-cleared');
});

test('handles detection failure', function () {
    $errorMessage = 'Geolocation not supported';

    Livewire::test(LocationDetector::class)
        ->set('detecting', true)
        ->call('failedDetection', $errorMessage)
        ->assertSet('detecting', false)
        ->assertDispatched('location-error', $errorMessage);
});

test('handles detection failure without error message', function () {
    Livewire::test(LocationDetector::class)
        ->set('detecting', true)
        ->call('failedDetection')
        ->assertSet('detecting', false)
        ->assertDispatched('location-error', '');
});

test('responds to detect-location event', function () {
    $component = Livewire::test(LocationDetector::class)
        ->assertSet('detecting', false);

    // Simulate receiving detect-location event
    $component->call('startDetection')
        ->assertSet('detecting', true)
        ->assertDispatched('start-geolocation');
});

test('location state persists correctly', function () {
    $lat = 50.0647;
    $lng = 19.9450;
    $address = 'Kraków, Poland';

    $component = Livewire::test(LocationDetector::class)
        ->call('setLocation', $lat, $lng, $address)
        ->assertSet('latitude', $lat)
        ->assertSet('longitude', $lng)
        ->assertSet('location_address', $address)
        ->assertSet('location_detected', true);

    // Verify state persists
    expect($component->get('latitude'))->toBe($lat);
    expect($component->get('longitude'))->toBe($lng);
    expect($component->get('location_address'))->toBe($address);
    expect($component->get('location_detected'))->toBeTrue();
});

test('can set location without address', function () {
    $lat = 52.2297;
    $lng = 21.0122;

    Livewire::test(LocationDetector::class)
        ->call('setLocation', $lat, $lng)
        ->assertSet('latitude', $lat)
        ->assertSet('longitude', $lng)
        ->assertSet('location_address', '')
        ->assertSet('location_detected', true)
        ->assertDispatched('location-set', [
            'latitude' => $lat,
            'longitude' => $lng,
            'address' => '',
        ]);
});

test('detecting flag works correctly during detection process', function () {
    $component = Livewire::test(LocationDetector::class)
        ->assertSet('detecting', false)
        ->call('startDetection')
        ->assertSet('detecting', true);

    // Complete detection successfully
    $component->call('setLocation', 52.2297, 21.0122, 'Warsaw')
        ->assertSet('detecting', false);
});

test('detecting flag resets on detection failure', function () {
    $component = Livewire::test(LocationDetector::class)
        ->call('startDetection')
        ->assertSet('detecting', true)
        ->call('failedDetection', 'Error occurred')
        ->assertSet('detecting', false);
});
