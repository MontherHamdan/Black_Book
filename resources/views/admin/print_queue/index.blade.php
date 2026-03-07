@extends('admin.layout')

@section('content')
    <style>
        .pq-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .pq-header-icon {
            width: 56px;
            height: 56px;
            border-radius: 1rem;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.5rem;
            box-shadow: 0 4px 14px rgba(99, 102, 241, 0.3);
        }

        .pq-card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            overflow: hidden;
        }

        .pq-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        }

        .pq-card .card-body {
            padding: 1.25rem 1.5rem;
        }

        .pq-order-id {
            font-size: 1.1rem;
            font-weight: 700;
            color: #4f46e5;
        }

        .pq-name {
            font-size: 0.95rem;
            font-weight: 600;
            color: #334155;
            margin-top: 0.25rem;
        }

        .pq-btn-view {
            border-radius: 50rem;
            padding: 0.4rem 1.2rem;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .pq-empty {
            text-align: center;
            padding: 4rem 2rem;
            color: #94a3b8;
        }

        .pq-empty i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.3;
        }

        .pq-count {
            background: rgba(99, 102, 241, 0.1);
            color: #4f46e5;
            padding: 0.3rem 0.8rem;
            border-radius: 50rem;
            font-size: 0.85rem;
            font-weight: 700;
        }

        .pq-select {
            border: 2px solid #e2e8f0;
            border-radius: 0.6rem;
            font-size: 0.82rem;
            font-weight: 600;
            padding: 0.35rem 0.6rem;
            cursor: pointer;
            transition: border-color 0.2s, box-shadow 0.2s;
            appearance: auto;
        }

        .pq-select:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
            outline: none;
        }

        .pq-select.is-printed {
            border-color: #10b981;
            background: rgba(16, 185, 129, 0.08);
            color: #059669;
        }

        .pq-card.printed-card {
            border-left: 4px solid #10b981;
            opacity: 0.7;
        }

        /* Toast */
        .pq-toast {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            z-index: 9999;
            padding: 0.8rem 1.5rem;
            border-radius: 0.75rem;
            font-weight: 600;
            font-size: 0.9rem;
            color: #fff;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
            transform: translateY(100px);
            opacity: 0;
            transition: all 0.35s ease;
        }

        .pq-toast.show {
            transform: translateY(0);
            opacity: 1;
        }

        .pq-toast.success {
            background: linear-gradient(135deg, #10b981, #059669);
        }

        .pq-toast.error {
            background: linear-gradient(135deg, #ef4444, #dc2626);
        }
    </style>

    <div class="pq-header">
        <div class="pq-header-icon">
            <i class="fas fa-print"></i>
        </div>
        <div>
            <h2 class="fw-bolder text-dark mb-0" style="letter-spacing: -0.5px;">Print Queues</h2>
            <p class="text-muted mb-0">الطلبات الجاهزة للطباعة (قيد التجهيز)</p>
        </div>
        <span class="pq-count ms-auto" id="pq-counter">{{ $orders->count() }} طلب</span>
    </div>

    @if($orders->isEmpty())
        <div class="card pq-card">
            <div class="pq-empty">
                <i class="fas fa-inbox d-block"></i>
                <p class="mb-0 fs-5">لا يوجد طلبات في قائمة الطباعة حالياً</p>
            </div>
        </div>
    @else
        <div class="row">
            @foreach($orders as $order)
                <div class="col-md-6 col-lg-4 col-xl-3 mb-4">
                    <div class="card pq-card h-100" id="pq-card-{{ $order->id }}">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <span class="pq-order-id">#{{ $order->id }}</span>
                                <select class="pq-select pq-status-select {{ $order->status === 'Printed' ? 'is-printed' : '' }}"
                                    data-order-id="{{ $order->id }}">
                                    <option value="preparing" {{ $order->status === 'preparing' ? 'selected' : '' }}>قيد التجهيز
                                    </option>
                                    <option value="Printed" {{ $order->status === 'Printed' ? 'selected' : '' }}>تم الطباعة ✓</option>
                                </select>
                            </div>
                            <p class="pq-name mb-1">
                                <i class="fas fa-user-graduate text-muted me-1"></i>
                                {{ $order->username_ar ?? 'غير متوفر' }}
                            </p>
                            <p class="text-muted small mb-auto">
                                <i class="far fa-clock me-1"></i>
                                {{ $order->updated_at ? $order->updated_at->diffForHumans() : '' }}
                            </p>
                            <div class="mt-3">
                                <a href="{{ route('orders.show', $order->id) }}"
                                    class="btn btn-outline-primary btn-sm pq-btn-view w-100">
                                    <i class="fas fa-eye me-1"></i> عرض التفاصيل
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Toast container --}}
    <div id="pq-toast" class="pq-toast"></div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

            function showToast(message, type = 'success') {
                const toast = document.getElementById('pq-toast');
                toast.textContent = message;
                toast.className = 'pq-toast ' + type + ' show';
                setTimeout(() => toast.classList.remove('show'), 3000);
            }

            document.querySelectorAll('.pq-status-select').forEach(function (select) {
                select.addEventListener('change', function () {
                    const orderId = this.dataset.orderId;
                    const newStatus = this.value;
                    const card = document.getElementById('pq-card-' + orderId);
                    const selectEl = this;

                    selectEl.disabled = true;

                    fetch("{{ route('orders.updateStatus') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ id: orderId, status: newStatus })
                    })
                        .then(res => res.json().then(data => ({ ok: res.ok, data })))
                        .then(({ ok, data }) => {
                            selectEl.disabled = false;

                            if (ok && data.success !== false) {
                                if (newStatus === 'Printed') {
                                    showToast('✓ تم تحديث الحالة إلى "تم الطباعة"', 'success');
                                    // إزالة البطاقة من الصفحة
                                    const col = card.closest('.col-md-6');
                                    card.style.transition = 'opacity 0.4s, transform 0.4s';
                                    card.style.opacity = '0';
                                    card.style.transform = 'scale(0.9)';
                                    setTimeout(() => {
                                        col.remove();
                                        const remaining = document.querySelectorAll('.pq-status-select').length;
                                        const counter = document.getElementById('pq-counter');
                                        if (counter) counter.textContent = remaining + ' طلب';
                                        if (remaining === 0) location.reload();
                                    }, 450);
                                } else {
                                    card.classList.remove('printed-card');
                                    selectEl.classList.remove('is-printed');
                                    showToast('✓ تم تحديث الحالة إلى "قيد التجهيز"', 'success');
                                }
                            } else {
                                // revert
                                selectEl.value = newStatus === 'Printed' ? 'preparing' : 'Printed';
                                showToast(data.message || 'حدث خطأ أثناء التحديث', 'error');
                            }
                        })
                        .catch(() => {
                            selectEl.disabled = false;
                            selectEl.value = newStatus === 'Printed' ? 'preparing' : 'Printed';
                            showToast('حدث خطأ في الاتصال', 'error');
                        });
                });
            });
        });
    </script>
@endsection