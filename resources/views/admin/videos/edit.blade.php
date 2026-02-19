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

                <form action="{{ route('videos.update', $video->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <h4 class="mb-4 text-primary">Edit Video</h4>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold" for="title">Title</label>
                            <input type="text" name="title" id="title" class="form-control"
                                value="{{ old('title', $video->title) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold" for="video">Video File</label>
                            <input type="file" name="video" id="video" class="form-control"
                                accept="video/mp4,video/mov,video/avi,video/wmv,video/webm">
                            <small class="text-muted">Leave empty to keep the current video. Accepted: MP4, MOV, AVI, WMV, WebM (Max: 100MB)</small>
                        </div>
                    </div>

                    @if($video->video)
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Current Video</label>
                            <div>
                                <video width="300" controls>
                                    <source src="{{ $video->video_url }}" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold" for="description">Description</label>
                            <textarea name="description" id="description" class="form-control" rows="3"
                                placeholder="Enter video description...">{{ old('description', $video->description) }}</textarea>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary px-4">Update Video</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
