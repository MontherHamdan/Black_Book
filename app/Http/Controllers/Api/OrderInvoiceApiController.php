<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderInvoiceApiController extends Controller
{
    /**
     * Map of status styles & labels for invoice display.
     */
    private function getStatusConfig(): array
    {
        return [
            'new_order' => [
                'class' => 'status-new-order bg-primary text-white p-1 rounded',
                'label' => 'طلب جديد',
            ],
            'needs_modification' => [
                'class' => 'status-needs-modification bg-danger text-white p-1 rounded',
                'label' => 'يوجد تعديل',
            ],
            'Pending' => [
                'class' => 'status-pending',
                'label' => 'تم التصميم',
            ],
            'Completed' => [
                'class' => 'status-completed',
                'label' => 'تم الاعتماد',
            ],
            'preparing' => [
                'class' => 'status-preparing',
                'label' => 'قيد التجهيز',
            ],
            'Printed' => [
                'class' => 'status-printed bg-primary text-white p-1 rounded',
                'label' => 'تم الطباعة',
            ],
            'Received' => [
                'class' => 'status-received',
                'label' => 'تم التسليم',
            ],
            'out_for_delivery' => [
                'class' => 'status-soft-warning',
                'label' => 'خرج مع التوصيل',
            ],
            'returned' => [
                'class' => 'status-soft-orange',
                'label' => 'مرتجع',
            ],
            'Canceled' => [
                'class' => 'status-canceled',
                'label' => 'رفض الإستلام',
            ],
        ];
    }

    /**
     * Resolve Order by ID from route parameter, query param, or request body.
     */
    private function resolveOrder($id, Request $request)
    {
        $orderId = $id ?: $request->input('order_id', $request->input('id'));

        if (!$orderId) {
            return null;
        }

        return Order::with([
            'discountCode',
            'bookType',
            'bookColor',
            'governorate',
            'city',
            'area',
            'bookDesign',
            'bookDecoration',
            'designer',
            'university',
            'universityMajor',
            'diploma',
            'diplomaMajor',
            'deliveryUniversity',
        ])->find($orderId);
    }

    /**
     * API 1: تنزيل فاتورة الطلب بصيغة PDF
     * GET/POST /api/v1/orders/{order_id}/pdf
     * GET/POST /api/v1/orders/pdf?order_id={order_id}
     */
    public function downloadPdf(Request $request, $id = null)
    {
        $order = $this->resolveOrder($id, $request);

        if (!$order) {
            if ($request->wantsJson() || $request->input('format') === 'json') {
                return response()->json([
                    'status' => false,
                    'message' => 'الطلب غير موجود، يرجى التأكد من رقم الطلب (order_id)',
                    'data' => null,
                ], 404);
            }
            abort(404, 'الطلب غير موجود، يرجى التأكد من رقم الطلب (order_id)');
        }

        $statusConfig = $this->getStatusConfig();
        $statusLabel = $statusConfig[$order->status]['label'] ?? $order->status;
        $statusClass = $statusConfig[$order->status]['class'] ?? 'badge-secondary';

        // إذا كان الطلب يتطلب JSON (مثل Postman أو استدعاء برمجياً)
        if ($request->wantsJson() || $request->input('format') === 'json') {
            return response()->json([
                'status' => true,
                'message' => 'تم استخراج رابط تنزيل فاتورة الطلب بصيغة PDF بنجاح',
                'data' => [
                    'order_id' => $order->id,
                    'file_name' => "فاتورة-طلب-#{$order->id}-BlackBook.pdf",
                    'file_type' => 'application/pdf',
                    'download_url' => url("/api/v1/orders/{$order->id}/pdf"),
                    'image_url' => url("/api/v1/orders/{$order->id}/image"),
                    'order_summary' => [
                        'order_number' => "#{$order->id}",
                        'customer_name' => $order->username_ar ?: $order->username_en,
                        'phone_number' => $order->user_phone_number,
                        'total_price' => $order->total_price,
                        'status' => $order->status,
                        'status_label' => $statusLabel,
                        'created_at' => $order->created_at?->format('Y-m-d H:i:s'),
                    ],
                ],
            ]);
        }

        // إذا كان فتح الرابط مباشرة بالمتصفح أو التطبيق (WebView):
        // يتم عرض الفاتورة وتشغيل التنزيل التلقائي لملف الـ PDF مباشرة
        $autoDownload = ($request->input('preview') == '1' || $request->input('action') === 'view') ? null : 'pdf';

        return view('admin.order.invoice', compact('order', 'statusLabel', 'statusClass', 'autoDownload'));
    }

    /**
     * API 2: تنزيل فاتورة الطلب بصيغة صورة (PNG)
     * GET/POST /api/v1/orders/{order_id}/image
     * GET/POST /api/v1/orders/image?order_id={order_id}
     */
    public function downloadImage(Request $request, $id = null)
    {
        $order = $this->resolveOrder($id, $request);

        if (!$order) {
            if ($request->wantsJson() || $request->input('format') === 'json') {
                return response()->json([
                    'status' => false,
                    'message' => 'الطلب غير موجود، يرجى التأكد من رقم الطلب (order_id)',
                    'data' => null,
                ], 404);
            }
            abort(404, 'الطلب غير موجود، يرجى التأكد من رقم الطلب (order_id)');
        }

        $statusConfig = $this->getStatusConfig();
        $statusLabel = $statusConfig[$order->status]['label'] ?? $order->status;
        $statusClass = $statusConfig[$order->status]['class'] ?? 'badge-secondary';

        // إذا كان الطلب يتطلب JSON (مثل Postman أو استدعاء برمجياً)
        if ($request->wantsJson() || $request->input('format') === 'json') {
            return response()->json([
                'status' => true,
                'message' => 'تم استخراج رابط تنزيل فاتورة الطلب بصيغة صورة بنجاح',
                'data' => [
                    'order_id' => $order->id,
                    'file_name' => "فاتورة-طلب-#{$order->id}-BlackBook.png",
                    'file_type' => 'image/png',
                    'download_url' => url("/api/v1/orders/{$order->id}/image"),
                    'pdf_url' => url("/api/v1/orders/{$order->id}/pdf"),
                    'order_summary' => [
                        'order_number' => "#{$order->id}",
                        'customer_name' => $order->username_ar ?: $order->username_en,
                        'phone_number' => $order->user_phone_number,
                        'total_price' => $order->total_price,
                        'status' => $order->status,
                        'status_label' => $statusLabel,
                        'created_at' => $order->created_at?->format('Y-m-d H:i:s'),
                    ],
                ],
            ]);
        }

        // إذا كان فتح الرابط مباشرة بالمتصفح أو التطبيق (WebView):
        // يتم عرض الفاتورة وتشغيل التنزيل التلقائي للصورة مباشرة
        $autoDownload = ($request->input('preview') == '1' || $request->input('action') === 'view') ? null : 'image';

        return view('admin.order.invoice', compact('order', 'statusLabel', 'statusClass', 'autoDownload'));
    }
}
