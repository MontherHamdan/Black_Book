@extends('admin.layout')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <!-- Header Section -->
                <div class="d-flex justify-content-between align-items-center p-3">
                    <h1 class="mb-0 text-primary">Discount Codes</h1>
                    <a href="{{ route('discount-codes.create') }}" class="btn btn-success">
                        <i class="fas fa-plus me-1"></i> Add New Discount Code
                    </a>
                </div>

                <!-- Table Section -->
                <div class="card-body">
                    <!-- DataTable -->
                    <table id="responsive-datatable" class="table table-striped table-bordered">



                        <thead>
                            <tr>
                                <th class="text-center">ID</th>
                                <th class="text-center">Code Name</th>
                                <th class="text-center">Discount Code</th>
                                <th class="text-center">Type (النوع)</th>
                                <th class="text-center">Discount Value</th>
                                <th class="text-center">Discount Type</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($discountCodes as $code)
                                <tr>
                                    <td class="text-center fw-bold text-secondary">{{ $code->id }}</td>
                                    <td class="text-center fw-bold text-dark">{{ $code->code_name ?? '-' }}</td>
                                    <td class="text-center"><span
                                            class="badge bg-light text-dark border p-2 fs-6">{{ $code->discount_code }}</span>
                                    </td>

                                    <td class="text-center">
                                        @if($code->is_group)
                                            <span
                                                class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-2">
                                                <i class="fas fa-users me-1"></i> مجموعة
                                            </span>
                                            <div class="small text-muted mt-1 fw-bold">{{ $code->plan->title ?? 'No Plan' }}</div>
                                        @else
                                            <span
                                                class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-3 py-2">
                                                <i class="fas fa-user me-1"></i> فردي
                                            </span>
                                        @endif
                                    </td>

                                    <td class="text-center fw-bold">
                                        @if($code->is_group)
                                            <span class="text-success">{{ $code->plan->discount_price ?? 0 }} JOD</span>
                                        @else
                                            {{ $code->discount_value }} {{ $code->discount_type === 'percentage' ? '%' : 'JOD' }}
                                        @endif
                                    </td>
                                    <td class="text-center text-muted">
                                        @if($code->is_group)
                                            By Plan
                                        @else
                                            {{ ucfirst($code->discount_type) }}
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <!-- Actions Dropdown -->
                                        <div class="dropdown">
                                            <a class="dropdown-toggle" id="dropdownMenuButton{{ $code->id }}"
                                                data-bs-toggle="dropdown" style="cursor: pointer;" aria-expanded="false"
                                                title="Actions">
                                                <i class="fas fa-ellipsis-h"></i>
                                            </a>
                                            <ul class="dropdown-menu dropdown-menu-end"
                                                aria-labelledby="dropdownMenuButton{{ $code->id }}">
                                                <!-- Edit Action -->
                                                <li>
                                                    <a href="{{ route('discount-codes.edit', $code) }}" class="dropdown-item"
                                                        title="Edit Discount Code">
                                                        <i class="fas fa-edit me-2"></i>Edit
                                                    </a>
                                                </li>
                                                <!-- Delete Action -->
                                                <li>
                                                    <form action="{{ route('discount-codes.destroy', $code) }}" method="POST"
                                                        id="delete-form-{{ $code->id }}">
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
                                    <td colspan="5" class="text-center text-muted">No discount codes found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>


                </div>
            </div>
        </div>
    </div>
@endsection