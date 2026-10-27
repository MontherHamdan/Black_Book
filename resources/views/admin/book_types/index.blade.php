@extends('admin.layout')

@push('styles')
    <style>
        .book-type-table th {
            vertical-align: middle;
            font-size: 0.88rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .book-type-table td {
            vertical-align: middle;
        }
        .color-dot {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: inline-block;
            border: 2px solid #fff;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
            margin-right: -4px;
        }
        .color-preview-pill {
            cursor: pointer;
            transition: all 0.2s ease-in-out;
        }
        .color-preview-pill:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1) !important;
        }
        .table-thumb {
            width: 65px;
            height: 65px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #e9ecef;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .color-row-thumb {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }
    </style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center p-3 border-bottom bg-white rounded-top">
                <div>
                    <h4 class="mb-1 text-primary fw-bold">
                        <i class="fas fa-book me-2"></i> Book Types
                    </h4>
                    <span class="text-muted small">إدارة أنواع الدفاتر، الأسعار، الوسائط وخيارات الألوان</span>
                </div>
                <a href="{{ route('book-types.create') }}" class="btn btn-success shadow-sm rounded-pill px-3">
                    <i class="fas fa-plus-circle me-1"></i> Add New Book Type
                </a>
            </div>

            <!-- Table Section -->
            <div class="card-body">
                <div class="table-responsive">
                    <table id="responsive-datatable" class="table table-hover table-bordered book-type-table align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 50px;">ID</th>
                                <th class="text-center" style="width: 80px;">Image</th>
                                <th class="text-center">Arabic Name</th>
                                <th class="text-center">English Name</th>
                                <th class="text-center" style="width: 100px;">Price</th>
                                <th class="text-center" style="width: 140px;">Colors</th>
                                <th class="text-center">Description (AR)</th>
                                <th class="text-center">Description (EN)</th>
                                <th class="text-center" style="width: 90px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($bookTypes as $bookType)
                            <tr>
                                <td class="text-center text-muted fw-bold">{{ $bookType->id }}</td>
                                <td class="text-center">
                                    <img src="{{ $bookType->image }}"
                                         alt="{{ $bookType->name_ar }}"
                                         class="table-thumb"
                                         loading="lazy">
                                </td>
                                <td class="text-center fw-bold text-dark">{{ $bookType->name_ar }}</td>
                                <td class="text-center text-muted">{{ $bookType->name_en }}</td>
                                <td class="text-center">
                                    <span class="badge bg-soft-success text-success fs-6 fw-bold px-2 py-1">
                                        {{ $bookType->price }} د.أ
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if ($bookType->colors && $bookType->colors->isNotEmpty())
                                        <button type="button"
                                                class="btn btn-sm btn-outline-primary rounded-pill px-2 py-1 shadow-sm d-inline-flex align-items-center gap-1 color-preview-pill"
                                                data-bs-toggle="modal"
                                                data-bs-target="#colorsModal{{ $bookType->id }}"
                                                title="انقر لإدارة الألوان">
                                            <div class="d-inline-flex align-items-center me-1">
                                                @foreach ($bookType->colors->take(4) as $col)
                                                    <span class="color-dot"
                                                          style="background-color: {{ $col->color_code ?? '#cccccc' }};"
                                                          title="{{ $col->name_ar }}"></span>
                                                @endforeach
                                            </div>
                                            <span class="fw-bold small">{{ $bookType->colors->count() }} ألوان</span>
                                        </button>
                                    @else
                                        <button type="button"
                                                class="btn btn-sm btn-outline-secondary rounded-pill px-2 py-1 text-muted"
                                                data-bs-toggle="modal"
                                                data-bs-target="#colorsModal{{ $bookType->id }}"
                                                title="انقر لإضافة ألوان لهذا المنتج">
                                            <i class="fas fa-plus-circle me-1 text-primary"></i> إضافة ألوان
                                        </button>
                                    @endif
                                </td>
                                <td class="text-center text-muted small" style="max-width: 180px;">
                                    {{ Str::limit($bookType->description_ar, 60, '...') ?: '-' }}
                                </td>
                                <td class="text-center text-muted small" style="max-width: 180px;">
                                    {{ Str::limit($bookType->description_en, 60, '...') ?: '-' }}
                                </td>
                                <td class="text-center">
                                    <!-- Actions Dropdown -->
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light border shadow-sm dropdown-toggle"
                                                type="button"
                                                id="dropdownMenuButton{{ $bookType->id }}"
                                                data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm"
                                            aria-labelledby="dropdownMenuButton{{ $bookType->id }}">
                                            <li>
                                                <a href="{{ route('book-types.edit', $bookType) }}"
                                                   class="dropdown-item">
                                                    <i class="fas fa-edit me-2 text-warning"></i>تعديل الدفتر
                                                </a>
                                            </li>
                                            <li>
                                                <button type="button" class="dropdown-item"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#colorsModal{{ $bookType->id }}">
                                                    <i class="fas fa-palette me-2 text-primary"></i>إدارة الألوان ({{ $bookType->colors->count() }})
                                                </button>
                                            </li>
                                            <li>
                                                <button type="button" class="dropdown-item"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#subMediaModal{{ $bookType->id }}">
                                                    <i class="fas fa-images me-2 text-info"></i>إدارة الصور الإضافية
                                                </button>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('book-types.destroy', $bookType) }}"
                                                      method="POST"
                                                      onsubmit="return confirm('هل أنت متأكد من حذف هذا النوع؟');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="fas fa-trash-alt me-2"></i>حذف
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x mb-2 d-block text-secondary"></i>
                                    لا توجد أنواع دفاتر مضافة حتى الآن.
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

