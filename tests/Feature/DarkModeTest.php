<?php

declare(strict_types=1);

use function Pest\Laravel\{get};

describe('Dark Mode', function () {
    it('includes dark mode toggle in navigation', function () {
        get('/')
            ->assertOk()
            ->assertSee('dark-mode-toggle')
            ->assertSeeInOrder([
                'x-data="darkModeToggle"',
                'x-init="init()"'
            ]);
    });

    it('has proper dark mode CSS classes in layout', function () {
        get('/')
            ->assertOk()
            ->assertSee('dark:bg-gray-800/95')
            ->assertSee('dark:text-gray-300')
            ->assertSee('dark:from-gray-900');
    });

    it('includes dark mode JavaScript functionality', function () {
        get('/')
            ->assertOk()
            ->assertSee('darkModeToggle');
    });

    it('navigation has dark mode support', function () {
        get('/')
            ->assertOk()
            ->assertSee('dark:text-gray-300')
            ->assertSee('dark:hover:text-indigo-400');
    });

    it('navigation elements have proper dark mode styling', function () {
        get('/')
            ->assertOk()
            ->assertSeeInOrder([
                'text-gray-700 dark:text-gray-300',
                'hover:text-indigo-600 dark:hover:text-indigo-400'
            ]);
    });
});