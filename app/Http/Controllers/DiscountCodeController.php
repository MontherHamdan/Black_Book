<?php

namespace App\Http\Controllers;

use App\Models\DiscountCode;
use Illuminate\Http\Request;

class DiscountCodeController extends Controller
{
    public function index()
    {
        $discountCodes = DiscountCode::paginate(10); // Paginate for better display
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
            'discount_value' => 'required|integer|min:0',
            'discount_type' => 'required|in:percentage,byJd', // Validate discount_type
        ]);

        DiscountCode::create($request->all());
        return redirect()->route('discount-codes.index')->with('success', 'Discount code created successfully.');
    }

    public function edit(DiscountCode $discountCode)
    {
        return view('admin.discount-codes.edit', compact('discountCode'));
    }

    public function update(Request $request, DiscountCode $discountCode)
    {
        $request->validate([
            'code_name' => 'nullable|string|max:255',
            'discount_code' => 'required|string|unique:discount_codes,discount_code,' . $discountCode->id,
            'discount_value' => 'required|integer|min:0',
            'discount_type' => 'required|in:percentage,byJd', // Validate discount_type
        ]);

        $discountCode->update($request->all());
        return redirect()->route('discount-codes.index')->with('success', 'Discount code updated successfully.');
    }

    public function destroy(DiscountCode $discountCode)
    {
        $discountCode->delete();
        return redirect()->route('discount-codes.index')->with('success', 'Discount code deleted successfully.');
    }
}
