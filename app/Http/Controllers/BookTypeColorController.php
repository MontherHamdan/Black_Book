<?php

namespace App\Http\Controllers;

use App\Models\BookType;
use App\Models\BookTypeColor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class BookTypeColorController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'book_type_id' => 'required|exists:book_types,id',
            'name_ar' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'color_code' => 'nullable|string|max:30',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'order_display' => 'nullable|integer',
        ]);

        $imageFile = $request->file('image');
        $imageName = uniqid('color_') . '.' . $imageFile->getClientOriginalExtension();
        $imagePath = $imageFile->storeAs('book_types/colors', $imageName, 'public');

        BookTypeColor::create([
            'book_type_id' => $validated['book_type_id'],
            'name_ar' => $validated['name_ar'],
            'name_en' => $validated['name_en'] ?? null,
            'color_code' => $validated['color_code'] ?? null,
            'image' => url('storage/' . $imagePath),
            'order_display' => $validated['order_display'] ?? 0,
        ]);

        Cache::forget('book_types');

        return back()->with('success', 'تمت إضافة اللون بنجاح.');
    }

    public function update(Request $request, BookTypeColor $bookTypeColor)
    {
        $validated = $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'color_code' => 'nullable|string|max:30',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'order_display' => 'nullable|integer',
        ]);

        if ($request->hasFile('image')) {
            if ($bookTypeColor->image) {
                $oldImagePath = str_replace(url('storage') . '/', '', $bookTypeColor->image);
                if (file_exists(public_path('storage/' . $oldImagePath))) {
                    @unlink(public_path('storage/' . $oldImagePath));
                }
            }

            $imageFile = $request->file('image');
            $imageName = uniqid('color_') . '.' . $imageFile->getClientOriginalExtension();
            $imagePath = $imageFile->storeAs('book_types/colors', $imageName, 'public');
            $validated['image'] = url('storage/' . $imagePath);
        }

        $bookTypeColor->update($validated);

        Cache::forget('book_types');

        return back()->with('success', 'تم تعديل اللون بنجاح.');
    }

    public function destroy(BookTypeColor $bookTypeColor)
    {
        if ($bookTypeColor->image) {
            $filePath = str_replace(url('storage') . '/', '', $bookTypeColor->image);
            if (file_exists(public_path('storage/' . $filePath))) {
                @unlink(public_path('storage/' . $filePath));
            }
        }

        $bookTypeColor->delete();

        Cache::forget('book_types');

        return back()->with('success', 'تم حذف اللون بنجاح.');
    }
}
