<?php

use App\Http\Controllers\Api\AbonneController;
use Illuminate\Support\Facades\Route;

Route::post('/abonnes', [AbonneController::class, 'store']);
Route::put('/abonnes/{id}', [AbonneController::class, 'update']);
Route::delete('/abonnes/{id}', [AbonneController::class, 'destroy']);
Route::get('/abonnes', [AbonneController::class, 'index']);
Route::get('/abonnes/{id}', [AbonneController::class, 'show']);
