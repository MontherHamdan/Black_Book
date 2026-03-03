@extends('admin.layout')

@section('content')
    <style>
        .glass-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid rgba(0, 0, 0, 0.05);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
            transition: transform 0.3s ease;
        }

        .table-custom th {
            background-color: #f8f9fc;
            color: #495057;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e3e6f0;
        }

        .table-custom td {
            vertical-align: middle;
            color: #5a5c69;
            border-bottom: 1px solid #f1f3f9;
        }

        .table-custom tr:hover {
            background-color: #fcfdfd;
        }

        .badge-soft-primary {
            background-color: rgba(78, 115, 223, 0.1);
            color: #4e73df;
            font-weight: 600;
            padding: 0.5em 1em;
        }

        .btn-gradient-primary {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            color: white;
            border: none;
            box-shadow: 0 4px 15px rgba(78, 115, 223, 0.4);
            transition: all 0.3s;
        }

        .btn-gradient-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(78, 115, 223, 0.6);
            color: white;
        }
    </style>

    <div class="container-fluid mt-5 mb-5">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold text-dark mb-1"><i class="mdi mdi-format-list-bulleted-type text-primary me-2"></i> إدارة
                    أقسام العبارات</h3>
                <p class="text-muted mb-0">تحكم بجميع أقسام الـ SVGs بكل سهولة ومرونة.</p>
            </div>
            <a href="{{ route('svg-categories.create') }}" class="btn btn-gradient-primary rounded-pill px-4 py-2">
                <i class="mdi mdi-plus-circle me-1"></i> إضافة قسم جديد
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3" role="alert">
                <i class="mdi mdi-check-circle-outline me-2 fs-5 align-middle"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card glass-card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-custom mb-0">
                        <thead>
                            <tr>
                                <th width="10%" class="ps-4"># ID</th>
                                <th>اسم القسم</th>
                                <th width="25%">عدد العبارات المرتبطة</th>
                                <th width="15%" class="text-center">إجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories as $category)
                                <tr>
                                    <td class="ps-4 fw-bold text-muted">#{{ $category->id }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light rounded-circle p-2 me-3 text-primary">
                                                <i class="mdi mdi-folder-outline fs-5"></i>
                                            </div>
                                            <span class="fw-bold fs-6 text-dark">{{ $category->name }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-soft-primary rounded-pill fs-6">
                                            <i class="mdi mdi-vector-triangle me-1"></i> {{ $category->svgs_count }} عبارة
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="{{ route('svg-categories.edit', $category->id) }}"
                                                class="btn btn-sm btn-outline-primary rounded-pill px-3 transition-all"
                                                title="تعديل القسم">
                                                <i class="mdi mdi-pencil-outline"></i> تعديل
                                            </a>

                                            <form action="{{ route('svg-categories.destroy', $category->id) }}" method="POST"
                                                class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا القسم؟');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="btn btn-sm btn-outline-danger rounded-pill px-3 transition-all"
                                                    title="حذف القسم">
                                                    <i class="mdi mdi-trash-can-outline"></i> حذف
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="mdi mdi-folder-open-outline" style="font-size: 4rem; opacity: 0.5;"></i>
                                            <h5 class="mt-3">لا توجد أقسام حتى الآن</h5>
                                            <p>ابدأ بإضافة قسم جديد لتنظيم العبارات الخاصة بك.</p>
                                        </div>
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