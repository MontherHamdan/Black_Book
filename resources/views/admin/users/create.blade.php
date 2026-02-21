@extends('admin.layout')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Add New User</h4>
            </div>
            <div class="card-body">
                @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input
                                type="text"
                                name="name"
                                id="name"
                                class="form-control"
                                placeholder="Enter user name"
                                required
                                value="{{ old('name') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input
                                type="email"
                                name="email"
                                id="email"
                                class="form-control"
                                placeholder="Enter user email"
                                required
                                value="{{ old('email') }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input
                                type="password"
                                name="password"
                                id="password"
                                class="form-control"
                                placeholder="Enter password"
                                required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                            <input
                                type="password"
                                name="password_confirmation"
                                id="password_confirmation"
                                class="form-control"
                                placeholder="Confirm password"
                                required>
                        </div>
                    </div>

                    <div class="row">
                        {{-- Title --}}
                        <div class="col-md-6 mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input
                                type="text"
                                name="title"
                                id="title"
                                class="form-control"
                                placeholder="Enter user title"
                                value="{{ old('title') }}">
                        </div>

                        {{-- User Image --}}
                        <div class="col-md-6 mb-3">
                            <label for="image" class="form-label">User Image</label>
                            <input
                                type="file"
                                name="image"
                                id="image"
                                class="form-control">
                        </div>
                    </div>
<hr>
                    <h5 class="text-primary mb-3"><i class="fas fa-coins me-2"></i>إعدادات عمولة المصمم</h5>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="base_order_price" class="form-label">السعر الأساسي للطلب</label>
                            <input type="number" step="0.01" min="0" name="base_order_price" id="base_order_price" class="form-control" placeholder="مثال: 1.50" value="{{ old('base_order_price', 0) }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="decoration_price" class="form-label">سعر صورة الزخرفة</label>
                            <input type="number" step="0.01" min="0" name="decoration_price" id="decoration_price" class="form-control" placeholder="مثال: 0.25" value="{{ old('decoration_price', 0) }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="custom_gift_price" class="form-label">سعر الإهداء المخصص</label>
                            <input type="number" step="0.01" min="0" name="custom_gift_price" id="custom_gift_price" class="form-control" placeholder="مثال: 0.25" value="{{ old('custom_gift_price', 0) }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="internal_image_price" class="form-label">سعر الصورة الداخلية</label>
                            <input type="number" step="0.01" min="0" name="internal_image_price" id="internal_image_price" class="form-control" placeholder="مثال: 0.50" value="{{ old('internal_image_price', 0) }}">
                        </div>
                    </div>
                    <hr>
                    {{-- Role --}}
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="role" class="form-label">Role</label>
                            <select
                                name="role"
                                id="role"
                                class="form-control"
                                required>
                                <option value="">Select role</option>
                                <option value="{{ \App\Models\User::ROLE_ADMIN }}"
                                    {{ old('role') === \App\Models\User::ROLE_ADMIN ? 'selected' : '' }}>
                                    Admin
                                </option>
                                <option value="{{ \App\Models\User::ROLE_DESIGNER }}"
                                    {{ old('role') === \App\Models\User::ROLE_DESIGNER ? 'selected' : '' }}>
                                    Designer
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-success px-4">Add User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection