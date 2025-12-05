@extends('admin.layout')

@section('content')
<style>
    .normalized-value {
        font-weight: bold;
    }
</style>

<div class="row">
    <div class="col-12">

        {{-- Alert Messages --}}
        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card shadow-sm border-0">
            <div class="d-flex justify-content-between align-items-center p-3">
                <h1 class="mb-0 text-primary">SVG Names</h1>

                {{-- زر إضافة يدوي --}}
                <a href="{{ route('svg-names.create') }}" class="btn btn-sm btn-success">
                    + إضافة اسم جديد
                </a>
            </div>

            <div class="card-body">
                <table id="responsive-datatable"
                    class="table table-bordered dt-responsive align-middle">

                    <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">الاسم</th>
                            <th class="text-center">الاسم المنسّق</th>
                            <th class="text-center">حالة الكود</th>
                            <th class="text-center">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($svgNames as $svgName)
                        <tr class="{{ empty($svgName->svg_code) ? 'table-danger' : '' }}">
                            {{-- رقم تسلسلي مع دعم الـ pagination --}}
                            <td class="text-center">
                                {{ $loop->iteration + ($svgNames->currentPage() - 1) * $svgNames->perPage() }}
                            </td>

                            <td class="text-center">
                                {{ $svgName->name }}
                            </td>

                            <td class="text-center">
                                <span class="normalized-value">{{ $svgName->normalized_name }}</span>
                            </td>

                            <td class="text-center">
                                @if(!empty($svgName->svg_code))
                                <span class="badge bg-success">
                                    كود موجود
                                </span>
                                @else
                                <span class="badge bg-danger">
                                    كود SVG ناقص
                                </span>
                                @endif
                            </td>

                            <td class="text-center">
                                <a href="{{ route('svg-names.edit', $svgName) }}"
                                    class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit me-1"></i> تعديل
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">
                                لا توجد أسماء حتى الآن.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- Pagination --}}
                <div class="mt-3">
                    {{ $svgNames->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection