<?php

use App\Http\Controllers\Api\WidgetController;
use Illuminate\Support\Facades\Route;

Route::prefix('widgets')->group(function () {
    Route::get('/', [WidgetController::class, 'index']);
    Route::post('/', [WidgetController::class, 'store']);
    Route::get('/{id}', [WidgetController::class, 'show']);
    Route::patch('/', [WidgetController::class, 'updateMultiple']);
    Route::patch('/{id}', [WidgetController::class, 'update']);
    Route::delete('/{id}', [WidgetController::class, 'destroy']);
});
