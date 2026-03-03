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
            border-color: #6c5ce7;
            box-shadow: 0 0 0 4px rgba(108, 92, 231, 0.1);
        }
    </style>

    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card svg-form-card">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0 text-center">
                        <h3 class="mb-0 text-primary fw-bold"><i class="fas fa-vector-square me-2"></i>إضافة عبارة جديدة
                            (SVG)</h3>
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

                        <form action="{{ route('svgs.store') }}" method="POST">
                            @csrf

                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label for="title" class="form-label fw-bold text-muted small text-uppercase">Title
                                        (Optional)</label>
                                    <input type="text" name="title" id="title" class="form-control form-control-custom"
                                        value="{{ old('title') }}" placeholder="Ex: Graduation Quote 1">
                                </div>

                                <div class="col-md-6">
                                    <label for="category_id"
                                        class="form-label fw-bold text-muted small text-uppercase">Category <span
                                            class="text-danger">*</span></label>
                                    <select name="category_id" id="category_id" class="form-select form-control-custom"
                                        required>
                                        <option value="" disabled selected>-- اختر القسم --</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
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
                                        placeholder="<svg>...</svg>" required>{{ old('svg_code') }}</textarea>
                                </div>
                            </div>

                            <div class="d-flex gap-3 mt-5">
                                <button type="submit" class="btn btn-primary rounded-pill px-5 flex-grow-1"
                                    style="background: linear-gradient(135deg, #6c5ce7, #a29bfe); border: none;">
                                    <i class="fas fa-cloud-upload-alt me-2"></i> حفظ الـ SVG
                                </button>
                                <a href="{{ route('svgs.index') }}" class="btn btn-light rounded-pill px-4 text-secondary">
                                    إلغاء
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection