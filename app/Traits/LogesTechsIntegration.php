<?php

namespace App\Traits;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

trait LogesTechsIntegration
{
    /**
     * Send the order to the delivery company LogesTechs
     */
    public function dispatchOrderToLogesTechs(Order $order)
    {
        $url = 'https://apisv2.logestechs.com/api/ship/request/by-email';

        // 1. Get login data from .env file
        $email = env('LOGESTECHS_EMAIL');
        $password = env('LOGESTECHS_PASSWORD');
        $companyId = env('LOGESTECHS_COMPANY_ID', 186);

        // Load relationships to access the logestechs_id of each area
        if ($order->delivery_target === 'university' && $order->university_id) {
            $order->load(['university.governorate', 'university.city', 'university.area']);
            $governorate = $order->university?->governorate;
            $city = $order->university?->city;
            $area = $order->university?->area;
        } else {
            $order->load(['governorate', 'city', 'area']);
            $governorate = $order->governorate;
            $city = $order->city;
            $area = $order->area;
        }
        // 2. حساب السعر والكمية بناءً على إذا كان الطلب فردي أو مجموعة
        $cod = (float) ($order->final_price_with_discount ?? $order->final_price ?? 0);
        $quantity = 1;
        $description = 'طلب دفتر تخرج';

        // إذا الطلب تابع لمجموعة، نجمع السعر والكمية لكل المجموعة
        if (! empty($order->discount_code_id)) {
            $groupOrders = Order::where('discount_code_id', $order->discount_code_id)->get();
            $quantity = $groupOrders->count();

            $cod = 0; // نصفر العداد ونجمع أسعار كل زملائه
            foreach ($groupOrders as $gOrder) {
                $cod += (float) ($gOrder->final_price_with_discount ?? $gOrder->final_price ?? 0);
            }
            $description = 'طلب مجموعة (عدد الدفاتر: '.$quantity.')';
        }

        if (! optional($governorate)->logestechs_id || ! optional($city)->logestechs_id || ! optional($area)->logestechs_id) {
            Log::error('LogesTechs Dispatch Blocked: Missing logestechs_id', [
                'order_id' => $order->id,
                'gov_id' => optional($governorate)->logestechs_id,
                'city_id' => optional($city)->logestechs_id,
                'area_id' => optional($area)->logestechs_id,
            ]);

            return [
                'success' => false,
                'message' => "الطلب #{$order->id}: بيانات التوصيل (المنطقة/المدينة/المحافظة) غير مرتبطة بنظام شركة التوصيل. يرجى تشغيل أمر المزامنة.",
            ];
        }
        // 2. Prepare the order data (Payload) as required by the API
        $payload = [
            'email' => $email,
            'password' => $password,
            'pkgUnitType' => 'METRIC',

            'pkg' => [
                'cod' => $cod, // 🔴 السعر الديناميكي
                'notes' => $order->note ?? '',
                'invoiceNumber' => 'ORD-'.$order->id,
                'senderName' => 'اسم متجركم',
                'businessSenderName' => 'اسم متجركم',
                'senderPhone' => '0790000000',
                'receiverName' => $order->username_ar,
                'receiverPhone' => $order->delivery_number_one,
                'receiverPhone2' => $order->delivery_number_two ?? '',
                'serviceType' => 'STANDARD',
                'shipmentType' => 'COD',
                'quantity' => $quantity, // 🔴 الكمية الديناميكية
                'description' => $description, // 🔴 الوصف
            ],
            'destinationAddress' => [
                'addressLine1' => $order->address ?? 'No details',
                'regionId' => optional($governorate)->logestechs_id,
                'cityId' => optional($city)->logestechs_id,
                'villageId' => optional($area)->logestechs_id,
            ],
            'originAddress' => [ // warehouse address from .env
                'addressLine1' => env('LOGESTECHS_ORIGIN_ADDRESS', 'عمان'),
                'regionId' => env('LOGESTECHS_ORIGIN_REGION_ID', 33),
                'cityId' => env('LOGESTECHS_ORIGIN_CITY_ID', 395),
                'villageId' => env('LOGESTECHS_ORIGIN_VILLAGE_ID', 3353),
            ],
        ];

        try {
            // 3. Send the request to the delivery company
            $response = Http::withHeaders([
                'company-id' => $companyId,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->post($url, $payload);

            // 4. Handle the response
            if ($response->successful()) {
                $data = $response->json();

                // Save the ID and barcode returned from the delivery company
                $order->update([
                    'logestechs_order_id' => $data['id'] ?? null,
                ]);

                return ['success' => true, 'data' => $data];
            }

            // In case the request failed (wrong data or from their side)
            $errorBody = $response->body();
            Log::error('LogesTechs Integration Failed', [
                'status' => $response->status(),
                'body' => $errorBody,
                'order_id' => $order->id,
                'area_logestechs_id' => optional($area)->logestechs_id,
                'city_logestechs_id' => optional($city)->logestechs_id,
                'gov_logestechs_id' => optional($governorate)->logestechs_id,
                'receiver_phone' => $order->delivery_number_one,
            ]);

            return [
                'success' => false,
                'message' => 'Failed to dispatch the order to the delivery company. Please check the data.',
            ];

        } catch (\Exception $e) {
            Log::error('LogesTechs Connection Error: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'An error occurred while connecting to the delivery company servers.',
            ];
        }
    }

    /**
     * طباعة بوليصات الشحن (AWBs)
     */
    public function printLogesTechsAWB(array $logestechsIds)
    {
        $companyId = env('LOGESTECHS_COMPANY_ID', 186);
        $url = "https://apisv2.logestechs.com/api/guests/{$companyId}/packages/pdf";

        try {
            $response = Http::withHeaders([
                'company-id' => $companyId,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->post($url, ['ids' => $logestechsIds]);

            if ($response->successful()) {
                // الـ API بيرجع url بصيغة JSON
                return ['success' => true, 'url' => $response->json('url')];
            }

            Log::error('LogesTechs Print AWB Failed: '.$response->body());

            return ['success' => false, 'message' => 'فشل طباعة بوليصات الشحن من المصدر.'];

        } catch (\Exception $e) {
            Log::error('LogesTechs Print AWB Error: '.$e->getMessage());

            return ['success' => false, 'message' => 'خطأ في الاتصال بخوادم شركة التوصيل.'];
        }
    }

    /**
     * إلغاء بوليصة الشحن من شركة التوصيل
     */
    public function cancelLogesTechsShipment($logestechsOrderId)
    {
        $companyId = env('LOGESTECHS_COMPANY_ID', 186);
        $email = env('LOGESTECHS_EMAIL');
        $password = env('LOGESTECHS_PASSWORD');

        $url = "https://apisv2.logestechs.com/api/guests/{$companyId}/packages/{$logestechsOrderId}/cancel";

        try {
            $response = Http::withHeaders([
                'company-id' => $companyId,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->put($url, [
                'email' => $email,
                'password' => $password,
            ]);

            if ($response->successful()) {
                return ['success' => true];
            }

            Log::error('LogesTechs Cancel Shipment Failed: '.$response->body());

            return ['success' => false, 'message' => 'فشل إلغاء الشحنة من نظام شركة التوصيل.'];

        } catch (\Exception $e) {
            Log::error('LogesTechs Cancel Shipment Error: '.$e->getMessage());

            return ['success' => false, 'message' => 'خطأ في الاتصال بخوادم شركة التوصيل.'];
        }
    }
}
