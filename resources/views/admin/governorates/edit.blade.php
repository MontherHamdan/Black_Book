@extends('admin.layout')

@section('content')
    <h1>Edit Governorate</h1>

    <form action="{{ route('governorates.update', $governorate->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="name_en">English Name</label>
            <input type="text" name="name_en" id="name_en" class="form-control" value="{{ $governorate->name_en }}"
                required>
        </div>
        <div class="form-group">
            <label for="name_ar">Arabic Name</label>
            <input type="text" name="name_ar" id="name_ar" class="form-control" value="{{ $governorate->name_ar }}"
                required>
        </div>
        <button type="submit" class="btn btn-warning mt-3">Update Governorate</button>
    </form>
@endsection
