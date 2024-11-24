@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>{{ isset($bookDesign) ? 'Edit' : 'Add' }} Book Design</h1>
        <form method="POST"
            action="{{ isset($bookDesign) ? route('book-designs.update', $bookDesign) : route('book-designs.store') }}"
            enctype="multipart/form-data">
            @csrf
            @if (isset($bookDesign))
                @method('PUT')
            @endif

            <div class="mb-3">
                <label for="image" class="form-label">Image</label>
                <input type="file" class="form-control" name="image" id="image"
                    {{ isset($bookDesign) ? '' : 'required' }}>
                @if (isset($bookDesign) && $bookDesign->image)
                    <img src="{{ asset('storage/' . $bookDesign->image) }}" alt="Current Image" width="100"
                        class="mt-2">
                @endif
            </div>

            <div class="mb-3">
                <label for="category_id" class="form-label">Category</label>
                <select name="category_id" id="category_id" class="form-control" required>
                    <option value="">Select Category</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}"
                            {{ isset($bookDesign) && $bookDesign->category_id == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="sub_category_id" class="form-label">Subcategory</label>
                <select name="sub_category_id" id="sub_category_id" class="form-control">
                    <option value="">Select Subcategory</option>
                    @foreach ($subCategories ?? [] as $subCategory)
                        <option value="{{ $subCategory->id }}"
                            {{ isset($bookDesign) && $bookDesign->sub_category_id == $subCategory->id ? 'selected' : '' }}>
                            {{ $subCategory->name }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-success">Save</button>
        </form>
    </div>
@endsection
