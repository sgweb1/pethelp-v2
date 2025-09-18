<?php

use Illuminate\Support\Facades\Route;

// Debug route to test CSRF
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