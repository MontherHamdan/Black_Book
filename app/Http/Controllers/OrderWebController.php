<?php

namespace App\Http\Controllers;

use App\Exports\OrdersExport;
use App\Models\BookDecoration;
use App\Models\BookType;
use App\Models\Diploma;
use App\Models\DiscountCode;
use App\Models\Governorate;
use App\Models\Note;
use App\Models\Order;
use App\Models\SvgName;
use App\Models\University;
use App\Models\User;
use App\Models\UserImage;
use App\Support\ArabicNameNormalizer;
use App\Traits\LogesTechsIntegration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Excel as ExcelFormat;
use Maatwebsite\Excel\Facades\Excel;

class OrderWebController extends Controller
{
    use LogesTechsIntegration;

    public function index()
    {
        $designers = User::where('role', User::ROLE_DESIGNER)
            ->orderBy('name')
            ->get(['id', 'name']);
        $discountCodes = DiscountCode::select('id', 'discount_code', 'code_name')
            ->orderBy('code_name')
            ->get();
        $allowedStatuses = $this->getAllowedStatuses(auth()->user());
        unset($allowedStatuses['out_for_delivery']);

        return view('admin.order.index', compact('designers', 'allowedStatuses', 'discountCodes'));
    }

    public function show($id)
    {
        /** @var \App\Models\User $authUser */
        $authUser = auth()->user();

        $order = Order::with([
            'discountCode',
            'bookType',
            'governorate',
            'city',
            'area',
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

        $bookTypes = BookType::orderBy('name_ar')->get(['id', 'name_ar']);
        $discountCodes = DiscountCode::orderBy('discount_code')->get(['id', 'discount_code', 'code_name']);
        $universities = University::with('majors')->orderBy('name')->get(['id', 'name']);
        $diplomas = Diploma::with('majors')->orderBy('name')->get(['id', 'name']);
        $governorates = Governorate::whereNotNull('logestechs_id')->get(['id', 'name_ar', 'name_en']);

        // 🔹 فلاغات عامة عن المستخدم
        $isAdmin = $authUser->isAdmin();
        $isDesigner = $authUser->isDesigner();

        $designerIsAssigned = ! is_null($order->designer_id);
        $designerIsCurrentUser = $designerIsAssigned && (int) $order->designer_id === (int) $authUser->id;
        $customDesignImages = $order->customDesignImagesFromIds();
        $customDesignImages = $customDesignImages->map(function ($img) {
            $img->resolved_url = $this->resolveImageUrl($img->image_path ?? null);

            return $img;
        });

        // =========================
        // 🔹 1) SVG الخاص بالاسم العربي
        // =========================
        $svgCodeForName = $this->resolveNameSvg($order->username_ar ?? null, $order->id);

        // =========================
        // 🔹 2) إعداد Config الحالات
        // =========================
        $statusConfig = $this->getAllowedStatuses($authUser);
        unset($statusConfig['out_for_delivery']);

        // الهيدر
        $currentStatusHeader = $statusConfig[$order->status] ?? [
            'class' => 'status-unknown',
            'label' => $order->status,
        ];

        $canChangeStatusHeader = $isAdmin
            || (method_exists($authUser, 'isSupervisor') && $authUser->isSupervisor())
            || (method_exists($authUser, 'isPrinter') && $authUser->isPrinter())
            || ($order->designer && $order->designer->id === $authUser->id);

        $canChangeDesignerHeader =
            $isAdmin ||
            (
                $isDesigner
                && (
                    ! $order->designer_id || (int) $order->designer_id === (int) $authUser->id
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
        // 🚨 8) نظام تنبيهات خصم المجموعات 🚨
        // =========================
        $groupWarning = null;

        if ($order->discountCode && $order->discountCode->is_group && $order->discountCode->plan_id) {

            // 1. كم عدد الطلبات الفعلي اللي استخدموا هاد الكود؟
            $groupOrdersCount = \App\Models\Order::where('discount_code_id', $order->discountCode->id)->count();

            // 2. شو الخطة المربوطة بكود الخصم؟
            $appliedPlan = \App\Models\Plan::find($order->discountCode->plan_id);

            if ($appliedPlan) {
                // 3. 🚨 التنبيه يظهر لما العدد الحالي لسه أقل من العدد المطلوب بالخطة
                // (يعني: الخصم طُبِّق بس المجموعة ما اكتملت بعد!)
                if ($groupOrdersCount < $appliedPlan->person_number) {

                    // السعر الأصلي للدفتر (بدون خصم)
                    $originalPrice = 25;

                    // السعر اللي طُبِّق على هاد الطلب بناءً على الخطة
                    $appliedPrice = $originalPrice - (float) $appliedPlan->discount_price;

                    $groupWarning = [
                        'original_price'  => $originalPrice,
                        'applied_price'   => $appliedPrice,
                        'applied_plan'    => $appliedPlan->title ?? ('Plan ' . $appliedPlan->id),
                        'current_count'   => $groupOrdersCount,   // العدد الحالي
                        'required_count'  => $appliedPlan->person_number, // العدد المطلوب
                    ];
                }
            }
        }
        // =========================
        // 🔹 7) تمرير كل شيء للـ View
        // =========================

        return view('admin.order.show', [
            'order' => $order,
            'bookTypes' => $bookTypes,
            'discountCodes' => $discountCodes,
            'universities' => $universities,
            'diplomas' => $diplomas,
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

            'governorates' => $governorates,

            'groupWarning' => $groupWarning,

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
     * إرجاع الحالات المسموحة بناءً على دور المستخدم.
     */
    private function getAllowedStatuses($user): array
    {
        $all = $this->statusConfig();

        if ($user->isAdmin()) {
            return $all;
        }

        if ($user->isDesigner()) {
            $allowed = ['new_order', 'Pending', 'needs_modification', 'Completed', 'preparing'];
        } elseif ($user->isSupervisor()) {
            $allowed = ['needs_modification', 'Completed', 'Printed', 'Received', 'out_for_delivery', 'returned', 'Canceled'];
        } elseif ($user->isPrinter()) {
            $allowed = ['preparing', 'Printed', 'out_for_delivery'];
        } else {
            $allowed = [];
        }

        return array_intersect_key($all, array_flip($allowed));
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
        $designerFilter = $request->input('designer_id');
        $codeNameFilter = $request->input('code_name');

        $query = Order::with([
            'discountCode',
            'bookType',
            'bookDesign',
            'frontImage',
            'transparentPrinting',
            'designer',
        ]);

        // 🚫 إخفاء طلبات "قيد التجهيز" و "خرج مع التوصيل" عن المصممين
        $authUser = auth()->user();
        if ($authUser->isDesigner() && ! $authUser->isAdmin() && ! $authUser->isSupervisor()) {
            $query->whereNotIn('status', ['preparing', 'out_for_delivery']);
            if ($authUser->isPenalized()) {
                $query->where(function ($q) {
                    $q->whereNotNull('designer_id')
                        ->orWhere('status', '!=', 'new_order');
                });
            }
        }

        if (! empty($designerFilter)) {
            if ($designerFilter === 'unassigned') {
                $query->whereNull('designer_id'); // للبحث عن الطلبات غير المسندة لأحد
            } else {
                $query->where('designer_id', $designerFilter);
            }
        }

        // 🔎 بحث عام
        if (! empty($searchValue)) {
            $query->where(function ($query) use ($searchValue) {
                $query->where('username_ar', 'like', "%{$searchValue}%")
                    ->orWhere('username_en', 'like', "%{$searchValue}%")
                    ->orWhereHas('governorate', function ($q) use ($searchValue) { // ✅ التعديل الصح
                        $q->where('name_ar', 'like', "%{$searchValue}%")
                            ->orWhere('name_en', 'like', "%{$searchValue}%");
                    })
                    ->orWhere('address', 'like', "%{$searchValue}%")
                    ->orWhere('user_phone_number', 'like', "%{$searchValue}%")
                    ->orWhere('delivery_number_two', 'like', "%{$searchValue}%")
                    ->orWhere('status', 'like', "%{$searchValue}%")
                    ->orWhere('final_price_with_discount', 'like', "%{$searchValue}%");
            });
        }

        // 🎯 فلتر الحالة
        if (! empty($statusFilter)) {
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
        if (! empty($dateFrom)) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if (! empty($dateTo)) {
            $query->whereDate('created_at', '<=', $dateTo);
        }
        if (! empty($codeNameFilter)) {
            $query->whereHas('discountCode', function ($q) use ($codeNameFilter) {
                $q->where('code_name', 'like', "%{$codeNameFilter}%")
                    ->orWhere('discount_code', 'like', "%{$codeNameFilter}%");
            });
        }
        $duplicatePhones = Order::select('user_phone_number')
            ->whereNotNull('user_phone_number')
            ->where('user_phone_number', '!=', '')
            ->groupBy('user_phone_number')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('user_phone_number')
            ->toArray();

        $duplicateNames = Order::select('username_ar')
            ->whereNotNull('username_ar')
            ->where('username_ar', '!=', '')
            ->groupBy('username_ar')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('username_ar')
            ->toArray();

        // ⬇ ترتيب + Pagination
        $orders = $query
            ->orderBy($sortColumn, $sortDirection)
            ->paginate($perPage, ['*'], 'page', $page);

        $formattedOrders = $orders->getCollection()->map(function ($order) use ($duplicatePhones, $duplicateNames) {
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

            // 🔸 بيانات الخصم
            $discountInfo = null;
            if ($order->discountCode) {
                $dc = $order->discountCode;
                $isGroup = (bool) $dc->is_group;
                $groupCount = null;
                $requiredCount = null;
                $incomplete = false;

                if ($isGroup && $dc->plan_id) {
                    $groupCount = \App\Models\Order::where('discount_code_id', $dc->id)->count();
                    $plan = \App\Models\Plan::find($dc->plan_id);
                    if ($plan) {
                        $requiredCount = (int) $plan->person_number;
                        $incomplete = $groupCount < $requiredCount;
                    }
                }

                $discountInfo = [
                    'code'          => $dc->discount_code,
                    'name'          => $dc->code_name,
                    'is_group'      => $isGroup,
                    'group_count'   => $groupCount,
                    'required_count'=> $requiredCount,
                    'incomplete'    => $incomplete,
                ];
            }

            return [
                'id' => $order->id,
                'data' => $createdAtFormatted,
                'status_created_diff' => $statusDiff,

                'username' => $order->username_ar,
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
                'is_duplicate' => in_array($order->user_phone_number, $duplicatePhones) || in_array($order->username_ar, $duplicateNames),
                'is_with_additives' => (bool) $order->is_with_additives,

                'designer' => $order->designer ? [
                    'id' => $order->designer->id,
                    'name' => $order->designer->name,
                ] : null,

                'discount_info' => $discountInfo,

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
            'status' => 'required|in:new_order,needs_modification,Pending,preparing,Completed,Printed,out_for_delivery,Received,returned,Canceled',
        ]);

        /** @var \App\Models\User $user */
        $user = $request->user();
        $order = Order::with('designer')->findOrFail($request->id); // جلبنا الديزاينر مع الطلب

        // 🛡️ التحقق من الصلاحيات
        if (! $user->isAdmin() && ! $user->isSupervisor() && ! $user->isPrinter()) {
            if (! $user->isDesigner() || $order->designer_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'غير مصرح لك بتحديث حالة هذا الطلب.',
                ], 403);
            }
        }

        // 🛡️ التحقق من الحالة المسموحة للدور
        $allowedStatuses = $this->getAllowedStatuses($user);
        if (! array_key_exists($request->status, $allowedStatuses)) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بتغيير الحالة إلى هذه القيمة.',
            ], 403);
        }

        $newStatus = $request->status;

        // 🛡️ حماية: منع المصمم من التغيير إلى "قيد التجهيز" إذا لم يرفع الملفات
        if ($newStatus === 'preparing') {
            $missingFiles = [];

            if (! $order->designer_design_file) {
                $missingFiles[] = 'صورة تصميم الدفتر النهائي';
            }

            if (! $order->designer_decoration_file) {
                $missingFiles[] = 'صورة الزخرفة';
            }

            // فحص الإهداء المخصص
            if ($order->gift_type === 'custom' && ! $order->designer_gift_file) {
                $missingFiles[] = 'صورة الإهداء المخصص';
            }

            // فحص عدد الصور الداخلية (يجب أن يتطابق مع ما أرسله المستخدم)
            $userInternalCount = $order->additionalImagesFromIds()->count();
            $designerInternalCount = is_array($order->designer_internal_files) ? count($order->designer_internal_files) : 0;

            if ($userInternalCount > 0 && $designerInternalCount !== $userInternalCount) {
                $missingFiles[] = "الصور الداخلية (طلب العميل $userInternalCount صورة، وأنت رفعت $designerInternalCount صورة)";
            }

            // إذا في ملفات ناقصة، نرجع Error للفرونت إيند
            if (! empty($missingFiles)) {
                return response()->json([
                    'success' => false,
                    'message' => "عذراً، لا يمكنك تغيير الحالة إلى 'قيد التجهيز'. يرجى رفع الملفات التالية في تبويب تجليد الدفتر:\n- ".implode("\n- ", $missingFiles),
                ], 422);
            }
        }

        // ✅ الحالات اللي نعتبر عندها شغل المصمم "منجَز"
        $designerDoneStatuses = [
            'Completed',        // تم الاعتماد
            'Received',         // تم التسليم
            'out_for_delivery', // خرج مع التوصيل
            'returned',         // مرتجع
            'Canceled',         // رفض الإستلام
            'preparing',        // قيد التجهيز
            'Printed',          // تم الطباعة
        ];

        // تحديث حالة الطلب وموعد الخروج للتوصيل
        $oldStatus = $order->status;
        $order->status = $newStatus;

        // 🚀 السحر هون: ترحيل الطلب لشركة التوصيل إذا تغيرت الحالة
        if ($newStatus === \App\Models\Order::STATUS_OUT_FOR_DELIVERY) {

            // 🛡️ حماية قبل إرسال الريكويست
            if (! $order->governorate_id || ! $order->city_id || ! $order->area_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يمكن ترحيل الطلب! يرجى تحديث "معلومات التوصيل" (المحافظة، المدينة، المنطقة) أولاً.',
                ], 422);
            }

            $isGroupOrder = ! empty($order->discount_code_id);

            // نتأكد إنه الطلب مش مترحل من قبل
            if (empty($order->logestechs_order_id)) {

                // 🛡️ فحص المجموعات: هل أحد أفراد المجموعة تم ترحيله مسبقاً؟
                $alreadyDispatched = false;
                if ($isGroupOrder) {
                    $sibling = Order::where('discount_code_id', $order->discount_code_id)
                        ->whereNotNull('logestechs_order_id')
                        ->first();

                    if ($sibling) {
                        // زميله ترحل مسبقاً! ننسخ رقم بوليصة الشحن بدون ضرب الـ API مرة ثانية
                        $order->logestechs_order_id = $sibling->logestechs_order_id;
                        $alreadyDispatched = true;
                    }
                }

                // إذا محدش بالمجموعة ترحل، أو إذا كان طلب فردي -> نضرب الـ API
                if (! $alreadyDispatched) {
                    $logestechsResponse = $this->dispatchOrderToLogesTechs($order);

                    if (! $logestechsResponse['success']) {
                        return response()->json([
                            'success' => false,
                            'message' => "رفضت شركة التوصيل الطلب، ولم يتم تغيير الحالة.\nالسبب: ".$logestechsResponse['message'],
                        ], 422);
                    }

                    $order->logestechs_order_id = $logestechsResponse['data']['id'] ?? null;

                    // 🪄 سحر إضافي: تحديث جميع أفراد المجموعة ليصبحوا Out For Delivery برقم بوليصة واحد!
                    if ($isGroupOrder && $order->logestechs_order_id) {
                        Order::where('discount_code_id', $order->discount_code_id)
                            ->where('id', '!=', $order->id)
                            ->update([
                                'logestechs_order_id' => $order->logestechs_order_id,
                                'status' => $newStatus,
                                'dispatched_at' => now(),
                            ]);
                    }
                }
            }

            $order->dispatched_at = now();
        }

        // 💰 حساب العمولة وتأكيد الإنجاز
        if (in_array($newStatus, $designerDoneStatuses, true)) {
            // نعتبر الطلب منجز
            $order->designer_done = true;
            if (! $order->designer_done_at) {
                $order->designer_done_at = now();
            }
            // نحسب العمولة دائماً لأي حالة منجزة لضمان التحديث المستمر
            $order->designer_commission = $this->calculateDesignerCommission($order);
        }

        $order->save();

        // نستخدم نفس الـ statusConfig عشان الكلاسات تتطابق مع الـ CSS
        $statusConfig = $this->statusConfig();

        $cfg = $statusConfig[$order->status] ?? [
            'class' => 'status-unknown',
            'label' => $order->status,
        ];

        return response()->json([
            'success' => true,
            'status' => $order->status,
            'label' => $cfg['label'],
            'class' => $cfg['class'],
        ]);
    }

    private function calculateDesignerCommission(Order $order): float
    {
        if (! $order->designer) {
            return 0;
        }

        $designer = $order->designer;
        $commission = (float) ($designer->base_order_price ?? 0);

        // 1. فحص الزخرفة
        if (! empty($order->book_decorations_id)) {
            $commission += (float) ($designer->decoration_price ?? 0);
        }

        // 2. فحص الإهداء المخصص
        if ($order->gift_type === 'custom') {
            $commission += (float) ($designer->custom_gift_price ?? 0);
        }

        // 3. فحص الصورة الداخلية
        $additionalIds = $order->additional_image_id;
        if (is_string($additionalIds)) {
            $additionalIds = json_decode($additionalIds, true);
        }
        if (is_array($additionalIds) && ! empty($additionalIds)) {
            $commission += (float) ($designer->internal_image_price ?? 0);
        }

        return $commission;
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
        if (! $user->isAdmin()) {
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
        if (! $user->isAdmin()) {
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
                $errors[] = "فشل حذف الطلب #{$order->id}: ".$e->getMessage();
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
     * Bulk update status for multiple orders.
     */
    public function bulkUpdateStatus(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        // Only admins, supervisors, and printers can do this.
        if (! $user->isAdmin() && ! $user->isSupervisor() && ! $user->isPrinter()) {
            return response()->json(['success' => false, 'message' => 'غير مصرح لك بتغيير الحالة.'], 403);
        }

        $request->validate([
            'order_ids' => 'required|array',
            'order_ids.*' => 'required|exists:orders,id',
            'status' => 'required|string',
        ]);

        $status = $request->input('status');
        $orderIds = $request->input('order_ids', []);

        // Validate if the user is allowed to set this specific status
        $allowed = $this->getAllowedStatuses($user);
        if (! array_key_exists($status, $allowed)) {
            return response()->json(['success' => false, 'message' => 'غير مصرح لك بتعيين هذه الحالة.'], 403);
        }

        // 🚀 إضافة اللوجيك الخاص بإلغاء الشحنة من شركة التوصيل
        if ($status === 'Canceled') {
            // جلب أرقام بوليصات الشحن الفريدة للطلبات المحددة
            $ordersToCancel = Order::whereIn('id', $orderIds)->whereNotNull('logestechs_order_id')->get();
            $uniqueShipmentIds = $ordersToCancel->pluck('logestechs_order_id')->unique()->filter();

            foreach ($uniqueShipmentIds as $shipmentId) {
                $cancelResponse = $this->cancelLogesTechsShipment($shipmentId);

                if (! $cancelResponse['success']) {
                    return response()->json([
                        'success' => false,
                        'message' => 'تعذر إلغاء الشحنة ('.$shipmentId.') لدى شركة التوصيل! لم يتم تغيير الحالة. السبب: '.$cancelResponse['message'],
                    ], 422);
                }
            }
        }

        $updateData = ['status' => $status];
        if ($status === Order::STATUS_OUT_FOR_DELIVERY) {
            $updateData['dispatched_at'] = now();
        }

        Order::whereIn('id', $orderIds)->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث حالة '.count($orderIds).' طلب بنجاح',
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
        if (is_array($backImageIds) && ! empty($backImageIds)) {
            $imageIdsToCheck = array_merge($imageIdsToCheck, $backImageIds);
        }

        // Additional images (from JSON array)
        $additionalImageIds = $order->additional_image_id;
        if (is_string($additionalImageIds)) {
            $additionalImageIds = json_decode($additionalImageIds, true);
        }
        if (is_array($additionalImageIds) && ! empty($additionalImageIds)) {
            $imageIdsToCheck = array_merge($imageIdsToCheck, $additionalImageIds);
        }

        // Custom design images (from JSON array)
        $customDesignImageIds = $order->custom_design_image_id;
        if (is_string($customDesignImageIds)) {
            $customDesignImageIds = json_decode($customDesignImageIds, true);
        }
        if (is_array($customDesignImageIds) && ! empty($customDesignImageIds)) {
            $imageIdsToCheck = array_merge($imageIdsToCheck, $customDesignImageIds);
        }

        // Remove duplicates
        $imageIdsToCheck = array_unique(array_filter($imageIdsToCheck));

        // 🗑️ Delete physical image files and UserImage records
        if (! empty($imageIdsToCheck)) {
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

                if (! $isUsedElsewhere) {
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
        if (! $userImage->image_path) {
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
            $filePath = storage_path('app/public/'.$relative);
        } elseif (Str::startsWith($path, ['user_images/'])) {
            $filePath = storage_path('app/public/'.ltrim($path, '/'));
        } else {
            // Assume it's just a filename in user_images directory
            $filePath = storage_path('app/public/user_images/'.ltrim($path, '/'));
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

        $note = new Note;
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
        $zipFileName = 'back_images_'.$orderId.'.zip';
        $zipFilePath = storage_path('app/'.$zipFileName);

        $zipDir = dirname($zipFilePath);
        if (! is_dir($zipDir)) {
            mkdir($zipDir, 0755, true);
        }

        if (file_exists($zipFilePath)) {
            @unlink($zipFilePath);
        }

        $zip = new \ZipArchive;

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

                $fileName = basename(parse_url($path, PHP_URL_PATH)) ?: ('image_'.$img->id.'.jpg');

                $tmpDir = storage_path('app/tmp');
                if (! is_dir($tmpDir)) {
                    mkdir($tmpDir, 0755, true);
                }

                $tempPath = $tmpDir.'/'.uniqid('img_', true).'_'.$fileName;

                file_put_contents($tempPath, $imageContent);

                $zip->addFile($tempPath, $fileName);
                $tempFiles[] = $tempPath;
            }

            // 🔹 2) لو مسار محلي
            else {

                $originalPath = $path;

                if (Str::startsWith($path, ['/storage/'])) {
                    $relative = ltrim(str_replace('/storage/', '', $path), '/');
                    $localPath = storage_path('app/public/'.$relative);
                } else {
                    // فقط اسم ملف → نضيف له user_images/
                    if (! Str::contains($path, '/')) {
                        $path = 'user_images/'.ltrim($path, '/');
                    }

                    $localPath = storage_path('app/public/'.ltrim($path, '/'));
                }

                if (! file_exists($localPath)) {
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

        if (! file_exists($zipFilePath)) {
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

        $fileName = 'orders-'.now()->format('Y-m-d_H-i-s').'.csv';

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

        $zip = new \ZipArchive;
        $zipFileName = 'additional_images_'.$orderId.'.zip';
        $zipFilePath = storage_path('app/public/'.$zipFileName);

        // نتأكد من وجود فولدر storage/app/public
        $zipDir = dirname($zipFilePath);
        if (! is_dir($zipDir)) {
            mkdir($zipDir, 0755, true);
        }

        // لو في ملف قديم بنفس الاسم نحذفه
        if (file_exists($zipFilePath)) {
            @unlink($zipFilePath);
        }

        if ($zip->open($zipFilePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {

            foreach ($images as $img) {
                if (! $img->image_path) {
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

                        $fileName = basename(parse_url($path, PHP_URL_PATH)) ?: ('image_'.$img->id.'.jpg');
                        $tempPath = storage_path('app/tmp_'.$fileName);

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
                        $localPath = storage_path('app/public/'.$relative);
                    } elseif (Str::startsWith($path, ['user_images/'])) {
                        $localPath = storage_path('app/public/'.ltrim($path, '/'));
                    } else {
                        // اعتبره اسم ملف عادي داخل user_images
                        $localPath = storage_path('app/public/user_images/'.ltrim($path, '/'));
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

        if ($user->isDesigner() && $user->isPenalized() && ! $user->isAdmin() && ! $user->isSupervisor()) {
            // Prevent them from taking new orders
            if ($request->designer_id == $user->id && $order->designer_id != $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'عذراً، لقد تجاوزت الحد الأقصى للتعديلات المسموحة أو تم إيقافك مؤقتاً. لا يمكنك استلام طلبات جديدة.',
                ], 403);
            }
        }

        // ✅ لو مو Admin
        if (! $user->isAdmin()) {

            // لازم يكون Designer أصلاً
            if (! $user->isDesigner()) {
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

            // المصمم العادي يسمح له فقط بتعيين نفسه على الطلب أو إزالة تعيينه لنفسه (إرجاعه لـ غير معيّن)
            if ($request->filled('designer_id') && (int) $request->designer_id !== (int) $user->id) {
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
        if (! $user->isAdmin() && ! $user->isDesigner()) {
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
            $imageName = $timestamp.'_'.$original;

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
            $imageName = $timestamp.'_'.$original;

            $file->storeAs('user_images', $imageName, 'public');

            $userImage = UserImage::create([
                'image_path' => $imageName,
            ]);

            $order->transparent_printing_id = $userImage->id;
        }

        // 📝 ملاحظات المتابعة على التجليد
        // 📝 ملاحظات المتابعة على التجليد (إضافة تراكمية)
        if ($request->filled('binding_followup_note')) {
            $newNote = trim($request->input('binding_followup_note'));
            $timestamp = now()->format('Y-m-d h:i A');
            $formattedNote = "--- {$timestamp} ---\n{$newNote}";

            if (empty($order->binding_followup_note)) {
                $order->binding_followup_note = $formattedNote;
            } else {
                $order->binding_followup_note = $order->binding_followup_note."\n\n".$formattedNote;
            }

            // عشان تبين عند المصمم كـ غير مقروءة
            $order->designer_read_notes = false;
        }
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
        if (! $user->isAdmin() && ! $user->isDesigner()) {
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

        // 📝 ملاحظات التوصيل (إضافة تراكمية)
        if ($request->filled('delivery_followup_note')) {
            $newNote = trim($request->input('delivery_followup_note'));
            $timestamp = now()->format('Y-m-d h:i A');
            $formattedNote = "--- {$timestamp} ---\n{$newNote}";

            if (empty($order->delivery_followup_note)) {
                $order->delivery_followup_note = $formattedNote;
            } else {
                $order->delivery_followup_note = $order->delivery_followup_note."\n\n".$formattedNote;
            }
        }
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
        if (! $user->isAdmin() && ! $user->isDesigner()) {
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

        if (! empty($data['design_followup_note'])) {
            $newNote = trim($data['design_followup_note']);
            $timestamp = now()->format('Y-m-d h:i A');

            $formattedNote = "--- {$timestamp} ---\n{$newNote}";

            if (empty($order->design_followup_note)) {
                $order->design_followup_note = $formattedNote;
            } else {
                $order->design_followup_note = $order->design_followup_note."\n\n".$formattedNote;
            }

            $order->designer_read_notes = false;
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
        if (! $path) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        if (Str::startsWith($path, ['user_images/'])) {
            return asset('storage/'.ltrim($path, '/'));
        }

        if (Str::startsWith($path, ['/storage/'])) {
            return asset(ltrim($path, '/'));
        }

        // افتراضياً نخزنه في storage/user_images
        return asset('storage/user_images/'.ltrim($path, '/'));
    }

    /**
     * جلب كود الـ SVG الخاص بالاسم العربي (أول اسم) إن وجد.
     */
    private function resolveNameSvg(?string $usernameAr, $orderId): ?array
    {
        if (! $usernameAr) {
            return null;
        }

        $firstArabicName = ArabicNameNormalizer::firstArabicName($usernameAr);
        if (! $firstArabicName) {
            return null;
        }

        $normalized = ArabicNameNormalizer::normalize($firstArabicName);

        $svgNameRow = SvgName::where('normalized_name', $normalized)->first();

        if ($svgNameRow && ! empty($svgNameRow->svg_code)) {

            // 1. إنشاء مجلد مؤقت للـ SVGs
            $svgDir = storage_path('app/public/temp_svgs');
            if (! is_dir($svgDir)) {
                mkdir($svgDir, 0755, true);
            }

            // 2. اسم ملف فريد (بدون استخدام ?v= في الرابط لأن الفوتوشوب يكرهها)
            $fileName = 'name_'.$orderId.'_'.uniqid().'.svg';
            $filePath = $svgDir.'/'.$fileName;

            // 3. تنظيف وتجهيز كود الـ SVG
            $svgCode = trim($svgNameRow->svg_code);

            // إزالة أي ترويسة XML قديمة لتجنب التكرار
            $svgCode = preg_replace('/<\?xml.*?\?>/is', '', $svgCode);

            // إذا لم يكن يحتوي على <svg> أصلاً (عبارة عن مسارات فقط)
            if (! preg_match('/<svg/i', $svgCode)) {
                $svgCode = '<svg xmlns="http://www.w3.org/2000/svg" version="1.1" viewBox="0 0 1000 1000" width="1000" height="1000">'."\n".$svgCode."\n".'</svg>';
            } else {
                // التأكد من وجود xmlns والأبعاد الثابتة
                if (! preg_match('/xmlns=/i', $svgCode)) {
                    $svgCode = preg_replace('/<svg/i', '<svg xmlns="http://www.w3.org/2000/svg" version="1.1"', $svgCode, 1);
                }
                $svgCode = preg_replace('/width\s*=\s*["\']?[0-9.]+%\s*["\']?/i', 'width="1000"', $svgCode);
                $svgCode = preg_replace('/height\s*=\s*["\']?[0-9.]+%\s*["\']?/i', 'height="1000"', $svgCode);
            }

            // إضافة الترويسة الرسمية كأول سطر
            $svgCode = '<?xml version="1.0" encoding="utf-8"?>'."\n".trim($svgCode);

            // 4. الحفظ والإرجاع
            file_put_contents($filePath, $svgCode);

            return [
                'code' => $svgCode,
                'url' => asset('storage/temp_svgs/'.$fileName), // 👈 رابط نظيف 100% بدون استعلامات
            ];
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

    public function updateNotebookFollowup(Request $request, Order $order)
    {
        $user = $request->user();

        if (! $user->isAdmin() && ! $user->isDesigner()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'غير مصرح لك بتعديل ملاحظات الدفتر.',
                ], 403);
            }

            abort(403, 'غير مصرح لك بتعديل ملاحظات الدفتر.');
        }

        $data = $request->validate([
            'notebook_followup_note' => ['nullable', 'string', 'max:5000'],
        ]);

        if (! empty($data['notebook_followup_note'])) {
            $newNote = trim($data['notebook_followup_note']);
            $timestamp = now()->format('Y-m-d h:i A');

            $formattedNote = "--- {$timestamp} ---\n{$newNote}";

            if (empty($order->notebook_followup_note)) {
                $order->notebook_followup_note = $formattedNote;
            } else {
                $order->notebook_followup_note = $order->notebook_followup_note."\n\n".$formattedNote;
            }

            $order->designer_read_notes = false;
        }

        $order->save();

        if ($request->expectsJson()) {
            $html = $order->notebook_followup_note
                ? nl2br(e($order->notebook_followup_note))
                : '<span class="text-muted">لا توجد ملاحظات دفتر حتى الآن.</span>';

            return response()->json([
                'success' => true,
                'html' => $html,
                'message' => 'تم حفظ ملاحظات الدفتر بنجاح.',
            ]);
        }

        return back()->with('success', 'تم حفظ ملاحظات الدفتر بنجاح.');
    }

    public function updateOrderCoreData(Request $request, Order $order)
    {
        $user = $request->user();
        if (! $user->isAdmin() && ! $user->isDesigner()) {
            abort(403, 'غير مصرح لك بتعديل بيانات الطلب.');
        }

        $validated = $request->validate([
            'book_type_id' => 'nullable|exists:book_types,id',
            'user_gender' => 'nullable|in:male,female',
            'final_price' => 'nullable|numeric',
            'final_price_with_discount' => 'nullable|numeric',
            'discount_code_id' => 'nullable|exists:discount_codes,id',
            'is_with_additives' => 'nullable',
        ]);

        // Handle checkbox — not sent when unchecked
        $validated['is_with_additives'] = $request->boolean('is_with_additives');

        $order->update($validated);

        return back()->with('success', 'تم تحديث تفاصيل الطلب بنجاح.');
    }

    public function updateGraduateInfo(Request $request, Order $order)
    {
        $user = $request->user();
        if (! $user->isAdmin() && ! $user->isDesigner()) {
            abort(403, 'غير مصرح لك بتعديل بيانات الخريج.');
        }

        $validated = $request->validate([
            'username_ar' => 'required|string|max:255',
            'username_en' => 'nullable|string|max:255',
            'user_phone_number' => 'nullable|string|max:50',
            'university_id' => 'nullable|exists:universities,id',
            'university_major_id' => 'nullable|exists:majors,id',
            'diploma_id' => 'nullable|exists:diplomas,id',
            'diploma_major_id' => 'nullable|exists:diploma_majors,id',
            'note' => 'nullable|string',
            'front_image' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:20480',
            'back_images' => 'nullable|array',
            'back_images.*' => 'image|mimes:jpg,jpeg,png,webp,gif|max:20480',
            'custom_design_images' => 'nullable|array',
            'custom_design_images.*' => 'image|mimes:jpg,jpeg,png,webp,gif|max:20480',
            'new_svg' => 'nullable|file|mimes:svg,txt|max:512',
        ]);

        // Update university / diploma IDs only (school_name & major_name columns are DROPPED)
        if ($request->filled('university_id')) {
            $order->university_id = $request->university_id;
            // Clear diploma fields if university is selected
            $order->diploma_id = null;
            $order->diploma_major_id = null;

            if ($request->filled('university_major_id')) {
                $order->university_major_id = $request->university_major_id;
            }
        } elseif ($request->filled('diploma_id')) {
            $order->diploma_id = $request->diploma_id;
            // Clear university fields if diploma is selected
            $order->university_id = null;
            $order->university_major_id = null;

            if ($request->filled('diploma_major_id')) {
                $order->diploma_major_id = $request->diploma_major_id;
            }
        }

        if (array_key_exists('username_ar', $validated)) {
            $order->username_ar = $validated['username_ar'];
        }
        if (array_key_exists('username_en', $validated)) {
            $order->username_en = $validated['username_en'];
        }
        if (array_key_exists('note', $validated)) {
            $order->note = $validated['note'];
        }
        if (array_key_exists('user_phone_number', $validated)) {
            $order->user_phone_number = $validated['user_phone_number'];
        }

        // 🖼️ Front Image upload
        if ($request->hasFile('front_image')) {
            $file = $request->file('front_image');
            $imageName = time().'_'.$file->getClientOriginalName();
            $file->storeAs('user_images', $imageName, 'public');
            $userImage = UserImage::create(['image_path' => $imageName]);
            $order->front_image_id = $userImage->id;
        }

        // 🖼️ Back Images upload (overwrite)
        if ($request->hasFile('back_images')) {
            $backIds = [];
            foreach ($request->file('back_images') as $file) {
                $imageName = time().'_'.$file->getClientOriginalName();
                $file->storeAs('user_images', $imageName, 'public');
                $userImage = UserImage::create(['image_path' => $imageName]);
                $backIds[] = $userImage->id;
            }
            // Merge with existing or overwrite
            $existingIds = $order->back_image_ids;
            if (is_string($existingIds)) {
                $existingIds = json_decode($existingIds, true) ?? [];
            }
            $order->back_image_ids = json_encode(array_merge((array) $existingIds, $backIds));
        }

        // 🖼️ Custom Design Images upload (overwrite)
        if ($request->hasFile('custom_design_images')) {
            $customIds = [];
            foreach ($request->file('custom_design_images') as $file) {
                $imageName = time().'_'.$file->getClientOriginalName();
                $file->storeAs('user_images', $imageName, 'public');
                $userImage = UserImage::create(['image_path' => $imageName]);
                $customIds[] = $userImage->id;
            }
            $existingIds = $order->custom_design_image_id;
            if (is_string($existingIds)) {
                $existingIds = json_decode($existingIds, true) ?? [];
            }
            $order->custom_design_image_id = json_encode(array_merge((array) $existingIds, $customIds));
        }

        // SVG Upload
        if ($request->hasFile('new_svg')) {
            $content = file_get_contents($request->file('new_svg')->getRealPath());
            if ($order->svg) {
                $order->svg->update(['svg_code' => $content]);
            } else {
                $svg = \App\Models\Svg::create(['svg_code' => $content, 'title' => 'Uploaded via Graduate Modal']);
                $order->svg_id = $svg->id;
            }
        }

        $order->save();

        return back()->with('success', 'تم تحديث معلومات الخريج بنجاح.');
    }

    public function updateInternalBook(Request $request, Order $order)
    {
        $user = $request->user();
        if (! $user->isAdmin() && ! $user->isDesigner()) {
            abort(403, 'غير مصرح لك بتعديل الدفتر من الداخل.');
        }

        $validated = $request->validate([
            'gift_type' => 'nullable|string',
            'gift_title' => 'nullable|string',
            'notebook_followup_note' => 'nullable|string',
            'internal_images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
            'transparent_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
            'decoration_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
        ]);

        $order->fill([
            'gift_type' => $validated['gift_type'] ?? $order->gift_type,
            'gift_title' => $validated['gift_title'] ?? $order->gift_title,
            'notebook_followup_note' => $validated['notebook_followup_note'] ?? $order->notebook_followup_note,
        ]);

        if ($request->hasFile('transparent_image')) {
            $path = $request->file('transparent_image')->store('user_images', 'public');
            $userImage = \App\Models\UserImage::create(['image_path' => $path]);
            $order->transparent_printing_id = $userImage->id;
        }

        if ($request->hasFile('internal_images')) {
            $existingIds = $order->additional_image_id;
            if (is_string($existingIds)) {
                $existingIds = json_decode($existingIds, true);
            }
            if (! is_array($existingIds)) {
                $existingIds = [];
            }

            $newIds = [];
            foreach ($request->file('internal_images') as $file) {
                $path = $file->store('user_images', 'public');
                $userImage = \App\Models\UserImage::create(['image_path' => $path]);
                $newIds[] = $userImage->id;
            }

            $order->additional_image_id = array_merge($existingIds, $newIds);
        }

        if ($request->hasFile('decoration_image')) {
            $path = $request->file('decoration_image')->store('user_images', 'public');
            $decoration = \App\Models\BookDecoration::create([
                'name' => '',
                'image' => asset('storage/'.$path),
            ]);
            $order->book_decorations_id = $decoration->id;
        }

        $order->designer_commission = $this->calculateDesignerCommission($order);
        $order->save();

        return back()->with('success', 'تم تحديث الدفتر من الداخل بنجاح.');
    }

    public function updateBindingTab(Request $request, Order $order)
    {
        $user = $request->user();
        if (! $user->isAdmin() && ! $user->isDesigner()) {
            abort(403, 'غير مصرح لك بتعديل التجليد.');
        }

        $validated = $request->validate([
            'book_decorations_id' => 'nullable|exists:book_decorations,id',
            'pages_number' => 'nullable|integer',
            'is_sponge' => 'nullable|boolean',
            'new_svg' => 'nullable|file|mimes:svg,txt|max:512',
            'designer_design_file' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
            'designer_decoration_file' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
            'designer_internal_files.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
            'designer_gift_file' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
        ]);

        if (array_key_exists('book_decorations_id', $validated)) {
            $order->book_decorations_id = $validated['book_decorations_id'];
        }
        if (array_key_exists('pages_number', $validated)) {
            $order->pages_number = $validated['pages_number'];
        }
        $order->is_sponge = $request->boolean('is_sponge');

        if ($request->hasFile('new_svg')) {
            $content = file_get_contents($request->file('new_svg')->getRealPath());
            if ($order->svg) {
                $order->svg->update(['svg_code' => $content]);
            } else {
                $svg = \App\Models\Svg::create(['svg_code' => $content, 'title' => 'Uploaded via Modal']);
                $order->svg_id = $svg->id;
            }
        }

        if ($request->hasFile('designer_design_file')) {
            $order->designer_design_file = $request->file('designer_design_file')->store('designer_uploads', 'public');
        }
        if ($request->hasFile('designer_decoration_file')) {
            $order->designer_decoration_file = $request->file('designer_decoration_file')->store('designer_uploads', 'public');
        }
        if ($request->hasFile('designer_gift_file')) {
            $order->designer_gift_file = $request->file('designer_gift_file')->store('designer_uploads', 'public');
        }
        if ($request->hasFile('designer_internal_files')) {
            $internalPaths = [];
            foreach ($request->file('designer_internal_files') as $file) {
                $internalPaths[] = $file->store('designer_uploads', 'public');
            }
            $order->designer_internal_files = $internalPaths; // Overwrite
        }

        $order->designer_commission = $this->calculateDesignerCommission($order);
        $order->save();

        return back()->with('success', 'تم تحديث معلومات التجليد وحفظ ملفات المصمم بنجاح.');
    }

    public function updateDeliveryInfo(Request $request, Order $order)
    {
        $request->validate([
            'delivery_number_one' => 'required|string|max:20',
            'delivery_number_two' => 'nullable|string|max:20',
            'governorate_id' => 'required|exists:governorates,id',
            'city_id' => 'required|exists:cities,id',
            'area_id' => 'required|exists:areas,id',
            'address' => 'required|string|max:1000',
        ]);

        $order->update([
            'delivery_number_one' => $request->delivery_number_one,
            'delivery_number_two' => $request->delivery_number_two,
            'governorate_id' => $request->governorate_id,
            'city_id' => $request->city_id,
            'area_id' => $request->area_id,
            'address' => $request->address,
        ]);

        return back()->with('success', 'تم تحديث معلومات التوصيل بنجاح.');
    }

    public function deleteImage(Request $request, $id)
    {
        $user = $request->user();
        if (! $user->isAdmin() && ! $user->isDesigner()) {
            return response()->json(['success' => false, 'message' => 'غير مصرح لك بحذف الصور.'], 403);
        }

        $order = Order::findOrFail($id);

        $request->validate([
            'field_name' => 'required|string',
            'image_id' => 'nullable|integer',
            'file_path' => 'nullable|string',
        ]);

        $fieldName = $request->field_name;
        $imageId = $request->image_id;
        $filePath = $request->file_path;

        try {
            // 1. صور مفردة (العلاقة مع UserImage)
            if (in_array($fieldName, ['front_image_id', 'transparent_printing_id', 'internal_image_id'])) {
                $imgIdToDelete = $order->$fieldName;
                $order->$fieldName = null;
                $order->save();

                if ($imgIdToDelete) {
                    $userImg = UserImage::find($imgIdToDelete);
                    if ($userImg) {
                        $this->deleteUserImageFile($userImg);
                    }
                    UserImage::where('id', $imgIdToDelete)->delete();
                }
            }
            // 2. صورة الزخرفة (العلاقة مع BookDecoration)
            elseif ($fieldName === 'book_decorations_id') {
                $decIdToDelete = $order->$fieldName;
                $order->$fieldName = null;
                $order->save();

                if ($decIdToDelete) {
                    BookDecoration::where('id', $decIdToDelete)->delete();
                }
            }
            // 3. مصفوفات الصور (JSON Arrays of UserImage IDs)
            elseif (in_array($fieldName, ['back_image_ids', 'additional_image_id', 'custom_design_image_id'])) {
                if (! $imageId) {
                    throw new \Exception('Image ID required');
                }

                $ids = $order->$fieldName;
                if (is_string($ids)) {
                    $ids = json_decode($ids, true);
                }
                if (! is_array($ids)) {
                    $ids = [];
                }

                // تصفية المصفوفة وحذف الـ ID
                $ids = array_values(array_filter($ids, function ($id) use ($imageId) {
                    return $id != $imageId;
                }));

                $order->$fieldName = json_encode($ids);
                $order->save();

                $userImg = UserImage::find($imageId);
                if ($userImg) {
                    $this->deleteUserImageFile($userImg);
                }
                UserImage::where('id', $imageId)->delete();
            }
            // 4. صور المصمم المفردة (مخزنة كمسار مباشر)
            elseif (in_array($fieldName, ['designer_design_file', 'designer_decoration_file', 'designer_gift_file'])) {
                $pathToDelete = $order->$fieldName;
                $order->$fieldName = null;
                $order->save();

                if ($pathToDelete && \Storage::disk('public')->exists($pathToDelete)) {
                    \Storage::disk('public')->delete($pathToDelete);
                }
            }
            // 5. صور المصمم الداخلية (مصفوفة مسارات مباشرة)
            elseif ($fieldName === 'designer_internal_files') {
                if (! $filePath) {
                    throw new \Exception('File path required');
                }

                $paths = $order->$fieldName;
                if (is_string($paths)) {
                    $paths = json_decode($paths, true);
                }
                if (! is_array($paths)) {
                    $paths = [];
                }

                $paths = array_values(array_filter($paths, function ($p) use ($filePath) {
                    return $p !== $filePath;
                }));

                $order->$fieldName = $paths;
                $order->save();

                if (\Storage::disk('public')->exists($filePath)) {
                    \Storage::disk('public')->delete($filePath);
                }
            } else {
                throw new \Exception('Invalid field name');
            }

            return response()->json(['success' => true, 'message' => 'تم حذف الصورة بنجاح.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'حدث خطأ: '.$e->getMessage()], 500);
        }
    }

    public function updateDesignImage(Request $request, Order $order)
    {
        $user = $request->user();
        if (! $user->isAdmin() && ! $user->isDesigner()) {
            abort(403, 'غير مصرح لك بتعديل التصميم.');
        }

        $request->validate([
            'design_image' => 'required|image|mimes:jpg,jpeg,png,webp|max:10240',
        ]);

        if ($request->hasFile('design_image')) {
            $path = $request->file('design_image')->store('user_images', 'public');

            // Create a new BookDesign for this order
            $design = \App\Models\BookDesign::create([
                'image' => asset('storage/'.$path),
                'is_uploaded_by_user' => false,
            ]);

            $order->book_design_id = $design->id;
            $order->save();
        }

        return back()->with('success', 'تم تعديل صورة التصميم المختارة بنجاح.');
    }

    /**
     * طباعة بوليصات الشحن (فردية أو جماعية)
     */
    public function printAWBs(Request $request)
    {
        $request->validate([
            'order_ids' => 'required|array',
            'order_ids.*' => 'exists:orders,id',
        ]);

        // نجلب أرقام بوليصات الشحن (logestechs_order_id) للطلبات المحددة، ونستثني الطلبات اللي لسا ما ترحلت
        $logestechsIds = Order::whereIn('id', $request->order_ids)
            ->whereNotNull('logestechs_order_id')
            ->pluck('logestechs_order_id')
            ->toArray();

        if (empty($logestechsIds)) {
            return response()->json(['success' => false, 'message' => 'الطلبات المحددة لم يتم ترحيلها لشركة التوصيل بعد (لا يوجد لها بوليصة).']);
        }

        // 🪄 السحر هون: بما إننا جمعنا الطلبات، ممكن طلبين يكون الهم نفس بوليصة الشحن، فبنشيل التكرار!
        $uniqueIds = array_values(array_unique($logestechsIds));

        // نضرب الـ API تبع الـ PDF
        $response = $this->printLogesTechsAWB($uniqueIds);

        if ($response['success']) {
            return response()->json(['success' => true, 'url' => $response['url']]);
        }

        return response()->json(['success' => false, 'message' => $response['message']], 422);
    }
}
