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
    </style>

    {{-- order cards --}}
    @include('admin.partials.order_cards')

    {{-- order charts --}}
    @include('admin.partials.order_charts')


    {{-- 🔹 قسم المصمم: جدول الملاحظات + كارد الأرباح --}}
    @if(auth()->check() && auth()->user()->is_admin == 0 && auth()->user()->role === 'designer')
        <div class="row mt-4">
            {{-- 1. جدول ملاحظات المتابعة (يأخذ المساحة الأكبر) --}}
            <div class="col-xl-8 col-lg-8 mb-4">
                <div class="card card-enhanced border-primary border-start border-4 h-100">
                    <div class="card-body">
                        <h4 class="header-title mb-3 text-primary">
                            <i class="fas fa-clipboard-list me-1"></i> ملاحظات المتابعة على طلباتك
                        </h4>
                        <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                            <table class="table table-hover table-centered mb-0 align-middle">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th>رقم الطلب</th>
                                        <th>اسم الخريج</th>
                                        <th class="text-center">الملاحظات</th>
                                        <th class="text-end">الإجراء</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($designerNotes as $noteOrder)
                                        <tr>
                                            <td>
                                                <a href="{{ route('orders.show', $noteOrder->id) }}" class="fw-bold text-primary">
                                                    #{{ $noteOrder->id }}
                                                </a>
                                            </td>
                                            <td>
                                                <a href="{{ route('orders.show', $noteOrder->id) }}" class="text-body fw-semibold">
                                                    {{ $noteOrder->username_ar }}
                                                </a>
                                            </td>
                                            <td class="text-center">
                                                {{-- زر فتح المودال --}}
                                                <button type="button" class="btn btn-sm btn-info rounded-pill text-white"
                                                    data-bs-toggle="modal" data-bs-target="#notesModal-{{ $noteOrder->id }}">
                                                    <i class="fas fa-eye me-1"></i> عرض الملاحظات
                                                </button>
                                            </td>
                                            <td class="text-end">
                                                <a href="{{ route('orders.show', $noteOrder->id) }}"
                                                    class="btn btn-sm btn-outline-primary rounded-pill">
                                                    الذهاب للطلب <i class="fas fa-arrow-left ms-1"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-4">
                                                <i class="fas fa-check-circle fs-3 text-success mb-2 d-block"></i>
                                                لا توجد ملاحظات متابعة لطلباتك حالياً. عمل ممتاز!
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. كارد إجمالي الأرباح/العمولات (يأخذ المساحة المتبقية) --}}
            <div class="col-xl-4 col-lg-4 mb-4">
                <div class="card card-enhanced border-success border-start border-4 h-100 bg-light">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center text-center">
                        <div class="icon-circle bg-success text-white mb-3" style="width: 70px; height: 70px; font-size: 30px;">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <h4 class="text-muted mb-2">إجمالي عمولاتك</h4>
                        <h2 class="text-success fw-bold mb-0">
                            {{ number_format($totalCommission, 2) }} <small class="fs-5 text-muted">دينار</small>
                        </h2>
                        <p class="text-muted mt-3 small">
                            * يتم احتساب العمولة للطلبات المنجزة (قيد التجهيز فأعلى).
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- 💡 نقلنا كل الـ Modals لبرّا الجدول لتجنب الـ DOM conflicts والترميش --}}
        @foreach($designerNotes as $noteOrder)
            <div class="modal fade" id="notesModal-{{ $noteOrder->id }}" tabindex="-1"
                aria-labelledby="notesModalLabel-{{ $noteOrder->id }}" aria-hidden="true"
                style="direction: rtl; text-align: right;">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header d-flex justify-content-between align-items-center">
                            <h5 class="modal-title text-primary mb-0" id="notesModalLabel-{{ $noteOrder->id }}">
                                <i class="fas fa-clipboard-list me-2"></i>
                                ملاحظات المتابعة - طلب #{{ $noteOrder->id }}
                            </h5>
                            <button type="button" class="btn-close m-0"
                                style="margin-right: auto !important; margin-left: 0 !important;" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body bg-light">
                            <div class="note-box auto-dir bg-white p-3 rounded shadow-sm border" dir="auto"
                                style="min-height: 100px; max-height: 400px; overflow-y: auto;">
                                {!! nl2br(e($noteOrder->design_followup_note)) !!}
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                            <a href="{{ route('orders.show', $noteOrder->id) }}" class="btn btn-primary">
                                الذهاب للطلب
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif

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
                                <small class="d-block text-truncate {{ $colorClass }}" style="max-width: 150px;">
                                    {{ $user->title ?? 'No Title' }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <!-- end row -->

    {{-- 🔹 Designers Scoreboard --}}
    <div class="row mt-4">
        <div class="col-12">
            <h4 class="mb-3">Designers Scoreboard</h4>
        </div>

        @forelse($designersScoreboard as $designer)
            @php
                $total = (int) $designer->total_orders;
                $done = (int) $designer->completed_orders;
                $percent = $total > 0 ? round(($done / $total) * 100) : 0;

                $colors = ['bg-primary', 'bg-success', 'bg-info', 'bg-warning', 'bg-danger'];
                $colorClass = $colors[$loop->index % count($colors)];
            @endphp

            <div class="col-xl-3 col-md-6">
                <div class="card card-enhanced">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <div class="flex-shrink-0 avatar-md me-3">
                                @if($designer->image)
                                    <img src="{{ asset('storage/' . $designer->image) }}" class="img-fluid rounded-circle"
                                        alt="Designer Image" style="width: 56px; height: 56px; object-fit: cover;">
                                @else
                                    @php
                                        $nameParts = explode(' ', $designer->name);
                                        $initials = collect($nameParts)
                                            ->filter(fn($part) => strlen($part) > 0)
                                            ->take(2)
                                            ->map(fn($part) => strtoupper(substr($part, 0, 1)))
                                            ->implode('');
                                    @endphp
                                    <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center"
                                        style="width: 56px; height: 56px; font-weight: 600; font-size: 0.95rem;">
                                        {{ $initials ?: 'U' }}
                                    </div>
                                @endif
                            </div>

                            <div class="flex-grow-1">
                                <h5 class="mt-0 mb-1">{{ $designer->name }}</h5>
                                <p class="mb-1 text-muted small">Total Assigned: {{ $total }}</p>
                                <p class="mb-0">
                                    <strong>{{ $done }}</strong> / {{ $total }} completed
                                </p>
                            </div>
                        </div>

                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar {{ $colorClass }}" role="progressbar" style="width: {{ $percent }}%;"
                                aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>

                        <div class="mt-1 text-end">
                            <small class="text-muted">{{ $percent }}%</small>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <p class="text-muted">No designers found.</p>
            </div>
        @endforelse
    </div>
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
@endsection