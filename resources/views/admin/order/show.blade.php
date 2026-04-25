@extends('admin.layout')

@push('styles')
    <link href="{{ asset('css/custome.css') }}" rel="stylesheet" type="text/css" />
    <style>
        /* Force uniformity using ID + Class selectors for maximum priority */
        #orderTabsContent .carousel-item img.unified-image,
        #orderTabsContent img.unified-image,
        #tab-graduate-info img.unified-image,
        #tab-internal-book img.unified-image {
            width: 100% !important;
            max-width: 350px !important;
            height: 350px !important;
            object-fit: cover !important;
            object-position: center !important;
            border-radius: 12px !important;
            display: block !important;
            margin: 0 auto !important;
            aspect-ratio: auto !important;
            /* Overriding any aspect-ratio from external CSS */
        }

        /* Standardizing all carousels container */
        #orderTabsContent .carousel {
            max-width: 380px !important;
            margin: 0 auto !important;
            background: #f8f9fa;
            border-radius: 12px;
            padding: 10px;
        }

        /* Fix arrows visibility and position */
        .custom-carousel-control {
            background-color: #0b5ed7 !important;
            width: 35px !important;
            height: 35px !important;
            border-radius: 50% !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            opacity: 0.9 !important;
        }

        .carousel-control-prev.custom-carousel-control {
            left: -15px !important;
        }

        .carousel-control-next.custom-carousel-control {
            right: -15px !important;
        }

        .image-wrapper-relative {
            position: relative;
            display: inline-block;
            width: 100%;
            max-width: 350px;
        }

        .delete-image-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 10;
            background-color: rgba(220, 53, 69, 0.9);
            color: white;
            border: none;
            border-radius: 50%;
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
        }

        .delete-image-btn:hover {
            background-color: #c82333;
            transform: scale(1.1);
        }

        @media (max-width: 768px) {
            #orderTabsContent .carousel-item img.unified-image {
                height: 250px !important;
                max-width: 100% !important;
            }
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
                opacity: 1;
            }

            50% {
                transform: scale(1.2);
                opacity: 0.7;
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container order-show-page">

        @php
            use Illuminate\Support\Str;

            // 🔹 كشف لغة النص (عربي / إنجليزي) لاختيار dir/lang صح
            if (!function_exists('detectLang')) {
                function detectLang($text)
                {
                    return preg_match('/\p{Arabic}/u', $text) ? 'ar' : 'en';
                }
            }

            // 🔹 دالة مساعدة لتهيئة مسار الصور (نفس منطق الكونترولر)
            if (!function_exists('resolveOrderImageUrl')) {
                function resolveOrderImageUrl(?string $path): ?string
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

                    return asset('storage/user_images/' . ltrim($path, '/'));
                }
            }

            // 🔹 عدد الصور الداخلية (تبويب الدفتر من الداخل)
            $internalImagesCount = $internalImages ? $internalImages->count() : 0;

            /** @var \App\Models\User|null $authUser */
            $authUser = auth()->user();
        @endphp

        {{-- 🔹 Header --}}
        <div class="order-page-header" style="direction: rtl; text-align: right;">
            <div class="order-page-header-left">
                <div class="order-page-title">تفاصيل الطلب</div>

                {{-- 🟣 السطر الأول: رقم الطلب + المجموعة + الخريج + حالة التصميم --}}
                <div class="order-page-header-meta order-page-header-meta-top">
                    {{-- 🧾 رقم الطلب --}}
                    <div class="order-header-chip">
                        <div class="order-header-main">
                            <div class="order-header-icon">
                                <i class="fas fa-hashtag"></i>
                            </div>
                            <div class="order-header-body">
                                <span class="order-header-label">رقم الطلب</span>
                                <span class="order-header-value">#{{ $order->id }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- 👥 اسم المجموعة --}}
                    <div class="order-header-chip order-header-chip-muted position-relative">
                        <div class="order-header-main">
                            <div class="order-header-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="order-header-body">
                                <span class="order-header-label">المجموعة</span>
                                <span class="order-header-value d-flex align-items-center gap-2">
                                    @if ($groupNameHeader)
                                        {{ $groupNameHeader }}
                                        {{-- 🚨 أيقونة التحذير (تفتح المودال) --}}
                                        @if(isset($groupWarning))
                                            <button type="button" class="btn btn-link p-0 border-0 text-danger"
                                                data-bs-toggle="modal" data-bs-target="#groupWarningModal" title="تفاصيل التنبيه"
                                                style="animation: pulse 1.5s infinite; line-height:1;">
                                                <i class="fas fa-exclamation-triangle fa-lg"></i>
                                            </button>
                                        @endif
                                    @else
                                        <span class="text-muted">غير متوفر</span>
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>


                    {{-- 🎓 اسم الخريج --}}
                    <div class="order-header-chip order-header-chip-muted">
                        <div class="order-header-main">
                            <div class="order-header-icon">
                                <i class="fas fa-user-graduate"></i>
                            </div>
                            <div class="order-header-body">
                                <span class="order-header-label">الخريج</span>
                                <span class="order-header-value">{{ $graduateNameHeader }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- 🎨 حالة التصميم --}}
                    <div class="order-header-chip order-header-chip-status order-header-chip--2lines">
                        {{-- عنوان --}}
                        <div class="order-chip-title-row">
                            <span class="order-chip-title">حالة التصميم</span>
                            <div class="order-chip-icon">
                                <i class="fas fa-layer-group"></i>
                            </div>
                        </div>

                        {{-- الكنترول + البادج --}}
                        <div class="order-chip-body-row">
                            @if ($canChangeStatusHeader)
                                <div class="order-status-control">
                                    <select class="order-status-select js-order-status-select" data-order-id="{{ $order->id }}">
                                        @foreach ($statusConfigHeader as $value => $cfg)
                                            <option value="{{ $value }}" {{ $order->status === $value ? 'selected' : '' }}>
                                                {{ $cfg['label'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            <span
                                class="order-status-pill badge-status {{ $currentStatusHeader['class'] }} js-order-status-badge-header">
                                <span class="badge-status-dot"></span>
                                <span class="badge-status-text">{{ $currentStatusHeader['label'] }}</span>
                            </span>
                        </div>
                    </div>
                </div>

                {{-- 🔵 السطر الثاني: المصمم المسؤول --}}
                <div class="order-page-header-meta order-page-header-meta-bottom">
                    <div class="order-header-chip order-header-chip-status order-header-chip--2lines">
                        <div class="order-chip-title-row">
                            <span class="order-chip-title">المصمم المسؤول</span>
                            <div class="order-chip-icon">
                                <i class="fas fa-user-tie"></i>
                            </div>
                        </div>

                        <div class="order-chip-body-row">
                            @if ($canChangeDesignerHeader && $authUser)
                                <div class="order-status-control">
                                    @if ($authUser->isAdmin())
                                        {{-- الأدمن: يختار أي مصمم --}}
                                        <select class="order-status-select js-designer-select" data-order-id="{{ $order->id }}">
                                            <option value="">غير معيّن</option>
                                            @foreach ($designers as $designer)
                                                <option value="{{ $designer->id }}" {{ (int) $order->designer_id === (int) $designer->id ? 'selected' : '' }}>
                                                    {{ $designer->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @elseif ($authUser->isDesigner())
                                        @if (!$order->designer_id)
                                            <button type="button" class="btn btn-outline-primary btn-xs js-assign-me-btn"
                                                data-order-id="{{ $order->id }}" data-designer-id="{{ $authUser->id }}">
                                                <i class="fas fa-user-check me-1"></i>
                                                تعيين نفسي كمصمم للطلب
                                            </button>
                                        @elseif ((int) $order->designer_id === (int) $authUser->id)
                                            <span class="badge bg-success">
                                                أنت المصمم المسؤول عن هذا الطلب
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                الطلب معيّن لمصمم آخر
                                            </span>
                                        @endif
                                    @endif
                                </div>
                            @endif

                            <span class="order-status-pill badge-status js-designer-pill">
                                <span class="badge-status-dot"></span>
                                <span class="badge-status-text js-designer-name">
                                    {{ $designerNameHeader ?? 'غير معيّن' }}
                                </span>
                            </span>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- ✅ Tabs --}}
        <div class="order-tabs">
            <ul class="nav nav-tabs justify-content-center" id="orderTabs" role="tablist" style="direction: rtl;">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="tab-order-details-tab" data-bs-toggle="tab"
                        data-bs-target="#tab-order-details" type="button" role="tab">
                        تفاصيل الطلب
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-graduate-info-tab" data-bs-toggle="tab"
                        data-bs-target="#tab-graduate-info" type="button" role="tab">
                        معلومات الخريج
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-internal-book-tab" data-bs-toggle="tab"
                        data-bs-target="#tab-internal-book" type="button" role="tab">
                        الدفتر من الداخل
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-binding-tab" data-bs-toggle="tab" data-bs-target="#tab-binding"
                        type="button" role="tab">
                        تجليد الدفتر
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-delivery-info-tab" data-bs-toggle="tab"
                        data-bs-target="#tab-delivery-info" type="button" role="tab">
                        معلومات التوصيل
                    </button>
                </li>
            </ul>

            <div class="tab-content mt-4" id="orderTabsContent">
                {{-- ====================== تبويب: تفاصيل الطلب ====================== --}}
                <div class="tab-pane fade show active" id="tab-order-details" role="tabpanel"
                    aria-labelledby="tab-order-details-tab">
                    <div class="card order-card mb-4" style="direction: rtl; text-align: right;">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="order-card-header-title">
                                <div class="order-card-header-icon">
                                    <i class="fas fa-receipt"></i>
                                </div>
                                <span>تفاصيل الطلب</span>
                            </div>
                            @if($isAdmin || $isDesigner)
                                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal"
                                    data-bs-target="#editOrderDetailsModal">
                                    <i class="fas fa-pencil-alt"></i> تعديل
                                </button>
                            @endif
                        </div>

                        <div class="card-body">
                            <div class="info-row">
                                <strong>اسم المنتج:</strong>
                                <span>{{ $order->bookType->name_ar ?? 'غير متوفر' }}</span>
                            </div>

                            <div class="section-separator"></div>

                            <div class="mb-3">
                                <div class="section-label">صورة المنتج:</div>
                                <div class="d-flex justify-content-start">
                                    @if ($order->bookType && $order->bookType->image)
                                        <img class="unified-image mb-2"
                                            style="width:100%;max-width:350px;height:350px;object-fit:cover;object-position:center;border-radius:12px;display:block;margin:0 auto;"
                                            src="{{ $order->bookType->image }}" alt="صورة التصميم">
                                    @else
                                        <span class="text-muted">لا يوجد تصميم متوفر</span>
                                    @endif
                                </div>
                            </div>

                            <div class="info-row">
                                <strong>الجندر:</strong>
                                <span>
                                    @if ($order->user_gender === 'male')
                                        ذكر
                                    @elseif ($order->user_gender === 'female')
                                        أنثى
                                    @elseif ($order->user_gender)
                                        {{ $order->user_gender }}
                                    @else
                                        غير متوفر
                                    @endif
                                </span>
                            </div>

                            <div class="info-row">
                                <strong>سعر الطلب شامل كود الخصم:</strong>
                                <span>{{ $order->final_price_with_discount ?? 'غير متوفر' }}</span>
                            </div>

                            <div class="info-row">
                                <strong>اسم كود الخصم:</strong>
                                <span>{{ $order->discountCode->discount_code ?? 'غير متوفر' }}</span>
                            </div>

                            <div class="info-row">
                                <strong>قيمة الخصم:</strong>
                                <span>
                                    @if ($order->discountCode)
                                        {{ $order->discountCode->discount_value }}
                                        {{ $order->discountCode->discount_type === 'percentage' ? '%' : 'دينار' }}
                                    @else
                                        غير متوفر
                                    @endif
                                </span>
                            </div>

                            <div class="info-row">
                                <strong>مع إضافات:</strong>
                                <span>{{ $order->is_with_additives ? 'نعم' : 'لا' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ====================== تبويب: الدفتر من الداخل ====================== --}}
                <div class="tab-pane fade" id="tab-internal-book" role="tabpanel" aria-labelledby="tab-internal-book-tab">

                    <div class="card order-card mb-4" style="direction: rtl; text-align: right;">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="order-card-header-title">
                                <div class="order-card-header-icon">
                                    <i class="fas fa-book-open"></i>
                                </div>
                                <span>الدفتر من الداخل</span>
                            </div>
                            @if($isAdmin || $isDesigner)
                                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal"
                                    data-bs-target="#editInternalBookModal">
                                    <i class="fas fa-pencil-alt"></i> تعديل
                                </button>
                            @endif
                        </div>

                        <div class="card-body">
                            {{-- 🔹 الصور الداخلية --}}
                            <div class="mb-4 text-center">
                                <strong class="d-block mb-2">
                                    الصور الداخلية
                                    @if ($internalImagesCount > 0)
                                        (عدد: {{ $internalImagesCount }})
                                    @endif
                                </strong>

                                @if ($internalImagesCount > 0)
                                    <div id="internalImagesCarousel" class="carousel slide mb-3" data-bs-ride="false">
                                        <div class="carousel-inner text-center">
                                            @foreach ($internalImages as $index => $img)
                                                @php
                                                    $src = resolveOrderImageUrl($img->image_path ?? null);
                                                @endphp

                                                @if ($src)
                                                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                                        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                                            <div class="image-wrapper-relative mx-auto">
                                                                @if($isAdmin || $isDesigner)
                                                                    <button type="button" class="delete-image-btn"
                                                                        onclick="deleteOrderImage('additional_image_id', {{ $img->id }})"
                                                                        title="حذف الصورة">
                                                                        <i class="fas fa-trash-alt"></i>
                                                                    </button>
                                                                @endif
                                                                <img src="{{ $src }}" class="unified-image mb-2"
                                                                    style="width:100%;max-width:350px;height:350px;object-fit:cover;object-position:center;border-radius:12px;display:block;margin:0 auto;"
                                                                    alt="الصورة الداخلية {{ $index + 1 }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>

                                        <button class="carousel-control-prev custom-carousel-control" type="button"
                                            data-bs-target="#internalImagesCarousel" data-bs-slide="prev">
                                            <span class="carousel-control-prev-icon"></span>
                                            <span class="visually-hidden">السابق</span>
                                        </button>

                                        <button class="carousel-control-next custom-carousel-control" type="button"
                                            data-bs-target="#internalImagesCarousel" data-bs-slide="next">
                                            <span class="carousel-control-next-icon"></span>
                                            <span class="visually-hidden">التالي</span>
                                        </button>
                                    </div>

                                    {{-- أزرار التحميل --}}
                                    <div class="download-buttons-wrapper">
                                        <button type="button" class="btn-download btn-download-all"
                                            id="downloadAllInternalImages">
                                            <i class="fas fa-cloud-download-alt"></i>
                                            تحميل جميع الصور
                                        </button>

                                        <button type="button" class="btn-download btn-download-current"
                                            id="downloadCurrentInternalImage">
                                            <i class="fas fa-download"></i>
                                            تحميل الصورة الحالية
                                        </button>
                                    </div>
                                @else
                                    <p class="text-muted">لا توجد صور داخلية.</p>
                                @endif
                            </div>



                            {{-- 🔸 صورة الزخرفة --}}
                            <div class="mb-4 text-center" id="decorationImageBlock">
                                <strong class="d-block mb-2">صورة الزخرفة</strong>

                                @if ($order->bookDecoration)
                                    <p class="mb-1" style="font-weight: bold;">
                                        {{ $order->bookDecoration->name }}
                                    </p>

                                    @if ($decorationImage)
                                        <div class="image-wrapper-relative mx-auto">
                                            @if($isAdmin || $isDesigner)
                                                <button type="button" class="delete-image-btn"
                                                    onclick="deleteOrderImage('book_decorations_id')" title="حذف الصورة">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            @endif
                                            <img src="{{ $decorationImage ?? $order->bookDecoration->image }}"
                                                class="unified-image mb-2"
                                                style="width:100%;max-width:350px;height:350px;object-fit:cover;object-position:center;border-radius:12px;display:block;margin:0 auto;"
                                                alt="صورة الزخرفة">
                                        </div>

                                        <div class="download-buttons-wrapper">
                                            <!-- <button type="button"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        class="btn-download btn-download-all"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        id="downloadAllDecorationImages">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <i class="fas fa-cloud-download-alt"></i>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        تحميل جميع الصور
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    </button> -->

                                            <button type="button" class="btn-download btn-download-current"
                                                id="downloadCurrentDecorationImage">
                                                <i class="fas fa-download"></i>
                                                تحميل الصورة الحالية
                                            </button>
                                        </div>
                                    @else
                                        <p class="text-muted">لا توجد صورة للزخرفة.</p>
                                    @endif
                                @else
                                    <p class="text-muted">لا توجد زخرفة محددة.</p>
                                @endif
                            </div>

                            {{-- الإهداء --}}
                            <div class="mb-3">
                                <strong>الإهداء:</strong>

                                <div class="mt-2">
                                    @if ($giftTypeInternal === 'none')
                                        <span class="badge bg-secondary">بدون إهداء</span>

                                    @elseif ($giftTypeInternal === 'default')
                                        <span class="badge bg-info text-dark">إهداء موحّد</span>

                                    @elseif ($giftTypeInternal === 'custom')
                                        <span class="badge bg-primary">إهداء مخصّص</span>

                                        @if (!empty($giftTitleInternal))
                                            <div class="note-box auto-dir mt-2" lang="{{ detectLang($giftTitleInternal) }}">
                                                {!! nl2br(e($giftTitleInternal)) !!}
                                            </div>
                                        @else
                                            <div class="note-box-light text-muted mt-2">
                                                لا توجد عبارة مضافة للإهداء المخصّص.
                                            </div>
                                        @endif

                                    @else
                                        <span class="badge bg-secondary">لا يوجد إهداء.</span>
                                    @endif
                                </div>
                            </div>

                            <div class="section-separator"></div>

                            {{-- 🔹 ملاحظات الدفتر --}}
                            <div class="mb-3">
                                <div class="section-label">ملاحظات الدفتر</div>

                                <div class="note-box auto-dir mt-2" dir="auto" style="cursor: default;">
                                    <div id="notebook-followup-box">
                                        @if ($order->notebook_followup_note)
                                            <div>{!! nl2br(e($order->notebook_followup_note)) !!}</div>
                                        @else
                                            <span class="text-muted">لا توجد ملاحظات حتى الآن.</span>
                                        @endif
                                    </div>
                                </div>

                                @if ($canAddNote)
                                    <form id="notebookUpdateForm" class="js-notebook-followup-form" {{-- 🔴 غيرنا اسم الراوت
                                        ليتطابق مع الكونترولر --}}
                                        action="{{ route('orders.updateNotebookFollowup', $order->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <textarea name="notebook_followup_note" class="form-control mt-2 auto-dir" dir="auto"
                                            rows="2"
                                            placeholder="اكتب ملاحظات الدفتر هنا...">{{ old('notebook_followup_note') }}</textarea>

                                        <div class="mt-3 text-end">
                                            <button type="submit" class="btn btn-primary btn-sm">
                                                <i class="fas fa-save me-1"></i> حفظ الملاحظات
                                            </button>
                                        </div>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ====================== تبويب: تجليد الدفتر ====================== --}}
                <div class="tab-pane fade" id="tab-binding" role="tabpanel" aria-labelledby="tab-binding-tab">
                    <div class="card order-card mb-4 binding-card" style="direction: rtl; text-align: right;">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="order-card-header-title">
                                <div class="order-card-header-icon">
                                    <i class="fas fa-layer-group"></i>
                                </div>
                                <span>تجليد الدفتر</span>
                            </div>
                            @if($isAdmin || $isDesigner)
                                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal"
                                    data-bs-target="#editBindingModal">
                                    <i class="fas fa-pencil-alt"></i> تعديل
                                </button>
                            @endif
                        </div>

                        <div class="card-body">
                            <form id="bindingUpdateForm" class="js-binding-followup-form"
                                action="{{ route('orders.updateBinding', $order->id) }}" method="POST"
                                enctype="multipart/form-data">

                                @csrf
                                @method('PUT')

                                {{-- 🔹 ملخص سريع للتجليد --}}
                                <div class="graduate-meta-row">
                                    {{-- حالة الإضافات --}}
                                    <div class="graduate-meta-item">
                                        <div class="graduate-meta-label">حالة الإضافات</div>
                                        <div class="graduate-meta-value">
                                            <div class="graduate-meta-icon">
                                                <i class="fas fa-plus-square"></i>
                                            </div>
                                            @if ($order->is_with_additives)
                                                <span>يوجد إضافات</span>
                                            @else
                                                <span class="text-muted">لا يوجد إضافات</span>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- عدد الورق --}}
                                    <div class="graduate-meta-item">
                                        <div class="graduate-meta-label">عدد الورق</div>
                                        <div class="graduate-meta-value">
                                            <div class="graduate-meta-icon">
                                                <i class="fas fa-book"></i>
                                            </div>
                                            <span>
                                                @if ($pagesCount > 0)
                                                    {{ $pagesCount }} ورقة
                                                @else
                                                    غير محدد
                                                @endif
                                            </span>
                                        </div>
                                    </div>

                                    {{-- حالة الإسفنج --}}
                                    <div class="graduate-meta-item">
                                        <div class="graduate-meta-label">الإسفنج</div>
                                        <div class="graduate-meta-value">
                                            <div class="graduate-meta-icon">
                                                <i class="fas fa-border-all"></i>
                                            </div>
                                            <span>
                                                {{ $order->is_sponge ? 'مع إسفنج' : 'بدون إسفنج' }}
                                            </span>
                                        </div>
                                    </div>


                                </div>

                                <div class="section-separator"></div>

                                {{-- 🔹 تفاصيل الإضافات --}}
                                <div class="row">
                                    <div class="col-md-7 mb-3">
                                        <div class="section-label">تفاصيل الإضافات</div>

                                        <div class="note-box-light">
                                            {{-- صور داخلية --}}
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" disabled {{ $internalImagesCountBinding > 0 ? 'checked' : '' }}>
                                                <label class="form-check-label">
                                                    صور داخلية
                                                    @if ($internalImagesCountBinding > 0)
                                                        (عدد: {{ $internalImagesCountBinding }})
                                                    @endif
                                                </label>
                                            </div>

                                            {{-- زخرفة --}}
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" disabled {{ $order->bookDecoration ? 'checked' : '' }}>
                                                <label class="form-check-label">
                                                    زخرفة
                                                    @if ($order->bookDecoration)
                                                        ({{ $order->bookDecoration->name }})
                                                    @endif
                                                </label>

                                                @unless ($order->bookDecoration)
                                                    <span class="ms-1 text-muted">(لا توجد زخرفة محددة)</span>
                                                @endunless
                                            </div>

                                            {{-- طباعة شفافة --}}
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" disabled {{ $order->transparentPrinting ? 'checked' : '' }}>
                                                <label class="form-check-label">
                                                    طباعة شفافة
                                                </label>
                                            </div>
                                        </div>
                                    </div>



                                </div>

                                <div class="section-separator"></div>

                                {{-- 🔹 الصور الداخلية داخل تجليد الدفتر --}}
                                <div class="mb-3">
                                    <div class="section-label">الصور الداخلية داخل تجليد الدفتر</div>

                                    @if ($internalImagesCountBinding > 0)
                                        <div class="d-flex flex-wrap" style="gap: 8px;">
                                            @foreach ($bindingInternalImages as $index => $img)
                                                @php
                                                    $srcBinding = resolveOrderImageUrl($img->image_path ?? null);
                                                @endphp

                                                @if ($srcBinding)
                                                    <div>
                                                        <img src="{{ $srcBinding }}" class="unified-image mb-2"
                                                            style="width:100%;max-width:350px;height:350px;object-fit:cover;object-position:center;border-radius:12px;display:block;margin:0 auto;"
                                                            alt="صورة داخلية {{ $index + 1 }}">
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-muted mb-0">لا توجد صور داخلية لهذا التجليد.</p>
                                    @endif
                                </div>

                                <div class="section-separator"></div>

                                {{-- 🔹 صورة الزخرفة في تجليد الدفتر --}}
                                <div class="mb-3">
                                    <div class="section-label">صورة الزخرفة في تجليد الدفتر</div>

                                    @if ($order->bookDecoration)
                                        <p class="mb-1" style="font-weight: bold;">
                                            {{ $order->bookDecoration->name }}
                                        </p>

                                        @if ($order->bookDecoration->image)
                                            <img src="{{ $order->bookDecoration->image }}" class="unified-image mb-2"
                                                style="width:100%;max-width:350px;height:350px;object-fit:cover;object-position:center;border-radius:12px;display:block;margin:0 auto;"
                                                alt="صورة الزخرفة">
                                        @else
                                            <p class="text-muted mb-0">لا توجد صورة للزخرفة.</p>
                                        @endif
                                    @else
                                        <p class="text-muted mb-0">لا توجد زخرفة محددة.</p>
                                    @endif
                                </div>
                                <div class="section-separator"></div>

                                {{-- ✅ ملفات التجليد النهائية — تصميم مبهر --}}
                                <div class="final-files-section">
                                    {{-- العنوان --}}
                                    <div class="final-files-header">
                                        <div class="final-files-header-icon">
                                            <i class="fas fa-gem"></i>
                                        </div>
                                        <div>
                                            <div class="final-files-title">ملفات التجليد النهائية</div>
                                            <div class="final-files-subtitle">الملفات المعتمدة من المصمم</div>
                                        </div>
                                        <div class="final-files-badge">
                                            <i class="fas fa-check-circle"></i> جاهز
                                        </div>
                                    </div>

                                    {{-- شبكة البطاقات --}}
                                    <div class="final-files-grid">

                                        {{-- 1) التصميم النهائي --}}
                                        <div class="final-file-card">
                                            <div class="final-file-card-icon design-icon">
                                                <i class="fas fa-paint-brush"></i>
                                            </div>
                                            <div class="final-file-card-label">التصميم النهائي</div>
                                            @if($order->designer_design_file)
                                                <div class="final-file-img-wrap">
                                                    <div class="final-file-img-wrap image-wrapper-relative">
                                                        <img src="{{ asset('storage/' . $order->designer_design_file) }}"
                                                            alt="التصميم النهائي">
                                                    </div>
                                                    <div class="final-file-img-overlay d-flex gap-2 justify-content-center">
                                                        <a href="{{ asset('storage/' . $order->designer_design_file) }}"
                                                            download class="final-file-dl-btn" title="تحميل">
                                                            <i class="fas fa-cloud-download-alt"></i> تحميل
                                                        </a>
                                                        @if($isAdmin || $isDesigner)
                                                            <button type="button" class="final-file-dl-btn"
                                                                style="border:none; cursor:pointer;"
                                                                onclick="document.getElementById('direct_upload_design').click();"
                                                                title="تعديل">
                                                                <i class="fas fa-upload"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>
                                            @else
                                                <div class="final-file-empty">
                                                    <i class="fas fa-image"></i>
                                                    <span>لم يتم الرفع بعد</span>
                                                    @if($isAdmin || $isDesigner)
                                                        <button type="button" class="btn btn-sm btn-outline-primary mt-2"
                                                            onclick="document.getElementById('direct_upload_design').click();">
                                                            <i class="fas fa-plus"></i> إضافة
                                                        </button>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>

                                        {{-- 2) الزخرفة --}}
                                        <div class="final-file-card">
                                            <div class="final-file-card-icon decoration-icon">
                                                <i class="fas fa-feather-alt"></i>
                                            </div>
                                            <div class="final-file-card-label">الزخرفة</div>
                                            @if($order->designer_decoration_file)
                                                <div class="final-file-img-wrap">
                                                    <img src="{{ asset('storage/' . $order->designer_decoration_file) }}"
                                                        alt="الزخرفة">
                                                    <div class="final-file-img-overlay d-flex gap-2 justify-content-center">
                                                        <a href="{{ asset('storage/' . $order->designer_decoration_file) }}"
                                                            download class="final-file-dl-btn" title="تحميل">
                                                            <i class="fas fa-cloud-download-alt"></i> تحميل
                                                        </a>
                                                        @if($isAdmin || $isDesigner)
                                                            <button type="button" class="final-file-dl-btn"
                                                                style="border:none; cursor:pointer;"
                                                                onclick="document.getElementById('direct_upload_decoration').click();"
                                                                title="تعديل">
                                                                <i class="fas fa-upload"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>
                                            @else
                                                <div class="final-file-empty">
                                                    <i class="fas fa-image"></i>
                                                    <span>لم يتم الرفع بعد</span>
                                                    @if($isAdmin || $isDesigner)
                                                        <button type="button" class="btn btn-sm btn-outline-primary mt-2"
                                                            onclick="document.getElementById('direct_upload_decoration').click();">
                                                            <i class="fas fa-plus"></i> إضافة
                                                        </button>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>

                                        {{-- 3) الإهداء المخصص --}}
                                        @if($order->gift_type === 'custom')
                                            <div class="final-file-card">
                                                <div class="final-file-card-icon gift-icon">
                                                    <i class="fas fa-gift"></i>
                                                </div>
                                                <div class="final-file-card-label">الإهداء المخصص</div>
                                                @if($order->designer_gift_file)
                                                    <div class="final-file-img-wrap">
                                                        <img src="{{ asset('storage/' . $order->designer_gift_file) }}"
                                                            alt="الإهداء المخصص">
                                                        <div class="final-file-img-overlay d-flex gap-2 justify-content-center">
                                                            <a href="{{ asset('storage/' . $order->designer_gift_file) }}" download
                                                                class="final-file-dl-btn" title="تحميل">
                                                                <i class="fas fa-cloud-download-alt"></i> تحميل
                                                            </a>
                                                            @if($isAdmin || $isDesigner)
                                                                <button type="button" class="final-file-dl-btn"
                                                                    style="border:none; cursor:pointer;"
                                                                    onclick="document.getElementById('direct_upload_gift').click();"
                                                                    title="تعديل">
                                                                    <i class="fas fa-upload"></i>
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="final-file-empty">
                                                        <i class="fas fa-image"></i>
                                                        <span>لم يتم الرفع بعد</span>
                                                        @if($isAdmin || $isDesigner)
                                                            <button type="button" class="btn btn-sm btn-outline-primary mt-2"
                                                                onclick="document.getElementById('direct_upload_gift').click();">
                                                                <i class="fas fa-plus"></i> إضافة
                                                            </button>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        @endif

                                        {{-- 4) الصور الداخلية --}}
                                        <div class="final-file-card final-file-card-internal">
                                            <div class="final-file-card-icon internal-icon">
                                                <i class="fas fa-images"></i>
                                            </div>
                                            <div class="final-file-card-label">
                                                الصور الداخلية
                                                <span class="final-file-count-badge">
                                                    {{ is_array($order->designer_internal_files) ? count($order->designer_internal_files) : 0 }}
                                                </span>
                                            </div>
                                            @if(is_array($order->designer_internal_files) && count($order->designer_internal_files) > 0)
                                                <div class="final-file-internal-grid">
                                                    @foreach($order->designer_internal_files as $idx => $internalFile)
                                                        <div class="final-file-thumb-wrap">
                                                            <img src="{{ asset('storage/' . $internalFile) }}"
                                                                alt="صورة داخلية {{ $idx + 1 }}">
                                                            <div class="final-file-thumb-overlay">
                                                                <a href="{{ asset('storage/' . $internalFile) }}" download
                                                                    title="تحميل الصورة {{ $idx + 1 }}">
                                                                    <i class="fas fa-download"></i>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                    @if($isAdmin || $isDesigner)
                                                        <div class="final-file-thumb-wrap d-flex justify-content-center align-items-center"
                                                            style="background: #eef2f5; cursor: pointer; border: 2px dashed #b5c4d1; border-radius: 8px;"
                                                            onclick="document.getElementById('direct_upload_internal').click();"
                                                            title="إضافة مجلد">
                                                            <i class="fas fa-plus text-primary fs-3"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                            @else
                                                <div class="final-file-empty">
                                                    <i class="fas fa-images"></i>
                                                    <span>لم يتم الرفع بعد</span>
                                                    @if($isAdmin || $isDesigner)
                                                        <button type="button" class="btn btn-sm btn-outline-primary mt-2"
                                                            onclick="document.getElementById('direct_upload_internal').click();">
                                                            <i class="fas fa-plus"></i> إضافة
                                                        </button>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>

                                    </div>



                                </div>
                                <div class="section-separator"></div>

                                {{-- 🔹 الإهداء --}}
                                <div class="mb-3">
                                    <div class="section-label">الإهداء</div>

                                    <div class="mt-1">
                                        @if ($giftTypeBinding === 'none')
                                            <span class="text-muted">لا يوجد أي إهداء.</span>

                                        @elseif ($giftTypeBinding === 'default')
                                            <span class="badge bg-info text-dark">إهداء موحّد</span>

                                            <div class="note-box auto-dir mt-2" dir="auto">
                                                {{ $defaultGiftText }}
                                            </div>

                                        @elseif ($giftTypeBinding === 'custom' && !empty($giftTitleBinding))
                                            @php
                                                $isGiftImage = Str::startsWith($giftTitleBinding, [
                                                    'http://',
                                                    'https://',
                                                    '/storage',
                                                ]);

                                                if ($isGiftImage) {
                                                    $giftSrc = Str::startsWith($giftTitleBinding, ['http://', 'https://'])
                                                        ? $giftTitleBinding
                                                        : asset(ltrim($giftTitleBinding, '/'));
                                                }
                                            @endphp

                                            @if ($isGiftImage ?? false)
                                                <img src="{{ $giftSrc }}" alt="العبارة المخصصة" class="unified-image mb-2"
                                                    style="width:100%;max-width:350px;height:350px;object-fit:cover;object-position:center;border-radius:12px;display:block;margin:0 auto;">
                                            @else
                                                <div class="note-box auto-dir" dir="auto">
                                                    {!! nl2br(e($giftTitleBinding)) !!}
                                                </div>
                                            @endif
                                        @else
                                            <span class="text-muted">لا يوجد إهداء.</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="section-separator"></div>

                                {{-- 🔹 ملاحظات المتابعة على التجليد --}}
                                <div class="mb-3">
                                    <div class="section-label">ملاحظات المتابعة على التجليد</div>

                                    <div class="note-box auto-dir mt-2" dir="auto" style="cursor: default;">
                                        <div id="binding-followup-box">
                                            @if ($bindingFollowupText)
                                                <div>{!! nl2br(e($bindingFollowupText)) !!}</div>
                                            @else
                                                <span class="text-muted">لا توجد ملاحظات حتى الآن.</span>
                                            @endif
                                        </div>
                                    </div>

                                    @if ($canAddNote)
                                        <textarea name="binding_followup_note" class="form-control mt-2 auto-dir" dir="auto"
                                            rows="2" placeholder="اكتب ملاحظة جديدة على التجليد هنا..."></textarea>
                                    @endif
                                </div>

                                @if ($canEditBinding)
                                    <div class="mt-3 text-end">
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <i class="fas fa-save me-1"></i> حفظ تعديلات التجليد
                                        </button>
                                    </div>
                                @endif
                            </form>

                            {{-- Hidden forms for direct upload (Moved outside of the main form to prevent nested form HTML
                            issue) --}}
                            <form id="directUploadForm_design" action="{{ route('orders.updateBindingTab', $order->id) }}"
                                method="POST" enctype="multipart/form-data" style="display: none !important;">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="book_decorations_id" value="{{ $order->book_decorations_id }}">
                                <input type="hidden" name="pages_number" value="{{ $order->pages_number }}">
                                <input type="hidden" name="is_sponge" value="{{ $order->is_sponge ? '1' : '0' }}">
                                <input type="file" id="direct_upload_design" name="designer_design_file" accept="image/*"
                                    style="display: none !important;"
                                    onchange="document.getElementById('directUploadForm_design').submit();">
                            </form>
                            <form id="directUploadForm_decoration"
                                action="{{ route('orders.updateBindingTab', $order->id) }}" method="POST"
                                enctype="multipart/form-data" style="display: none !important;">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="book_decorations_id" value="{{ $order->book_decorations_id }}">
                                <input type="hidden" name="pages_number" value="{{ $order->pages_number }}">
                                <input type="hidden" name="is_sponge" value="{{ $order->is_sponge ? '1' : '0' }}">
                                <input type="file" id="direct_upload_decoration" name="designer_decoration_file"
                                    accept="image/*" style="display: none !important;"
                                    onchange="document.getElementById('directUploadForm_decoration').submit();">
                            </form>
                            <form id="directUploadForm_gift" action="{{ route('orders.updateBindingTab', $order->id) }}"
                                method="POST" enctype="multipart/form-data" style="display: none !important;">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="book_decorations_id" value="{{ $order->book_decorations_id }}">
                                <input type="hidden" name="pages_number" value="{{ $order->pages_number }}">
                                <input type="hidden" name="is_sponge" value="{{ $order->is_sponge ? '1' : '0' }}">
                                <input type="file" id="direct_upload_gift" name="designer_gift_file" accept="image/*"
                                    style="display: none !important;"
                                    onchange="document.getElementById('directUploadForm_gift').submit();">
                            </form>
                            <form id="directUploadForm_internal" action="{{ route('orders.updateBindingTab', $order->id) }}"
                                method="POST" enctype="multipart/form-data" style="display: none !important;">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="book_decorations_id" value="{{ $order->book_decorations_id }}">
                                <input type="hidden" name="pages_number" value="{{ $order->pages_number }}">
                                <input type="hidden" name="is_sponge" value="{{ $order->is_sponge ? '1' : '0' }}">
                                <input type="file" id="direct_upload_internal" name="designer_internal_files[]"
                                    accept="image/*" multiple style="display: none !important;"
                                    onchange="document.getElementById('directUploadForm_internal').submit();">
                            </form>

                        </div>
                    </div>
                </div>

                {{-- ====================== تبويب: معلومات التوصيل ====================== --}}
                <div class="tab-pane fade" id="tab-delivery-info" role="tabpanel" aria-labelledby="tab-delivery-info-tab">
                    <div class="card order-card mb-4" style="direction: rtl; text-align: right;">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="order-card-header-title">
                                <div class="order-card-header-icon">
                                    <i class="fas fa-truck"></i>
                                </div>
                                <span>معلومات التوصيل</span>
                            </div>
                            @if($isAdmin || $isDesigner)
                                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal"
                                    data-bs-target="#editDeliveryModal">
                                    <i class="fas fa-pencil-alt"></i> تعديل
                                </button>
                            @endif
                        </div>

                        <div class="card-body">
                            <div class="info-row">
                                <strong>رقم 1:</strong>
                                <span>{{ $order->delivery_number_one ?? 'غير متوفر' }}</span>
                            </div>

                            <div class="info-row">
                                <strong>رقم 2:</strong>
                                <span>{{ $order->delivery_number_two ?? 'غير متوفر' }}</span>
                            </div>

                            <div class="info-row">
                                <strong>المحافظة:</strong>
                                <span>{{ optional($order->governorate)->name_ar ?? 'غير متوفر' }}</span>
                            </div>

                            <div class="info-row">
                                <strong>المدينة:</strong>
                                <span>{{ optional($order->city)->name_ar ?? 'غير متوفر' }}</span>
                            </div>

                            <div class="info-row">
                                <strong>المنطقة (القرية):</strong>
                                <span>{{ optional($order->area)->name_ar ?? 'غير متوفر' }}</span>
                            </div>

                            <div class="info-row">
                                <strong>تفاصيل العنوان:</strong>
                                <span>{{ $order->address ?? 'غير متوفر' }}</span>
                            </div>

                            <div class="info-row">
                                <strong>السعر:</strong>
                                <span>
                                    @if (!is_null($order->final_price_with_discount))
                                        {{ $order->final_price_with_discount }}
                                    @elseif (!is_null($order->final_price))
                                        {{ $order->final_price }}
                                    @else
                                        غير متوفر
                                    @endif
                                </span>
                            </div>

                            <div class="mb-2 mt-3">
                                <strong>ملاحظات المتابعة على التوصيل:</strong>

                                <div class="note-box auto-dir mt-2" dir="auto">
                                    <div id="delivery-followup-box">
                                        @if ($deliveryFollowupText)
                                            <div>{!! nl2br(e($deliveryFollowupText)) !!}</div>
                                        @else
                                            <span class="text-muted">لا توجد ملاحظات حتى الآن.</span>
                                        @endif
                                    </div>
                                </div>

                                @if ($canEditDeliveryFollowup)
                                    <form action="{{ route('orders.updateDeliveryFollowup', $order->id) }}" method="POST"
                                        class="mt-2 js-delivery-followup-form">
                                        @csrf
                                        @method('PUT')

                                        <textarea name="delivery_followup_note" class="form-control auto-dir" dir="auto"
                                            rows="3"
                                            placeholder="اكتب ملاحظات المتابعة على التوصيل هنا...">{{  $deliveryFollowupText }}</textarea>

                                        <div class="text-end mt-2">
                                            <button type="submit" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-save me-1"></i> حفظ ملاحظات التوصيل
                                            </button>
                                        </div>
                                    </form>
                                @endif
                            </div>

                        </div>
                    </div>
                </div>

                {{-- ====================== تبويب: معلومات الخريج ====================== --}}
                <div class="tab-pane fade" id="tab-graduate-info" role="tabpanel" aria-labelledby="tab-graduate-info-tab">

                    <div class="card order-card mb-4" style="direction: rtl; text-align: right;">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="order-card-header-title">
                                <div class="order-card-header-icon">
                                    <i class="fas fa-user-graduate"></i>
                                </div>
                                <span>معلومات الخريج</span>
                            </div>
                            @if($isAdmin || $isDesigner)
                                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal"
                                    data-bs-target="#editGraduateInfoModal">
                                    <i class="fas fa-pencil-alt"></i> تعديل
                                </button>
                            @endif
                        </div>

                        <div class="card-body">
                            {{-- ✅ أولاً: معلومات الخريج "الأصليات" --}}
                            <div class="graduate-meta-row">
                                {{-- 🔹 اسم المجموعة --}}
                                <div class="graduate-meta-item">
                                    <div class="graduate-meta-label">اسم المجموعة</div>
                                    <div class="graduate-meta-value">
                                        <div class="graduate-meta-icon">
                                            <i class="fas fa-users"></i>
                                        </div>

                                        @if ($order->discountCode && $order->discountCode->code_name)
                                            <span>{{ $order->discountCode->code_name }}</span>
                                        @elseif ($order->discountCode)
                                            <span>{{ $order->discountCode->discount_code }}</span>
                                        @else
                                            <span class="text-muted">غير متوفر</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- رقم اعتماد التصميم + واتساب --}}
                            <div class="row mb-3">
                                <div class="col-md-6 mb-1">
                                    <strong> رقم اعتماد التصميم:</strong>

                                    <div class="mt-1 d-flex align-items-center">
                                        <span>
                                            {{ $order->user_phone_number ?? 'غير متوفر' }}
                                        </span>
                                        @if (!empty($order->user_phone_number))
                                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $order->user_phone_number) }}"
                                                target="_blank" class="ms-2" style="color: #25D366; font-size: 22px;">
                                                <i class="fab fa-whatsapp"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- الاسم عربي + زر نسخ SVG للاسم --}}
                            <p>
                                <strong>الاسم (عربي):</strong> {{ $order->username_ar }}

                                {{-- عرض SVG الخاص باسم الخريج --}}
                                @if (!empty($svgCodeForName) && is_array($svgCodeForName))
                                    <div class="d-flex align-items-start mb-3 mt-2">
                                        <div class="svg-preview-box me-3 border p-2 rounded bg-white shadow-sm"
                                            style="width: 160px; text-align: center;">
                                            <img src="{{ $svgCodeForName['url'] }}" alt="SVG Name" class="drag-to-ps"
                                                draggable="true" title="امسك الصورة واسحبها للـ Word، أو استخدم الأزرار للفوتوشوب"
                                                style="max-width: 100%; height: auto; cursor: grab;">
                                        </div>

                                        <div class="d-flex flex-column gap-2">
                                            <a href="{{ $svgCodeForName['url'] }}"
                                                download="Name_{{ $order->username_ar }}_Order_{{ $order->id }}.svg"
                                                class="btn btn-primary rounded-pill px-3 py-2 fw-bold shadow-sm d-flex align-items-center">
                                                <i class="fas fa-download me-2"></i> تنزيل كملف
                                            </a>

                                            <button type="button"
                                                class="btn btn-dark rounded-pill px-3 py-2 fw-bold shadow-sm d-flex align-items-center copy-for-ps-btn"
                                                data-url="{{ $svgCodeForName['url'] }}">
                                                <i class="fas fa-copy me-2"></i> نسخ للفوتوشوب (Copy)
                                            </button>
                                        </div>
                                    </div>
                                @else
                                <span class="badge bg-warning ms-2">SVG للاسم غير مضاف بعد</span>
                            @endif
                            </p>

                            {{-- الاسم إنجليزي --}}
                            <p>
                                <strong>الاسم (إنجليزي):</strong> {{ $order->username_en ?? 'غير متوفر' }}
                            </p>

                            {{-- الجامعة / الدبلوم --}}
                            <p>
                                <strong>الجامعة / الدبلوم:</strong>
                                @if($order->university)
                                    {{ $order->university->name }}
                                @elseif($order->diploma)
                                    {{ $order->diploma->name }}
                                @else
                                    <span class="text-muted">غير متوفر</span>
                                @endif
                            </p>

                            {{-- التخصص --}}
                            <p>
                                <strong>التخصص:</strong>
                                @if($order->universityMajor)
                                    {{ $order->universityMajor->name }}
                                @elseif($order->diplomaMajor)
                                    {{ $order->diplomaMajor->name }}
                                @else
                                    <span class="text-muted">غير متوفر</span>
                                @endif
                            </p>

                            <div class="section-separator"></div>

                            {{-- 1️⃣ ملاحظات المتابعة على التصميم --}}
                            <div class="mb-3">
                                <div class="section-label">ملاحظات المتابعة على التصميم</div>

                                <div class="note-box auto-dir mb-2" dir="auto" style="min-height: 80px; cursor: default;">
                                    <div id="design-followup-box">
                                        @if ($designFollowupText)
                                            <div>{!! nl2br(e($designFollowupText)) !!}</div>
                                        @else
                                            <span class="text-muted">لا توجد ملاحظات متابعة حتى الآن.</span>
                                        @endif
                                    </div>
                                </div>

                                @if ($canEditDesignFollowup)
                                    <form action="{{ route('orders.updateDesignFollowup', $order->id) }}" method="POST"
                                        class="mt-2 js-design-followup-form">
                                        @csrf
                                        @method('PUT')

                                        <textarea name="design_followup_note" class="form-control auto-dir" dir="auto" rows="3"
                                            placeholder="اكتب ملاحظة جديدة لتضاف إلى الملاحظات السابقة...">{{ old('design_followup_note') }}</textarea>

                                        <div class="text-end mt-2">
                                            <button type="submit" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-save me-1"></i> حفظ ملاحظات المتابعة
                                            </button>
                                        </div>
                                    </form>
                                @endif
                            </div>

                            <div class="section-separator"></div>

                            {{-- 2️⃣ صورة التصميم المختار --}}
                            <div class="mb-4 image-block" id="designImageBlock">
                                <div class="section-label">صورة التصميم المختارة</div>

                                @if ($designImagePath)
                                    @if ($designTitle)
                                        <p class="mb-2">
                                            <span class="badge bg-info text-dark">
                                                {{ $designTitle }}
                                            </span>
                                        </p>
                                    @endif

                                    <div class="design-image-wrapper position-relative text-center"
                                        style="display: inline-block; width: 100%;">
                                        <img src="{{ $designImagePath }}" class="unified-image mb-2"
                                            style="width:100%;max-width:350px;height:350px;object-fit:cover;object-position:center;border-radius:12px;display:block;margin:0 auto;"
                                            alt="صورة التصميم المختارة">

                                        @if($isAdmin || $isDesigner)
                                            <button type="button" class="btn btn-sm btn-light position-absolute shadow-sm"
                                                style="top: 10px; right: 50%; transform: translateX(165px); border-radius: 50%; width: 35px; height: 35px; padding: 0; z-index: 10;"
                                                title="تعديل"
                                                onclick="document.getElementById('direct_upload_book_design').click();">
                                                <i class="fas fa-pencil-alt text-primary"></i>
                                            </button>
                                        @endif
                                    </div>


                                    <!-- <div class="download-buttons-wrapper">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <button type="button"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            class="btn-download btn-download-all"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            id="downloadAllDesignImages">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <i class="fas fa-cloud-download-alt"></i>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            تحميل جميع الصور
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        </button>

                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <button type="button"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            class="btn-download btn-download-current"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            id="downloadCurrentDesignImage">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <i class="fas fa-download"></i>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            تحميل الصورة الحالية
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        </button>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    </div> -->
                                @else
                                    <p class="text-muted mb-0">لا يوجد تصميم محدّد لهذا الطلب.</p>
                                    @if($isAdmin || $isDesigner)
                                        <div class="mt-2">
                                            <button type="button" class="btn btn-sm btn-outline-primary"
                                                onclick="document.getElementById('direct_upload_book_design').click();">
                                                <i class="fas fa-plus"></i> إضافة تصميم
                                            </button>
                                        </div>
                                    @endif
                                @endif

                                <form id="directUploadForm_bookDesign"
                                    action="{{ route('orders.updateDesignImage', $order->id) }}" method="POST"
                                    enctype="multipart/form-data" style="display: none !important;">
                                    @csrf
                                    @method('PUT')
                                    <input type="file" id="direct_upload_book_design" name="design_image" accept="image/*"
                                        style="display: none !important;"
                                        onchange="document.getElementById('directUploadForm_bookDesign').submit();">
                                </form>
                            </div>

                            <div class="section-separator"></div>

                            {{-- 3️⃣ تصميم آخر --}}
                            <div class="mb-4 text-center image-block" id="anotherDesignBlock">
                                <strong class="d-block mb-2">
                                    تصميم آخر
                                    @if ($customDesignImages && $customDesignImages->isNotEmpty())
                                        (عدد: {{ $customDesignImages->count() }})
                                    @endif
                                </strong>

                                @if ($customDesignImages && $customDesignImages->isNotEmpty())
                                    {{-- ✅ تم حذف ה- div الزائد (another-carousel-wrap) ليتطابق مع الصور الخلفية --}}
                                    <div id="anotherDesignCarousel" class="carousel slide mb-3" data-bs-ride="false">

                                        <div class="carousel-inner text-center">
                                            @foreach ($customDesignImages as $index => $img)
                                                <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                                        <div class="image-wrapper-relative mx-auto">
                                                            @if($isAdmin || $isDesigner)
                                                                <button type="button" class="delete-image-btn"
                                                                    onclick="deleteOrderImage('custom_design_image_id', {{ $img->id }})"
                                                                    title="حذف الصورة">
                                                                    <i class="fas fa-trash-alt"></i>
                                                                </button>
                                                            @endif
                                                            <img src="{{ $img->resolved_url }}" class="unified-image mb-2"
                                                                style="width:100%;max-width:350px;height:350px;object-fit:cover;object-position:center;border-radius:12px;display:block;margin:0 auto;"
                                                                alt="تصميم آخر {{ $index + 1 }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                        <button class="carousel-control-prev custom-carousel-control" type="button"
                                            data-bs-target="#anotherDesignCarousel" data-bs-slide="prev">
                                            <span class="carousel-control-prev-icon"></span>
                                            <span class="visually-hidden">السابق</span>
                                        </button>

                                        <button class="carousel-control-next custom-carousel-control" type="button"
                                            data-bs-target="#anotherDesignCarousel" data-bs-slide="next">
                                            <span class="carousel-control-next-icon"></span>
                                            <span class="visually-hidden">التالي</span>
                                        </button>

                                    </div>

                                    <div class="download-buttons-wrapper">
                                        <button type="button" class="btn-download btn-download-all"
                                            id="downloadAllAnotherImages">
                                            <i class="fas fa-cloud-download-alt"></i>
                                            تحميل جميع الصور
                                        </button>

                                        <button type="button" class="btn-download btn-download-current"
                                            id="downloadCurrentAnotherImage">
                                            <i class="fas fa-download"></i>
                                            تحميل الصورة الحالية
                                        </button>
                                    </div>
                                @else
                                    <p class="text-muted">لا يوجد تصميم آخر.</p>
                                @endif
                            </div>

                            {{-- 4️⃣ الصورة الأمامية --}}
                            <div class="mb-4 image-block" id="frontImageBlock">
                                <strong class="d-block mb-2">الصورة الأمامية</strong>

                                @if ($frontSrc)
                                    <div class="image-wrapper-relative mx-auto">
                                        @if($isAdmin || $isDesigner)
                                            <button type="button" class="delete-image-btn"
                                                onclick="deleteOrderImage('front_image_id')" title="حذف الصورة">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        @endif
                                        <img src="{{ $frontSrc }}" class="unified-image mb-2"
                                            style="width:100%;max-width:350px;height:350px;object-fit:cover;object-position:center;border-radius:12px;display:block;margin:0 auto;"
                                            alt="الصورة الأمامية">
                                    </div>

                                    <div class="download-buttons-wrapper">


                                        <button type="button" class="btn-download btn-download-current"
                                            id="downloadCurrentFrontImage">
                                            <i class="fas fa-download"></i>
                                            تحميل الصورة الحالية
                                        </button>
                                    </div>
                                @else
                                    <p class="text-muted">لا توجد صورة أمامية.</p>
                                @endif
                            </div>

                            {{-- 5️⃣ الصور من الخلف --}}
                            <div class="mb-4 text-center">
                                <strong class="d-block mb-2">
                                    الصور من الخلف
                                    @if ($backImages && $backImages->isNotEmpty())
                                        (عدد: {{ $backImages->count() }})
                                    @endif
                                </strong>

                                @if ($backImages && $backImages->isNotEmpty())
                                    <div id="finalBackImagesCarousel" class="carousel slide mb-3" data-bs-ride="false">
                                        <div class="carousel-inner text-center">
                                            @foreach ($backImages as $index => $backImage)
                                                @php
                                                    $backSrc = resolveOrderImageUrl($backImage->image_path ?? null);
                                                @endphp

                                                @if ($backSrc)
                                                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                                        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                                            <div class="image-wrapper-relative mx-auto">
                                                                @if($isAdmin || $isDesigner)
                                                                    <button type="button" class="delete-image-btn"
                                                                        onclick="deleteOrderImage('back_image_ids', {{ $backImage->id }})"
                                                                        title="حذف الصورة">
                                                                        <i class="fas fa-trash-alt"></i>
                                                                    </button>
                                                                @endif
                                                                <img src="{{ $backSrc }}" class="unified-image mb-2"
                                                                    style="width:100%;max-width:350px;height:350px;object-fit:cover;object-position:center;border-radius:12px;display:block;margin:0 auto;"
                                                                    alt="الصورة الخلفية {{ $index + 1 }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>

                                        <button class="carousel-control-prev custom-carousel-control" type="button"
                                            data-bs-target="#finalBackImagesCarousel" data-bs-slide="prev">
                                            <span class="carousel-control-prev-icon"></span>
                                            <span class="visually-hidden">السابق</span>
                                        </button>

                                        <button class="carousel-control-next custom-carousel-control" type="button"
                                            data-bs-target="#finalBackImagesCarousel" data-bs-slide="next">
                                            <span class="carousel-control-next-icon"></span>
                                            <span class="visually-hidden">التالي</span>
                                        </button>
                                    </div>

                                    <div class="download-buttons-wrapper">
                                        <button type="button" class="btn-download btn-download-all"
                                            id="downloadAllFinalBackImages">
                                            <i class="fas fa-cloud-download-alt"></i> تحميل جميع الصور
                                        </button>
                                        <button type="button" class="btn-download btn-download-current"
                                            id="downloadCurrentFinalBackImage">
                                            <i class="fas fa-download"></i>
                                            تحميل الصورة الحالية
                                        </button>
                                    </div>
                                @else
                                    <p class="text-muted">لا توجد صور خلفية لهذا الطلب.</p>
                                @endif
                            </div>

                            <div class="section-separator"></div>

                            {{-- 6️⃣ عبارة الدفتر (SVG) --}}
                            <div class="mb-4">
                                <div class="section-label">عبارة الدفتر (ملف SVG)</div>

                                @if ($hasSvg)
                                    @if ($svgTitle)
                                        <p class="mb-2">
                                            <span class="badge bg-info text-dark">
                                                {{ $svgTitle }}
                                            </span>
                                        </p>
                                    @endif

                                    <div class="d-flex align-items-center svg-preview-container">
                                        <div class="img-fluids svg-preview mb-2" style="width: 80%; height: auto;">
                                            {!! $order->svg->svg_code !!}
                                        </div>

                                        <button type="button" class="btn btn-primary btn-sm me-3 copy-svg-button">
                                            <i class="fas fa-copy me-1"></i> نسخ SVG
                                        </button>
                                    </div>
                                @else
                                    <p class="text-muted mb-0">لا يوجد ملف SVG مرفق لهذا الطلب.</p>
                                @endif
                            </div>

                            <div class="section-separator"></div>

                            {{-- 7️⃣ ملاحظات المستخدم على التصميم --}}
                            <div class="mb-3">
                                <div class="section-label">ملاحظات المستخدم على التصميم</div>

                                @if ($order->note)
                                    <div class="note-box auto-dir" dir="auto" style="min-height: 80px;" @if (function_exists('detectLang')) lang="{{ detectLang($order->note) }}" @endif>
                                        {!! nl2br(e($order->note)) !!}
                                    </div>
                                @else
                                    <div class="note-box-light text-muted" style="min-height: 80px;">
                                        لا توجد ملاحظات مضافة من المستخدم.
                                    </div>
                                @endif
                            </div>

                        </div>
                    </div>
                </div>

                {{-- ====================== نهاية التبويبات ====================== --}}
            </div>
        </div>
    </div>

    @if($isAdmin || $isDesigner)
        {{-- Modal 1: تفاصيل الطلب --}}
        <div class="modal fade" id="editOrderDetailsModal" tabindex="-1" aria-hidden="true"
            style="direction: rtl; text-align: right;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('orders.updateCoreData', $order->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title">تعديل تفاصيل الطلب</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                                style="margin: 0 auto 0 0;"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">نوع المنتج (Book Type)</label>
                                <select name="book_type_id" class="form-select">
                                    <option value="">اختر المنتج</option>
                                    @foreach($bookTypes as $bt)
                                        <option value="{{ $bt->id }}" {{ $order->book_type_id == $bt->id ? 'selected' : '' }}>
                                            {{ $bt->name_ar }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">الجندر</label>
                                <select name="user_gender" class="form-select">
                                    <option value="">اختر الجندر</option>
                                    <option value="male" {{ $order->user_gender == 'male' ? 'selected' : '' }}>ذكر</option>
                                    <option value="female" {{ $order->user_gender == 'female' ? 'selected' : '' }}>أنثى</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">السعر الشامل</label>
                                <input type="number" step="0.01" name="final_price" class="form-control"
                                    value="{{ $order->final_price ?? '' }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">السعر شامل كود الخصم</label>
                                <input type="number" step="0.01" name="final_price_with_discount" class="form-control"
                                    value="{{ $order->final_price_with_discount ?? '' }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">كود الخصم</label>
                                <select name="discount_code_id" class="form-select">
                                    <option value="">بدون كود خصم</option>
                                    @foreach($discountCodes as $dc)
                                        <option value="{{ $dc->id }}" {{ $order->discount_code_id == $dc->id ? 'selected' : '' }}>
                                            {{ $dc->discount_code }} @if($dc->code_name) ({{ $dc->code_name }}) @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" name="is_with_additives"
                                    id="modal_is_with_additives" value="1" {{ $order->is_with_additives ? 'checked' : '' }}>
                                <label class="form-check-label" for="modal_is_with_additives">مع إضافات</label>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                            <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Modal 2: معلومات الخريج --}}
        <div class="modal fade" id="editGraduateInfoModal" tabindex="-1" aria-hidden="true"
            style="direction: rtl; text-align: right;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('orders.updateGraduateInfo', $order->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title">تعديل معلومات الخريج</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                                style="margin: 0 auto 0 0;"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">الاسم (عربي)</label>
                                <input type="text" name="username_ar" class="form-control" value="{{ $order->username_ar }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">الاسم (إنجليزي)</label>
                                <input type="text" name="username_en" class="form-control" value="{{ $order->username_en }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">رقم اعتماد التصميم</label>
                                <input type="text" name="user_phone_number" class="form-control"
                                    value="{{ $order->user_phone_number }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">الجامعة</label>
                                <select name="university_id" class="form-select mb-2" id="modalUniversitySelect">
                                    <option value="">اختر الجامعة</option>
                                    @foreach($universities as $uni)
                                        <option value="{{ $uni->id }}" {{ $order->university_id == $uni->id ? 'selected' : '' }}
                                            data-majors='@json($uni->majors->map(fn($m) => ["id" => $m->id, "name" => $m->name]))'>
                                            {{ $uni->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">تخصص الجامعة</label>
                                <select name="university_major_id" class="form-select" id="modalUniversityMajorSelect">
                                    <option value="">اختر التخصص</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">الدبلوم</label>
                                <select name="diploma_id" class="form-select mb-2" id="modalDiplomaSelect">
                                    <option value="">اختر الدبلوم</option>
                                    @foreach($diplomas as $dip)
                                        <option value="{{ $dip->id }}" {{ $order->diploma_id == $dip->id ? 'selected' : '' }}
                                            data-majors='@json($dip->majors->map(fn($m) => ["id" => $m->id, "name" => $m->name]))'>
                                            {{ $dip->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">تخصص الدبلوم</label>
                                <select name="diploma_major_id" class="form-select" id="modalDiplomaMajorSelect">
                                    <option value="">اختر التخصص</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">ملاحظات المستخدم على التصميم</label>
                                <textarea name="note" class="form-control" rows="3">{{ $order->note }}</textarea>
                            </div>

                            <hr>
                            <h6 class="fw-bold mb-3">صور الخريج</h6>

                            <div class="mb-3">
                                <label class="form-label">استبدال الصورة الأمامية</label>
                                <input type="file" name="front_image" class="form-control" accept="image/*">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">إضافة صور خلفية جديدة</label>
                                <input type="file" name="back_images[]" class="form-control" accept="image/*" multiple>
                                <small class="text-muted">يمكنك اختيار أكثر من صورة. الصور الحالية لن تُحذف.</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">إضافة تصميم آخر (صور مخصصة)</label>
                                <input type="file" name="custom_design_images[]" class="form-control" accept="image/*" multiple>
                                <small class="text-muted">يمكنك اختيار أكثر من صورة. الصور الحالية لن تُحذف.</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                            <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Modal 3: الدفتر من الداخل --}}
        <div class="modal fade" id="editInternalBookModal" tabindex="-1" aria-hidden="true"
            style="direction: rtl; text-align: right;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('orders.updateInternalBook', $order->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title">تعديل الدفتر من الداخل</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                                style="margin: 0 auto 0 0;"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">نوع الإهداء</label>
                                <select name="gift_type" class="form-select">
                                    <option value="none" {{ $order->gift_type == 'none' ? 'selected' : '' }}>بدون إهداء</option>
                                    <option value="default" {{ $order->gift_type == 'default' ? 'selected' : '' }}>إهداء موحّد
                                    </option>
                                    <option value="custom" {{ $order->gift_type == 'custom' ? 'selected' : '' }}>إهداء مخصّص
                                    </option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">نص العبارة / الإهداء المخصص</label>
                                <textarea name="gift_title" class="form-control" rows="3">{{ $order->gift_title }}</textarea>
                            </div>
                            <hr>
                            <div class="mb-3">
                                <label class="form-label">الصور الداخلية</label>
                                <input type="file" name="internal_images[]" class="form-control" accept="image/*" multiple>
                                <small class="text-muted">يمكنك اختيار أكثر من صورة. الصور الجديدة ستضاف للصور الحالية.</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">الطباعة الشفافة</label>
                                <input type="file" name="transparent_image" class="form-control" accept="image/*">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">صورة الزخرفة</label>
                                <input type="file" name="decoration_image" class="form-control" accept="image/*">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                            <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Modal 4: تجليد الدفتر --}}
        <div class="modal fade" id="editBindingModal" tabindex="-1" aria-hidden="true"
            style="direction: rtl; text-align: right;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('orders.updateBindingTab', $order->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title">تعديل التجليد</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                                style="margin: 0 auto 0 0;"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">زخرفة الكتاب</label>
                                <select name="book_decorations_id" class="form-select">
                                    <option value="">بدون زخرفة</option>
                                    @foreach($decorations as $dec)
                                        <option value="{{ $dec->id }}" {{ $order->book_decorations_id == $dec->id ? 'selected' : '' }}>{{ $dec->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">عدد الورق</label>
                                <input type="number" name="pages_number" class="form-control"
                                    value="{{ $order->pages_number }}">
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" name="is_sponge" id="modal_is_sponge" value="1"
                                    {{ $order->is_sponge ? 'checked' : '' }}>
                                <label class="form-check-label" for="modal_is_sponge">إسفنج</label>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">تحميل ملف SVG للطالب</label>
                                <input type="file" name="new_svg" class="form-control" accept=".svg,.txt">
                                <small class="text-muted">لرفع كود SVG للاسم.</small>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                            <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Modal 5: معلومات التوصيل --}}
        <div class="modal fade" id="editDeliveryModal" tabindex="-1" aria-hidden="true"
            style="direction: rtl; text-align: right;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('orders.updateDeliveryInfo', $order->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title">تعديل معلومات التوصيل</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                                style="margin: 0 auto 0 0;"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">رقم 1</label>
                                <input type="text" name="delivery_number_one" class="form-control"
                                    value="{{ $order->delivery_number_one }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">رقم 2</label>
                                <input type="text" name="delivery_number_two" class="form-control"
                                    value="{{ $order->delivery_number_two }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">المحافظة <span class="text-danger">*</span></label>
                                <select name="governorate_id" id="admin_gov_select" class="form-select" required>
                                    <option value="">اختر المحافظة...</option>
                                    @foreach($governorates as $gov)
                                        <option value="{{ $gov->id }}" {{ $order->governorate_id == $gov->id ? 'selected' : '' }}>
                                            {{ $gov->name_ar ?: $gov->name_en }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">المدينة <span class="text-danger">*</span></label>
                                <select name="city_id" id="admin_city_select" class="form-select" required>
                                    <option value="">اختر المدينة...</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">المنطقة (القرية) <span class="text-danger">*</span></label>
                                <select name="area_id" id="admin_area_select" class="form-select" required>
                                    <option value="">اختر المنطقة...</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">تفاصيل العنوان (الشارع، البناية، إلخ) <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="address" class="form-control" value="{{ $order->address }}" required>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                            <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
    {{-- Modal التنبيه الخاص بخصم المجموعة --}}
    {{-- Modal التنبيه الإبداعي لخصم المجموعة --}}
    @if(isset($groupWarning))
        <div class="modal fade" id="groupWarningModal" tabindex="-1" aria-hidden="true"
            style="direction: rtl; text-align: right;">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">

                    {{-- هيدر المودال بلون خفيف --}}
                    {{-- هيدر المودال بلون خفيف --}}
                            <div class="modal-header border-0 pb-0 pt-4 px-4 position-relative d-flex justify-content-between align-items-start">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                                        style="width: 50px; height: 50px; background-color: rgba(220, 53, 69, 0.1);">
                                        <i class="fas fa-exclamation-triangle fa-lg text-danger"></i>
                                    </div>
                                    <div>
                                        <h5 class="modal-title fw-bold text-dark mb-1">المجموعة لم تكتمل!</h5>
                                        <p class="text-muted small mb-0">يرجى الانتباه لعدد الطلبات في هذه الخطة</p>
                                    </div>
                                </div>
                                <button type="button" class="btn-close m-0" data-bs-dismiss="modal"
                                    aria-label="Close" style="opacity: 0.5;"></button>
                            </div>

                            <div class="modal-body px-4 py-4">

                                {{-- معلومات الخطة --}}
                                <div class="p-3 mb-4 rounded-3" style="background: #f8f9fa; border: 1px solid #e9ecef;">
                                    <div class="d-flex align-items-center mb-1">
                                        <i class="fas fa-gem text-primary me-2"></i>
                                        <span class="text-secondary fw-semibold">الخطة المُطبَّقة:</span>
                                    </div>
                                    <div class="fs-5 fw-bold text-dark ms-4 pl-2">{{ $groupWarning['applied_plan'] }}</div>
                                </div>

                                {{-- شريط التقدم والأرقام --}}
                                <div class="mb-4">
                                    <div class="d-flex justify-content-between align-items-end mb-2">
                                        <div>
                                            <span class="text-muted fw-bold d-block" style="font-size: 0.85rem;">التقدم الحالي</span>
                                            <strong class="fs-4 text-danger">{{ $groupWarning['current_count'] }}</strong>
                                            <span class="text-muted mx-1">من</span>
                                            <strong class="fs-5 text-dark">{{ $groupWarning['required_count'] }}</strong>
                                            <span class="text-muted small">شخص</span>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge rounded-pill bg-danger-subtle text-danger fw-bold px-3 py-2">
                                                ناقص {{ $groupWarning['required_count'] - $groupWarning['current_count'] }} أشخاص
                                            </span>
                                        </div>
                                    </div>

                                    {{-- شريط التقدم الديناميكي (بحسب النسبة مئوية لحاله!) --}}
                                    <div class="progress" style="height: 10px; border-radius: 10px; background-color: #ffe6e6;">
                                        <div class="progress-bar bg-danger progress-bar-striped progress-bar-animated"
                                            role="progressbar"
                                            style="width: {{ ($groupWarning['current_count'] / $groupWarning['required_count']) * 100 }}%;"
                                            aria-valuenow="{{ $groupWarning['current_count'] }}" aria-valuemin="0"
                                            aria-valuemax="{{ $groupWarning['required_count'] }}">
                                        </div>
                                    </div>
                                </div>

                                {{-- قسم السعر --}}
                                <div class="d-flex align-items-center justify-content-between p-3 rounded-4"
                                    style="background: linear-gradient(135deg, #fff3cd 0%, #ffecb3 100%); border: 1px solid rgba(255, 193, 7, 0.3);">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center me-2"
                                            style="width: 35px; height: 35px; background-color: rgba(255, 255, 255, 0.5);">
                                            <i class="fas fa-money-bill-wave text-warning"
                                                style="filter: drop-shadow(0px 2px 2px rgba(0,0,0,0.1));"></i>
                                        </div>
                                        <span class="fw-bold text-dark">السعر المُطبَّق</span>
                                    </div>
                                    <h4 class="mb-0 fw-black text-dark" style="letter-spacing: -0.5px;">
                                        {{ $groupWarning['applied_price'] }} <span class="fs-6 text-muted">JOD</span></h4>
                                </div>

                            </div>

                            {{-- الفوتر --}}
                            <div class="modal-footer border-0 px-4 pb-4 pt-0 text-start w-100 d-block">
                                <button type="button" class="btn btn-light w-100 fw-bold py-2 rounded-3 text-secondary"
                                    data-bs-dismiss="modal" style="background-color: #f1f3f5;">إغلاق التنبيه</button>
                            </div>
                        </div>
                    </div>
                </div>
    @endif
        <script>
            window.orderShowConfig = {
                csrfToken: '{{ csrf_token() }}',
                updateStatusUrl: '{{ route('orders.updateStatus') }}',
                updateDesignerUrl: '{{ route('orders.updateDesigner') }}',
                currentUniversityMajorId: {{ $order->university_major_id ?? 'null' }},
                currentDiplomaMajorId: {{ $order->diploma_major_id ?? 'null' }},
            };

        </script>
        <script src="{{ asset('js/order-show.js') }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {

                // 1. قراءة الـ Hash من الرابط لفتح التبويب مباشرة
                var hash = window.location.hash;
                if (hash) {
                    var targetTabButton = document.querySelector('.order-tabs button[data-bs-target="' + hash + '"]');
                    if (targetTabButton) {
                        var tab = new bootstrap.Tab(targetTabButton);
                        tab.show();
                        setTimeout(() => {
                            document.querySelector('.order-tabs').scrollIntoView({ behavior: 'smooth', block: 'start' });
                        }, 300);
                    }
                }

                // 2. تحسين السحب (Drag) لبرامج مثل الـ Word التي تقبل كود الـ HTML المدمج
                document.addEventListener('dragstart', async function (e) {
                    if (e.target && e.target.classList.contains('drag-to-ps')) {
                        const imgUrl = e.target.src;
                        e.dataTransfer.setData('text/plain', imgUrl);

                        try {
                            // سحب كود الـ SVG ووضعه كـ HTML في الحافظة أثناء السحب ليفهمه Word
                            fetch(imgUrl).then(res => res.text()).then(svgText => {
                                e.dataTransfer.setData('text/html', svgText);
                            });
                        } catch (err) { }
                    }
                });

                // 3. السر الأعظم: كود "نسخ للفوتوشوب"
                const copyBtns = document.querySelectorAll('.copy-for-ps-btn');
                copyBtns.forEach(btn => {
                    btn.addEventListener('click', async function () {
                        const originalHtml = this.innerHTML;
                        this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> جاري النسخ...';
                        this.classList.add('disabled');

                        const svgUrl = this.getAttribute('data-url');

                        try {
                            // جلب محتوى الـ SVG
                            const response = await fetch(svgUrl);
                            const svgText = await response.text();

                            // تحويله لـ Blob
                            const svgBlob = new Blob([svgText], { type: 'image/svg+xml;charset=utf-8' });
                            const DOMURL = window.URL || window.webkitURL || window;
                            const url = DOMURL.createObjectURL(svgBlob);

                            // رسمه على Canvas بدقة 2000x2000 بكسل عشان الفوتوشوب
                            const img = new Image();
                            img.onload = function () {
                                const canvas = document.createElement('canvas');
                                canvas.width = 2000;
                                canvas.height = 2000;
                                const ctx = canvas.getContext('2d');

                                // رسم الصورة
                                ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
                                DOMURL.revokeObjectURL(url);

                                // تحويل الـ Canvas لـ PNG ونسخه لـ Clipboard
                                canvas.toBlob(async function (blob) {
                                    try {
                                        const item = new ClipboardItem({ 'image/png': blob });
                                        await navigator.clipboard.write([item]);

                                        // نجاح
                                        btn.innerHTML = '<i class="fas fa-check me-2"></i> تم النسخ بنجاح!';
                                        btn.classList.replace('btn-dark', 'btn-success');
                                    } catch (clipboardErr) {
                                        console.error('Clipboard error:', clipboardErr);
                                        btn.innerHTML = '<i class="fas fa-times me-2"></i> فشل (راجع صلاحيات المتصفح)';
                                        btn.classList.replace('btn-dark', 'btn-danger');
                                    }

                                    // إرجاع الزر لشكله الطبيعي
                                    btn.classList.remove('disabled');
                                    setTimeout(() => {
                                        btn.innerHTML = originalHtml;
                                        btn.classList.remove('btn-success', 'btn-danger');
                                        btn.classList.add('btn-dark');
                                    }, 3000);

                                }, 'image/png');
                            };
                            img.src = url;

                        } catch (err) {
                            console.error('Fetch error:', err);
                            this.innerHTML = '<i class="fas fa-times me-2"></i> خطأ بالنسخ';
                            this.classList.replace('btn-dark', 'btn-danger');
                            this.classList.remove('disabled');
                            setTimeout(() => {
                                this.innerHTML = originalHtml;
                                this.classList.replace('btn-danger', 'btn-dark');
                            }, 3000);
                        }
                    });
                });

            });

        </script>
        <script>
            function deleteOrderImage(fieldName, imageId = null, filePath = null) {
                Swal.fire({
                    title: 'هل أنت متأكد؟',
                    text: "سيتم حذف هذه الصورة نهائياً ولن تتمكن من التراجع!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'نعم، احذفها!',
                    cancelButtonText: 'إلغاء'
                }).then((result) => {
                    if (result.isConfirmed) {

                        Swal.fire({
                            title: 'جاري الحذف...',
                            allowOutsideClick: false,
                            didOpen: () => { Swal.showLoading(); }
                        });

                        fetch(`/orders/{{ $order->id }}/delete-image`, {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                field_name: fieldName,
                                image_id: imageId,
                                file_path: filePath
                            })
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire({
                                        icon: 'success', title: 'تم الحذف!', text: data.message, timer: 1500, showConfirmButton: false
                                    }).then(() => window.location.reload());
                                } else {
                                    Swal.fire('خطأ!', data.message, 'error');
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                Swal.fire('خطأ!', 'حدث مشكلة في الاتصال بالسيرفر.', 'error');
                            });
                    }
                });
            }

        </script>
        <script>

            document.addEventListener('DOMContentLoaded', function () {
                const govSelect = document.getElementById('admin_gov_select');
                const citySelect = document.getElementById('admin_city_select');
                const areaSelect = document.getElementById('admin_area_select');

                // 🔴 الحل هون: عرفنا الرابط الأساسي بشكل آمن جداً
                const baseUrl = "{{ url('/') }}";

                if (govSelect && citySelect && areaSelect) {
                    const currentCityId = '{{ $order->city_id }}';
                    const currentAreaId = '{{ $order->area_id }}';

                    function fetchCities(govId, selectedCityId = null) {
                        if (!govId) {
                            citySelect.innerHTML = '<option value="">اختر المدينة...</option>';
                            areaSelect.innerHTML = '<option value="">اختر المنطقة...</option>';
                            return;
                        }

                        // استخدمنا الـ baseUrl مع مسار الـ API
                        fetch(`${baseUrl}/api/v1/locations/cities/${govId}`)
                            .then(res => res.json())
                            .then(data => {
                                let options = '<option value="">اختر المدينة...</option>';
                                data.data.forEach(city => {
                                    let selected = (selectedCityId == city.id) ? 'selected' : '';
                                    options += `<option value="${city.id}" ${selected}>${city.name_ar || city.name_en}</option>`;
                                });
                                citySelect.innerHTML = options;

                                if (selectedCityId) {
                                    fetchAreas(selectedCityId, currentAreaId);
                                } else {
                                    areaSelect.innerHTML = '<option value="">اختر المنطقة...</option>';
                                }
                            })
                            .catch(error => console.error('Error fetching cities:', error));
                    }

                    function fetchAreas(cityId, selectedAreaId = null) {
                        if (!cityId) {
                            areaSelect.innerHTML = '<option value="">اختر المنطقة...</option>';
                            return;
                        }

                        // استخدمنا الـ baseUrl مع مسار الـ API
                        fetch(`${baseUrl}/api/v1/locations/areas/${cityId}`)
                            .then(res => res.json())
                            .then(data => {
                                let options = '<option value="">اختر المنطقة...</option>';
                                data.data.forEach(area => {
                                    let selected = (selectedAreaId == area.id) ? 'selected' : '';
                                    options += `<option value="${area.id}" ${selected}>${area.name_ar || area.name_en}</option>`;
                                });
                                areaSelect.innerHTML = options;
                            })
                            .catch(error => console.error('Error fetching areas:', error));
                    }

                    govSelect.addEventListener('change', function () {
                        fetchCities(this.value);
                    });

                    citySelect.addEventListener('change', function () {
                        fetchAreas(this.value);
                    });

                    if (govSelect.value) {
                        fetchCities(govSelect.value, currentCityId);
                    }
                }
            });
        </script>
@endsection