@extends('admin.layout')

@section('admin.content')
    <div class="container">
        <h1>Edit Book Design</h1>

        <form action="{{ route('book-designs.update', $bookDesign->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Book Design Image -->
            <div class="form-group">
                <label for="image">Book Design Image</label>
                <div>
                    <!-- Display the current image -->
                    <img src="{{ $bookDesign->image }}" alt="Current Image" width="150">
                </div>
                <input type="file" class="form-control @error('image') is-invalid @enderror" name="image" id="image"
                    accept="image/*">
                @error('image')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Category Selection -->
            <div class="form-group">
                <label for="category_id">Category</label>
                <select name="category_id" id="category_id" class="form-control @error('category_id') is-invalid @enderror">
                    <option value="" disabled>Select Category</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}"
                            {{ $category->id == $bookDesign->category_id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Subcategory Selection -->
            <div class="form-group">
                <label for="sub_category_id">Subcategory</label>
                <select name="sub_category_id" id="sub_category_id"
                    class="form-control @error('sub_category_id') is-invalid @enderror">
                    <option value="">Select Subcategory</option>
                    @foreach ($subCategories as $subCategory)
                        <option value="{{ $subCategory->id }}"
                            {{ $subCategory->id == $bookDesign->sub_category_id ? 'selected' : '' }}>
                            {{ $subCategory->name }}
                        </option>
                    @endforeach
                </select>
                @error('sub_category_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">Update Design</button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const categorySelect = document.getElementById('category_id');
            const subCategorySelect = document.getElementById('sub_category_id');
            const selectedCategoryId = '{{ $bookDesign->category_id }}'; // Get the currently selected category ID

            // Function to populate subcategories based on selected category
            function populateSubCategories(categoryId) {
                // Clear the subcategory options
                subCategorySelect.innerHTML =
                    '<option value="" disabled selected>Select Subcategory</option>';

                if (categoryId) {
                    fetch(`/api/v1/book_design_subCategories?category_id=${categoryId}`)
                        .then(response => response.json())
                        .then(responseData => {
                            if (responseData.status === 'success' && Array.isArray(responseData.data)) {
                                const subCategories = responseData.data;

                                subCategories.forEach(subCategory => {
                                    const option = document.createElement('option');
                                    option.value = subCategory.id;
                                    option.textContent = subCategory.name;
                                    subCategorySelect.appendChild(option);
                                });

                                // Select the current subcategory if it's already set
                                if ('{{ $bookDesign->sub_category_id }}') {
                                    subCategorySelect.value = '{{ $bookDesign->sub_category_id }}';
                                }
                            } else {
                                console.error('Error: Subcategories data is not an array or request failed');
                            }
                        })
                        .catch(error => console.error('Error fetching subcategories:', error));
                }
            }

            // Initially populate subcategories based on the current category
            populateSubCategories(selectedCategoryId);

            // Update subcategories when a new category is selected
            categorySelect.addEventListener('change', function() {
                populateSubCategories(this.value);
            });
        });
    </script>
@endsection
