@extends('admin.layout')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <!-- Display Errors if any -->
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Form for Editing Book Type -->
                    <form action="{{ route('book-types.update', $bookType->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Section Title -->
                        <h4 class="mb-4 text-primary">Edit Book Type</h4>

                        <!-- Book Type Image -->
                        <div class="form-group mb-3">
                            <label for="image" class="form-label fw-bold">Book Type Image</label>
                            <input type="file" data-plugins="dropify" data-height="200"
                                class="form-control @error('image') is-invalid @enderror" name="image" id="image"
                                data-default-file="{{ $bookType->image }}" accept="image/*">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="row mb-3">

                            <!-- name en -->
                            <div class="col-md-6">
                                <label for="name_en" class="form-label fw-bold">English Name</label>
                                <input type="text" class="form-control @error('name_en') is-invalid @enderror"
                                    id="name_en" name="name_en" value="{{ $bookType->name_en }}" required>
                                @error('name_en')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- name ar -->
                            <div class="col-md-6">
                                <label for="name_ar" class="form-label fw-bold">Arabic Name</label>
                                <input type="text" class="form-control @error('name_ar') is-invalid @enderror"
                                    id="name_ar" name="name_ar" value="{{ $bookType->name_ar }}" required>
                                @error('name_ar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>



                        <div class="row mb-3">

                            <!-- Description (EN) -->
                            <div class="col-md-6">
                                <label for="description_en" class="form-label fw-bold">Description (EN)</label>
                                <textarea class="form-control @error('description_en') is-invalid @enderror" id="description_en" name="description_en"
                                    rows="4">{{ $bookType->description_en }}</textarea>
                                @error('description_en')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Description (AR) -->
                            <div class="col-md-6">
                                <label for="description_ar" class="form-label fw-bold">Description (AR)</label>
                                <textarea class="form-control @error('description_ar') is-invalid @enderror" id="description_ar" name="description_ar"
                                    rows="4">{{ $bookType->description_ar }}</textarea>
                                @error('description_ar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <!-- Price -->
                            <div class="col-md-6">
                                <label for="price" class="form-label fw-bold">Price</label>
                                <input type="number" class="form-control @error('price') is-invalid @enderror"
                                    id="price" name="price" value="{{ $bookType->price }}" required>
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary px-4">Update Book Type</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- قسم إدارة ألوان المنتج --}}
        <div class="col-12 mt-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-primary">
                        <i class="fas fa-palette me-2"></i> ألوان المنتج (Product Colors)
                        <span class="badge bg-primary ms-2">{{ $bookType->colors->count() }} ألوان</span>
                    </h5>
                </div>
                <div class="card-body">
                    {{-- الألوان الحالية --}}
                    @if ($bookType->colors && $bookType->colors->isNotEmpty())
                        <div class="row g-3 mb-4">
                            @foreach ($bookType->colors as $color)
                                <div class="col-md-3 col-sm-6">
                                    <div class="card border h-100 shadow-sm text-center p-2 position-relative">
                                        <img src="{{ $color->image }}" alt="{{ $color->name_ar }}"
                                            class="rounded mb-2 mx-auto" width="100%" height="150"
                                            style="object-fit: cover;">
                                        <div class="d-flex align-items-center justify-content-center gap-2 mb-1">
                                            @if ($color->color_code)
                                                <span class="rounded-circle d-inline-block border shadow-sm"
                                                    style="width: 20px; height: 20px; background-color: {{ $color->color_code }};"></span>
                                            @endif
                                            <span class="fw-bold">{{ $color->name_ar }}</span>
                                        </div>
                                        @if ($color->name_en)
                                            <small class="text-muted mb-2">{{ $color->name_en }}</small>
                                        @endif
                                        <div class="mt-auto pt-2 border-top d-flex justify-content-between align-items-center">
                                            <span class="badge bg-light text-muted border">الترتيب: {{ $color->order_display }}</span>
                                            <form action="{{ route('book-type-colors.destroy', $color) }}" method="POST"
                                                onsubmit="return confirm('هل أنت متأكد من حذف هذا اللون؟');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2" title="حذف اللون">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info d-flex align-items-center py-2 mb-4">
                            <i class="fas fa-info-circle me-2 fa-lg"></i>
                            <div>لا توجد ألوان مضافة لهذا المنتج بعد. يمكنك إضافة ألوان أدناه (إذا كان المنتج يدعم خيارات ألوان متعددة).</div>
                        </div>
                    @endif

                    {{-- إضافة لون جديد --}}
                    <div class="p-3 bg-light rounded border">
                        <h6 class="fw-bold text-success mb-3">
                            <i class="fas fa-plus-circle me-1"></i> إضافة لون جديد
                        </h6>
                        <form action="{{ route('book-type-colors.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="book_type_id" value="{{ $bookType->id }}">

                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">اسم اللون بالعربي <span class="text-danger">*</span></label>
                                    <input type="text" name="name_ar" class="form-control" placeholder="مثال: أسود ملكي، أزرق كحلي" required>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold">اسم اللون بالإنجليزي</label>
                                    <input type="text" name="name_en" class="form-control" placeholder="مثال: Royal Black">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold">رمز اللون (Color Code)</label>
                                    <div class="input-group">
                                        <input type="color" class="form-control form-control-color" value="#111111"
                                            id="picker_edit"
                                            onchange="document.getElementById('code_edit').value = this.value;">
                                        <input type="text" name="color_code" id="code_edit"
                                            class="form-control text-uppercase" value="#111111"
                                            placeholder="#000000"
                                            onchange="document.getElementById('picker_edit').value = this.value;">
                                    </div>
                                </div>

                                <div class="col-md-8">
                                    <label class="form-label fw-bold">صورة الدفتر بهذا اللون <span class="text-danger">*</span></label>
                                    <input type="file" name="image" class="form-control" accept="image/*" required>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold">الترتيب</label>
                                    <input type="number" name="order_display" class="form-control" value="{{ $bookType->colors ? $bookType->colors->count() + 1 : 1 }}" min="0">
                                </div>
                            </div>

                            <div class="text-end mt-3">
                                <button type="submit" class="btn btn-success px-4">
                                    <i class="fas fa-save me-1"></i> إضافة وحفظ اللون
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
