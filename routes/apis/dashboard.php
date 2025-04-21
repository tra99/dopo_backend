<?php

use App\Http\Controllers\Api\DashboardController;
use Illuminate\Support\Facades\Route;

Route::prefix('dashboards')->group(function () {
    Route::get('/tree', [DashboardController::class, 'getDashboardTree']);
    Route::get('/children', [DashboardController::class, 'getChildDashboards']);
    Route::get('/parents', [DashboardController::class, 'getParentDashboards']);
    Route::post('/', [DashboardController::class, 'store']);
    Route::get('/{id}', [DashboardController::class, 'show']);
    Route::patch('/{id}', [DashboardController::class, 'update']);
    Route::delete('/{id}', [DashboardController::class, 'destroy']);
});
