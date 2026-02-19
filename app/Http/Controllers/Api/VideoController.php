<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class VideoController extends Controller
{
    public function index()
    {
        $videos = Video::latest()->get();
        return response()->json(['success' => true, 'data' => $videos], 200);
    }


    public function show($id)
    {
        $video = Video::find($id);

        if (!$video) {
            return response()->json(['success' => false, 'message' => 'Video not found.'], 404);
        }

        return response()->json(['success' => true, 'data' => $video], 200);
    }





}