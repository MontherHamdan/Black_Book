@extends('admin.layout')

@section('content')
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-warning text-dark border-bottom-0 pt-4 pb-3 text-center">
                        <h3 class="mb-0 fw-bold"><i class="fas fa-edit me-2"></i> تعديل بيانات الدولة</h3>
                    </div>

                    <div class="card-body p-4 p-md-5">
                        @if ($errors->any())
                            <div class="alert alert-danger rounded-3 border-0 shadow-sm">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('countries.update', $country->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="row g-4">
                                <div class="col-md-12 text-center mb-3">
                                    @if($country->flag_image)
                                        <label class="form-label text-muted d-block">العلم الحالي:</label>
                                        <img src="{{ asset($country->flag_image) }}" alt="Flag" class="img-thumbnail"
                                            width="120">
                                    @endif
                                </div>

                                <div class="col-md-12">
                                    <label for="name" class="form-label fw-bold">اسم الدولة <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" class="form-control"
                                        value="{{ old('name', $country->name) }}" required>
                                </div>

                                <div class="col-md-6">
                                    <label for="code" class="form-label fw-bold">رمز الدولة (Code) <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="code" id="code" class="form-control text-uppercase"
                                        value="{{ old('code', $country->code) }}" dir="ltr" required>
                                </div>

                                <div class="col-md-6">
                                    <label for="dial_code" class="form-label fw-bold">مفتاح الاتصال <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="dial_code" id="dial_code" class="form-control"
                                        value="{{ old('dial_code', $country->dial_code) }}" dir="ltr" required>
                                </div>

                                <div class="col-12 mt-4">
                                    <label class="form-label fw-bold">تغيير صورة العلم (اختياري)</label>
                                    <input type="file" name="flag_image" class="form-control"
                                        accept="image/png, image/jpeg, image/svg+xml">
                                    <small class="text-muted mt-1 d-block">اترك هذا الحقل فارغاً إذا كنت لا تريد تغيير العلم
                                        الحالي.</small>
                                </div>
                            </div>

                            <div class="d-flex gap-3 mt-5 border-top pt-4">
                                <button type="submit" class="btn btn-warning rounded-pill px-5 flex-grow-1 fw-bold">
                                    حفظ التعديلات
                                </button>
                                <a href="{{ route('countries.index') }}"
                                    class="btn btn-light rounded-pill px-4 text-secondary fw-bold">
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