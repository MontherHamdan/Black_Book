<?php

namespace App\Http\Controllers;

use App\Models\PhoneNumbers;
use Illuminate\Http\Request;

class PhoneNumberController extends Controller
{
    // Display a listing of phone numbers
    public function index()
    {
        $phoneNumbers = PhoneNumbers::all();
        return view('admin.phone_numbers.index', compact('phoneNumbers'));
    }

    // Remove the specified phone number from storage
    public function destroy($id)
    {
        $phoneNumber = PhoneNumbers::findOrFail($id);
        $phoneNumber->delete();

        return redirect()->route('phone_numbers.index')->with('success', 'Phone number deleted successfully.');
    }
}
