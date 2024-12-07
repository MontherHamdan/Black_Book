<?php

namespace App\Http\Controllers\Api;

use App\Models\Diploma;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DiplomaController extends Controller
{
    public function index()
    {
        // Fetch and return all diplomas
        return response()->json(Diploma::all(), 200);
    }
}
