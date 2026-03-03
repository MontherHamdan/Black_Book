<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class CountryController extends Controller
{
    public function index()
    {
        $countries = Country::orderBy('name')->get();
        return view('admin.countries.index', compact('countries'));
    }

    public function create()
    {
        return view('admin.countries.create');
    }

    public function edit(Country $country)
    {
        return view('admin.countries.edit', compact('country'));
    }

    public function update(Request $request, Country $country)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:countries,name,' . $country->id,
            'code' => 'required|string|max:10|unique:countries,code,' . $country->id,
            'dial_code' => 'required|string|max:10',
            // استبدلنا image|mimes بـ file لتقبل أي شيء، مع الإبقاء على حجم 2 ميجابايت كحد أقصى
            'flag_image' => 'nullable|file|max:2048',
        ]);

        $data = $request->except('flag_image');

        if ($request->hasFile('flag_image')) {
            if ($country->flag_image && File::exists(public_path($country->flag_image))) {
                File::delete(public_path($country->flag_image));
            }

            $file = $request->file('flag_image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/flags'), $filename);
            $data['flag_image'] = 'uploads/flags/' . $filename;
        }

        $country->update($data);

        return redirect()->route('countries.index')->with('success', 'The country has been successfully updated.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:countries,name',
            'code' => 'required|string|max:10|unique:countries,code',
            'dial_code' => 'required|string|max:10',
            'flag_image' => 'required|file',
        ]);

        $data = $request->except('flag_image');

        if ($request->hasFile('flag_image')) {
            $file = $request->file('flag_image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/flags'), $filename);
            $data['flag_image'] = 'uploads/flags/' . $filename;
        }

        Country::create($data);

        return redirect()->route('countries.index')->with('success', 'The country has been successfully added.');
    }

    public function destroy(Country $country)
    {
        if ($country->flag_image && File::exists(public_path($country->flag_image))) {
            File::delete(public_path($country->flag_image));
        }

        $country->delete();
        return redirect()->route('countries.index')->with('success', 'The country has been successfully deleted.');
    }
}