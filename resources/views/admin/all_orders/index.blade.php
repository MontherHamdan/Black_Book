@extends('admin.layout')

@section('content')
    <style>
        .ao-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .ao-header-icon {
            width: 56px;
            height: 56px;
            border-radius: 1rem;
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.5rem;
            box-shadow: 0 4px 14px rgba(59, 130, 246, 0.3);
        }
        .ao-count {
            background: rgba(59, 130, 246, 0.1);
            color: #2563eb;
            padding: 0.3rem 0.8rem;
            border-radius: 50rem;
            font-size: 0.85rem;
            font-weight: 700;
        }
        .ao-table {
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        }
        .ao-table thead th {
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
            font-weight: 700;
            font-size: 0.82rem;
            color: #475569;
            border-bottom: 2px solid #e2e8f0;
            padding: 0.75rem 1rem;
            white-space: nowrap;
        }
        .ao-table tbody td {
            padding: 0.7rem 1rem;
            font-size: 0.88rem;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
        }
        .ao-table tbody tr:hover { background-color: #fafbfc; }
        .ao-order-id { font-weight: 700; color: #4f46e5; }
        .ao-status {
            display: inline-block;
            padding: 0.25rem 0.65rem;
            border-radius: 50rem;
            font-size: 0.72rem;
            font-weight: 700;
        }
        .ao-st-new_order { background: rgba(59,130,246,0.1); color: #2563eb; }
        .ao-st-needs_modification { background: rgba(239,68,68,0.1); color: #dc2626; }
        .ao-st-Pending { background: rgba(245,158,11,0.1); color: #d97706; }
        .ao-st-Completed { background: rgba(16,185,129,0.1); color: #059669; }
        .ao-st-preparing { background: rgba(168,85,247,0.1); color: #9333ea; }
        .ao-st-Printed { background: rgba(99,102,241,0.1); color: #4f46e5; }
        .ao-st-Received { background: rgba(20,184,166,0.1); color: #0d9488; }
        .ao-st-Out_for_Delivery { background: rgba(249,115,22,0.1); color: #ea580c; }
        .ao-st-Canceled { background: rgba(107,114,128,0.1); color: #4b5563; }
        .ao-btn { border-radius: 50rem; padding: 0.3rem 0.9rem; font-size: 0.8rem; font-weight: 600; }
        .ao-empty { text-align: center; padding: 4rem 2rem; color: #94a3b8; }
        .ao-empty i { font-size: 3rem; margin-bottom: 1rem; opacity: 0.3; }
    </style>

    @php
        $statusLabels = [
            'new_order' => 'طلب جديد',
            'needs_modification' => 'يوجد تعديل',
            'Pending' => 'تم التصميم',
            'Completed' => 'تم الاعتماد',
            'preparing' => 'قيد التجهيز',
            'Printed' => 'تم الطباعة',
            'Received' => 'تم التسليم',
            'out_for_delivery' => 'خرج مع التوصيل',
            'returned' => 'مرتجع',
            'Canceled' => 'رفض الإستلام',
        ];
    @endphp

    <div class="ao-header">
        <div class="ao-header-icon">
            <i class="fas fa-list-alt"></i>
        </div>
        <div>
            <h2 class="fw-bolder text-dark mb-0" style="letter-spacing: -0.5px;">جميع الطلبات - All Orders</h2>
            <p class="text-muted mb-0">عرض شامل لجميع الطلبات</p>
        </div>
        <span class="ao-count ms-auto">{{ $orders->total() }} طلب</span>
    </div>

    @if($orders->isEmpty())
        <div class="card ao-table">
            <div class="ao-empty">
                <i class="fas fa-inbox d-block"></i>
                <p class="mb-0 fs-5">لا يوجد طلبات حالياً</p>
            </div>
        </div>
    @else
        <div class="card ao-table">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>اسم الخريج</th>
                            <th>نوع الكتاب</th>
                            <th>المصمم</th>
                            <th>الحالة</th>
                            <th>المحافظة</th>
                            <th>السعر</th>
                            <th>التاريخ</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            @php
                                $stClass = 'ao-st-' . str_replace(' ', '_', $order->status);
                                $stLabel = $statusLabels[$order->status] ?? $order->status;
                            @endphp
                            <tr>
                                <td><span class="ao-order-id">{{ $order->id }}</span></td>
                                <td>{{ $order->username_ar ?? '—' }}</td>
                                <td>{{ $order->bookType->name_ar ?? '—' }}</td>
                                <td>{{ $order->designer->name ?? '—' }}</td>
                                <td><span class="ao-status {{ $stClass }}">{{ $stLabel }}</span></td>
                                <td>{{ $order->governorate->name_ar ?? '—' }}</td>
                                <td class="fw-bold text-success">{{ $order->final_price_with_discount ?? $order->final_price ?? 0 }} د.أ</td>
                                <td class="text-muted small">{{ $order->created_at ? $order->created_at->format('Y-m-d') : '' }}</td>
                                <td>
                                    <a href="{{ route('orders.show', $order->id) }}" class="btn btn-outline-primary btn-sm ao-btn">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="d-flex justify-content-end mt-3">
            {{ $orders->links() }}
        </div>
    @endif
@endsection
