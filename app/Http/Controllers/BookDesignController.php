<?php

namespace App\Http\Controllers;

use App\Models\BookDesign;
use App\Models\BookDesignCategory;
use App\Models\BookDesignSubCategory;
use Illuminate\Http\Request;
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
            'design_name' => 'nullable|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,gif,webp|max:20480',
            'category_id' => 'required|exists:book_design_categories,id',
            'sub_category_id' => 'nullable|exists:book_design_sub_categories,id',
            'is_image_required' => 'boolean',
        ]);

        $imageFile = $request->file('image');
        $imageName = \Str::uuid().'.'.$imageFile->getClientOriginalExtension();
        $imagePath = $imageFile->storeAs('book_designs', $imageName, 'public');

        if (! $imagePath) {
            return back()->withErrors(['image' => 'فشل حفظ الصورة، تحقق من صلاحيات المجلد.'])->withInput();
        }

        $imageUrl = url('storage/'.$imagePath);

        // Handle sub_category_id properly if it's present
        $subCategoryId = $validated['sub_category_id'] ?? null;

        // Save the image URL and other data in the database
        BookDesign::create([
            'design_name' => $validated['design_name'] ?? null,
            'image' => $imageUrl, // Store the full URL instead of just the path
            'category_id' => $validated['category_id'],
            'sub_category_id' => $subCategoryId,
            'is_image_required' => $request->has('is_image_required'),
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
            'design_name' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,gif,webp|max:20480',
            'category_id' => 'required|exists:book_design_categories,id',
            'sub_category_id' => 'nullable|exists:book_design_sub_categories,id',
            'is_image_required' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            if ($bookDesign->image) {
                $oldImagePath = ltrim(str_replace(url('storage'), '', $bookDesign->image), '/');
                Storage::disk('public')->delete($oldImagePath);
            }

            $imageFile = $request->file('image');
            $imageName = \Str::uuid().'.'.$imageFile->getClientOriginalExtension();
            $imagePath = $imageFile->storeAs('book_designs', $imageName, 'public');

            if (! $imagePath) {
                return back()->withErrors(['image' => 'فشل حفظ الصورة، تحقق من صلاحيات المجلد.'])->withInput();
            }

            $validated['image'] = url('storage/'.$imagePath);
        }

        // Update the BookDesign record
        $bookDesign->update([
            'design_name' => $validated['design_name'] ?? $bookDesign->design_name,
            'image' => $validated['image'] ?? $bookDesign->image,
            'category_id' => $validated['category_id'],
            'sub_category_id' => $validated['sub_category_id'] ?? null,
            'is_image_required' => $request->has('is_image_required'),
        ]);

        return redirect()->route('book-designs.index')->with('success', 'Book Design updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BookDesign $bookDesign)
    {
        // ما بنحذف الصورة هون عشان نقدر نسترجعها بعدين
        $bookDesign->delete();

        return redirect()->route('book-designs.index')->with('success', 'Book Design moved to trash successfully.');
    }
}
