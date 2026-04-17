@extends('admin.layout')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Google Font for Premium Arabic Typography --}}
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">

    <div class="delivery-dashboard" dir="rtl" style="font-family: 'Cairo', sans-serif;">
        <div class="row">
            <div class="col-12">
                {{-- 🌟 Premium Hero Header 🌟 --}}
                <div class="card border-0 mb-4 overflow-hidden shadow-sm"
                    style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border-radius: 1rem;">
                    <div class="card-body p-4 p-md-5 text-white position-relative">
                        {{-- Decorative Element --}}
                        <div class="position-absolute top-0 start-0 opacity-10" style="transform: translate(-10%, -10%);">
                        </div>

                        <div class="row align-items-center position-relative z-1">
                            <div class="col-md-7 mb-4 mb-md-0">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-3 d-flex align-items-center justify-content-center ms-4 shadow-sm"
                                        style="width: 75px; height: 75px; background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255,255,255,0.15);">
                                        <i class="fas fa-shipping-fast fs-2 text-white"></i>
                                    </div>
                                    <div>
                                        <h2 class="mb-2 fw-bold text-white">إدارة التوصيل والمتابعة</h2>
                                        <p class="mb-0 text-white-50 fs-6">تتبع وتحديث حالات طلبات التوصيل الفردية
                                            والمجموعات بضغطة زر.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="d-flex justify-content-md-end gap-3 text-center">
                                    <div class="rounded-3 p-3 shadow-sm"
                                        style="min-width: 130px; background: rgba(0, 0, 0, 0.2); border: 1px solid rgba(255,255,255,0.1);">
                                        <div class="fs-2 fw-bolder text-white mb-1">{{ $paginatedOrders->count() }}</div>
                                        <div class="small text-white-50 fw-bold">إجمالي الطلبات</div>
                                    </div>
                                    <div class="rounded-3 p-3 shadow-sm"
                                        style="min-width: 130px; background: rgba(0, 0, 0, 0.2); border: 1px solid rgba(255,255,255,0.1);">
                                        <div class="fs-2 fw-bolder text-white mb-1">{{ $deliveryGroups->count() }}</div>
                                        <div class="small text-white-50 fw-bold">عدد المجموعات</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 📦 Delivery Grid - Ultra Modern Floating UI 📦 --}}
                <div class="d-flex justify-content-between align-items-center mb-3 px-2 mt-4">
                    <h5 class="fw-bold text-slate-700 m-0"><i class="fas fa-list-ul me-2 text-primary"></i> قائمة الشحنات</h5>
                    <button id="bulkPrintBtn" class="btn btn-info text-white fw-bold shadow-sm px-4 rounded-pill" disabled style="background-color: #0dcaf0; border-color: #0dcaf0;">
                        <i class="fas fa-print me-1"></i> طباعة المحدد (<span id="printCount">0</span>)
                    </button>
                </div>
                <div class="table-responsive" style="min-height: 500px; padding: 5px;">
                    <table class="table custom-ui-table align-middle w-100 mb-0">
                        <thead>
                            <tr>
                                <th width="5%" class="text-center py-3 border-0">
                                    <input class="form-check-input shadow-sm" type="checkbox" id="selectAllDeliveries" style="transform: scale(1.2); cursor: pointer;" title="تحديد الكل للطباعة">
                                </th>
                                <th class="text-muted fw-bold py-3 border-0 fs-6" width="30%">المجموعة / الطلبات التابعة
                                </th>
                                <th class="text-center text-muted fw-bold py-3 border-0 fs-6">الكمية</th>
                                <th class="text-muted fw-bold py-3 border-0 fs-6">العنوان والتواصل</th>
                                <th class="text-start text-muted fw-bold py-3 border-0 fs-6">الإجمالي</th>
                                <th class="text-center text-muted fw-bold py-3 border-0 fs-6" width="18%">تحديث الحالة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($deliveryGroups as $groupKey => $ordersGroup)
                                @php
                                    $isGroup = str_starts_with($groupKey, 'group_');
                                    $discountCodeName = $isGroup && $ordersGroup->first()->discountCode
                                        ? ($ordersGroup->first()->discountCode->code_name ?? $ordersGroup->first()->discountCode->discount_code)
                                        : current(array_filter([$ordersGroup->first()->username_ar, $ordersGroup->first()->username_en]));

                                    $totalBooks = $ordersGroup->count();
                                    $masterPhoneOne = $ordersGroup->first()->delivery_number_one;
                                    $masterPhoneTwo = $ordersGroup->first()->delivery_number_two;
                                    $masterAddress = current(array_filter([$ordersGroup->first()->governorate->name_ar, $ordersGroup->first()->address]));
                                    $aggregatePrice = $ordersGroup->sum('final_price_with_discount');
                                    $hasMultiple = $totalBooks > 1;
                                    $currentStatus = $ordersGroup->first()->status;

                                    $statusOptions = [
                                        'out_for_delivery' => 'خرج مع التوصيل',
                                        'Received' => 'تم التسليم',
                                        'returned' => 'مرتجع',
                                        'Canceled' => 'رفض الاستلام',
                                    ];

                                    // استخراج جميع أرقام الطلبات في هذه المجموعة
                                    $orderIds = $ordersGroup->pluck('id')->toArray();
                                @endphp

                                {{-- 💳 Parent Row 💳 --}}
                                <tr class="ui-row bg-white">
                                 <td class="text-center py-4 position-relative">
                                        <div class="position-absolute top-0 bottom-0 end-0"
                                            style="width: 4px; background: {{ $isGroup ? '#3b82f6' : '#94a3b8' }}; border-radius: 0 12px 12px 0;">
                                        </div>
                                        
                                        <div class="d-flex align-items-center justify-content-center gap-2">
                                            {{-- حماية: نظهر مربع التحديد فقط للشحنات المترحلة --}}
                                            @if(in_array($currentStatus, ['out_for_delivery', 'Received', 'returned', 'Canceled']))
                                                <input class="form-check-input delivery-checkbox shadow-sm" type="checkbox" value="{{ json_encode($orderIds) }}" style="transform: scale(1.2); cursor: pointer;">
                                            @else
                                                <span title="يجب تحويل الحالة لـ 'خرج مع التوصيل' أولاً"><i class="fas fa-lock text-muted opacity-25"></i></span>
                                            @endif

                                            @if($hasMultiple)
                                            <button class="btn btn-sm text-secondary expand-btn" data-bs-toggle="collapse"
                                                data-bs-target="#collapse-{{ $groupKey }}" aria-expanded="false"
                                                title="عرض التفاصيل">
                                                <i class="fas fa-chevron-down"></i>
                                            </button>
                                        @else
                                            <div class="d-inline-flex align-items-center justify-content-center text-muted rounded-circle"
                                                style="width: 32px; height: 32px; background: #f1f5f9;">
                                                <i class="fas fa-user fs-6"></i>
                                            </div>
                                        @endif
                                    </td>

                                    <td class="py-4">
                                        <div class="d-flex flex-column gap-2">
                                            {{-- Group / User Name --}}
                                            <div class="fw-bold fs-5 text-dark">
                                                @if($isGroup)
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="d-flex align-items-center justify-content-center rounded"
                                                            style="width: 36px; height: 36px; background: #eff6ff; color: #3b82f6;">
                                                            <i class="fas fa-layer-group"></i>
                                                        </div>
                                                        <span>{{ $discountCodeName }}</span>
                                                    </div>
                                                @else
                                                    <div class="d-flex align-items-center gap-2">
                                                        <span>{{ $discountCodeName }}</span>
                                                    </div>
                                                @endif
                                            </div>

                                            {{-- Show Order IDs explicitly --}}
                                            <div class="d-flex flex-wrap gap-1 mt-1">
                                                @foreach($orderIds as $id)
                                                    <span class="badge bg-slate-100 text-slate-500 border"
                                                        style="font-size: 0.75rem; padding: 4px 8px;">
                                                        #{{ $id }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    </td>

                                    <td class="text-center py-4">
                                        <span
                                            class="badge {{ $hasMultiple ? 'bg-slate-800 text-white' : 'bg-slate-100 text-slate-600' }} px-3 py-2 fs-6 rounded-pill fw-bold">
                                            {{ $totalBooks }}
                                        </span>
                                    </td>

                                    <td class="py-4">
                                        <div class="d-flex flex-column gap-2">
                                            @if($masterAddress)
                                                <div class="text-slate-700 fw-bold fs-6 d-flex align-items-center gap-2">
                                                    <i class="fas fa-map-marker-alt text-danger opacity-75"></i>
                                                    <span>{{ $masterAddress }}</span>
                                                </div>
                                            @endif
                                            <div class="d-flex flex-wrap gap-3 mt-1">
                                                @if($masterPhoneOne)
                                                    <a href="{{ $ordersGroup->first()->whatsapp_link }}" target="_blank"
                                                        class="contact-link text-decoration-none fw-bold d-flex align-items-center gap-1">
                                                        <i class="fab fa-whatsapp text-success fs-5"></i>
                                                        <span dir="ltr">{{ $masterPhoneOne }}</span>
                                                    </a>
                                                @endif
                                                @if($masterPhoneTwo)
                                                    <a href="{{ $ordersGroup->first()->whatsapp_link_two }}" target="_blank"
                                                        class="contact-link text-decoration-none fw-bold d-flex align-items-center gap-1">
                                                        <i class="fab fa-whatsapp text-success fs-5"></i>
                                                        <span dir="ltr">{{ $masterPhoneTwo }}</span>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    <td class="text-start py-4">
                                        <div class="fw-bolder fs-4 text-dark">
                                            {{ number_format($aggregatePrice, 2) }} <span
                                                class="fs-6 text-muted fw-bold">د.أ</span>
                                        </div>
                                    </td>

                                    <td class="text-center ps-3 py-4">
                                        {{-- 🚀 Auto-updating Status Select (No Button) 🚀 --}}
                                        <div class="d-flex align-items-center justify-content-center">
                                            <select class="form-select shadow-none fw-bold border text-dark status-dropdown"
                                                style="width: 100%; min-width: 140px; border-radius: 8px; cursor: pointer; border-color: #cbd5e1; background-color: #f8fafc;"
                                                data-original-value="{{ $currentStatus }}"
                                                onchange="updateGroupStatus(this, {{ json_encode($orderIds) }})">
                                                <option value="" disabled>اختر الحالة...</option>
                                                @foreach($statusOptions as $key => $label)
                                                    <option value="{{ $key }}" {{ $currentStatus == $key ? 'selected' : '' }}>
                                                        {{ $label }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            {{-- زر الطباعة الفردي --}}
                                            @if(in_array($currentStatus, ['out_for_delivery', 'Received', 'returned', 'Canceled']))
                                                <button class="btn btn-info text-white shadow-sm ms-2 print-single-btn" data-ids="{{ json_encode($orderIds) }}" title="طباعة بوليصة الشحن" style="border-radius: 8px;">
                                                    <i class="fas fa-print"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>

                                {{-- 🔽 Child Row (Expanded Inner Details) 🔽 --}}
                                @if($hasMultiple)
                                    <tr id="collapse-{{ $groupKey }}" class="collapse inner-row">
                                        <td colspan="6" class="p-0 border-0">
                                            <div class="mx-4 mb-4 mt-2 rounded-4 p-4"
                                                style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                                <h6 class="fw-bold text-slate-500 mb-3 fs-6 d-flex align-items-center gap-2">
                                                    <i class="fas fa-bars-staggered"></i> تفاصيل محتويات المجموعة
                                                </h6>
                                                <div class="table-responsive">
                                             <table class="table table-borderless align-middle mb-0">
    <thead style="border-bottom: 2px solid #e2e8f0;">
        <tr>
            <th class="text-slate-400 fw-bold pb-2" width="8%">رقم الطلب</th>
            <th class="text-slate-400 fw-bold pb-2">اسم الخريج</th>
            <th class="text-slate-400 fw-bold pb-2">نوع الدفتر</th>
            <th class="text-slate-400 fw-bold pb-2">أرقام التواصل</th> <th class="text-center text-slate-400 fw-bold pb-2">الحالة</th>
            <th class="text-start text-slate-400 fw-bold ps-2 pb-2" width="12%">السعر</th>
        </tr>
    </thead>
    <tbody>
        @foreach($ordersGroup as $order)
            <tr style="border-bottom: 1px solid #f1f5f9;">
                <td class="fw-bold text-slate-400 py-3">#{{ $order->id }}</td>
                <td class="py-3 fw-bold text-dark fs-6">
                    {{ current(array_filter([$order->username_ar, $order->username_en])) }}
                </td>
                <td class="py-3">
                    <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded bg-white border"
                        style="border-color: #e2e8f0;">
                        <i class="fas fa-book-open text-slate-400 small"></i>
                        <span class="fw-bold text-slate-700 fs-6">{{ $order->bookType->name_ar ?? 'غير محدد' }}</span>
                    </div>
                </td>
                
                {{-- 🌟 داتا العمود الجديد (أرقام الهواتف مع الواتساب) 🌟 --}}
                <td class="py-3">
                    <div class="d-flex flex-column gap-1">
                        @if($order->delivery_number_one)
                            <a href="{{ $order->whatsapp_link }}" target="_blank" class="contact-link text-decoration-none fw-bold d-flex align-items-center gap-1" style="font-size: 0.85rem;">
                                <i class="fab fa-whatsapp text-success fs-6"></i>
                                <span dir="ltr">{{ $order->delivery_number_one }}</span>
                            </a>
                        @endif
                        @if($order->delivery_number_two)
                            <a href="{{ $order->whatsapp_link_two }}" target="_blank" class="contact-link text-decoration-none fw-bold d-flex align-items-center gap-1" style="font-size: 0.85rem;">
                                <i class="fab fa-whatsapp text-success fs-6"></i>
                                <span dir="ltr">{{ $order->delivery_number_two }}</span>
                            </a>
                        @endif
                        @if(!$order->delivery_number_one && !$order->delivery_number_two)
                            <span class="text-muted small fw-bold">لا يوجد رقم</span>
                        @endif
                    </div>
                </td>

                <td class="text-center py-3">
                    @php
                        $statusClass = 'bg-slate-100 text-slate-600';
                        $statusLabel = $order->status;
                        $statuses = [
                            'out_for_delivery' => ['class' => 'bg-amber-100 text-amber-700', 'label' => 'خرج مع التوصيل'],
                            'Received' => ['class' => 'bg-emerald-100 text-emerald-700', 'label' => 'تم التسليم'],
                            'returned' => ['class' => 'bg-slate-200 text-slate-700', 'label' => 'مرتجع'],
                            'Canceled' => ['class' => 'bg-rose-100 text-rose-700', 'label' => 'ملغى'],
                        ];
                        if (isset($statuses[$order->status])) {
                            $statusClass = $statuses[$order->status]['class'];
                            $statusLabel = $statuses[$order->status]['label'];
                        }
                    @endphp
                    <span class="badge {{ $statusClass }} px-3 py-2 rounded-pill fw-bold" style="font-size: 0.85rem;">
                        {{ $statusLabel }}
                    </span>
                </td>
                <td class="text-start fw-black text-dark ps-2 py-3 fs-5">
                    {{ number_format($order->final_price_with_discount, 2) }} <span class="fw-bold text-slate-400 fs-6">د.أ</span>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endif

                            @empty
                                <tr>
                                    <td colspan="6" class="text-center border-0 p-5">
                                        <div class="bg-white text-center p-5 rounded-4 shadow-sm border"
                                            style="max-width: 500px; margin: 3rem auto;">
                                            <div class="d-inline-flex align-items-center justify-content-center mb-4 shadow-sm"
                                                style="width: 100px; height: 100px; background: #f8fafc; border-radius: 50%;">
                                                <i class="fas fa-box-open fs-1 text-slate-400"></i>
                                            </div>
                                            <h3 class="text-dark fw-bolder mb-2">لا توجد طلبات للتوصيل</h3>
                                            <p class="text-muted fs-6 mb-0">قائمة التوصيل فارغة حالياً.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination Links --}}
                @if($paginatedOrders->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $paginatedOrders->links() }}
                    </div>
                @endif

            </div>
        </div>
    </div>

    <script>
