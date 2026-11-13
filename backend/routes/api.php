<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PartController;

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