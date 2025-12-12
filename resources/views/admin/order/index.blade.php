@extends('admin.layout')

@section('content')
<style>
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

    .order-has-notes {
        background-color: #fff8e6 !important;
        box-shadow: inset 3px 0 0 #f0ad4e;
    }

    .order-duplicate-phone {
        background-color: #ffe5e5 !important;
        box-shadow: inset 3px 0 0 #dc3545;
    }

    .order-with-additives {
        background-color: #fff3cd !important;
        box-shadow: inset 3px 0 0 #ffc107;
    }

    .status-badge-big {
        font-size: 0.8rem;
        padding: 0.35rem 0.9rem;
        border-radius: 999px;
        font-weight: 600;
        letter-spacing: 1px;
    }

    .status-duration {
        font-size: 0.75rem;
        color: #6c757d;
        display: block;
        margin-top: 4px;
    }
</style>


<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="d-flex justify-content-between align-items-center p-3">
                <h1 class="mb-0 text-primary">الطلبات</h1>

                <div class="d-flex gap-2">
                    <button id="openAdvancedSearch" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#advancedSearchModal">
                        <i class="fas fa-search me-1"></i> بحث متقدم
                    </button>

                    <button id="exportExcelBtn" class="btn btn-success btn-sm">
                        <i class="fas fa-file-excel me-1"></i> تصدير CSV
                    </button>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table id="orders-table" class="table table-hover table-striped ">
                        <thead>
                            <tr>
                                <th>رقم الطلب</th>
                                <th>التاريخ</th>
                                <th>الحالة</th>
                                <th>المصمم</th>
                                <th>اسم المستخدم</th>
                                <th>نوع الطلب</th>
                                <th>المحافظة</th>
                                <th>العنوان</th>
                                <th>الجامعة</th>
                                <th>رقم الهاتف</th>
                                <th>رقم الهاتف 2</th>
                                <th>السعر</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Add Notes Modal --}}
<div class="modal fade" id="addNoteModal" tabindex="-1" aria-labelledby="addNoteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addNoteModalLabel">Order Notes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- Add Note Card --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <form id="addNoteForm">
                            <input type="hidden" id="noteOrderId">
                            <div class="mb-3">
                                <label for="noteContent" class="form-label">Enter your note</label>
                                <textarea class="form-control" id="noteContent" rows="3" placeholder="Write your note here..."></textarea>
                            </div>
                            <div class="d-flex justify-content-between">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i> Save Note
                                </button>
                                <button type="button" class="btn btn-secondary" id="clearNoteBtn">
                                    <i class="fas fa-eraser me-2"></i> Clear
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Existing Notes Card --}}
                <div class="card shadow-sm">
                    <div class="card-body">
                        <ul id="notesList" class="list-group">
                            <li class="text-muted">no notes yet</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Advanced Search Modal --}}
<div class="modal fade" id="advancedSearchModal" tabindex="-1" aria-labelledby="advancedSearchModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="advancedSearchModalLabel">البحث المتقدم</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="advancedSearchForm">
                    <div class="mb-3">
                        <label for="dateFrom" class="form-label">من تاريخ</label>
                        <input type="date" id="dateFrom" name="date_from" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="dateTo" class="form-label">إلى تاريخ</label>
                        <input type="date" id="dateTo" name="date_to" class="form-control">
                    </div>
                    {{-- لو حاب تضيف فلاتر أخرى (محافظة، حالة، ... ) تضيفها هنا --}}
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="clearFiltersBtn" class="btn btn-outline-secondary">
                    مسح الفلاتر
                </button>
                <button type="button" id="applyFiltersBtn" class="btn btn-primary">
                    تطبيق
                </button>
            </div>
        </div>
    </div>
</div>

