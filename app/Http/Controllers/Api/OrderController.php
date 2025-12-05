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

            // تصميم جاهز من book_designs
            'book_design_id'        => 'nullable|exists:book_designs,id',

            // تصميم مرفوع من user_images (تحميل تصميم آخر)
            'custom_design_image_id' => 'nullable|exists:user_images,id',

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
            'is_with_additives' => 'nullable|boolean',

            'university_id'       => 'required_if:user_type,university|prohibited_if:user_type,diploma|exists:universities,id',
            'university_major_id' => 'required_if:user_type,university|prohibited_if:user_type,diploma|exists:majors,id',

            'diploma_id'       => 'required_if:user_type,diploma|prohibited_if:user_type,university|exists:diplomas,id',
            'diploma_major_id' => 'required_if:user_type,diploma|prohibited_if:user_type,university|exists:diploma_majors,id',
        ]);

        $validator->after(function ($validator) use ($request) {
            // 1) منطق اختيار التصميم: يا جاهز يا مرفوع، مش الاثنين
            $bookDesignId        = $request->input('book_design_id');
            $customDesignImageId = $request->input('custom_design_image_id');

            // الاثنين فاضيين
            if (empty($bookDesignId) && empty($customDesignImageId)) {
                $validator->errors()->add(
                    'book_design_id',
                    'يجب اختيار تصميم من التصاميم الجاهزة أو تحميل تصميم آخر.'
                );
            }

            // الاثنين موجودين معًا
            if (!empty($bookDesignId) && !empty($customDesignImageId)) {
                $validator->errors()->add(
                    'book_design_id',
                    'You cannot select a ready-made design and upload another design at the same time. Choose only one of the two options.'
                );
                $validator->errors()->add(
                    'custom_design_image_id',
                   'You cannot select a ready-made design and upload another design at the same time. Choose only one of the two options.'
                );
            }

            // 2) منطق university / diploma + majors
            $userType = $request->input('user_type');

            if ($userType === 'university') {
                $universityId      = $request->input('university_id');
                $universityMajorId = $request->input('university_major_id');

                if ($universityId && $universityMajorId) {
                    $exists = DB::table('majors')
                        ->where('id', $universityMajorId)
                        ->where('university_id', $universityId)
                        ->exists();

                    if (! $exists) {
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

                    if (! $exists) {
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
        if (!array_key_exists('book_design_id', $data)) {
            $data['book_design_id'] = null;
        }
        if ($data['gift_type'] !== 'custom') {
            $data['gift_title'] = null;
        }

        // default متوافق مع الداتا الحالية
        $data['status'] = $data['status'] ?? 'Pending';

        $data['back_image_ids']           = json_encode($data['back_image_ids'] ?? []);
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
