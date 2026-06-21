<?php

use App\Http\Controllers\ObatController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('verify.login')->group(function () {
    Route::get('/obat', [ObatController::class, 'index']);
    Route::get('/obat/{id}', [ObatController::class, 'show']);
    Route::patch('/obat/{id}/stock', [ObatController::class, 'updatestok']);
    Route::middleware('check.role')->group(function () {
        Route::post('/obat', [ObatController::class, 'store']);
        Route::put('/obat/{id}', [ObatController::class, 'update']);
        Route::delete('/obat/{id}', [ObatController::class, 'destroy']);
    });
});