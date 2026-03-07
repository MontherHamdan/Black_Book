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

                        {{-- ═══ Base Info ═══ --}}
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <label class="form-label fw-bold" for="discount_code">Discount Code</label>
                                <input type="text" name="discount_code" id="discount_code" class="form-control"
                                    value="{{ old('discount_code') }}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold" for="code_name">Code Name</label>
                                <input type="text" name="code_name" id="code_name" class="form-control"
                                    value="{{ old('code_name') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold" for="discount_value">Base Discount Value</label>
                                <input type="number" step="0.01" name="discount_value" id="discount_value"
                                    class="form-control" value="{{ old('discount_value') }}" min="0" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold" for="discount_type">Discount Type</label>
                                <select name="discount_type" id="discount_type" class="form-select" required>
                                    <option value="" disabled selected>Select Type</option>
                                    <option value="percentage" {{ old('discount_type') == 'percentage' ? 'selected' : '' }}>
                                        Percentage (%)</option>
                                    <option value="byJd" {{ old('discount_type') == 'byJd' ? 'selected' : '' }}>By JOD
                                    </option>
                                </select>
                            </div>
                        </div>

                        {{-- ═══ Tiers Section ═══ --}}
                        <div class="card border rounded-4 mb-4">
                            <div
                                class="card-header bg-light d-flex justify-content-between align-items-center rounded-top-4">
                                <h5 class="mb-0"><i class="fas fa-layer-group me-2 text-primary"></i>Tiered Discounts
                                    (Optional)</h5>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="addTierBtn">
                                    <i class="fas fa-plus me-1"></i>Add Tier
                                </button>
                            </div>
                            <div class="card-body p-3">
                                <p class="text-muted small mb-3">Tiers apply escalating discounts when the number of orders
                                    using this code reaches the specified quantity. Base discount applies for quantities
                                    below the first tier.</p>
                                <div id="tiersContainer">
                                    {{-- Dynamic tier rows inserted here --}}
                                </div>
                                <div id="noTiersMsg" class="text-center text-muted py-3">
                                    <i class="fas fa-info-circle me-1"></i>No tiers added. Base discount will always apply.
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
    </script>
@endsection