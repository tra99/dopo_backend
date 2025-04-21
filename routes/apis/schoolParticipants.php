<?php

use App\Http\Controllers\Api\SchoolParticipantsController;
use Illuminate\Support\Facades\Route;

Route::prefix('/school-participants')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [SchoolParticipantsController::class, 'index']);
    Route::post('/', [SchoolParticipantsController::class, 'store']);
    Route::get('/{id}', [SchoolParticipantsController::class, 'show']);
    Route::patch('/{id}', [SchoolParticipantsController::class, 'update']);
    Route::delete('/{id}', [SchoolParticipantsController::class, 'destroy']);
});
