@extends('admin.layout')

@section('content')
    <style>
        .normalized-value {
            font-weight: bold;
        }

        /* ستايلات الـ Modal الفخم */
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

        .modal-creative .svg-name-highlight {
            background: #f8f9fc;
            padding: 10px 20px;
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
    </style>

    <div class="row">
        <div class="col-12">

            {{-- Alert Messages بتصميم عصري --}}
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
                <div class="d-flex justify-content-between align-items-center p-3">
                    <h1 class="mb-0 text-primary">SVG Names</h1>

                    {{-- زر إضافة يدوي --}}
                    <a href="{{ route('svg-names.create') }}" class="btn btn-sm btn-success px-3 py-2 rounded-pill">
                        <i class="fas fa-plus me-1"></i> إضافة اسم جديد
                    </a>
                </div>

                <div class="card-body">
                    <table id="responsive-datatable" class="table table-bordered dt-responsive align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center">#</th>
                                <th class="text-center">الاسم</th>
                                <th class="text-center">الاسم المنسّق</th>
                                <th class="text-center">حالة الكود</th>
                                <th class="text-center" width="20%">إجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($svgNames as $svgName)
                                <tr class="{{ empty($svgName->svg_code) ? 'table-danger' : '' }}">
                                    <td class="text-center">
                                        {{ $loop->iteration + ($svgNames->currentPage() - 1) * $svgNames->perPage() }}
                                    </td>

                                    <td class="text-center fw-bold">
                                        {{ $svgName->name }}
                                    </td>

                                    <td class="text-center">
                                        <span class="normalized-value text-muted">{{ $svgName->normalized_name }}</span>
                                    </td>

                                    <td class="text-center">
                                        @if(!empty($svgName->svg_code))
                                            <span class="badge bg-success rounded-pill px-3 py-2">
                                                <i class="fas fa-check-circle me-1"></i> كود موجود
                                            </span>
                                        @else
                                            <span class="badge bg-danger rounded-pill px-3 py-2">
                                                <i class="fas fa-times-circle me-1"></i> كود ناقص
                                            </span>
                                        @endif
                                    </td>

                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="{{ route('svg-names.edit', $svgName) }}"
                                                class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                                <i class="fas fa-edit me-1"></i> تعديل
                                            </a>

                                            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3"
                                                data-bs-toggle="modal" data-bs-target="#deleteNameModal{{ $svgName->id }}">
                                                <i class="fas fa-trash me-1"></i> حذف
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <div class="modal fade modal-creative" id="deleteNameModal{{ $svgName->id }}" tabindex="-1"
                                    aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="icon-box">
                                                    <i class="fas fa-trash-alt"></i>
                                                </div>
                                                <h3 class="modal-title-custom">تأكيد الحذف</h3>
                                                <p class="modal-desc">أنت على وشك حذف هذا الاسم نهائياً من النظام. لا يمكن
                                                    التراجع عن هذا الإجراء.</p>

                                                <div class="svg-name-highlight">
                                                    الاسم: {{ $svgName->name }} <br>
                                                    <span class="text-dark fs-6">{{ $svgName->normalized_name }}</span>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-custom-cancel"
                                                    data-bs-dismiss="modal">إلغاء الأمر</button>
                                                <form action="{{ route('svg-names.destroy', $svgName->id) }}" method="POST"
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
                                    <td colspan="5" class="text-center text-muted py-4">
                                        <i class="fas fa-info-circle fs-4 mb-2 d-block"></i>
                                        لا توجد أسماء حتى الآن.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{-- Pagination --}}
                    <div class="mt-4 d-flex justify-content-center">
                        {{ $svgNames->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection