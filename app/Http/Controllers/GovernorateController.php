<?php

namespace App\Http\Controllers;

use App\Models\Governorate;
use App\Models\Address;
use Illuminate\Http\Request;

class GovernorateController extends Controller
{
    public function index()
    {
        $governorates = Governorate::with(['addresses' => function ($query) {
            $query->paginate(10); // Adjust the per-page limit as needed
        }])->get();

        return view('admin.governorates.index', compact('governorates'));
    }


    public function create()
    {
        return view('admin.governorates.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name_en' => 'required|string|unique:governorates,name_en',
            'name_ar' => 'required|string|unique:governorates,name_ar',
        ]);

        Governorate::create($request->only('name_en', 'name_ar'));

        return redirect()->route('governorates.index')->with('success', 'Governorate created successfully.');
    }

    public function edit(Governorate $governorate)
    {
        return view('admin.governorates.edit', compact('governorate'));
    }

    public function update(Request $request, Governorate $governorate)
    {
        $request->validate([
            'name_en' => 'required|string|unique:governorates,name_en,' . $governorate->id,
            'name_ar' => 'required|string|unique:governorates,name_ar,' . $governorate->id,
        ]);

        $governorate->update($request->only('name_en', 'name_ar'));

        return redirect()->route('governorates.index')->with('success', 'Governorate updated successfully.');
    }

    public function destroy(Governorate $governorate)
    {
        // Delete all addresses related to this governorate first
        $governorate->addresses()->delete();

        $governorate->delete();

        return redirect()->route('governorates.index')->with('success', 'Governorate and related addresses deleted successfully.');
    }

    public function addAddress(Request $request, Governorate $governorate)
    {
        $request->validate([
            'name_en' => 'required|string',
            'name_ar' => 'required|string',
        ]);

        $address = new Address();
        $address->name_en = $request->name_en;
        $address->name_ar = $request->name_ar;
        $address->governorate_id = $governorate->id;
        $address->save();

        return response()->json([
            'success' => true,
            'message' => 'Address added successfully.',
            'address' => $address,
        ]);
    }


    public function getAddresses($governorateId)
    {
        $addresses = Address::where('governorate_id', $governorateId)->paginate(10);

        return view('admin.governorates.partials.addresses', compact('addresses'))->render();
    }

    public function deleteAddress(Address $address)
    {
        $address->delete();

        return redirect()->route('governorates.index')->with('success', 'Address deleted successfully.');
    }
}
