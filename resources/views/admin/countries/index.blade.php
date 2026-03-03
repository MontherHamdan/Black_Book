@extends('admin.layout')

@section('content')
    <style>
        /* ستايلات الـ Modal الفخم للحذف */
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

        .modal-creative .country-highlight {
            background: #f8f9fc;
            padding: 15px 20px;
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

        .flag-img {
            width: 50px;
            height: auto;
            border-radius: 6px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            border: 1px solid #eee;
        }
    </style>

    <div class="row">
        <div class="col-12">

            {{-- Alerts --}}
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
                <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                    <h1 class="mb-0 text-primary"><i class="mdi mdi-earth me-2"></i> إدارة الدول</h1>
                    <a href="{{ route('countries.create') }}"
                        class="btn btn-sm btn-success px-4 py-2 rounded-pill shadow-sm">
                        <i class="fas fa-plus me-1"></i> إضافة دولة جديدة
                    </a>
                </div>

                <div class="card-body">
                    <table id="responsive-datatable"
                        class="table table-hover table-bordered dt-responsive align-middle text-center">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">#</th>
                                <th>العلم</th>
                                <th>اسم الدولة</th>
                                <th>رمز الدولة (Code)</th>
                                <th>مفتاح الاتصال (Dial)</th>
                                <th width="15%">إجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($countries as $country)
                                <tr>
                                    <td class="text-center fw-bold">{{ $loop->iteration }}</td>

                                    <td class="text-center">
                                        @if($country->flag_image)
                                            <img src="{{ asset($country->flag_image) }}" alt="{{ $country->name }} flag"
                                                class="flag-img">
                                        @else
                                            <span class="badge bg-secondary">لا يوجد علم</span>
                                        @endif
                                    </td>

                                    <td class="text-center fw-bold text-dark">{{ $country->name }}</td>

                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border px-3 py-2">{{ $country->code }}</span>
                                    </td>

                                    <td class="text-center" dir="ltr">
                                        <span class="badge bg-primary px-3 py-2 fs-6">{{ $country->dial_code }}</span>
                                    </td>

                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="{{ route('countries.edit', $country->id) }}"
                                                class="btn btn-sm btn-outline-warning rounded-pill px-3">
                                                <i class="fas fa-edit me-1"></i> تعديل
                                            </a>

                                            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3"
                                                data-bs-toggle="modal" data-bs-target="#deleteCountryModal{{ $country->id }}">
                                                <i class="fas fa-trash me-1"></i> حذف
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <div class="modal fade modal-creative" id="deleteCountryModal{{ $country->id }}" tabindex="-1"
                                    aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="icon-box"><i class="fas fa-trash-alt"></i></div>
                                                <h3 class="modal-title-custom">تأكيد الحذف</h3>
                                                <p class="modal-desc">أنت على وشك حذف هذه الدولة نهائياً من النظام. سيتم حذف
                                                    صورة العلم المرتبطة بها أيضاً.</p>

                                                <div
                                                    class="country-highlight d-flex align-items-center justify-content-center gap-3">
                                                    @if($country->flag_image)
                                                        <img src="{{ asset($country->flag_image) }}" alt="flag" width="40"
                                                            class="rounded border">
                                                    @endif
                                                    <div>
                                                        <span class="text-dark fs-5">{{ $country->name }}</span> <br>
                                                        <small class="text-muted" dir="ltr">{{ $country->dial_code }} |
                                                            {{ $country->code }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-custom-cancel"
                                                    data-bs-dismiss="modal">إلغاء الأمر</button>
                                                <form action="{{ route('countries.destroy', $country->id) }}" method="POST"
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
                                    <td colspan="6" class="text-center text-muted py-5">
                                        <i class="fas fa-globe fs-1 text-light mb-3 d-block"></i>
                                        <h5>لا توجد دول مضافة حتى الآن</h5>
                                        <a href="{{ route('countries.create') }}" class="btn btn-sm btn-primary mt-2">إضافة دولة
                                            جديدة</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection