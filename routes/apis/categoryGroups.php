<?php

use App\Http\Controllers\Api\CategoryGroupsController;
use Illuminate\Support\Facades\Route;

Route::prefix('category-groups')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [CategoryGroupsController::class, 'index']);
    Route::post('/', [CategoryGroupsController::class, 'store']);
    Route::get('/{id}', [CategoryGroupsController::class, 'show']);
    Route::patch('/{id}', [CategoryGroupsController::class, 'update']);
    Route::delete('/{id}', [CategoryGroupsController::class, 'destroy']);
});
