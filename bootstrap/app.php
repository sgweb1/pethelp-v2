<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders([
        \App\Providers\VoltServiceProvider::class,
        \App\Providers\MapServiceProvider::class,
    ])
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->group(base_path('routes/auth.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web([
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\SetLocale::class,
        ]);

        // Configure CSRF exceptions for Livewire
        $middleware->validateCsrfTokens(
            except: [
                'livewire/*',
                'livewire/update',
                'livewire/upload-file',
                'api/js-logs',
                'payu/notify', // PayU webhook endpoint
                'api/ai/*', // AI Assistant API endpoints
                'api/location/*', // Location & Population Estimation API endpoints
            ]
        );

        $middleware->alias([
            'map.throttle' => \App\Http\Middleware\MapApiThrottle::class,
            'requires.feature' => \App\Http\Middleware\RequiresFeature::class,
            'requires.subscription' => \App\Http\Middleware\RequiresActiveSubscription::class,
            'check.listing.limits' => \App\Http\Middleware\CheckListingLimits::class,
            'local-only' => \App\Http\Middleware\LocalOnlyMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->dontReportDuplicates();
        $exceptions->dontFlash([
            'current_password',
            'password',
            'password_confirmation',
        ]);

        // Disable syntax highlighting to avoid phiki/phiki issues and speed up error display
        $exceptions->context(function () {
            return [
                'SYNTAX_HIGHLIGHTING_ENABLED' => false,
                'WHOOPS_EDITOR' => false,
                'SHOW_EXCEPTION_CODEHIGHLIGHTING' => false,
            ];
        });

        // Suppress phiki/phiki exceptions in production
        $exceptions->dontReport([
            \Phiki\Exceptions\FailedToInitializePatternSearchException::class,
            \Phiki\Exceptions\FailedToSetSearchPositionException::class,
        ]);

        // Handle phiki errors gracefully and speed up RouteNotFoundException
        $exceptions->renderable(function (\Throwable $e) {
            // Fast display for RouteNotFoundException
            if ($e instanceof \Symfony\Component\Routing\Exception\RouteNotFoundException && app()->environment('local')) {
                return response('<h1>Route Error (Fast Display)</h1><p><strong>Route not found:</strong> ' . $e->getMessage() . '</p><p><strong>File:</strong> ' . $e->getFile() . ':' . $e->getLine() . '</p>', 404);
            }

            if (str_contains(get_class($e), 'Phiki\\') ||
                str_contains($e->getFile() ?? '', 'phiki/phiki')) {
                // Return a simple error page without syntax highlighting
                if (app()->environment('production')) {
                    return response()->view('errors.500', [], 500);
                }
            }
        });
    })->create();
