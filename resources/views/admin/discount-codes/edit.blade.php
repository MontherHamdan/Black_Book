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

                    <form action="{{ route('discount-codes.update', $discountCode->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <h4 class="mb-4 text-primary"><i class="fas fa-edit me-2"></i>Edit Discount Code</h4>

                        <div class="mb-4 p-3 bg-light rounded border border-light-subtle">
                            <label class="form-label fw-bold d-block mb-3 text-dark">Code Type (نوع الكود)</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input shadow-sm" type="radio" name="is_group" id="type_individual"
                                    value="0" {{ old('is_group', $discountCode->is_group) == '0' ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold text-secondary" for="type_individual"
                                    style="cursor: pointer;">Individual (فردي)</label>
                            </div>
                            <div class="form-check form-check-inline ms-4">
                                <input class="form-check-input shadow-sm" type="radio" name="is_group" id="type_group"
                                    value="1" {{ old('is_group', $discountCode->is_group) == '1' ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold text-secondary" for="type_group"
                                    style="cursor: pointer;">Group (مجموعة)</label>
                            </div>
                        </div>

                        {{-- ═══ Base Info ═══ --}}
                        <div class="row mb-4">
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-bold" for="discount_code">Discount Code</label>
                                <input type="text" name="discount_code" id="discount_code" class="form-control shadow-sm"
                                    value="{{ old('discount_code', $discountCode->discount_code) }}" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-bold" for="code_name">Code Name</label>
                                <input type="text" name="code_name" id="code_name" class="form-control shadow-sm"
                                    value="{{ old('code_name', $discountCode->code_name) }}">
                            </div>

                            <div class="col-md-3 mb-3 individual-fields">
                                <label class="form-label fw-bold" for="discount_value">Base Discount Value</label>
                                <input type="number" step="0.01" name="discount_value" id="discount_value"
                                    class="form-control shadow-sm"
                                    value="{{ old('discount_value', $discountCode->discount_value) }}" min="0" required>
                            </div>
                            <div class="col-md-3 mb-3 individual-fields">
                                <label class="form-label fw-bold" for="discount_type">Discount Type</label>
                                <select name="discount_type" id="discount_type" class="form-select shadow-sm" required>
                                    <option value="" disabled>Select Type</option>
                                    <option value="percentage" {{ old('discount_type', $discountCode->discount_type) == 'percentage' ? 'selected' : '' }}>Percentage (%)
                                    </option>
                                    <option value="byJd" {{ old('discount_type', $discountCode->discount_type) == 'byJd' ? 'selected' : '' }}>By JOD</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3 group-fields" style="display: none;">
                                <label class="form-label fw-bold text-primary" for="plan_id"><i
                                        class="fas fa-crown me-1"></i>Select Plan (اختر الخطة)</label>
                                <select name="plan_id" id="plan_id" class="form-select border-primary shadow-sm"
                                    style="background-color: #f8faff;">
                                    <option value="" disabled>Select a Plan</option>
                                    @if(isset($plans))
                                        @foreach($plans as $plan)
                                            <option value="{{ $plan->id }}" {{ old('plan_id', $discountCode->plan_id) == $plan->id ? 'selected' : '' }}>
                                                {{ $plan->title }} (Target: {{ $plan->person_number }} | Discount:
                                                {{ $plan->discount_price }} JOD)
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
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
                                <div id="tiersContainer">
                                    {{-- Existing tier rows --}}
                                </div>
                                <div id="noTiersMsg"
                                    class="text-center text-muted py-4 bg-light rounded border border-dashed"
                                    style="display:none;">
                                    <i class="fas fa-inbox fa-2x mb-2 opacity-25"></i><br>
                                    No tiers added. Base discount will always apply.
                                </div>
                            </div>
                        </div>

                        <div class="text-end">
                            <a href="{{ route('discount-codes.index') }}" class="btn btn-outline-secondary me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save me-1"></i>Update
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

        // Pre-populate existing tiers
        document.addEventListener('DOMContentLoaded', function () {
            const existingTiers = @json($discountCode->tiers ?? []);
            if (existingTiers.length === 0) {
                document.getElementById('noTiersMsg').style.display = 'block';
            }
            existingTiers.forEach(tier => {
                addTierRow(tier.min_qty, tier.discount_value, tier.discount_type);
            });
        });

        // Logic to switch between Individual and Group types
        document.addEventListener('DOMContentLoaded', function () {
            const radioButtons = document.querySelectorAll('input[name="is_group"]');
            const individualFields = document.querySelectorAll('.individual-fields');
            const groupFields = document.querySelectorAll('.group-fields');
            const discountValueInput = document.getElementById('discount_value');
            const discountTypeSelect = document.getElementById('discount_type');
            const planSelect = document.getElementById('plan_id');

            function toggleFields(isGroup) {
                if (isGroup) {
                    individualFields.forEach(el => el.style.display = 'none');
                    groupFields.forEach(el => el.style.display = 'block');

                    discountValueInput.removeAttribute('required');
                    discountTypeSelect.removeAttribute('required');
                    planSelect.setAttribute('required', 'required');
                } else {
                    individualFields.forEach(el => el.style.display = 'block');
                    groupFields.forEach(el => el.style.display = 'none');

                    discountValueInput.setAttribute('required', 'required');
                    discountTypeSelect.setAttribute('required', 'required');
                    planSelect.removeAttribute('required');
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
        });
    </script>
@endsection