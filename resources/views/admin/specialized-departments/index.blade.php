@extends('admin.layout')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center p-3">
                <h1 class="mb-0 text-primary">Specialized Departments</h1>
                <a href="{{ route('specialized-departments.create') }}" class="btn btn-success">
                    <i class="fas fa-plus me-1"></i> Add New Department
                </a>
            </div>

            <!-- Table Section -->
            <div class="card-body">
                <table id="responsive-datatable" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th class="text-center">ID</th>
                            <th class="text-center">Title</th>
                            <th class="text-center">Phone Number</th>
                            <th class="text-center">WhatsApp</th>
                            <th class="text-center">Color</th>
                            <th class="text-center">Icon</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($departments as $department)
                        <tr>
                            <td class="text-center">{{ $department->id }}</td>
                            <td class="text-center">{{ $department->title }}</td>
                            <td class="text-center">{{ $department->phone_number ?? '-' }}</td>
                            <td class="text-center">
                                @if($department->whatsapp_link)
                                    <a href="{{ $department->whatsapp_link }}" target="_blank" class="text-success">
                                        <i class="mdi mdi-whatsapp"></i> Link
                                    </a>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-center">
                                @if($department->color_code)
                                    <span class="badge" style="background-color: {{ $department->color_code }}; width: 30px; height: 20px; display: inline-block;">&nbsp;</span>
                                    <br><small>{{ $department->color_code }}</small>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-center">
                                @if($department->icon_svg)
                                    <div style="max-width: 40px; max-height: 40px; display: inline-block;">{!! $department->icon_svg !!}</div>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="dropdown">
                                    <a class="dropdown-toggle" id="dropdownMenuButton{{ $department->id }}"
                                        data-bs-toggle="dropdown" style="cursor: pointer;" aria-expanded="false"
                                        title="Actions">
                                        <i class="fas fa-ellipsis-h"></i>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end"
                                        aria-labelledby="dropdownMenuButton{{ $department->id }}">
                                        <li>
                                            <a href="{{ route('specialized-departments.edit', $department) }}"
                                                class="dropdown-item" title="Edit Department">
                                                <i class="fas fa-edit me-2"></i>Edit
                                            </a>
                                        </li>
                                        <li>
                                            <form action="{{ route('specialized-departments.destroy', $department) }}"
                                                method="POST" id="delete-form-{{ $department->id }}">
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
                            <td colspan="7" class="text-center text-muted">No specialized departments found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
