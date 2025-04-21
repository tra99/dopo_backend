<?php

use App\Http\Controllers\Api\ParticipantController;
use Illuminate\Support\Facades\Route;

Route::prefix('participants')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [ParticipantController::class, 'index']);
    Route::get('/{id}', [ParticipantController::class, 'show']);
    Route::post('/', [ParticipantController::class, 'store']);
    Route::post('/import', [ParticipantController::class, 'storeWithImport']);
    Route::patch('/{id}', [ParticipantController::class, 'update']);
    Route::delete('/{id}', [ParticipantController::class, 'destroy']);
});
