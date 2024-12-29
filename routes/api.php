<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'login']);

Route::middleware('auth:sanctum')->post('logout', [UserController::class, 'logout']);

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('tasks')->group(function () {
        Route::get('list', [TaskController::class, 'index']);
        Route::post('add', [TaskController::class, 'store']);
        Route::post('edit/{task_id}', [TaskController::class, 'update']);
        Route::delete('delete/{task_id}', [TaskController::class, 'destroy']);
        Route::get('show', [TaskController::class, 'show']);
    });
});