<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\City;
use App\Models\DiscountCode;
use App\Models\Governorate;
use App\Models\Plan;
use App\Models\University;
use Illuminate\Http\Request;

class DiscountCodeController extends Controller
{
    public function index()
    {
        $discountCodes = DiscountCode::with(['plan', 'university', 'governorate', 'city', 'area'])
            ->withCount('tiers', 'orders')
            ->orderByDesc('id')
            ->get();

        return view('admin.discount-codes.index', compact('discountCodes'));
    }

    public function create()
    {
        $plans = Plan::all();
        $universities = University::all();
        $governorates = Governorate::all();
        $cities = City::all();
        $areas = Area::all();

        return view('admin.discount-codes.create', compact('plans', 'universities', 'governorates', 'cities', 'areas'));
    }

    public function store(Request $request)
    {
        $isGroup = $request->has('is_group') && $request->is_group == '1';

        $rules = [
            'code_name' => 'nullable|string|max:255',
            'discount_code' => 'required|string|unique:discount_codes,discount_code',
            'is_group' => 'required|boolean',
        ];

        if ($isGroup) {
            $rules['plan_id'] = 'required|exists:plans,id';
            $data['discount_value'] = null;
            $data['discount_type'] = null;
            $rules['user_phone_number'] = 'required|string|max:20';
            $rules['delivery_number_two'] = 'nullable|string|max:20';
            $rules['university_id'] = 'nullable|exists:universities,id';
            $rules['governorate_id'] = 'nullable|exists:governorates,id';
            $rules['city_id'] = 'nullable|exists:cities,id';
            $rules['area_id'] = 'nullable|exists:areas,id';
        } else {
            $rules['discount_value'] = 'required|numeric|min:0';
            $rules['discount_type'] = 'required|in:percentage,byJd';
            $rules['tiers'] = 'nullable|array';
            $rules['tiers.*.min_qty'] = 'required_with:tiers|integer|min:2';
            $rules['tiers.*.discount_value'] = 'required_with:tiers|numeric|min:0';
            $rules['tiers.*.discount_type'] = 'required_with:tiers|in:percentage,byJd';
        }

        $request->validate($rules);

        $data = $request->only([
            'code_name', 'discount_code', 'is_group',
            'user_phone_number', 'delivery_number_two',
            'university_id', 'governorate_id', 'city_id', 'area_id',
        ]);

        if ($isGroup) {
            $data['plan_id'] = $request->plan_id;
            $data['discount_value'] = null;
            $data['discount_type'] = null;
        } else {
            $data['plan_id'] = null;
            $data['discount_value'] = $request->discount_value;
            $data['discount_type'] = $request->discount_type;
        }

        $discountCode = DiscountCode::create($data);

        if (! $isGroup && $request->has('tiers') && is_array($request->tiers)) {
            foreach ($request->tiers as $tier) {
                if (! empty($tier['min_qty']) && isset($tier['discount_value'])) {
                    $discountCode->tiers()->create([
                        'min_qty' => $tier['min_qty'],
                        'discount_value' => $tier['discount_value'],
                        'discount_type' => $tier['discount_type'],
                    ]);
                }
            }
        }

        return redirect()->route('discount-codes.index')->with('success', 'Discount code created successfully.');
    }

    public function edit(DiscountCode $discountCode)
    {
        $discountCode->load('tiers', 'plan');
        $plans = Plan::all();
        $universities = University::all();
        $governorates = Governorate::all();
        $cities = City::all();
        $areas = area::all();

        return view('admin.discount-codes.edit', compact('discountCode', 'plans', 'universities', 'governorates', 'cities', 'areas'));
    }

    public function update(Request $request, DiscountCode $discountCode)
    {
        $isGroup = $request->has('is_group') && $request->is_group == '1';

        $rules = [
            'code_name' => 'nullable|string|max:255',
            'discount_code' => 'required|string|unique:discount_codes,discount_code,'.$discountCode->id,
            'is_group' => 'required|boolean',
        ];

        if ($isGroup) {
            $rules['plan_id'] = 'required|exists:plans,id';
            $rules['user_phone_number'] = 'required|string|max:20';
            $rules['delivery_number_two'] = 'nullable|string|max:20';

            // --- تحقق صارم من مكان التوصيل ---
            $rules['delivery_target'] = 'required|in:university,home';
            if ($request->delivery_target === 'university') {
                $rules['university_id'] = 'required|exists:universities,id';
            } else {
                $rules['governorate_id'] = 'required|exists:governorates,id';
                $rules['city_id'] = 'required|exists:cities,id';
                $rules['area_id'] = 'required|exists:areas,id';
            }

        } else {
            $rules['discount_value'] = 'required|numeric|min:0';
            $rules['discount_type'] = 'required|in:percentage,byJd';
            $rules['tiers'] = 'nullable|array';
            $rules['tiers.*.min_qty'] = 'required_with:tiers|integer|min:2';
            $rules['tiers.*.discount_value'] = 'required_with:tiers|numeric|min:0';
            $rules['tiers.*.discount_type'] = 'required_with:tiers|in:percentage,byJd';
        }

        $request->validate($rules);

        // جلب البيانات الأساسية
        $data = $request->only([
            'code_name', 'discount_code', 'is_group',
            'user_phone_number', 'delivery_number_two',
        ]);

        if ($isGroup) {
            $data['plan_id'] = $request->plan_id;
            $data['discount_value'] = null;
            $data['discount_type'] = null;

            // --- التصفير الإجباري والنهائي ---
            if ($request->delivery_target === 'university') {
                $data['university_id'] = $request->university_id;
                // تصفير بيانات المنزل
                $data['governorate_id'] = null;
                $data['city_id'] = null;
                $data['area_id'] = null;
            } else {
                // تصفير بيانات الجامعة
                $data['university_id'] = null;
                $data['governorate_id'] = $request->governorate_id;
                $data['city_id'] = $request->city_id;
                $data['area_id'] = $request->area_id;
            }
        } else {
            $data['plan_id'] = null;
            $data['discount_value'] = $request->discount_value;
            $data['discount_type'] = $request->discount_type;

            // تصفير كل شيء يخص المجموعات إذا تم تحويله لفردي
            $data['user_phone_number'] = null;
            $data['delivery_number_two'] = null;
            $data['university_id'] = null;
            $data['governorate_id'] = null;
            $data['city_id'] = null;
            $data['area_id'] = null;
        }

        $discountCode->update($data);

        // Sync tiers
        $discountCode->tiers()->delete();

        if (! $isGroup && $request->has('tiers') && is_array($request->tiers)) {
            foreach ($request->tiers as $tier) {
                if (! empty($tier['min_qty']) && isset($tier['discount_value'])) {
                    $discountCode->tiers()->create([
                        'min_qty' => $tier['min_qty'],
                        'discount_value' => $tier['discount_value'],
                        'discount_type' => $tier['discount_type'],
                    ]);
                }
            }
        }

        return redirect()->route('discount-codes.index')->with('success', 'Discount code updated successfully.');
    }

    public function destroy(DiscountCode $discountCode)
    {
        $discountCode->delete();

        return redirect()->route('discount-codes.index')->with('success', 'Discount code deleted successfully.');
    }
}