<link href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {

        // مصفوفة المصممين + صلاحيات المستخدم الحالي
        const DESIGNERS = @json($designers);
        const IS_ADMIN = @json(auth() -> user() -> isAdmin());
        const CURRENT_USER_ID = @json(auth() -> id());

        function loadNotes(orderId) {
            $('#notesList').html('<li class="list-group-item text-muted">Loading...</li>');

            $.ajax({
                url: '/orders/' + orderId + '/notes',
                method: 'GET',
                success: function(response) {
                    const notes = response.notes || [];
                    const $list = $('#notesList');
                    $list.empty();

                    if (notes.length === 0) {
                        $list.append('<li class="list-group-item text-muted">no notes yet</li>');
                        return;
                    }

                    notes.forEach(function(note) {
                        const itemHtml = `
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <strong>${note.user_name}</strong>
                                        <p class="mb-1">${note.content}</p>
                                    </div>
                                    <small class="text-muted">${note.created_at}</small>
                                </div>
                            </li>
                        `;
                        $list.append(itemHtml);
                    });
                },
                error: function() {
                    $('#notesList').html('<li class="list-group-item text-danger">Failed to load notes.</li>');
                }
            });
        }

        const table = $('#orders-table').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            order: [
                [0, 'desc']
            ],
            ajax: {
                url: '{{ route('orders.fetch') }}',
                data: function(d) {
                    d.status = $('#statusFilter').val();
                    d.additives = $('#additivesFilter').val();
                    d.date_from = $('#dateFrom').val();
                    d.date_to = $('#dateTo').val();
                },
                error: function(xhr) {
                    console.error('DataTables AJAX error:', xhr);
                    console.error('Response text:', xhr.responseText);

                    alert(
                        'حدث خطأ في جلب البيانات من السيرفر.\n' +
                        'الكود: ' + xhr.status + '\n' +
                        'افتحي Console / Network عشان تشوفي التفاصيل.'
                    );
                }
            },
            lengthMenu: [10, 25, 50, 100],
            pageLength: 10,
            columns: [{
                    data: 'id',
                    name: 'id',
                    render: function(data, type, row) {
                        return `<a href="/orders/${data}" class="btn btn-primary btn-sm">${data}</a>`;
                    },
                    orderable: true,
                    searchable: false
                },
                {
                    data: 'data',
                    name: 'data',
                    orderable: false
                },
                {
                    data: 'status',
                    name: 'status',
                    orderable: false,
                    render: function(data, type, row) {

                        const statusConfig = {
                            Pending: {
                                class: 'bg-warning text-dark',
                                label: 'تم التصميم'
                            },
                            Completed: {
                                class: 'bg-info text-dark',
                                label: 'تم الاعتماد'
                            },
                            preparing: {
                                class: 'bg-purple',
                                label: 'قيد التجهيز'
                            },
                            Received: {
                                class: 'bg-success text-white',
                                label: 'تم التسليم'
                            },
                            'Out for Delivery': {
                                class: 'bg-orange',
                                label: 'مرتجع'
                            },
                            Canceled: {
                                class: 'bg-maroon',
                                label: 'رفض الإستلام'
                            },
                            error: {
                                class: 'bg-danger text-white',
                                label: 'خــطــأ' // 👈 عدلنا شكل الكلمة
                            }
                        };

                        const defaultConfig = statusConfig.error;
                        const currentStatus = statusConfig[data] || defaultConfig;

                        const allStatuses = [
                            'Pending',
                            'Completed',
                            'preparing',
                            'Received',
                            'Out for Delivery',
                            'Canceled',
                            'error'
                        ];

                        // هل المستخدم يقدر يغير الحالة؟
                        const canChangeStatus =
                            IS_ADMIN ||
                            (row.designer && row.designer.id === CURRENT_USER_ID);

                        // المدة اللي رجعناها من الـ Controller
                        const durationText = row.status_created_diff ?
                            row.status_created_diff :
                            '';

                        // لو ما عنده صلاحية → Badge كبير + المدة فقط
                        if (!canChangeStatus) {
                            return `
                <div class="text-center">
                    <span class="badge status-badge-big ${currentStatus.class}">
                        ${currentStatus.label}
                    </span>
                </div>
            `;
                        }

                        // لو عنده صلاحية → Dropdown + المدة تحت
                        const dropdownItems = allStatuses
                            .filter(function(status) {
                                return status !== data;
                            })
                            .map(function(status) {
                                const cfg = statusConfig[status] || defaultConfig;
                                return `
                    <li>
                        <a class="dropdown-item change-status-item"
                           href="#"
                           data-order-id="${row.id}"
                           data-new-status="${status}">
                            ${cfg.label}
                        </a>
                    </li>
                `;
                            })
                            .join('');

                        return `
            <div class="text-center">
                <div class="dropdown d-inline">
                    <span
                        class="badge status-badge-big ${currentStatus.class} dropdown-toggle"
                        id="statusDropdown${row.id}"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                        style="cursor: pointer;">
                        ${currentStatus.label}
                    </span>
                    <ul class="dropdown-menu" aria-labelledby="statusDropdown${row.id}">
                        ${dropdownItems}
                    </ul>
                </div>
            </div>
        `;
                    }
                },

                {
                    data: 'designer',
                    name: 'designer',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        const currentDesignerId = (data && data.id) ? data.id : null;

                        let optionsHtml = '';

                        DESIGNERS.forEach(function(designer) {
                            const selected = (currentDesignerId === designer.id) ? 'selected' : '';
                            optionsHtml += `<option value="${designer.id}" ${selected}>${designer.name}</option>`;
                        });

                        const notAssignedOption = '<option value="">غير معيّن</option>';

                        // هل الـ select يكون Disabled؟
                        let disabledAttr = '';

                        if (!IS_ADMIN) {
                            // Designer user
                            if (currentDesignerId && currentDesignerId !== CURRENT_USER_ID) {
                                // الطلب معيّن لمصمم آخر → ممنوع يلمسه
                                disabledAttr = 'disabled';
                            }
                        }

                        return `
                            <select class="form-select form-select-sm order-designer-select"
                                    data-order-id="${row.id}"
                                    data-current-designer-id="${currentDesignerId || ''}"
                                    ${disabledAttr}>
                                ${notAssignedOption}
                                ${optionsHtml}
                            </select>
                        `;
                    }
                },
                {
                    data: 'username',
                    name: 'username',
                    orderable: false
                },
                {
                    data: 'order',
                    name: 'order',
                    orderable: false
                },
                {
                    data: 'governorate',
                    name: 'governorate',
                    orderable: false
                },
                {
                    data: 'address',
                    name: 'address',
                    orderable: false
                },
                {
                    data: 'school_name',
                    name: 'school_name',
                    orderable: false
                },
                {
                    data: 'phone',
                    name: 'phone',
                    orderable: false,
                    render: function(data, type, row) {
                        if (!data) return '';

                        let cleaned = String(data).replace(/\D/g, '');
                        let waNumber = cleaned;

                        if (waNumber.startsWith('962')) {
                            // ok
                        } else if (waNumber.startsWith('00')) {
                            waNumber = waNumber.substring(2);
                        } else if (waNumber.startsWith('0')) {
                            waNumber = '962' + waNumber.replace(/^0+/, '');
                        }

                        return `
                            <span>${data}</span>
                            <a href="https://wa.me/${waNumber}" target="_blank"
                               class="ms-2 text-success" title="WhatsApp">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                        `;
                    }
                },
                {
                    data: 'phone2',
                    name: 'phone2',
                    orderable: false,
                    render: function(data, type, row) {
                        if (!data) return '';
                        // فقط عرض الرقم كما هو بدون أيقونة واتساب
                        return `<span>${data}</span>`;
                    }
                },

                {
                    data: 'price',
                    name: 'price',
                    orderable: false
                },
                {
                    data: 'actions',
                    name: 'actions',
                    className: 'text-center',
                    orderable: false,
                    searchable: false
                }
            ],
            language: {
                search: "Search Orders:"
            },
            rowCallback: function(row, data) {
                $(row).removeClass('order-has-notes order-duplicate-phone order-with-additives');

                if (data.is_duplicate_phone) {
                    $(row).addClass('order-duplicate-phone');
                }

                if (data.is_with_additives) {
                    $(row).addClass('order-with-additives');
                }
            },

            initComplete: function() {
                const statusDropdown = $(`
                    <select id="statusFilter" class="form-select" style="width: 230px;height:34px; margin-left: 15px;">
                        <option value="">تصفية حسب الحالة</option>
                        <option value="Pending">تم التصميم</option>
                        <option value="Completed">تم الاعتماد</option>
                        <option value="preparing">قيد التجهيز</option>
                        <option value="Received">تم التسليم</option>
                        <option value="Out for Delivery">مرتجع</option>
                        <option value="Canceled">رفض الإستلام</option>
                        <option value="error">خطأ</option>
                    </select>
                `);

                const additivesDropdown = $(`
                    <select id="additivesFilter" class="form-select" style="width: 175px;height:34px; margin-left: 15px;">
                        <option value="">تصفية حسب الإضافات</option>
                        <option value="with_additives">مع إضافات</option>
                        <option value="with_out_additives">بدون إضافات</option>
                    </select>
                `);

                $('.dataTables_filter').css({
                    display: 'flex',
                    justifyContent: 'flex-end',
                    alignItems: 'center'
                });

                $('.dataTables_filter').append(statusDropdown);
                $('.dataTables_filter').append(additivesDropdown);

                $('#statusFilter').on('change', function() {
                    table.ajax.reload();
                });

                $('#additivesFilter').on('change', function() {
                    table.ajax.reload();
                });
            }
        });

        // Export CSV
        $('#exportExcelBtn').on('click', function() {
            const status = $('#statusFilter').val() || '';
            const additives = $('#additivesFilter').val() || '';
            const search = $('.dataTables_filter input[type="search"]').val() || '';
            const dateFrom = $('#dateFrom').val() || '';
            const dateTo = $('#dateTo').val() || '';

            const params = new URLSearchParams({
                status: status,
                additives: additives,
                search: search,
                date_from: dateFrom,
                date_to: dateTo
            });

            window.location.href = '{{ route('orders.exportExcel')}}' + '?' + params.toString();
        });

        // تغيير الحالة
        $(document).on('click', '.change-status-item', function(e) {
            e.preventDefault();

            const orderId = $(this).data('order-id');
            const newStatus = $(this).data('new-status');

            $.ajax({
                url: '{{ route('orders.updateStatus')}}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: orderId,
                    status: newStatus
                },
                success: function(response) {
                    if (response.success) {
                        table.ajax.reload(null, false);
                    } else {
                        alert(response.message || 'Failed to update order status. Please try again.');
                    }
                },
                error: function(xhr) {
                    let msg = 'An error occurred while updating the status.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    alert(msg);
                }
            });
        });

        // تغيير المصمم
        $(document).on('change', '.order-designer-select', function() {
            const select = $(this);
            const orderId = select.data('order-id');
            const previousDesignerId = select.data('current-designer-id') || '';
            const newDesignerId = select.val();

            $.ajax({
                url: '{{ route('orders.updateDesigner')}}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    order_id: orderId,
                    designer_id: newDesignerId || null
                },
                success: function(response) {
                    if (!response.success) {
                        alert(response.message || 'Failed to update designer.');
                        select.val(previousDesignerId || '');
                        return;
                    }

                    select.data('current-designer-id', newDesignerId || '');
                    table.ajax.reload(null, false);
                },
                error: function(xhr) {
                    let msg = 'An error occurred while updating designer.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    alert(msg);
                    select.val(previousDesignerId || '');
                }
            });
        });

        // فتح مودال النوتس
        $(document).on('click', '.btn-add-note', function(e) {
            e.preventDefault();
            const orderId = $(this).data('order-id');

            $('#noteOrderId').val(orderId);
            $('#noteContent').val('');
            loadNotes(orderId);
            $('#addNoteModal').modal('show');
        });

        // حفظ النوت
        $('#addNoteForm').on('submit', function(e) {
            e.preventDefault();

            const orderId = $('#noteOrderId').val();
            const content = $('#noteContent').val().trim();

            if (!content) {
                alert('Please enter a note.');
                return;
            }

            $.ajax({
                url: '{{ route('orders.addNote')}}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    order_id: orderId,
                    note: content
                },
                success: function(response) {
                    if (response.success) {
                        $('#noteContent').val('');
                        const note = response.note;
                        const newItem = `
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <strong>${note.user_name}</strong>
                                        <p class="mb-1">${note.content}</p>
                                    </div>
                                    <small class="text-muted">${note.created_at}</small>
                                </div>
                            </li>
                        `;
                        $('#notesList').prepend(newItem);
                        table.ajax.reload(null, false);
                    } else {
                        alert('Failed to save note. Please try again.');
                    }
                },
                error: function() {
                    alert('An error occurred while saving the note.');
                }
            });
        });

        // زر مسح محتوى النوت
        $('#clearNoteBtn').on('click', function() {
            $('#noteContent').val('');
        });

        // البحث المتقدم - تطبيق الفلاتر
        $('#applyFiltersBtn').on('click', function() {
            table.ajax.reload();
            $('#advancedSearchModal').modal('hide');
        });

        // البحث المتقدم - مسح الفلاتر
        $('#clearFiltersBtn').on('click', function() {
            $('#dateFrom').val('');
            $('#dateTo').val('');
            table.ajax.reload();
        });
    });
</script>

@endsection