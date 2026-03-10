@extends('admin.layout')

@section('content')
    <style>
        /* Enhanced Card Styling */
        .card-enhanced {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card-enhanced:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        /* Icon Circle Styling */
        .icon-circle {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Typography for Card Titles & Counts */
        .card-title {
            font-size: 14px;
            color: #6c757d;
            margin-bottom: 0.25rem;
        }

        .card-count {
            font-size: 24px;
            font-weight: 700;
            margin: 0;
        }

        .card-fintech {
            background: linear-gradient(135deg, #727cf5 0%, #39afd1 100%) !important;
            border: none !important;
        }

        .bg-soft-primary {
            background-color: #f1f4f8 !important;
        }

        .bg-soft-success {
            background-color: #f1fdf6 !important;
        }

        .bg-soft-warning {
            background-color: #fff9f0 !important;
        }

        .pulse-icon {
            animation: pulse 2s infinite;
            color: #727cf5;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
                opacity: 1;
            }

            50% {
                transform: scale(1.1);
                opacity: 0.7;
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        .nav-pills .nav-link {
            color: #6c757d;
            transition: all 0.3s;
        }

        .nav-pills .nav-link.active {
            background-color: #727cf5 !important;
            color: #fff !important;
            box-shadow: 0 4px 10px rgba(114, 124, 245, 0.3);
        }

        .hover-primary:hover {
            background-color: #727cf5 !important;
        }

        .hover-primary:hover i {
            color: #fff !important;
        }

        .custom-switch-lg .form-check-input {
            width: 3.2rem;
            height: 1.6rem;
            cursor: pointer;
        }

        .shadow-2xl {
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15) !important;

        }

        /* ==================================================
                                               💎 تصميم الـ Pagination المبهر (Fintech Style)
                                               ================================================== */
        .custom-pagination-wrapper nav {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* إخفاء النص الافتراضي تبع لارافل والإبقاء على الأزرار فقط */
        .custom-pagination-wrapper>nav>div:first-child {
            display: none !important;
        }

        .custom-pagination-wrapper>nav>div:last-child {
            display: flex !important;
            justify-content: center !important;
            width: 100%;
        }

        .custom-pagination-wrapper .pagination {
            gap: 8px;
            margin-bottom: 0;
            flex-wrap: wrap;
        }

        /* تصميم الأزرار العادية */
        .custom-pagination-wrapper .page-item .page-link {
            border: none;
            border-radius: 12px;
            /* حواف ناعمة عصرية */
            min-width: 42px;
            height: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #64748b;
            font-weight: 700;
            font-size: 15px;
            background-color: #f8fafc;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
            padding: 0 12px;
        }

        /* تصميم الزر الفعال (Active) */
        .custom-pagination-wrapper .page-item.active .page-link {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            box-shadow: 0 6px 15px rgba(16, 185, 129, 0.35);
            transform: translateY(-2px);
        }

        /* تأثير التمرير (Hover) */
        .custom-pagination-wrapper .page-item .page-link:hover:not(.active) {
            background-color: #e2e8f0;
            color: #10b981;
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.05);
        }

        /* تصميم الأسهم المعطلة (Disabled) */
        .custom-pagination-wrapper .page-item.disabled .page-link {
            background-color: #f1f5f9;
            color: #cbd5e1;
            box-shadow: none;
            transform: none;
            opacity: 0.7;
        }
    </style>
    {{-- order cards --}}
    @include('admin.partials.order_cards')

    {{-- order charts --}}
    @include('admin.partials.order_charts')


    {{-- 🔹 قسم المصمم: جدول الملاحظات + كارد الأرباح --}}
    {{-- 🔹 قسم المصمم: جدول الملاحظات + كارد المحفظة --}}
    {{-- 🟢 الشرط الرئيسي: يسمح للآدمن، المشرف، والمصمم برؤية هذا القسم --}}
    @if(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isSupervisor() || auth()->user()->isDesigner()))
        <div class="row mt-4" style="direction: rtl; text-align: right; font-family: 'Cairo', sans-serif;">

            {{-- 1. جدول ملاحظات المتابعة --}}
            {{-- 💡 لمسة ذكية: إذا كان المستخدم مصمم نعطيه حجم 8، وإذا آدمن/مشرف نعطيه حجم 12 (عرض كامل) لأن المحفظة مخفية --}}
            <div class="{{ auth()->user()->isDesigner() ? 'col-xl-8 col-lg-8' : 'col-12' }} mb-4">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 bg-white">
                    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center mt-2">
                        <h4 class="mb-0 text-primary fw-bold fs-5">
                            <i class="fas fa-bell me-2 pulse-icon"></i> ملاحظات المتابعة بانتظارك
                        </h4>
                        <span class="badge bg-soft-primary text-primary rounded-pill px-3">{{ $designerNotes->count() }} ملاحظات
                            جديدة</span>
                    </div>
                    <div class="card-body p-0 mt-2">
                        <div class="table-responsive" style="max-height: 400px;">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light text-muted small fw-bold border-top">
                                    <tr>
                                        <th class="px-4 py-3">رقم الطلب</th>
                                        <th>الخريج</th>
                                        <th class="text-center">الملاحظات</th>
                                        <th class="text-end px-4">عرض الطلب</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($designerNotes as $note)
                                        <tr class="border-bottom">
                                            <td class="px-4">
                                                <span class="fw-bold text-primary">#{{ $note->id }}</span>
                                            </td>
                                            <td>
                                                <div class="fw-semibold text-dark">{{ $note->username_ar }}</div>
                                            </td>
                                            <td class="text-center">
                                                <button type="button"
                                                    class="btn btn-sm btn-info text-white rounded-pill px-4 shadow-sm fw-bold"
                                                    data-bs-toggle="modal" data-bs-target="#notesModal-{{ $note->id }}">
                                                    <i class="fas fa-eye me-1"></i> عرض الملاحظات
                                                </button>
                                            </td>
                                            <td class="text-end px-4">
                                                <a href="{{ route('orders.show', ['id' => $note->id]) }}"
                                                    class="btn btn-sm btn-light rounded-circle shadow-sm p-2 hover-primary">
                                                    <i class="fas fa-arrow-left text-primary"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-5">
                                                <div class="opacity-25 mb-3 text-muted"><i class="fas fa-check-circle fa-4x"></i>
                                                </div>
                                                <h6 class="text-muted fw-bold">لا توجد ملاحظات جديدة حالياً</h6>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. كارد المحفظة المالية (🔴 يظهر للمصمم فقط 🔴) --}}
            @if(auth()->user()->isDesigner())
                <div class="col-xl-4 col-lg-4 mb-4">
                    <div
                        class="card border-0 shadow-lg rounded-4 bg-primary text-white h-100 position-relative overflow-hidden card-fintech">
                        <div class="card-body p-4 position-relative z-1 d-flex flex-column justify-content-between">
                            <div>
                                <div class="bg-white bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center mb-3 shadow-sm"
                                    style="width: 50px; height: 50px;">
                                    <i class="fas fa-wallet fs-4"></i>
                                </div>
                                <h6 class="text-white text-opacity-75 fw-bold mb-1">المستحقات المعلقة</h6>
                                <h2 class="display-5 fw-bolder mb-0 lh-1">
                                    {{ number_format($totalCommission, 2) }} <small class="fs-4">د.أ</small>
                                </h2>
                            </div>
                            <div
                                class="mt-4 pt-3 border-top border-white border-opacity-20 d-flex align-items-center justify-content-between">
                                <span class="small opacity-75"><i class="fas fa-info-circle me-1"></i> بانتظار التسوية
                                    المالية</span>
                                <i class="fas fa-university fs-3 opacity-25"></i>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        </div>

        {{-- 💡 مودالات الملاحظات (تظهر للجميع عشان يقدروا يقرأوا الملاحظات اللي بالجدول) --}}
        @foreach($designerNotes as $note)
            <div class="modal fade" id="notesModal-{{ $note->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content border-0 shadow-2xl overflow-hidden" style="border-radius: 20px;">

                        <div class="modal-header border-0 bg-dark p-4 d-flex justify-content-between align-items-center text-white">
                            <div>
                                <h3 class="modal-title fw-bold mb-1 text-white">ملاحظات المتابعة #{{ $note->id }}</h3>
                                <span class="text-white opacity-75 fs-6">{{ $note->username_ar }}</span>
                            </div>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body p-0 bg-white">
                            <div class="p-3 bg-light border-bottom">
                                <ul class="nav nav-pills nav-fill gap-2 p-1 bg-white rounded-pill shadow-sm border" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active rounded-pill fw-bold" data-bs-toggle="tab"
                                            href="#tab-design-{{ $note->id }}">
                                            <i class="fas fa-pen-nib me-1"></i> التصميم
                                        </a>
                                    </li>
                                    <li class="nav-item mx-1">
                                        <a class="nav-link rounded-pill fw-bold" data-bs-toggle="tab"
                                            href="#tab-notebook-{{ $note->id }}">
                                            <i class="fas fa-book-open me-1"></i> الدفتر
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link rounded-pill fw-bold" data-bs-toggle="tab"
                                            href="#tab-binding-{{ $note->id }}">
                                            <i class="fas fa-scroll me-1"></i> التجليد
                                        </a>
                                    </li>
                                </ul>
                            </div>

                            <div class="tab-content p-4" style="min-height: 250px;">
                                <div class="tab-pane fade show active" id="tab-design-{{ $note->id }}">
                                    @if($note->design_followup_note)
                                        <div class="p-4 rounded-4 bg-soft-primary border-primary border-start border-4 shadow-sm">
                                            <h6 class="fw-bold text-primary mb-3"><i class="fas fa-layer-group me-1"></i> ملاحظات
                                                التصميم:</h6>
                                            <div class="text-dark fs-5 lh-base">{!! nl2br(e($note->design_followup_note)) !!}</div>
                                        </div>
                                    @else
                                        <p class="text-center text-muted py-5 italic">لا توجد ملاحظات تصميم مضافة.</p>
                                    @endif
                                </div>

                                <div class="tab-pane fade" id="tab-notebook-{{ $note->id }}">
                                    @if($note->notebook_followup_note)
                                        <div
                                            class="p-4 rounded-4 bg-soft-success border-success border-start border-4 shadow-sm text-dark fs-5">
                                            <h6 class="fw-bold text-success mb-3"><i class="fas fa-book-open me-1"></i> ملاحظات الدفتر
                                                من الداخل:</h6>
                                            {!! nl2br(e($note->notebook_followup_note)) !!}
                                        </div>
                                    @else
                                        <p class="text-center text-muted py-5">لا توجد ملاحظات للدفتر.</p>
                                    @endif
                                </div>

                                <div class="tab-pane fade" id="tab-binding-{{ $note->id }}">
                                    @if($note->binding_followup_note)
                                        <div
                                            class="p-4 rounded-4 bg-soft-warning border-warning border-start border-4 shadow-sm text-dark fs-5">
                                            <h6 class="fw-bold text-warning mb-3"><i class="fas fa-scroll me-1"></i> ملاحظات التجليد:
                                            </h6>
                                            {!! nl2br(e($note->binding_followup_note)) !!}
                                        </div>
                                    @else
                                        <p class="text-center text-muted py-5">لا توجد ملاحظات تجليد.</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer border-0 bg-light p-4 d-flex justify-content-between align-items-center">
                            {{-- 🔴 زر "تمت المراجعة" يظهر للمصمم فقط 🔴 --}}
                            @if(auth()->user()->isDesigner())
                                <div class="form-check form-switch custom-switch-lg mb-0 p-0 d-flex align-items-center">
                                    <label class="form-check-label fw-bold me-4 text-dark" for="dismissNotes-{{ $note->id }}">تمت
                                        المراجعة</label>
                                    <input class="form-check-input js-dismiss-notes-btn cursor-pointer shadow-sm ms-0" type="checkbox"
                                        id="dismissNotes-{{ $note->id }}" data-order-id="{{ $note->id }}">
                                </div>
                            @else
                                <div></div> {{-- ديف فارغ للحفاظ على المسافات (Flexbox) --}}
                            @endif

                            <a href="{{ route('orders.show', ['id' => $note->id]) }}#tab-graduate-info"
                                id="executeBtn-{{ $note->id }}"
                                class="btn btn-primary rounded-pill px-4 fw-bold shadow py-2 execute-order-btn">
                                تفاصيل الطلب <i class="fas fa-arrow-left ms-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

