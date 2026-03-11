<?php

use Illuminate\Support\Facades\Route;
use Modules\CryptoBalance\Http\Controllers\CryptoBalanceController;

//Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('cryptobalances', CryptoBalanceController::class)->names('cryptobalance');

    Route::post('/balance/credit', [CryptoBalanceController::class, 'credit'])
        ->name('cryptobalance.credit');

    Route::post('/balance/debit', [CryptoBalanceController::class, 'debit'])
        ->name('cryptobalance.debit');
//});
