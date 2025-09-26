<?php

use App\Http\Controllers\MapController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SitterController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/search', function () {
    return view('search');
})->name('search');

Route::get('/sitter/{sitter}', [SitterController::class, 'show'])->name('sitter.show');

Route::get('/booking/{service}', function (\App\Models\Service $service) {
    return view('booking.create', compact('service'));
})->name('booking.create');

Route::get('/dashboard', function () {
    // Wszyscy użytkownicy korzystają z tego samego dashboard-a
    return view('dashboard-enhanced');
})->name('dashboard')->middleware('auth');


// Pet Sitter Dashboard
Route::get('/dashboard/sitter', function () {
    return view('livewire.dashboard.sitter');
})->name('dashboard.sitter')->middleware('auth');

Route::get('/pets', \App\Livewire\Dashboard\Pets\PetsList::class)->name('pets.index')->middleware('auth');

Route::get('/pets/create', \App\Livewire\Dashboard\Pets\PetForm::class)->name('pets.create')->middleware('auth');

Route::get('/pets/{pet}/edit', \App\Livewire\Dashboard\Pets\PetForm::class)->name('pets.edit')->middleware('auth');

// Gallery routes
Route::get('/gallery', \App\Livewire\Dashboard\Gallery\GalleryIndex::class)->name('gallery.index')->middleware('auth');
Route::get('/gallery/upload', \App\Livewire\Dashboard\Gallery\PhotoUpload::class)->name('gallery.upload')->middleware('auth');

// Pet Sitter Services routes
Route::get('/pet-sitter/services', \App\Livewire\Services\ServicesList::class)->name('sitter-services.index')->middleware('auth');

// Dynamic service creation routes - MUST come before /services/{service}/edit
Route::get('/pet-sitter/services/create', \App\Livewire\Services\CategorySelector::class)->name('sitter-services.create')->middleware('auth');

Route::get('/pet-sitter/services/create/{category}', function (int $category) {
    $serviceCategory = \App\Models\ServiceCategory::findOrFail($category);

    // Map category slugs to appropriate view templates
    $viewMap = [
        'opieka-w-domu' => 'services.forms.home-care-wrapper',
        'spacery' => 'services.forms.walking-wrapper',
        'opieka-u-opiekuna' => 'services.forms.sitter-home-wrapper',
        'wizyta-kontrolna' => 'services.forms.checkup-wrapper',
        'karmienie' => 'services.forms.feeding-wrapper',
        'transport-weterynaryjny' => 'services.forms.transport-wrapper',
        'pielegnacja' => 'services.forms.grooming-wrapper',
        'opieka-nocna' => 'services.forms.night-care-wrapper',
    ];

    $view = $viewMap[$serviceCategory->slug] ?? 'services.forms.home-care-wrapper';

    return view($view, ['categoryId' => $category]);
})->name('sitter-services.create.form')->middleware('auth');

Route::get('/pet-sitter/services/{service}/edit', function (\App\Models\Service $service) {
    // Check if user owns this service
    if ($service->sitter_id !== auth()->id()) {
        abort(403, 'Nie masz uprawnień do edycji tej usługi.');
    }

    $serviceCategory = $service->category;

    // Map category slugs to appropriate edit view templates
    $viewMap = [
        'opieka-w-domu' => 'services.forms.home-care-wrapper',
        'spacery' => 'services.forms.walking-wrapper',
        'opieka-u-opiekuna' => 'services.forms.sitter-home-wrapper',
        'wizyta-kontrolna' => 'services.forms.checkup-wrapper',
        'karmienie' => 'services.forms.feeding-wrapper',
        'transport-weterynaryjny' => 'services.forms.transport-wrapper',
        'pielegnacja' => 'services.forms.grooming-wrapper',
        'opieka-nocna' => 'services.forms.night-care-wrapper',
    ];

    $view = $viewMap[$serviceCategory->slug] ?? 'services.forms.home-care-wrapper';

    return view($view, ['service' => $service]);
})->name('sitter-services.edit')->middleware('auth');

// Debug route
Route::get('/test-service-class', function () {
    try {
        // Test basic class existence
        if (!class_exists('App\Models\Service')) {
            return response('Service class does not exist', 500);
        }

        // Test creating Service query
        $count = \App\Models\Service::count();

        // Test creating CategorySelector
        $selector = new \App\Livewire\Services\CategorySelector();

        return response()->json([
            'service_class_exists' => true,
            'service_count' => $count,
            'category_selector_created' => true,
            'current_user' => auth()->id(),
            'message' => 'All tests passed!'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ], 500);
    }
});

