@extends('admin.layout')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <!-- Header Section -->
                <div class="d-flex justify-content-between align-items-center p-3">
                    <h1 class="mb-0 text-primary">Manage Users</h1>
                    <a href="{{ route('users.create') }}" class="btn btn-success">
                        <i class="fas fa-plus me-1"></i> Add New User
                    </a>
                </div>

                <!-- Table Section -->
                <div class="card-body">
                    <!-- DataTable -->
                    <table id="responsive-datatable" class="table table-striped table-bordered dt-responsive ">
                        <thead>
                            <tr>
                                <th class="text-center">ID</th>
                                <th class="text-center">Name</th>
                                <th class="text-center">Email</th>
                                <th class="text-center">Admin?</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                                <tr>
                                    <td class="text-center">{{ $user->id }}</td>
                                    <td class="text-center">{{ $user->name }}</td>
                                    <td class="text-center">{{ $user->email }}</td>
                                    <td class="text-center">{{ $user->is_admin ? 'Yes' : 'No' }}</td>
                                    <td class="text-center">
                                        <!-- Actions Dropdown -->
                                        <div class="dropdown">
                                            <a class="dropdown-toggle" id="dropdownMenuButton{{ $user->id }}"
                                                data-bs-toggle="dropdown" style="cursor: pointer;" aria-expanded="false"
                                                title="Actions">
                                                <i class="fas fa-ellipsis-h"></i>
                                            </a>
                                            <ul class="dropdown-menu dropdown-menu-end"
                                                aria-labelledby="dropdownMenuButton{{ $user->id }}">
                                                <!-- Edit Action -->
                                                <li>
                                                    <a href="{{ route('users.edit', $user) }}"
                                                        class="dropdown-item" title="Edit Book Type">
                                                        <i class="fas fa-edit me-2"></i>Edit
                                                    </a>
                                                </li>
                                                <!-- Sub Media Action -->
                                                <li>
                                                    <button type="button" class="dropdown-item" data-bs-toggle="modal"
                                                        data-bs-target="#subMediaModal{{ $user->id }}">
                                                        <i class="fas fa-images me-2"></i>Manage Sub Media
                                                    </button>
                                                </li>
                                                <!-- Delete Action -->
                                                <li>
                                                    <form action="{{ route('users.destroy', $user) }}"
                                                        method="POST" id="delete-form-{{ $user->id }}">
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
                                    <td colspan="6" class="text-center text-muted">No book types found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
