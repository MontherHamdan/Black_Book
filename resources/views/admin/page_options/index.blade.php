@extends('admin.layout')

@section('content')
    <style>
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
            font-size: 1.05rem;
            margin-bottom: 25px;
        }

        .modal-creative .item-highlight {
            background: #f8f9fc;
            padding: 12px 20px;
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

        .badge-default {
            background: linear-gradient(135deg, #f6d365 0%, #fda085 100%);
            color: #5a2e00;
            font-weight: 800;
            padding: 6px 14px;
            border-radius: 50px;
            box-shadow: 0 2px 8px rgba(246, 211, 101, 0.4);
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .badge-pages {
            background: #eef2ff;
            color: #4338ca;
            font-weight: 800;
            font-size: 1.05rem;
            padding: 6px 14px;
            border-radius: 10px;
            border: 1px solid #c7d2fe;
        }
    </style>

    <div class="row">
        <div class="col-12">

            {{-- Alerts --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert" style="border-radius: 10px;">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert" style="border-radius: 10px;">
                    <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card shadow-sm border-0 mt-3">
                <div class="d-flex justify-content-between align-items-center p-3 border-bottom flex-wrap gap-2">
                    <div>
                        <h2 class="mb-1 text-primary fw-bold">
                            <i class="mdi mdi-book-open-page-variant-outline me-2"></i> إدارة خيارات عدد الأوراق
                        </h2>
                        <p class="text-muted mb-0 small">التحكم في خيارات الأوراق والأسعار الإضافية والشارات التي تظهر للعملاء في الفرونت إند</p>
                    </div>
                    <a href="{{ route('page-options.create') }}" class="btn btn-sm btn-success px-4 py-2 rounded-pill shadow-sm">
                        <i class="fas fa-plus me-1"></i> إضافة خيار أوراق جديد
                    </a>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered dt-responsive align-middle text-center">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">الترتيب</th>
                                    <th>عدد الأوراق</th>
                                    <th>النص المعروض</th>
                                    <th>السعر الإضافي</th>
                                    <th>الشارة (Badge)</th>
                                    <th>الافتراضي</th>
                                    <th>الحالة</th>
                                    <th width="15%">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pageOptions as $option)
                                    <tr>
                                        <td class="fw-bold text-muted">{{ $option->sort_order }}</td>

                                        <td>
                                            <span class="badge-pages">
                                                {{ $option->pages }} ورقة
                                            </span>
                                        </td>

                                        <td class="fw-bold text-dark fs-6">{{ $option->label }}</td>

                                        <td>
                                            @if((float)$option->extra_price > 0)
                                                <span class="badge bg-warning text-dark px-3 py-2 fs-6">
                                                    +{{ number_format($option->extra_price, 2) }} د.أ
                                                </span>
                                            @else
                                                <span class="badge bg-light text-muted border px-3 py-2">
                                                    مجاني (0.00 د.أ)
                                                </span>
                                            @endif
                                        </td>

                                        <td>
                                            @if($option->badge)
                                                <span class="badge bg-danger px-3 py-2 fs-6 shadow-sm">
                                                    {{ $option->badge }}
                                                </span>
                                            @else
                                                <span class="text-muted small">—</span>
                                            @endif
                                        </td>

                                        <td>
                                            @if($option->is_default)
                                                <span class="badge-default">
                                                    <i class="fas fa-star me-1"></i> افتراضي
                                                </span>
                                            @else
                                                <span class="text-muted small">—</span>
                                            @endif
                                        </td>

                                        <td>
                                            <form action="{{ route('page-options.toggleActive', $option->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @if($option->is_active)
                                                    <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-3" title="اضغط للتعطيل">
                                                        <i class="fas fa-check-circle me-1"></i> مفعل
                                                    </button>
                                                @else
                                                    <button type="submit" class="btn btn-sm btn-outline-secondary rounded-pill px-3" title="اضغط للتفعيل">
                                                        <i class="fas fa-times-circle me-1"></i> معطل
                                                    </button>
                                                @endif
                                            </form>
                                        </td>

                                        <td>
                                            <div class="d-flex justify-content-center gap-2">
                                                <a href="{{ route('page-options.edit', $option->id) }}"
                                                    class="btn btn-sm btn-outline-warning rounded-pill px-3">
                                                    <i class="fas fa-edit me-1"></i> تعديل
                                                </a>

                                                <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3"
                                                    data-bs-toggle="modal" data-bs-target="#deleteOptionModal{{ $option->id }}">
                                                    <i class="fas fa-trash me-1"></i> حذف
                                                </button>
                                            </div>
                                        </td>
                                    </tr>

                                    {{-- Delete Modal --}}
                                    <div class="modal fade modal-creative" id="deleteOptionModal{{ $option->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="icon-box">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </div>
                                                    <h4 class="modal-title-custom">تأكيد حذف خيار الأوراق</h4>
                                                    <p class="modal-desc">
                                                        هل أنت متأكد من رغبتك في حذف هذا الخيار؟ لن يتم حذفه من الطلبات السابقة.
                                                    </p>
                                                    <div class="item-highlight">
                                                        <i class="mdi mdi-book-open-page-variant-outline me-1"></i>
                                                        {{ $option->label }} ({{ $option->pages }} ورقة)
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">إلغاء</button>
                                                    <form action="{{ route('page-options.destroy', $option->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger rounded-pill px-4">
                                                            <i class="fas fa-trash me-1"></i> نعم، حذف
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-5 text-muted">
                                            <i class="mdi mdi-book-open-page-variant-outline display-4 d-block mb-2"></i>
                                            لا توجد أي خيارات أوراق حالياً. اضغط على "إضافة خيار أوراق جديد" لإضافة أول خيار.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
