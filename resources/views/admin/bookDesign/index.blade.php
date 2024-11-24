@extends('admin.layout')
@section('admin.content')
    <div class="container">
        <h1>Book Designs</h1>
        <a href="{{ route('book-designs.create') }}" class="btn btn-primary">Add New Design</a>
        <table class="table table-bordered mt-4">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Category</th>
                    <th>Subcategory</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($bookDesigns as $design)
                    <tr>
                        <td>{{ $design->id }}</td>
                        <td> <img src="{{ $design->image }}" alt="Design Image" width="100"></td>
                        <td>{{ $design->category->name }}</td>
                        <td>{{ $design->subCategory->name ?? 'N/A' }}</td>
                        <td>
                            <a href="{{ route('book-designs.edit', $design) }}" class="btn btn-warning">Edit</a>
                            <form action="{{ route('book-designs.destroy', $design) }}" method="POST"
                                style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
