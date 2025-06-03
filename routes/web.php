<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskListController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;


// Public routes
Route::post('/signin', [AuthController::class, 'signin']);
Route::post('/signup', [AuthController::class, 'signup']);
Route::get('/is-authenticated', [AuthController::class, 'isAuthenticated']);

// Protected routes (only logged in users)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/login-user', [AuthController::class, 'profile']);

    Route::post('/user/address', [ProfileController::class, 'userAddress']);
    Route::get('/user/address/show', [ProfileController::class, 'show']);    

    Route::prefix('api')->group(function () {
        Route::resource('projects', ProjectController::class);
        Route::resource('projects.tasklists', TaskListController::class);
        Route::resource('tasks', TaskController::class);

        Route::get('/tags', [TagController::class, 'index']);
        Route::put('/tags/{id}', [TagController::class, 'update']);

        Route::get('/users', function () {
            return \App\Models\User::select('id', 'first_name', 'last_name')->get()->map(function ($user) {
                $user->name = $user->first_name . ' ' . $user->last_name;
                $user->avatar = 'https://ui-avatars.com/api/?name=' . urlencode($user->name);
                return $user;
            });
        });

        Route::get('/admin/users/list', function () {
            return \App\Models\User::select('id', 'first_name', 'last_name', 'email', 'phone', 'email_verified_at')
                ->paginate(request('per_page', 10));
        });
    });
});

Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');

