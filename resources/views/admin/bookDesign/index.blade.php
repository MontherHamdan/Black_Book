@extends('admin.layout')

@push('styles')
    <style>
        .drag-handle {
            cursor: grab;
            user-select: none;
            width: 45px;
        }
        .drag-handle:active {
            cursor: grabbing;
        }
        tr.sortable-chosen {
            background-color: #f0f7ff !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        tr.sortable-ghost {
            opacity: 0.35;
            background-color: #cfe2ff !important;
        }
        .order-badge {
            transition: all 0.2s ease;
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <!-- Header Section -->
                <div class="d-flex justify-content-between align-items-center p-3">
                    <div>
                        <h1 class="mb-1 text-primary">Book Designs</h1>
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i> يمكنك سحب الصفوف من أيقونة <i class="fas fa-grip-vertical"></i> لتغيير ترتيب ظهور التصاميم من الأعلى للأسفل
                        </small>
                    </div>
                    <a href="{{ route('book-designs.create') }}" class="btn btn-success">
                        <i class="fas fa-plus me-1"></i> Add New Design
                    </a>
                </div>

                <!-- Table Section -->
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="designs-sortable-table" class="table table-hover table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" style="width: 45px;">
                                        <i class="fas fa-arrows-alt text-muted" title="سحب لتغيير الترتيب"></i>
                                    </th>
                                    <th class="text-center" style="width: 70px;">ID</th>
                                    <th class="text-center" style="width: 90px;">Order</th>
                                    <th class="text-center">Name</th>
                                    <th class="text-center">Image</th>
                                    <th class="text-center">Category</th>
                                    <th class="text-center">Subcategory</th>
                                    <th class="text-center" style="width: 100px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="sortable-tbody">
                                @forelse ($bookDesigns as $index => $design)
                                    <tr data-id="{{ $design->id }}">
                                        <td class="text-center drag-handle" title="اسحب لإعادة الترتيب">
                                            <i class="fas fa-grip-vertical text-muted fs-5"></i>
                                        </td>
                                        <td class="text-center">
                                            {{ $design->id }}
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-primary fs-6 order-badge">{{ $design->order_display ?? ($index + 1) }}</span>
                                        </td>
                                        <td class="text-center fw-bold text-dark">
                                            {{ $design->design_name ?? 'بدون اسم' }}
                                        </td>
                                        <td class="text-center">
                                            <img src="{{ $design->image }}" alt="Design Image" width="80" height="80"
                                                class="rounded shadow-sm"
                                                style="object-fit: cover; object-position: center;">
                                        </td>
                                        <td class="text-center">{{ $design->category->arabic_name ?? 'N/A' }}</td>
                                        <td class="text-center">{{ $design->subCategory->arabic_name ?? 'N/A' }}</td>
                                        <td class="text-center">
                                            <!-- Actions Dropdown -->
                                            <div class="dropdown">
                                                <a class="dropdown-toggle" id="dropdownMenuButton{{ $design->id ?? 'N/A' }}"
                                                    data-bs-toggle="dropdown" style="cursor: pointer;" aria-expanded="false"
                                                    title="Actions">
                                                    <i class="fas fa-ellipsis-h"></i>
                                                </a>
                                                <ul class="dropdown-menu dropdown-menu-end"
                                                    aria-labelledby="dropdownMenuButton{{ $design->id ?? 'N/A' }}">
                                                    <!-- Edit Action -->
                                                    <li>
                                                        <a href="{{ route('book-designs.edit', $design) }}" class="dropdown-item"
                                                            title="Edit Design">
                                                            <i class="fas fa-edit me-2"></i>Edit
                                                        </a>
                                                    </li>
                                                    <!-- Delete Action -->
                                                    <li>
                                                        <form action="{{ route('book-designs.destroy', $design) }}" method="POST"
                                                            id="delete-form-{{ $design->id }}">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" class="dropdown-item text-danger sa-warning-btn">
                                                                <i class="fas fa-trash me-2"></i>Delete
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">No book designs found.</td>
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

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tbody = document.getElementById('sortable-tbody');
            if (!tbody) return;

            new Sortable(tbody, {
                handle: '.drag-handle',
                animation: 200,
                chosenClass: 'sortable-chosen',
                ghostClass: 'sortable-ghost',
                onEnd: function () {
                    // Update visual order badges immediately
                    const rows = tbody.querySelectorAll('tr[data-id]');
                    const orderIds = [];

                    rows.forEach((row, index) => {
                        const newOrder = index + 1;
                        orderIds.push(row.getAttribute('data-id'));
                        const badge = row.querySelector('.order-badge');
                        if (badge) {
                            badge.textContent = newOrder;
                        }
                    });

                    // Send AJAX request to persist order
                    fetch("{{ route('book-designs.update-order') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ order: orderIds })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            if (typeof toastr !== 'undefined') {
                                toastr.success(data.message || 'تم تحديث الترتيب بنجاح', 'نجاح', { timeOut: 2000 });
                            }
                        } else {
                            if (typeof toastr !== 'undefined') {
                                toastr.error('حدث خطأ أثناء حفظ الترتيب', 'خطأ');
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error updating order:', error);
                        if (typeof toastr !== 'undefined') {
                            toastr.error('فشل الاتصال بالسيرفر لحفظ الترتيب', 'خطأ');
                        }
                    });
                }
            });
        });
    </script>
@endpush