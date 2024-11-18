@extends('admin.dashboard')
@include('admin.head')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1 class="page-title">Users</h1>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary mb-3">Add User</a>
            <div class="card">
                <div class="card-body">
                    <table id="userTable" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                            <tr id="user-{{ $user->id }}">
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <!-- Edit Button -->
                                    <button 
                                        class="btn btn-warning btn-sm edit-user-btn" 
                                        data-bs-toggle="modal"
                                        data-bs-target="#editUserModal"
                                        data-id="{{ $user->id }}" 
                                        data-name="{{ $user->name }}" 
                                        data-email="{{ $user->email }}">
                                        Edit
                                    </button>
                    
                                    <!-- Delete Button -->
                                    <button 
                                        class="btn btn-danger btn-sm delete-user-btn" 
                                        data-id="{{ $user->id }}">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editUserForm">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="editUserId" name="id">
                    <div class="mb-3">
                        <label for="editUserName" class="form-label">Name</label>
                        <input type="text" class="form-control" id="editUserName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="editUserEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="editUserEmail" name="email" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

<!-- JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        const csrfToken = $('meta[name="csrf-token"]').attr('content');

        // Handle Edit Button Click
        $('.edit-user-btn').on('click', function () {
            const userId = $(this).data('id');
            const userName = $(this).data('name');
            const userEmail = $(this).data('email');

            $('#editUserId').val(userId);
            $('#editUserName').val(userName);
            $('#editUserEmail').val(userEmail);
        });

        // Handle Edit Form Submission
        $('#editUserForm').on('submit', function (e) {
            e.preventDefault();

            const userId = $('#editUserId').val();
            const formData = $(this).serialize();

            $.ajax({
                url: `/admin/users/${userId}`,
                type: 'PUT',
                data: formData,
                success: function (response) {
                    alert(response.success || 'User updated successfully!');
                    location.reload();
                },
                error: function () {
                    alert('Error updating user.');
                }
            });
        });

        // Handle Delete Button Click
        $('.delete-user-btn').on('click', function () {
            if (!confirm('Are you sure you want to delete this user?')) return;

            const userId = $(this).data('id');

            $.ajax({
                url: `/admin/users/${userId}`,
                type: 'DELETE',
                data: { _token: csrfToken },
                success: function (response) {
                    alert(response.success || 'User deleted successfully!');
                    $(`#user-${userId}`).remove();
                },
                error: function () {
                    alert('Error deleting user.');
                }
            });
        });
    });
</script>

