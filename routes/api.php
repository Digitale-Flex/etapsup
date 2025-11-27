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
