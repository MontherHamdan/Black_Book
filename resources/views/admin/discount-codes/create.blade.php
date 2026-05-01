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

                    <form action="{{ route('discount-codes.store') }}" method="POST">
                        @csrf
                        <h4 class="mb-4 text-primary"><i class="fas fa-tag me-2"></i>Add New Discount Code</h4>

                        <div class="mb-4 p-3 bg-light rounded border border-light-subtle">
                            <label class="form-label fw-bold d-block mb-3 text-dark">Code Type (نوع الكود)</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input shadow-sm" type="radio" name="is_group" id="type_individual"
                                    value="0" {{ old('is_group', '0') == '0' ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold text-secondary" for="type_individual"
                                    style="cursor: pointer;">Individual (فردي)</label>
                            </div>
                            <div class="form-check form-check-inline ms-4">
                                <input class="form-check-input shadow-sm" type="radio" name="is_group" id="type_group"
                                    value="1" {{ old('is_group') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold text-secondary" for="type_group"
                                    style="cursor: pointer;">Group (مجموعة)</label>
                            </div>
                        </div>

                        {{-- ═══ Base Info ═══ --}}
                        <div class="row mb-4">
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-bold" for="discount_code">Discount Code</label>
                                <input type="text" name="discount_code" id="discount_code" class="form-control shadow-sm"
                                    value="{{ old('discount_code') }}" required placeholder="e.g. SUMMER2026">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-bold" for="code_name">Code Name</label>
                                <input type="text" name="code_name" id="code_name" class="form-control shadow-sm"
                                    value="{{ old('code_name') }}" placeholder="e.g. JUST Graduation">
                            </div>

                            <div class="col-md-3 mb-3 individual-fields">
                                <label class="form-label fw-bold" for="discount_value">Base Discount Value</label>
                                <input type="number" step="0.01" name="discount_value" id="discount_value"
                                    class="form-control shadow-sm" value="{{ old('discount_value') }}" min="0" required>
                            </div>
                            <div class="col-md-3 mb-3 individual-fields">
                                <label class="form-label fw-bold" for="discount_type">Discount Type</label>
                                <select name="discount_type" id="discount_type" class="form-select shadow-sm" required>
                                    <option value="" disabled selected>Select Type</option>
                                    <option value="percentage" {{ old('discount_type') == 'percentage' ? 'selected' : '' }}>
                                        Percentage (%)</option>
                                    <option value="byJd" {{ old('discount_type') == 'byJd' ? 'selected' : '' }}>By JOD
                                    </option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3 group-fields" style="display: none;">
                                <label class="form-label fw-bold text-primary" for="plan_id"><i
                                        class="fas fa-crown me-1"></i>Select Plan (اختر الخطة)</label>
                                <select name="plan_id" id="plan_id" class="form-select border-primary shadow-sm"
                                    style="background-color: #f8faff;">
                                    <option value="" disabled selected>Select a Plan</option>
                                    @if(isset($plans))
                                        @foreach($plans as $plan)
                                            <option value="{{ $plan->id }}" {{ old('plan_id') == $plan->id ? 'selected' : '' }}>
                                                {{ $plan->title }} (Target: {{ $plan->person_number }} | Discount:
                                                {{ $plan->discount_price }} JOD)
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>

                        {{-- ═══ Contact & Delivery Info (Group ONLY) ═══ --}}
                        <div class="card border rounded-4 mb-4 group-fields shadow-sm" style="display: none;">
                            <div class="card-header bg-light rounded-top-4 border-bottom-0">
                                <h5 class="mb-0 text-primary"><i class="fas fa-truck me-2"></i>Contact & Delivery Details
                                </h5>
                            </div>
                            <div class="card-body p-4 bg-white rounded-bottom-4">
                                <div class="row">
                                    <!-- أرقام الهواتف -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Primary Phone (رقم الطالب) <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="user_phone_number" id="user_phone_number"
                                            class="form-control shadow-sm" value="{{ old('user_phone_number') }}"
                                            placeholder="079xxxxxxx">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Secondary Phone (رقم احتياطي)</label>
                                        <input type="text" name="delivery_number_two" class="form-control shadow-sm"
                                            value="{{ old('delivery_number_two') }}" placeholder="078xxxxxxx (Optional)">
                                    </div>

                                    <div class="col-12 mb-3 mt-2 border-top pt-3">
                                        <label class="form-label fw-bold d-block mb-2">Delivery Target (مكان
                                            التوصيل)</label>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="delivery_target"
                                                id="target_university" value="university" {{ old('delivery_target', 'university') == 'university' ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold text-secondary"
                                                for="target_university">University (للجامعة)</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="delivery_target"
                                                id="target_home" value="home" {{ old('delivery_target') == 'home' ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold text-secondary" for="target_home">Home
                                                (للمنزل)</label>
                                        </div>
                                    </div>

                                    <!-- الجامعة -->
                                  <!-- الجامعة -->
                                    <div class="col-12 mb-3 delivery-uni-fields">
                                        <label class="form-label fw-bold">University (الجامعة)</label>
                                        <select name="university_id" id="university_id" class="form-select shadow-sm">
                                            <option value="" selected disabled>Select University</option>
                                            @foreach($universities as $uni)
                                                <!-- استخدمنا ?? للـ fallback في حال كان اسم العمود name بدل name_ar -->
                                                <option value="{{ $uni->id }}" {{ old('university_id') == $uni->id ? 'selected' : '' }}>
                                                    {{ $uni->name_ar ?? $uni->name ?? 'بدون اسم' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- المنزل -->
                                    <div class="col-md-4 mb-3 delivery-home-fields" style="display: none;">
                                        <label class="form-label fw-bold">Governorate (المحافظة)</label>
                                        <select name="governorate_id" id="governorate_id" class="form-select shadow-sm">
                                            <option value="" selected disabled>Select Governorate</option>
                                            @foreach($governorates as $gov)
                                                <option value="{{ $gov->id }}" {{ old('governorate_id') == $gov->id ? 'selected' : '' }}>
                                                    {{ $gov->name_ar }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3 delivery-home-fields" style="display: none;">
                                        <label class="form-label fw-bold">City (المدينة)</label>
                                        <!-- شلنا الـ foreach من هون لأننا رح نعبيه بالجافاسكربت، وضفنا id -->
                                        <select name="city_id" id="city_id" class="form-select shadow-sm" disabled>
                                            <option value="" selected disabled>Select City</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3 delivery-home-fields" style="display: none;">
                                        <label class="form-label fw-bold">Area (الحي)</label>
                                        <!-- شلنا الـ foreach وضفنا id -->
                                        <select name="area_id" id="area_id" class="form-select shadow-sm" disabled>
                                            <option value="" selected disabled>Select Area</option>
                                        </select>
                                    </div>

                                </div>
                            </div>
                        </div>

                        {{-- ═══ Tiers Section (Individual ONLY) ═══ --}}
                        <div class="card border rounded-4 mb-4 individual-fields shadow-sm" id="tiersSection">
                            <div
                                class="card-header bg-light d-flex justify-content-between align-items-center rounded-top-4 border-bottom-0">
                                <h5 class="mb-0 text-dark"><i class="fas fa-layer-group me-2 text-secondary"></i>Tiered
                                    Discounts (Optional)</h5>
                                <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm"
                                    id="addTierBtn">
                                    <i class="fas fa-plus me-1"></i>Add Tier
                                </button>
                            </div>
                            <div class="card-body p-4 bg-white rounded-bottom-4">
                                <p class="text-muted small mb-4 bg-light p-2 rounded border border-light-subtle">
                                    <i class="fas fa-info-circle me-1 text-primary"></i> Tiers apply escalating discounts
                                    when the number of orders using this code reaches the specified quantity. Base discount
                                    applies for quantities below the first tier.
                                </p>
                                <div id="tiersContainer">
                                    {{-- Dynamic tier rows inserted here --}}
                                </div>
                                <div id="noTiersMsg"
                                    class="text-center text-muted py-4 bg-light rounded border border-dashed">
                                    <i class="fas fa-inbox fa-2x mb-2 opacity-25"></i><br>
                                    No tiers added. Base discount will always apply.
                                </div>
                            </div>
                        </div>

                        <div class="text-end">
                            <a href="{{ route('discount-codes.index') }}" class="btn btn-outline-secondary me-2">Cancel</a>
                            <button type="submit" class="btn btn-success px-4"><i class="fas fa-save me-1"></i>Create
                                Discount Code</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        let tierIndex = 0;

        function addTierRow(minQty = '', discountValue = '', discountType = 'percentage') {
            const container = document.getElementById('tiersContainer');
            const row = document.createElement('div');
            row.className = 'row g-2 mb-2 align-items-end tier-row';
            row.innerHTML = `
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold">Min Quantity</label>
                                        <input type="number" name="tiers[${tierIndex}][min_qty]" class="form-control" min="2" value="${minQty}" required placeholder="e.g. 5">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold">Discount Value</label>
                                        <input type="number" step="0.01" name="tiers[${tierIndex}][discount_value]" class="form-control" min="0" value="${discountValue}" required placeholder="e.g. 10">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold">Type</label>
                                        <select name="tiers[${tierIndex}][discount_type]" class="form-select" required>
                                            <option value="percentage" ${discountType === 'percentage' ? 'selected' : ''}>Percentage (%)</option>
                                            <option value="byJd" ${discountType === 'byJd' ? 'selected' : ''}>By JOD</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <button type="button" class="btn btn-outline-danger btn-sm w-100" onclick="removeTierRow(this)">
                                            <i class="fas fa-trash me-1"></i>Remove
                                        </button>
                                    </div>
                                `;
            container.appendChild(row);
            tierIndex++;
            toggleNoTiersMsg();
        }

        function removeTierRow(btn) {
            btn.closest('.tier-row').remove();
            toggleNoTiersMsg();
        }

        function toggleNoTiersMsg() {
            const msg = document.getElementById('noTiersMsg');
            const rows = document.querySelectorAll('.tier-row');
            msg.style.display = rows.length === 0 ? 'block' : 'none';
        }

        document.getElementById('addTierBtn').addEventListener('click', () => addTierRow());

        // Logic to switch between Individual and Group types
        document.addEventListener('DOMContentLoaded', function () {
            const radioButtons = document.querySelectorAll('input[name="is_group"]');
            const individualFields = document.querySelectorAll('.individual-fields');
            const groupFields = document.querySelectorAll('.group-fields');
            const discountValueInput = document.getElementById('discount_value');
            const discountTypeSelect = document.getElementById('discount_type');
            const planSelect = document.getElementById('plan_id');
            // Logic to switch between University and Home delivery
            const deliveryRadios = document.querySelectorAll('input[name="delivery_target"]');
            const uniFields = document.querySelectorAll('.delivery-uni-fields');
            const homeFields = document.querySelectorAll('.delivery-home-fields');

            function toggleDeliveryFields(target) {
                if (target === 'university') {
                    uniFields.forEach(el => el.style.display = 'block');
                    homeFields.forEach(el => el.style.display = 'none');
                } else {
                    uniFields.forEach(el => el.style.display = 'none');
                    homeFields.forEach(el => el.style.display = 'block');
                }
            }

            const initialTarget = document.querySelector('input[name="delivery_target"]:checked').value;
            toggleDeliveryFields(initialTarget);

            deliveryRadios.forEach(radio => {
                radio.addEventListener('change', (e) => toggleDeliveryFields(e.target.value));
            });
            function toggleFields(isGroup) {
                if (isGroup) {
                    individualFields.forEach(el => el.style.display = 'none');
                    groupFields.forEach(el => el.style.display = 'block');

                    discountValueInput.removeAttribute('required');
                    discountTypeSelect.removeAttribute('required');
                    planSelect.setAttribute('required', 'required');
                    document.getElementById('user_phone_number').setAttribute('required', 'required');
                } else {
                    individualFields.forEach(el => el.style.display = 'block');
                    groupFields.forEach(el => el.style.display = 'none');

                    discountValueInput.setAttribute('required', 'required');
                    discountTypeSelect.setAttribute('required', 'required');
                    planSelect.removeAttribute('required');
                    document.getElementById('user_phone_number').removeAttribute('required');
                }
            }

            // Run on load to set initial state (based on old() value)
            const initialIsGroup = document.querySelector('input[name="is_group"]:checked').value === '1';
            toggleFields(initialIsGroup);

            // Run on change
            radioButtons.forEach(radio => {
                radio.addEventListener('change', (e) => {
                    toggleFields(e.target.value === '1');
                });
                
            });
            // --- Logic for Dependent Dropdowns (Governorate -> City -> Area) ---
            // تحويل داتا اللارافل لجافاسكربت أوبجكت
            const allCities = @json($cities);
            const allAreas = @json($areas);

            const govSelect = document.getElementById('governorate_id');
            const citySelect = document.getElementById('city_id');
            const areaSelect = document.getElementById('area_id');

            // القيم القديمة (عشان لو صار Error بالـ Validation يرجع يختارهم صح)
            const oldCityId = "{{ old('city_id') }}";
            const oldAreaId = "{{ old('area_id') }}";

            // دالة تعبئة المدن بناءً على المحافظة
            function populateCities(govId, selectedCity = null) {
                citySelect.innerHTML = '<option value="" selected disabled>Select City</option>';
                areaSelect.innerHTML = '<option value="" selected disabled>Select Area</option>';
                areaSelect.disabled = true;

                if (govId) {
                    const filteredCities = allCities.filter(c => c.governorate_id == govId);
                    filteredCities.forEach(city => {
                        const opt = document.createElement('option');
                        opt.value = city.id;
                        opt.textContent = city.name_ar || city.name_en;
                        if (selectedCity && selectedCity == city.id) opt.selected = true;
                        citySelect.appendChild(opt);
                    });
                    citySelect.disabled = false;
                } else {
                    citySelect.disabled = true;
                }
            }

            // دالة تعبئة الأحياء بناءً على المدينة
            function populateAreas(cityId, selectedArea = null) {
                areaSelect.innerHTML = '<option value="" selected disabled>Select Area</option>';

                if (cityId) {
                    const filteredAreas = allAreas.filter(a => a.city_id == cityId);
                    filteredAreas.forEach(area => {
                        const opt = document.createElement('option');
                        opt.value = area.id;
                        opt.textContent = area.name_ar || area.name_en;
                        if (selectedArea && selectedArea == area.id) opt.selected = true;
                        areaSelect.appendChild(opt);
                    });
                    areaSelect.disabled = false;
                } else {
                    areaSelect.disabled = true;
                }
            }

            // عند تغيير المحافظة
            govSelect.addEventListener('change', function() {
                populateCities(this.value);
            });

            // عند تغيير المدينة
            citySelect.addEventListener('change', function() {
                populateAreas(this.value);
            });

            // تشغيل الدالة عند تحميل الصفحة (إذا كان في محافظة مختارة مسبقاً)
            if (govSelect.value) {
                populateCities(govSelect.value, oldCityId);
                if (oldCityId) {
                    populateAreas(oldCityId, oldAreaId);
                }
            }
        });
    </script>
@endsection