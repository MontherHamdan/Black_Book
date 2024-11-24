@extends('admin.layout')
@section('admin.content')
    <div class="container">
        <h1>Create Book Design</h1>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('book-designs.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group mb-3">
                <label for="image" class="form-label">Image</label>
                <input type="file" class="form-control" id="image" name="image" required>
            </div>

            <div class="form-group mb-3">
                <label for="category_id" class="form-label">Category</label>
                <select class="form-control" id="category_id" name="category_id" required>
                    <option value="" disabled selected>Select a category</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group mb-3">
                <label for="sub_category_id" class="form-label">Subcategory (optional)</label>
                <select class="form-control" id="sub_category_id" name="sub_category_id">
                    <option value="" disabled selected>Select a subcategory</option>
                    <!-- Subcategories will be populated dynamically using JavaScript -->
                </select>
            </div>

            <button type="submit" class="btn btn-success">Create Design</button>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const categorySelect = document.getElementById('category_id');
            const subCategorySelect = document.getElementById('sub_category_id');

            categorySelect.addEventListener('change', function() {
                const categoryId = this.value;

                // Clear the subcategory options
                subCategorySelect.innerHTML =
                    '<option value="" disabled selected>Select a subcategory</option>';

                if (categoryId) {
                    fetch(`/api/v1/book_design_subCategories?category_id=${categoryId}`)
                        .then(response => response.json())
                        .then(responseData => {
                            // Check the structure of the response
                            if (responseData.status === 'success' && Array.isArray(responseData.data)) {
                                const subCategories = responseData.data;

                                subCategories.forEach(subCategory => {
                                    const option = document.createElement('option');
                                    option.value = subCategory.id;
                                    option.textContent = subCategory.name;
                                    subCategorySelect.appendChild(option);
                                });
                            } else {
                                console.error(
                                    'Error: Subcategories data is not an array or request failed');
                            }
                        })
                        .catch(error => console.error('Error fetching subcategories:', error));
                }
            });
        });
    </script>
@endsection
