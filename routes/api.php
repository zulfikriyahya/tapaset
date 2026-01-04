<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RfidController;

Route::prefix('rfid')->group(function () {
    Route::post('/tap', [RfidController::class, 'tap']);
    Route::post('/loan', [RfidController::class, 'loan']);
    Route::post('/return', [RfidController::class, 'return']);
});
