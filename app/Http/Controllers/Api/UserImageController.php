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
            'image' => 'required|image|mimes:jpg,png,jpeg,gif,webp|max:20480',
        ]);
    
        // Retrieve the uploaded image
        $imageFile = $request->file('image');
    
        // Generate a unique name for the image by appending the current timestamp to the original file name
        $timestamp = time();
        $originalName = $imageFile->getClientOriginalName();
        $imageName = $timestamp . '_' . $originalName;
    
        // Store the image in the 'user_images' directory within the public storage
        $imagePath = $imageFile->storeAs('user_images', $imageName, 'public');
    
        // Save the image path in the database (without full URL)
        $userImage = UserImage::create(['image_path' => $imageName]);
    
        // Return the ID and image name of the uploaded image
        return response()->json([
            'success' => true,
            'message' => 'Image uploaded successfully.',
            'data' => [
                'image_id' => $userImage->id,
                'image_name' => $imageName, // Send back image name for frontend usage
            ],
        ]);
    }
}
