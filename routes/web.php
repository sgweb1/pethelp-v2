<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SitterController;

Route::get('/', function () {
    return view('welcome');
});

// Test CSRF
Route::get('/test-csrf', function () {
    return view('test-csrf');
});

Route::post('/test-csrf', function () {
    return 'CSRF działa poprawnie!';
})->name('test-csrf');

Route::get('/search', function () {
    return view('search');
})->name('search');

Route::get('/sitter/{sitter}', [SitterController::class, 'show'])->name('sitter.show');

Route::get('/booking/{service}', function(\App\Models\Service $service) {
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

Route::get('/bookings', function () {
    $view = request('view', 'owner');
    return view('bookings', compact('view'));
})->name('bookings')->middleware('auth');

Route::get('/payment/{booking}', function(\App\Models\Booking $booking) {
    return view('payment.process', compact('booking'));
})->name('payment.process')->middleware('auth');

Route::get('/notifications', function () {
    return view('notifications.center');
})->name('notifications')->middleware('auth');

Route::get('/review/{booking}', function(\App\Models\Booking $booking) {
    return view('review.create', compact('booking'));
})->name('review.create')->middleware('auth');

Route::get('/reviews', function () {
    return view('reviews.index');
})->name('reviews');

Route::get('/reviews/{user}', function(\App\Models\User $user) {
    return view('reviews.user', compact('user'));
})->name('reviews.user');

Route::get('/chat', function () {
    return view('chat.index');
})->name('chat')->middleware('auth');

Route::get('/availability', function () {
    return view('availability.calendar');
})->name('availability.calendar')->middleware('auth');

// Debug routes for CSRF testing
Route::get('/test-csrf', function () {
    return view('test-csrf');
});

Route::post('/test-csrf', function (\Illuminate\Http\Request $request) {
    return response()->json([
        'success' => true,
        'token' => csrf_token(),
        'session_id' => session()->getId(),
        'data' => $request->all()
    ]);
})->name('test-csrf');