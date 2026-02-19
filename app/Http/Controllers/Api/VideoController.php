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

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'video' => 'required|file|mimetypes:video/mp4,video/avi,video/mpeg,video/quicktime|max:51200', // الحد الأقصى 50MB
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        if ($request->hasFile('video')) {
            $data['video'] = $request->file('video')->store('videos', 'public');
        }

        $video = Video::create($data);

        return response()->json(['success' => true, 'message' => 'Video created successfully.', 'data' => $video], 201);
    }

    public function show($id)
    {
        $video = Video::find($id);

        if (!$video) {
            return response()->json(['success' => false, 'message' => 'Video not found.'], 404);
        }

        return response()->json(['success' => true, 'data' => $video], 200);
    }

    public function update(Request $request, $id)
    {
        $video = Video::find($id);

        if (!$video) {
            return response()->json(['success' => false, 'message' => 'Video not found.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'video' => 'nullable|file|mimetypes:video/mp4,video/avi,video/mpeg,video/quicktime|max:51200',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        if ($request->hasFile('video')) {
            if ($video->video && Storage::disk('public')->exists($video->video)) {
                Storage::disk('public')->delete($video->video);
            }
            $data['video'] = $request->file('video')->store('videos', 'public');
        }

        $video->update($data);

        return response()->json(['success' => true, 'message' => 'Video updated successfully.', 'data' => $video], 200);
    }

    public function destroy($id)
    {
        $video = Video::find($id);

        if (!$video) {
            return response()->json(['success' => false, 'message' => 'Video not found.'], 404);
        }

        if ($video->video && Storage::disk('public')->exists($video->video)) {
            Storage::disk('public')->delete($video->video);
        }

        $video->delete();

        return response()->json(['success' => true, 'message' => 'Video deleted successfully.'], 200);
    }
}