function updateGroupStatus(selectElement, groupIdsArray) {
            const newStatus = selectElement.value;
            const originalValue = selectElement.getAttribute('data-original-value');

            if (!newStatus) return;

            const selectedText = selectElement.options[selectElement.selectedIndex].text;
            
            // 🔴 تعديل رسالة التحذير بناءً على الحالة
            let alertHtml = `سيتم تغيير حالة <strong>${groupIdsArray.length}</strong> طلب إلى <span class="fw-bold text-primary">"${selectedText}"</span>. هل أنت متأكد؟`;
            let confirmBtnColor = '#0f172a';

            if (newStatus === 'Canceled') {
                alertHtml = `سيتم تغيير الحالة إلى <span class="fw-bold text-danger">"${selectedText}"</span>.<br><br><span class="text-danger fw-bold"><i class="fas fa-exclamation-triangle"></i> تنبيه: سيتم إلغاء بوليصة الشحن من نظام شركة التوصيل نهائياً!</span><br>هل أنت متأكد؟`;
                confirmBtnColor = '#dc2626'; // أحمر للخطورة
            }

            Swal.fire({
                title: 'تأكيد التحديث',
                html: alertHtml,
                icon: newStatus === 'Canceled' ? 'warning' : 'question',
                showCancelButton: true,
                confirmButtonText: 'تأكيد التحديث',
                cancelButtonText: 'إلغاء',
                confirmButtonColor: confirmBtnColor,
                cancelButtonColor: '#94a3b8',
                reverseButtons: true,
                customClass: {
                    confirmButton: 'px-4 py-2 rounded-3',
                    cancelButton: 'px-4 py-2 rounded-3'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Disable select while fetching
                    selectElement.disabled = true;

                    // Show a small loading indicator near the select (optional, but good UX)
                    const loadingIcon = document.createElement('i');
                    loadingIcon.className = 'fas fa-spinner fa-spin ms-2 text-primary';
                    loadingIcon.id = 'status-spinner';
                    selectElement.parentNode.appendChild(loadingIcon);

                    fetch('{{ route("orders.bulkUpdateStatus") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ order_ids: groupIdsArray, status: newStatus })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    title: 'تم بنجاح!',
                                    text: 'تم تحديث الحالات بنجاح.',
                                    icon: 'success',
                                    confirmButtonColor: '#10b981',
                                    confirmButtonText: 'إغلاق'
                                }).then(() => location.reload());
                            } else {
                                Swal.fire('خطأ', data.message || 'حدث خطأ أثناء التحديث.', 'error');
                                // Revert back to original value
                                selectElement.value = originalValue;
                                selectElement.disabled = false;
                                document.getElementById('status-spinner').remove();
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire('خطأ اتصال', 'يرجى التحقق من اتصال الإنترنت والمحاولة مجدداً.', 'error');
                            // Revert back to original value
                            selectElement.value = originalValue;
                            selectElement.disabled = false;
                            document.getElementById('status-spinner').remove();
                        });
                } else {
                    // If user cancels, revert the dropdown to its previous value
                    selectElement.value = originalValue;
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            const collapseElements = document.querySelectorAll('.collapse');
            collapseElements.forEach(collapseEl => {
                collapseEl.addEventListener('show.bs.collapse', function () {
                    const btn = document.querySelector(`[data-bs-target="#${this.id}"]`);
                    if (btn) {
                        btn.classList.add('active');
                        btn.innerHTML = '<i class="fas fa-chevron-up"></i>';
                    }
                });
                collapseEl.addEventListener('hide.bs.collapse', function () {
                    const btn = document.querySelector(`[data-bs-target="#${this.id}"]`);
                    if (btn) {
                        btn.classList.remove('active');
                        btn.innerHTML = '<i class="fas fa-chevron-down"></i>';
                    }
                });
            });
        });
   document.addEventListener('DOMContentLoaded', function () {
            // --- Logic 1: Checkboxes & Bulk Print Button UI ---
            const selectAll = document.getElementById('selectAllDeliveries');
            const checkboxes = document.querySelectorAll('.delivery-checkbox');
            const bulkPrintBtn = document.getElementById('bulkPrintBtn');
            const printCountSpan = document.getElementById('printCount');

            function updateBulkButton() {
                const checked = document.querySelectorAll('.delivery-checkbox:checked');
                printCountSpan.textContent = checked.length;
                bulkPrintBtn.disabled = checked.length === 0;
                
                if (checked.length > 0 && checked.length === checkboxes.length) {
                    selectAll.checked = true;
                } else {
                    selectAll.checked = false;
                }
            }

            if (selectAll) {
                selectAll.addEventListener('change', function() {
                    checkboxes.forEach(cb => cb.checked = this.checked);
                    updateBulkButton();
                });
            }

            checkboxes.forEach(cb => {
                cb.addEventListener('change', updateBulkButton);
            });

            // --- Logic 2: Execute Print (AJAX) ---
            function executePrint(orderIdsArray, btnElement) {
                const originalHtml = btnElement.innerHTML;
                btnElement.disabled = true;
                btnElement.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

                fetch('{{ route("orders.printAWBs") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ order_ids: orderIdsArray })
                })
                .then(res => res.json())
                .then(data => {
                    btnElement.disabled = false;
                    btnElement.innerHTML = originalHtml;

                    if (data.success && data.url) {
                        window.open(data.url, '_blank'); // فتح الـ PDF بتاب جديد
                        Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'تم تجهيز بوليصة الشحن', showConfirmButton: false, timer: 3000 });
                    } else {
                        Swal.fire('تنبيه', data.message || 'حدث خطأ أثناء الطباعة.', 'warning');
                    }
                })
                .catch(error => {
                    btnElement.disabled = false;
                    btnElement.innerHTML = originalHtml;
                    Swal.fire('خطأ اتصال', 'تأكد أن الطلبات تم ترحيلها بنجاح لشركة التوصيل.', 'error');
                });
            }

            // A) Individual Print Click
            document.querySelectorAll('.print-single-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    // Extract the array of IDs stored in data-ids
                    const ids = JSON.parse(this.getAttribute('data-ids'));
                    executePrint(ids, this);
                });
            });

            // B) Bulk Print Click
            if (bulkPrintBtn) {
                bulkPrintBtn.addEventListener('click', function() {
                    const checked = document.querySelectorAll('.delivery-checkbox:checked');
                    let allIds = [];
                    checked.forEach(cb => {
                        const ids = JSON.parse(cb.value);
                        allIds = allIds.concat(ids); // Flatten arrays
                    });

                    // Remove duplicates just in case
                    const uniqueIds = [...new Set(allIds)];
                    
                    if (uniqueIds.length > 0) {
                        executePrint(uniqueIds, this);
                    }
                });
            }
        });
   </script>

    <style>
        /* 🌟 Minimalist Premium UI Styles 🌟 */

        body {
            background-color: #f8fafc;
        }

        /* Clean Table Spacing */
        .custom-ui-table {
            border-collapse: separate;
            border-spacing: 0 12px;
        }

        /* Floating Row Card */
        .ui-row {
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05), 0 1px 2px 0 rgba(0, 0, 0, 0.03);
            border-radius: 12px;
            transition: all 0.2s ease-in-out;
        }

        .ui-row:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.025);
        }

        .ui-row td {
            border-top: 1px solid #f1f5f9;
            border-bottom: 1px solid #f1f5f9;
        }

        .ui-row td:first-child {
            border-right: 1px solid #f1f5f9;
            border-radius: 0 12px 12px 0;
        }

        .ui-row td:last-child {
            border-left: 1px solid #f1f5f9;
            border-radius: 12px 0 0 12px;
        }

        /* Custom Tailwind-like Colors */
        .text-slate-400 {
            color: #94a3b8;
        }

        .text-slate-500 {
            color: #64748b;
        }

        .text-slate-600 {
            color: #475569;
        }

        .text-slate-700 {
            color: #334155;
        }

        .bg-slate-100 {
            background-color: #f1f5f9;
        }

        .bg-slate-200 {
            background-color: #e2e8f0;
        }

        .bg-slate-800 {
            background-color: #1e293b;
        }

        .bg-amber-100 {
            background-color: #fef3c7;
        }

        .text-amber-700 {
            color: #b45309;
        }

        .bg-emerald-100 {
            background-color: #d1fae5;
        }

        .text-emerald-700 {
            color: #047857;
        }

        .bg-rose-100 {
            background-color: #ffe4e6;
        }

        .text-rose-700 {
            color: #be123c;
        }

        /* Clean Contact Links */
        .contact-link {
            color: #475569;
            transition: color 0.2s;
        }

        .contact-link:hover {
            color: #10b981;
        }

        /* Expand Button */
        .expand-btn {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: #f1f5f9;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            border: none;
            /* Removing border for cleaner look */
        }

        .expand-btn:hover {
            background: #e2e8f0;
        }

        .expand-btn.active {
            background: #cbd5e1;
        }

        /* Status Dropdown Styling */
        .status-dropdown {
            transition: all 0.2s ease;
        }

        .status-dropdown:hover {
            border-color: #94a3b8 !important;
            background-color: #fff;
        }

        .status-dropdown:focus {
            box-shadow: 0 0 0 0.25rem rgba(15, 23, 42, 0.1) !important;
            border-color: #0f172a !important;
            background-color: #fff;
        }
    </style>
@endsection