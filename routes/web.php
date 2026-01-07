<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\UserController;
use App\Http\Controllers\Web\TaskController;
use App\Http\Controllers\Auth\LoginController;

// Authentication routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Public routes
Route::get('/', function () {
    return redirect()->route('login');
});

// Protected routes - require authentication
Route::middleware('auth')->group(function () {
    // User routes (admin only - optional, can be removed if not needed)
    Route::resource('users', UserController::class)->only(['index', 'create', 'store', 'show']);

    // Task routes - users can only access their own tasks
    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::get('/tasks/create', [TaskController::class, 'create'])->name('tasks.create');
    Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::patch('/tasks/{task}/status', [TaskController::class, 'updateStatus'])
        ->name('tasks.updateStatus')
        ->middleware('can:update,task');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])
        ->name('tasks.destroy')
        ->middleware('can:delete,task');
});
