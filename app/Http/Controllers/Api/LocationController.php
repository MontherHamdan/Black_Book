<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\City;
use App\Models\Country;
use App\Models\Governorate;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function getCountries()
    {
        $countries = Country::where('is_active', true)
            ->get(['id', 'name', 'code', 'dial_code', 'flag_image']);

        return response()->json(['success' => true, 'data' => $countries]);
    }

    public function getGovernorates(Request $request)
    {
        $query = Governorate::whereNotNull('logestechs_id')
            ->where('is_active', true);

        if ($request->has('country_id')) {
            $query->where('country_id', $request->country_id);
        }

        $governorates = $query->get(['id', 'name_ar', 'name_en', 'country_id']);

        return response()->json(['success' => true, 'data' => $governorates]);
    }

    public function getCities($governorateId)
    {
        $cities = City::where('governorate_id', $governorateId)
            ->whereNotNull('logestechs_id')
            ->where('is_active', true)
            ->get(['id', 'name_ar', 'name_en']);

        return response()->json(['success' => true, 'data' => $cities]);
    }

    public function getAreas($cityId)
    {
        $areas = Area::where('city_id', $cityId)
            ->whereNotNull('logestechs_id')
            ->where('is_active', true)
            ->get(['id', 'name_ar', 'name_en']);

        return response()->json(['success' => true, 'data' => $areas]);
    }
}
