@extends('admin.layout')

@section('content')
    <style>
        /* Typography & General */
        body {
            font-family: 'Cairo', sans-serif;
            background-color: #f8f9fc;
        }

        /* Premium Card styling for the Table Container */
        .premium-card {
            border-radius: .4rem;
            border: none;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.03);
            background: #ffffff;
            overflow: hidden;
        }

        /* Header Style */
        .header-icon-box {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            color: #fff;
            box-shadow: 0 8px 20px rgba(99, 102, 241, 0.2);
            transform: rotate(-5deg);
            transition: transform 0.3s ease;
        }

        .header-icon-box:hover {
            transform: rotate(0deg);
        }

        /* Smooth Pill Buttons */
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

        /* Table Styling */
        #orders-table {
            border-collapse: separate;
            border-spacing: 0 12px;
            /* 👈 هنا السحر: مسافة 12 بكسل بين كل صف وصف */
            margin-bottom: 0;
            border: none;
        }

        #orders-table thead th {
            background-color: transparent;
            /* شلنا الخلفية عشان يبين أرتب مع الفواصل */
            color: #64748b;
            text-transform: uppercase;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            padding: 0.5rem 1.5rem;
            /* صغرنا البادينج للهيدر شوي */
            border: none;
        }

        /* تنسيق الصف كأنه بطاقة (Card) */
        #orders-table tbody tr {
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.03);
            transition: box-shadow 0.2s ease;
        }

        /* حركة خفيفة لما تمرر الماوس فوق الصف */
        #orders-table tbody tr:hover {
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.06);
        }

        #orders-table tbody td {
            padding: 1rem 1.5rem;
            vertical-align: middle;
            background-color: #ffffff;
            /* لون أبيض افتراضي لكل خلية */
            color: #334155;
            font-weight: 500;
            border-top: 1px solid #000000ff;
            border-bottom: 1px solid #f1f5f9;
        }

        /* تدوير الزاوية اليمنى (لأننا باللغة العربية RTL) */
        #orders-table tbody td:first-child {
            border-right: 1px solid #f1f5f9;
            border-top-right-radius: 12px;
            border-bottom-right-radius: 12px;
        }

        /* تدوير الزاوية اليسرى */
        #orders-table tbody td:last-child {
            border-left: 1px solid #f1f5f9;
            border-top-left-radius: 12px;
            border-bottom-left-radius: 12px;
        }

        /* Row Highlighting (تلوين الخلايا الداخلية وتطبيق الحد على أول خلية يمين) */
        .order-has-notes td {
            background-color: #fffbeb !important;
        }

        .order-has-notes td:first-child {
            border-right: 4px solid #f59e0b !important;
        }

        .order-duplicate td {
            background-color: #fef2f2 !important;
        }

        .order-duplicate td:first-child {
            border-right: 4px solid #ef4444 !important;
        }

        .order-with-additives td {
            background-color: #eef2ff !important;
        }

        .order-with-additives td:first-child {
            border-right: 4px solid #6366f1 !important;
        }

        /* Soft Status Badges */
        .status-badge-soft {
            font-size: 0.75rem;
            padding: 0.4rem 1rem;
            border-radius: 50rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            display: inline-block;
            border: 1px solid transparent;
        }

        .status-soft-primary {
            background-color: rgba(99, 102, 241, 0.1);
            color: #4f46e5;
            border-color: rgba(99, 102, 241, 0.2);
        }

        .status-soft-danger {
            background-color: rgba(239, 68, 68, 0.1);
            color: #dc2626;
            border-color: rgba(239, 68, 68, 0.2);
        }

        .status-soft-warning {
            background-color: rgba(245, 158, 11, 0.1);
            color: #d97706;
            border-color: rgba(245, 158, 11, 0.2);
        }

        .status-soft-info {
            background-color: rgba(14, 165, 233, 0.1);
            color: #0284c7;
            border-color: rgba(14, 165, 233, 0.2);
        }

        .status-soft-purple {
            background-color: rgba(168, 85, 247, 0.1);
            color: #9333ea;
            border-color: rgba(168, 85, 247, 0.2);
        }

        .status-soft-success {
            background-color: rgba(16, 185, 129, 0.1);
            color: #059669;
            border-color: rgba(16, 185, 129, 0.2);
        }

        .status-soft-orange {
            background-color: rgba(249, 115, 22, 0.1);
            color: #ea580c;
            border-color: rgba(249, 115, 22, 0.2);
        }

        .status-soft-maroon {
            background-color: rgba(159, 18, 57, 0.1);
            color: #e11d48;
            border-color: rgba(159, 18, 57, 0.2);
        }

        /* Premium Modals */
        .premium-modal .modal-content {
            border-radius: 1.5rem;
            border: none;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .premium-modal .modal-header {
            background-color: #f8fafc;
            border-bottom: 1px solid #f1f5f9;
            padding: 1.25rem 1.5rem;
        }

        .premium-modal .modal-title {
            font-weight: 700;
            color: #1e293b;
        }

        /* Chat-like Notes Feed */
        .chat-feed {
            list-style: none;
            padding: 0;
            margin: 0;
            max-height: 400px;
            overflow-y: auto;
        }

        .chat-message {
            background: #f8fafc;
            border-radius: 1rem;
            padding: 1rem;
            margin-bottom: 1rem;
            border: 1px solid #e2e8f0;
            position: relative;
        }

        .chat-message::before {
            content: '';
            position: absolute;
            top: 15px;
            right: -8px;
            border-style: solid;
            border-width: 8px 0 8px 8px;
            border-color: transparent transparent transparent #e2e8f0;
        }

        .chat-message::after {
            content: '';
            position: absolute;
            top: 15px;
            right: -6px;
            border-style: solid;
            border-width: 8px 0 8px 8px;
            border-color: transparent transparent transparent #f8fafc;
        }

        .chat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }

        .chat-author {
            font-weight: 700;
            color: #3b82f6;
            font-size: 0.9rem;
        }

        .chat-time {
            font-size: 0.75rem;
            color: #94a3b8;
        }

        .chat-content {
            color: #334155;
            font-size: 0.95rem;
            line-height: 1.5;
            margin: 0;
        }

        /* Filters input styling */
        .dataTables_filter select {
            border-radius: 50rem;
            border: 1px solid #e2e8f0;
            padding: 0.4rem 1rem;
            font-size: 0.85rem;
            color: #475569;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
            background-color: #fff;
        }

        .dataTables_filter select:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        /* Discount badges */
        .badge-discount {
            font-size: 0.7rem;
            padding: 0.3rem 0.65rem;
            border-radius: 50rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            white-space: nowrap;
        }

        .badge-individual {
            background: rgba(16, 185, 129, 0.1);
            color: #059669;
            border: 1px solid rgba(16, 185, 129, 0.25);
        }

        .badge-group-ok {
            background: rgba(99, 102, 241, 0.1);
            color: #4f46e5;
            border: 1px solid rgba(99, 102, 241, 0.25);
        }

        .badge-group-warn {
            background: rgba(245, 158, 11, 0.12);
            color: #b45309;
            border: 1px solid rgba(245, 158, 11, 0.3);
            animation: pulse-warn 1.8s ease-in-out infinite;
        }

        @keyframes pulse-warn {

            0%,
            100% {
                box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.3);
            }

            50% {
                box-shadow: 0 0 0 5px rgba(245, 158, 11, 0);
            }
        }

        .no-discount {
            font-size: 0.72rem;
            color: #94a3b8;
        }
    </style>


    <div class="row align-items-center mb-4">
        <div class="col-md-8">
            <div class="d-flex align-items-center gap-3">
                <div class="header-icon-box">
                    <i class="fas fa-boxes fa-2x"></i>
                </div>
                <div>
                    <h2 class="fw-bolder text-dark mb-1" style="letter-spacing: -0.5px;">Orders</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4 text-start mt-3 mt-md-0 d-flex gap-2 justify-content-md-end">
            @if(auth()->user()->isAdmin())
                <button id="bulkDeleteBtn" class="btn btn-pill-action btn-pill-danger" disabled style="display: none;">
                    <i class="fas fa-trash"></i> حذف المحدد (<span id="selectedCount">0</span>)
                </button>

            @endif
            <button id="openAdvancedSearch" class="btn btn-pill-action btn-pill-primary" data-bs-toggle="modal"
                data-bs-target="#advancedSearchModal">
                <i class="fas fa-search"></i> بحث متقدم
            </button>

            <button id="exportExcelBtn" class="btn btn-pill-action btn-pill-success">
                <i class="fas fa-file-excel"></i> تصدير CSV
            </button>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card premium-card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table id="orders-table" class="table table-hover table-borderless align-middle w-100 mb-0">
                            <thead>
                                <tr>
                                    @if(auth()->user()->isAdmin())
                                        <th width="40" class="text-center">
                                            <input type="checkbox" id="selectAllOrders" title="تحديد الكل"
                                                class="form-check-input shadow-sm">
                                        </th>
                                    @endif
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
                                    <th class="text-center">حالة الخصم</th>
                                    <th class="text-center">الإجراءات</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Add Notes Modal --}}
    {{-- Add Notes Modal --}}
    <div class="modal fade premium-modal" id="addNoteModal" tabindex="-1" aria-labelledby="addNoteModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center justify-content-between">
                    <h5 class="modal-title fs-4" id="addNoteModalLabel"><i
                            class="fas fa-comment-dots text-primary me-2"></i>تعليقات الطلب</h5>
                    <button type="button" class="btn-close m-0" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 bg-light">
                    {{-- Add Note Card --}}
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-body p-4">
                            <form id="addNoteForm">
                                <input type="hidden" id="noteOrderId">
                                <div class="mb-3">
                                    <label for="noteContent" class="form-label fw-bold text-dark">أضف تعليقاً جديداً</label>
                                    <textarea class="form-control bg-light border-0" id="noteContent" rows="3"
                                        placeholder="اكتب ملاحظاتك هنا..."
                                        style="border-radius: 1rem; box-shadow: inset 0 2px 4px rgba(0,0,0,0.02); padding: 1rem; resize: none;"></textarea>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <button type="submit" class="btn btn-pill-action btn-pill-primary">
                                        <i class="fas fa-paper-plane me-2"></i> إرسال التعليق
                                    </button>
                                    <button type="button"
                                        class="btn btn-pill-action btn-light text-muted border border-light-subtle"
                                        id="clearNoteBtn">
                                        <i class="fas fa-eraser me-2"></i> مسح الحقل
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Existing Notes Card --}}
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-4 text-dark"><i class="fas fa-history text-secondary me-2"></i>سجل
                                التعليقات</h6>
                            <ul id="notesList" class="chat-feed">
                                <li class="text-muted text-center py-4"><i
                                        class="fas fa-inbox fa-2x mb-3 opacity-25"></i><br>لا يوجد تعليقات بعد</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Advanced Search Modal --}}
    {{-- Advanced Search Modal --}}
    <div class="modal fade premium-modal" id="advancedSearchModal" tabindex="-1" aria-labelledby="advancedSearchModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center justify-content-between">
                    <h5 class="modal-title fs-5" id="advancedSearchModalLabel"><i
                            class="fas fa-filter text-primary me-2"></i>البحث المتقدم</h5>
                    <button type="button" class="btn-close m-0" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 bg-light">
                    <form id="advancedSearchForm">
                        <div class="mb-4">
                            <label for="dateFrom" class="form-label fw-bold text-dark">من تاريخ</label>
                            <input type="date" id="dateFrom" name="date_from"
                                class="form-control border-0 shadow-sm rounded-3 px-3 py-2 bg-white">
                        </div>
                        <div class="mb-2">
                            <label for="dateTo" class="form-label fw-bold text-dark">إلى تاريخ</label>
                            <input type="date" id="dateTo" name="date_to"
                                class="form-control border-0 shadow-sm rounded-3 px-3 py-2 bg-white">
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-0 p-4 bg-light d-flex justify-content-between">
                    <button type="button" id="clearFiltersBtn" class="btn btn-pill-action btn-light border shadow-sm">
                        <i class="fas fa-times me-1"></i> مسح الفلاتر
                    </button>
                    <button type="button" id="applyFiltersBtn" class="btn btn-pill-action btn-pill-primary">
                        <i class="fas fa-check me-1"></i> تطبيق الفلاتر
                    </button>
                </div>
            </div>
        </div>
    </div>
    {{-- Modal التنبيه الإبداعي لخصم المجموعة في صفحة الفهرس --}}
    <div class="modal fade" id="groupWarningIndexModal" tabindex="-1" aria-hidden="true"
        style="direction: rtl; text-align: right;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">

                {{-- هيدر المودال --}}
                <div
                    class="modal-header border-0 pb-0 pt-4 px-4 position-relative d-flex justify-content-between align-items-start">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                            style="width: 50px; height: 50px; background-color: rgba(220, 53, 69, 0.1);">
                            <i class="fas fa-exclamation-triangle fa-lg text-danger"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-dark mb-1">المجموعة لم تكتمل!</h5>
                            <p class="text-muted small mb-0">يرجى الانتباه لعدد الطلبات في هذه الخطة</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close m-0" data-bs-dismiss="modal" aria-label="Close"
                        style="opacity: 0.5;"></button>
                </div>

                <div class="modal-body px-4 py-4">
                    {{-- معلومات الخطة --}}
                    <div class="p-3 mb-4 rounded-3" style="background: #f8f9fa; border: 1px solid #e9ecef;">
                        <div class="d-flex align-items-center mb-1">
                            <i class="fas fa-gem text-primary me-2"></i>
                            <span class="text-secondary fw-semibold">الخطة المُطبَّقة:</span>
                        </div>
                        <div class="fs-5 fw-bold text-dark ms-4 pl-2" id="modalAppliedPlan"></div>
                    </div>

                    {{-- شريط التقدم والأرقام --}}
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-end mb-2">
                            <div>
                                <span class="text-muted fw-bold d-block" style="font-size: 0.85rem;">التقدم الحالي</span>
                                <strong class="fs-4 text-danger" id="modalCurrentCount"></strong>
                                <span class="text-muted mx-1">من</span>
                                <strong class="fs-5 text-dark" id="modalRequiredCount"></strong>
                                <span class="text-muted small">شخص</span>
                            </div>
                            <div class="text-end">
                                <span class="badge rounded-pill bg-danger-subtle text-danger fw-bold px-3 py-2"
                                    id="modalMissingCount">
                                </span>
                            </div>
                        </div>

                        <div class="progress" style="height: 10px; border-radius: 10px; background-color: #ffe6e6;">
                            <div id="modalProgressBar"
                                class="progress-bar bg-danger progress-bar-striped progress-bar-animated"
                                role="progressbar"></div>
                        </div>
                    </div>

                    {{-- قسم السعر --}}
                    <div class="d-flex align-items-center justify-content-between p-3 rounded-4"
                        style="background: linear-gradient(135deg, #fff3cd 0%, #ffecb3 100%); border: 1px solid rgba(255, 193, 7, 0.3);">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle d-flex align-items-center justify-content-center me-2"
                                style="width: 35px; height: 35px; background-color: rgba(255, 255, 255, 0.5);">
                                <i class="fas fa-money-bill-wave text-warning"
                                    style="filter: drop-shadow(0px 2px 2px rgba(0,0,0,0.1));"></i>
                            </div>
                            <span class="fw-bold text-dark">السعر المُطبَّق</span>
                        </div>
                        <h4 class="mb-0 fw-black text-dark" style="letter-spacing: -0.5px;">
                            <span id="modalAppliedPrice"></span> <span class="fs-6 text-muted">JOD</span>
                        </h4>
                    </div>
                </div>

                <div class="modal-footer border-0 px-4 pb-4 pt-0 text-start w-100 d-block">
                    <button type="button" class="btn btn-light w-100 fw-bold py-2 rounded-3 text-secondary"
                        data-bs-dismiss="modal" style="background-color: #f1f3f5;">إغلاق التنبيه</button>
                </div>
            </div>
        </div>
    </div>
    <link href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function () {

            // مصفوفة المصممين + صلاحيات المستخدم الحالي
            const DESIGNERS = @json($designers);
            const DISCOUNT_CODES = @json($discountCodes);
            const IS_ADMIN = @json(auth()->user()->isAdmin());
            const IS_SUPERVISOR = @json(method_exists(auth()->user(), 'isSupervisor') ? auth()->user()->isSupervisor() : false);
            const IS_PRINTER = @json(method_exists(auth()->user(), 'isPrinter') ? auth()->user()->isPrinter() : false);
            const CURRENT_USER_ID = @json(auth()->id());

            function loadNotes(orderId) {
                $('#notesList').html('<li class="list-group-item text-muted">Loading...</li>');

                $.ajax({
                    url: '/orders/' + orderId + '/notes',
                    method: 'GET',
                    success: function (response) {
                        const notes = response.notes || [];
                        const $list = $('#notesList');
                        $list.empty();

                        if (notes.length === 0) {
                            $list.append('<li class="text-muted text-center py-4"><i class="fas fa-inbox fa-2x mb-3 opacity-25"></i><br>لا يوجد تعليقات بعد</li>');
                            return;
                        }

                        notes.forEach(function (note) {
                            const itemHtml = `
                                                                                                                                                                                                        <li class="chat-message">
                                                                                                                                                                                                            <div class="chat-header">
                                                                                                                                                                                                                <span class="chat-author"><i class="fas fa-user-circle me-1"></i>${note.user_name}</span>
                                                                                                                                                                                                                <span class="chat-time"><i class="far fa-clock me-1"></i>${note.created_at}</span>
                                                                                                                                                                                                            </div>
                                                                                                                                                                                                            <p class="chat-content">${note.content}</p>
                                                                                                                                                                                                        </li>
                                                                                                                                                                                                    `;
                            $list.append(itemHtml);
                        });
                    },
                    error: function () {
                        $('#notesList').html('<li class="text-danger text-center py-4"><i class="fas fa-exclamation-triangle fa-2x mb-3 opacity-50"></i><br>فشل في تحميل التعليقات.</li>');
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
                    data: function (d) {
                        d.status = $('#statusFilter').val();
                        d.additives = $('#additivesFilter').val();
                        d.date_from = $('#dateFrom').val();
                        d.date_to = $('#dateTo').val();
                        d.designer_id = $('#designerFilter').val();
                        d.code_name = $('#codeNameFilter').val();
                    },
                    error: function (xhr) {
                        console.error('DataTables AJAX error:', xhr);
                        console.error('Response text:', xhr.responseText);

                        Swal.fire({
                            title: 'خطأ في جلب البيانات',
                            text: 'الكود: ' + xhr.status + ' — افتحي Console / Network عشان تشوفي التفاصيل.',
                            icon: 'error',
                            confirmButtonColor: '#dc2626'
                        });
                    }
                },
                lengthMenu: [10, 25, 50, 100],
                pageLength: 10,
                columns: [
                    @if(auth()->user()->isAdmin())
                                                                                                                                                                                                                                                                                                                                                                {
                            data: null,
                            name: 'checkbox',
                            orderable: false,
                            searchable: false,
                            width: '50px',
                            render: function (data, type, row) {
                                return `<input type="checkbox" class="order-checkbox" value="${row.id}" data-order-id="${row.id}">`;
                            }
                        },
                    @endif
                    {
                        data: 'id',
                        name: 'id',
                        render: function (data, type, row) {
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
                        render: function (data, type, row) {

                            const statusConfig = {
                                new_order: {
                                    class: 'status-soft-primary', // لون أزرق للطلب الجديد
                                    label: 'طلب جديد'
                                },
                                needs_modification: {
                                    class: 'status-soft-danger', // لون أحمر لوجود تعديل
                                    label: 'يوجد تعديل'
                                },
                                Pending: {
                                    class: 'status-soft-warning',
                                    label: 'تم التصميم'
                                },
                                Completed: {
                                    class: 'status-soft-info',
                                    label: 'تم الاعتماد'
                                },
                                preparing: {
                                    class: 'status-soft-purple',
                                    label: 'قيد التجهيز'
                                },
                                Printed: {
                                    class: 'status-soft-primary',
                                    label: 'تم الطباعة'
                                },
                                Received: {
                                    class: 'status-soft-success',
                                    label: 'تم التسليم'
                                },
                                out_for_delivery: {
                                    class: 'status-soft-warning',
                                    label: 'خرج مع التوصيل'
                                },
                                returned: {
                                    class: 'status-soft-orange',
                                    label: 'مرتجع'
                                },
                                Canceled: {
                                    class: 'status-soft-maroon',
                                    label: 'رفض الإستلام'
                                }
                            };

                            const defaultConfig = statusConfig.error || { class: 'status-soft-info', label: data };
                            const currentStatus = statusConfig[data] || defaultConfig;

                            const allStatuses = Object.keys(@json($allowedStatuses));

                            // هل المستخدم يقدر يغير الحالة؟
                            const canChangeStatus =
                                IS_ADMIN ||
                                IS_SUPERVISOR ||
                                IS_PRINTER ||
                                (row.designer && row.designer.id === CURRENT_USER_ID);

                            // المدة اللي رجعناها من الـ Controller
                            const durationText = row.status_created_diff ?
                                row.status_created_diff :
                                '';

                            // لو ما عنده صلاحية → Badge كبير + المدة فقط
                            if (!canChangeStatus) {
                                return `
                                                                                                                                                                                            <div class="text-center">
                                                                                                                                                                                                <span class="status-badge-soft shadow-sm ${currentStatus.class}">
                                                                                                                                                                                                    ${currentStatus.label}
                                                                                                                                                                                                </span>
                                                                                                                                                                                            </div>
                                                                                                                                                                                        `;
                            }

                            // لو عنده صلاحية → Dropdown + المدة تحت
                            const dropdownItems = allStatuses
                                .filter(function (status) {
                                    return status !== data;
                                })
                                .map(function (status) {
                                    const cfg = statusConfig[status] || defaultConfig;
                                    return `
                                                                                                                                                                                                <li>
                                                                                                                                                                                                    <a class="dropdown-item change-status-item py-2"
                                                                                                                                                                                                       href="#"
                                                                                                                                                                                                       data-order-id="${row.id}"
                                                                                                                                                                                                       data-new-status="${status}">
                                                                                                                                                                                                        <span class="status-badge-soft w-100 ${cfg.class}">${cfg.label}</span>
                                                                                                                                                                                                    </a>
                                                                                                                                                                                                </li>
                                                                                                                                                                                            `;
                                })
                                .join('');
                            return `
                                                                                <div class="text-center">
                                                                                    <div class="dropdown d-inline">
                                                                                        <span
                                                                                            class="status-badge-soft shadow-sm dropdown-toggle ${currentStatus.class}"
                                                                                            id="statusDropdown${row.id}"
                                                                                            data-bs-toggle="dropdown"
                                                                                            aria-expanded="false"
                                                                                            style="cursor: pointer;">
                                                                                            ${currentStatus.label}
                                                                                        </span>
                                                                                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 py-2" style="z-index: 1050; min-width: 140px;" aria-labelledby="statusDropdown${row.id}">
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
                        render: function (data, type, row) {
                            const currentDesignerId = (data && data.id) ? data.id : null;

                            let optionsHtml = '';

                            DESIGNERS.forEach(function (designer) {
                                const selected = (currentDesignerId === designer.id) ? 'selected' : '';
                                optionsHtml += `<option value="${designer.id}" ${selected}>${designer.name}</option>`;
                            });

                            const notAssignedOption = '<option value="">غير معيّن</option>';

                            let disabledAttr = '';

                            if (!IS_ADMIN) {
                                if (currentDesignerId && currentDesignerId !== CURRENT_USER_ID) {
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
                        orderable: false,
                        render: function (data, type, row) {
                            if (data && data.name_ar) {
                                return data.name_ar;
                            } else if (typeof data === 'string') {
                                return data;
                            }
                            return '<span class="text-muted small">غير محدد</span>';
                        }
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
                        render: function (data, type, row) {
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
                        render: function (data, type, row) {
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
                        data: 'discount_info',
                        name: 'discount_info',
                        className: 'text-center',
                        orderable: false,
                        searchable: false,
                        render: function (data, type, row) {
                            if (!data) {
                                return '<span class="no-discount">—</span>';
                            }
                            const label = data.name || data.code;
                            if (!data.is_group) {
                                return `<span class="badge-discount badge-individual" title="${label}">
                                                        <i class="fas fa-user"></i> فردي
                                                    </span>`;
                            }
                            // مجموعة
                            if (data.incomplete) {
                                return `<span class="badge-discount badge-group-warn js-show-group-warning" 
                                                      data-plan="${label}" 
                                                      data-current="${data.group_count}" 
                                                      data-required="${data.required_count}" 
                                                      data-price="${row.price}"
                                                      style="cursor: pointer;" title="اضغط لعرض التفاصيل">
                                                    <i class="fas fa-users"></i> مجموعة
                                                    <span style="font-size:0.65rem;opacity:0.85;">(${data.group_count}/${data.required_count})</span>
                                                    <i class="fas fa-exclamation-triangle" style="font-size:0.6rem;"></i>
                                                </span>`;
                            }
                            return `<span class="badge-discount badge-group-ok" title="${label}">
                                                    <i class="fas fa-users"></i> مجموعة
                                                    <i class="fas fa-check" style="font-size:0.6rem;"></i>
                                                </span>`;
                        }
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
                rowCallback: function (row, data) {
                    $(row).removeClass('order-has-notes order-duplicate order-with-additives');

                    // 🔴 تلوين السطر بالأحمر إذا كان الاسم أو الرقم مكرر فقط
                    if (data.is_duplicate) {
                        $(row).addClass('order-duplicate');
                    }
                },

                initComplete: function () {
                    // 1. فلتر الحالة
                    const statusDropdown = $(`
                                                                                                                                                                                        <select id="statusFilter" class="form-select" style="width: 230px;height:34px; margin-left: 15px;">
                                                                                                                                                                                            <option value="">تصفية حسب الحالة</option>
                                                                                                                                                                                            <option value="new_order">طلب جديد</option>
                                                                                                                                                                                            <option value="needs_modification">يوجد تعديل</option>
                                                                                                                                                                                            <option value="Pending">تم التصميم</option>
                                                                                                                                                                                            <option value="Completed">تم الاعتماد</option>
                                                                                                                                                                                            <option value="preparing">قيد التجهيز</option>
                                                                                                                                                                                            <option value="Printed">تم الطباعة</option>
                                                                                                                                                                                            <option value="Received">تم التسليم</option>
                                                                                                                                                                                            <option value="out_for_delivery">خرج مع التوصيل</option>
                                                                                                                                                                                            <option value="returned">مرتجع</option>
                                                                                                                                                                                            <option value="Canceled">رفض الإستلام</option>
                                                                                                                                                                                        </select>
                                                                                                                                                                                    `);

                    // 2. فلتر الإضافات
                    const additivesDropdown = $(`
                                                                                                                                                                                        <select id="additivesFilter" class="form-select" style="width: 175px;height:34px; margin-left: 15px;">
                                                                                                                                                                                            <option value="">تصفية حسب الإضافات</option>
                                                                                                                                                                                            <option value="with_additives">مع إضافات</option>
                                                                                                                                                                                            <option value="with_out_additives">بدون إضافات</option>
                                                                                                                                                                                        </select>
                                                                                                                                                                                    `);

                    // 3. فلتر المصمم (هاد اللي كان ناقص عندك)
                    let designerOptions = '<option value="">تصفية حسب المصمم</option>';
                    designerOptions += '<option value="unassigned">غير معيّن</option>';

                    // جلب المصممين من المصفوفة اللي تم تمريرها من الكونترولر
                    if (typeof DESIGNERS !== 'undefined') {
                        DESIGNERS.forEach(function (designer) {
                            designerOptions += `<option value="${designer.id}">${designer.name}</option>`;
                        });
                    }

                    const designerDropdown = $(`
                                                                                                                                                                                        <select id="designerFilter" class="form-select" style="width: 175px;height:34px; margin-left: 15px;">
                                                                                                                                                                                            ${designerOptions}
                                                                                                                                                                                        </select>
                                                                                                                                                                                    `);
                    let codeNameOptions = '<option value="">تصفية حسب المجموعة</option>';
                    if (typeof DISCOUNT_CODES !== 'undefined') {
                        DISCOUNT_CODES.forEach(function (code) {
                            let displayName = code.code_name ? code.code_name : code.discount_code;
                            codeNameOptions += `<option value="${displayName}">${displayName}</option>`;
                        });
                    }
                    const codeNameDropdown = $(`
                                        <select id="codeNameFilter" class="form-select shadow-sm" style="width: 175px;height:34px; margin-left: 15px; border-radius: 50rem;">
                                            ${codeNameOptions}
                                        </select>
                                    `);
                    // 4. تنسيق الحاوية وإضافة الفلاتر
                    $('.dataTables_filter').css({
                        display: 'flex',
                        justifyContent: 'flex-end',
                        alignItems: 'center'
                    });

                    $('.dataTables_filter').append(designerDropdown);
                    $('.dataTables_filter').append(statusDropdown);
                    $('.dataTables_filter').append(additivesDropdown);
                    $('.dataTables_filter').append(codeNameDropdown);
                    // 5. تفعيل التحديث التلقائي عند تغيير أي فلتر
                    $('#statusFilter, #additivesFilter, #designerFilter, #codeNameFilter').on('change', function () {
                        table.ajax.reload();
                    });
                    let typingTimer;
                    $('#codeNameFilter').on('keyup', function () {
                        clearTimeout(typingTimer);
                        typingTimer = setTimeout(function () {
                            table.ajax.reload();
                        }, 500); // ينتظر نص ثانية بعد آخر ضغطة زر
                    });
                }
            }); // ← نهاية DataTable config

            // Export CSV
            $('#exportExcelBtn').on('click', function () {
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
            $(document).on('click', '.change-status-item', function (e) {
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
                    success: function (response) {
                        if (response.success) {
                            table.ajax.reload(null, false);
                        } else {
                            Swal.fire({ title: 'خطأ', text: response.message || 'فشل تحديث حالة الطلب.', icon: 'error', confirmButtonColor: '#dc2626' });
                        }
                    },
                    error: function (xhr) {
                        let msg = 'حدث خطأ أثناء تحديث الحالة.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        Swal.fire({ title: 'خطأ', text: msg, icon: 'error', confirmButtonColor: '#dc2626' });
                    }
                });
            });

            // تغيير المصمم
            $(document).on('change', '.order-designer-select', function () {
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
                    success: function (response) {
                        if (!response.success) {
                            Swal.fire({ title: 'خطأ', text: response.message || 'فشل تحديث المصمم.', icon: 'error', confirmButtonColor: '#dc2626' });
                            select.val(previousDesignerId || '');
                            return;
                        }

                        select.data('current-designer-id', newDesignerId || '');
                        table.ajax.reload(null, false);
                    },
                    error: function (xhr) {
                        let msg = 'حدث خطأ أثناء تحديث المصمم.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        Swal.fire({ title: 'خطأ', text: msg, icon: 'error', confirmButtonColor: '#dc2626' });
                        select.val(previousDesignerId || '');
                    }
                });
            });

            // فتح مودال النوتس
            $(document).on('click', '.btn-add-note', function (e) {
                e.preventDefault();
                const orderId = $(this).data('order-id');

                $('#noteOrderId').val(orderId);
                $('#noteContent').val('');
                loadNotes(orderId);
                $('#addNoteModal').modal('show');
            });

            // حفظ النوت
            $('#addNoteForm').on('submit', function (e) {
                e.preventDefault();

                const orderId = $('#noteOrderId').val();
                const content = $('#noteContent').val().trim();

                if (!content) {
                    Swal.fire({ title: 'تنبيه', text: 'يرجى إدخال ملاحظة.', icon: 'warning', confirmButtonColor: '#f59e0b' });
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
                    success: function (response) {
                        if (response.success) {
                            $('#noteContent').val('');
                            const note = response.note;
                            const newItem = `
                                                                                                                                                                                                <li class="chat-message">
                                                                                                                                                                                                    <div class="chat-header">
                                                                                                                                                                                                        <span class="chat-author"><i class="fas fa-user-circle me-1"></i>${note.user_name}</span>
                                                                                                                                                                                                        <span class="chat-time"><i class="far fa-clock me-1"></i>${note.created_at}</span>
                                                                                                                                                                                                    </div>
                                                                                                                                                                                                    <p class="chat-content">${note.content}</p>
                                                                                                                                                                                                </li>
                                                                                                                                                                                            `;
                            $('#notesList').prepend(newItem);
                            // Remove empty indicator if exists
                            $('#notesList').find('.text-muted.text-center').remove();
                            table.ajax.reload(null, false);
                        } else {
                            Swal.fire({ title: 'خطأ', text: 'فشل حفظ الملاحظة.', icon: 'error', confirmButtonColor: '#dc2626' });
                        }
                    },
                    error: function () {
                        Swal.fire({ title: 'خطأ', text: 'حدث خطأ أثناء حفظ الملاحظة.', icon: 'error', confirmButtonColor: '#dc2626' });
                    }
                });
            });

            // زر مسح محتوى النوت
            $('#clearNoteBtn').on('click', function () {
                $('#noteContent').val('');
            });

            // البحث المتقدم - تطبيق الفلاتر
            $('#applyFiltersBtn').on('click', function () {
                table.ajax.reload();
                $('#advancedSearchModal').modal('hide');
            });

            // البحث المتقدم - مسح الفلاتر
            $('#clearFiltersBtn').on('click', function () {
                $('#dateFrom').val('');
                $('#dateTo').val('');
                table.ajax.reload();
            });

            @if(auth()->user()->isAdmin())
                // ============================================
                // Bulk Delete Functionality (Admin Only)
                // ============================================

                // Update selected count and toggle bulk delete button
                function updateBulkDeleteButton() {
                    const checkedBoxes = $('.order-checkbox:checked');
                    const count = checkedBoxes.length;
                    $('#selectedCount').text(count);

                    const $btn = $('#bulkDeleteBtn');
                    if (count > 0) {
                        $btn.html('<i class="fas fa-trash me-1"></i> حذف المحدد (<span id="selectedCount">' + count + '</span>)');
                        $btn.prop('disabled', false).show();
                    } else {
                        $btn.prop('disabled', true).hide();
                    }
                }

                // Select/Deselect all orders
                $('#selectAllOrders').on('change', function () {
                    const isChecked = $(this).prop('checked');
                    $('.order-checkbox').prop('checked', isChecked);
                    updateBulkDeleteButton();
                });

                // Handle individual checkbox changes
                $(document).on('change', '.order-checkbox', function () {
                    updateBulkDeleteButton();

                    // Update "select all" checkbox state
                    const totalCheckboxes = $('.order-checkbox').length;
                    const checkedCheckboxes = $('.order-checkbox:checked').length;
                    $('#selectAllOrders').prop('checked', totalCheckboxes === checkedCheckboxes);
                });

                // Bulk delete handler
                $('#bulkDeleteBtn').on('click', function () {
                    const checkedBoxes = $('.order-checkbox:checked');
                    const orderIds = checkedBoxes.map(function () {
                        return $(this).val();
                    }).get();

                    if (orderIds.length === 0) {
                        Swal.fire({ title: 'تنبيه', text: 'لم يتم تحديد أي طلبات للحذف.', icon: 'warning', confirmButtonColor: '#f59e0b' });
                        return;
                    }

                    Swal.fire({
                        title: 'تأكيد الحذف الجماعي',
                        html: `هل أنت متأكد من حذف <strong>${orderIds.length}</strong> طلب؟<br><br><span style="color:#dc2626;font-weight:700;">هذا الإجراء لا يمكن التراجع عنه!</span>`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'نعم، احذف',
                        cancelButtonText: 'إلغاء',
                        confirmButtonColor: '#dc2626',
                        cancelButtonColor: '#94a3b8',
                        reverseButtons: true
                    }).then((result) => {
                        if (!result.isConfirmed) return;

                        const $btn = $('#bulkDeleteBtn');
                        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> جاري الحذف...');

                        $.ajax({
                            url: '{{ route('orders.bulkDelete') }}',
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                order_ids: orderIds
                            },
                            success: function (response) {
                                if (response.success) {
                                    Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: response.message, showConfirmButton: false, timer: 3000, timerProgressBar: true });
                                    $('#selectAllOrders').prop('checked', false);
                                    $('.order-checkbox').prop('checked', false);
                                    updateBulkDeleteButton();
                                    table.ajax.reload(null, false);
                                } else {
                                    Swal.fire({ title: 'خطأ', text: response.message || 'حدث خطأ أثناء حذف الطلبات.', icon: 'error', confirmButtonColor: '#dc2626' });
                                    const remainingCount = $('.order-checkbox:checked').length;
                                    if (remainingCount > 0) {
                                        $btn.prop('disabled', false).html('<i class="fas fa-trash me-1"></i> حذف المحدد (<span id="selectedCount">' + remainingCount + '</span>)');
                                    } else {
                                        updateBulkDeleteButton();
                                    }
                                }
                            },
                            error: function (xhr) {
                                let message = 'حدث خطأ أثناء حذف الطلبات.';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    message = xhr.responseJSON.message;
                                }
                                Swal.fire({ title: 'خطأ', text: message, icon: 'error', confirmButtonColor: '#dc2626' });
                                const remainingCount = $('.order-checkbox:checked').length;
                                if (remainingCount > 0) {
                                    $btn.prop('disabled', false).html('<i class="fas fa-trash me-1"></i> حذف المحدد (<span id="selectedCount">' + remainingCount + '</span>)');
                                } else {
                                    updateBulkDeleteButton();
                                }
                            }
                        });
                    });
                });

                // Handle single order delete
                $(document).on('click', '.delete-order', function (e) {
                    e.preventDefault();
                    const orderId = $(this).data('id');

                    Swal.fire({
                        title: 'تأكيد حذف الطلب',
                        html: `هل أنت متأكد من حذف الطلب <strong>#${orderId}</strong>؟<br><br><span style="color:#dc2626;font-weight:700;">هذا الإجراء لا يمكن التراجع عنه!</span>`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'نعم، احذف',
                        cancelButtonText: 'إلغاء',
                        confirmButtonColor: '#dc2626',
                        cancelButtonColor: '#94a3b8',
                        reverseButtons: true
                    }).then((result) => {
                        if (!result.isConfirmed) return;

                        $.ajax({
                            url: '/orders/' + orderId,
                            method: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function (response) {
                                if (response.success) {
                                    Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: response.message, showConfirmButton: false, timer: 2500, timerProgressBar: true });
                                    table.ajax.reload(null, false);
                                } else {
                                    Swal.fire({ title: 'خطأ', text: response.message || 'فشل حذف الطلب.', icon: 'error', confirmButtonColor: '#dc2626' });
                                }
                            },
                            error: function (xhr) {
                                let message = 'حدث خطأ أثناء حذف الطلب.';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    message = xhr.responseJSON.message;
                                }
                                Swal.fire({ title: 'خطأ', text: message, icon: 'error', confirmButtonColor: '#dc2626' });
                            }
                        });
                    });
                });

                // Update bulk delete button on table draw
                table.on('draw', function () {
                    updateBulkDeleteButton();
                    $('#selectAllOrders').prop('checked', false);
                });
            @endif
                // فتح مودال تنبيه المجموعة وتعبئة بياناته
                $(document).on('click', '.js-show-group-warning', function() {
                const plan = $(this).data('plan');
                    const current = parseInt($(this).data('current')) || 0;
                    const required = parseInt($(this).data('required')) || 0;
                    const price = $(this).data('price');

                    const missing = required - current;
                    const percentage = required > 0 ? (current / required) * 100 : 0;

                    $('#modalAppliedPlan').text(plan);
                    $('#modalCurrentCount').text(current);
                    $('#modalRequiredCount').text(required);
                    $('#modalMissingCount').text('ناقص ' + missing + ' أشخاص');
                    $('#modalProgressBar').css('width', percentage + '%');
                    $('#modalAppliedPrice').text(price);

                    $('#groupWarningIndexModal').modal('show');
                });
                                                                                                                                                                        }); // ← نهاية $(document).ready()
        </script>

@endsection