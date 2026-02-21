<?php

namespace App\Http\Controllers;

use App\Models\BookDecoration;
use Carbon\Carbon;
use App\Models\Note;
use App\Models\Order;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Exports\OrdersExport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelFormat;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\UserImage;
use Illuminate\Support\Facades\Log;
use App\Support\ArabicNameNormalizer;
use App\Models\SvgName;

class OrderWebController extends Controller
{
    public function index()
    {
        $designers = User::where('role', User::ROLE_DESIGNER)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin.order.index', compact('designers'));
    }

    public function show($id)
    {
        /** @var \App\Models\User $authUser */
        $authUser = auth()->user();

        $order = Order::with([
            'discountCode',
            'bookType',
            'bookDesign',
            'bookDecoration',
            'frontImage',
            'transparentPrinting',
            'svg',
            'notes.user',
            'designer',
            'university',
            'universityMajor',
            'diploma',
            'diplomaMajor',
        ])->findOrFail($id);

        $decorations = BookDecoration::orderBy('id')
            ->get(['id', 'name', 'image']);

        $designers = User::where('role', User::ROLE_DESIGNER)
            ->orderBy('name')
            ->get(['id', 'name']);

        // نحمل المصمم لو مش محمل
        $order->loadMissing('designer');

        // 🔹 فلاغات عامة عن المستخدم
        $isAdmin = $authUser->isAdmin();
        $isDesigner = $authUser->isDesigner();

        $designerIsAssigned = !is_null($order->designer_id);
        $designerIsCurrentUser = $designerIsAssigned && (int) $order->designer_id === (int) $authUser->id;
        $customDesignImages = $order->customDesignImagesFromIds();
        $customDesignImages = $customDesignImages->map(function ($img) {
            $img->resolved_url = $this->resolveImageUrl($img->image_path ?? null);
            return $img;
        });

        // =========================
        // 🔹 1) SVG الخاص بالاسم العربي
        // =========================
        $svgCodeForName = $this->resolveNameSvg($order->username_ar ?? null);

        // =========================
        // 🔹 2) إعداد Config الحالات
        // =========================
        $statusConfig = $this->statusConfig();

        // الهيدر
        $currentStatusHeader = $statusConfig[$order->status] ?? [
            'class' => 'status-unknown',
            'label' => $order->status,
        ];

        $canChangeStatusHeader = $isAdmin
            || ($order->designer && $order->designer->id === $authUser->id);

        $canChangeDesignerHeader =
            $isAdmin ||
            (
                $isDesigner
                && (
                    !$order->designer_id || (int) $order->designer_id === (int) $authUser->id
                )
            );

        $designerNameHeader = $order->designer->name ?? 'غير معيّن';

        if ($order->discountCode && $order->discountCode->code_name) {
            $groupNameHeader = $order->discountCode->code_name;
        } elseif ($order->discountCode) {
            $groupNameHeader = $order->discountCode->discount_code;
        } else {
            $groupNameHeader = null;
        }

        $graduateNameHeader = $order->username_ar ?? 'غير متوفر';

        // =========================
        // 🔹 3) تبويب "معلومات الخريج"
        // =========================

        $currentStatus = $statusConfig[$order->status] ?? [
            'class' => 'status-unknown',
            'label' => $order->status,
        ];

        $canChangeStatus = $canChangeStatusHeader;

        $designerName = $order->designer->name ?? 'غير معيّن';
        $designerInitial = $designerName ? mb_substr($designerName, 0, 1, 'UTF-8') : null;

        // صورة التصميم المختار + العنوان
        [$designImagePath, $designTitle] = $this->resolveDesignImage($order);

        // معلومات الـ SVG لعبارة الدفتر
        $hasSvg = (bool) ($order->svg && $order->svg->svg_code);
        $svgTitle = $order->svg->title ?? null;

        $canEditDesignFollowup = $isAdmin || $isDesigner;
        $designFollowupText = $order->design_followup_note;

        // صور الخريج (تصميم آخر + أمامية + خلفيات)
        $frontSrc = $this->resolveImageUrl(optional($order->frontImage)->image_path);
        $anotherSrc = $customDesignImages->first()->resolved_url ?? null;


        $backImages = $order->back_images ?? collect();
        $backImages = $backImages->map(function ($img) {
            $img->resolved_url = $this->resolveImageUrl($img->image_path ?? null);
            return $img;
        });

        // =========================
        // 🔹 4) تبويب "الدفتر من الداخل"
        // =========================

        $internalImages = $order->additionalImagesFromIds();
        $internalImagesCount = $internalImages ? $internalImages->count() : 0;

        $internalImages = $internalImages->map(function ($img) {
            $img->resolved_url = $this->resolveImageUrl($img->image_path ?? null);
            return $img;
        });

        $transparentImage = $this->resolveImageUrl(
            optional($order->transparentPrinting)->image_path
        );

        // للزخرفة نستخدم نفس التخزين كما هو (لو عندك pattern معيّن للـ path ممكن تستخدم resolveImageUrl هنا أيضًا)
        $decorationImage = $order->bookDecoration->image ?? null;

        $giftTitleInternal = $order->gift_title;
        $giftTypeInternal = $order->gift_type ?? 'default';

        // =========================
        // 🔹 5) تبويب "تجليد الدفتر"
        // =========================

        $canEditBinding = $isAdmin || $isDesigner;
        $canAddNote = $canEditBinding;

        $bindingInternalImages = $internalImages;
        $internalImagesCountBinding = $internalImagesCount;

        $pagesCount = $order->pages_number ?? 0;
        $giftTitleBinding = $order->gift_title;
        $giftTypeBinding = $order->gift_type ?? 'default';
        $transparentImagePath = $transparentImage;

        $bindingFollowupText = $order->binding_followup_note;

        // =========================
        // 🔹 6) تبويب "معلومات التوصيل"
        // =========================

        $canEditDeliveryFollowup = $isAdmin || $isDesigner;
        $deliveryFollowupText = $order->delivery_followup_note;

        // نص الإهداء الموحّد
        $defaultGiftText = config('app.default_gift_text', 'نص الإهداء الموحّد يوضع هنا...');

        // =========================
        // 🔹 7) تمرير كل شيء للـ View
        // =========================

        return view('admin.order.show', [
            'order' => $order,
            'decorations' => $decorations,
            'designers' => $designers,

            'isAdmin' => $isAdmin,
            'isDesigner' => $isDesigner,
            'designerIsAssigned' => $designerIsAssigned,
            'designerIsCurrentUser' => $designerIsCurrentUser,

            // SVG للاسم
            'svgCodeForName' => $svgCodeForName,

            // Config الحالات
            'statusConfigHeader' => $statusConfig,
            'statusConfig' => $statusConfig,
            'currentStatusHeader' => $currentStatusHeader,
            'canChangeStatusHeader' => $canChangeStatusHeader,
            'canChangeDesignerHeader' => $canChangeDesignerHeader,
            'designerNameHeader' => $designerNameHeader,
            'groupNameHeader' => $groupNameHeader,
            'graduateNameHeader' => $graduateNameHeader,

            // تبويب "معلومات الخريج"
            'currentStatus' => $currentStatus,
            'canChangeStatus' => $canChangeStatus,
            'designerName' => $designerName,
            'designerInitial' => $designerInitial,
            'designImagePath' => $designImagePath,
            'designTitle' => $designTitle,
            'hasSvg' => $hasSvg,
            'svgTitle' => $svgTitle,
            'canEditDesignFollowup' => $canEditDesignFollowup,
            'designFollowupText' => $designFollowupText,
            'frontSrc' => $frontSrc,
            'anotherSrc' => $anotherSrc,
            'backImages' => $backImages,

            // تبويب "الدفتر من الداخل"
            'internalImages' => $internalImages,
            'internalImagesCount' => $internalImagesCount,
            'transparentImage' => $transparentImage,
            'decorationImage' => $decorationImage,
            'giftTitleInternal' => $giftTitleInternal,
            'giftTypeInternal' => $giftTypeInternal,

            // تبويب "تجليد الدفتر"
            'bindingInternalImages' => $bindingInternalImages,
            'internalImagesCountBinding' => $internalImagesCountBinding,
            'pagesCount' => $pagesCount,
            'giftTitleBinding' => $giftTitleBinding,
            'giftTypeBinding' => $giftTypeBinding,
            'transparentImagePath' => $transparentImagePath,
            'canEditBinding' => $canEditBinding,
            'canAddNote' => $canAddNote,
            'bindingFollowupText' => $bindingFollowupText,

            // تبويب "معلومات التوصيل"
            'canEditDeliveryFollowup' => $canEditDeliveryFollowup,
            'deliveryFollowupText' => $deliveryFollowupText,

            // نص الإهداء الموحّد
            'defaultGiftText' => $defaultGiftText,
            'customDesignImages' => $customDesignImages,

        ]);
    }

