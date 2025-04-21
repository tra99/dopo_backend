<?php

use App\Http\Controllers\Api\CategoryController;
use Illuminate\Support\Facades\Route;

Route::prefix('categories')->middleware('auth:sanctum')->group(function () {
  Route::get('/', [CategoryController::class, 'index']);
  Route::post('/', [CategoryController::class, 'store']);
  Route::get('/{id}', [CategoryController::class, 'show']);
  Route::patch('/{id}', [CategoryController::class, 'update']);
  Route::delete('/{id}', [CategoryController::class, 'destroy']);
  Route::get('parent/by-type', [CategoryController::class, 'getParentByType']);
});