{{-- 🔹 سجل العمولات (🔴 يظهر للمصمم فقط 🔴) --}}
        @if(auth()->user()->isDesigner())
            <div class="row mt-4" style="direction: rtl; text-align: right;">
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
                        <div class="card-body p-4">
                            <h5 class="fw-bold text-dark mb-4 border-bottom pb-3">
                                <i class="fas fa-history text-success me-2 ms-1"></i> سجل العمولات المنجزة
                            </h5>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle text-center">
                                    <thead class="bg-soft-success">
                                        <tr class="text-success small fw-bold">
                                            <th class="py-3">رقم الطلب</th>
                                            <th>اسم الخريج</th>
                                            <th>الزخرفة</th>
                                            <th>الإهداء المخصص</th>
                                            <th>الصور الداخلية</th>
                                            <th>العمولة النهائية</th>
                                            <th>تاريخ الإنجاز</th>
                                            <th>الحالة المالية</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($historyOrders as $history)
                                            <tr>
                                                <td class="fw-bold">#{{ $history->id }}</td>
                                                <td class="fw-semibold text-dark">{{ $history->username_ar }}</td>
                                                
                                                <td>{!! $history->book_decorations_id ? '<span class="text-success fw-bold">نعم</span>' : '<span class="text-muted">لا</span>' !!}</td>
                                                
                                                <td>{!! $history->gift_type === 'custom' ? '<span class="text-warning fw-bold">نعم</span>' : '<span class="text-muted">لا</span>' !!}</td>
                                                
                                                {{-- فحص الصور الداخلية --}}
                                                @php
                                                    $hasInternalImages = false;
                                                    $additionalIds = $history->additional_image_id;
                                                    if (is_string($additionalIds)) {
                                                        $additionalIds = json_decode($additionalIds, true);
                                                    }
                                                    if (is_array($additionalIds) && !empty($additionalIds)) {
                                                        $hasInternalImages = true;
                                                    }
                                                @endphp
                                                <td>{!! $hasInternalImages ? '<span class="text-primary fw-bold">نعم</span>' : '<span class="text-muted">لا</span>' !!}</td>

                                                <td class="text-success fw-bolder fs-5">
                                                    @if($history->is_commission_paid)
                                                        {{ number_format($history->paid_commission ?? 0, 2) }}
                                                    @else
                                                        {{ number_format(($history->designer_commission ?? 0) - ($history->paid_commission ?? 0), 2) }}
                                                    @endif
                                                    <small class="fs-6">د.أ</small>
                                                </td>
                                                
                                                <td class="text-muted small">{{ $history->updated_at->format('Y-m-d') }}</td>
                                                
                                                <td>
                                                    @if($history->is_commission_paid)
                                                        <span class="badge bg-soft-success text-success rounded-pill px-3 py-2">
                                                            <i class="fas fa-check-circle me-1 ms-1"></i> مدفوع
                                                        </span>
                                                    @else
                                                        <span class="badge bg-soft-warning text-warning rounded-pill px-3 py-2">
                                                            <i class="fas fa-clock me-1 ms-1"></i> قيد الانتظار
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="py-5 text-muted">لا يوجد سجل عمولات حتى الآن</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            @if($historyOrders instanceof \Illuminate\Pagination\LengthAwarePaginator && $historyOrders->hasPages())
                                <div class="mt-4 pt-3 border-top custom-pagination-wrapper">
                                    {{ $historyOrders->links('pagination::bootstrap-5') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif

    @endif

    {{-- 🔹 Designers Scoreboard --}}
    <div class="row mt-4" style="text-align: left; direction: ltr;">
        <div class="col-12 mb-3">
            <h4 class="fw-bold text-dark d-flex align-items-center">
                <i class="fas fa-trophy text-warning me-2 fs-3"></i> Designers Scoreboard
            </h4>
        </div>

        @forelse($designersScoreboard as $designer)
            @php
                $total = (int) $designer->total_orders;
                $done = (int) $designer->completed_orders;

                // 👈 متغير الطلبات اليومية
                $todayAssigned = (int) ($designer->today_assigned_orders ?? 0);

                $percent = $total > 0 ? round(($done / $total) * 100) : 0;

                $colors = ['bg-primary', 'bg-success', 'bg-info', 'bg-warning', 'bg-danger', 'bg-dark'];
                $colorClass = $colors[$loop->index % count($colors)];
            @endphp

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden card-enhanced">
                    {{-- شريط علوي ملون لكل مصمم --}}
                    <div style="height: 5px;" class="{{ $colorClass }} w-100 opacity-75"></div>

                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="position-relative me-3"> {{-- me-3 لليسار --}}
                                @if($designer->image)
                                    <img src="{{ asset('storage/' . $designer->image) }}" class="rounded-circle shadow-sm"
                                        alt="{{ $designer->name }}" style="width: 55px; height: 55px; object-fit: cover;">
                                @else
                                    @php
                                        $nameParts = explode(' ', trim($designer->name));
                                        $initials = count($nameParts) > 1
                                            ? mb_substr($nameParts[0], 0, 1) . mb_substr($nameParts[1], 0, 1)
                                            : mb_substr($designer->name, 0, 1);
                                    @endphp
                                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm {{ $colorClass }}"
                                        style="width: 55px; height: 55px; color: #fff; font-size: 1.2rem; opacity: 0.85;">
                                        {{ strtoupper($initials) }}
                                    </div>
                                @endif
                            </div>

                            <div class="flex-grow-1">
                                <h6 class="mb-1 fw-bold text-dark text-truncate" style="max-width: 140px;">{{ $designer->name }}
                                </h6>
                                <span class="badge bg-light text-muted border px-2 py-1 fw-normal"
                                    style="font-size: 0.7rem;">Designer</span>
                            </div>
                        </div>

                        {{-- صندوق الإحصائيات (قيد التجهيز، اليومي، الكلي) --}}
                        <div class="row g-0 mb-4 bg-light rounded-4 p-2 text-center border">


                            {{-- عدد المخصص اليوم --}}
                            <div class="col-4 border-end">
                                <span class="d-block text-muted mb-1" style="font-size: 0.70rem; font-weight: 700;">Today</span>
                                <span class="fw-bolder fs-5 text-dark d-flex justify-content-center align-items-center gap-1">
                                    {{ $todayAssigned }}
                                    @if($todayAssigned > 0)
                                        <i class="fas fa-fire text-danger" style="font-size: 0.8rem;" title="Active Today"></i>
                                    @endif
                                </span>
                            </div>
                            {{-- عدد قيد التجهيز --}}
                            <div class="col-4 border-end">
                                <span class="d-block text-muted mb-1"
                                    style="font-size: 0.70rem; font-weight: 700;">Preparing</span>
                                <span class="fw-bolder fs-5 text-primary">
                                    {{ $designer->preparing_orders ?? 0 }}
                                </span>
                            </div>
                            {{-- الإجمالي --}}
                            <div class="col-4">
                                <span class="d-block text-muted mb-1" style="font-size: 0.70rem; font-weight: 700;">Total</span>
                                <span class="fw-bolder fs-5 text-dark">{{ $total }}</span>
                            </div>
                        </div>

                        {{-- نسبة الإنجاز --}}
                        <div class="d-flex justify-content-between align-items-end mb-2">
                            <span class="text-muted fw-semibold" style="font-size: 0.75rem;">Completed ({{ $done }} /
                                {{ $total }})</span>
                            <span class="fw-bold"
                                style="font-size: 0.85rem; color: var(--bs-{{ str_replace('bg-', '', $colorClass) }});">{{ $percent }}%</span>
                        </div>
                        <div class="progress rounded-pill bg-light shadow-inner" style="height: 8px;">
                            <div class="progress-bar {{ $colorClass }} rounded-pill" role="progressbar"
                                style="width: {{ $percent }}%;" aria-valuenow="{{ $percent }}" aria-valuemin="0"
                                aria-valuemax="100">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5 bg-white rounded-4 shadow-sm border border-dashed">
                <i class="fas fa-users-slash text-muted mb-3" style="font-size: 3rem; opacity: 0.5;"></i>
                <h5 class="text-dark fw-bold">No designers found.</h5>
            </div>
        @endforelse
    </div>

    {{-- first 4 users --}}
    <div class="row">
        @foreach($recentUsers as $user)
            @php
                $colors = ['text-warning', 'text-pink', 'text-success', 'text-primary'];
                $colorClass = $colors[$loop->index % count($colors)];
            @endphp

            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body widget-user">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 avatar-lg me-3">
                                @if($user->image)
                                    <img src="{{ asset('storage/' . $user->image) }}" class="img-fluid rounded-circle"
                                        alt="User Image" style="width: 64px; height: 64px; object-fit: cover;">
                                @else
                                    @php
                                        $nameParts = explode(' ', $user->name);
                                        $initials = collect($nameParts)
                                            ->filter(fn($part) => strlen($part) > 0)
                                            ->take(2)
                                            ->map(fn($part) => strtoupper(substr($part, 0, 1)))
                                            ->implode('');
                                    @endphp
                                    <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center"
                                        style="width: 64px; height: 64px; font-weight: 600; font-size: 1rem;">
                                        {{ $initials ?: 'U' }}
                                    </div>
                                @endif
                            </div>

                            <div class="flex-grow-1 overflow-hidden">
                                <h5 class="mt-0 mb-1">{{ $user->name }}</h5>
                                <p class="text-muted mb-2 font-13 text-truncate">{{ $user->email }}</p>
                                <small class="d-block text-truncate text-capitalize fw-bold {{ $colorClass }}"
                                    style="max-width: 150px;">
                                    {{ $user->role ?? 'No Role' }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <!-- end row -->



    <style>
        .card-enhanced {
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        .card-enhanced:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.08) !important;
        }

        .shadow-inner {
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.075);
        }
    </style>
    <!-- end scoreboard row -->
    @if(auth()->check() && auth()->user()->is_admin == 1)
        {{-- Orders Count by University chart --}}
        <div class="row">
            <div class="col">
                <div class="card shadow-lg border-0 w-100">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center" style="height: 600px;">
                        <h4 class="header-title mt-0">Orders Count by University</h4>
                        <canvas class="mt-2" id="schoolChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <!-- end row -->
    @endif
    <!-- Load Chart.js and Plugin in the Correct Order -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.2.1/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.1.0"></script>

    <script>
        // 🎯 نفس تعريف الحالات المستخدمة في صفحة الطلبات
        const statusConfig = {
            Pending: {
                label: 'تم التصميم',
                color: '#ffc107' // أصفر
            },
            Completed: {
                label: 'تم الاعتماد',
                color: '#0dcaf0' // سماوي
            },
            preparing: {
                label: 'قيد التجهيز',
                color: '#6f42c1' // بنفسجي
            },
            Received: {
                label: 'تم التسليم',
                color: '#198754' // أخضر
            },
            'Out for Delivery': {
                label: 'مرتجع',
                color: '#fd7e14' // أورانج
            },
            Canceled: {
                label: 'رفض الإستلام',
                color: '#800000' // مارون
            },
            error: {
                label: 'خطأ',
                color: '#dc3545' // أحمر
            }
        };
    </script>

    {{-- dashboard scripts --}}
    @include('admin.partials.dashboard_scripts')
    {{-- مودال التأكيد على إخفاء الملاحظات --}}
    {{-- مودال التأكيد المالي العصري --}}
    <div class="modal fade" id="confirmDismissModal" tabindex="-1" aria-hidden="true"
        style="direction: rtl; text-align: right;">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 24px; background: #fff;">
                <div class="modal-body p-4 text-center">
                    <div class="mb-3 text-warning">
                        <div class="bg-soft-warning rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm"
                            style="width: 80px; height: 80px;">
                            <i class="fas fa-exclamation-circle fa-3x"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold text-dark mb-2">تأكيد المراجعة؟</h4>
                    <p class="text-muted fw-semibold">بمجرد التأكيد، ستتم أرشفة هذه الملاحظات ولن تظهر في الداشبورد مرة
                        أخرى.</p>

                    <div class="d-grid gap-2 mt-4">
                        <button type="button" id="confirmDismissBtn"
                            class="btn btn-dark btn-sm rounded-pill fw-bold py-3 shadow-sm">
                            نعم، تمت المراجعة
                        </button>
                        <button type="button" class="btn btn-light btn-sm rounded-pill text-muted fw-bold border-0"
                            data-bs-dismiss="modal">
                            إلغاء التراجع
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let currentOrderId = null;
            let confirmModal = new bootstrap.Modal(document.getElementById('confirmDismissModal'));

            // عند تغيير حالة السويتش
            $(document).on('change', '.js-dismiss-notes-btn', function () {
                if (this.checked) {
                    currentOrderId = $(this).data('order-id');
                    this.checked = false; // نرجعه لحد ما يضغط تأكيد
                    confirmModal.show();
                }
            });

            // عند الضغط على زر "نعم" في مودال التأكيد
            document.getElementById('confirmDismissBtn').addEventListener('click', function () {
                if (!currentOrderId) return;

                fetch(`/dashboard/orders/${currentOrderId}/dismiss-notes`, {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            confirmModal.hide();
                            // إغلاق مودال الملاحظات المفتوح (إن وجد)
                            const openNotesModal = bootstrap.Modal.getInstance(document.getElementById(`notesModal-${currentOrderId}`));
                            if (openNotesModal) openNotesModal.hide();

                            // حذف السطر من الجدول بسلاسة
                            $(`tr:has(button[data-bs-target="#notesModal-${currentOrderId}"])`).fadeOut(600, function () {
                                $(this).remove();
                                if ($('tbody tr').length === 0) location.reload();
                            });
                        }
                    });
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // مراقبة تغيير التبويبات داخل مودال الملاحظات
            var modalTabs = document.querySelectorAll('.modal .nav-link[data-bs-toggle="tab"]');

            modalTabs.forEach(function (tab) {
                tab.addEventListener('shown.bs.tab', function (event) {
                    var targetId = event.target.getAttribute('href'); // مثلاً: #tab-design-12
                    var modal = event.target.closest('.modal');
                    var orderId = modal.getAttribute('id').split('-')[1]; // استخراج رقم الطلب
                    var executeBtn = document.getElementById('executeBtn-' + orderId);

                    if (executeBtn) {
                        // أخذ الرابط الأصلي بدون الـ Hash
                        var baseUrl = executeBtn.getAttribute('href').split('#')[0];

                        // تغيير الرابط حسب التاب المفتوح
                        if (targetId.includes('tab-design')) {
                            executeBtn.setAttribute('href', baseUrl + '#tab-graduate-info');
                        } else if (targetId.includes('tab-notebook')) {
                            executeBtn.setAttribute('href', baseUrl + '#tab-internal-book');
                        } else if (targetId.includes('tab-binding')) {
                            executeBtn.setAttribute('href', baseUrl + '#tab-binding');
                        }
                    }
                });
            });
        });
    </script>
@endsection