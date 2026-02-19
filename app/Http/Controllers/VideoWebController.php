<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VideoWebController extends Controller
{
    public function index()
    {
        $videos = Video::orderByDesc('id')->get();
        return view('admin.videos.index', compact('videos'));
    }

    public function create()
    {
        return view('admin.videos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'video' => 'required|file|mimes:mp4,mov,avi,wmv,webm|max:102400',
        ]);

        $videoPath = $request->file('video')->store('videos', 'public');

        Video::create([
            'title' => $request->title,
            'description' => $request->description,
            'video' => $videoPath,
        ]);

        return redirect()->route('videos.index')->with('success', 'Video uploaded successfully.');
    }

    public function edit(Video $video)
    {
        return view('admin.videos.edit', compact('video'));
    }

    public function update(Request $request, Video $video)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'video' => 'nullable|file|mimes:mp4,mov,avi,wmv,webm|max:102400',
        ]);

        $data = [
            'title' => $request->title,
            'description' => $request->description,
        ];

        if ($request->hasFile('video')) {
            // Delete old video
            if ($video->video) {
                Storage::disk('public')->delete($video->video);
            }
            $data['video'] = $request->file('video')->store('videos', 'public');
        }

        $video->update($data);

        return redirect()->route('videos.index')->with('success', 'Video updated successfully.');
    }

    public function destroy(Video $video)
    {
        // Delete the video file
        if ($video->video) {
            Storage::disk('public')->delete($video->video);
        }

        $video->delete();
        return redirect()->route('videos.index')->with('success', 'Video deleted successfully.');
    }
}
