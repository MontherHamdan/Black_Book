<fieldset class="border rounded p-4 mb-4" dir="rtl" id="product-question-settings">
    <legend class="float-none w-auto px-2 fs-5">سؤال للعميل عن المنتج</legend>
    <input type="hidden" name="question_enabled" value="0">
    <div class="form-check form-switch mb-3">
        <input class="form-check-input" role="switch" type="checkbox" id="question-enabled" name="question_enabled" value="1" @checked(old('question_enabled', $bookType->question_enabled ?? false))>
        <label class="form-check-label fw-bold" for="question-enabled">إظهار السؤال للعميل</label>
    </div>
    <p class="text-muted small">أوقف المفتاح لإخفاء السؤال. يظل نصه محفوظاً لتفعيله لاحقاً، والطلبات السابقة لا تتغيّر.</p>
    <label for="question_text" class="form-label fw-bold">نص السؤال</label>
    <textarea id="question_text" name="question_text" maxlength="500" rows="3" class="form-control @error('question_text') is-invalid @enderror" placeholder="شو الاسم أو العبارة المطلوبة على الستاند؟">{{ old('question_text', $bookType->question_text ?? '') }}</textarea>
    @error('question_text')<p class="text-danger">{{ $message }}</p>@enderror
    <label for="question-required" class="form-label fw-bold mt-3">نوع الإجابة</label>
    <select id="question-required" name="question_required" class="form-select">
        <option value="0" @selected(!old('question_required', $bookType->question_required ?? false))>اختيارية — يمكن إكمال الطلب بدون إجابة</option>
        <option value="1" @selected(old('question_required', $bookType->question_required ?? false))>إجبارية — يجب الإجابة قبل إكمال الطلب</option>
    </select>
    <div class="bg-light border rounded p-3 mt-4" aria-live="polite">
        <span class="text-muted small d-block mb-2">معاينة ما يظهر للعميل</span>
        <p id="question-disabled-preview" class="mb-0">السؤال متوقف ولن يظهر للعميل.</p>
        <div id="question-preview">
            <p class="fw-bold mb-2" id="question-preview-label" style="white-space:pre-wrap;overflow-wrap:anywhere"></p>
            <div class="bg-white border rounded p-3 text-muted">اكتب إجابتك هنا...</div>
        </div>
    </div>
</fieldset>
<script>
(() => {
    const enabled = document.getElementById('question-enabled');
    const text = document.getElementById('question_text');
    const required = document.getElementById('question-required');
    const render = () => {
        text.required = enabled.checked;
        document.getElementById('question-preview').hidden = !enabled.checked;
        document.getElementById('question-disabled-preview').hidden = enabled.checked;
        document.getElementById('question-preview-label').textContent = (text.value.trim() || 'اكتب نص السؤال لعرض المعاينة') + (required.value === '1' ? ' (إجباري)' : ' (اختياري)');
    };
    enabled.addEventListener('change', render);
    text.addEventListener('input', render);
    required.addEventListener('change', render);
    render();
})();
</script>
