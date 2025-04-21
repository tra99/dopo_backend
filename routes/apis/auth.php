<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'auth'], function () {
  Route::post('login', [AuthController::class, 'login']);
  Route::post('register', [AuthController::class, 'register']);

  Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('logout', [AuthController::class, 'logout']);
    Route::patch('update', [AuthController::class, 'update']);
    Route::patch('update-password', [AuthController::class, 'updatePassword']);
    Route::get('user', [AuthController::class, 'user']);
    Route::post('refresh-token', [AuthController::class, 'refreshToken']);
  });
});
