@extends('admin.layout')

@section('content')
<div class="container" style="direction: rtl; text-align: right;">
    <h1 class="my-4">تعديل بيانات اسم SVG</h1>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('svg-names.update', $svgName) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="name" class="form-label">الاسم الأصلي</label>
                    <input type="text"
                        name="name"
                        id="name"
                        class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name', $svgName->name) }}"
                        required>
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="normalized_name" class="form-label">الاسم المنسّق (Normalized)</label>
                    <input type="text"
                        name="normalized_name"
                        id="normalized_name"
                        class="form-control @error('normalized_name') is-invalid @enderror"
                        value="{{ old('normalized_name', $svgName->normalized_name) }}"
                        required>
                    @error('normalized_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="svg_code" class="form-label">كود الـ SVG</label>
                    <textarea
                        name="svg_code"
                        id="svg_code"
                        rows="6"
                        class="form-control font-monospace @error('svg_code') is-invalid @enderror"
                        dir="ltr">{{ old('svg_code', $svgName->svg_code) }}</textarea>
                    <small class="text-muted">
                        يمكنك تعديل كود الـ SVG الخاص بهذا الاسم مباشرة.
                    </small>
                    @error('svg_code')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-success">حفظ</button>
                <a href="{{ route('svg-names.index') }}" class="btn btn-secondary">رجوع</a>
            </form>
        </div>
    </div>
</div>
@endsection