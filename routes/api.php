<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\MetricsController;
use Illuminate\Support\Facades\Route;

// Metrics for Prometheus (Internal)
Route::get('/metrics', [MetricsController::class, 'index']);
Route::get('/health', function () {
    try {
        \DB::connection()->getPdo();

        return response()->json(['status' => 'ok', 'database' => 'connected']);
    } catch (\Exception $e) {
        return response()->json(['status' => 'error', 'database' => 'disconnected'], 500);
    }
});

// 5 tentatives par minutes pour une adresse ip
Route::post('/auth/login', [AuthController::class, 'login'])->middleware('throttle:5,1');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/auth/refresh', [AuthController::class, 'refresh']);
});

// Resources protected by auth and general rate limit
Route::middleware('throttle:api')->group(function () {
    require __DIR__.'/api/abonne.php';
    require __DIR__.'/api/facture.php';
});
