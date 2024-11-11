<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PhoneNumbers;
use Illuminate\Support\Facades\Validator;

class PhoneNumbersConroller extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', '10');

        return PhoneNumbers::paginate($perPage);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => [
                'required' => 'رقم الهاتف مطلوب',
                'string' => 'رقم الهاتف يجب أن يكون نصاً',
                'size' => 'رقم الهاتف يجب أن يتكون من 10 أرقام',
                'unique' => 'رقم الهاتف مسجل بالفعل',
                'regex' => 'رقم الهاتف يجب أن يحتوي على أرقام فقط',
            ]
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $phone_number = PhoneNumbers::create([
            'phone_number' => $request->input('phone_number'),
        ]);

        if ($phone_number) {
            return response()->json(['message' => 'تم تسجيل رقم الهاتف بنجاح', 'data' => $phone_number], 201);
        } else {
            return response()->json(['message' => 'فشل في تسجيل رقم الهاتف. الرجاء المحاولة مرة أخرى'], 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
