@extends('admin.layout')

@section('content')
    <style>
        .svg-form-card {
            border-radius: 16px;
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }

        .form-control-custom {
            border-radius: 12px;
            border: 2px solid #eef2f7;
            padding: 12px 18px;
            transition: 0.3s;
        }

        .form-control-custom:focus {
            border-color: #fdcb6e;
            box-shadow: 0 0 0 4px rgba(253, 203, 110, 0.1);
        }

        .svg-preview-box {
            border-radius: 12px;
            border: 2px dashed #eef2f7;
            padding: 20px;
            text-align: center;
            background: #fcfdfd;
        }

        .svg-preview-box svg {
            max-width: 100%;
            height: 120px;
        }
    </style>

    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card svg-form-card">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0 text-center">
                        <h3 class="mb-0 text-warning fw-bold"><i class="fas fa-edit me-2"></i>تعديل العبارة (SVG)</h3>
                        <p class="text-muted mt-1">أنت الآن تقوم بتعديل العبارة رقم: #{{ $svg->id }}</p>
                    </div>

                    <div class="card-body p-4 p-md-5">
                        @if ($errors->any())
                            <div class="alert alert-danger rounded-3 border-0 shadow-sm">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li><i class="fas fa-exclamation-circle me-2"></i>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('svgs.update', $svg->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row g-4">
                                <div class="col-12 mb-2">
                                    <label class="form-label fw-bold text-muted small text-uppercase">المعاينة الحالية
                                        (Preview)</label>
                                    <div class="svg-preview-box d-flex justify-content-center align-items-center">
                                        {!! $svg->svg_code !!}
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="title" class="form-label fw-bold text-muted small text-uppercase">Title
                                        (Optional)</label>
                                    <input type="text" name="title" id="title" class="form-control form-control-custom"
                                        value="{{ old('title', $svg->title) }}">
                                </div>

                                <div class="col-md-6">
                                    <label for="category_id"
                                        class="form-label fw-bold text-muted small text-uppercase">Category <span
                                            class="text-danger">*</span></label>
                                    <select name="category_id" id="category_id" class="form-select form-control-custom"
                                        required>
                                        <option value="" disabled>-- اختر القسم --</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ (old('category_id', $svg->category_id) == $category->id) ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-12">
                                    <label for="svg_code" class="form-label fw-bold text-muted small text-uppercase">SVG
                                        Code (XML) <span class="text-danger">*</span></label>
                                    <textarea name="svg_code" id="svg_code"
                                        class="form-control form-control-custom text-start" rows="8" dir="ltr"
                                        required>{{ old('svg_code', $svg->svg_code) }}</textarea>
                                </div>
                            </div>

                            <div class="d-flex gap-3 mt-5">
                                <button type="submit" class="btn btn-warning rounded-pill px-5 flex-grow-1"
                                    style="border: none; color: #fff; font-weight: bold;">
                                    <i class="fas fa-check-circle me-2"></i> حفظ التعديلات
                                </button>
                                <a href="{{ route('svgs.index') }}" class="btn btn-light rounded-pill px-4 text-secondary">
                                    إلغاء وعودة
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection