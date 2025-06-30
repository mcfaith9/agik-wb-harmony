<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskListController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\HealthController;
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
        Route::get('/projects/budget', [ProjectController::class, 'projectBudgetOverview']);
        Route::get('/budget/summary', [ProjectController::class, 'budgetSummary']);

        Route::resource('projects', ProjectController::class);
        Route::resource('projects.tasklists', TaskListController::class);
        Route::resource('tasks', TaskController::class);

        // Tags
        Route::get('/tags', [TagController::class, 'index']);
        Route::put('/tags/{id}', [TagController::class, 'update']);

        // Comment
        Route::get('/comments/{type}/{id}', [CommentController::class, 'index']);
        Route::post('/comments/{type}/{id}', [CommentController::class, 'store']);

        // Users
        Route::get('/app/users', [UserController::class, 'index']);
        Route::get('/user/badges', function () {
            return auth()->user()->badges()->get();
        });

        // Teams
        Route::resource('teams', TeamController::class);
        Route::post('/teams/{team}/add-user', [TeamController::class, 'addUser']);
        Route::post('/teams/{team}/remove-user', [TeamController::class, 'removeUser']);

        // Admin
        Route::get('/roles', [RoleController::class, 'index']);
        Route::post('/roles', [RoleController::class, 'store']);
        Route::put('/roles/{role}', [RoleController::class, 'update']);
        Route::post('/users/{user}/assign-role', [RoleController::class, 'assign']);
        Route::post('/users/{user}/unassign-role', [RoleController::class, 'unassign']);
        Route::get('/users/{user}/roles', [RoleController::class, 'userRoles']);
        Route::get('/admin/users/list', function () {
            return \App\Models\User::with('roles:id,name')
                ->select('id', 'first_name', 'last_name', 'email', 'phone', 'email_verified_at')
                ->paginate(request('per_page', 10));
        });
        Route::delete('/roles/{role}', [RoleController::class, 'destroy']);

        // Project Health Dashboard
        Route::prefix('/admin/health')->controller(HealthController::class)->group(function () {
            Route::get('/burnout', 'burnout');
            Route::get('/delayed-tasks', 'delayedTasks');
            Route::get('/productivity-drop', 'productivityDrop');
            Route::get('/over-assignment', 'overAssignment');
        });

        // Settings
        Route::prefix('/admin/settings')->group(function () {
            Route::get('/', [SettingController::class, 'index']);
            Route::put('/{key}', [SettingController::class, 'update']);
        });

        // Gamify
        Route::get('/leaderboard/teams', [LeaderboardController::class, 'teamsLeaderboard']);
        Route::get('/leaderboard/users', [LeaderboardController::class, 'index']);        
        Route::get('/badges', function () {
            return App\Models\Badge::all();
        });
    });
});

Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');

