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

$missingTaskHandler = function (Request $request) {
    return response()->json([
        'message' => 'Task not found.'
    ], 404);
};

Route::middleware(['api', 'auth:sanctum'])->group(function () use($missingTaskHandler) {
    Route::get('/tasks', [TaskController::class, 'index']);
    Route::get('/tasks/tree/{task}', [TaskController::class, 'getUserTaskTree'])->missing($missingTaskHandler)->middleware('can:view,task');
    Route::get('/tasks/filtered', [TaskController::class, 'getFilteredCollection']);
    Route::get('/tasks/sorted', [TaskController::class, 'getSortedCollection']);
    Route::get('/tasks/{task}', [TaskController::class, 'show'])->missing($missingTaskHandler)->middleware('can:view,task');
    Route::post('/tasks', [TaskController::class, 'store']);
    Route::put('/tasks/{task}', [TaskController::class, 'update'])->missing($missingTaskHandler)->middleware('can:update,task');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->missing($missingTaskHandler)->middleware('can:delete,task');
    Route::patch('/tasks/done/{task}', [TaskController::class, 'markTaskDone'])->missing($missingTaskHandler)->middleware('can:update,task');
});