{{-- ============================================================== --}}
{{-- 🔴 قسم النوافذ المنبثقة (Modals) خارج الجدول لمنع تداخل DataTables --}}
{{-- ============================================================== --}}
@foreach ($bookTypes as $bookType)
    <!-- Colors Modal -->
    <div class="modal fade" id="colorsModal{{ $bookType->id }}" tabindex="-1"
         aria-labelledby="colorsModalLabel{{ $bookType->id }}" aria-hidden="true"
         style="direction: rtl; text-align: right;">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <h5 class="modal-title d-flex align-items-center" id="colorsModalLabel{{ $bookType->id }}">
                        <i class="fas fa-palette text-primary me-2 ms-2"></i>
                        <span>إدارة ألوان: <strong>{{ $bookType->name_ar }}</strong></span>
                    </h5>
                    <button type="button" class="btn-close ms-0 me-auto" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    {{-- قائمة الألوان الحالية --}}
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0 text-dark">
                            <i class="fas fa-layer-group text-secondary me-1"></i> الألوان المضافة حالياً
                        </h6>
                        <span class="badge bg-primary rounded-pill">{{ $bookType->colors->count() }} ألوان</span>
                    </div>

                    @if ($bookType->colors && $bookType->colors->isNotEmpty())
                        <div class="table-responsive mb-4 border rounded">
                            <table class="table table-hover align-middle mb-0 text-center">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 70px;">الصورة</th>
                                        <th style="width: 100px;">اللون</th>
                                        <th>الاسم (عربي)</th>
                                        <th>الاسم (إنجليزي)</th>
                                        <th style="width: 70px;">الترتيب</th>
                                        <th style="width: 60px;">حذف</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($bookType->colors as $color)
                                        <tr>
                                            <td>
                                                <img src="{{ $color->image }}"
                                                     alt="{{ $color->name_ar }}"
                                                     class="color-row-thumb"
                                                     loading="lazy">
                                            </td>
                                            <td>
                                                @if ($color->color_code)
                                                    <span class="rounded-circle d-inline-block border shadow-sm align-middle me-1"
                                                          style="width: 22px; height: 22px; background-color: {{ $color->color_code }};"></span>
                                                    <code class="small text-muted">{{ $color->color_code }}</code>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="fw-bold">{{ $color->name_ar }}</td>
                                            <td class="text-muted">{{ $color->name_en ?? '-' }}</td>
                                            <td>
                                                <span class="badge bg-light text-dark border">{{ $color->order_display }}</span>
                                            </td>
                                            <td>
                                                <form action="{{ route('book-type-colors.destroy', $color) }}" method="POST"
                                                      onsubmit="return confirm('هل أنت متأكد من حذف هذا اللون؟');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="حذف اللون">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info d-flex align-items-center mb-4 py-2">
                            <i class="fas fa-info-circle me-2 ms-2 fa-lg"></i>
                            <div>لا توجد ألوان مضافة لهذا المنتج بعد. يعتبر المنتج معتمداً على لونه وصورته الافتراضية.</div>
                        </div>
                    @endif

                    <hr class="my-4">

                    {{-- فورم إضافة لون جديد --}}
                    <div class="bg-light p-3 rounded-3 border">
                        <h6 class="fw-bold text-success mb-3">
                            <i class="fas fa-plus-circle me-1 ms-1"></i> إضافة لون جديد لهذا الدفتر
                        </h6>
                        <form action="{{ route('book-type-colors.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="book_type_id" value="{{ $bookType->id }}">

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small">اسم اللون بالعربي <span class="text-danger">*</span></label>
                                    <input type="text" name="name_ar" class="form-control" placeholder="مثال: أسود ملكي، أزرق كحلي" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold small">اسم اللون بالإنجليزي</label>
                                    <input type="text" name="name_en" class="form-control" placeholder="مثال: Royal Black">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold small">رمز اللون (Color Code)</label>
                                    <div class="input-group">
                                        <input type="color" class="form-control form-control-color" value="#1a1a1a"
                                               id="modal_picker_{{ $bookType->id }}"
                                               onchange="document.getElementById('modal_code_{{ $bookType->id }}').value = this.value;">
                                        <input type="text" name="color_code" id="modal_code_{{ $bookType->id }}"
                                               class="form-control text-uppercase" value="#1a1a1a"
                                               placeholder="#000000"
                                               onchange="document.getElementById('modal_picker_{{ $bookType->id }}').value = this.value;">
                                    </div>
                                </div>

                                <div class="col-md-5">
                                    <label class="form-label fw-bold small">صورة الدفتر بهذا اللون <span class="text-danger">*</span></label>
                                    <input type="file" name="image" class="form-control" accept="image/*" required>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label fw-bold small">الترتيب</label>
                                    <input type="number" name="order_display" class="form-control"
                                           value="{{ $bookType->colors ? $bookType->colors->count() + 1 : 1 }}" min="0">
                                </div>
                            </div>

                            <div class="text-end mt-3">
                                <button type="submit" class="btn btn-success px-4 shadow-sm">
                                    <i class="fas fa-save me-1 ms-1"></i> حفظ وإضافة اللون
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="modal-footer bg-white border-top">
                    <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">إغلاق</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Sub Media Modal -->
    <div class="modal fade" id="subMediaModal{{ $bookType->id }}" tabindex="-1"
         aria-labelledby="subMediaModalLabel{{ $bookType->id }}" aria-hidden="true"
         style="direction: rtl; text-align: right;">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <h5 class="modal-title d-flex align-items-center" id="subMediaModalLabel{{ $bookType->id }}">
                        <i class="fas fa-images text-info me-2 ms-2"></i>
                        <span>الصور والوسائط الإضافية: <strong>{{ $bookType->name_ar }}</strong></span>
                    </h5>
                    <button type="button" class="btn-close ms-0 me-auto" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    {{-- وسائط حالية --}}
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0 text-dark">
                            <i class="fas fa-photo-video text-secondary me-1"></i> الوسائط المرفوعة
                        </h6>
                        <span class="badge bg-info rounded-pill">{{ $bookType->subMedia->count() }} وسائط</span>
                    </div>

                    @if ($bookType->subMedia && $bookType->subMedia->isNotEmpty())
                        <div class="row g-3 mb-4">
                            @foreach ($bookType->subMedia as $subMedia)
                                <div class="col-md-4 col-sm-6">
                                    <div class="card h-100 border shadow-sm text-center p-2">
                                        @if ($subMedia->type === 'image')
                                            <img class="img-fluid rounded mb-2 mx-auto"
                                                 src="{{ $subMedia->media }}" alt="Sub Media"
                                                 style="height: 140px; object-fit: cover; width: 100%;">
                                        @else
                                            <video class="img-fluid rounded mb-2 mx-auto" style="height: 140px; width: 100%; object-fit: cover;" controls>
                                                <source src="{{ $subMedia->media }}" type="video/mp4">
                                            </video>
                                        @endif
                                        <div class="mt-auto pt-2 border-top">
                                            <form action="{{ route('book-type-sub-media.destroy', $subMedia) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('هل أنت متأكد من حذف هذا الملف؟');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger w-100">
                                                    <i class="fas fa-trash-alt me-1"></i> حذف
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info d-flex align-items-center mb-4 py-2">
                            <i class="fas fa-info-circle me-2 ms-2 fa-lg"></i>
                            <div>لا توجد وسائط إضافية مضافة لهذا المنتج حالياً.</div>
                        </div>
                    @endif

                    <hr class="my-4">

                    <!-- Add Sub Media Form -->
                    <div class="bg-light p-3 rounded-3 border">
                        <h6 class="fw-bold text-primary mb-3">
                            <i class="fas fa-cloud-upload-alt me-1 ms-1"></i> رفع وسيط جديد (صورة / فيديو)
                        </h6>
                        <form action="{{ route('book-type-sub-media.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="book_type_id" value="{{ $bookType->id }}">
                            <div class="row g-3">
                                <div class="col-md-7">
                                    <label for="media_{{ $bookType->id }}" class="form-label fw-bold small">الملف <span class="text-danger">*</span></label>
                                    <input type="file" name="media" id="media_{{ $bookType->id }}" class="form-control" required>
                                </div>
                                <div class="col-md-5">
                                    <label for="type_{{ $bookType->id }}" class="form-label fw-bold small">النوع <span class="text-danger">*</span></label>
                                    <select name="type" id="type_{{ $bookType->id }}" class="form-select" required>
                                        <option value="image">صورة (Image)</option>
                                        <option value="video">فيديو (Video)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="text-end mt-3">
                                <button type="submit" class="btn btn-primary px-4 shadow-sm">
                                    <i class="fas fa-upload me-1 ms-1"></i> رفع الوسيط
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="modal-footer bg-white border-top">
                    <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">إغلاق</button>
                </div>
            </div>
        </div>
    </div>
@endforeach
@endsection