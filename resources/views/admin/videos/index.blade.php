@extends('admin.layout')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center p-3">
                <h1 class="mb-0 text-primary">Videos</h1>
                <a href="{{ route('videos.create') }}" class="btn btn-success">
                    <i class="fas fa-plus me-1"></i> Add New Video
                </a>
            </div>

            <!-- Table Section -->
            <div class="card-body">
                <table id="responsive-datatable" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th class="text-center">ID</th>
                            <th class="text-center">Title</th>
                            <th class="text-center">Description</th>
                            <th class="text-center">Video</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($videos as $video)
                        <tr>
                            <td class="text-center">{{ $video->id }}</td>
                            <td class="text-center">{{ $video->title }}</td>
                            <td class="text-center">{{ Str::limit($video->description, 50) ?? '-' }}</td>
                            <td class="text-center">
                                @if($video->video)
                                    <video width="120" controls>
                                        <source src="{{ $video->video_url }}" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="dropdown">
                                    <a class="dropdown-toggle" id="dropdownMenuButton{{ $video->id }}"
                                        data-bs-toggle="dropdown" style="cursor: pointer;" aria-expanded="false"
                                        title="Actions">
                                        <i class="fas fa-ellipsis-h"></i>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end"
                                        aria-labelledby="dropdownMenuButton{{ $video->id }}">
                                        <li>
                                            <a href="{{ route('videos.edit', $video) }}"
                                                class="dropdown-item" title="Edit Video">
                                                <i class="fas fa-edit me-2"></i>Edit
                                            </a>
                                        </li>
                                        <li>
                                            <form action="{{ route('videos.destroy', $video) }}"
                                                method="POST" id="delete-form-{{ $video->id }}">
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
                            <td colspan="5" class="text-center text-muted">No videos found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
