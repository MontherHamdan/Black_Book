@extends('admin.layout')

@push('styles')
<link href="{{ asset('css/custome.css') }}" rel="stylesheet" type="text/css" />
@endpush

@section('content')
<div class="container order-show-page">

    @php
    use Illuminate\Support\Str;

    // 🔹 كشف لغة النص (عربي / إنجليزي) لاختيار dir/lang صح
    if (! function_exists('detectLang')) {
    function detectLang($text) {
    return preg_match('/\p{Arabic}/u', $text) ? 'ar' : 'en';
    }
    }

    // 🔹 دالة مساعدة لتهيئة مسار الصور (نفس منطق الكونترولر)
    if (! function_exists('resolveOrderImageUrl')) {
    function resolveOrderImageUrl(?string $path): ?string {
    if (! $path) {
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
                <div class="order-header-chip order-header-chip-muted">
                    <div class="order-header-main">
                        <div class="order-header-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="order-header-body">
                            <span class="order-header-label">المجموعة</span>
                            <span class="order-header-value">
                                @if ($groupNameHeader)
                                {{ $groupNameHeader }}
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
                            <select
                                class="order-status-select js-order-status-select"
                                data-order-id="{{ $order->id }}">
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
                            <select
                                class="order-status-select js-designer-select"
                                data-order-id="{{ $order->id }}">
                                <option value="">غير معيّن</option>
                                @foreach ($designers as $designer)
                                <option
                                    value="{{ $designer->id }}"
                                    {{ (int) $order->designer_id === (int) $designer->id ? 'selected' : '' }}>
                                    {{ $designer->name }}
                                </option>
                                @endforeach
                            </select>
                            @elseif ($authUser->isDesigner())
                            @if (! $order->designer_id)
                            <button
                                type="button"
                                class="btn btn-outline-primary btn-xs js-assign-me-btn"
                                data-order-id="{{ $order->id }}"
                                data-designer-id="{{ $authUser->id }}">
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
                <button class="nav-link" id="tab-binding-tab" data-bs-toggle="tab"
                    data-bs-target="#tab-binding" type="button" role="tab">
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
                    <div class="card-header">
                        <div class="order-card-header-title">
                            <div class="order-card-header-icon">
                                <i class="fas fa-receipt"></i>
                            </div>
                            <span>تفاصيل الطلب</span>
                        </div>
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
                                <img class="img-fluid img-thumbnail"
                                    src="{{ $order->bookType->image }}"
                                    alt="صورة التصميم"
                                    style="max-width: 260px; height: auto;">
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
            <div class="tab-pane fade" id="tab-internal-book" role="tabpanel"
                aria-labelledby="tab-internal-book-tab">

                <div class="card order-card mb-4" style="direction: rtl; text-align: right;">
                    <div class="card-header">
                        <div class="order-card-header-title">
                            <div class="order-card-header-icon">
                                <i class="fas fa-book-open"></i>
                            </div>
                            <span>الدفتر من الداخل</span>
                        </div>
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
                                        <img src="{{ $src }}"
                                            class="d-block mx-auto img-fluid img-thumbnail"
                                            style="max-width: 260px;"
                                            alt="الصورة الداخلية {{ $index + 1 }}">
                                    </div>
                                    @endif
                                    @endforeach
                                </div>

                                <button class="carousel-control-prev custom-carousel-control"
                                    type="button"
                                    data-bs-target="#internalImagesCarousel"
                                    data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon"></span>
                                    <span class="visually-hidden">السابق</span>
                                </button>

                                <button class="carousel-control-next custom-carousel-control"
                                    type="button"
                                    data-bs-target="#internalImagesCarousel"
                                    data-bs-slide="next">
                                    <span class="carousel-control-next-icon"></span>
                                    <span class="visually-hidden">التالي</span>
                                </button>
                            </div>

                            {{-- أزرار التحميل --}}
                            <div class="download-buttons-wrapper">
                                <button type="button"
                                    class="btn-download btn-download-all"
                                    id="downloadAllInternalImages">
                                    <i class="fas fa-cloud-download-alt"></i>
                                    تحميل جميع الصور
                                </button>

                                <button type="button"
                                    class="btn-download btn-download-current"
                                    id="downloadCurrentInternalImage">
                                    <i class="fas fa-download"></i>
                                    تحميل الصورة الحالية
                                </button>
                            </div>
                            @else
                            <p class="text-muted">لا توجد صور داخلية.</p>
                            @endif
                        </div>

                        {{-- 🔸 الطباعة الشفافة --}}
                        <div class="mb-4 text-center" id="transparentImageBlock">
                            <strong class="d-block mb-2">الصورة الشفافة</strong>

                            @if ($transparentImage)
                            <img src="{{ $transparentImage }}"
                                class="img-fluid img-thumbnail"
                                style="max-width: 260px;"
                                alt="الطباعة الشفافة">

                            <div class="download-buttons-wrapper">
                                <!-- <button type="button"
                                    class="btn-download btn-download-all"
                                    id="downloadAllTransparentImages">
                                    <i class="fas fa-cloud-download-alt"></i>
                                    تحميل جميع الصور
                                </button> -->

                                <button type="button"
                                    class="btn-download btn-download-current"
                                    id="downloadCurrentTransparentImage">
                                    <i class="fas fa-download"></i>
                                    تحميل الصورة الحالية
                                </button>
                            </div>
                            @else
                            <p class="text-muted">لا توجد صورة للطباعة الشفافة.</p>
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
                            <img src="{{ $decorationImage }}"
                                class="img-fluid img-thumbnail"
                                style="max-width: 260px;"
                                alt="صورة الزخرفة">

                            <div class="download-buttons-wrapper">
                                <!-- <button type="button"
                                    class="btn-download btn-download-all"
                                    id="downloadAllDecorationImages">
                                    <i class="fas fa-cloud-download-alt"></i>
                                    تحميل جميع الصور
                                </button> -->

                                <button type="button"
                                    class="btn-download btn-download-current"
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

                                @if (! empty($giftTitleInternal))
                                <div class="note-box auto-dir mt-2"
                                    lang="{{ detectLang($giftTitleInternal) }}">
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
                    </div>
                </div>
            </div>

            {{-- ====================== تبويب: تجليد الدفتر ====================== --}}
            <div class="tab-pane fade" id="tab-binding" role="tabpanel"
                aria-labelledby="tab-binding-tab">
                <div class="card order-card mb-4 binding-card" style="direction: rtl; text-align: right;">
                    <div class="card-header">
                        <div class="order-card-header-title">
                            <div class="order-card-header-icon">
                                <i class="fas fa-layer-group"></i>
                            </div>
                            <span>تجليد الدفتر</span>
                        </div>
                    </div>

                    <div class="card-body">
                        <form id="bindingUpdateForm"
                            class="js-binding-followup-form"
                            action="{{ route('orders.updateBinding', $order->id) }}"
                            method="POST"
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
                                            <input class="form-check-input" type="checkbox" disabled
                                                {{ $internalImagesCountBinding > 0 ? 'checked' : '' }}>
                                            <label class="form-check-label">
                                                صور داخلية
                                                @if ($internalImagesCountBinding > 0)
                                                (عدد: {{ $internalImagesCountBinding }})
                                                @endif
                                            </label>
                                        </div>

                                        {{-- زخرفة --}}
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" disabled
                                                {{ $order->bookDecoration ? 'checked' : '' }}>
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
                                            <input class="form-check-input" type="checkbox" disabled
                                                {{ $order->transparentPrinting ? 'checked' : '' }}>
                                            <label class="form-check-label">
                                                طباعة شفافة
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                {{-- 🔹 الطباعة الشفافة داخل تجليد الدفتر --}}
                                <div class="col-md-5 mb-3">
                                    <div class="section-label">الطباعة الشفافة داخل التجليد</div>

                                    <div class="mb-4 text-center" id="bindingTransparentImageBlock">
                                        @if ($transparentImagePath)
                                        <strong class="d-block mb-2">الصورة الشفافة</strong>

                                        <img src="{{ $transparentImagePath }}"
                                            class="img-fluid img-thumbnail mb-2"
                                            style="max-width: 260px;"
                                            alt="الطباعة الشفافة">
                                        <!-- 
                                        <div class="download-buttons-wrapper">
                                            <button type="button"
                                                class="btn-download btn-download-all"
                                                id="downloadAllBindingTransparentImages">
                                                <i class="fas fa-cloud-download-alt"></i>
                                                تحميل جميع الصور
                                            </button>

                                            <button type="button"
                                                class="btn-download btn-download-current"
                                                id="downloadCurrentBindingTransparentImage">
                                                <i class="fas fa-download"></i>
                                                تحميل الصورة الحالية
                                            </button>
                                        </div> -->
                                        @else
                                        <p class="text-muted mb-0">لا توجد صورة للطباعة الشفافة.</p>
                                        @endif
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
                                        <img src="{{ $srcBinding }}"
                                            class="img-fluid img-thumbnail"
                                            style="max-width: 140px; height: 110px; object-fit: cover;"
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
                                <img src="{{ $order->bookDecoration->image }}"
                                    class="img-fluid img-thumbnail"
                                    style="max-width: 220px;"
                                    alt="صورة الزخرفة">
                                @else
                                <p class="text-muted mb-0">لا توجد صورة للزخرفة.</p>
                                @endif
                                @else
                                <p class="text-muted mb-0">لا توجد زخرفة محددة.</p>
                                @endif
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

                                    @elseif ($giftTypeBinding === 'custom' && ! empty($giftTitleBinding))
                                    @php
                                    $isGiftImage = Str::startsWith($giftTitleBinding, [
                                    'http://', 'https://', '/storage',
                                    ]);

                                    if ($isGiftImage) {
                                    $giftSrc = Str::startsWith($giftTitleBinding, ['http://', 'https://'])
                                    ? $giftTitleBinding
                                    : asset(ltrim($giftTitleBinding, '/'));
                                    }
                                    @endphp

                                    @if ($isGiftImage ?? false)
                                    <img src="{{ $giftSrc }}"
                                        alt="العبارة المخصصة"
                                        class="img-fluid img-thumbnail"
                                        style="max-width: 220px;">
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

                                <div class="note-box auto-dir mt-2"
                                    dir="auto"
                                    style="cursor: default;">
                                    <div id="binding-followup-box">
                                        @if ($bindingFollowupText)
                                        <div>{!! nl2br(e($bindingFollowupText)) !!}</div>
                                        @else
                                        <span class="text-muted">لا توجد ملاحظات حتى الآن.</span>
                                        @endif
                                    </div>
                                </div>

                                @if ($canAddNote)
                                <textarea
                                    name="binding_followup_note"
                                    class="form-control mt-2 auto-dir"
                                    dir="auto"
                                    rows="2"
                                    placeholder="اكتب ملاحظة جديدة على التجليد هنا...">{{ old('binding_followup_note', $bindingFollowupText) }}</textarea>
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
                    </div>
                </div>
            </div>

            {{-- ====================== تبويب: معلومات التوصيل ====================== --}}
            <div class="tab-pane fade" id="tab-delivery-info" role="tabpanel"
                aria-labelledby="tab-delivery-info-tab">
                <div class="card order-card mb-4" style="direction: rtl; text-align: right;">
                    <div class="card-header">
                        <div class="order-card-header-title">
                            <div class="order-card-header-icon">
                                <i class="fas fa-truck"></i>
                            </div>
                            <span>معلومات التوصيل</span>
                        </div>
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
                            <span>{{ $order->governorate ?? 'غير متوفر' }}</span>
                        </div>

                        <div class="info-row">
                            <strong>المنطقة:</strong>
                            <span>{{ $order->address ?? 'غير متوفر' }}</span>
                        </div>

                        <div class="info-row">
                            <strong>السعر:</strong>
                            <span>
                                @if (! is_null($order->final_price_with_discount))
                                {{ $order->final_price_with_discount }}
                                @elseif (! is_null($order->final_price))
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
                            <form action="{{ route('orders.updateDeliveryFollowup', $order->id) }}"
                                method="POST"
                                class="mt-2 js-delivery-followup-form">
                                @csrf
                                @method('PUT')

                                <textarea
                                    name="delivery_followup_note"
                                    class="form-control auto-dir"
                                    dir="auto"
                                    rows="3"
                                    placeholder="اكتب ملاحظات المتابعة على التوصيل هنا...">{{ old('delivery_followup_note', $deliveryFollowupText) }}</textarea>

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
            <div class="tab-pane fade" id="tab-graduate-info" role="tabpanel"
                aria-labelledby="tab-graduate-info-tab">

                <div class="card order-card mb-4" style="direction: rtl; text-align: right;">
                    <div class="card-header">
                        <div class="order-card-header-title">
                            <div class="order-card-header-icon">
                                <i class="fas fa-user-graduate"></i>
                            </div>
                            <span>معلومات الخريج</span>
                        </div>
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
                                    @if (! empty($order->user_phone_number))
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $order->user_phone_number) }}"
                                        target="_blank"
                                        class="ms-2"
                                        style="color: #25D366; font-size: 22px;">
                                        <i class="fab fa-whatsapp"></i>
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- الاسم عربي + زر نسخ SVG للاسم --}}
                        <p>
                            <strong>الاسم (عربي):</strong> {{ $order->username_ar }}

                            @if ($svgCodeForName)
                            <button type="button"
                                class="btn btn-sm btn-outline-primary ms-2 copy-name-svg-btn"
                                data-svg="{{ htmlspecialchars($svgCodeForName, ENT_QUOTES, 'UTF-8') }}">
                                <i class="fas fa-copy me-1"></i> نسخ SVG للاسم
                            </button>
                            @else
                            <span class="badge bg-warning ms-2">SVG للاسم غير مضاف بعد</span>
                            @endif
                        </p>

                        {{-- الاسم إنجليزي --}}
                        <p>
                            <strong>الاسم (إنجليزي):</strong> {{ $order->username_en ?? 'غير متوفر' }}
                        </p>

                        {{-- الجامعة --}}
                        <p>
                            <strong>الجامعة:</strong> {{ $order->school_name ?? 'غير متوفر' }}
                        </p>

                        {{-- التخصص --}}
                        <p>
                            <strong>التخصص:</strong> {{ $order->major_name ?? 'غير متوفر' }}
                        </p>

                        <div class="section-separator"></div>

                        {{-- 1️⃣ ملاحظات المتابعة على التصميم --}}
                        <div class="mb-3">
                            <div class="section-label">ملاحظات المتابعة على التصميم</div>

                            <div class="note-box auto-dir mb-2"
                                dir="auto"
                                style="min-height: 80px; cursor: default;">
                                <div id="design-followup-box">
                                    @if ($designFollowupText)
                                    <div>{!! nl2br(e($designFollowupText)) !!}</div>
                                    @else
                                    <span class="text-muted">لا توجد ملاحظات متابعة حتى الآن.</span>
                                    @endif
                                </div>
                            </div>

                            @if ($canEditDesignFollowup)
                            <form action="{{ route('orders.updateDesignFollowup', $order->id) }}"
                                method="POST"
                                class="mt-2 js-design-followup-form">
                                @csrf
                                @method('PUT')

         <textarea
    name="design_followup_note"
    class="form-control auto-dir"
    dir="auto"
    rows="3"
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

                            <div class="design-image-wrapper">
                                <img src="{{ $designImagePath }}"
                                    class="design-image-full"
                                    alt="صورة التصميم المختارة">
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
                            @endif
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
                            <div class="mx-auto another-carousel-wrap">
                                <div id="anotherDesignCarousel" class="carousel slide" data-bs-ride="false">

                                    @foreach ($customDesignImages as $index => $img)
                                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                        <img src="{{ $img->resolved_url }}"
                                            class="d-block mx-auto img-fluid img-thumbnail mb-2"
                                            style="max-width: 260px;"
                                            alt="تصميم آخر {{ $index + 1 }}">
                                    </div>
                                    @endforeach
                                </div>
                            </div>

                            <button class="carousel-control-prev custom-carousel-control"
                                type="button"
                                data-bs-target="#anotherDesignCarousel"
                                data-bs-slide="prev">
                                <span class="carousel-control-prev-icon"></span>
                                <span class="visually-hidden">السابق</span>
                            </button>

                            <button class="carousel-control-next custom-carousel-control"
                                type="button"
                                data-bs-target="#anotherDesignCarousel"
                                data-bs-slide="next">
                                <span class="carousel-control-next-icon"></span>
                                <span class="visually-hidden">التالي</span>
                            </button>
                        </div>

                        <div class="download-buttons-wrapper">
                            <button type="button"
                                class="btn-download btn-download-all"
                                id="downloadAllAnotherImages">
                                <i class="fas fa-cloud-download-alt"></i>
                                تحميل جميع الصور
                            </button>

                            <button type="button"
                                class="btn-download btn-download-current"
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
                        <img src="{{ $frontSrc }}"
                            class="img-fluid img-thumbnail mb-2"
                            style="max-width: 260px;"
                            alt="الصورة الأمامية">

                        <div class="download-buttons-wrapper">
                            <!-- <button type="button"
                                class="btn-download btn-download-all"
                                id="downloadAllFrontImages">
                                <i class="fas fa-cloud-download-alt"></i>
                                تحميل جميع الصور
                            </button> -->

                            <button type="button"
                                class="btn-download btn-download-current"
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
                                    <img src="{{ $backSrc }}"
                                        class="d-block mx-auto img-fluid img-thumbnail mb-2"
                                        style="max-width: 260px;"
                                        alt="الصورة الخلفية {{ $index + 1 }}">
                                </div>
                                @endif
                                @endforeach
                            </div>

                            <button class="carousel-control-prev custom-carousel-control"
                                type="button"
                                data-bs-target="#finalBackImagesCarousel"
                                data-bs-slide="prev">
                                <span class="carousel-control-prev-icon"></span>
                                <span class="visually-hidden">السابق</span>
                            </button>

                            <button class="carousel-control-next custom-carousel-control"
                                type="button"
                                data-bs-target="#finalBackImagesCarousel"
                                data-bs-slide="next">
                                <span class="carousel-control-next-icon"></span>
                                <span class="visually-hidden">التالي</span>
                            </button>
                        </div>

                        <div class="download-buttons-wrapper">
                            <a href="{{ route('orders.backImages.download', $order->id) }}"
                                class="btn-download btn-download-all">
                                <i class="fas fa-cloud-download-alt"></i>
                                تحميل جميع الصور
                            </a>
                            <button type="button"
                                class="btn-download btn-download-current"
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
                            <div class="img-fluids img-thumbnail svg-preview mb-2"
                                style="width: 80%; height: auto;">
                                {!! $order->svg->svg_code !!}
                            </div>

                            <button type="button"
                                class="btn btn-primary btn-sm me-3 copy-svg-button">
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
                        <div class="note-box auto-dir"
                            dir="auto"
                            style="min-height: 80px;"
                            @if (function_exists('detectLang')) lang="{{ detectLang($order->note) }}" @endif>
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

<script>
    window.orderShowConfig = {
        csrfToken: '{{ csrf_token() }}',
        updateStatusUrl: '{{ route('orders.updateStatus') }}',
        updateDesignerUrl: '{{ route('orders.updateDesigner') }}',
    };
</script>
<script src="{{ asset('js/order-show.js') }}"></script>
@endsection