// Auth debug route
Route::get('/test-auth', function () {
    return response()->json([
        'authenticated' => auth()->check(),
        'user_id' => auth()->id(),
        'user_email' => auth()->user()?->email,
        'session_id' => session()->getId(),
        'csrf_token' => csrf_token(),
    ]);
});

// Quick login routes - tylko w środowisku lokalnym
Route::middleware('local-only')->group(function () {
    Route::get('/quick-login', [App\Http\Controllers\QuickLoginController::class, 'loginAsUser']);
    Route::get('/quick-login-owner', [App\Http\Controllers\QuickLoginController::class, 'loginAsOwner']);
    Route::get('/quick-login-sitter', [App\Http\Controllers\QuickLoginController::class, 'loginAsSitter']);
    Route::get('/quick-login/{userId}', [App\Http\Controllers\QuickLoginController::class, 'loginAs'])->where('userId', '[0-9]+');
});

// Test create route
Route::get('/test-create-route/{category}', function (int $category) {
    try {
        $serviceCategory = \App\Models\ServiceCategory::findOrFail($category);

        return response()->json([
            'category_found' => true,
            'category_name' => $serviceCategory->name,
            'category_slug' => $serviceCategory->slug,
            'user_authenticated' => auth()->check(),
            'user_id' => auth()->id(),
            'view_path' => 'services.forms.home-care-wrapper',
            'message' => 'Route works fine, ready to render view'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ], 500);
    }
})->middleware('auth');

// Test view rendering
Route::get('/test-view-render/{category}', function (int $category) {
    try {
        $serviceCategory = \App\Models\ServiceCategory::findOrFail($category);
        $categoryId = $category;

        return view('services.forms.home-care-wrapper', compact('categoryId'));
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ], 500);
    }
})->middleware('auth');


// Events routes
Route::prefix('events')->name('events.')->group(function () {
    Route::get('/', function () {
        return view('events.index');
    })->name('index');

    Route::get('/create', function () {
        return view('events.create');
    })->name('create')->middleware('auth');

    Route::get('/{event}', function (\App\Models\Event $event) {
        return view('events.show', compact('event'));
    })->name('show');

    Route::get('/{event}/edit', function (\App\Models\Event $event) {
        if (auth()->id() !== $event->user_id) {
            abort(403, 'Nie masz uprawnień do edycji tego wydarzenia.');
        }

        return view('events.edit', compact('event'));
    })->name('edit')->middleware('auth');
});

// Advertisements routes
Route::prefix('advertisements')->name('advertisements.')->group(function () {
    Route::get('/', function () {
        return view('advertisements.index');
    })->name('index');

    Route::get('/create', \App\Livewire\Advertisements\CategorySelector::class)
        ->name('create')->middleware('auth');

    Route::get('/create/{category}', function (int $category) {
        $advertisementCategory = \App\Models\AdvertisementCategory::findOrFail($category);

        // Map category types to appropriate view templates
        $viewMap = [
            'adoption' => 'advertisements.forms.adoption-wrapper',
            'sales' => 'advertisements.forms.sales-wrapper',
            'lost_found' => 'advertisements.forms.lost-found-wrapper',
            'supplies' => 'advertisements.forms.supplies-wrapper',
            'services' => 'advertisements.forms.services-wrapper',
        ];

        $view = $viewMap[$advertisementCategory->type] ?? 'advertisements.forms.adoption-wrapper';

        return view($view, ['categoryId' => $category]);
    })->name('create.form')->middleware('auth');

    Route::get('/{advertisement}', function (\App\Models\Advertisement $advertisement) {
        return view('advertisements.show', compact('advertisement'));
    })->name('show');

    Route::get('/{advertisement}/edit', function (\App\Models\Advertisement $advertisement) {
        if (auth()->id() !== $advertisement->user_id) {
            abort(403, 'Nie masz uprawnień do edycji tego ogłoszenia.');
        }

        $advertisementCategory = $advertisement->advertisementCategory;

        // Map category types to appropriate edit view templates
        $viewMap = [
            'adoption' => 'advertisements.forms.adoption-wrapper',
            'sales' => 'advertisements.forms.sales-wrapper',
            'lost_found' => 'advertisements.forms.lost-found-wrapper',
            'supplies' => 'advertisements.forms.supplies-wrapper',
            'services' => 'advertisements.forms.services-wrapper',
        ];

        $view = $viewMap[$advertisementCategory->type] ?? 'advertisements.forms.adoption-wrapper';

        return view($view, ['advertisement' => $advertisement]);
    })->name('edit')->middleware('auth');
});

