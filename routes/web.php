<?php

use App\Http\Controllers\MapController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SitterController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Strony publiczne (bez autoryzacji)
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('pages.public.welcome');
})->name('home');

Route::get('/search', function () {
    return view('domains.map.search');
})->name('search');

// Test SVG.js - tylko w dev mode
if (app()->environment('local')) {
    Route::get('/svg-test', function () {
        return view('svg-test');
    })->name('svg-test');

    // AI Wizard Presentation
    Route::get('/wizard-ai-presentation', function () {
        return view('pages.wizard-ai-presentation');
    })->name('wizard-ai-presentation');

    // Smart Search Mockup
    Route::get('/mockups/smart-search', function () {
        return view('mockups.smart-search');
    })->name('mockups.smart-search');
}

Route::get('/sitter/{sitter}', [SitterController::class, 'show'])->name('sitter.show');

Route::get('/booking/{service}', function (\App\Models\Service $service) {
    return view('domains.bookings.create', compact('service'));
})->name('booking.create');

// Publiczne ogłoszenia
Route::prefix('advertisements')->name('advertisements.')->group(function () {
    Route::get('/', function () {
        return view('domains.advertisements.index');
    })->name('index');

    Route::get('/{advertisement}', function (\App\Models\Advertisement $advertisement) {
        return view('domains.advertisements.show', compact('advertisement'));
    })->name('show');
});

// Publiczne wydarzenia
Route::prefix('events')->name('events.')->group(function () {
    Route::get('/', function () {
        return view('domains.events.index');
    })->name('index');

    Route::get('/{event}', function (\App\Models\Event $event) {
        return view('domains.events.show', compact('event'));
    })->name('show');
});

// Publiczne usługi profesjonalne
Route::prefix('professional-services')->name('professional-services.')->group(function () {
    Route::get('/', function () {
        return view('domains.professional-services.index');
    })->name('index');

    Route::get('/{professionalService}', function (\App\Models\ProfessionalService $professionalService) {
        return view('domains.professional-services.show', compact('professionalService'));
    })->name('show');
});

// Publiczne recenzje użytkownika
Route::get('/reviews/{user}', function (\App\Models\User $user) {
    return view('domains.reviews.user', compact('user'));
})->name('reviews.user');

// Mapa (publiczna)
Route::get('/map', function () {
    return view('domains.map.index');
})->name('map.index');

Route::prefix('map')->name('map.')->group(function () {
    Route::get('/data', [MapController::class, 'index'])->name('data');
    Route::get('/stats', [MapController::class, 'stats'])->name('stats');
    Route::get('/categories', [MapController::class, 'categories'])->name('categories');
    Route::get('/near', [MapController::class, 'nearLocation'])->name('near');
    Route::get('/items/{mapItem}', [MapController::class, 'show'])->name('item');
});

// Subskrypcje (częściowo publiczne)
Route::prefix('subscription')->name('subscription.')->group(function () {
    Route::get('/plans', \App\Livewire\Subscription\PricingPage::class)->name('plans');
});

/*
|--------------------------------------------------------------------------
| Panel użytkownika (z autoryzacją i prefiksem profil/)
|--------------------------------------------------------------------------
*/