    /**
     * إعداد كونفيغ حالات الطلب (class + label) للاستخدام في جميع التبويبات.
     */
    private function statusConfig(): array
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
            'Received' => [
                'class' => 'status-received',
                'label' => 'تم التسليم',
            ],
            'Out for Delivery' => [
                'class' => 'status-out-for-delivery',
                'label' => 'مرتجع',
            ],
            'Canceled' => [
                'class' => 'status-canceled',
                'label' => 'رفض الإستلام',
            ],
        ];
    }

    /**
     * Fetch orders for DataTable (server-side).
     */
    public function fetchOrders(Request $request)
    {
        $perPage = $request->input('length', 10);
        $page = ($request->input('start', 0) / max($perPage, 1)) + 1;

        $columnIndex = $request->input('order.0.column');
        $columnDataKey = $request->input('columns')[$columnIndex]['data'] ?? 'id';
        $sortDirection = $request->input('order.0.dir') ?? 'desc';

        $columnMap = [
            'id' => 'id',
            'data' => 'created_at',
            'status' => 'status',
            'designer' => 'designer_id',
            'username' => 'username_ar',
            'order' => 'book_type_id',
            'governorate' => 'governorate',
            'address' => 'address',
            'school_name' => 'university_id',
            'phone' => 'user_phone_number',
            'phone2' => 'delivery_number_two',
            'price' => 'final_price_with_discount',
            'actions' => 'id',
        ];

        $sortColumn = $columnMap[$columnDataKey] ?? 'id';

        $searchValue = $request->input('search.value');
        $statusFilter = $request->input('status');
        $additivesFilter = $request->input('additives'); // with_additives / with_out_additives
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $query = Order::with([
            'discountCode',
            'bookType',
            'bookDesign',
            'frontImage',
            'transparentPrinting',
            'designer',
        ]);


        // 🔎 بحث عام
        if (!empty($searchValue)) {
            $query->where(function ($query) use ($searchValue) {
                $query->where('username_ar', 'like', "%{$searchValue}%")
                    ->orWhere('username_en', 'like', "%{$searchValue}%")
                    ->orWhere('governorate', 'like', "%{$searchValue}%")
                    ->orWhere('address', 'like', "%{$searchValue}%")
                    ->orWhere('user_phone_number', 'like', "%{$searchValue}%")
                    ->orWhere('delivery_number_two', 'like', "%{$searchValue}%")
                    ->orWhere('status', 'like', "%{$searchValue}%")
                    ->orWhere('final_price_with_discount', 'like', "%{$searchValue}%");
            });
        }

        // 🎯 فلتر الحالة
        if (!empty($statusFilter)) {
            $query->where('status', $statusFilter);
        }

        // 🟡 فلتر الإضافات
        if ($additivesFilter === 'with_additives') {
            $query->where('is_with_additives', true);
        } elseif ($additivesFilter === 'with_out_additives') {
            $query->where(function ($q) {
                $q->where('is_with_additives', false)
                    ->orWhereNull('is_with_additives');
            });
        }

        // 📅 فلاتر التاريخ
        if (!empty($dateFrom)) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if (!empty($dateTo)) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        // ☎️ حساب أرقام الهواتف المكررة (ضمن الفلاتر الحالية)
        $duplicatePhones = (clone $query)
            ->select('user_phone_number')
            ->whereNotNull('user_phone_number')
            ->groupBy('user_phone_number')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('user_phone_number')
            ->toArray();

        // ⬇ ترتيب + Pagination
        $orders = $query
            ->orderBy($sortColumn, $sortDirection)
            ->paginate($perPage, ['*'], 'page', $page);

        $formattedOrders = $orders->getCollection()->map(function ($order) use ($duplicatePhones) {
            // ⏱️ معالجة التاريخ بأمان
            try {
                $createdAt = $order->created_at
                    ? ($order->created_at instanceof \Carbon\Carbon
                        ? $order->created_at->timezone('Asia/Amman')
                        : \Carbon\Carbon::parse($order->created_at)->timezone('Asia/Amman'))
                    : null;
            } catch (\Throwable $e) {
                $createdAt = null;
            }

            $createdAtFormatted = $createdAt
                ? $createdAt->format('d-m-Y, h:i A')
                : '';

            $statusDiff = $createdAt
                ? $createdAt->diffForHumans()
                : '';

            return [
                'id' => $order->id,
                'data' => $createdAtFormatted,
                'status_created_diff' => $statusDiff,

                'username' => $order->username_ar . ' / ' . $order->username_en,
                'order' => $order->bookType?->name_ar ?? '',
                'governorate' => $order->governorate,
                'address' => $order->address,

                // ✅ عشان DataTables ما يشتكي: نرجع school_name حتى لو فاضي مؤقتًا
                'school_name' => '',

                'phone' => $order->user_phone_number,
                'phone2' => $order->delivery_number_two,
                'status' => $order->status,
                'price' => $order->final_price_with_discount,

                'has_notes' => Note::where('order_id', $order->id)->exists(),
                'is_duplicate_phone' => in_array($order->user_phone_number, $duplicatePhones),
                'is_with_additives' => (bool) $order->is_with_additives,

                'designer' => $order->designer ? [
                    'id' => $order->designer->id,
                    'name' => $order->designer->name,
                ] : null,

                'actions' => view('admin.order.partials.actions', compact('order'))->render(),
            ];
        });

        return response()->json([
            'draw' => $request->input('draw'),
            'recordsTotal' => Order::count(),
            'recordsFiltered' => $orders->total(),
            'data' => $formattedOrders,
        ]);
    }



    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:orders,id',
            'status' => 'required|in:new_order,needs_modification,Pending,preparing,Completed,Out for Delivery,Received,Canceled',
        ]);

        /** @var \App\Models\User $user */
        $user = $request->user();
        $order = Order::with('designer')->findOrFail($request->id); // جلبنا الديزاينر مع الطلب

        // 🛡️ التحقق من الصلاحيات
        if (!$user->isAdmin()) {
            if (!$user->isDesigner() || $order->designer_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'غير مصرح لك بتحديث حالة هذا الطلب.',
                ], 403);
            }
        }

        $newStatus = $request->status;

        // ✅ الحالات اللي نعتبر عندها شغل المصمم "منجَز"
        $designerDoneStatuses = [
            'Completed',        // تم الاعتماد
            'Received',         // تم التسليم
            'Out for Delivery', // مرتجع
            'Canceled',         // رفض الإستلام
            'preparing',        // قيد التجهيز (أضفناها هنا لتُحسب العمولة بمجرد تغييرها لهذه الحالة)
        ];

        // تحديث حالة الطلب
        $order->status = $newStatus;

        // 💰 حساب العمولة وتأكيد الإنجاز
        if (in_array($newStatus, $designerDoneStatuses, true)) {

            // نعتبر الطلب منجز
            $order->designer_done = true;
            if (!$order->designer_done_at) {
                $order->designer_done_at = now();
            }

            // نحسب العمولة فقط إذا ما كانت محسوبة من قبل وتأكدنا إنه الطلب مربوط بمصمم فعلاً
            if (is_null($order->designer_commission) && $order->designer) {

                $designer = $order->designer;
                $commission = (float) ($designer->base_order_price ?? 0);

                // 1. فحص الزخرفة (استخدمنا الاسم الصحيح بالـ s)
                if (!empty($order->book_decorations_id)) {
                    $commission += (float) ($designer->decoration_price ?? 0);
                }

                // 2. فحص الإهداء المخصص
                if ($order->gift_type === 'custom') {
                    $commission += (float) ($designer->custom_gift_price ?? 0);
                }

                // 3. فحص الصورة الداخلية (فحصنا حقل الـ JSON الجديد)
                $additionalIds = $order->additional_image_id;
                // احتياطاً لو الداتا رجعت كنص (String) بدل مصفوفة (Array)
                if (is_string($additionalIds)) {
                    $additionalIds = json_decode($additionalIds, true);
                }
                if (is_array($additionalIds) && !empty($additionalIds)) {
                    $commission += (float) ($designer->internal_image_price ?? 0);
                }

                // حفظ العمولة النهائية في الطلب
                $order->designer_commission = $commission;
            }
        }

        $order->save();

        // 👇 نفس config الموجود في الـ Blade عشان نرجع label + class جاهزين للـ JS
        $statusConfig = [
            'new_order' => [
                'class' => 'bg-primary text-white',
                'label' => 'طلب جديد',
            ],
            'needs_modification' => [
                'class' => 'bg-danger text-white',
                'label' => 'يوجد تعديل',
            ],
            'Pending' => [
                'class' => 'bg-warning text-dark',
                'label' => 'تم التصميم',
            ],
            'Completed' => [
                'class' => 'bg-info text-dark',
                'label' => 'تم الاعتماد',
            ],
            'preparing' => [
                'class' => 'bg-purple',
                'label' => 'قيد التجهيز',
            ],
            'Received' => [
                'class' => 'bg-success text-white',
                'label' => 'تم التسليم',
            ],
            'Out for Delivery' => [
                'class' => 'bg-orange',
                'label' => 'مرتجع',
            ],
            'Canceled' => [
                'class' => 'bg-maroon',
                'label' => 'رفض الإستلام',
            ],
        ];

        $cfg = $statusConfig[$order->status] ?? [
            'class' => 'bg-secondary',
            'label' => $order->status,
        ];

        return response()->json([
            'success' => true,
            'status' => $order->status,
            'label' => $cfg['label'],
            'class' => $cfg['class'],
        ]);
    }




    /**
     * Delete a single order and all related data.
     * Only admins can delete orders.
     */
    public function destroy($id)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        // 🛡️ Only admins can delete orders
        if (!$user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بحذف الطلبات.',
            ], 403);
        }

        $order = Order::findOrFail($id);
        $this->deleteOrderAndRelatedData($order);

        return response()->json([
            'success' => true,
            'message' => 'تم حذف الطلب بنجاح!',
        ]);
    }

    /**
     * Bulk delete multiple orders.
     * Only admins can perform bulk delete.
     */
    public function bulkDelete(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        // 🛡️ Only admins can bulk delete
        if (!$user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بحذف الطلبات.',
            ], 403);
        }

        $request->validate([
            'order_ids' => 'required|array',
            'order_ids.*' => 'required|exists:orders,id',
        ]);

        $orderIds = $request->input('order_ids', []);
        $orders = Order::whereIn('id', $orderIds)->get();

        $deletedCount = 0;
        $errors = [];

        foreach ($orders as $order) {
            try {
                $this->deleteOrderAndRelatedData($order);
                $deletedCount++;
            } catch (\Exception $e) {
                $errors[] = "فشل حذف الطلب #{$order->id}: " . $e->getMessage();
                Log::error('Bulk delete order failed', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => "تم حذف {$deletedCount} طلب بنجاح.",
            'deleted_count' => $deletedCount,
            'errors' => $errors,
        ]);
    }

    /**
     * Comprehensive method to delete an order and all its related data:
     * - Notes (cascade delete via foreign key)
     * - UserImage records (front, transparent, internal, back, additional, custom)
     * - Physical image files from storage
     * - The order itself (soft delete)
     */
    private function deleteOrderAndRelatedData(Order $order): void
    {
        // 📸 Collect all UserImage IDs that need to be checked/deleted
        $imageIdsToCheck = [];

        // Front image
        if ($order->front_image_id) {
            $imageIdsToCheck[] = $order->front_image_id;
        }

        // Transparent printing image
        if ($order->transparent_printing_id) {
            $imageIdsToCheck[] = $order->transparent_printing_id;
        }

        // Internal image
        if ($order->internal_image_id) {
            $imageIdsToCheck[] = $order->internal_image_id;
        }

        // Back images (from JSON array)
        $backImageIds = $order->back_image_ids;
        if (is_string($backImageIds)) {
            $backImageIds = json_decode($backImageIds, true);
        }
        if (is_array($backImageIds) && !empty($backImageIds)) {
            $imageIdsToCheck = array_merge($imageIdsToCheck, $backImageIds);
        }

        // Additional images (from JSON array)
        $additionalImageIds = $order->additional_image_id;
        if (is_string($additionalImageIds)) {
            $additionalImageIds = json_decode($additionalImageIds, true);
        }
        if (is_array($additionalImageIds) && !empty($additionalImageIds)) {
            $imageIdsToCheck = array_merge($imageIdsToCheck, $additionalImageIds);
        }

        // Custom design images (from JSON array)
        $customDesignImageIds = $order->custom_design_image_id;
        if (is_string($customDesignImageIds)) {
            $customDesignImageIds = json_decode($customDesignImageIds, true);
        }
        if (is_array($customDesignImageIds) && !empty($customDesignImageIds)) {
            $imageIdsToCheck = array_merge($imageIdsToCheck, $customDesignImageIds);
        }

        // Remove duplicates
        $imageIdsToCheck = array_unique(array_filter($imageIdsToCheck));

        // 🗑️ Delete physical image files and UserImage records
        if (!empty($imageIdsToCheck)) {
            $userImages = UserImage::whereIn('id', $imageIdsToCheck)->get();

            foreach ($userImages as $userImage) {
                $this->deleteUserImageFile($userImage);
            }

            // Check if these images are used by other orders before deleting
            // We only delete UserImage records if they're not used elsewhere
            foreach ($imageIdsToCheck as $imageId) {
                // Get all other orders and check if they use this image
                $otherOrders = Order::where('id', '!=', $order->id)->get();
                $isUsedElsewhere = false;

                foreach ($otherOrders as $otherOrder) {
                    // Check direct foreign key columns
                    if (
                        $otherOrder->front_image_id == $imageId ||
                        $otherOrder->transparent_printing_id == $imageId ||
                        $otherOrder->internal_image_id == $imageId
                    ) {
                        $isUsedElsewhere = true;
                        break;
                    }

                    // Check JSON array columns
                    $backIds = is_string($otherOrder->back_image_ids)
                        ? json_decode($otherOrder->back_image_ids, true)
                        : $otherOrder->back_image_ids;
                    if (is_array($backIds) && in_array($imageId, $backIds)) {
                        $isUsedElsewhere = true;
                        break;
                    }

                    $additionalIds = is_string($otherOrder->additional_image_id)
                        ? json_decode($otherOrder->additional_image_id, true)
                        : $otherOrder->additional_image_id;
                    if (is_array($additionalIds) && in_array($imageId, $additionalIds)) {
                        $isUsedElsewhere = true;
                        break;
                    }

                    $customIds = is_string($otherOrder->custom_design_image_id)
                        ? json_decode($otherOrder->custom_design_image_id, true)
                        : $otherOrder->custom_design_image_id;
                    if (is_array($customIds) && in_array($imageId, $customIds)) {
                        $isUsedElsewhere = true;
                        break;
                    }
                }

                if (!$isUsedElsewhere) {
                    UserImage::where('id', $imageId)->delete();
                }
            }
        }

        // 📝 Notes will be automatically deleted via foreign key cascade
        // But we can explicitly delete them for clarity
        $order->notes()->delete();

        // 🗑️ Soft delete the order
        $order->delete();
    }

    /**
     * Delete physical image file from storage.
     */
    private function deleteUserImageFile(UserImage $userImage): void
    {
        if (!$userImage->image_path) {
            return;
        }

        $path = $userImage->image_path;

        // Skip external URLs
        if (Str::startsWith($path, ['http://', 'https://'])) {
            return;
        }

        // Handle different path formats
        $filePath = null;

        if (Str::startsWith($path, ['/storage/'])) {
            $relative = ltrim(str_replace('/storage/', '', $path), '/');
            $filePath = storage_path('app/public/' . $relative);
        } elseif (Str::startsWith($path, ['user_images/'])) {
            $filePath = storage_path('app/public/' . ltrim($path, '/'));
        } else {
            // Assume it's just a filename in user_images directory
            $filePath = storage_path('app/public/user_images/' . ltrim($path, '/'));
        }

        // Delete the file if it exists
        if ($filePath && file_exists($filePath)) {
            @unlink($filePath);
        }
    }

    public function addNote(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'note' => 'required|string|max:1000',
        ]);

        $note = new Note();
        $note->order_id = $request->order_id;
        $note->user_id = auth()->id();
        $note->content = $request->note;

        if ($note->save()) {
            return response()->json([
                'success' => true,
                'note' => [
                    'id' => $note->id,
                    'content' => $note->content,
                    'created_at' => $note->created_at->format('d M Y h:i A'),
                    'user_name' => $note->user->name,
                ],
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Failed to save note.'], 500);
    }

    public function getNotes($orderId)
    {
        $notes = Note::where('order_id', $orderId)
            ->with('user:id,name')
            ->latest()
            ->get(['id', 'content', 'created_at', 'user_id']);

        return response()->json([
            'notes' => $notes->map(function ($note) {
                return [
                    'id' => $note->id,
                    'content' => $note->content,
                    'created_at' => $note->created_at->format('d M Y , h:i A'),
                    'user_name' => $note->user->name,
                ];
            }),
        ]);
    }



    public function downloadAllBackImages($orderId)
    {
        $order = Order::findOrFail($orderId);

        // 🟢 1) نجيب الصور من الـ Accessor (getBackImagesAttribute)
        $backImages = $order->back_images; // Collection من UserImage

        if ($backImages->isEmpty()) {
            return back()->with('error', 'لا توجد صور خلفية متاحة لهذا الطلب.');
        }

        // 🟢 2) تحضير مسار ملف الـ ZIP داخل storage/app
        $zipFileName = 'back_images_' . $orderId . '.zip';
        $zipFilePath = storage_path('app/' . $zipFileName);

        $zipDir = dirname($zipFilePath);
        if (!is_dir($zipDir)) {
            mkdir($zipDir, 0755, true);
        }

        if (file_exists($zipFilePath)) {
            @unlink($zipFilePath);
        }

        $zip = new \ZipArchive();

        $openResult = $zip->open($zipFilePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        if ($openResult !== true) {
            Log::error('Zip open failed', [
                'result' => $openResult,
                'path' => $zipFilePath,
            ]);

            return back()->with('error', 'فشل إنشاء ملف ZIP (ZipArchive open).');
        }

        $tempFiles = [];

        foreach ($backImages as $img) {
            $path = $img->image_path;

            // 🔹 1) لو الصورة URL كامل
            if (Str::startsWith($path, ['http://', 'https://'])) {

                $imageContent = @file_get_contents($path);
                if ($imageContent === false) {
                    Log::warning('Failed to read image from URL', ['url' => $path]);
                    continue;
                }

                $fileName = basename(parse_url($path, PHP_URL_PATH)) ?: ('image_' . $img->id . '.jpg');

                $tmpDir = storage_path('app/tmp');
                if (!is_dir($tmpDir)) {
                    mkdir($tmpDir, 0755, true);
                }

                $tempPath = $tmpDir . '/' . uniqid('img_', true) . '_' . $fileName;

                file_put_contents($tempPath, $imageContent);

                $zip->addFile($tempPath, $fileName);
                $tempFiles[] = $tempPath;
            }

            // 🔹 2) لو مسار محلي
            else {

                $originalPath = $path;

                if (Str::startsWith($path, ['/storage/'])) {
                    $relative = ltrim(str_replace('/storage/', '', $path), '/');
                    $localPath = storage_path('app/public/' . $relative);
                } else {
                    // فقط اسم ملف → نضيف له user_images/
                    if (!Str::contains($path, '/')) {
                        $path = 'user_images/' . ltrim($path, '/');
                    }

                    $localPath = storage_path('app/public/' . ltrim($path, '/'));
                }

                if (!file_exists($localPath)) {
                    Log::warning('Local image not found for ZIP', [
                        'db_path' => $originalPath,
                        'final_path' => $path,
                        'local_path' => $localPath,
                    ]);
                    continue;
                }

                $zip->addFile($localPath, basename($localPath));
            }
        }

        $closeResult = $zip->close();

        if ($closeResult === false) {
            Log::error('Zip close failed', ['path' => $zipFilePath]);
            return back()->with('error', 'فشل إغلاق ملف ZIP.');
        }

        if (!file_exists($zipFilePath)) {
            Log::error('ZIP file not found after close()', ['path' => $zipFilePath]);
            return back()->with('error', 'لم يتم إنشاء ملف ZIP بنجاح.');
        }

        return response()->download($zipFilePath)->deleteFileAfterSend(true);
    }




    /**
     * Export filtered orders as CSV.
     */
    public function exportExcel(Request $request)
    {
        $filters = [
            'status' => $request->get('status'),
            'additives' => $request->get('additives'),
            'search' => $request->get('search'),
            'date_from' => $request->get('date_from'),
            'date_to' => $request->get('date_to'),
        ];

        $fileName = 'orders-' . now()->format('Y-m-d_H-i-s') . '.csv';

        return Excel::download(
            new OrdersExport($filters),
            $fileName,
            ExcelFormat::CSV,
            [
                'Content-Type' => 'text/csv; charset=UTF-8',
            ]
        );
    }

    public function downloadAllAdditionalImages($orderId)
    {
        // نجيب الطلب
        $order = Order::findOrFail($orderId);

        // نجيب الصور الإضافية من الـ JSON الموجود في additional_image_id
        $images = $order->additionalImagesFromIds(); // Collection من UserImage

        if ($images->isEmpty()) {
            return back()->with('error', 'لا توجد صور إضافية لهذا الطلب.');
        }

        $zip = new \ZipArchive();
        $zipFileName = 'additional_images_' . $orderId . '.zip';
        $zipFilePath = storage_path('app/public/' . $zipFileName);

        // نتأكد من وجود فولدر storage/app/public
        $zipDir = dirname($zipFilePath);
        if (!is_dir($zipDir)) {
            mkdir($zipDir, 0755, true);
        }

        // لو في ملف قديم بنفس الاسم نحذفه
        if (file_exists($zipFilePath)) {
            @unlink($zipFilePath);
        }

        if ($zip->open($zipFilePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {

            foreach ($images as $img) {
                if (!$img->image_path) {
                    continue;
                }

                $path = $img->image_path;

                // 🔹 لو الصورة URL خارجي
                if (Str::startsWith($path, ['http://', 'https://'])) {
                    try {
                        $contents = @file_get_contents($path);
                        if ($contents === false) {
                            continue;
                        }

                        $fileName = basename(parse_url($path, PHP_URL_PATH)) ?: ('image_' . $img->id . '.jpg');
                        $tempPath = storage_path('app/tmp_' . $fileName);

                        // نخزنها مؤقتًا
                        file_put_contents($tempPath, $contents);

                        // نضيفها للـ ZIP
                        $zip->addFile($tempPath, $fileName);
                    } catch (\Throwable $e) {
                        continue;
                    }
                }
                // 🔹 صورة مرفوعة ومحفوظة في storage/user_images
                else {
                    // نفس المنطق اللي مستخدمه في backImages
                    if (Str::startsWith($path, ['/storage/'])) {
                        $relative = ltrim(str_replace('/storage/', '', $path), '/');
                        $localPath = storage_path('app/public/' . $relative);
                    } elseif (Str::startsWith($path, ['user_images/'])) {
                        $localPath = storage_path('app/public/' . ltrim($path, '/'));
                    } else {
                        // اعتبره اسم ملف عادي داخل user_images
                        $localPath = storage_path('app/public/user_images/' . ltrim($path, '/'));
                    }

                    if (file_exists($localPath)) {
                        $zip->addFile($localPath, basename($localPath));
                    }
                }
            }

            $zip->close();

            return response()->download($zipFilePath)->deleteFileAfterSend(true);
        }

        return back()->with('error', 'Failed to create ZIP file.');
    }


    public function updateDesigner(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'designer_id' => 'nullable|exists:users,id',
        ]);

        $order = Order::findOrFail($request->order_id);
        $user = $request->user();

        // ✅ لو مو Admin
        if (!$user->isAdmin()) {

            // لازم يكون Designer أصلاً
            if (!$user->isDesigner()) {
                return response()->json([
                    'success' => false,
                    'message' => 'غير مصرح لك بتعديل المصمم.',
                ], 403);
            }

            // الطلب معيّن على مصمم آخر → ممنوع يلمسه
            if ($order->designer_id && $order->designer_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'هذا الطلب معيّن لمصمم آخر.',
                ], 403);
            }

            // المصمم العادي يسمح له فقط بتعيين نفسه على الطلب
            if ((int) $request->designer_id !== (int) $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'يمكنك فقط تعيين نفسك كمصمم لهذا الطلب.',
                ], 403);
            }
        }

        // 📝 حفظ الـ designer_id (Admin يقدر يعيّن أي مصمم أو يفرّغ)
        $order->designer_id = $request->designer_id ?: null;
        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث المصمم بنجاح.',
        ]);
    }
    public function updateBinding(Request $request, $id)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        // 🛡️ فقط أدمن أو ديزاينر
        if (!$user->isAdmin() && !$user->isDesigner()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'غير مصرح لك بتعديل تجليد الدفتر.',
                ], 403);
            }

            abort(403, 'غير مصرح لك بتعديل تجليد الدفتر.');
        }

        $order = Order::with('bookDecoration')->findOrFail($id);

        // ✅ فاليديشين
        $validated = $request->validate([
            'is_with_additives' => ['nullable'], // checkbox
            'is_sponge' => ['nullable'], // checkbox
            'gift_title' => ['nullable', 'string', 'max:1000'],
            'internal_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:20480'],
            'transparent_printing_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:20480'],

            // ⬅️ حقل النص الجديد لاسم الزخرفة
            'book_decoration_name' => ['nullable', 'string', 'max:255'],
            'binding_followup_note' => ['nullable', 'string', 'max:5000'],
        ]);

        // 🧽 إسفنج
        $order->is_sponge = $request->boolean('is_sponge');

        // 📝 تعديل نص الزخرفة (تعديل الـ BookDecoration نفسه)
        if ($request->filled('book_decoration_name') && $order->bookDecoration) {
            $order->bookDecoration->name = $request->input('book_decoration_name');
            $order->bookDecoration->save();
        }

        // 🖼️ رفع / تغيير الصورة الداخلية
        if ($request->hasFile('internal_image')) {
            $file = $request->file('internal_image');

            $timestamp = time();
            $original = $file->getClientOriginalName();
            $imageName = $timestamp . '_' . $original;

            $file->storeAs('user_images', $imageName, 'public');

            $userImage = UserImage::create([
                'image_path' => $imageName,
            ]);

            $order->internal_image_id = $userImage->id;
        }

        // 🖼️ رفع / تغيير صورة الطباعة الشفافة
        if ($request->hasFile('transparent_printing_image')) {
            $file = $request->file('transparent_printing_image');

            $timestamp = time();
            $original = $file->getClientOriginalName();
            $imageName = $timestamp . '_' . $original;

            $file->storeAs('user_images', $imageName, 'public');

            $userImage = UserImage::create([
                'image_path' => $imageName,
            ]);

            $order->transparent_printing_id = $userImage->id;
        }

        // 📝 ملاحظات المتابعة على التجليد
        $order->binding_followup_note = $request->input('binding_followup_note');
        $order->save();

        // ⚡ لو الطلب جاينا بـ AJAX → نرجّع JSON ونترك الصفحة زي ما هي
        if ($request->ajax() || $request->wantsJson()) {

            $html = '';
            if ($order->binding_followup_note) {
                // نرجع الـ HTML الجاهز عشان نحطه جوه البوكس
                $html = nl2br(e($order->binding_followup_note));
            } else {
                $html = '<span class="text-muted">لا توجد ملاحظات حتى الآن.</span>';
            }

            return response()->json([
                'success' => true,
                'message' => 'تم حفظ ملاحظات التجليد بنجاح.',
                'html' => $html,
            ]);
        }

        // 🚶‍♂️ طلب عادي (لو فتحتيه من مكان ثاني مثلاً)
        return redirect()
            ->route('orders.show', $order->id)
            ->with('success', 'تم تحديث تجليد الدفتر بنجاح.');
    }


    public function updateDeliveryFollowup(Request $request, $id)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        // 🛡️ فقط أدمن أو ديزاينر
        if (!$user->isAdmin() && !$user->isDesigner()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'غير مصرح لك بتعديل ملاحظات التوصيل.',
                ], 403);
            }

            abort(403, 'غير مصرح لك بتعديل ملاحظات التوصيل.');
        }

        $request->validate([
            'delivery_followup_note' => ['nullable', 'string', 'max:5000'],
        ]);

        $order = Order::findOrFail($id);
        $order->delivery_followup_note = $request->input('delivery_followup_note');
        $order->save();

        // 👇 لو الطلب من AJAX (fetch) نرجع JSON
        if ($request->expectsJson()) {
            $html = $order->delivery_followup_note
                ? nl2br(e($order->delivery_followup_note))
                : '<span class="text-muted">لا توجد ملاحظات حتى الآن.</span>';

            return response()->json([
                'success' => true,
                'html' => $html,
                'message' => 'تم حفظ ملاحظات المتابعة على التوصيل بنجاح.',
            ]);
        }

        // 👈 لو فورم عادي (بدون AJAX) نرجع back زي ما هو
        return back()->with('success', 'تم حفظ ملاحظات المتابعة على التوصيل بنجاح.');
    }


    public function updateDesignFollowup(Request $request, Order $order)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        // 🛡️ فقط أدمن أو ديزاينر
        if (!$user->isAdmin() && !$user->isDesigner()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'غير مصرح لك بتعديل ملاحظات المتابعة على التصميم.',
                ], 403);
            }

            abort(403, 'غير مصرح لك بتعديل ملاحظات المتابعة على التصميم.');
        }

        // ✅ فاليديشين بسيط
        $data = $request->validate([
            'design_followup_note' => ['nullable', 'string', 'max:5000'],
        ]);

        if (!empty($data['design_followup_note'])) {
            $newNote = trim($data['design_followup_note']);
            $timestamp = now()->format('Y-m-d h:i A');

            $formattedNote = "--- {$timestamp} ---\n{$newNote}";

            if (empty($order->design_followup_note)) {
                $order->design_followup_note = $formattedNote;
            } else {
                $order->design_followup_note = $order->design_followup_note . "\n\n" . $formattedNote;
            }
        }
        $order->save();

        if ($request->expectsJson()) {
            $html = $order->design_followup_note
                ? nl2br(e($order->design_followup_note))
                : '<span class="text-muted">لا توجد ملاحظات متابعة حتى الآن.</span>';

            return response()->json([
                'success' => true,
                'html' => $html,
                'message' => 'تم حفظ ملاحظات المتابعة على التصميم بنجاح.',
            ]);
        }

        return back()->with('success', 'تم حفظ ملاحظات المتابعة على التصميم بنجاح.');
    }

    /**
     * تحويل مسار الصورة إلى URL جاهز للعرض في الـ Blade.
     */
    private function resolveImageUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        if (Str::startsWith($path, ['user_images/'])) {
            return asset('storage/' . ltrim($path, '/'));
        }

        if (Str::startsWith($path, ['/storage/'])) {
            return asset(ltrim($path, '/'));
        }

        // افتراضياً نخزنه في storage/user_images
        return asset('storage/user_images/' . ltrim($path, '/'));
    }

    /**
     * جلب كود الـ SVG الخاص بالاسم العربي (أول اسم) إن وجد.
     */
    private function resolveNameSvg(?string $usernameAr): ?string
    {
        if (!$usernameAr) {
            return null;
        }

        $firstArabicName = ArabicNameNormalizer::firstArabicName($usernameAr);
        if (!$firstArabicName) {
            return null;
        }

        $normalized = ArabicNameNormalizer::normalize($firstArabicName);

        /** @var \App\Models\SvgName|null $svgNameRow */
        $svgNameRow = SvgName::where('normalized_name', $normalized)->first();

        if ($svgNameRow && !empty($svgNameRow->svg_code)) {
            return $svgNameRow->svg_code;
        }

        return null;
    }

    /**
     * تجهيز صورة التصميم المختار (bookDesign) + العنوان المناسب.
     *
     * @return array{0: string|null, 1: string|null} [imageUrl, title]
     */
    private function resolveDesignImage(Order $order): array
    {
        $designImagePath = null;
        $designTitle = null;

        if ($order->bookDesign) {
            $designTitle = $order->bookDesign->title
                ?? $order->bookDesign->name_ar
                ?? $order->bookDesign->name
                ?? null;

            if ($order->bookDesign->image) {
                $path = $order->bookDesign->image;

                if (Str::startsWith($path, ['http://', 'https://'])) {
                    $designImagePath = $path;
                } else {
                    // حسب شغلك القديم كنت تستخدم asset مباشرة
                    $designImagePath = asset($path);
                }
            }
        }

        return [$designImagePath, $designTitle];
    }
}
