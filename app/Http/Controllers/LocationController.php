<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Country;
use App\Models\Region;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LocationController extends Controller
{
    public function getCountries(): JsonResponse
    {
        $countries = Country::select('id', 'name')->get();
        return response()->json($countries);
    }

    public function getCities(Region $region): JsonResponse
    {
        $cacheKey = "cities_region_{$region->id}";

        $cities = Cache::remember($cacheKey, now()->addHours(24), function () use ($region) {
            return $region->cities()
                ->select('id', 'name')
                ->orderBy('name')
                ->get();
        });

        return response()->json($cities);
    }
}
