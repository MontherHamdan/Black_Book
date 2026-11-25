@if ($order->product_question)
<div class="card order-card mb-4" dir="rtl">
    <div class="card-header"><strong>تخصيص المنتج</strong></div>
    <div class="card-body">
        <p class="fw-bold" style="white-space:pre-wrap">{{ $order->product_question }}</p>
        <p style="white-space:pre-wrap;overflow-wrap:anywhere">{{ $order->product_answer ?? 'لم يضف العميل إجابة (اختياري)' }}</p>
    </div>
</div>
@endif
