<?php

namespace App\Http\Controllers;

use App\Models\BookType;
use Illuminate\Http\Request;

class BookTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bookTypes = BookType::all();
        return view('admin.book_types.index', compact('bookTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.book_types.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate incoming request
        $validated = $request->validate([
            'image' => 'required|image|mimes:jpg,png,jpeg,gif,webp|max:2048',
            'price' => 'required|numeric',
            'description_en' => 'nullable|string',
            'description_ar' => 'nullable|string',
        ]);

        // Store the uploaded image in the "book_types" folder under the "public" disk
        $imageFile = $request->file('image');

        $imageName = $imageFile->getClientOriginalName(); // Get original file name
        $imagePath = $imageFile->storeAs('book_types', $imageName, 'public'); // Store image with original name

        // Generate the full URL for the image
        $imageUrl = url('storage/' . $imagePath); // Create a full URL using the stored path

        // Save the image URL and other data in the database
        BookType::create([
            'image' => $imageUrl, // Store the full URL instead of just the path
            'price' => $validated['price'],
            'description_en' => $validated['description_en'],
            'description_ar' => $validated['description_ar'],
        ]);

        // Redirect to the index page with a success message
        return redirect()->route('book-types.index')->with('success', 'Book Type created successfully.');
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BookType $bookType)
    {
        return view('admin.book_types.edit', compact('bookType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BookType $bookType)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'price' => 'required|numeric',
            'description_en' => 'nullable|string',
            'description_ar' => 'nullable|string',
        ]);

        // Check if an image is uploaded
        if ($request->hasFile('image')) {
            // Delete the old image if it exists
            if ($bookType->image) {
                $oldImagePath = str_replace(url('storage') . '/', '', $bookType->image); // Extract the relative path
                if (file_exists(public_path('storage/' . $oldImagePath))) {
                    unlink(public_path('storage/' . $oldImagePath));
                }
            }

            // Store the uploaded image in the "book_types" folder under the "public" disk
            $imageFile = $request->file('image');
            $imageName = $imageFile->getClientOriginalName(); // Get the original file name
            $imagePath = $imageFile->storeAs('book_types', $imageName, 'public'); // Store with original name

            // Generate the full URL for the image
            $imageUrl = url('storage/' . $imagePath);

            // Add the image URL to the validated data
            $validated['image'] = $imageUrl;
        }

        // Update the book type with the validated data
        $bookType->update($validated);

        // Redirect to the index page with a success message
        return redirect()->route('book-types.index')->with('success', 'Book Type updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BookType $bookType)
    {
        // Delete the image from storage if it exists
        if ($bookType->image) {
            $oldImagePath = str_replace(url('storage') . '/', '', $bookType->image); // Extract the relative path

            if (file_exists(public_path('storage/' . $oldImagePath))) {
                unlink(public_path('storage/' . $oldImagePath));
            }
        }

        // Delete the book type record
        $bookType->delete();

        // Redirect to the index page with a success message
        return redirect()->route('book-types.index')->with('success', 'Book Type deleted successfully.');
    }

    public function storeSubMedia(Request $request, BookType $bookType)
    {
        $request->validate([
            'media' => 'required|file|mimes:jpg,jpeg,png,mp4,avi|max:5000',
            'type' => 'required|in:image,video',
        ]);

        // Handle file upload
        $mediaFile = $request->file('media');
        $mediaPath = $mediaFile->storeAs('book_types/sub_media', uniqid() . '.' . $mediaFile->getClientOriginalExtension(), 'public');

        // Store media in the sub-media table
        $bookType->subMedia()->create([
            'media' => url('storage/' . $mediaPath),
            'type' => $request->type,
        ]);

        return redirect()->route('book-types.index')->with('success', 'Sub-media added successfully.');
    }
}
