<?php

use App\Http\Controllers\ExpenseController;
use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => ['auth:api', 'throttle:60,1']
], function () {
    Route::apiResource('expenses', ExpenseController::class)
        ->except('show');
});
