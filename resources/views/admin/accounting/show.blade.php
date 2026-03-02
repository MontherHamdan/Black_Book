@extends('admin.layout')

@section('content')
    <div class="container-fluid mt-4" style="direction: rtl; text-align: right; font-family: 'Cairo', sans-serif;">

        {{-- Header & Back Button --}}
        <div class="row align-items-center mb-4">
            <div class="col-md-6">
                <h2 class="fw-bold text-dark mb-0">كشف الحساب المالي</h2>
                <p class="text-muted">سجل العمولات والمدفوعات للمصمم: <span
                        class="text-primary fw-bold">{{ $user->name }}</span></p>
            </div>
            <div class="col-md-6 text-md-start">
                <a href="{{ route('designer-accounting.index') }}" class="btn btn-light rounded-pill px-4 shadow-sm border">
                    <i class="fas fa-chevron-right me-1"></i> رجوع
                </a>
            </div>
        </div>

        <div class="row">
            {{-- Side Summary --}}
            <div class="col-lg-4 mb-4">
                <div class="card border-0 shadow-lg rounded-4 p-4 text-center bg-dark text-white h-100">
                    <div class="avatar-xl mx-auto mb-3">
                        @if($user->image)
                            <img src="{{ asset('storage/' . $user->image) }}"
                                class="rounded-circle shadow-lg border border-3 border-secondary"
                                style="width: 100px; height: 100px; object-fit: cover;">
                        @else
                            <div class="rounded-circle bg-secondary mx-auto d-flex align-items-center justify-content-center fw-bold fs-1 shadow-lg"
                                style="width: 100px; height: 100px;">
                                {{ mb_substr($user->name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    <h4 class="fw-bold mb-1 text-white">{{ $user->name }}</h4>
                    <p class="text-light opacity-75 mb-4">{{ $user->email }}</p>

                    <div
                        class="bg-white bg-opacity-10 rounded-4 p-3 mb-4 border border-white border-opacity-10 shadow-inner">
                        <p class="small text-light opacity-50 mb-1 text-uppercase fw-bold">الرصيد المعلق</p>
                        <h1 class="display-5 fw-bolder mb-0 text-warning">{{ number_format($totalUnpaid, 2) }}</h1>
                        <small class="text-light">دينار أردني</small>
                    </div>

                    @if($totalUnpaid > 0)
                        <div class="d-flex flex-column gap-2">
                            <form action="{{ route('designer-accounting.settle', $user->id) }}" method="POST"
                                onsubmit="return confirm('تأكيد تسوية جميع المستحقات وتصفير الرصيد بالكامل؟');">
                                @csrf
                                <button type="submit" class="btn btn-warning w-100 rounded-4 fw-bold py-3 shadow">
                                    <i class="fas fa-check-double me-2"></i> تسوية الحساب بالكامل
                                </button>
                            </form>

                            <button type="button"
                                class="btn btn-outline-warning text-white border-warning w-100 rounded-4 fw-bold py-2 shadow-sm"
                                data-bs-toggle="modal" data-bs-target="#customSettleModal"
                                style="background-color: rgba(255, 193, 7, 0.1);">
                                <i class="fas fa-hand-holding-usd me-2"></i> تسديد دفعة مخصصة
                            </button>
                        </div>
                    @else
                        <div
                            class="alert alert-soft-success bg-success bg-opacity-20 border-0 text-success rounded-4 p-3 fw-bold">
                            <i class="fas fa-check-double me-1"></i> الرصيد مُسدد بالكامل
                        </div>
                    @endif
                </div>
            </div>

            {{-- Transactions List --}}
            <div class="col-lg-8">
                <div class="card border-0 shadow-lg rounded-4 h-100 overflow-hidden bg-white">
                    <div class="card-header bg-white p-0 border-0">
                        <ul class="nav nav-pills nav-fill bg-light p-2 m-3 rounded-4" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active fw-bold py-3 rounded-4" id="unpaid-tab" data-bs-toggle="tab"
                                    href="#unpaid" role="tab">
                                    <i class="fas fa-clock me-2"></i> مستحقات معلقة
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link fw-bold py-3 rounded-4" id="paid-tab" data-bs-toggle="tab" href="#paid"
                                    role="tab">
                                    <i class="fas fa-history me-2"></i> أرشيف الدفعات
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="card-body p-0">
                        <div class="tab-content">
                            {{-- Tab 1 --}}
                            <div class="tab-pane fade show active" id="unpaid" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-hover table-borderless align-middle mb-0">
                                        <thead class="bg-light text-muted">
                                            <tr class="small text-uppercase fw-bold">
                                                <th class="px-4 py-3">الطلب</th>
                                                <th>اسم الخريج</th>
                                                <th>التاريخ</th>
                                                <th class="text-start px-4">العمولة</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($unpaidOrders as $order)
                                                <tr class="border-bottom border-light">
                                                    <td class="px-4"><span
                                                            class="badge bg-soft-dark text-dark fs-6 px-3">#{{ $order->id }}</span>
                                                    </td>
                                                    <td><span class="fw-bold">{{ $order->username_ar }}</span></td>
                                                    <td class="small text-muted">{{ $order->updated_at->format('d/m/Y') }}</td>
                                                    <td class="text-start px-4 text-danger fw-bolder fs-5">{{ number_format($order->designer_commission - $order->paid_commission, 2) }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center py-5 text-muted"><i
                                                            class="fas fa-info-circle me-1"></i> لا توجد عمولات بانتظار الصرف
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- Tab 2 --}}
                            <div class="tab-pane fade" id="paid" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-hover table-borderless align-middle mb-0 text-center">
                                        <thead class="bg-light">
                                            <tr class="small text-uppercase fw-bold">
                                                <th class="py-3 px-4 text-end">الطلب</th>
                                                <th>الخريج</th>
                                                <th>تاريخ التسوية</th>
                                                <th class="text-start px-4">المبلغ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($paidOrders as $order)
                                                <tr class="border-bottom border-light">
                                                    <td class="px-4 text-end"><span
                                                            class="badge bg-soft-success text-success fs-6 px-3">#{{ $order->id }}</span>
                                                    </td>
                                                    <td>{{ $order->username_ar }}</td>
                                                    <td class="small text-muted">
    @if($order->commission_paid_at)
        {{ \Carbon\Carbon::parse($order->commission_paid_at)->format('d/m/Y H:i') }}
    @else
        <span class="text-warning">دفعة جزئية</span>
    @endif
</td>
                                                 <td class="text-start px-4 text-success fw-bolder fs-5">{{ number_format($order->paid_commission, 2) }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center py-5 text-muted">لم يتم دفع أي عمولات بعد
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<div class="modal fade" id="customSettleModal" tabindex="-1" aria-hidden="true" style="direction: rtl; text-align: right;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <form action="{{ route('designer-accounting.customSettle', $user->id) }}" method="POST">
                @csrf
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold text-dark">تسديد دفعة مخصصة للمصمم</h5>
                    <button type="button" class="btn-close m-0" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-info rounded-3 mb-4">
                        <i class="fas fa-info-circle me-1"></i> 
                        الرصيد المعلق الحالي: <strong>{{ number_format($totalUnpaid, 2) }} دينار</strong><br>
                        <small>سيقوم النظام بخصم هذا المبلغ من أقدم الطلبات غير المدفوعة تلقائياً.</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">المبلغ المراد تسديده (دينار أردني)</label>
                        <div class="input-group input-group-lg">
                            <input type="number" name="custom_amount" class="form-control" step="0.01" min="0.1" max="{{ $totalUnpaid }}" required placeholder="أدخل المبلغ هنا...">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">
                        <i class="fas fa-save me-1"></i> تأكيد الدفع
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
    <style>
        .bg-soft-dark {
            background-color: rgba(52, 58, 64, 0.1);
        }

        .bg-soft-success {
            background-color: rgba(25, 135, 84, 0.1);
        }

        .shadow-inner {
            box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.06);
        }

        .nav-pills .nav-link.active {
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .avatar-xl img {
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);
        }
    </style>
@endsection