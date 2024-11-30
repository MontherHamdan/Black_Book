<?php

namespace App\Http\Controllers;

use App\Models\BookType;
use App\Models\BookTypeSubMedia;
use Illuminate\Http\Request;

class BookTypeSubMediaController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'book_type_id' => 'required|exists:book_types,id',
            'media' => 'required|file|mimes:jpeg,png,jpg,gif,webp,mp4|max:10240',
            'type' => 'required|in:image,video',
        ]);

        $imageFile = $request->file('media');
        $imageName = $imageFile->getClientOriginalName(); // Get original file name
        $imagePath = $imageFile->storeAs('book_types/sub_media', $imageName, 'public'); // Store image with original name

        BookTypeSubMedia::create([
            'book_type_id' => $validated['book_type_id'],
            'media' => url('storage/' . $imagePath),
            'type' => $validated['type'],
        ]);

        return back()->with('success', 'Sub Media added successfully.');
    }

    public function destroy(BookTypeSubMedia $subMedia)
    {
        $filePath = str_replace(url('storage') . '/', '', $subMedia->media);
        if (file_exists(public_path('storage/' . $filePath))) {
            unlink(public_path('storage/' . $filePath));
        }

        $subMedia->delete();

        return back()->with('success', 'Sub Media deleted successfully.');
    }
}
