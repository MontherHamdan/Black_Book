<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Governorate;

class GovernorateController extends Controller
{
    public function index()
    {
        $governorates = Governorate::all();

        return response()->json(['status' => 'success', 'data' => $governorates]);
    }
}
