<?php

namespace App\Http\Controllers;

use App\Models\BookDesign;
use Illuminate\Http\Request;
use App\Models\BookDesignCategory;
use App\Models\BookDesignSubCategory;
use Illuminate\Support\Facades\Storage;

class BookDesignController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bookDesigns = BookDesign::with(['category', 'subCategory'])->get();

        return view('admin.bookDesign.index', compact('bookDesigns'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = BookDesignCategory::all();
        return view('admin.bookDesign.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate incoming request
        $validated = $request->validate([
            'image' => 'required|image|mimes:jpg,png,jpeg,gif|max:2048',
            'category_id' => 'required|exists:book_design_categories,id',
            'sub_category_id' => 'nullable|exists:book_design_sub_categories,id',
        ]);

        // Store the uploaded image in the "book_designs" folder under the "public" disk
        $imageFile = $request->file('image');
        $imageName = $imageFile->getClientOriginalName(); // Get original file name
        $imagePath = $imageFile->storeAs('book_designs', $imageName, 'public'); // Store image with original name

        // Generate the full URL for the image
        $imageUrl = url('storage/' . $imagePath);  // Create a full URL using the stored path

        // Handle sub_category_id properly if it's present
        $subCategoryId = $validated['sub_category_id'] ?? null;

        // Save the image URL and other data in the database
        BookDesign::create([
            'image' => $imageUrl, // Store the full URL instead of just the path
            'category_id' => $validated['category_id'],
            'sub_category_id' => $subCategoryId,
        ]);

        // Redirect to the index page with a success message
        return redirect()->route('book-designs.index')->with('success', 'Book Design created successfully.');
    }


    /**
     * Display the specified resource.
     */
    public function show(BookDesign $bookDesign)
    {
        $bookDesign->load(['category', 'subCategory']);

        return view('admin.bookDesign.show', compact('bookDesign'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BookDesign $bookDesign)
    {
        // Get all categories and subcategories related to the bookDesign's category
        $categories = BookDesignCategory::all();
        $subCategories = BookDesignSubCategory::where('category_id', $bookDesign->category_id)->get();

        // Return the edit view with the necessary data
        return view('admin.bookDesign.edit', compact('bookDesign', 'categories', 'subCategories'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BookDesign $bookDesign)
    {
        // Validate the input
        $validated = $request->validate([
            'image' => 'nullable|image|mimes:jpg,png,jpeg,gif|max:2048',
            'category_id' => 'required|exists:book_design_categories,id',
            'sub_category_id' => 'nullable|exists:book_design_sub_categories,id',
        ]);

        // If a new image is uploaded, store it and update the image path
        if ($request->hasFile('image')) {
            $storedImage = $request->file('image')->store('book_designs', 'public');
            $bookDesign->image = url('storage/' . $storedImage); // Store the full URL
        }

        // Update the BookDesign record with the new data
        $bookDesign->category_id = $validated['category_id'];
        $bookDesign->sub_category_id = $validated['sub_category_id'] ?? null;
        $bookDesign->save();

        return redirect()->route('book-designs.index')->with('success', 'Book Design updated successfully.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BookDesign $bookDesign)
    {
        if ($bookDesign->image) {
            Storage::disk('public')->delete($bookDesign->image);
        }

        $bookDesign->delete();

        return redirect()->route('book-designs.index')->with('success', 'Book Design deleted successfully.');
    }
}
