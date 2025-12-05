<?php

namespace App\Http\Controllers\Api;

use App\Models\UserImage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserImageController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'image' => 'nullable|image|mimes:jpg,png,jpeg,gif,webp|max:20480',
            'images.*' => 'nullable|image|mimes:jpg,png,jpeg,gif,webp|max:20480',
        ]);

        // لو كانت SINGLE
        if ($request->hasFile('image')) {
            $imageFile = $request->file('image');
            $imageName = time() . '_' . $imageFile->getClientOriginalName();
            $imagePath = $imageFile->storeAs('user_images', $imageName, 'public');

            $userImage = UserImage::create([
                'image_path' => $imageName
            ]);

            return response()->json([
                'success' => true,
                'type' => 'single',
                'message' => 'Image uploaded successfully.',
                'data' => [
                    'image_id' => $userImage->id,
                    'image_name' => $imageName,
                ],
            ]);
        }

        // لو كانت MULTIPLE
        $uploadedImages = [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $img) {
                $imageName = time() . '_' . $img->getClientOriginalName();
                $img->storeAs('user_images', $imageName, 'public');

                $userImage = UserImage::create([
                    'image_path' => $imageName
                ]);

                $uploadedImages[] = [
                    'image_id' => $userImage->id,
                    'image_name' => $imageName
                ];
            }

            return response()->json([
                'success' => true,
                'type' => 'multiple',
                'message' => 'Images uploaded successfully.',
                'data' => $uploadedImages
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No images found in request.'
        ], 400);
    }
}
