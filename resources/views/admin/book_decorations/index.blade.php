@extends('admin.layout')

@section('content')
    <style>
        /* ستايلات إبداعية للصور والجدول */
        .decoration-wrapper {
            width: 80px;
            height: 80px;
            margin: 0 auto;
            border-radius: 12px;
            padding: 5px;
            background: #f8f9fc;
            border: 1px dashed #d1d3e2;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .decoration-wrapper:hover {
            transform: scale(1.1);
            border-color: #4e73df;
            box-shadow: 0 8px 20px rgba(78, 115, 223, 0.15);
            background: #fff;
        }

        .decoration-image {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            /* عشان الصورة ما تنمط وتحافظ على جودتها */
            border-radius: 8px;
        }

        .table-custom th {
            color: #5a5c69;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e3e6f0 !important;
        }

        .action-btn {
            border-radius: 8px;
            padding: 6px 16px;
            font-weight: 600;
            transition: all 0.2s;
        }

        .action-btn:hover {
            transform: translateY(-2px);
        }
    </style>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0 mt-3">
                <div class="card-header bg-white border-bottom-0 p-4 d-flex justify-content-between align-items-center">
                    <h2 class="mb-0 text-primary fw-bold">
                        <i class="mdi mdi-book-open-page-variant me-2"></i> Book Decorations
                    </h2>
                    <a href="{{ route('book-decorations.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
                        <i class="fas fa-plus me-2"></i> Add New Decoration
                    </a>
                </div>

                <div class="card-body p-0">
                    <div class="p-4 pt-0">
                        <table id="responsive-datatable"
                            class="table table-hover table-custom align-middle dt-responsive nowrap w-100">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" width="5%">ID</th>
                                    <th class="text-center" width="20%">Image</th>
                                    <th class="text-center">Name</th>
                                    <th class="text-center" width="25%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($bookDecorations as $decoration)
                                    <tr>
                                        <td class="text-center fw-bold text-muted">#{{ $decoration->id }}</td>

                                        <td class="text-center">
                                            <div class="decoration-wrapper">
                                                <img src="{{ $decoration->image }}" alt="{{ $decoration->name }}"
                                                    class="decoration-image">
                                            </div>
                                        </td>

                                        <td class="text-center">
                                            <span class="fw-bold text-dark fs-6">{{ $decoration->name ?? '—' }}</span>
                                        </td>

                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <a href="{{ route('book-decorations.edit', $decoration) }}"
                                                    class="btn btn-outline-warning action-btn">
                                                    <i class="fas fa-edit me-1"></i> Edit
                                                </a>

                                                <form action="{{ route('book-decorations.destroy', $decoration) }}"
                                                    method="POST" id="delete-form-{{ $decoration->id }}" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button"
                                                        class="btn btn-outline-danger action-btn sa-warning-btn">
                                                        <i class="fas fa-trash-alt me-1"></i> Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5">
                                            <div class="text-muted">
                                                <i class="fas fa-image fs-1 mb-3 d-block" style="opacity: 0.5;"></i>
                                                <h5 class="fw-bold">No decorations found</h5>
                                                <p>Get started by adding a new book decoration.</p>
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
    </div>
@endsection