Route::get('/bookings', function () {
    $view = request('view', 'owner');

    return view('bookings', compact('view'));
})->name('bookings')->middleware('auth');

Route::get('/payment/{booking}', function (\App\Models\Booking $booking) {
    return view('payment.process', compact('booking'));
})->name('payment.process')->middleware('auth');

Route::get('/notifications', function () {
    return view('notifications.center');
})->name('notifications')->middleware('auth');

Route::get('/review/{booking}', function (\App\Models\Booking $booking) {
    return view('review.create', compact('booking'));
})->name('review.create')->middleware('auth');

Route::get('/reviews', function () {
    return view('reviews.index');
})->name('reviews');

Route::get('/reviews/{user}', function (\App\Models\User $user) {
    return view('reviews.user', compact('user'));
})->name('reviews.user');

Route::get('/chat', function () {
    return view('chat.index');
})->name('chat')->middleware('auth');

// Czat w trybie pełnoekranowym (dla embedowania, modali itp.)
Route::get('/chat/fullscreen', function () {
    return view('chat.fullscreen');
})->name('chat.fullscreen')->middleware('auth');

Route::get('/availability', function () {
    return view('availability.calendar');
})->name('availability.calendar')->middleware('auth');

// Map routes
Route::get('/map', function () {
    return view('map.index');
})->name('map.index');

Route::prefix('map')->name('map.')->group(function () {
    Route::get('/data', [MapController::class, 'index'])->name('data');
    Route::get('/stats', [MapController::class, 'stats'])->name('stats');
    Route::get('/categories', [MapController::class, 'categories'])->name('categories');
    Route::get('/near', [MapController::class, 'nearLocation'])->name('near');
    Route::get('/items/{mapItem}', [MapController::class, 'show'])->name('item');
});

// Direct map route test
Route::get('/map-test', function () {
    return 'Direct map route working!';
});

// Simple Livewire test
Route::get('/test-livewire', function () {
    return view('test-livewire');
});


// Professional Services routes
Route::prefix('professional-services')->name('professional-services.')->group(function () {
    Route::get('/', function () {
        return view('professional-services.index');
    })->name('index');

    Route::get('/create', function () {
        return view('professional-services.create');
    })->name('create')->middleware('auth');

    Route::get('/{professionalService}', function (\App\Models\ProfessionalService $professionalService) {
        return view('professional-services.show', compact('professionalService'));
    })->name('show');

    Route::get('/{professionalService}/edit', function (\App\Models\ProfessionalService $professionalService) {
        if (auth()->id() !== $professionalService->user_id) {
            abort(403, 'Nie masz uprawnień do edycji tej usługi.');
        }

        return view('professional-services.edit', compact('professionalService'));
    })->name('edit')->middleware('auth');
});

// Subscription routes
Route::prefix('subscription')->name('subscription.')->group(function () {
    Route::get('/plans', \App\Livewire\Subscription\PricingPage::class)->name('plans');

    Route::middleware('auth')->group(function () {
        Route::get('/dashboard', \App\Livewire\Subscription\Dashboard::class)->name('dashboard');
        Route::get('/checkout/{plan}', \App\Livewire\Subscription\CheckoutForm::class)->name('checkout');
        Route::post('/subscribe/{plan}', [PaymentController::class, 'createSubscriptionPayment'])->name('subscribe');
        Route::get('/payment/form', function () {
            $formData = session('payu_form_data');
            $redirectUrl = session('payu_redirect_url');

            if (!$formData || !$redirectUrl) {
                return redirect()->route('subscription.plans')
                    ->with('error', 'Sesja płatności wygasła. Spróbuj ponownie.');
            }

            // Log session data for debugging
            \Log::info('Payment form accessed', [
                'form_data_keys' => array_keys($formData),
                'redirect_url' => $redirectUrl,
                'session_id' => session()->getId()
            ]);

            return view('subscription.payment-form-standalone', compact('formData', 'redirectUrl'));
        })->name('payment.form');
        Route::get('/payment/success', [PaymentController::class, 'paymentSuccess'])->name('payment.success');
        Route::get('/payment/cancel', [PaymentController::class, 'paymentCancel'])->name('payment.cancel');
        Route::get('/payments/{payment}/status', [PaymentController::class, 'paymentStatus'])->name('payment.status');

        // Faktury
        Route::prefix('invoices')->name('invoices.')->group(function () {
            Route::get('/{payment}/download', [\App\Http\Controllers\InvoiceController::class, 'downloadInvoicePdf'])->name('download');
            Route::post('/{payment}/regenerate', [\App\Http\Controllers\InvoiceController::class, 'regenerateInvoice'])->name('regenerate');
            Route::get('/{payment}/status', [\App\Http\Controllers\InvoiceController::class, 'checkInvoiceStatus'])->name('status');
        });
    });
});

