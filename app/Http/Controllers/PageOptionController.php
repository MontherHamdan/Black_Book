<?php

namespace App\Http\Controllers;

use App\Models\PageOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PageOptionController extends Controller
{
    /**
     * Display a listing of page options.
     */
    public function index()
    {
        $pageOptions = PageOption::orderBy('sort_order', 'asc')
            ->orderBy('pages', 'asc')
            ->get();

        return view('admin.page_options.index', compact('pageOptions'));
    }

    /**
     * Show the form for creating a new page option.
     */
    public function create()
    {
        return view('admin.page_options.create');
    }

    /**
     * Store a newly created page option in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pages' => 'required|integer|min:1|unique:page_options,pages',
            'label' => 'required|string|max:255',
            'extra_price' => 'nullable|numeric|min:0',
            'badge' => 'nullable|string|max:255',
            'is_default' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
        ], [
            'pages.required' => 'حقل عدد الأوراق مطلوب.',
            'pages.integer' => 'يجب أن يكون عدد الأوراق رقماً صحيحاً.',
            'pages.unique' => 'عدد الأوراق هذا موجود بالفعل مسبقاً.',
            'label.required' => 'النص المعروض مطلوب (مثال: 150 ورقة).',
        ]);

        $isDefault = $request->boolean('is_default');
        $isActive = $request->has('is_active') ? $request->boolean('is_active') : true;

        // إذا تم تحديد هذا الخيار كافتراضي، قم بإلغاء الافتراضي عن باقي الخيارات
        if ($isDefault) {
            PageOption::where('is_default', true)->update(['is_default' => false]);
        }

        PageOption::create([
            'pages' => $validated['pages'],
            'label' => $validated['label'],
            'extra_price' => $validated['extra_price'] ?? 0.00,
            'badge' => $validated['badge'] ?: null,
            'is_default' => $isDefault,
            'is_active' => $isActive,
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        Cache::forget('active_page_options');

        return redirect()->route('page-options.index')
            ->with('success', 'تمت إضافة خيار الأوراق بنجاح.');
    }

    /**
     * Show the form for editing the specified page option.
     */
    public function edit(PageOption $pageOption)
    {
        return view('admin.page_options.edit', compact('pageOption'));
    }

    /**
     * Update the specified page option in storage.
     */
    public function update(Request $request, PageOption $pageOption)
    {
        $validated = $request->validate([
            'pages' => 'required|integer|min:1|unique:page_options,pages,' . $pageOption->id,
            'label' => 'required|string|max:255',
            'extra_price' => 'nullable|numeric|min:0',
            'badge' => 'nullable|string|max:255',
            'is_default' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
        ], [
            'pages.required' => 'حقل عدد الأوراق مطلوب.',
            'pages.integer' => 'يجب أن يكون عدد الأوراق رقماً صحيحاً.',
            'pages.unique' => 'عدد الأوراق هذا مسجل لخيار آخر بالفعل.',
            'label.required' => 'النص المعروض مطلوب (مثال: 150 ورقة).',
        ]);

        $isDefault = $request->boolean('is_default');
        $isActive = $request->boolean('is_active');

        // إذا تم تعيينه كافتراضي، ألغِ الافتراضي عن الباقين
        if ($isDefault && ! $pageOption->is_default) {
            PageOption::where('id', '!=', $pageOption->id)
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }

        $pageOption->update([
            'pages' => $validated['pages'],
            'label' => $validated['label'],
            'extra_price' => $validated['extra_price'] ?? 0.00,
            'badge' => $validated['badge'] ?: null,
            'is_default' => $isDefault,
            'is_active' => $isActive,
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        Cache::forget('active_page_options');

        return redirect()->route('page-options.index')
            ->with('success', 'تم تعديل خيار الأوراق بنجاح.');
    }

    /**
     * Remove the specified page option from storage.
     */
    public function destroy(PageOption $pageOption)
    {
        $pageOption->delete();

        Cache::forget('active_page_options');

        return redirect()->route('page-options.index')
            ->with('success', 'تم حذف خيار الأوراق بنجاح.');
    }

    /**
     * Toggle active status.
     */
    public function toggleActive(PageOption $pageOption)
    {
        $pageOption->is_active = ! $pageOption->is_active;
        $pageOption->save();

        Cache::forget('active_page_options');

        $statusText = $pageOption->is_active ? 'تفعيل' : 'تعطيل';

        return redirect()->route('page-options.index')
            ->with('success', "تم {$statusText} الخيار ({$pageOption->label}) بنجاح.");
    }
}
