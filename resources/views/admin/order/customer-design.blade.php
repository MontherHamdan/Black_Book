@if ($upload)
    <div class="border rounded p-3 my-3 text-center" style="background:#fff8f8;" dir="rtl">
        <strong class="d-block mb-2">{{ $label }} — تصميم رفعه العميل</strong>
        @if (str_starts_with($upload->mime_type, 'image/'))
            <a href="{{ route('orders.customerDesign', [$order, $upload->purpose, 'preview' => 1]) }}" target="_blank" rel="noopener">
                <img src="{{ route('orders.customerDesign', [$order, $upload->purpose, 'preview' => 1]) }}"
                    alt="{{ $label }}" style="max-width:100%;max-height:280px;object-fit:contain;" loading="lazy">
            </a>
        @else
            <div class="my-3"><i class="fas fa-file-pdf fa-3x text-danger" aria-hidden="true"></i> PDF</div>
        @endif
        <p class="mt-2 mb-1 text-break">{{ $upload->original_name }}</p>
        <small class="text-muted">{{ number_format($upload->size / 1024 / 1024, 2) }} MB</small>
        <div class="d-flex flex-wrap justify-content-center gap-2 mt-3">
            <a class="btn btn-outline-secondary" target="_blank" rel="noopener"
                href="{{ route('orders.customerDesign', [$order, $upload->purpose, 'preview' => 1]) }}">معاينة التصميم</a>
            <a class="btn btn-danger" href="{{ route('orders.customerDesign', [$order, $upload->purpose]) }}">
                <i class="fas fa-download" aria-hidden="true"></i> تنزيل الملف الأصلي
            </a>
        </div>
    </div>
@endif
