@extends('admin.layout')

@section('content')
<div class="container order-show-page">

    <!-- Custom Styling -->
    <style>
        /* Ensure images have a consistent size */
        .img-fluid.img-thumbnail {
            max-width: 250px;
            height: 200px;
            object-fit: cover;
        }

        /* Fix carousel images to a uniform size */
        #backImagesCarousel img {
            max-width: 500px;
            height: 350px;
            object-fit: contain;
        }

        /* Center carousel controls & make them red */
        .custom-carousel-control {
            width: 5%;
        }

        .custom-carousel-control .carousel-control-prev-icon,
        .custom-carousel-control .carousel-control-next-icon {
            background-color: red;
            border-radius: 50%;
        }

        /* SVG preview container */
        .svg-preview-container {
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 5px;
            background: #f8f9fa;
        }

        /* ✅ نفس ألوان index بالضبط */
        .bg-purple {
            background-color: #6f42c1 !important;
            color: #fff !important;
        }

        .bg-maroon {
            background-color: #800000 !important;
            color: #fff !important;
        }

        .bg-orange {
            background-color: #fd7e14 !important;
            color: #fff !important;
        }



        /* كل نصوص كرت تجليد الدفتر باللون الأسود الواضح */
        .binding-card .card-body {
            color: #000 !important;
        }

        .binding-card .card-body strong,
        .binding-card .card-body span,
        .binding-card .card-body label,
        .binding-card .card-body p,
        .binding-card .card-body div,
        .binding-card .card-body textarea {
            color: #000 !important;
        }

        /* لو في Placeholder داخل textarea */
        .binding-card textarea::placeholder {
            color: #000 !important;
            opacity: 0.6;
        }

        /* إزالة تأثير الشفافية عن label داخل كرت التجليد */
        .binding-card .form-check-label {
            opacity: 1 !important;
        }

        .order-show-page,
        .order-show-page .card,
        .order-show-page .card * {
            font-size: 20px !important;
            color: #000 !important;
        }

        .order-show-page .text-muted {
            color: #000 !important;
        }

        .order-show-page .badge,
        .order-show-page .badge * {
            color: #fff !important;
        }

        .order-show-page i,
        .order-show-page .fas,
        .order-show-page .far,
        .order-show-page .fab,
        .order-show-page .fa {
            color: #000 !important;
        }

        .order-show-page h1 {
            font-size: 20px !important;
            font-weight: bold;
        }

        .order-show-page h2 {
            font-size: 20px !important;
            font-weight: bold;
        }

        .order-show-page h3 {
            font-size: 20px !important;
            font-weight: bold;
        }
    </style>

    @php
    use App\Support\ArabicNameNormalizer;
    use App\Models\SvgName;

    $firstArabicName = ArabicNameNormalizer::firstArabicName($order->username_ar ?? '');
    $svgCodeForName = null;

    if ($firstArabicName) {
    $normalized = ArabicNameNormalizer::normalize($firstArabicName);

    // ✅ نبحث عن الاسم من جدول svg_names مباشرة (بدون علاقة مع svgs)
    $svgNameRow = SvgName::where('normalized_name', $normalized)->first();

    if ($svgNameRow && !empty($svgNameRow->svg_code)) {
    $svgCodeForName = $svgNameRow->svg_code;
    }
    }

    // ضمان تحميل علاقة المصمم
    $order->loadMissing('designer');
    @endphp


    <h1 class="my-4 text-center">تفاصيل الطلب</h1>

    <div class="row">
        <!-- Left Side: Order Details and Other Information -->
        <div class="col-md-6">

            {{-- 🔹 كرت تفاصيل الطلب --}}
            <div class="card shadow-sm mb-4" style="direction: rtl; text-align: right;">
                <div class="card-header d-flex align-items-center" style="font-weight: bold">
                    تفاصيل الطلب
                </div>

                <div class="card-body">
                    {{-- 1. اسم المنتج من علاقة bookType --}}
                    <p>
                        <strong>اسم المنتج:</strong>
                        {{ $order->bookType->name_ar ?? 'غير متوفر' }}
                    </p>

                    {{-- 2. صورة التصميم من علاقة bookDesign --}}
                    <p><strong>صورة المنتج:</strong></p>
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

                    {{-- 3. الجندر من user_gender --}}
                    <p class="mt-3">
                        <strong>الجندر:</strong>
                        @if ($order->user_gender === 'male')
                        ذكر
                        @elseif ($order->user_gender === 'female')
                        أنثى
                        @elseif($order->user_gender)
                        {{ $order->user_gender }}
                        @else
                        غير متوفر
                        @endif
                    </p>

                    {{-- 5. سعر الطلب شامل كود الخصم (final_price_with_discount) --}}
                    <p>
                        <strong>سعر الطلب شامل كود الخصم:</strong>
                        {{ $order->final_price_with_discount ?? 'غير متوفر' }}
                    </p>

                    {{-- 6. اسم كود الخصم من علاقة discountCode --}}
                    <p>
                        <strong>اسم كود الخصم:</strong>
                        {{ $order->discountCode->discount_code ?? 'غير متوفر' }}
                    </p>

                    {{-- 7. قيمة الخصم (القيمة + نوعها) --}}
                    <p>
                        <strong>قيمة الخصم:</strong>
                        @if ($order->discountCode)
                        {{ $order->discountCode->discount_value }}
                        {{ $order->discountCode->discount_type === 'percentage' ? '%' : 'دينار' }}
                        @else
                        غير متوفر
                        @endif
                    </p>

                    {{-- 8. مع إضافات من is_with_additives --}}
                    <p>
                        <strong>مع إضافات:</strong>
                        {{ $order->is_with_additives ? 'نعم' : 'لا' }}
                    </p>
                </div>
            </div>

            {{-- 🔹 كرت الدفتر من الداخل --}}
            <div class="card shadow-sm mb-4" style="direction: rtl; text-align: right;">
                <div class="card-header d-flex align-items-center" style="font-weight: bold">
                    الدفتر من الداخل
                </div>

                <div class="card-body">

                    @php
                    use Illuminate\Support\Str;

                    $internalImage = $order->internalImage;
                    $internalImagesCount = $internalImage ? 1 : 0;

                    // ✅ تجهيز مسار الصورة الشفافة بشكل صحيح
                    $transparentImage = null;
                    if ($order->transparentPrinting && $order->transparentPrinting->image_path) {
                    $tpPath = $order->transparentPrinting->image_path;

                    $transparentImage = Str::startsWith($tpPath, ['http://', 'https://'])
                    ? $tpPath
                    : asset('storage/user_images/' . $tpPath);
                    }

                    $decorationImage = $order->bookDecoration->image ?? null;
                    $giftTitle = $order->gift_title;
                    @endphp


                    {{-- 🔸 الصور الداخلية (عمودي مثل تجليد الدفتر) --}}
                    {{-- 🔸 الصورة الداخلية (صورة واحدة) --}}
                    <div class="mb-4 text-center">
                        <strong class="d-block mb-2">الصورة الداخلية</strong>

                        @if ($internalImage && $internalImage->image_path)
                        @php
                        $path = $internalImage->image_path;
                        $src = \Illuminate\Support\Str::startsWith($path, ['http://', 'https://'])
                        ? $path
                        : asset('storage/user_images/' . $path);
                        @endphp

                        <img src="{{ $src }}"
                            class="d-block mx-auto img-fluid img-thumbnail"
                            style="max-width: 260px;"
                            alt="الصورة الداخلية">
                        @else
                        <p class="text-muted">لا توجد صورة داخلية.</p>
                        @endif
                    </div>


                    {{-- 🔸 الطباعة الشفافة (نفس شكل الزخرفة في تجليد الدفتر) --}}
                    <div class="mb-4 text-center">
                        <strong class="d-block mb-2"> الصورة الشفافة</strong>

                        @if ($transparentImage)
                        <img src="{{ $transparentImage }}"
                            class="img-fluid img-thumbnail"
                            style="max-width: 260px;"
                            alt="الطباعة الشفافة">
                        @else
                        <p class="text-muted">لا توجد صورة للطباعة الشفافة.</p>
                        @endif
                    </div>

                    {{-- 🔸 صورة الزخرفة --}}
                    <div class="mb-4 text-center">
                        <strong class="d-block mb-2">صورة الزخرفة</strong>

                        @if ($order->bookDecoration)
                        {{-- اسم الزخرفة --}}
                        <p class="mb-1" style="font-weight: bold;">
                            {{ $order->bookDecoration->name }}
                        </p>

                        {{-- صورة الزخرفة --}}
                        @if ($order->bookDecoration->image)
                        <img src="{{ $order->bookDecoration->image }}"
                            class="img-fluid img-thumbnail"
                            style="max-width: 260px;"
                            alt="صورة الزخرفة">
                        @else
                        <p class="text-muted">لا توجد صورة للزخرفة.</p>
                        @endif

                        @else
                        <p class="text-muted">لا توجد زخرفة محددة.</p>
                        @endif
                    </div>


                    {{-- 🔸 الإهداء داخل الدفتر --}}
                    <div class="mt-3">
                        <strong>الإهداء داخل الدفتر:</strong>

                        @php
                        $giftType = $order->gift_type ?? 'default';
                        $giftTitle = $order->gift_title;
                        @endphp

                        {{-- ✅ 1) نوع الإهداء --}}
                        <p class="mt-2">
                            @if ($giftType === 'default')
                            <span class="badge bg-info text-dark">إهداء موحّد (مجاني)</span>
                            @elseif ($giftType === 'custom')
                            <span class="badge bg-primary">إهداء مخصّص (مدفوع)</span>
                            @elseif ($giftType === 'none')
                            <span class="badge bg-secondary">بدون إهداء</span>
                            @endif
                        </p>

                        {{-- ✅ 2) محتوى الإهداء حسب النوع --}}
                        @if ($giftType === 'default')
                        {{-- هنا لو عندكم صورة/نص للإهداء الموحد --}}
                        <div class="border rounded p-2 bg-light mt-2" style="white-space: pre-wrap;">
                            {{ config('app.default_gift_text', 'نص الإهداء الموحّد يوضع هنا...') }}
                        </div>

                        @elseif ($giftType === 'custom' && !empty($giftTitle))

                        {{-- 🔹 عرض الهدية كنص أو صورة (مثل ما كان عندك سابقاً) --}}
                        @php
                        $isGiftImage = \Illuminate\Support\Str::startsWith($giftTitle, ['http://', 'https://', '/storage']);
                        if ($isGiftImage) {
                        $giftSrc = \Illuminate\Support\Str::startsWith($giftTitle, ['http://', 'https://'])
                        ? $giftTitle
                        : asset(ltrim($giftTitle, '/'));
                        }
                        @endphp

                        @if ($isGiftImage)
                        <div class="mt-2 mb-2">
                            <img src="{{ $giftSrc }}" class="img-fluid img-thumbnail" style="max-width: 260px;">
                        </div>
                        @endif

                        {{-- نص الإهداء --}}
                        <textarea class="form-control mt-2" rows="3" readonly>{{ $giftTitle }}</textarea>

                        <button type="button"
                            class="btn btn-primary btn-sm mt-2 copy-gift-btn"
                            data-text="{{ $giftTitle }}">
                            <i class="fas fa-copy me-1"></i> نسخ العبارة
                        </button>

                        @elseif ($giftType === 'none')
                        <p class="text-muted mt-2">لا يوجد أي إهداء في هذا الطلب.</p>
                        @endif
                    </div>


                </div>
            </div>

            <div class="card shadow-sm mb-4" style="direction: rtl; text-align: right;">
                <div class="card-header d-flex align-items-center" style="font-weight: bold">
                    معلومات التوصيل
                </div>

                <div class="card-body">
                    {{-- 1. رقم 1 (رقم الديلفري الأول) --}}
                    <p>
                        <strong>رقم 1:</strong>
                        {{ $order->delivery_number_one ?? 'غير متوفر' }}
                    </p>

                    {{-- 2. رقم 2 (رقم الديلفري الثاني) --}}
                    <p>
                        <strong>رقم 2:</strong>
                        {{ $order->delivery_number_two ?? 'غير متوفر' }}
                    </p>

                    {{-- 3. المحافظة --}}
                    <p>
                        <strong>المحافظة:</strong>
                        {{ $order->governorate ?? 'غير متوفر' }}
                    </p>

                    {{-- 4. المنطقة --}}
                    <p>
                        <strong>المنطقة:</strong>
                        {{ $order->address ?? 'غير متوفر' }}
                    </p>

                    {{-- 5. السعر --}}
                    <p>
                        <strong>السعر:</strong>
                        @if (!is_null($order->final_price_with_discount))
                        {{ $order->final_price_with_discount }}
                        @elseif (!is_null($order->final_price))
                        {{ $order->final_price }}
                        @else
                        غير متوفر
                        @endif
                    </p>

                    <div class="mb-2">
                        <strong>ملاحظات المتابعة على التوصيل:</strong>

                        <form action="{{ route('orders.updateDeliveryFollowup', $order->id) }}" method="POST" class="mt-2">
                            @csrf
                            @method('PUT')

                            <textarea
                                name="delivery_followup_note"
                                class="form-control"
                                rows="3"
                                placeholder="اكتب ملاحظات المتابعة على التوصيل هنا...">{{ old('delivery_followup_note', $order->delivery_followup_note) }}</textarea>

                            <div class="text-end mt-2">
                                <button type="submit" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-save me-1"></i> حفظ ملاحظات التوصيل
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>






        </div>

        <!-- Right Side: User Details, Address, and More -->
        <div class="col-md-6">
            <!-- User Details -->
            <!-- User Details -->
            @php
            // 👇 إعداد حالة الطلب + صلاحيات تغييرها (تستخدم في كرت 'معلومات الخريج')
            $statusConfig = [
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
            'error' => [
            'class' => 'bg-danger text-white',
            'label' => 'خطأ',
            ],
            ];

            $currentStatus = $statusConfig[$order->status] ?? [
            'class' => 'bg-secondary',
            'label' => $order->status,
            ];

            // نفس منطق الصلاحيات تبع index
            $canChangeStatus = auth()->user()->isAdmin()
            || ($order->designer && $order->designer->id === auth()->id());
            @endphp
            <div class="card shadow-sm mb-4" style="direction: rtl; text-align: right;">
                <div class="card-header d-flex align-items-center" style="font-weight: bold">
                    معلومات الخريج
                </div>

                <div class="card-body">

                    {{-- 1. اسم المجموعة + حالة التصميم --}}
                    <div class="row mb-2">
                        <div class="col-md-6 mb-1">
                            <strong>اسم المجموعة:</strong>

                            @if ($order->discountCode && $order->discountCode->code_name)
                            <span class="badge bg-secondary">
                                {{ $order->discountCode->code_name }}
                            </span>
                            @elseif ($order->discountCode)
                            {{-- احتياطًا لو ما في code_name نعرض كود الخصم نفسه --}}
                            <span class="badge bg-secondary">
                                {{ $order->discountCode->discount_code }}
                            </span>
                            @else
                            <span class="badge bg-secondary">غير متوفر</span>
                            @endif
                        </div>

                        <div class="col-md-6 mb-1">
                            <strong>حالة التصميم:</strong>

                            @if (! $canChangeStatus)
                            {{-- عرض فقط بدون تعديل --}}
                            <span class="badge {{ $currentStatus['class'] }}">
                                {{ $currentStatus['label'] }}
                            </span>
                            @else
                            {{-- Dropdown لتغيير الحالة --}}
                            <div class="dropdown d-inline">
                                <span
                                    class="badge {{ $currentStatus['class'] }} dropdown-toggle"
                                    id="orderStatusDropdownInfo"
                                    data-bs-toggle="dropdown"
                                    aria-expanded="false"
                                    style="cursor: pointer;">
                                    {{ $currentStatus['label'] }}
                                </span>
                                <ul class="dropdown-menu" aria-labelledby="orderStatusDropdownInfo">
                                    @foreach($statusConfig as $statusKey => $cfg)
                                    @if($statusKey !== $order->status)
                                    <li>
                                        <a href="#"
                                            class="dropdown-item change-status-item"
                                            data-order-id="{{ $order->id }}"
                                            data-new-status="{{ $statusKey }}">
                                            {{ $cfg['label'] }}
                                        </a>
                                    </li>
                                    @endif
                                    @endforeach
                                </ul>
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- 2. الرقم الخاص بالتصميم + واتساب  ||  المصمم المسؤول --}}
                    <div class="row mb-3">

                        {{-- يمين: الرقم + واتساب --}}
                        <div class="col-md-6 mb-1">
                            <strong> رقم اعتماد التصميم:</strong>

                            <div class="mt-1 d-flex align-items-center">
                                <span>
                                    {{ $order->user_phone_number ?? 'غير متوفر' }}
                                </span>

                                @if (!empty($order->user_phone_number))
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $order->user_phone_number) }}"
                                    target="_blank"
                                    class="ms-2"
                                    style="color: #25D366; font-size: 22px;">
                                    <i class="fab fa-whatsapp"></i>
                                </a>
                                @endif
                            </div>
                        </div>


                        {{-- يسار: المصمم المسؤول (مقابل الرقم وتحت حالة التصميم) --}}
                        <div class="col-md-6 mb-1">
                            <strong>المصمم المسؤول:</strong>
                            @if ($order->designer)
                            <span class="badge bg-info text-dark">
                                {{ $order->designer->name }}
                            </span>
                            @else
                            <span class="badge bg-secondary">
                                غير معيّن
                            </span>
                            @endif
                        </div>
                    </div>

                    {{-- 3 + 4. الاسم (عربي) + زر نسخ SVG للاسم --}}
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

                    {{-- 5. الاسم (إنجليزي) --}}
                    <p>
                        <strong>الاسم (إنجليزي):</strong> {{ $order->username_en ?? 'غير متوفر' }}
                    </p>

                    {{-- 6. الجامعة --}}
                    <p>
                        <strong>الجامعة:</strong> {{ $order->school_name ?? 'غير متوفر' }}
                    </p>

                    {{-- 7. التخصص --}}
                    <p>
                        <strong>التخصص:</strong> {{ $order->major_name ?? 'غير متوفر' }}
                    </p>

                </div>
            </div>



            {{-- 🔹 كرت تفاصيل التصميم + الملاحظات --}}
            <div class="card shadow-sm mb-4" style="direction: rtl; text-align: right;">
                <div class="card-header d-flex align-items-center" style="font-weight: bold">
                    تفاصيل التصميم والعبارة
                </div>

                <div class="card-body">
                    @php
                    // ✅ صورة التصميم المختار من book_designs
                    $designImagePath = null;

                    if ($order->bookDesign && $order->bookDesign->image) {
                    $path = $order->bookDesign->image;

                    if (\Illuminate\Support\Str::startsWith($path, ['http://', 'https://'])) {
                    $designImagePath = $path;
                    } else {
                    // مثل: assets/images.svg أو مسار داخل public
                    $designImagePath = asset($path);
                    }
                    }
                    @endphp

                    <div class="row">
                        {{-- 🎨 صورة التصميم (المختارة من book_design) --}}
                        <div class="col-md-6 mb-3">
                            <p><strong>صورة التصميم (المختارة):</strong></p>

                            @if ($order->bookDesign && $order->bookDesign->image)
                            <div class="d-flex flex-column align-items-center">
                                <img src="{{ $order->bookDesign->image }}"
                                    class="img-fluid img-thumbnail mb-2"
                                    style="max-width: 260px;"
                                    alt="صورة التصميم المختارة">

                                {{-- لو بدك زر تحميل --}}
                                <!--
                <a href="{{ $order->bookDesign->image }}"
                   class="btn btn-secondary btn-sm"
                   download>
                   <i class="fas fa-download me-1"></i> تنزيل صورة التصميم
                </a>
                -->
                            </div>
                            @else
                            <p class="text-muted mb-0">لا يوجد تصميم محدّد لهذا الطلب.</p>
                            @endif
                        </div>

                        {{-- 📝 عبارة الدفتر = ملف SVG --}}
                        <div class="col-md-6 mb-3">
                            <p><strong>عبارة الدفتر (ملف SVG):</strong></p>

                            @if ($order->svg && $order->svg->svg_code)
                            {{-- اسم/عنوان الـ SVG إن حبيت --}}
                            @if ($order->svg->title)
                            <p class="mb-2">
                                <span class="badge bg-info text-dark">
                                    {{ $order->svg->title }}
                                </span>
                            </p>
                            @endif

                            <div class="d-flex align-items-center svg-preview-container">
                                <div class="img-fluids img-thumbnail svg-preview mb-2"
                                    style="width: 80%; height: auto;">
                                    {!! $order->svg->svg_code !!}
                                </div>

                                {{-- زر نسخ الـ SVG (نفس اللوجيك القديم) --}}
                                <button type="button"
                                    class="btn btn-primary btn-sm me-3 copy-svg-button">
                                    <i class="fas fa-copy me-1"></i> نسخ SVG
                                </button>
                            </div>
                            @else
                            <p class="text-muted mb-0">لا يوجد ملف SVG مرفق لهذا الطلب.</p>
                            @endif
                        </div>
                    </div>

                    <hr>

                    <div class="row mt-3">
                        {{-- 🧑‍🎓 ملاحظات اليوزر على التصميم --}}
                        <div class="col-md-6 mb-3">
                            <p><strong>ملاحظات المستخدم على التصميم:</strong></p>

                            @if ($order->note)
                            <div class="border rounded p-2 bg-light"
                                style="min-height: 80px; white-space: pre-wrap;">
                                {{ $order->note }}
                            </div>
                            @else
                            <p class="text-muted mb-0">لا توجد ملاحظات مضافة من اليوزر.</p>
                            @endif
                        </div>

                        {{-- 🧑‍💻 ملاحظات المتابعة على التصميم (من نفس $order->notes) --}}
                        <div class="col-md-6 mb-3">
                            <p><strong>ملاحظات المتابعة على التصميم :</strong></p>

                            @php
                            /** @var \App\Models\User $authUser */
                            $authUser = auth()->user();
                            $canEditDesignFollowup = $authUser->isAdmin() || $authUser->isDesigner();

                            // نستخدم نفس العلاقة $order->notes ونجيب آخر نوت كـ ملاحظة المتابعة الحالية
                            $latestDesignNote = $order->notes
                            ? $order->notes->sortByDesc('created_at')->first()
                            : null;

                            $designFollowupText = $latestDesignNote->content ?? null;
                            $designFollowupUser = $latestDesignNote->user->name ?? null;
                            $designFollowupDate = $latestDesignNote?->created_at?->format('d-m-Y , h:i A');
                            @endphp

                            {{-- بوكس ثابت بنفس شكل ملاحظات المستخدم --}}
                            <div class="border rounded bg-light p-3 mb-2">
                                @if ($designFollowupText)
                                <div class="d-flex justify-content-between mb-2">
                                    <small class="text-muted">
                                        {{ $designFollowupUser ?? 'بدون اسم' }}
                                    </small>
                                    @if($designFollowupDate)
                                    <small class="text-muted">
                                        {{ $designFollowupDate }}
                                    </small>
                                    @endif
                                </div>

                                <div>
                                    {{ $designFollowupText }}
                                </div>
                                @else
                                <span class="text-muted">
                                    لا توجد ملاحظات متابعة حتى الآن.
                                </span>
                                @endif
                            </div>

                            {{-- فورم إضافة / تعديل نفس الملاحظة --}}
                            @if ($canEditDesignFollowup)
                            <form action="{{ route('orders.updateDesignFollowup', $order->id) }}"
                                method="POST"
                                class="mt-2">
                                @csrf
                                @method('PUT')

                                <textarea
                                    name="design_followup_note"
                                    class="form-control"
                                    rows="3"
                                    placeholder="اكتب ملاحظات المتابعة على التصميم هنا...">{{ old('design_followup_note', $designFollowupText) }}</textarea>

                                <div class="text-end mt-2">
                                    <button type="submit" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-save me-1"></i> حفظ ملاحظات المتابعة
                                    </button>
                                </div>
                            </form>
                            @endif
                        </div>


                    </div>
                </div>
            </div>






            {{-- 🔹 كرت الصور النهائية للدفتر --}}
            <div class="card shadow-sm mb-4" style="direction: rtl; text-align: right;">
                <div class="card-header d-flex align-items-center" style="font-weight: bold">
                    صـور الـخـريـج
                </div>

                <div class="card-body">
                    @php
                    // صورة أمامية (front_image_id → user_images)
                    $frontImagePath = $order->frontImage->image_path ?? null;

                    // تصميم آخر (custom_design_image_id → user_images)
                    $anotherDesignPath = $order->customDesignImage->image_path ?? null;

                    // أول صورة من الخلف (back_image_ids → user_images)
                    $backImages = $order->backImages();
                    $firstBackImagePath = $backImages->isNotEmpty()
                    ? $backImages->first()->image_path
                    : null;

                    // الصور الإضافية النهائية (من order_additional_images)
                    $additionalImages = $order->additionalImages ?? collect();

                    // 🔧 دالة صغيرة لتجهيز الـ URL الصحيح لأي صورة
                    $resolveImageUrl = function ($path) {
                    if (!$path) {
                    return null;
                    }

                    // لو رابط خارجي كامل
                    if (\Illuminate\Support\Str::startsWith($path, ['http://', 'https://'])) {
                    return $path;
                    }

                    // لو مخزّن على شكل user_images/xxx.png
                    if (\Illuminate\Support\Str::startsWith($path, ['user_images/'])) {
                    return asset('storage/' . ltrim($path, '/'));
                    }

                    // لو مخزّن على شكل /storage/user_images/xxx.png
                    if (\Illuminate\Support\Str::startsWith($path, ['/storage/'])) {
                    return asset(ltrim($path, '/'));
                    }

                    // لو اسم ملف فقط: xxx.png
                    return asset('storage/user_images/' . ltrim($path, '/'));
                    };

                    // نحضّر الـ URLs الجاهزة
                    $frontSrc = $resolveImageUrl($frontImagePath);
                    $anotherSrc = $resolveImageUrl($anotherDesignPath);
                    @endphp



                    {{-- 4) تصميم آخر من custom_design_image_id --}}

                    <div class="mb-4 text-center">
                        <strong class="d-block mb-2">تصميم آخر</strong>

                        @if ($anotherSrc)
                        <img src="{{ $anotherSrc }}"
                            class="d-block mx-auto img-fluid img-thumbnail mb-2"
                            style="max-width: 260px;"
                            alt="تصميم آخر">
                        @else
                        <p class="text-muted">لا يوجد تصميم آخر.</p>
                        @endif
                    </div>




                    {{-- 1) الصورة الأمامية --}}
                    <div class="mb-4 text-center">
                        <strong class="d-block mb-2">الصورة الأمامية</strong>

                        @if ($frontSrc)
                        <img src="{{ $frontSrc }}"
                            class="img-fluid img-thumbnail mb-2"
                            style="max-width: 260px;"
                            alt="الصورة الأمامية">

                        <div>
                            <a href="{{ $frontSrc }}"
                                class="btn btn-secondary btn-sm"
                                download>
                                <i class="fas fa-download me-1"></i> تنزيل الصورة الأمامية
                            </a>
                        </div>
                        @else
                        <p class="text-muted">لا توجد صورة أمامية.</p>
                        @endif

                    </div>



                    {{-- 3) الصور من الخلف --}}
                    <div class="mb-4 text-center">
                        <strong class="d-block mb-2">
                            الصور من الخلف
                            @if($backImages->isNotEmpty())
                            (عدد: {{ $backImages->count() }})
                            @endif
                        </strong>

                        @if ($backImages->isNotEmpty())
                        <div id="finalBackImagesCarousel" class="carousel slide mb-3" data-bs-ride="false">
                            <div class="carousel-inner text-center">

                                @foreach ($backImages as $index => $backImage)
                                @php
                                // نستخدم نفس الفنكشن resolveImageUrl
                                $backSrc = $resolveImageUrl($backImage->image_path ?? null);
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

                            {{-- الأسهم --}}
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

                        {{-- الأزرار: تنزيل الصورة الحالية + تنزيل جميع الصور الخلفية --}}
                        <div class="d-flex justify-content-center gap-2">
                            {{-- تنزيل الصورة الحالية فقط --}}
                            <button type="button"
                                id="downloadCurrentFinalBackImage"
                                class="btn btn-secondary btn-sm">
                                <i class="fas fa-download me-1"></i> تنزيل الصورة الحالية
                            </button>

                            {{-- تنزيل جميع الصور الخلفية --}}
                            <a href="{{ route('orders.backImages.download', $order->id) }}"
                                class="btn btn-success btn-sm">
                                <i class="fas fa-download me-1"></i> تنزيل جميع الصور الخلفية
                            </a>
                        </div>
                        @else
                        <p class="text-muted">لا توجد صور خلفية لهذا الطلب.</p>
                        @endif
                    </div>







                </div>
            </div>


            {{-- 🔹 كرت تجليد الدفتر --}}
            <div class="card shadow-sm mb-4 binding-card" style="direction: rtl; text-align: right;">

                <div class="card-header d-flex align-items-center" style="font-weight: bold">
                    تجليد الدفتر
                </div>

                <div class="card-body">
                    @php
                    /** @var \App\Models\User $authUser */
                    $authUser = auth()->user();
                    $canEditBinding = $authUser->isAdmin() || $authUser->isDesigner();

                    // صورة داخلية واحدة فقط من علاقة internalImage
                    $internalImage = $order->internalImage ?? null;
                    $hasInternalImage = $internalImage && $internalImage->image_path;
                    $internalImagesCount = $hasInternalImage ? 1 : 0;

                    // عدد الورق (جاية من اليوزر - عرض فقط)
                    $pagesCount = $order->pages_number ?? 0;

                    // العبارة (جاية من اليوزر - عرض فقط)
                    $giftTitle = $order->gift_title;

                    // صورة الطباعة الشفافة (من علاقة transparentPrinting)
                    $transparentImagePath = null;
                    if ($order->transparentPrinting && $order->transparentPrinting->image_path) {
                    $tpPath = $order->transparentPrinting->image_path;
                    $transparentImagePath = \Illuminate\Support\Str::startsWith($tpPath, ['http://', 'https://'])
                    ? $tpPath
                    : asset('storage/user_images/' . $tpPath);
                    }
                    @endphp

                    <form id="bindingUpdateForm"
                        action="{{ route('orders.updateBinding', $order->id) }}"
                        method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- 1. مع إضافات --}}
                        <div class="mb-2">
                            <strong>مع إضافات:</strong>

                            @php
                            $hasAdditives = $order->notes && $order->notes->count() > 0;
                            @endphp

                            <div class="form-check d-inline-block ms-2">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    disabled
                                    {{ $hasAdditives ? 'checked' : '' }}>

                                <label class="form-check-label">
                                    {{ $hasAdditives ? 'يوجد إضافات' : 'لا يوجد إضافات' }}
                                </label>
                            </div>
                        </div>

                        {{-- 2. الإضافات الموجودة --}}
                        <div class="mb-3">
                            <strong>الإضافات الموجودة:</strong>

                            <div class="mt-2">

                                {{-- صور داخلية --}}
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" disabled
                                        {{ $internalImagesCount > 0 ? 'checked' : '' }}>
                                    <label class="form-check-label">
                                        صور داخلية
                                        @if($internalImagesCount > 0)
                                        (صورة واحدة)
                                        @endif
                                    </label>
                                </div>

                                {{-- زخرفة --}}
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" disabled
                                        {{ $order->bookDecoration ? 'checked' : '' }}>
                                    <label class="form-check-label">
                                        زخرفة
                                        @if($order->bookDecoration)
                                        ({{ $order->bookDecoration->name }})
                                        @endif
                                    </label>

                                    @if(!$order->bookDecoration)
                                    {{-- بدون text-muted حتى يكون النص أسود --}}
                                    <span class="ms-1">(لا توجد زخرفة محددة)</span>
                                    @endif
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

                        {{-- 4. عدد الصور الداخلية (قراءة فقط) --}}
                        <div class="mb-2">
                            <strong>عدد الصور الداخلية:</strong>
                            <div class="form-check d-inline-block ms-2">
                                <input class="form-check-input" type="checkbox" disabled
                                    {{ $internalImagesCount > 0 ? 'checked' : '' }}>
                                <label class="form-check-label">
                                    @if($internalImagesCount > 0)
                                    موجودة (عدد: 1)
                                    @else
                                    لا توجد صور داخلية
                                    @endif
                                </label>
                            </div>
                        </div>

                        {{-- 5. عدد الورق (عرض فقط - لا تعديل) --}}
                        <div class="mb-3">
                            <strong>عدد الورق:</strong>
                            <span class="ms-2">
                                @if($pagesCount > 0)
                                {{ $pagesCount }} ورقة
                                @else
                                غير محدد
                                @endif
                            </span>
                        </div>

                        {{-- 6. إسفنج (ما زال قابل للتعديل للآدمن/الديزاينر) --}}
                        <div class="mb-3">
                            <strong>إسفنج:</strong>
                            <div class="form-check d-inline-block ms-2">
                                @if($canEditBinding)
                                <input class="form-check-input"
                                    type="checkbox"
                                    name="is_sponge"
                                    {{ $order->is_sponge ? 'checked' : '' }}>
                                <label class="form-check-label">
                                    {{ $order->is_sponge ? 'مع إسفنج' : 'بدون إسفنج' }}
                                </label>
                                @else
                                <input class="form-check-input" type="checkbox" disabled
                                    {{ $order->is_sponge ? 'checked' : '' }}>
                                <label class="form-check-label">
                                    {{ $order->is_sponge ? 'مع إسفنج' : 'بدون إسفنج' }}
                                </label>
                                @endif
                            </div>
                        </div>

                        {{-- 7. الإهداء داخل الدفتر (قراءة فقط - لا تعديل) --}}
                        <div class="mb-3">
                            <strong>الإهداء:</strong>

                            @php
                            $giftType = $order->gift_type ?? 'default';
                            $giftTitle = $order->gift_title;
                            @endphp

                            <div class="mt-2">

                                {{-- ✅ الحالة 1: بدون إهداء --}}
                                @if ($giftType === 'none')
                                <span class="text-muted">لا يوجد أي إهداء.</span>

                                {{-- ✅ الحالة 2: إهداء موحّد --}}
                                @elseif ($giftType === 'default')
                                <span class="badge bg-info text-dark">إهداء موحّد</span>

                                {{-- لو عندك صورة ثابتة أو نص ثابت جذاب، ضيفه هنا --}}
                                <div class="border rounded p-2 bg-light mt-2" style="white-space: pre-wrap;">
                                    {{ config('app.default_gift_text', 'نص الإهداء الموحّد يوضع هنا...') }}
                                </div>

                                {{-- ✅ الحالة 3: إهداء مخصّص --}}
                                @elseif ($giftType === 'custom' && !empty($giftTitle))

                                {{-- نتحقق هل الإهداء عبارة عن صورة أم نص --}}
                                @php
                                $isGiftImage = \Illuminate\Support\Str::startsWith($giftTitle, [
                                'http://', 'https://', '/storage'
                                ]);
                                if ($isGiftImage) {
                                $giftSrc = \Illuminate\Support\Str::startsWith($giftTitle, ['http://', 'https://'])
                                ? $giftTitle
                                : asset(ltrim($giftTitle, '/'));
                                }
                                @endphp

                                @if ($isGiftImage)
                                <img src="{{ $giftSrc }}"
                                    alt="العبارة المخصصة"
                                    class="img-fluid img-thumbnail"
                                    style="max-width: 220px;">
                                @else
                                <div class="border rounded p-2" style="white-space: pre-wrap;">
                                    {{ $giftTitle }}
                                </div>
                                @endif

                                {{-- أي سيناريو غير متوقع --}}
                                @else
                                <span class="text-muted">لا يوجد إهداء.</span>

                                @endif
                            </div>
                        </div>


                        {{-- 8. ملاحظات المتابعة على التجليد --}}
                        <div class="mb-2">
                            <strong>ملاحظات المتابعة على التجليد:</strong>
                            <textarea
                                name="binding_followup_note"
                                class="form-control mt-2"
                                rows="3"
                                placeholder="اكتب ملاحظات المتابعة على التجليد هنا...">{{ old('binding_followup_note', $order->binding_followup_note) }}</textarea>
                        </div>

                        @if($canEditBinding)
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

        <!-- Full-width: Images Section -->
        <!-- <div class="col-12">
            <div class="card shadow-sm mb-4" style="direction: rtl; text-align: right;">
                <div class="card-header d-flex align-items-center">
                    الصور
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>الصورة الأمامية:</strong></p>
                            @if ($order->frontImage)
                            <div class="d-flex align-items-center">
                                <img src="{{ $order->frontImage->image_path }}"
                                    class="img-fluid img-thumbnail mb-2"
                                    alt="الصورة الأمامية">
                                <a href="{{ $order->frontImage->image_path }}"
                                    class="btn btn-secondary btn-sm me-3"
                                    download>
                                    <i class="fas fa-download me-1"></i> تنزيل
                                </a>
                            </div>
                            @else
                            <p>لا توجد صورة متوفرة</p>
                            @endif
                        </div>

                        <div class="col-md-6">
                            <p><strong>الصور الإضافية:</strong></p>

                            @if ($order->additionalImages && $order->additionalImages->isNotEmpty())
                            {{-- سلايدر Bootstrap للصور الإضافية --}}
                            <div id="additionalImagesCarousel" class="carousel slide mb-3" data-bs-ride="false">
                                <div class="carousel-inner text-center">
                                    @foreach ($order->additionalImages as $index => $img)
                                    @if ($img->userImage && $img->userImage->image_path)
                                    @php
                                    $path = $img->userImage->image_path;

                                    if (\Illuminate\Support\Str::startsWith($path, ['http://', 'https://'])) {
                                    $src = $path;
                                    } else {
                                    $src = asset('storage/user_images/' . $path);
                                    }
                                    @endphp

                                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                        <img src="{{ $src }}"
                                            class="d-block mx-auto img-fluid img-thumbnail mb-2"
                                            alt="الصورة الإضافية">
                                    </div>
                                    @endif
                                    @endforeach
                                </div>

                                {{-- أسهم التنقّل --}}
                                <button class="carousel-control-prev custom-carousel-control"
                                    type="button"
                                    data-bs-target="#additionalImagesCarousel"
                                    data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">السابق</span>
                                </button>

                                <button class="carousel-control-next custom-carousel-control"
                                    type="button"
                                    data-bs-target="#additionalImagesCarousel"
                                    data-bs-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">التالي</span>
                                </button>
                            </div>

                            {{-- الأزرار --}}
                            <div class="d-flex gap-2">
                                {{-- تنزيل الصورة الحالية فقط --}}
                                <button type="button"
                                    id="downloadCurrentAdditional"
                                    class="btn btn-secondary btn-sm">
                                    <i class="fas fa-download me-1"></i> تنزيل الصورة
                                </button>

                                {{-- تنزيل جميع الصور --}}
                                <a href="{{ route('orders.additionalImages.download', $order->id) }}"
                                    class="btn btn-success btn-sm">
                                    <i class="fas fa-download me-1"></i> تنزيل جميع الصور
                                </a>
                            </div>

                            @elseif ($order->additionalImage)
                            {{-- دعم النسخة القديمة لصورة إضافية واحدة --}}
                            <div class="d-flex align-items-center">
                                <img src="{{ $order->additionalImage->image_path }}"
                                    class="img-fluid img-thumbnail mb-2"
                                    alt="الصورة الإضافية">
                                <a href="{{ $order->additionalImage->image_path }}"
                                    class="btn btn-secondary btn-sm me-3"
                                    download>
                                    <i class="fas fa-download me-1"></i> تنزيل
                                </a>
                            </div>

                            @else
                            <p>لا توجد صور متوفرة</p>
                            @endif
                        </div>

                        <div class="col-md-6">
                            <p><strong>زخرفة الكتاب:</strong></p>

                            @if ($order->bookDecoration)
                            <p class="mb-2">
                                <strong>اسم الزخرفة:</strong> {{ $order->bookDecoration->name ?? '—' }}
                            </p>

                            <div class="d-flex align-items-center">
                                <img src="{{ $order->bookDecoration->image }}"
                                    class="img-fluid img-thumbnail mb-2"
                                    alt="زخرفة الكتاب">

                                <a href="{{ $order->bookDecoration->image }}"
                                    class="btn btn-secondary btn-sm me-3"
                                    download>
                                    <i class="fas fa-download me-1"></i> تنزيل
                                </a>
                            </div>
                            @else
                            <p>لا توجد صورة متوفرة</p>
                            @endif
                        </div>

                        <div class="col-md-6">
                            <p><strong>الطباعة الشفافة:</strong></p>
                            @if ($order->transparentPrinting)
                            <div class="d-flex align-items-center">
                                <img src="{{ $order->transparentPrinting->image_path }}"
                                    class="img-fluid img-thumbnail mb-2"
                                    alt="الطباعة الشفافة">
                                <a href="{{ $order->transparentPrinting->image_path }}"
                                    class="btn btn-secondary btn-sm me-3"
                                    download>
                                    <i class="fas fa-download me-1"></i> تنزيل
                                </a>
                            </div>
                            @else
                            <p>لا توجد صورة متوفرة</p>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-2 mt-3">
                        <p><strong>ملف SVG:</strong></p>
                        <div class="d-flex align-items-center svg-preview-container">
                            <div class="img-fluids img-thumbnail svg-preview mb-2" style="width: 80%; height: auto;">
                                {!! $order->svg->svg_code !!}
                            </div>
                            <button class="btn btn-primary btn-sm me-3 copy-svg-button">
                                <i class="fas fa-copy me-1"></i> نسخ
                            </button>
                        </div>
                    </div>

                    <p><strong>الصور الخلفية:</strong></p>
                    @if ($order->backImages()->isNotEmpty())
                    <div id="backImagesCarousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner text-center">
                            @foreach ($order->backImages() as $index => $backImage)
                            <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                <img src="{{ $backImage->image_path }}"
                                    class="d-block mx-auto img-fluid rounded shadow"
                                    alt="الصورة الخلفية">
                            </div>
                            @endforeach
                        </div>

                        <button class="carousel-control-prev custom-carousel-control"
                            type="button"
                            data-bs-target="#backImagesCarousel"
                            data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">السابق</span>
                        </button>

                        <button class="carousel-control-next custom-carousel-control"
                            type="button"
                            data-bs-target="#backImagesCarousel"
                            data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">التالي</span>
                        </button>
                    </div>

                    <div class="mt-3 text-center">
                        <a href="{{ route('orders.backImages.download', $order->id) }}"
                            class="btn btn-success btn-sm">
                            <i class="fas fa-download me-1"></i> تنزيل جميع الصور
                        </a>
                    </div>
                    @else
                    <p>لا توجد صور خلفية متوفرة</p>
                    @endif
                </div>
            </div>
        </div> -->
    </div>
</div>

{{-- Scripts --}}

<script src="{{ asset('js/order-show.js') }}"></script>
@endsection