@extends('admin.layout')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
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

                <form action="{{ route('videos.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <h4 class="mb-4 text-primary">Add New Video</h4>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold" for="title">Title</label>
                            <input type="text" name="title" id="title" class="form-control"
                                value="{{ old('title') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold" for="video">Video File</label>
                            <input type="file" name="video" id="video" class="form-control"
                                accept="video/mp4,video/mov,video/avi,video/wmv,video/webm" required>
                            <small class="text-muted">Accepted formats: MP4, MOV, AVI, WMV, WebM (Max: 100MB)</small>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold" for="description">Description</label>
                            <textarea name="description" id="description" class="form-control" rows="3"
                                placeholder="Enter video description...">{{ old('description') }}</textarea>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-success px-4">Upload Video</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
