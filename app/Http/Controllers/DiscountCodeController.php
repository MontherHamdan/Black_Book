<?php

namespace App\Http\Controllers;

use App\Models\DiscountCode;
use App\Models\DiscountCodeTier;
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
        return view('admin.discount-codes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code_name' => 'nullable|string|max:255',
            'discount_code' => 'required|string|unique:discount_codes,discount_code',
            'discount_value' => 'required|numeric|min:0',
            'discount_type' => 'required|in:percentage,byJd',
            // Tier validation
            'tiers' => 'nullable|array',
            'tiers.*.min_qty' => 'required_with:tiers|integer|min:2',
            'tiers.*.discount_value' => 'required_with:tiers|numeric|min:0',
            'tiers.*.discount_type' => 'required_with:tiers|in:percentage,byJd',
        ]);

        $discountCode = DiscountCode::create($request->only(['code_name', 'discount_code', 'discount_value', 'discount_type']));

        // Save tiers
        if ($request->has('tiers') && is_array($request->tiers)) {
            foreach ($request->tiers as $tier) {
                if (!empty($tier['min_qty']) && isset($tier['discount_value'])) {
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
        $discountCode->load('tiers');
        return view('admin.discount-codes.edit', compact('discountCode'));
    }

    public function update(Request $request, DiscountCode $discountCode)
    {
        $request->validate([
            'code_name' => 'nullable|string|max:255',
            'discount_code' => 'required|string|unique:discount_codes,discount_code,' . $discountCode->id,
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
                if (!empty($tier['min_qty']) && isset($tier['discount_value'])) {
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
