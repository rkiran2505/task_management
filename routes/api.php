<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'login']);

// Logout Route (Requires authentication)
Route::middleware('auth:sanctum')->post('logout', [UserController::class, 'logout']);

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('tasks')->group(function () {
        // Get all tasks (GET request)
        Route::get('list', [TaskController::class, 'index']);
        
        // Create a new task (POST request)
        Route::post('add', [TaskController::class, 'store']);
        
        // Update a specific task (PUT request)
        Route::post('edit', [TaskController::class, 'update']);
        
        // Delete a specific task (DELETE request)
        Route::delete('delete/{task}', [TaskController::class, 'destroy']);
    });
});