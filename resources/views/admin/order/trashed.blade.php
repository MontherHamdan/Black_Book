@extends('admin.layout')

@section('content')
    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background-color: #f8f9fc;
        }

        .premium-card {
            border-radius: .4rem;
            border: none;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.03);
            background: #ffffff;
            overflow: hidden;
        }

        .header-icon-box {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: #fff;
            box-shadow: 0 8px 20px rgba(239, 68, 68, 0.2);
            transform: rotate(-5deg);
            transition: transform 0.3s ease;
        }

        .header-icon-box:hover {
            transform: rotate(0deg);
        }

        .btn-pill-action {
            border-radius: 50rem;
            padding: 0.5rem 1.25rem;
            font-weight: 600;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border: none;
        }

        .btn-pill-action:hover {
            transform: translateY(-2px);
        }

        .btn-pill-primary {
            background-color: #eef2ff;
            color: #4f46e5;
        }

        .btn-pill-primary:hover {
            background-color: #4f46e5;
            color: #fff;
            box-shadow: 0 8px 15px rgba(79, 70, 229, 0.2);
        }

        .btn-pill-success {
            background-color: #ecfdf5;
            color: #10b981;
        }

        .btn-pill-success:hover {
            background-color: #10b981;
            color: #fff;
            box-shadow: 0 8px 15px rgba(16, 185, 129, 0.2);
        }

        .btn-pill-danger {
            background-color: #fef2f2;
            color: #ef4444;
        }

        .btn-pill-danger:hover {
            background-color: #ef4444;
            color: #fff;
            box-shadow: 0 8px 15px rgba(239, 68, 68, 0.2);
        }

        #trashed-table {
            border-collapse: separate;
            border-spacing: 0 12px;
            margin-bottom: 0;
            border: none;
            width: 100% !important;
        }

        #trashed-table tbody tr {
            background: #fff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            border-radius: 12px;
            transition: box-shadow 0.2s ease, transform 0.2s ease;
        }

        #trashed-table tbody tr:hover {
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
            transform: translateY(-1px);
        }

        #trashed-table thead th {
            background: transparent;
            border: none;
            color: #94a3b8;
            font-size: 0.78rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding-bottom: 0.5rem;
        }

        #trashed-table tbody td {
            border: none;
            vertical-align: middle;
            padding: 0.85rem 1rem;
        }

        #trashed-table tbody tr td:first-child {
            border-radius: 12px 0 0 12px;
        }

        #trashed-table tbody tr td:last-child {
            border-radius: 0 12px 12px 0;
        }

        .order-id-badge {
            background: linear-gradient(135deg, #6366f1, #4f46e5);
            color: #fff;
            border-radius: 8px;
            padding: 0.3rem 0.65rem;
            font-weight: 700;
            font-size: 0.85rem;
            display: inline-block;
        }

        .deleted-badge {
            background: rgba(239, 68, 68, 0.1);
            color: #dc2626;
            border: 1px solid rgba(239, 68, 68, 0.2);
            border-radius: 6px;
            padding: 0.2rem 0.6rem;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .action-btn {
            border-radius: 8px;
            padding: 0.35rem 0.75rem;
            font-size: 0.8rem;
            font-weight: 600;
            border: none;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .action-btn:hover {
            transform: translateY(-1px);
        }

        .btn-restore {
            background: rgba(16, 185, 129, 0.1);
            color: #059669;
        }

        .btn-restore:hover {
            background: #10b981;
            color: #fff;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .btn-permanent-delete {
            background: rgba(239, 68, 68, 0.1);
            color: #dc2626;
        }

        .btn-permanent-delete:hover {
            background: #ef4444;
            color: #fff;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }
    </style>

    {{-- Header --}}
    <div class="row align-items-center mb-4">
        <div class="col-md-8">
            <div class="d-flex align-items-center gap-3">
                <div class="header-icon-box">
                    <i class="fas fa-trash-alt fa-2x"></i>
                </div>
                <div>
                    <h2 class="fw-bolder text-dark mb-1" style="letter-spacing: -0.5px;">Trashed Orders</h2>
                    <p class="text-muted mb-0" style="font-size:0.88rem;">سلة المحذوفات — يمكن استعادة الطلبات أو حذفها نهائياً</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 text-start mt-3 mt-md-0 d-flex gap-2 justify-content-md-end flex-wrap">
            <button id="bulkRestoreBtn" class="btn btn-pill-action btn-pill-success" disabled style="display:none;">
                <i class="fas fa-undo"></i> استعادة المحدد (<span id="restoreCount">0</span>)
            </button>
            <button id="bulkForceDeleteBtn" class="btn btn-pill-action btn-pill-danger" disabled style="display:none;">
                <i class="fas fa-fire"></i> حذف نهائي (<span id="forceDeleteCount">0</span>)
            </button>
            <a href="{{ route('orders.index') }}" class="btn btn-pill-action btn-pill-primary">
                <i class="fas fa-arrow-right"></i> العودة للطلبات
            </a>
        </div>
    </div>

    {{-- Table --}}
    <div class="row">
        <div class="col-12">
            <div class="card premium-card">
                <div class="card-body p-0">
                    <table id="trashed-table" class="table table-hover table-borderless align-middle w-100 mb-0">
                        <thead>
                            <tr>
                                <th width="40" class="text-center">
                                    <input type="checkbox" id="selectAll" class="form-check-input shadow-sm">
                                </th>
                                <th>رقم الطلب</th>
                                <th>اسم المستخدم</th>
                                <th>الهاتف</th>
                                <th>نوع الطلب</th>
                                <th>المصمم</th>
                                <th>تاريخ الإنشاء</th>
                                <th>تاريخ الحذف</th>
                                <th>السعر</th>
                                <th class="text-center">الإجراءات</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function () {

            const table = $('#trashed-table').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                order: [[7, 'desc']],
                ajax: {
                    url: '{{ route('orders.trashed.fetch') }}',
                    error: function (xhr) {
                        console.error('DataTables AJAX error:', xhr.responseText);
                    }
                },
                lengthMenu: [10, 25, 50, 100],
                pageLength: 10,
                language: {
                    search: 'بحث:',
                    lengthMenu: 'عرض _MENU_ طلب',
                    info: 'عرض _START_ إلى _END_ من _TOTAL_ طلب',
                    infoEmpty: 'لا توجد طلبات',
                    paginate: { first: 'الأول', last: 'الأخير', next: 'التالي', previous: 'السابق' },
                    zeroRecords: 'لا توجد نتائج مطابقة',
                    processing: '<div class="spinner-border text-primary" role="status"></div>',
                },
                columns: [
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        width: '40px',
                        render: function (data, type, row) {
                            return `<input type="checkbox" class="order-checkbox form-check-input" value="${row.id}">`;
                        }
                    },
                    {
                        data: 'id',
                        render: function (data) {
                            return `<span class="order-id-badge">#${data}</span>`;
                        }
                    },
                    { data: 'username', defaultContent: '—' },
                    { data: 'phone', defaultContent: '—' },
                    { data: 'order_type', defaultContent: '—' },
                    { data: 'designer', defaultContent: '—' },
                    { data: 'created_at', defaultContent: '—' },
                    {
                        data: 'deleted_at',
                        render: function (data) {
                            return `<span class="deleted-badge"><i class="fas fa-clock me-1"></i>${data}</span>`;
                        }
                    },
                    {
                        data: 'price',
                        render: function (data) {
                            return data ? parseFloat(data).toFixed(2) + ' د.أ' : '—';
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        render: function (data, type, row) {
                            return `
                                <div class="d-flex gap-2 justify-content-center">
                                    <button class="action-btn btn-restore restore-btn" data-id="${row.id}" title="استعادة الطلب">
                                        <i class="fas fa-undo me-1"></i>استعادة
                                    </button>
                                    <button class="action-btn btn-permanent-delete force-delete-btn" data-id="${row.id}" title="حذف نهائي">
                                        <i class="fas fa-fire me-1"></i>حذف نهائي
                                    </button>
                                </div>`;
                        }
                    },
                ]
            });

            // ── Checkbox helpers ────────────────────────────────────────
            function updateBulkButtons() {
                const count = $('.order-checkbox:checked').length;
                $('#restoreCount, #forceDeleteCount').text(count);
                if (count > 0) {
                    $('#bulkRestoreBtn').prop('disabled', false).show();
                    $('#bulkForceDeleteBtn').prop('disabled', false).show();
                } else {
                    $('#bulkRestoreBtn').prop('disabled', true).hide();
                    $('#bulkForceDeleteBtn').prop('disabled', true).hide();
                }
            }

            $('#selectAll').on('change', function () {
                $('.order-checkbox').prop('checked', $(this).prop('checked'));
                updateBulkButtons();
            });

            $(document).on('change', '.order-checkbox', function () {
                updateBulkButtons();
                const total = $('.order-checkbox').length;
                const checked = $('.order-checkbox:checked').length;
                $('#selectAll').prop('checked', total === checked);
            });

            table.on('draw', function () {
                updateBulkButtons();
                $('#selectAll').prop('checked', false);
            });

            // ── Single Restore ──────────────────────────────────────────
            $(document).on('click', '.restore-btn', function () {
                const orderId = $(this).data('id');
                Swal.fire({
                    title: 'استعادة الطلب',
                    html: `هل تريد استعادة الطلب <strong>#${orderId}</strong>؟`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'نعم، استعد',
                    cancelButtonText: 'إلغاء',
                    confirmButtonColor: '#10b981',
                    cancelButtonColor: '#94a3b8',
                    reverseButtons: true,
                }).then(result => {
                    if (!result.isConfirmed) return;
                    $.ajax({
                        url: '/orders/' + orderId + '/restore',
                        method: 'POST',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function (res) {
                            if (res.success) {
                                Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: res.message, showConfirmButton: false, timer: 2500, timerProgressBar: true });
                                table.ajax.reload(null, false);
                            } else {
                                Swal.fire('خطأ', res.message, 'error');
                            }
                        },
                        error: function (xhr) {
                            Swal.fire('خطأ', xhr.responseJSON?.message ?? 'حدث خطأ.', 'error');
                        }
                    });
                });
            });

            // ── Single Force Delete ─────────────────────────────────────
            $(document).on('click', '.force-delete-btn', function () {
                const orderId = $(this).data('id');
                Swal.fire({
                    title: 'حذف نهائي',
                    html: `هل أنت متأكد من الحذف النهائي للطلب <strong>#${orderId}</strong>؟<br><br><span style="color:#dc2626;font-weight:700;">لا يمكن التراجع عن هذا الإجراء!</span>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'نعم، احذف نهائياً',
                    cancelButtonText: 'إلغاء',
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#94a3b8',
                    reverseButtons: true,
                }).then(result => {
                    if (!result.isConfirmed) return;
                    $.ajax({
                        url: '/orders/' + orderId + '/force-delete',
                        method: 'POST',
                        data: { _token: '{{ csrf_token() }}', _method: 'DELETE' },
                        success: function (res) {
                            if (res.success) {
                                Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: res.message, showConfirmButton: false, timer: 2500, timerProgressBar: true });
                                table.ajax.reload(null, false);
                            } else {
                                Swal.fire('خطأ', res.message, 'error');
                            }
                        },
                        error: function (xhr) {
                            Swal.fire('خطأ', xhr.responseJSON?.message ?? 'حدث خطأ.', 'error');
                        }
                    });
                });
            });

            // ── Bulk Restore ────────────────────────────────────────────
            $('#bulkRestoreBtn').on('click', function () {
                const ids = $('.order-checkbox:checked').map(function () { return $(this).val(); }).get();
                if (!ids.length) return;
                Swal.fire({
                    title: 'استعادة جماعية',
                    html: `هل تريد استعادة <strong>${ids.length}</strong> طلب؟`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'نعم، استعد الكل',
                    cancelButtonText: 'إلغاء',
                    confirmButtonColor: '#10b981',
                    cancelButtonColor: '#94a3b8',
                    reverseButtons: true,
                }).then(result => {
                    if (!result.isConfirmed) return;
                    $.ajax({
                        url: '{{ route('orders.bulkRestore') }}',
                        method: 'POST',
                        data: { _token: '{{ csrf_token() }}', order_ids: ids },
                        success: function (res) {
                            Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: res.message, showConfirmButton: false, timer: 2500, timerProgressBar: true });
                            table.ajax.reload(null, false);
                        },
                        error: function (xhr) {
                            Swal.fire('خطأ', xhr.responseJSON?.message ?? 'حدث خطأ.', 'error');
                        }
                    });
                });
            });

            // ── Bulk Force Delete ───────────────────────────────────────
            $('#bulkForceDeleteBtn').on('click', function () {
                const ids = $('.order-checkbox:checked').map(function () { return $(this).val(); }).get();
                if (!ids.length) return;
                Swal.fire({
                    title: 'حذف نهائي جماعي',
                    html: `هل أنت متأكد من الحذف النهائي لـ <strong>${ids.length}</strong> طلب؟<br><br><span style="color:#dc2626;font-weight:700;">لا يمكن التراجع عن هذا الإجراء!</span>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'نعم، احذف نهائياً',
                    cancelButtonText: 'إلغاء',
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#94a3b8',
                    reverseButtons: true,
                }).then(result => {
                    if (!result.isConfirmed) return;
                    $.ajax({
                        url: '{{ route('orders.bulkForceDelete') }}',
                        method: 'POST',
                        data: { _token: '{{ csrf_token() }}', order_ids: ids },
                        success: function (res) {
                            Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: res.message, showConfirmButton: false, timer: 2500, timerProgressBar: true });
                            table.ajax.reload(null, false);
                        },
                        error: function (xhr) {
                            Swal.fire('خطأ', xhr.responseJSON?.message ?? 'حدث خطأ.', 'error');
                        }
                    });
                });
            });

        });
    </script>
@endpush
