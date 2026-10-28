@extends('admin.layout')

@push('styles')
    <style>
        .stat-badge {
            font-size: 1.15rem;
            font-weight: 800;
            padding: 6px 14px;
            border-radius: 10px;
            background: linear-gradient(135deg, #0b5ed7 0%, #0d6efd 100%);
            color: #fff;
            display: inline-block;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 10px rgba(13, 110, 253, 0.2);
        }
        .stats-table th {
            font-size: 0.88rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
    </style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center p-3 border-bottom bg-white rounded-top">
                <div>
                    <h4 class="mb-1 text-primary fw-bold">
                        <i class="mdi mdi-chart-box-outline me-2"></i> Statistics (أرقام وإحصائيات قصتنا)
                    </h4>
                    <span class="text-muted small">إدارة الأرقام والإحصائيات المعروضة في قسم «أين نحن اليوم؟» بصفحة قصتنا</span>
                </div>
                <a href="{{ route('statistics.create') }}" class="btn btn-success shadow-sm rounded-pill px-3">
                    <i class="fas fa-plus-circle me-1"></i> إضافة إحصائية جديدة
                </a>
            </div>

            <!-- Table Section -->
            <div class="card-body">
                <div class="table-responsive">
                    <table id="responsive-datatable" class="table table-hover table-bordered stats-table align-middle text-center">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 60px;">ID</th>
                                <th style="width: 140px;">القيمة (Value)</th>
                                <th>النص بالعربي</th>
                                <th>النص بالإنجليزي</th>
                                <th style="width: 90px;">الترتيب</th>
                                <th style="width: 100px;">الحالة</th>
                                <th style="width: 120px;">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($statistics as $stat)
                            <tr>
                                <td class="text-muted fw-bold">{{ $stat->id }}</td>
                                <td>
                                    <span class="stat-badge">
                                        {{ $stat->value }}
                                    </span>
                                </td>
                                <td class="fw-bold text-dark text-end pe-3">{{ $stat->title_ar }}</td>
                                <td class="text-muted text-start ps-3">{{ $stat->title_en ?: '-' }}</td>
                                <td>
                                    <span class="badge bg-light text-dark border px-2 py-1 fs-6">
                                        {{ $stat->sort_order }}
                                    </span>
                                </td>
                                <td>
                                    @if ($stat->is_active)
                                        <span class="badge bg-soft-success text-success px-2 py-1">
                                            <i class="fas fa-check-circle me-1"></i> مفعل
                                        </span>
                                    @else
                                        <span class="badge bg-soft-danger text-danger px-2 py-1">
                                            <i class="fas fa-times-circle me-1"></i> معطل
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="{{ route('statistics.edit', $stat) }}"
                                           class="btn btn-sm btn-outline-primary"
                                           title="تعديل">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('statistics.destroy', $stat) }}"
                                              method="POST"
                                              onsubmit="return confirm('هل أنت متأكد من حذف هذه الإحصائية؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="حذف">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="fas fa-chart-bar fa-2x mb-2 d-block text-secondary"></i>
                                    لا توجد إحصائيات مضافة حتى الآن.
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
