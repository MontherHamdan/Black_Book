<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Governorate;
use Illuminate\Http\Request;

class GovernorateController extends Controller
{
    /**
     * عرض جميع المحافظات مع اسم الدولة التابعة لها
     */
    public function index()
    {
        // جلب المحافظات مع الدولة التابعة لها، وترتيبها حسب الدولة ثم الاسم
        $governorates = Governorate::with('country')->orderBy('country_id')->orderBy('name_ar')->get();

        return view('admin.governorates.index', compact('governorates'));
    }

    /**
     * صفحة إضافة محافظة يدوياً (إن لزم الأمر)
     */
    public function create()
    {
        $countries = Country::where('is_active', true)->get();

        return view('admin.governorates.create', compact('countries'));
    }

    /**
     * حفظ المحافظة الجديدة
     */
    public function store(Request $request)
    {
        $request->validate([
            'country_id' => 'required|exists:countries,id',
            'name_en' => 'required|string|unique:governorates,name_en',
            'name_ar' => 'required|string|unique:governorates,name_ar',
        ]);

        $data = $request->only('country_id', 'name_en', 'name_ar');
        $data['is_active'] = $request->has('is_active'); // التقاط حالة التفعيل

        Governorate::create($data);

        return redirect()->route('governorates.index')->with('success', 'تم إضافة المحافظة بنجاح.');
    }

    /**
     * صفحة تعديل المحافظة
     */
    public function edit(Governorate $governorate)
    {
        $countries = Country::all();

        return view('admin.governorates.edit', compact('governorate', 'countries'));
    }

    /**
     * تحديث بيانات المحافظة
     */
    public function update(Request $request, Governorate $governorate)
    {
        $request->validate([
            'country_id' => 'required|exists:countries,id',
            'name_en' => 'required|string|unique:governorates,name_en,'.$governorate->id,
            'name_ar' => 'required|string|unique:governorates,name_ar,'.$governorate->id,
        ]);

        $data = $request->only('country_id', 'name_en', 'name_ar');
        $data['is_active'] = $request->has('is_active');

        $governorate->update($data);

        return redirect()->route('governorates.index')->with('success', 'تم تحديث المحافظة بنجاح.');
    }

    /**
     * 🚀 دالة تفعيل وتعطيل المحافظة عبر الـ AJAX
     */
    public function toggleActive(Governorate $governorate)
    {
        $governorate->update([
            'is_active' => ! $governorate->is_active,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم تغيير حالة المحافظة بنجاح.',
            'is_active' => $governorate->is_active,
        ]);
    }

    /**
     * حذف المحافظة
     */
    public function destroy(Governorate $governorate)
    {
        // إذا كان هناك مدن مرتبطة بهذه المحافظة، يفضل حذفها أو منع الحذف
        // $governorate->cities()->delete(); // في حال أردت تفعيل الحذف المتسلسل مستقبلاً

        $governorate->delete();

        return redirect()->route('governorates.index')->with('success', 'تم حذف المحافظة بنجاح.');
    }
}
