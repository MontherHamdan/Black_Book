<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;

class CountryApiController extends Controller
{
    public function index()
    {
        $countries = Country::all()->map(function ($country) {
            return [
                'id' => $country->id,
                'name' => $country->name,
                'code' => $country->code,
                'dial_code' => $country->dial_code,
                // هاد السطر رح يرجع الرابط كامل للفرونت إيند عشان يشتغل مباشرة عندهم
                'flag_url' => $country->flag_image ? asset($country->flag_image) : null,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $countries
        ]);
    }
}