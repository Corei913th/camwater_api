<?php

use App\Http\Controllers\Api\FactureController;
use Illuminate\Support\Facades\Route;

// Factures
Route::get('/factures', [FactureController::class, 'index']);
Route::post('/factures/generer', [FactureController::class, 'generer']);
Route::get('/factures/{id}', [FactureController::class, 'show']);
