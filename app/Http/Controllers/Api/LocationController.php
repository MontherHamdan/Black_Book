<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Governorate;
use App\Models\City;
use App\Models\Area;

class LocationController extends Controller
{
    public function getGovernorates()
    {
        // Return only the provinces associated with the delivery company
        $governorates = Governorate::whereNotNull('logestechs_id')->get(['id', 'name_ar', 'name_en']);
        return response()->json(['success' => true, 'data' => $governorates]);
    }

    public function getCities($governorateId)
    {
        $cities = City::where('governorate_id', $governorateId)
                      ->whereNotNull('logestechs_id')
                      ->get(['id', 'name_ar', 'name_en']);
        return response()->json(['success' => true, 'data' => $cities]);
    }

    public function getAreas($cityId)
    {
        $areas = Area::where('city_id', $cityId)
                     ->whereNotNull('logestechs_id')
                     ->get(['id', 'name_ar', 'name_en']);
        return response()->json(['success' => true, 'data' => $areas]);
    }
}