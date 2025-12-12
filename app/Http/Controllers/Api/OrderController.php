<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\SvgName;
use App\Support\ArabicNameNormalizer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

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
            'user_gender'        => 'required|in:male,female,group',
            'discount_code_id'   => 'nullable|exists:discount_codes,id',
            'book_type_id'       => 'required|exists:book_types,id',

            'book_design_id'        => 'nullable|exists:book_designs,id',
            'custom_design_image_id'   => 'nullable|array',
            'custom_design_image_id.*' => 'exists:user_images,id',


            'front_image_id'      => 'nullable|exists:user_images,id',
            'book_decorations_id' => 'nullable|exists:book_decorations,id',

            'back_image_ids'   => 'nullable|array',
            'back_image_ids.*' => 'exists:user_images,id',

            'user_type'    => 'required|in:university,diploma',
            'username_ar'  => 'required|string|max:255',
            'username_en'  => 'required|string|max:255',

            'svg_id'       => 'nullable|exists:svgs,id',
            'svg_title'    => 'nullable|string|max:255',
            'note'         => 'nullable|string',
            'user_phone_number' => 'required|string|max:20',
            'is_sponge'    => 'required|boolean',
            'pages_number' => 'required|integer',

            'additional_images'   => 'nullable|array',
            'additional_images.*' => 'exists:user_images,id',

            'transparent_printing_ids'   => 'nullable|array',
            'transparent_printing_ids.*' => 'exists:user_images,id',

            'delivery_number_one' => 'required|string|max:20',
            'delivery_number_two' => 'nullable|string|max:20',
            'governorate'         => 'required|string',
            'address'             => 'required|string',
            'final_price'         => 'required|numeric|min:0',
            'final_price_with_discount' => 'required|numeric|min:0',

            'status' => 'nullable|in:preparing,shipping,completed,canceled,Pending,Received,Out for Delivery,error',

            'gift_type'        => 'required|in:default,custom,none',
            'gift_title'       => 'nullable|string|required_if:gift_type,custom',

            'university_id'       => 'required_if:user_type,university|prohibited_if:user_type,diploma|exists:universities,id',
            'university_major_id' => 'required_if:user_type,university|prohibited_if:user_type,diploma|exists:majors,id',

            'diploma_id'       => 'required_if:user_type,diploma|prohibited_if:user_type,university|exists:diplomas,id',
            'diploma_major_id' => 'required_if:user_type,diploma|prohibited_if:user_type,university|exists:diploma_majors,id',
        ]);

        $validator->after(function ($validator) use ($request) {
            $bookDesignId = $request->input('book_design_id');

            $customDesignImageIds = $request->input('custom_design_image_id', []);

            if (is_null($customDesignImageIds)) {
                $customDesignImageIds = [];
            }

            if (empty($bookDesignId) && empty($customDesignImageIds)) {
                $validator->errors()->add(
                    'book_design_id',
                    'You must choose a design from the ready-made designs or upload another design.'
                );
            }

            

            $userType = $request->input('user_type');

            if ($userType === 'university') {
                $universityId      = $request->input('university_id');
                $universityMajorId = $request->input('university_major_id');

                if ($universityId && $universityMajorId) {
                    $exists = DB::table('majors')
                        ->where('id', $universityMajorId)
                        ->where('university_id', $universityId)
                        ->exists();

                    if (!$exists) {
                        $validator->errors()->add(
                            'university_major_id',
                            'The specialisation is not specific to a particular university.'
                        );
                    }
                }
            }

            if ($userType === 'diploma') {
                $diplomaId      = $request->input('diploma_id');
                $diplomaMajorId = $request->input('diploma_major_id');

                if ($diplomaId && $diplomaMajorId) {
                    $exists = DB::table('diploma_majors')
                        ->where('id', $diplomaMajorId)
                        ->where('diploma_id', $diplomaId)
                        ->exists();

                    if (!$exists) {
                        $validator->errors()->add(
                            'diploma_major_id',
                            'The specialisation does not follow the specific diploma programme.'
                        );
                    }
                }
            }
        });

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        // 🔹 book_design_id ممكن تكون null
        $data['book_design_id'] = $data['book_design_id'] ?? null;
        $data['custom_design_image_id'] = $data['custom_design_image_id'] ?? [];

        // 🔹 لو الإهداء مش custom نخليه null
        if ($data['gift_type'] !== 'custom') {
            $data['gift_title'] = null;
        }

        // 🔹 حالة افتراضية
        $data['status'] = $data['status'] ?? 'Pending';

        // 🔹 back_image_ids نخليها array (Laravel يتكفّل بـ JSON)
        $data['back_image_ids'] = $data['back_image_ids'] ?? [];

        // 🔹 additional_images → نخزنها في additional_image_id (array cast)
        $data['additional_image_id'] = $data['additional_images'] ?? [];

        // 🔹 transparent_printing_ids → نخزن أول واحد في transparent_printing_id
        $transparentIds = $data['transparent_printing_ids'] ?? [];
        $data['transparent_printing_id'] = !empty($transparentIds) ? $transparentIds[0] : null;

        // 🔹 حساب is_with_additives (إسفنج أو صور إضافية أو طباعة شفافة)
        $hasAdditionalImages = !empty($data['additional_image_id']);
        $hasSponge           = !empty($data['is_sponge']);
        $hasTransparent      = !empty($data['transparent_printing_id']);

        $data['is_with_additives'] = ($hasAdditionalImages || $hasSponge || $hasTransparent);

        // نحذف الحقول المساعدة اللي مش موجودة في جدول orders
        unset($data['additional_images'], $data['transparent_printing_ids']);

        // 🧾 إنشاء الطلب
        $order = Order::create($data);

        // 🔤 حفظ الإسم في جدول svg_names (نفس منطقك الحالي)
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
            'order'   => $order->fresh(),  
        ], 201);
    }
}
