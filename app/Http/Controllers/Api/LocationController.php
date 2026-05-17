<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\City;
use App\Models\Country;
use App\Models\Governorate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LocationController extends Controller
{
    public function getCountries()
    {
        $countries = Cache::remember('countries', 86400, function () {
            return Country::where('is_active', true)->get(['id', 'name', 'code', 'dial_code', 'flag_image']);
        });

        return response()->json(['success' => true, 'data' => $countries]);
    }

    public function getGovernorates(Request $request)
    {
        $countryId = $request->input('country_id');
        $cacheKey = $countryId ? "location_governorates_{$countryId}" : 'location_governorates';

        $governorates = Cache::remember($cacheKey, 86400, function () use ($countryId) {
            $query = Governorate::whereNotNull('logestechs_id')->where('is_active', true);
            if ($countryId) {
                $query->where('country_id', $countryId);
            }
            return $query->get(['id', 'name_ar', 'name_en', 'country_id']);
        });

        return response()->json(['success' => true, 'data' => $governorates]);
    }

    public function getCities(int $governorateId)
    {
        $cities = Cache::remember("cities_{$governorateId}", 86400, function () use ($governorateId) {
            return City::where('governorate_id', $governorateId)
                ->whereNotNull('logestechs_id')
                ->where('is_active', true)
                ->get(['id', 'name_ar', 'name_en']);
        });

        return response()->json(['success' => true, 'data' => $cities]);
    }

    public function getAreas(int $cityId)
    {
        $areas = Cache::remember("areas_{$cityId}", 86400, function () use ($cityId) {
            return Area::where('city_id', $cityId)
                ->whereNotNull('logestechs_id')
                ->where('is_active', true)
                ->get(['id', 'name_ar', 'name_en']);
        });

        return response()->json(['success' => true, 'data' => $areas]);
    }
}
