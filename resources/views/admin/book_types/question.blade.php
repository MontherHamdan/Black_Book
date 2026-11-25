@extends('admin.layout')

@section('content')
<div class="row justify-content-center" dir="rtl">
    <div class="col-12 col-xl-8">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <div>
                <h3 class="mb-1">إعداد سؤال العميل</h3>
                <p class="text-muted mb-0">خصص السؤال الذي يظهر عند اختيار هذا المنتج.</p>
            </div>
            <a href="{{ route('book-types.index') }}" class="btn btn-outline-secondary">العودة للمنتجات</a>
        </div>
        @if(session('success'))
            <div class="alert alert-success" role="status">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger" role="alert">يرجى مراجعة الإعدادات. لا يمكن تفعيل السؤال دون كتابة نصه.</div>
        @endif
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white p-4 d-flex align-items-center gap-3">
                <img src="{{ $bookType->image }}" alt="" width="64" height="64" style="object-fit:cover;border-radius:12px;">
                <div>
                    <h4 class="mb-1">{{ $bookType->name_ar }}</h4>
                    <span class="badge {{ $bookType->question_enabled ? 'bg-success' : 'bg-secondary' }}">{{ $bookType->question_enabled ? 'السؤال مفعّل' : 'السؤال متوقف' }}</span>
                </div>
            </div>
            <form method="POST" action="{{ route('book-types.question.update', $bookType) }}" class="card-body p-4">
                @csrf
                @method('PUT')
                @include('admin.book_types.question-fields')
                <div class="d-flex flex-wrap gap-2">
                    <button type="submit" class="btn btn-primary px-4">حفظ إعدادات السؤال</button>
                    <a href="{{ route('book-types.index') }}" class="btn btn-light">إلغاء</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
