<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address;

class AddressController extends Controller
{
    public function getAddressesByGovernorate($id)
    {
        $addresses = Address::where('governorate_id', $id)->get();

        return response()->json(['status' => 'success', 'data' => $addresses]);
    }
}
