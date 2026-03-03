@extends('admin.layout')

@section('content')
    <style>
        .form-card {
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
            border-color: #4e73df;
            box-shadow: 0 0 0 4px rgba(78, 115, 223, 0.1);
        }

        .custom-file-upload {
            border: 2px dashed #d1d3e2;
            border-radius: 12px;
            padding: 30px;
            text-align: center;
            background: #f8f9fc;
            cursor: pointer;
            transition: 0.3s;
        }

        .custom-file-upload:hover {
            background: #eaedf4;
            border-color: #4e73df;
        }

        .file-icon {
            font-size: 40px;
            color: #b7b9cc;
            margin-bottom: 10px;
        }
    </style>

    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card form-card">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0 text-center">
                        <h3 class="mb-0 text-primary fw-bold"><i class="mdi mdi-earth-plus me-2"></i> إضافة دولة جديدة</h3>
                        <p class="text-muted mt-2">قم بإدخال بيانات الدولة وصورة العلم بصيغة PNG</p>
                    </div>

                    <div class="card-body p-4 p-md-5">

                        {{-- عرض الأخطاء إن وجدت --}}
                        @if ($errors->any())
                            <div class="alert alert-danger rounded-3 border-0 shadow-sm">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li><i class="fas fa-exclamation-circle me-2"></i>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('countries.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="row g-4">
                                <div class="col-md-12">
                                    <label for="name" class="form-label fw-bold text-muted small">اسم الدولة <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" class="form-control form-control-custom"
                                        value="{{ old('name') }}" placeholder="مثال: المملكة الأردنية الهاشمية" required>
                                </div>

                                <div class="col-md-6">
                                    <label for="code" class="form-label fw-bold text-muted small">رمز الدولة (Code) <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="code" id="code"
                                        class="form-control form-control-custom text-uppercase" value="{{ old('code') }}"
                                        placeholder="مثال: JO" dir="ltr" required>
                                    <small class="text-muted mt-1 d-block">يرجى إدخال الرمز بحروف إنجليزية (حرفين أو
                                        ثلاثة).</small>
                                </div>

                                <div class="col-md-6">
                                    <label for="dial_code" class="form-label fw-bold text-muted small">مفتاح الاتصال (Dial
                                        Code) <span class="text-danger">*</span></label>
                                    <input type="text" name="dial_code" id="dial_code"
                                        class="form-control form-control-custom" value="{{ old('dial_code') }}"
                                        placeholder="مثال: +962" dir="ltr" required>
                                </div>

                                <div class="col-12 mt-4">
                                    <label class="form-label fw-bold text-muted small">صورة العلم (Flag Image) <span
                                            class="text-danger">*</span></label>
                                    <div class="custom-file-upload position-relative">
                                        <div class="file-icon"><i class="fas fa-cloud-upload-alt"></i></div>
                                        <h6 class="text-primary fw-bold">اضغط هنا لاختيار صورة العلم</h6>
                                        <p class="text-muted small mb-0">يفضل أن تكون الصورة بصيغة PNG بخلفية شفافة</p>
                                        <input type="file" name="flag_image" id="flag_image"
                                            class="form-control position-absolute top-0 start-0 w-100 h-100"
                                            style="opacity: 0; cursor: pointer;"
                                            accept="image/png, image/jpeg, image/svg+xml" required>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex gap-3 mt-5 border-top pt-4">
                                <button type="submit" class="btn btn-primary rounded-pill px-5 flex-grow-1"
                                    style="font-weight: bold; font-size: 1.1rem;">
                                    <i class="fas fa-save me-2"></i> حفظ الدولة
                                </button>
                                <a href="{{ route('countries.index') }}"
                                    class="btn btn-light rounded-pill px-4 text-secondary" style="font-weight: bold;">
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