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
    return view('dashboard');
})->name('dashboard')->middleware('auth');

Route::get('/pets', function () {
    return view('pets.index');
})->name('pets.index')->middleware('auth');

Route::get('/pets/create', function () {
    return view('pets.create');
})->name('pets.create')->middleware('auth');

Route::get('/pets/{pet}/edit', function (\App\Models\Pet $pet) {
    return view('pets.edit', compact('pet'));
})->name('pets.edit')->middleware('auth');

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

// Subscription routes
Route::prefix('subscription')->name('subscription.')->group(function () {
    Route::get('/plans', \App\Livewire\Subscription\PricingPage::class)->name('plans');

    Route::middleware('auth')->group(function () {
        Route::get('/dashboard', \App\Livewire\Subscription\Dashboard::class)->name('dashboard');
        Route::post('/subscribe/{plan}', [PaymentController::class, 'createSubscriptionPayment'])->name('subscribe');
        Route::get('/payment/success', [PaymentController::class, 'paymentSuccess'])->name('payment.success');
        Route::get('/payment/cancel', [PaymentController::class, 'paymentCancel'])->name('payment.cancel');
        Route::get('/payments/{payment}/status', [PaymentController::class, 'paymentStatus'])->name('payment.status');
    });
});

// PayU webhook (no auth required)
Route::post('/payu/notify', [PaymentController::class, 'payuNotification'])->name('payu.notify');
