<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('verify.login')->group(function () {


    Route::get('orders/{id}', [OrderController::class, 'show']);
    Route::get('orders/user/{id}', [OrderController::class, 'getByUser']);
    Route::post('orders', [OrderController::class, 'store']);

    Route::middleware('check.role')->group(function () {
        Route::patch('orders/{id}/status', [OrderController::class, 'updateStatus']);
        Route::put('orders/{id}', [OrderController::class, 'update']);
        Route::delete('orders/{id}', [OrderController::class, 'destroy']);
        Route::get('orders', [OrderController::class, 'index']);
    });

});
