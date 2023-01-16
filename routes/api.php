<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CurrencyController;
use App\Http\Controllers\Api\ExchangeRateController;
use App\Http\Controllers\Api\ExchangeRateReportController;
use App\Http\Controllers\Api\RegisterController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')
    ->post('register', RegisterController::class)
    ->name('register');

Route::controller(AuthController::class)
    ->name('auth.')
    ->group(function () {
        Route::post('login', 'store')->middleware('guest')->name('login');
        Route::delete('logout', 'destroy')->middleware('auth:sanctum')->name('logout');
    });

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/currencies', CurrencyController::class)
        ->name('currencies');

    Route::post('/exchange-rates', ExchangeRateController::class)
        ->name('exchange-rates');

    Route::controller(ExchangeRateReportController::class)
        ->prefix('reports')
        ->name('exchange-rate-reports.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('{report}', 'show')->name('show')->can('view', 'report');
            Route::post('/', 'store')->name('store');
        });
});