Route::prefix('profil')->name('profile.')->middleware('auth')->group(function () {

    // Główny dashboard
    Route::get('/', function () {
        return view('pages.dashboard.dashboard-enhanced');
    })->name('dashboard');

    // Dashboard opiekuna
    Route::get('/opiekun', function () {
        return view('pages.dashboard.sitter');
    })->name('sitter');

    // Pet Sitter Registration Wizard
    Route::get('/zostan-pet-sitterem/{step?}', \App\Livewire\PetSitterWizard::class)->name('become-sitter');

    /*
    |--------------------------------------------------------------------------
    | Zwierzęta użytkownika
    |--------------------------------------------------------------------------
    */
    Route::prefix('zwierzeta')->name('pets.')->group(function () {
        Route::get('/', \App\Livewire\Dashboard\Pets\PetsList::class)->name('index');
        Route::get('/dodaj', \App\Livewire\Dashboard\Pets\PetForm::class)->name('create');
        Route::get('/{pet}/edytuj', \App\Livewire\Dashboard\Pets\PetForm::class)->name('edit');
    });

    /*
    |--------------------------------------------------------------------------
    | Galeria użytkownika
    |--------------------------------------------------------------------------
    */
    Route::prefix('galeria')->name('gallery.')->group(function () {
        Route::get('/', \App\Livewire\Dashboard\Gallery\GalleryIndex::class)->name('index');
        Route::get('/dodaj', \App\Livewire\Dashboard\Gallery\PhotoUpload::class)->name('upload');
    });

    /*
    |--------------------------------------------------------------------------
    | Usługi opiekuna
    |--------------------------------------------------------------------------
    */
    Route::prefix('uslugi')->name('services.')->group(function () {
        Route::get('/', \App\Livewire\Services\ServicesList::class)->name('index');
        Route::get('/dodaj', \App\Livewire\Services\CategorySelector::class)->name('create');

        Route::get('/dodaj/{category}', function (int $category) {
            $serviceCategory = \App\Models\ServiceCategory::findOrFail($category);

            $viewMap = [
                'opieka-w-domu' => 'domains.services.forms.home-care-wrapper',
                'spacery' => 'domains.services.forms.walking-wrapper',
                'opieka-u-opiekuna' => 'domains.services.forms.sitter-home-wrapper',
                'wizyta-kontrolna' => 'domains.services.forms.checkup-wrapper',
                'karmienie' => 'domains.services.forms.feeding-wrapper',
                'transport-weterynaryjny' => 'domains.services.forms.transport-wrapper',
                'pielegnacja' => 'domains.services.forms.grooming-wrapper',
                'opieka-nocna' => 'domains.services.forms.night-care-wrapper',
            ];

            $view = $viewMap[$serviceCategory->slug] ?? 'domains.services.forms.home-care-wrapper';

            return view($view, ['categoryId' => $category]);
        })->name('create.form');

        Route::get('/{service}/edytuj', function (\App\Models\Service $service) {
            // Sprawdź czy użytkownik jest właścicielem usługi
            if ($service->sitter_id !== auth()->id()) {
                abort(403, 'Nie masz uprawnień do edycji tej usługi.');
            }

            $serviceCategory = $service->category;
            $viewMap = [
                'opieka-w-domu' => 'domains.services.forms.home-care-wrapper',
                'spacery' => 'domains.services.forms.walking-wrapper',
                'opieka-u-opiekuna' => 'domains.services.forms.sitter-home-wrapper',
                'wizyta-kontrolna' => 'domains.services.forms.checkup-wrapper',
                'karmienie' => 'domains.services.forms.feeding-wrapper',
                'transport-weterynaryjny' => 'domains.services.forms.transport-wrapper',
                'pielegnacja' => 'domains.services.forms.grooming-wrapper',
                'opieka-nocna' => 'domains.services.forms.night-care-wrapper',
            ];

            $view = $viewMap[$serviceCategory->slug] ?? 'domains.services.forms.home-care-wrapper';

            return view($view, ['service' => $service]);
        })->name('edit');
    });

    /*
    |--------------------------------------------------------------------------
    | Rezerwacje użytkownika
    |--------------------------------------------------------------------------
    */
    Route::get('/rezerwacje', function () {
        $view = request('view', 'owner');

        return view('domains.bookings.bookings', compact('view'));
    })->name('bookings');

    /*
    |--------------------------------------------------------------------------
    | Powiadomienia użytkownika
    |--------------------------------------------------------------------------
    */
    Route::get('/powiadomienia', function () {
        return view('domains.notifications.center');
    })->name('notifications');

    /*
    |--------------------------------------------------------------------------
    | Recenzje użytkownika
    |--------------------------------------------------------------------------
    */
    Route::get('/recenzje', \App\Livewire\ReviewListNew::class)->name('reviews');

    Route::get('/recenzja/{booking}', function (\App\Models\Booking $booking) {
        // Sprawdź czy użytkownik ma prawo do wystawienia recenzji dla tej rezerwacji
        if ($booking->user_id !== auth()->id() && $booking->service->sitter_id !== auth()->id()) {
            abort(403, 'Nie masz uprawnień do wystawienia recenzji dla tej rezerwacji.');
        }

        return view('domains.reviews.create', compact('booking'));
    })->name('review.create');

    /*
    |--------------------------------------------------------------------------
    | Czat użytkownika
    |--------------------------------------------------------------------------
    */
    Route::prefix('czat')->name('chat.')->group(function () {
        Route::get('/', function () {
            return view('domains.chat.index');
        })->name('index');

        Route::get('/pelny-ekran', function () {
            return view('domains.chat.fullscreen');
        })->name('fullscreen');
    });

    /*
    |--------------------------------------------------------------------------
    | Dostępność opiekuna
    |--------------------------------------------------------------------------
    */
    Route::get('/dostepnosc', function () {
        return view('domains.availability.calendar');
    })->name('availability');

    /*
    |--------------------------------------------------------------------------
    | Ogłoszenia użytkownika
    |--------------------------------------------------------------------------
    */
    Route::prefix('ogloszenia')->name('advertisements.')->group(function () {
        Route::get('/dodaj', \App\Livewire\Advertisements\CategorySelector::class)->name('create');

        Route::get('/dodaj/{category}', function (int $category) {
            $advertisementCategory = \App\Models\AdvertisementCategory::findOrFail($category);

            $viewMap = [
                'adoption' => 'advertisements.forms.adoption-wrapper',
                'sales' => 'advertisements.forms.sales-wrapper',
                'lost_found' => 'advertisements.forms.lost-found-wrapper',
                'supplies' => 'advertisements.forms.supplies-wrapper',
                'services' => 'advertisements.forms.services-wrapper',
            ];

            $view = $viewMap[$advertisementCategory->type] ?? 'advertisements.forms.adoption-wrapper';

            return view($view, ['categoryId' => $category]);
        })->name('create.form');

        Route::get('/{advertisement}/edytuj', function (\App\Models\Advertisement $advertisement) {
            if (auth()->id() !== $advertisement->user_id) {
                abort(403, 'Nie masz uprawnień do edycji tego ogłoszenia.');
            }

            $advertisementCategory = $advertisement->advertisementCategory;
            $viewMap = [
                'adoption' => 'advertisements.forms.adoption-wrapper',
                'sales' => 'advertisements.forms.sales-wrapper',
                'lost_found' => 'advertisements.forms.lost-found-wrapper',
                'supplies' => 'advertisements.forms.supplies-wrapper',
                'services' => 'advertisements.forms.services-wrapper',
            ];

            $view = $viewMap[$advertisementCategory->type] ?? 'advertisements.forms.adoption-wrapper';

            return view($view, ['advertisement' => $advertisement]);
        })->name('edit');
    });

    /*
    |--------------------------------------------------------------------------
    | Wydarzenia użytkownika
    |--------------------------------------------------------------------------
    */
    Route::prefix('wydarzenia')->name('events.')->group(function () {
        Route::get('/dodaj', function () {
            return view('domains.events.create');
        })->name('create');

        Route::get('/{event}/edytuj', function (\App\Models\Event $event) {
            if (auth()->id() !== $event->organizer_id) {
                abort(403, 'Nie masz uprawnień do edycji tego wydarzenia.');
            }

            return view('domains.events.edit', compact('event'));
        })->name('edit');
    });

    /*
    |--------------------------------------------------------------------------
    | Usługi profesjonalne użytkownika
    |--------------------------------------------------------------------------
    */
    Route::prefix('uslugi-profesjonalne')->name('professional-services.')->group(function () {
        Route::get('/dodaj', function () {
            return view('domains.professional-services.create');
        })->name('create');

        Route::get('/{professionalService}/edytuj', function (\App\Models\ProfessionalService $professionalService) {
            if (auth()->id() !== $professionalService->user_id) {
                abort(403, 'Nie masz uprawnień do edycji tej usługi.');
            }

            return view('domains.professional-services.edit', compact('professionalService'));
        })->name('edit');
    });

    /*
    |--------------------------------------------------------------------------
    | Płatności i subskrypcje
    |--------------------------------------------------------------------------
    */
    Route::prefix('platnosci')->name('payments.')->group(function () {
        Route::get('/{booking}', function (\App\Models\Booking $booking) {
            // Sprawdź czy użytkownik ma prawo do płatności za tę rezerwację
            if ($booking->user_id !== auth()->id()) {
                abort(403, 'Nie masz uprawnień do tej płatności.');
            }

            return view('domains.payments.process', compact('booking'));
        })->name('process');
    });

    Route::prefix('subskrypcja')->name('subscription.')->group(function () {
        Route::get('/', \App\Livewire\Subscription\Dashboard::class)->name('dashboard');
        Route::get('/wybierz/{plan}', \App\Livewire\Subscription\CheckoutForm::class)->name('checkout');

        Route::post('/subskrybuj/{plan}', [PaymentController::class, 'createSubscriptionPayment'])->name('subscribe');

        Route::get('/platnosc/formularz', function () {
            $formData = session('payu_form_data');
            $redirectUrl = session('payu_redirect_url');

            if (! $formData || ! $redirectUrl) {
                return redirect()->route('subscription.plans')
                    ->with('error', 'Sesja płatności wygasła. Spróbuj ponownie.');
            }

            return view('domains.subscription.payment-form-standalone', compact('formData', 'redirectUrl'));
        })->name('payment.form');

        Route::get('/platnosc/sukces', [PaymentController::class, 'paymentSuccess'])->name('payment.success');
        Route::get('/platnosc/anulowano', [PaymentController::class, 'paymentCancel'])->name('payment.cancel');
        Route::get('/platnosc/{payment}/status', [PaymentController::class, 'paymentStatus'])->name('payment.status');

        // Faktury
        Route::prefix('faktury')->name('invoices.')->group(function () {
            Route::get('/{payment}/pobierz', [\App\Http\Controllers\InvoiceController::class, 'downloadInvoicePdf'])
                ->name('download');
            Route::post('/{payment}/regeneruj', [\App\Http\Controllers\InvoiceController::class, 'regenerateInvoice'])
                ->name('regenerate');
            Route::get('/{payment}/status', [\App\Http\Controllers\InvoiceController::class, 'checkInvoiceStatus'])
                ->name('status');
        });
    });
});

