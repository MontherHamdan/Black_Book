<?php

namespace App\Http\Controllers;

use App\Models\BookDesignCategory;
use App\Models\BookDesignSubCategory;
use Illuminate\Http\Request;

class BookDesignSubCategoryController extends Controller
{
    // Display a listing of the subcategories
    public function index()
    {
        $subCategories = BookDesignSubCategory::with('category')->get();
        return view('admin.subcategories.index', compact('subCategories'));
    }

    // Show the form for creating a new subcategory
    public function create()
    {
        $categories = BookDesignCategory::all();
        return view('admin.subcategories.create', compact('categories'));
    }

    // Store a newly created subcategory
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'arabic_name' => 'required|string|max:255',
            'category_id' => 'required|exists:book_design_categories,id',
        ]);

        BookDesignSubCategory::create($validated);

        return redirect()->route('subcategories.index')->with('success', 'Subcategory created successfully.');
    }

    // Show the form for editing the specified subcategory
    public function edit($id)
    {
        $subCategory = BookDesignSubCategory::findOrFail($id);

        $categories = BookDesignCategory::all();

        return view('admin.subcategories.edit', compact('subCategory', 'categories'));
    }

    // Update the specified subcategory
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'arabic_name' => 'required|string|max:255',
            'category_id' => 'required|exists:book_design_categories,id',
        ]);
        $subCategory = BookDesignSubCategory::findOrFail($id);

        $subCategory->update($validated);

        return redirect()->route('subcategories.index')->with('success', 'Subcategory updated successfully.');
    }

    // Remove the specified subcategory
    public function destroy(BookDesignSubCategory $subCategory)
    {
        $subCategory->delete();

        return redirect()->route('subcategories.index')->with('success', 'Subcategory deleted successfully.');
    }
}
