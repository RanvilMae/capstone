<?php

use App\Http\Controllers\Api\DecisionSupportApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/dss')->group(function () {
    Route::get('/yield-per-plot/{id}', [DecisionSupportApiController::class, 'getYieldPerPlot']);
    Route::get('/sales-prices', [DecisionSupportApiController::class, 'getSalesPrices']);
    Route::post('/calculate-revenue', [DecisionSupportApiController::class, 'calculateRevenueProjection']);
    Route::post('/forecast-production', [DecisionSupportApiController::class, 'forecastProduction']);
});