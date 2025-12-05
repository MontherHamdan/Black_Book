<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\SvgName;
use App\Support\ArabicNameNormalizer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    /**
     * Store a newly created order in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_gender' => 'required|in:male,female,group',
            'discount_code_id' => 'nullable|exists:discount_codes,id',
            'book_type_id' => 'required|exists:book_types,id',
            'book_design_id' => 'required',
            'front_image_id' => 'nullable|exists:user_images,id',
            'book_decorations_id' => 'nullable|exists:book_decorations,id',

            'back_image_ids' => 'nullable|array',
            'back_image_ids.*' => 'exists:user_images,id',

            'user_type' => 'required|in:university,diploma',
            'username_ar' => 'required|string|max:255',
            'username_en' => 'required|string|max:255',
            'school_name' => 'required|string|max:255',
            'major_name' => 'required|string|max:255',
            'svg_id' => 'nullable|exists:svgs,id',
            'svg_title' => 'nullable|string|max:255',
            'note' => 'nullable|string',
            'user_phone_number' => 'required|string|max:20',
            'is_sponge' => 'required|boolean',
            'pages_number' => 'required|integer',

            'additional_images' => 'nullable|array',
            'additional_images.*' => 'exists:user_images,id',

            'transparent_printing_ids' => 'nullable|array',
            'transparent_printing_ids.*' => 'exists:user_images,id',

            'delivery_number_one' => 'required|string|max:20',
            'delivery_number_two' => 'nullable|string|max:20',
            'governorate' => 'required|string',
            'address' => 'required|string',
            'final_price' => 'required|numeric|min:0',
            'final_price_with_discount' => 'required|numeric|min:0',

            // 👇 هنا التعديل
            'status' => 'nullable|in:preparing,shipping,completed,canceled,Pending,Received,Out for Delivery,error',

            'gift_type'  => 'required|in:default,custom,none',
            'gift_title' => 'nullable|string|required_if:gift_type,custom',
            'is_with_additives' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        
        if ($data['gift_type'] !== 'custom') {
            $data['gift_title'] = null;
        }

        // 👇 default متوافق مع الداتا الحالية عندك
        $data['status'] = $data['status'] ?? 'Pending';

        $data['back_image_ids'] = json_encode($data['back_image_ids'] ?? []);
        $data['transparent_printing_ids'] = json_encode($data['transparent_printing_ids'] ?? []);

        unset($data['additional_images'], $data['additional_image_id']);

        $order = Order::create($data);

        if ($request->filled('additional_images')) {
            foreach ($request->additional_images as $imageId) {
                $order->additionalImages()->create([
                    'image' => $imageId,
                ]);
            }
        }

        $firstArabicName = ArabicNameNormalizer::firstArabicName($order->username_ar ?? '');

        if (!empty($firstArabicName)) {
            $normalized = ArabicNameNormalizer::normalize($firstArabicName);

            SvgName::firstOrCreate(
                ['normalized_name' => $normalized],
                [
                    'name'   => $firstArabicName,
                    'svg_id' => null,
                ]
            );
        }

        return response()->json([
            'message' => 'Order created successfully.',
            'order'   => $order->load('additionalImages'),
        ], 201);
    }
}
