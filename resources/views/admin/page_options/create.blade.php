@extends('admin.layout')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        
        <div class="card shadow-sm border-0 mt-3">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h4 class="mb-0 text-primary fw-bold">
                    <i class="mdi mdi-plus-circle-outline me-2"></i> إضافة خيار أوراق جديد
                </h4>
                <a href="{{ route('page-options.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                    <i class="fas fa-arrow-right me-1"></i> العودة للقائمة
                </a>
            </div>

            <div class="card-body p-4">
                @if(isset($errors) && $errors->any())
                    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert" style="border-radius: 10px;">
                        <i class="fas fa-exclamation-triangle me-2"></i><strong>تنبيه!</strong> يرجى تصحيح الأخطاء التالية:
                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form action="{{ route('page-options.store') }}" method="POST">
                    @csrf

                    <div class="row g-3">
                        {{-- عدد الأوراق --}}
                        <div class="col-md-6">
                            <label for="pages" class="form-label fw-bold">
                                عدد الأوراق (Pages Count) <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="pages" id="pages" class="form-control form-control-lg @error('pages') is-invalid @enderror"
                                value="{{ old('pages') }}" placeholder="مثال: 150" min="1" required>
                            <div class="form-text">رقم صحيح فقط، يتم إرساله إلى قاعدة البيانات والنظام الداخلي.</div>
                            @error('pages')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- النص المعروض --}}
                        <div class="col-md-6">
                            <label for="label" class="form-label fw-bold">
                                النص المعروض للعميل (Label) <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="label" id="label" class="form-control form-control-lg @error('label') is-invalid @enderror"
                                value="{{ old('label') }}" placeholder="مثال: 150 ورقة" required>
                            <div class="form-text">النص الذي يظهر في قائمة اختيار الفرونت إند.</div>
                            @error('label')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- السعر الإضافي --}}
                        <div class="col-md-6">
                            <label for="extra_price" class="form-label fw-bold">
                                السعر الإضافي (Extra Price)
                            </label>
                            <div class="input-group input-group-lg">
                                <input type="number" step="0.01" min="0" name="extra_price" id="extra_price"
                                    class="form-control @error('extra_price') is-invalid @enderror"
                                    value="{{ old('extra_price', '0.00') }}" placeholder="0.00">
                                <span class="input-group-text">د.أ</span>
                            </div>
                            <div class="form-text">اتركه 0.00 إذا كان خياراً مجانياً مشمولاً بالسعر الأساسي.</div>
                            @error('extra_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- الشارة / البادج --}}
                        <div class="col-md-6">
                            <label for="badge" class="form-label fw-bold">
                                شارة مميزة (Badge) <span class="text-muted small">(اختياري)</span>
                            </label>
                            <input type="text" name="badge" id="badge" class="form-control form-control-lg @error('badge') is-invalid @enderror"
                                value="{{ old('badge') }}" placeholder="مثال: الأكثر طلباً 🔥">
                            <div class="form-text">نص أو إيموجي يظهر بجانب الخيار لجذب انتباه العميل.</div>
                            @error('badge')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- الترتيب --}}
                        <div class="col-md-6">
                            <label for="sort_order" class="form-label fw-bold">
                                ترتيب الظهور (Sort Order)
                            </label>
                            <input type="number" name="sort_order" id="sort_order" class="form-control form-control-lg @error('sort_order') is-invalid @enderror"
                                value="{{ old('sort_order', 0) }}" min="0">
                            <div class="form-text">الأرقام الأصغر تظهر أولاً في القائمة.</div>
                            @error('sort_order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- الخيارات --}}
                        <div class="col-md-6 d-flex flex-column justify-content-center">
                            <div class="card bg-light border-0 p-3 mt-2">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" role="switch" name="is_default" id="is_default" value="1"
                                        {{ old('is_default') ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="is_default">
                                        تعيين كخيار افتراضي (Default Selection)
                                    </label>
                                    <div class="text-muted small">سيتم اختياره تلقائياً عند فتح صفحة المنتج.</div>
                                </div>

                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" name="is_active" id="is_active" value="1"
                                        {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="is_active">
                                        مفعل ويظهر للعملاء (Active)
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                            <a href="{{ route('page-options.index') }}" class="btn btn-light rounded-pill px-4">إلغاء</a>
                            <button type="submit" class="btn btn-success rounded-pill px-5">
                                <i class="fas fa-save me-1"></i> حفظ خيار الأوراق
                            </button>
                        </div>

                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
