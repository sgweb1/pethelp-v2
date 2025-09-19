<?php

declare(strict_types=1);

use function Pest\Laravel\{get};

describe('Accessibility (WCAG 2.1)', function () {
    it('has skip navigation link', function () {
        get('/')
            ->assertOk()
            ->assertSee('Przejdź do głównej treści')
            ->assertSee('skip-navigation');
    });

    it('has proper semantic HTML structure', function () {
        get('/')
            ->assertOk()
            ->assertSee('main-content');
    });

    it('accessibility styles are available', function () {
        get('/')
            ->assertOk();

        expect(file_exists(resource_path('css/accessibility.css')))->toBeTrue();
    });

    it('accessibility JavaScript is available', function () {
        expect(file_exists(resource_path('js/accessibility.js')))->toBeTrue();
    });

    it('has basic accessibility structure', function () {
        get('/')
            ->assertOk()
            ->assertSee('skip-navigation')
            ->assertSee('main-content');
    });
});