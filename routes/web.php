<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function () {
    Route::post('/signup', [AuthController::class, 'signup']);
});

Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');