/*
|--------------------------------------------------------------------------
| Webhooks i API (bez autoryzacji)
|--------------------------------------------------------------------------
*/

// PayU webhook (bez autoryzacji)
Route::post('/payu/notify', [PaymentController::class, 'payuNotification'])->name('payu.notify');

// Campaign tracking (bez autoryzacji i CSRF - dostępne dla emaili)
Route::prefix('campaigns')->name('campaigns.')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])->group(function () {
    Route::get('/track/open/{campaign}/{user}', [\App\Http\Controllers\Api\CampaignTrackingController::class, 'trackOpen'])
        ->name('track.open');

    Route::get('/track/click/{campaign}/{user}', [\App\Http\Controllers\Api\CampaignTrackingController::class, 'trackClick'])
        ->name('track.click');

    Route::get('/unsubscribe/{campaign}/{user}', [\App\Http\Controllers\Api\CampaignTrackingController::class, 'unsubscribe'])
        ->name('unsubscribe');

    Route::post('/track/conversion/{campaign}/{user}', [\App\Http\Controllers\Api\CampaignTrackingController::class, 'trackConversion'])
        ->name('track.conversion');
});

// AI Assistant API (without CSRF protection for easier integration)
Route::prefix('api/ai')->name('api.ai.')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])->group(function () {
    Route::post('/suggestions/{step}', function (int $step) {
        $wizardData = request()->validate([
            'wizard_data' => 'required|array',
            'context' => 'array',
        ]);

        $hybridAI = app(\App\Services\AI\HybridAIAssistant::class);

        try {
            $suggestions = $hybridAI->getStepSuggestions(
                $step,
                $wizardData['wizard_data'],
                $wizardData['context'] ?? []
            );

            return response()->json([
                'success' => true,
                'data' => $suggestions,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Nie udało się wygenerować sugestii',
                'message' => app()->environment('local') ? $e->getMessage() : null,
            ], 500);
        }
    })->name('suggestions');

    Route::delete('/cache/{step}', function (int $step) {
        $wizardData = request()->validate([
            'wizard_data' => 'array',
            'context' => 'array',
        ]);

        $hybridAI = app(\App\Services\AI\HybridAIAssistant::class);

        $cleared = $hybridAI->clearStepCache(
            $step,
            $wizardData['wizard_data'] ?? [],
            $wizardData['context'] ?? []
        );

        return response()->json([
            'success' => $cleared,
            'message' => $cleared ? 'Cache został wyczyszczony' : 'Nie udało się wyczyścić cache',
        ]);
    })->name('cache.clear');

    Route::get('/stats', function () {
        $hybridAI = app(\App\Services\AI\HybridAIAssistant::class);

        return response()->json([
            'success' => true,
            'data' => $hybridAI->getUsageStats(),
        ]);
    })->name('stats');

    Route::get('/performance', function () {
        $hybridAI = app(\App\Services\AI\HybridAIAssistant::class);

        return response()->json([
            'success' => true,
            'data' => $hybridAI->getPerformanceMetrics(),
        ]);
    })->name('performance');

    Route::post('/optimize-cache', function () {
        $hybridAI = app(\App\Services\AI\HybridAIAssistant::class);
        $maxAge = request()->get('max_age_hours', 24);

        $deleted = $hybridAI->optimizeCache($maxAge);

        return response()->json([
            'success' => true,
            'message' => "Optymalizacja zakończona. Usunięto {$deleted} wpisów.",
            'deleted_entries' => $deleted,
        ]);
    })->name('optimize.cache');
});

