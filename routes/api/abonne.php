<?php

use App\Http\Controllers\Api\AbonneController;
use Illuminate\Support\Facades\Route;

// Accès conditioné par le middleware role
Route::middleware('role:ADMIN')->group(function () {
    Route::post('/abonnes', [AbonneController::class, 'store']);
    Route::put('/abonnes/{id}', [AbonneController::class, 'update']);
    Route::delete('/abonnes/{id}', [AbonneController::class, 'destroy']);
});

// Routes protégés juste par je JWT
Route::get('/abonnes', [AbonneController::class, 'index']);
Route::get('/abonnes/{id}', [AbonneController::class, 'show']);
