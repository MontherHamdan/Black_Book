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
                    <form action="{{ route('universities.store') }}" method="POST">
                        @csrf
                        <h4 class="mb-4 text-primary">Add New University</h4>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold" for="name">University Name</label>
                                <input type="text" name="name" id="name" class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold" for="governorate_id">Governorate</label>
                                <select name="governorate_id" id="governorate_id" class="form-select">
                                    <option value="">-- Select Governorate --</option>
                                    @foreach ($governorates as $gov)
                                        <option value="{{ $gov->id }}">{{ $gov->name_ar }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold" for="city_id">City</label>
                                <select name="city_id" id="city_id" class="form-select" disabled>
                                    <option value="">-- Select City --</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold" for="area_id">Area</label>
                                <select name="area_id" id="area_id" class="form-select" disabled>
                                    <option value="">-- Select Area --</option>
                                </select>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-success px-4">Create University</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            const governorateSelect = document.getElementById('governorate_id');
            const citySelect = document.getElementById('city_id');
            const areaSelect = document.getElementById('area_id');

            governorateSelect.addEventListener('change', function () {
                const govId = this.value;
                citySelect.innerHTML = '<option value="">-- Select City --</option>';
                areaSelect.innerHTML = '<option value="">-- Select Area --</option>';
                citySelect.disabled = true;
                areaSelect.disabled = true;

                if (!govId) return;

                fetch(`/api/v1/locations/cities/${govId}`)
                    .then(r => r.json())
                    .then(response => {
                        const cities = response.data ?? [];
                        cities.forEach(c => {
                            citySelect.innerHTML += `<option value="${c.id}">${c.name_ar}</option>`;
                        });
                        citySelect.disabled = false;
                    });
            });

            citySelect.addEventListener('change', function () {
                const cityId = this.value;
                areaSelect.innerHTML = '<option value="">-- Select Area --</option>';
                areaSelect.disabled = true;

                if (!cityId) return;

                fetch(`/api/v1/locations/areas/${cityId}`)
                    .then(r => r.json())
                    .then(response => {
                        const areas = response.data ?? [];
                        areas.forEach(a => {
                            areaSelect.innerHTML += `<option value="${a.id}">${a.name_ar}</option>`;
                        });
                        areaSelect.disabled = false;
                    });
            });
        </script>
    @endpush
@endsection