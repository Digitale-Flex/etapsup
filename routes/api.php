<?php

use App\Models\Certificate\Partner;
use App\Rules\ValidateHashid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Vinkla\Hashids\Facades\Hashids;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/countries', [\App\Http\Controllers\LocationController::class, 'getCountries']);
Route::get('/cities/{region}', [\App\Http\Controllers\LocationController::class, 'getCities']);

// Sprint1 Feature 1.8.1 — Gestion des documents de candidature
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::get('/documents', [\App\Http\Controllers\Api\DocumentController::class, 'index']);
    Route::post('/documents', [\App\Http\Controllers\Api\DocumentController::class, 'store']);
    Route::delete('/documents/{document}', [\App\Http\Controllers\Api\DocumentController::class, 'destroy']);
    Route::get('/documents/{document}/download', [\App\Http\Controllers\Api\DocumentController::class, 'download']);
});
