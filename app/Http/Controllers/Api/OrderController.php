<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'book_type_id' => 'required|exists:book_types,id',
            'book_design_id' => 'required|exists:book_designs,id',
            'front_image_id' => 'required|exists:user_images,id',
            'back_image_ids' => 'required|array',
            'back_image_ids.*' => 'exists:user_images,id',
            'user_type' => 'required|in:university,diploma',
            'arabic_name' => 'required|string|max:255',
            'english_name' => 'required|string|max:255',
            'university_id' => 'required|exists:universities,id',
            'major_id' => 'required|exists:majors,id',
            'svg_id' => 'nullable|exists:svgs,id',
            'svg_title' => 'nullable|string|max:255',
            'note' => 'nullable|string|max:255',
            'user_phone_number' => 'required|string|max:15',
            'pages_number' => 'required|integer',
            'is_sponge' => 'required|boolean',
            'is_option' => 'required|boolean',
            'add_photo_option_id' => 'nullable',
            'transparent_print_option_id' => 'nullable',
            'gift_option' => 'nullable',
            'first_delivery_phone_number' => 'required|string|max:15',
            'second_delivery_phone_number' => 'nullable|string|max:15',
            'total_price' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,click',
        ]);

        // Convert `back_image_ids` to JSON
        $validated['back_image_ids'] = json_encode($validated['back_image_ids']);

        // Create the order
        $order = Order::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Order created successfully.',
            'data' => $order,
        ], 201);
    }
}