// PayU webhook (no auth required)
Route::post('/payu/notify', [PaymentController::class, 'payuNotification'])->name('payu.notify');

// PayU test route (tylko dev)
Route::get('/payu/test', function () {
    if (config('app.env') !== 'local') {
        abort(404);
    }

    // Wybierz serwis w zależności od konfiguracji API
    $service = config('payu.api_type') === 'classic'
        ? app(\App\Services\PayUClassicService::class)
        : app(\App\Services\PayUService::class);

    $result = $service->testConnection();

    return response()->json($result, $result['success'] ? 200 : 400);
})->name('payu.test');

// InFakt test route (tylko dev)
Route::get('/infakt/test', function () {
    if (config('app.env') !== 'local') {
        abort(404);
    }

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
                'api_key_set' => config('infakt.api_key') !== 'your_api_key_here'
            ]
        ], 500);
    }
})->name('infakt.test');

// InFakt test bez Company ID (nowa wersja API)
Route::get('/infakt/test-no-company', function () {
    if (config('app.env') !== 'local') {
        abort(404);
    }

    $apiKey = config('infakt.api_key');
    if (!$apiKey || $apiKey === 'your_api_key_here') {
        return response()->json([
            'success' => false,
            'error' => 'API Key nie jest skonfigurowany'
        ], 400);
    }

    $environment = config('infakt.environment', 'sandbox');

    // Sprawdźmy różne URL-e dla API InFakt
    $possibleUrls = [
        'https://api.infakt.pl/v3',
        'https://api.sandbox.infakt.pl/v3',
        'https://sandbox.infakt.pl/v3/api',
        'https://infakt.pl/api/v3'
    ];

    $baseUrl = $environment === 'production'
        ? 'https://api.infakt.pl/v3'
        : 'https://api.infakt.pl/v3'; // Może używają tego samego URL?

    try {
        // Test 1: Sprawdź profile użytkownika
        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'X-inFakt-ApiKey' => $apiKey,
            'Accept' => 'application/json'
        ])->get($baseUrl . '/profile.json');

        if ($response->successful()) {
            $profile = $response->json();
            return response()->json([
                'success' => true,
                'message' => 'Połączenie działa! Company ID nie jest potrzebny',
                'environment' => $environment,
                'api_endpoint' => $baseUrl,
                'profile' => [
                    'email' => $profile['email'] ?? 'N/A',
                    'name' => $profile['name'] ?? 'N/A',
                    'company_name' => $profile['company_name'] ?? 'N/A'
                ]
            ]);
        }

        // Test 2: Sprawdź czy można pobrać listę faktur
        $response2 = \Illuminate\Support\Facades\Http::withHeaders([
            'X-inFakt-ApiKey' => $apiKey,
            'Accept' => 'application/json'
        ])->get($baseUrl . '/invoices.json?limit=1');

        if ($response2->successful()) {
            return response()->json([
                'success' => true,
                'message' => 'API działa bez Company ID!',
                'environment' => $environment,
                'api_endpoint' => $baseUrl,
                'invoices_endpoint_works' => true
            ]);
        }

        return response()->json([
            'success' => false,
            'error' => 'API Error: ' . $response->status() . ' - ' . $response->body(),
            'status_code' => $response->status()
        ], 400);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => 'Connection error: ' . $e->getMessage()
        ], 500);
    }
})->name('infakt.test-no-company');
