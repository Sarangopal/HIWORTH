<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TaskController;

// Protected API routes - require authentication
Route::middleware('auth:sanctum')->group(function () {
    // User API routes (admin only - optional)
    Route::apiResource('users', UserController::class);

    // Task API routes - users can only access their own tasks
    Route::apiResource('tasks', TaskController::class);
    Route::patch('tasks/{task}/status', [TaskController::class, 'updateStatus']);
});

