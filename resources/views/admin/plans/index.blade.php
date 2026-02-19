@extends('admin.layout')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center p-3">
                <h1 class="mb-0 text-primary">Plans</h1>
                <a href="{{ route('plans.create') }}" class="btn btn-success">
                    <i class="fas fa-plus me-1"></i> Add New Plan
                </a>
            </div>

            <!-- Table Section -->
            <div class="card-body">
                <table id="responsive-datatable" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th class="text-center">ID</th>
                            <th class="text-center">Title</th>
                            <th class="text-center">Book Price</th>
                            <th class="text-center">Discount Price</th>
                            <th class="text-center">Person Number</th>
                            <th class="text-center">Features</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($plans as $plan)
                        <tr>
                            <td class="text-center">{{ $plan->id }}</td>
                            <td class="text-center">{{ $plan->title }}</td>
                            <td class="text-center">{{ $plan->book_price }} JOD</td>
                            <td class="text-center">{{ $plan->discount_price }} JOD</td>
                            <td class="text-center">{{ $plan->person_number ?? '-' }}</td>
                            <td class="text-center">
                                @if($plan->features && count($plan->features) > 0)
                                    <ul class="list-unstyled mb-0">
                                        @foreach($plan->features as $feature)
                                            <li><i class="mdi mdi-check-circle text-success me-1"></i>{{ $feature }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="dropdown">
                                    <a class="dropdown-toggle" id="dropdownMenuButton{{ $plan->id }}"
                                        data-bs-toggle="dropdown" style="cursor: pointer;" aria-expanded="false"
                                        title="Actions">
                                        <i class="fas fa-ellipsis-h"></i>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end"
                                        aria-labelledby="dropdownMenuButton{{ $plan->id }}">
                                        <li>
                                            <a href="{{ route('plans.edit', $plan) }}"
                                                class="dropdown-item" title="Edit Plan">
                                                <i class="fas fa-edit me-2"></i>Edit
                                            </a>
                                        </li>
                                        <li>
                                            <form action="{{ route('plans.destroy', $plan) }}"
                                                method="POST" id="delete-form-{{ $plan->id }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button"
                                                    class="dropdown-item text-danger sa-warning-btn">
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
                            <td colspan="7" class="text-center text-muted">No plans found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
