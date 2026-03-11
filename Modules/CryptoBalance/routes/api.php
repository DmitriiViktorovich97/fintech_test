<?php

use Illuminate\Support\Facades\Route;
use Modules\CryptoBalance\Http\Controllers\CryptoBalanceController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('cryptobalances', CryptoBalanceController::class)->names('cryptobalance');
});
