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

                <form action="{{ route('specialized-departments.update', $specializedDepartment->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <h4 class="mb-4 text-primary">Edit Specialized Department</h4>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold" for="title">Title</label>
                            <input type="text" name="title" id="title" class="form-control"
                                value="{{ old('title', $specializedDepartment->title) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold" for="phone_number">Phone Number</label>
                            <input type="text" name="phone_number" id="phone_number" class="form-control"
                                value="{{ old('phone_number', $specializedDepartment->phone_number) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold" for="whatsapp_link">WhatsApp Link</label>
                            <input type="text" name="whatsapp_link" id="whatsapp_link" class="form-control"
                                value="{{ old('whatsapp_link', $specializedDepartment->whatsapp_link) }}" placeholder="https://wa.me/...">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold" for="color_code">Color Code</label>
                            <div class="input-group">
                                <input type="color" name="color_code" id="color_code" class="form-control form-control-color"
                                    value="{{ old('color_code', $specializedDepartment->color_code ?? '#000000') }}" title="Choose color">
                                <input type="text" class="form-control" id="color_text" 
                                    value="{{ old('color_code', $specializedDepartment->color_code ?? '#000000') }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-bold" for="icon_svg">Icon SVG Code</label>
                            <textarea name="icon_svg" id="icon_svg" class="form-control" rows="3"
                                placeholder="Paste SVG code here...">{{ old('icon_svg', $specializedDepartment->icon_svg) }}</textarea>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary px-4">Update Department</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('color_code').addEventListener('input', function() {
    document.getElementById('color_text').value = this.value;
});
</script>
@endsection
