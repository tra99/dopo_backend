<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CertificateController;

Route::prefix('certificates')
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::get('/', [CertificateController::class, 'index']);
        // Route::get('/', function () {
        //     return "hehlo";
        // });
        Route::get('/{certificate}', [CertificateController::class, 'show']);
        Route::post('/', [CertificateController::class, 'store']);
        Route::patch('/{certificate}', [CertificateController::class, 'update']);
        Route::delete('/{certificate}', [CertificateController::class, 'destroy']);
    });
