@extends('admin.layout')

@section('content')
    <style>
        .edit-card {
            background: #ffffff;
            border-radius: 20px;
            border: none;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.04);
            overflow: hidden;
        }

        .edit-header {
            background: linear-gradient(135deg, #f8f9fc 0%, #f1f3f9 100%);
            border-bottom: 1px solid #e3e6f0;
            padding: 25px;
        }

        .form-control-custom {
            border-radius: 12px;
            padding: 14px 20px;
            border: 2px solid #eaedf4;
            background-color: #fcfdfd;
            font-size: 1rem;
            transition: all 0.2s;
        }

        .form-control-custom:focus {
            border-color: #f6c23e;
            background-color: #fff;
            box-shadow: 0 0 0 4px rgba(246, 194, 62, 0.1);
        }

        .btn-update {
            background: #f6c23e;
            color: #fff;
            border-radius: 12px;
            padding: 12px 25px;
            font-weight: 600;
            border: none;
            transition: all 0.3s;
        }

        .btn-update:hover {
            background: #dfa822;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(246, 194, 62, 0.3);
            color: #fff;
        }

        .btn-back {
            background: #f8f9fc;
            color: #5a5c69;
            border-radius: 12px;
            padding: 12px 25px;
            font-weight: 600;
            border: 1px solid #eaedf4;
            transition: all 0.3s;
        }

        .btn-back:hover {
            background: #eaedf4;
            color: #3a3b45;
        }
    </style>

    <div class="container-fluid mt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-7 col-md-9">

                @if($errors->any())
                    <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4" role="alert">
                        <div class="d-flex align-items-center mb-2">
                            <i class="mdi mdi-alert-circle-outline fs-4 me-2"></i>
                            <strong class="fs-5">يرجى تصحيح الأخطاء التالية:</strong>
                        </div>
                        <ul class="mb-0 ps-4">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card edit-card">
                    <div class="edit-header text-center">
                        <div class="bg-white rounded-circle d-inline-flex justify-content-center align-items-center shadow-sm mb-3"
                            style="width: 70px; height: 70px;">
                            <i class="mdi mdi-pencil-outline text-warning" style="font-size: 2.2rem;"></i>
                        </div>
                        <h4 class="fw-bold text-dark mb-1">تعديل القسم</h4>
                        <p class="text-muted mb-0">تعديل بيانات القسم الحالي: <span
                                class="fw-bold text-primary">{{ $svgCategory->name }}</span></p>
                    </div>

                    <div class="card-body p-4 p-md-5">
                        <form action="{{ route('svg-categories.update', $svgCategory->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-4">
                                <label for="name" class="form-label fw-bold text-dark mb-2">اسم القسم <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" class="form-control form-control-custom"
                                    value="{{ old('name', $svgCategory->name) }}" required autofocus>
                                <div class="form-text text-muted mt-2"><i class="mdi mdi-information-outline"></i> تأكد من
                                    كتابة اسم واضح ويدل على العبارات التي بداخله.</div>
                            </div>

                            <div class="d-flex gap-3 mt-5">
                                <button type="submit" class="btn btn-update flex-grow-1">
                                    <i class="mdi mdi-check-all me-1"></i> حفظ التعديلات
                                </button>
                                <a href="{{ route('svg-categories.index') }}"
                                    class="btn btn-back flex-grow-1 text-center text-decoration-none">
                                    <i class="mdi mdi-close me-1"></i> إلغاء وعودة
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection