@extends('admin.layout')

@section('content')
    <style>
        .normalized-value {
            font-weight: bold;
        }

        /* ستايلات الـ Modal الفخم */
        .modal-creative .modal-content {
            border-radius: 24px;
            border: none;
            overflow: hidden;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
        }

        .modal-creative .modal-header {
            border-bottom: none;
            padding: 30px 30px 10px;
            display: flex;
            justify-content: flex-end;
        }

        .modal-creative .btn-close {
            background-color: #f1f3f9;
            border-radius: 50%;
            padding: 12px;
            opacity: 0.7;
            transition: all 0.3s;
        }

        .modal-creative .btn-close:hover {
            background-color: #e3e6f0;
            opacity: 1;
        }

        .modal-creative .modal-body {
            padding: 0 40px 30px;
            text-align: center;
        }

        .modal-creative .icon-box {
            width: 90px;
            height: 90px;
            background: rgba(231, 74, 59, 0.1);
            color: #e74a3b;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 45px;
            margin: 0 auto 25px;
            box-shadow: 0 0 0 10px rgba(231, 74, 59, 0.05);
            animation: pulse-danger 2s infinite;
        }

        @keyframes pulse-danger {
            0% {
                box-shadow: 0 0 0 0 rgba(231, 74, 59, 0.2);
            }

            70% {
                box-shadow: 0 0 0 15px rgba(231, 74, 59, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(231, 74, 59, 0);
            }
        }

        .modal-creative .modal-title-custom {
            font-weight: 800;
            color: #3a3b45;
            margin-bottom: 10px;
            font-size: 1.5rem;
        }

        .modal-creative .modal-desc {
            color: #858796;
            font-size: 1.1rem;
            margin-bottom: 30px;
        }

        .modal-creative .svg-name-highlight {
            background: #f8f9fc;
            padding: 10px 20px;
            border-radius: 12px;
            display: inline-block;
            font-weight: 700;
            color: #4e73df;
            border: 1px dashed #d1d3e2;
            margin-top: 10px;
        }

        .modal-creative .modal-footer {
            border-top: none;
            padding: 0 40px 40px;
            display: flex;
            gap: 15px;
            justify-content: center;
        }

        .modal-creative .btn-custom-cancel {
            background: #f8f9fc;
            color: #5a5c69;
            border: 1px solid #e3e6f0;
            border-radius: 14px;
            padding: 12px 25px;
            font-weight: 600;
            transition: all 0.3s;
            flex: 1;
        }

        .modal-creative .btn-custom-cancel:hover {
            background: #eaedf4;
        }

        .modal-creative .btn-custom-delete {
            background: #e74a3b;
            color: #fff;
            border: none;
            border-radius: 14px;
            padding: 12px 25px;
            font-weight: 600;
            box-shadow: 0 5px 15px rgba(231, 74, 59, 0.3);
            transition: all 0.3s;
            flex: 1;
        }

        .modal-creative .btn-custom-delete:hover {
            background: #be2617;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(231, 74, 59, 0.4);
        }
    </style>

    <div class="row">
        <div class="col-12">

            {{-- Alert Messages بتصميم عصري --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert"
                    style="border-radius: 10px;">
                    <i class="fas fa-check-circle me-2"></i><strong>نجاح!</strong> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert" style="border-radius: 10px;">
                    <i class="fas fa-exclamation-triangle me-2"></i><strong>تنبيه!</strong> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card shadow-sm border-0 mt-3">
                <div class="d-flex justify-content-between align-items-center p-3 flex-wrap gap-2">
                    <h1 class="mb-0 text-primary">SVG Names</h1>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-primary px-3 py-2 rounded-pill" id="openImportBtn">
                            <i class="fas fa-file-import me-1"></i> استيراد SVG بالجملة
                        </button>
                        <a href="{{ route('svg-names.create') }}" class="btn btn-sm btn-success px-3 py-2 rounded-pill">
                            <i class="fas fa-plus me-1"></i> إضافة اسم جديد
                        </a>
                    </div>
                </div>

                {{-- ===== BULK IMPORT PANEL ===== --}}
                <div id="importPanel" class="mx-3 mb-3" style="display:none;">
                    <div class="card border-2 border-primary border-opacity-25 rounded-4">
                        <div class="card-body p-4">

                            {{-- Dropzone (compact mode عند وجود ملفات) --}}
                            <div id="svgDropzone"
                                class="rounded-4 d-flex flex-column align-items-center justify-content-center gap-2"
                                style="border: 2.5px dashed #4e73df; background:#f8f9ff; cursor:pointer; transition: all .3s ease; min-height:180px; padding: 2rem;">
                                <i id="dropzoneIcon" class="fas fa-cloud-upload-alt fa-3x text-primary opacity-75"></i>
                                <p id="dropzoneTitle" class="mb-0 fw-bold text-primary fs-5">اسحب ملفات SVG هنا</p>
                                <p id="dropzoneSubtitle" class="text-muted small mb-0">أو اضغط لاختيار الملفات — يقبل .svg
                                    فقط، حتى 500 ملف</p>
                                <input type="file" id="svgFileInput" multiple accept=".svg" style="display:none">
                            </div>

                            {{-- Preview Section --}}
                            <div id="previewSection" style="display:none;" class="mt-3">

                                {{-- Sticky Action Bar 👈 الزر الرئيسي دايماً فوق --}}
                                <div class="position-sticky top-0 bg-white py-3 mb-3 border-bottom"
                                    style="z-index: 10; margin: 0 -1.5rem; padding: 0.75rem 1.5rem !important;">
                                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                        <div class="d-flex align-items-center gap-2">
                                            <h6 class="fw-bold mb-0">
                                                <i class="fas fa-list-check text-primary me-1"></i>
                                                معاينة الملفات
                                                <span class="badge bg-primary rounded-pill ms-1" id="previewCount">0</span>
                                            </h6>
                                        </div>
                                        <div class="d-flex gap-2 flex-wrap">
                                            <button class="btn btn-success rounded-pill px-4 fw-bold shadow-sm"
                                                id="startImportBtn">
                                                <i class="fas fa-rocket me-2"></i> ابدأ الاستيراد
                                            </button>
                                            <button class="btn btn-outline-secondary rounded-pill px-3 fw-bold"
                                                id="clearFilesBtn">
                                                <i class="fas fa-broom me-1"></i> مسح
                                            </button>
                                            <button class="btn btn-outline-danger rounded-pill px-3 fw-bold"
                                                id="cancelImportBtn">
                                                <i class="fas fa-ban me-1"></i> إلغاء
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div
                                    style="max-height:320px; overflow-y:auto; border-radius:12px; border:1px solid #e3e6f0;">
                                    <table class="table table-sm mb-0 align-middle">
                                        <thead class="table-light sticky-top">
                                            <tr>
                                                <th style="width:50px">#</th>
                                                <th>معاينة</th>
                                                <th>اسم الملف</th>
                                                <th>الاسم المنسّق</th>
                                                <th class="text-center">الحالة</th>
                                            </tr>
                                        </thead>
                                        <tbody id="previewTableBody"></tbody>
                                    </table>
                                </div>

                                {{-- Stats --}}
                                <div class="d-flex gap-3 mt-3 flex-wrap">
                                    <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill fs-6">
                                        <i class="fas fa-plus-circle me-1"></i> جديد: <strong id="statNew">0</strong>
                                    </span>
                                    <span class="badge bg-warning-subtle text-warning px-3 py-2 rounded-pill fs-6">
                                        <i class="fas fa-sync me-1"></i> سيُحدَّث: <strong id="statUpdate">0</strong>
                                    </span>
                                    <span class="badge bg-danger-subtle text-danger px-3 py-2 rounded-pill fs-6">
                                        <i class="fas fa-exclamation-circle me-1"></i> غير صالح: <strong
                                            id="statInvalid">0</strong>
                                    </span>
                                </div>

                                <div id="importProgress" style="display:none;" class="mt-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <small class="text-muted fw-bold">جاري الاستيراد...</small>
                                        <small id="progressText" class="text-primary fw-bold">0%</small>
                                    </div>
                                    <div class="progress" style="height:10px; border-radius:50rem;">
                                        <div id="progressBar"
                                            class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                                            role="progressbar" style="width:0%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- ===== END BULK IMPORT PANEL ===== --}}

                <div class="card-body">
                    <table id="responsive-datatable" class="table table-bordered dt-responsive align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center">#</th>
                                <th class="text-center">الاسم</th>
                                <th class="text-center">الاسم المنسّق</th>
                                <th class="text-center">حالة الكود</th>
                                <th class="text-center" width="20%">إجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($svgNames as $svgName)
                                <tr class="{{ empty($svgName->svg_code) ? 'table-danger' : '' }}">
                                    <td class="text-center">
                                        {{ $loop->iteration + ($svgNames->currentPage() - 1) * $svgNames->perPage() }}
                                    </td>

                                    <td class="text-center fw-bold">
                                        {{ $svgName->name }}
                                    </td>

                                    <td class="text-center">
                                        <span class="normalized-value text-muted">{{ $svgName->normalized_name }}</span>
                                    </td>

                                    <td class="text-center">
                                        @if(!empty($svgName->svg_code))
                                            <span class="badge bg-success rounded-pill px-3 py-2">
                                                <i class="fas fa-check-circle me-1"></i> كود موجود
                                            </span>
                                        @else
                                            <span class="badge bg-danger rounded-pill px-3 py-2">
                                                <i class="fas fa-times-circle me-1"></i> كود ناقص
                                            </span>
                                        @endif
                                    </td>

                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="{{ route('svg-names.edit', $svgName) }}"
                                                class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                                <i class="fas fa-edit me-1"></i> تعديل
                                            </a>

                                            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3"
                                                data-bs-toggle="modal" data-bs-target="#deleteNameModal{{ $svgName->id }}">
                                                <i class="fas fa-trash me-1"></i> حذف
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <div class="modal fade modal-creative" id="deleteNameModal{{ $svgName->id }}" tabindex="-1"
                                    aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="icon-box">
                                                    <i class="fas fa-trash-alt"></i>
                                                </div>
                                                <h3 class="modal-title-custom">تأكيد الحذف</h3>
                                                <p class="modal-desc">أنت على وشك حذف هذا الاسم نهائياً من النظام. لا يمكن
                                                    التراجع عن هذا الإجراء.</p>

                                                <div class="svg-name-highlight">
                                                    الاسم: {{ $svgName->name }} <br>
                                                    <span class="text-dark fs-6">{{ $svgName->normalized_name }}</span>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-custom-cancel"
                                                    data-bs-dismiss="modal">إلغاء الأمر</button>
                                                <form action="{{ route('svg-names.destroy', $svgName->id) }}" method="POST"
                                                    class="d-inline flex-grow-1">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-custom-delete w-100">نعم، احذف
                                                        نهائياً</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        <i class="fas fa-info-circle fs-4 mb-2 d-block"></i>
                                        لا توجد أسماء حتى الآن.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{-- Pagination --}}
                    <div class="mt-4 d-flex justify-content-center">
                        {{ $svgNames->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== Import Result Modal ===== --}}
    <div class="modal fade modal-creative" id="importResultModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="icon-box" id="resultIconBox"
                        style="background: rgba(28, 200, 138, 0.1); color: #1cc88a; box-shadow: 0 0 0 10px rgba(28, 200, 138, 0.05); animation: none;">
                        <i class="fas fa-check-double"></i>
                    </div>
                    <h3 class="modal-title-custom">اكتمل الاستيراد بنجاح!</h3>
                    <p class="modal-desc" id="resultDesc">تم معالجة جميع الملفات وحفظها في قاعدة البيانات.</p>

                    <div class="row g-3 mt-2">
                        <div class="col-md-4">
                            <div class="p-3 rounded-4 text-center" style="background:#e8f8f3; border:1px solid #c3eddd;">
                                <i class="fas fa-plus-circle text-success fs-2 mb-2"></i>
                                <div class="fw-bold text-success fs-3" id="resultCreated">0</div>
                                <small class="text-muted fw-bold">ملف جديد</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 rounded-4 text-center" style="background:#fff8e6; border:1px solid #ffe7a3;">
                                <i class="fas fa-sync text-warning fs-2 mb-2"></i>
                                <div class="fw-bold text-warning fs-3" id="resultUpdated">0</div>
                                <small class="text-muted fw-bold">ملف محدَّث</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 rounded-4 text-center" style="background:#fdecea; border:1px solid #f8c5c0;">
                                <i class="fas fa-exclamation-triangle text-danger fs-2 mb-2"></i>
                                <div class="fw-bold text-danger fs-3" id="resultFailed">0</div>
                                <small class="text-muted fw-bold">ملف فشل</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-custom-cancel" data-bs-dismiss="modal">إغلاق</button>
                    <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold" id="reloadAfterImportBtn">
                        <i class="fas fa-rotate me-2"></i> تحديث الصفحة
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const IMPORT_URL = "{{ route('svg-names.bulk-import') }}";
            const CSRF = "{{ csrf_token() }}";
            // الأسماء المنسّقة الموجودة حالياً (لتحديد new/update)
            const EXISTING = @json($allNormalizedNames);

            // ---- helpers ----
            function normalizeArabic(name) {
                name = name.trim().toLowerCase();
                name = name.replace(/[أإآٱ]/g, 'ا')
                    .replace(/ة/g, 'ه')
                    .replace(/ى/g, 'ي');
                name = name.replace(/[\u0610-\u061A\u064B-\u065F\u0670\u06D6-\u06DC\u06DF-\u06E8\u06EA-\u06ED]/g, '');
                name = name.replace(/\bعبد\s+/g, 'عبد');
                name = name.replace(/\s+/g, '');
                return name;
            }

            let filesData = []; // [{name, normalized, code, valid, isNew}]

            // ---- DOM ----
            const openBtn = document.getElementById('openImportBtn');
            const panel = document.getElementById('importPanel');
            const dropzone = document.getElementById('svgDropzone');
            const fileInput = document.getElementById('svgFileInput');
            const previewSec = document.getElementById('previewSection');
            const tbody = document.getElementById('previewTableBody');
            const previewCount = document.getElementById('previewCount');
            const statNew = document.getElementById('statNew');
            const statUpdate = document.getElementById('statUpdate');
            const statInvalid = document.getElementById('statInvalid');
            const startBtn = document.getElementById('startImportBtn');
            const cancelBtn = document.getElementById('cancelImportBtn');
            const clearBtn = document.getElementById('clearFilesBtn');
            const progress = document.getElementById('importProgress');
            const progressBar = document.getElementById('progressBar');
            const progressTxt = document.getElementById('progressText');

            // Toggle panel
            openBtn.addEventListener('click', () => {
                panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
            });
            cancelBtn.addEventListener('click', () => { panel.style.display = 'none'; reset(); });
            clearBtn.addEventListener('click', reset);

            // Dropzone events
            dropzone.addEventListener('click', () => fileInput.click());
            dropzone.addEventListener('dragover', e => { e.preventDefault(); dropzone.style.background = '#eef2ff'; });
            dropzone.addEventListener('dragleave', () => { dropzone.style.background = '#f8f9ff'; });
            dropzone.addEventListener('drop', e => {
                e.preventDefault();
                dropzone.style.background = '#f8f9ff';
                handleFiles([...e.dataTransfer.files]);
            });
            fileInput.addEventListener('change', () => handleFiles([...fileInput.files]));

            const dropzoneIcon = document.getElementById('dropzoneIcon');
            const dropzoneTitle = document.getElementById('dropzoneTitle');
            const dropzoneSubtitle = document.getElementById('dropzoneSubtitle');

            function compactDropzone(fileCount) {
                dropzone.style.minHeight = '70px';
                dropzone.style.padding = '0.75rem 1.5rem';
                dropzoneIcon.className = 'fas fa-check-circle fa-lg text-success';
                dropzoneTitle.className = 'mb-0 fw-bold text-success';
                dropzoneTitle.textContent = `✓ تم تحميل ${fileCount} ملف`;
                dropzoneSubtitle.textContent = 'اضغط لإضافة ملفات أخرى';
                dropzoneSubtitle.classList.add('mb-0');
            }

            function expandDropzone() {
                dropzone.style.minHeight = '180px';
                dropzone.style.padding = '2rem';
                dropzoneIcon.className = 'fas fa-cloud-upload-alt fa-3x text-primary opacity-75';
                dropzoneTitle.className = 'mb-0 fw-bold text-primary fs-5';
                dropzoneTitle.textContent = 'اسحب ملفات SVG هنا';
                dropzoneSubtitle.textContent = 'أو اضغط لاختيار الملفات — يقبل .svg فقط، حتى 500 ملف';
            }

            function reset() {
                filesData = [];
                tbody.innerHTML = '';
                previewSec.style.display = 'none';
                progress.style.display = 'none';
                fileInput.value = '';
                startBtn.disabled = false;
                startBtn.innerHTML = '<i class="fas fa-rocket me-2"></i> ابدأ الاستيراد';
                expandDropzone();
            }

            function handleFiles(files) {
                const svgFiles = [...files].filter(f => f.name.toLowerCase().endsWith('.svg'));
                if (!svgFiles.length) { alert('يرجى اختيار ملفات .svg فقط'); return; }
                if (svgFiles.length > 500) { alert('الحد الأقصى 500 ملف في المرة الواحدة'); return; }

                filesData = new Array(svgFiles.length); // pre-allocate بالترتيب
                tbody.innerHTML = '';
                progress.style.display = 'none';

                let pending = svgFiles.length;

                svgFiles.forEach((file, idx) => {
                    const reader = new FileReader();
                    reader.onload = e => {
                        const code = e.target.result;
                        const rawName = file.name.replace(/\.svg$/i, '');
                        const normalized = normalizeArabic(rawName);
                        const valid = normalized.length > 0;
                        const isNew = valid && !EXISTING.includes(normalized);

                        filesData[idx] = { name: rawName, normalized, code, valid, isNew, idx };

                        if (--pending === 0) renderPreview();
                    };
                    reader.onerror = () => {
                        filesData[idx] = { name: file.name, normalized: '', code: '', valid: false, isNew: false, idx };
                        if (--pending === 0) renderPreview();
                    };
                    reader.readAsText(file, 'UTF-8');
                });
            }
            function renderPreview() {
                filesData.sort((a, b) => a.idx - b.idx);
                tbody.innerHTML = '';

                let cntNew = 0, cntUpd = 0, cntInv = 0;

                filesData.forEach((f, i) => {
                    if (!f.valid) cntInv++;
                    else if (f.isNew) cntNew++;
                    else cntUpd++;

                    const statusBadge = !f.valid
                        ? `<span class="badge bg-danger rounded-pill">غير صالح</span>`
                        : f.isNew
                            ? `<span class="badge bg-success rounded-pill">جديد</span>`
                            : `<span class="badge bg-warning text-dark rounded-pill">سيُحدَّث</span>`;

                    // مصغّر الـ SVG
                    const svgBlob = new Blob([f.code], { type: 'image/svg+xml' });
                    const svgUrl = URL.createObjectURL(svgBlob);

                    const tr = document.createElement('tr');
                    tr.style.opacity = f.valid ? '1' : '0.45';
                    tr.innerHTML = `
                                                                        <td class="text-muted small">${i + 1}</td>
                                                                        <td><img src="${svgUrl}" style="width:40px;height:40px;object-fit:contain;border-radius:6px;background:#f8f9fc;padding:4px;"></td>
                                                                        <td class="fw-bold">${f.name}</td>
                                                                        <td class="text-muted font-monospace small">${f.normalized || '—'}</td>
                                                                        <td class="text-center">${statusBadge}</td>
                                                                    `;
                    tbody.appendChild(tr);
                });

                statNew.textContent = cntNew;
                statUpdate.textContent = cntUpd;
                statInvalid.textContent = cntInv;
                previewCount.textContent = filesData.length;
                previewSec.style.display = 'block';
                compactDropzone(filesData.length);
            }

            // ---- Import ----
            startBtn.addEventListener('click', async () => {
                const validFiles = filesData.filter(f => f.valid);
                if (!validFiles.length) { alert('لا يوجد ملفات صالحة للاستيراد'); return; }

                startBtn.disabled = true;
                startBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> جاري الاستيراد...';
                progress.style.display = 'block';

                const BATCH = 50; // نرسل دفعات عشان ما نثقّل السيرفر
                let imported = 0;
                const results = { created: 0, updated: 0, failed: [] };

                for (let i = 0; i < validFiles.length; i += BATCH) {
                    const batch = validFiles.slice(i, i + BATCH).map(f => ({
                        name: f.name,
                        code: f.code,
                    }));

                    try {
                        const res = await fetch(IMPORT_URL, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': CSRF,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({ svgs: batch }),
                        });
                        const json = await res.json();
                        if (json.success) {
                            results.created += json.results.created;
                            results.updated += json.results.updated;
                            results.failed.push(...json.results.failed);
                        }
                    } catch (e) {
                        results.failed.push('batch error');
                    }

                    imported += batch.length;
                    const pct = Math.round((imported / validFiles.length) * 100);
                    progressBar.style.width = pct + '%';
                    progressTxt.textContent = pct + '%';
                }

                // النتيجة النهائية
                progressBar.classList.remove('progress-bar-animated', 'progress-bar-striped');
                progressBar.classList.add('bg-success');
                progressTxt.textContent = '100%';

                // عرض المودال
                document.getElementById('resultCreated').textContent = results.created;
                document.getElementById('resultUpdated').textContent = results.updated;
                document.getElementById('resultFailed').textContent = results.failed.length;

                const resultModal = new bootstrap.Modal(document.getElementById('importResultModal'));
                resultModal.show();
            });

            // زر تحديث الصفحة من المودال
            document.getElementById('reloadAfterImportBtn').addEventListener('click', () => {
                window.location.reload();
            });
        })();
    </script>

@endsection