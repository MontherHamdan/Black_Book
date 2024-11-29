<?php

namespace App\Http\Controllers;

use App\Models\BookDesignCategory;
use Illuminate\Http\Request;

class BookDesignCategoryController extends Controller
{
    // Display a listing of the categories
    public function index()
    {
        $categories = BookDesignCategory::get();
        return view('admin.categories.index', compact('categories'));
    }

    // Show the form for creating a new category
    public function create()
    {
        return view('admin.categories.create');
    }

    // Store a newly created category
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'arabic_name' => 'required|string|max:255',
        ]);

        $validated['type'] = $request->has('type') ? 'multiple' : 'single';

        BookDesignCategory::create($validated);

        return redirect()->route('categories.index')->with('success', 'Category created successfully.');
    }


    // Show the form for editing the specified category
    public function edit(BookDesignCategory $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    // Update the specified category
    public function update(Request $request, BookDesignCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'arabic_name' => 'required|string|max:255',
        ]);

        // Add the type field to the validated data
        $validated['type'] = $request->has('type') ? 'multiple' : 'single';

        $category->update($validated);

        return redirect()->route('categories.index')->with('success', 'Category updated successfully.');
    }


    // Remove the specified category
    public function destroy(BookDesignCategory $category)
    {
        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Category deleted successfully.');
    }
}
