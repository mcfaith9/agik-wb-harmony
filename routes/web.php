<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/signin', [AuthController::class, 'signin']);
Route::post('/signup', [AuthController::class, 'signup']);
Route::get('/is-authenticated', [AuthController::class, 'isAuthenticated']);

// Protected routes (only logged in users)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/login-user', [AuthController::class, 'profile']);
});

Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');

