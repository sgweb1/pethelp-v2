<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('search page has mobile-friendly viewport meta tag', function () {
    $response = $this->get('/search');

    $response->assertStatus(200);
    $response->assertSee('width=device-width, initial-scale=1, viewport-fit=cover', false);
});

test('search page has mobile app meta tags', function () {
    $response = $this->get('/search');

    $response->assertStatus(200);
    $response->assertSee('mobile-web-app-capable', false);
    $response->assertSee('apple-mobile-web-app-capable', false);
    $response->assertSee('theme-color', false);
});

test('search page loads without errors', function () {
    $response = $this->get('/search');

    $response->assertStatus(200);
    $response->assertViewIs('livewire.search');
});

test('search filters component renders responsive classes', function () {
    $response = $this->get('/search');

    $response->assertStatus(200);
    // Check for responsive grid classes
    $response->assertSee('grid-cols-1 sm:grid-cols-2 lg:grid-cols-4', false);
    // Check for responsive padding
    $response->assertSee('px-3 sm:px-4', false);
    // Check for responsive text sizing
    $response->assertSee('text-xs sm:text-sm', false);
});

test('map component has mobile-optimized height', function () {
    $response = $this->get('/search');

    $response->assertStatus(200);
    // Check for mobile map height
    $response->assertSee('height: 300px; min-height: 250px;', false);
});

test('navigation elements have touch-friendly sizing', function () {
    $response = $this->get('/search');

    $response->assertStatus(200);
    // Check for mobile button sizing
    $response->assertSee('px-2 sm:px-3 py-1.5 sm:py-2', false);
    // Check for responsive gap spacing
    $response->assertSee('gap-1 sm:gap-2', false);
});

test('mobile styles css file exists and is accessible', function () {
    $cssPath = resource_path('css/mobile-responsive.css');
    expect(file_exists($cssPath))->toBeTrue();

    $cssContent = file_get_contents($cssPath);
    expect($content = $cssContent)->toContain('.mobile-padding');
    expect($content)->toContain('.touch-target');
    expect($content)->toContain('@media (max-width: 767px)');
});

test('search results display properly on mobile layouts', function () {
    $response = $this->get('/search');

    $response->assertStatus(200);
    // Check for mobile-first flex layouts
    $response->assertSee('flex-col sm:flex-row', false);
    // Check for responsive item alignment
    $response->assertSee('items-stretch sm:items-center', false);
});

test('form inputs have mobile-optimized sizing', function () {
    $response = $this->get('/search');

    $response->assertStatus(200);
    // Check for responsive input padding
    $response->assertSee('px-3 sm:px-4 py-2 sm:py-3', false);
    // Check for responsive text size
    $response->assertSee('text-base', false);
});

test('buttons maintain minimum touch target size', function () {
    $response = $this->get('/search');

    $response->assertStatus(200);
    // Should have mobile-friendly button sizes
    $response->assertSee('px-2 sm:px-3', false);
    $response->assertSee('py-1.5 sm:py-2', false);
});

test('responsive breakpoints work correctly', function () {
    $css = file_get_contents(resource_path('css/mobile-responsive.css'));

    // Check for proper breakpoints
    expect($css)->toContain('@media (min-width: 640px)'); // sm
    expect($css)->toContain('@media (min-width: 768px)'); // md
    expect($css)->toContain('@media (min-width: 1024px)'); // lg
    expect($css)->toContain('@media (max-width: 767px)'); // mobile-only
});

test('mobile touch optimizations are present', function () {
    $jsPath = resource_path('js/mobile-touch.js');
    expect(file_exists($jsPath))->toBeTrue();

    $jsContent = file_get_contents($jsPath);
    expect($jsContent)->toContain('MobileTouchHandler');
    expect($jsContent)->toContain('touchstart');
    expect($jsContent)->toContain('orientationchange');
});

test('safe area insets are handled for modern mobile devices', function () {
    $css = file_get_contents(resource_path('css/mobile-responsive.css'));

    expect($css)->toContain('env(safe-area-inset-');
    expect($css)->toContain('mobile-safe-area');
});

test('high contrast and reduced motion are supported', function () {
    $css = file_get_contents(resource_path('css/mobile-responsive.css'));

    expect($css)->toContain('@media (prefers-contrast: high)');
    expect($css)->toContain('@media (prefers-reduced-motion: reduce)');
});

test('dark mode mobile adjustments exist', function () {
    $css = file_get_contents(resource_path('css/mobile-responsive.css'));

    expect($css)->toContain('@media (prefers-color-scheme: dark)');
    expect($css)->toContain('mobile-dark-card');
    expect($css)->toContain('mobile-dark-text');
});

test('mobile map container has proper responsive behavior', function () {
    $css = file_get_contents(resource_path('css/mobile-responsive.css'));

    expect($css)->toContain('.mobile-map-container');
    expect($css)->toContain('min-height: 250px');
    expect($css)->toContain('height: 300px');
});

test('touch feedback styles are implemented', function () {
    $jsContent = file_get_contents(resource_path('js/mobile-touch.js'));

    expect($jsContent)->toContain('touch-active');
    expect($jsContent)->toContain('transform: scale(0.95)');
    expect($jsContent)->toContain('-webkit-tap-highlight-color: transparent');
});

test('mobile landscape optimizations exist', function () {
    $css = file_get_contents(resource_path('css/mobile-responsive.css'));

    expect($css)->toContain('@media (max-width: 767px) and (orientation: landscape)');
    expect($css)->toContain('mobile-landscape-map');
});

test('iOS specific optimizations are implemented', function () {
    $jsContent = file_get_contents(resource_path('js/mobile-touch.js'));

    expect($jsContent)->toContain('iPhone|iPad|iPod');
    expect($jsContent)->toContain('handleIOSViewport');
    expect($jsContent)->toContain('keyboard-open');
});

test('pull-to-refresh prevention is implemented', function () {
    $jsContent = file_get_contents(resource_path('js/mobile-touch.js'));

    expect($jsContent)->toContain('preventPullToRefresh');
    expect($jsContent)->toContain('touchmove');
});