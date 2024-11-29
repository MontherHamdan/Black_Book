@extends('admin.layout')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="d-flex justify-content-between align-items-center p-3">
                    <h1 class="mb-0 text-primary">Book Types</h1>
                    <a href="{{ route('book-types.create') }}" class="btn btn-primary mb-3">Add New Book Type</a>
                </div>

                <div class="card-body">
                    <table id="responsive-datatable" class="table table-striped table-bordered dt-responsive nowrap">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th class="text-center">Image</th>
                                <th class="text-center">Price</th>
                                <th class="text-center">Description (EN)</th>
                                <th class="text-center">Description (AR)</th>
                                <th class="text-center">Sub-Media</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($bookTypes as $bookType)
                                <tr>
                                    <td class="text-center">{{ $bookType->id }}</td>
                                    <td class="text-center">
                                        <img class="img-fluid img-thumbnail rounded-circle" src="{{ $bookType->image }}"
                                            alt="Book Type Image" width="70" height="70">
                                    </td>
                                    <td class="text-center">{{ $bookType->price }}</td>
                                    <td class="text-center">{{ $bookType->description_en }}</td>
                                    <td class="text-center">{{ $bookType->description_ar }}</td>
                                    <td class="text-center">
                                        @forelse ($bookType->subMedia as $media)
                                            <div class="d-inline-block">
                                                @if ($media->type == 'image')
                                                    <img src="{{ $media->media }}" alt="Media" width="50"
                                                        height="50" class="img-thumbnail">
                                                @elseif ($media->type == 'video')
                                                    <video width="50" height="50" controls>
                                                        <source src="{{ $media->media }}" type="video/mp4">
                                                    </video>
                                                @endif
                                            </div>
                                        @empty
                                            <p>No media found</p>
                                        @endforelse
                                        <button class="btn btn-info btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#addMediaModal{{ $bookType->id }}">Add Media</button>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('book-types.edit', $bookType) }}"
                                            class="btn btn-warning btn-sm">Edit</a>
                                        <form action="{{ route('book-types.destroy', $bookType) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    </td>
                                </tr>

                                <!-- Add Media Modal -->
                                <div class="modal fade" id="addMediaModal{{ $bookType->id }}" tabindex="-1"
                                    aria-labelledby="addMediaModalLabel{{ $bookType->id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="addMediaModalLabel{{ $bookType->id }}">Add
                                                    Sub-Media</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="{{ route('book-types.submedia.store', $bookType) }}"
                                                    method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    <div class="mb-3">
                                                        <label for="media" class="form-label">Select Media</label>
                                                        <input type="file" name="media" class="form-control" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="type" class="form-label">Media Type</label>
                                                        <select name="type" class="form-select" required>
                                                            <option value="image">Image</option>
                                                            <option value="video">Video</option>
                                                        </select>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary">Save Media</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No Book Types Found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
