@extends('admin.layout')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-primary fw-bold">
                        <i class="fas fa-edit me-1"></i> تعديل الإحصائية: {{ $statistic->value }}
                    </h5>
                    <a href="{{ route('statistics.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> العودة للقائمة
                    </a>
                </div>
            </div>

            <div class="card-body p-4">
                @if ($errors->any())
                    <div class="alert alert-danger mb-4">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('statistics.update', $statistic->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">
                                القيمة أو الرقم (Value) <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   name="value"
                                   class="form-control @error('value') is-invalid @enderror"
                                   placeholder="مثال: 40K + أو 1st أو 3"
                                   value="{{ old('value', $statistic->value) }}"
                                   required>
                            <small class="text-muted">يقبل أرقام ورموز مثل: 40K +, 1st, 100%</small>
                            @error('value')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">الترتيب في الصفحة</label>
                            <input type="number"
                                   name="sort_order"
                                   class="form-control"
                                   value="{{ old('sort_order', $statistic->sort_order) }}"
                                   min="0">
                            <small class="text-muted">الأقل رقماً يظهر أولاً</small>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold">
                                النص التوضيحي (بالعربي) <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   name="title_ar"
                                   class="form-control @error('title_ar') is-invalid @enderror"
                                   placeholder="مثال: قاعدة جماهيرية واسعة عبر إنستغرام"
                                   value="{{ old('title_ar', $statistic->title_ar) }}"
                                   required>
                            @error('title_ar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold">النص التوضيحي (بالإنجليزي) - اختياري</label>
                            <input type="text"
                                   name="title_en"
                                   class="form-control"
                                   placeholder="مثال: Wide audience on Instagram"
                                   value="{{ old('title_en', $statistic->title_en) }}">
                        </div>

                        <div class="col-12 mt-3">
                            <div class="form-check form-switch fs-5">
                                <input class="form-check-input"
                                       type="checkbox"
                                       name="is_active"
                                       id="is_active"
                                       value="1"
                                       {{ old('is_active', $statistic->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label fs-6 fw-bold text-dark" for="is_active">
                                    تفعيل الإحصائية وظهورها في الموقع
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="text-end mt-4 pt-3 border-top">
                        <a href="{{ route('statistics.index') }}" class="btn btn-light px-3 me-2">إلغاء</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fas fa-save me-1"></i> حفظ التعديلات
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
