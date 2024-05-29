<?php

use App\Models\Task;
use Illuminate\Support\Facades\Route;

Route::middleware(['cors', 'api',])->group(function () {
    Route::apiResource('/task', Task::class);
});
