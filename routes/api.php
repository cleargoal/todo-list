<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::middleware(['auth:sanctum',])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/auth-user', [AuthController::class, 'authUser']);
});

Route::middleware(['api', 'auth:sanctum',])->group(function () {
    Route::apiResource('/tasks', TaskController::class)
        ->missing(function (Request $request) {
            return response()->json('Record not found.', 404);
        });
});
