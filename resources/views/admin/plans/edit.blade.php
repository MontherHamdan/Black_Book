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

                <form action="{{ route('plans.update', $plan->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <h4 class="mb-4 text-primary">Edit Plan</h4>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold" for="title">Title</label>
                            <input type="text" name="title" id="title" class="form-control"
                                value="{{ old('title', $plan->title) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold" for="book_price">Book Price (JOD)</label>
                            <input type="number" name="book_price" id="book_price" class="form-control"
                                value="{{ old('book_price', $plan->book_price) }}" step="0.01" min="0" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold" for="discount_price">Discount Price (JOD)</label>
                            <input type="number" name="discount_price" id="discount_price" class="form-control"
                                value="{{ old('discount_price', $plan->discount_price) }}" step="0.01" min="0" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold" for="person_number">Person Number</label>
                            <input type="text" name="person_number" id="person_number" class="form-control"
                                value="{{ old('person_number', $plan->person_number) }}">
                        </div>
                    </div>

                    <!-- Dynamic Features -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Features</label>
                        <div id="features-container">
                            @php
                                $features = old('features', $plan->features ?? []);
                            @endphp
                            @if($features && count($features) > 0)
                                @foreach($features as $feature)
                                <div class="input-group mb-2 feature-row">
                                    <input type="text" name="features[]" class="form-control" value="{{ $feature }}" placeholder="Enter feature">
                                    <button type="button" class="btn btn-danger remove-feature"><i class="fas fa-times"></i></button>
                                </div>
                                @endforeach
                            @else
                                <div class="input-group mb-2 feature-row">
                                    <input type="text" name="features[]" class="form-control" placeholder="Enter feature">
                                    <button type="button" class="btn btn-danger remove-feature"><i class="fas fa-times"></i></button>
                                </div>
                            @endif
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm" id="add-feature">
                            <i class="fas fa-plus me-1"></i> Add Feature
                        </button>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary px-4">Update Plan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('add-feature').addEventListener('click', function() {
    const container = document.getElementById('features-container');
    const row = document.createElement('div');
    row.className = 'input-group mb-2 feature-row';
    row.innerHTML = '<input type="text" name="features[]" class="form-control" placeholder="Enter feature">' +
                    '<button type="button" class="btn btn-danger remove-feature"><i class="fas fa-times"></i></button>';
    container.appendChild(row);
});

document.addEventListener('click', function(e) {
    if (e.target.closest('.remove-feature')) {
        const rows = document.querySelectorAll('.feature-row');
        if (rows.length > 1) {
            e.target.closest('.feature-row').remove();
        }
    }
});
</script>
@endsection