// Location & Population Estimation API (bez autoryzacji CSRF dla łatwiejszej integracji)
Route::prefix('api/location')->name('api.location.')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])->group(function () {
    Route::post('/estimate-population', [\App\Http\Controllers\Api\LocationController::class, 'estimatePopulation'])
        ->name('estimate.population');

    Route::get('/search', [\App\Http\Controllers\Api\LocationController::class, 'search'])
        ->name('search');

    Route::post('/reverse-geocode', [\App\Http\Controllers\Api\LocationController::class, 'reverseGeocode'])
        ->name('reverse.geocode');
});

/*
|--------------------------------------------------------------------------
| Routes deweloperskie (tylko lokalnie)
|--------------------------------------------------------------------------
*/

Route::middleware('local-only')->group(function () {
    // Szybkie logowanie dla deweloperów
    Route::get('/quick-login', [App\Http\Controllers\QuickLoginController::class, 'loginAsUser']);
    Route::get('/quick-login-owner', [App\Http\Controllers\QuickLoginController::class, 'loginAsOwner']);
    Route::get('/quick-login-sitter', [App\Http\Controllers\QuickLoginController::class, 'loginAsSitter']);
    Route::get('/quick-login/{userId}', [App\Http\Controllers\QuickLoginController::class, 'loginAs'])->where('userId', '[0-9]+');

    // Testy kroków wizard'a Pet Sitter
    Route::get('/wizard-test', function () {
        $stepNames = [
            1 => 'Wprowadzenie - Motywacja',
            2 => 'Doświadczenie z zwierzętami',
            3 => 'Rodzaje zwierząt',
            4 => 'Rodzaje usług',
            5 => 'Lokalizacja i promień',
            6 => 'Dostępność',
            7 => 'Dom i ogród',
            8 => 'Zdjęcia',
            9 => 'Weryfikacja',
            10 => 'Cennik',
            11 => 'Podsumowanie',
            12 => 'Podgląd',
        ];

        return view('domains.dev.wizard-test', compact('stepNames'));
    })->name('dev.wizard-test');

    // Testy PayU
    Route::get('/payu/test', function () {
        $service = config('payu.api_type') === 'classic'
            ? app(\App\Services\PayUClassicService::class)
            : app(\App\Services\PayUService::class);

        $result = $service->testConnection();

        return response()->json($result, $result['success'] ? 200 : 400);
    })->name('payu.test');

    // Testy InFakt
    Route::get('/infakt/test', function () {
        try {
            $service = app(\App\Services\InFaktService::class);
            $result = $service->testConnection();

            return response()->json($result, $result['success'] ? 200 : 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'config' => [
                    'environment' => config('infakt.environment'),
                    'api_key_set' => config('infakt.api_key') !== 'your_api_key_here',
                ],
            ], 500);
        }
    })->name('infakt.test');
});
