<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PartController;
use App\Http\Controllers\CompanyController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/ping', function () {
    return [
        'pong' => true,
        'php' => PHP_VERSION,
        'time' => now()->toDateTimeString(),
    ];
});

Route::apiResource('parts', PartController::class);

Route::apiResource('companies', CompanyController::class);

// Stock de una pieza dentro de una empresa ("en la empresa X, la pieza Y").
Route::put('companies/{company}/parts/{part}', [CompanyController::class, 'updateStock']);
Route::delete('companies/{company}/parts/{part}', [CompanyController::class, 'removePart']);
