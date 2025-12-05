<?php

namespace App\Http\Controllers;

use App\Models\BookDecoration;
use Illuminate\Http\Request;

class BookDecorationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bookDecorations = BookDecoration::all();

        return view('admin.book_decorations.index', compact('bookDecorations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.book_decorations.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'  => 'nullable|string|max:255',
            'image' => 'required|image|mimes:jpg,png,jpeg,gif,webp|max:2048',
        ]);

        $imageFile = $request->file('image');
        $timestamp = time();
        $originalName = $imageFile->getClientOriginalName();
        $imageName = $timestamp . '_' . $originalName;
        $imagePath = $imageFile->storeAs('book_decorations', $imageName, 'public');
        $imageUrl = url('storage/' . $imagePath);

        BookDecoration::create([
            'name'  => $validated['name'] ?? pathinfo($originalName, PATHINFO_FILENAME), // لو ما كتب اسم، ناخذ اسم الملف بدون الامتداد
            'image' => $imageUrl,
        ]);

        return redirect()->route('book-decorations.index')
            ->with('success', 'Book Decoration created successfully.');
    }



    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BookDecoration $bookDecoration)
    {
        return view('admin.book_decorations.edit', compact('bookDecoration'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BookDecoration $bookDecoration)
    {
        $validated = $request->validate([
            'name'  => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpg,png,jpeg,gif,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            // حذف الصورة القديمة
            if ($bookDecoration->image) {
                $oldImagePath = str_replace(url('storage') . '/', '', $bookDecoration->image);
                if (file_exists(public_path('storage/' . $oldImagePath))) {
                    unlink(public_path('storage/' . $oldImagePath));
                }
            }

            // حفظ الصورة الجديدة
            $imageFile = $request->file('image');
            $timestamp = time();
            $originalName = $imageFile->getClientOriginalName();
            $imageName = $timestamp . '_' . $originalName;
            $imagePath = $imageFile->storeAs('book_decorations', $imageName, 'public');
            $validated['image'] = url('storage/' . $imagePath);

            // لو ما فيه اسم مدخل، نستخدم اسم الملف
            if (empty($validated['name'])) {
                $validated['name'] = pathinfo($originalName, PATHINFO_FILENAME);
            }
        }

        $bookDecoration->update($validated);

        return redirect()->route('book-decorations.index')
            ->with('success', 'Book Decoration updated successfully.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BookDecoration $bookDecoration)
    {
        if ($bookDecoration->image) {
            $oldImagePath = str_replace(url('storage') . '/', '', $bookDecoration->image);
            if (file_exists(public_path('storage/' . $oldImagePath))) {
                unlink(public_path('storage/' . $oldImagePath));
            }
        }

        $bookDecoration->delete();

        return redirect()->route('book-decorations.index')->with('success', 'Book Decoration deleted successfully.');
    }
}
