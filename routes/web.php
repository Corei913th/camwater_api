<?php

use App\Http\Controllers\WebController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [WebController::class, 'showLogin'])->name('login');
Route::post('/login', [WebController::class, 'login'])->name('web.login.post');
Route::post('/logout', [WebController::class, 'logout'])->name('web.logout');

Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return redirect()->route('web.dashboard');
    });

    Route::prefix('admin')->group(function () {
        Route::get('/dashboard', [WebController::class, 'dashboard'])->name('web.dashboard');
        Route::get('/abonnes', [WebController::class, 'abonnesIndex'])->name('web.abonnes.index');
        Route::get('/abonnes/nouveau', [WebController::class, 'abonnesCreate'])->name('web.abonnes.create');
        Route::post('/abonnes', [WebController::class, 'abonnesStore'])->name('web.abonnes.store');

        Route::get('/factures', [WebController::class, 'facturesIndex'])->name('web.factures.index');
        Route::get('/factures/nouvelle', [WebController::class, 'facturesCreate'])->name('web.factures.create');
        Route::post('/factures', [WebController::class, 'facturesStore'])->name('web.factures.store');
        Route::get('/factures/{id}', [WebController::class, 'facturesShow'])->name('web.factures.show');
    });
});
