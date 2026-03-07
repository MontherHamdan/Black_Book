@extends('admin.layout')

@section('content')
    <style>
        /* ستايلات إبداعية للفورم */
        .edit-card {
            border-radius: 20px;
            border: none;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .edit-header {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            padding: 30px;
            text-align: center;
            color: white;
        }

        .edit-header h3 {
            font-weight: 800;
            margin-bottom: 5px;
            letter-spacing: 1px;
        }

        .edit-header p {
            opacity: 0.8;
            font-size: 0.95rem;
            margin-bottom: 0;
        }

        .form-control-custom {
            border-radius: 12px;
            border: 2px solid #eef2f7;
            padding: 12px 18px;
            font-weight: 500;
            transition: all 0.3s;
            background-color: #f8f9fc;
        }

        .form-control-custom:focus {
            border-color: #4e73df;
            background-color: #fff;
            box-shadow: 0 0 0 4px rgba(78, 115, 223, 0.1);
        }

        .form-label {
            font-weight: 700;
            color: #4a5568;
            margin-bottom: 8px;
            font-size: 0.95rem;
        }

        .btn-update {
            background-color: #1cc88a;
            border: none;
            border-radius: 12px;
            padding: 12px 30px;
            font-weight: 700;
            color: white;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(28, 200, 138, 0.3);
        }

        .btn-update:hover {
            background-color: #13a671;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(28, 200, 138, 0.4);
        }

        .btn-cancel {
            background-color: #f8f9fc;
            border: 2px solid #e3e6f0;
            border-radius: 12px;
            padding: 12px 30px;
            font-weight: 700;
            color: #858796;
            transition: all 0.3s;
        }

        .btn-cancel:hover {
            background-color: #eaedf4;
            color: #5a5c69;
        }

        /* تحسينات لـ Dropify عشان يتماشى مع الستايل */
        .dropify-wrapper {
            border-radius: 15px !important;
            border: 2px dashed #d1d3e2 !important;
            background-color: #f8f9fc !important;
        }

        .dropify-wrapper:hover {
            border-color: #4e73df !important;
        }
    </style>

    <div class="row justify-content-center mt-3">
        <div class="col-lg-8 col-md-10">
            <div class="card edit-card">

                <div class="edit-header">
                    <i class="mdi mdi-book-edit fs-1 mb-2 d-block"></i>
                    <h3>Edit Book Decoration</h3>
                    <p>Update the details and image of your decoration</p>
                </div>

                <div class="card-body p-4 p-md-5">

                    @if ($errors->any())
                        <div class="alert alert-danger rounded-3 border-0 shadow-sm mb-4">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-exclamation-triangle fs-4 me-2"></i>
                                <strong class="fs-5">Oops! Please fix the errors below:</strong>
                            </div>
                            <ul class="mb-0 ps-4">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('book-decorations.update', $bookDecoration->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row g-4">
                            <div class="col-12">
                                <label for="name" class="form-label">
                                    <i class="fas fa-tag me-1 text-primary"></i> Decoration Name <span
                                        class="text-danger">*</span>
                                </label>
                                <input type="text"
                                    class="form-control form-control-custom @error('name') is-invalid @enderror" name="name"
                                    id="name" value="{{ old('name', $bookDecoration->name) }}"
                                    placeholder="e.g. Vintage Floral Border">
                                @error('name')
                                    <div class="invalid-feedback fw-bold mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 mt-4">
                                <label for="image" class="form-label">
                                    <i class="fas fa-image me-1 text-primary"></i> Decoration Image
                                </label>

                                <div class="position-relative">
                                    <input type="file" data-plugins="dropify" data-height="250"
                                        data-default-file="{{ $bookDecoration->image }}"
                                        class="form-control @error('image') is-invalid @enderror" name="image" id="image"
                                        accept="image/*">
                                </div>

                                <small class="text-muted mt-2 d-block">
                                    <i class="fas fa-info-circle me-1"></i> Upload a new image to replace the current one.
                                    Leave empty to keep it unchanged.
                                </small>

                                @error('image')
                                    <div class="invalid-feedback d-block fw-bold mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-3 mt-5 border-top pt-4">
                            <a href="{{ route('book-decorations.index') }}" class="btn btn-cancel text-decoration-none">
                                <i class="fas fa-times me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-update">
                                <i class="fas fa-save me-1"></i> Update Decoration
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection