<?php

namespace App\Http\Controllers\Api;

use App\Models\UserImage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserImageController extends Controller
{
    public function store(Request $request)
    {
        // Validate the uploaded file
        $validated = $request->validate([
            'image' => 'required|image|mimes:jpg,png,jpeg,gif,webp|max:2048',
        ]);

        // Retrieve the uploaded image
        $imageFile = $request->file('image');

        // Generate a unique name for the image by appending the current timestamp to the original file name
        $timestamp = time();
        $originalName = $imageFile->getClientOriginalName();
        $imageName = $timestamp . '_' . $originalName;

        // Store the image in the 'user_images' directory within the public storage
        $imagePath = $imageFile->storeAs('user_images', $imageName, 'public');

        // Create a full URL for the stored image
        $imageUrl = url('storage/' . $imagePath);

        // Save the image URL in the database
        $userImage = UserImage::create(['image_path' => $imageUrl]);

        // Return the ID and URL of the uploaded image
        return response()->json([
            'success' => true,
            'message' => 'Image uploaded successfully.',
            'data' => [
                'image_id' => $userImage->id,
            ],
        ]);
    }
}
