<?php

namespace App\Http\Controllers;

use App\Models\DiscountCode;
use App\Models\Plan;
use Illuminate\Http\Request;

class DiscountCodeController extends Controller
{
    public function index()
    {
        $discountCodes = DiscountCode::withCount('tiers', 'orders')->orderByDesc('id')->get();

        return view('admin.discount-codes.index', compact('discountCodes'));
    }

    public function create()
    {
        $plans = Plan::all();

        return view('admin.discount-codes.create', compact('plans'));
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
        } else {
            $rules['discount_value'] = 'required|numeric|min:0';
            $rules['discount_type'] = 'required|in:percentage,byJd';
            $rules['tiers'] = 'nullable|array';
            $rules['tiers.*.min_qty'] = 'required_with:tiers|integer|min:2';
            $rules['tiers.*.discount_value'] = 'required_with:tiers|numeric|min:0';
            $rules['tiers.*.discount_type'] = 'required_with:tiers|in:percentage,byJd';
        }

        $request->validate($rules);

        $data = $request->only(['code_name', 'discount_code', 'is_group']);

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

        return view('admin.discount-codes.edit', compact('discountCode', 'plans'));
    }

    public function update(Request $request, DiscountCode $discountCode)
    {
        $request->validate([
            'code_name' => 'nullable|string|max:255',
            'discount_code' => 'required|string|unique:discount_codes,discount_code,'.$discountCode->id,
            'discount_value' => 'required|numeric|min:0',
            'discount_type' => 'required|in:percentage,byJd',
            // Tier validation
            'tiers' => 'nullable|array',
            'tiers.*.min_qty' => 'required_with:tiers|integer|min:2',
            'tiers.*.discount_value' => 'required_with:tiers|numeric|min:0',
            'tiers.*.discount_type' => 'required_with:tiers|in:percentage,byJd',
        ]);

        $discountCode->update($request->only(['code_name', 'discount_code', 'discount_value', 'discount_type']));

        // Sync tiers: delete old, insert new
        $discountCode->tiers()->delete();

        if ($request->has('tiers') && is_array($request->tiers)) {
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
