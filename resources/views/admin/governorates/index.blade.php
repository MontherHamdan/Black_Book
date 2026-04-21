@extends('admin.layout')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        /* ستايلات الـ Modal والـ Switch (نفس ستايل الدول للتناسق) */
        .modal-creative .modal-content { border-radius: 24px; border: none; overflow: hidden; box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15); }
        .modal-creative .modal-header { border-bottom: none; padding: 30px 30px 10px; display: flex; justify-content: flex-end; }
        .modal-creative .btn-close { background-color: #f1f3f9; border-radius: 50%; padding: 12px; opacity: 0.7; transition: all 0.3s; }
        .modal-creative .btn-close:hover { background-color: #e3e6f0; opacity: 1; }
        .modal-creative .modal-body { padding: 0 40px 30px; text-align: center; }
        .modal-creative .icon-box { width: 90px; height: 90px; background: rgba(231, 74, 59, 0.1); color: #e74a3b; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 45px; margin: 0 auto 25px; box-shadow: 0 0 0 10px rgba(231, 74, 59, 0.05); animation: pulse-danger 2s infinite; }
        @keyframes pulse-danger { 0% { box-shadow: 0 0 0 0 rgba(231, 74, 59, 0.2); } 70% { box-shadow: 0 0 0 15px rgba(231, 74, 59, 0); } 100% { box-shadow: 0 0 0 0 rgba(231, 74, 59, 0); } }
        .modal-creative .modal-title-custom { font-weight: 800; color: #3a3b45; margin-bottom: 10px; font-size: 1.5rem; }
        .modal-creative .modal-desc { color: #858796; font-size: 1.1rem; margin-bottom: 30px; }
        .modal-creative .country-highlight { background: #f8f9fc; padding: 15px 20px; border-radius: 12px; display: inline-block; font-weight: 700; color: #4e73df; border: 1px dashed #d1d3e2; margin-top: 10px; }
        .modal-creative .modal-footer { border-top: none; padding: 0 40px 40px; display: flex; gap: 15px; justify-content: center; }
        .modal-creative .btn-custom-cancel { background: #f8f9fc; color: #5a5c69; border: 1px solid #e3e6f0; border-radius: 14px; padding: 12px 25px; font-weight: 600; transition: all 0.3s; flex: 1; }
        .modal-creative .btn-custom-cancel:hover { background: #eaedf4; }
        .modal-creative .btn-custom-delete { background: #e74a3b; color: #fff; border: none; border-radius: 14px; padding: 12px 25px; font-weight: 600; box-shadow: 0 5px 15px rgba(231, 74, 59, 0.3); transition: all 0.3s; flex: 1; }
        .modal-creative .btn-custom-delete:hover { background: #be2617; transform: translateY(-2px); box-shadow: 0 8px 20px rgba(231, 74, 59, 0.4); }
        .form-switch .form-check-input:checked { background-color: #10b981; border-color: #10b981; }
    </style>

    <div class="row">
        <div class="col-12">
            {{-- Alerts --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert" style="border-radius: 10px;">
                    <i class="fas fa-check-circle me-2"></i><strong>نجاح!</strong> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card shadow-sm border-0 mt-3">
                {{-- <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                    <h1 class="mb-0 text-primary"><i class="fas fa-map-marked-alt me-2"></i> إدارة المحافظات</h1>
                    <a href="{{ route('governorates.create') }}" class="btn btn-sm btn-success px-4 py-2 rounded-pill shadow-sm">
                        <i class="fas fa-plus me-1"></i> إضافة محافظة
                    </a>
                </div> --}}
<form action="{{ route('locations.sync') }}" method="POST" class="d-inline" id="sync-locations-form">
    @csrf
    <button type="button" id="sync-btn" class="btn btn-sm btn-info px-4 py-2 rounded-pill shadow-sm text-white">
        <i class="fas fa-sync-alt me-1"></i> مزامنة مع LogesTechs
    </button>
</form>
                <div class="card-body">
                    <table id="responsive-datatable" class="table table-hover table-bordered dt-responsive align-middle text-center">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">#</th>
                                {{-- <th>الدولة التابعة لها</th> --}}
                                <th>اسم المحافظة (EN)</th>
                                <th>اسم المحافظة (AR)</th>
                                <th width="12%">حالة التفعيل</th>
                                {{-- <th width="15%">إجراءات</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($governorates as $governorate)
                                <tr>
                                    <td class="fw-bold">{{ $loop->iteration }}</td>
                                    
                                    {{-- <td class="fw-bold text-primary">
                                        @if($governorate->country)
                                            @if($governorate->country->flag_image)
                                                <img src="{{ asset($governorate->country->flag_image) }}" alt="flag" width="24" class="rounded me-1 border">
                                            @endif
                                            {{ $governorate->country->name }}
                                        @else
                                            <span class="text-muted">غير محددة</span>
                                        @endif
                                    </td> --}}

                                    <td class="text-dark">{{ $governorate->name_en }}</td>
                                    <td class="fw-bold text-dark">{{ $governorate->name_ar }}</td>

                                    {{-- 🚀 عامود التفعيل 🚀 --}}
                                    <td>
                                        <div class="form-check form-switch d-flex justify-content-center m-0 p-0">
                                            <input class="form-check-input toggle-active-switch shadow-sm" type="checkbox" role="switch"
                                                data-id="{{ $governorate->id }}"
                                                {{ $governorate->is_active ? 'checked' : '' }}
                                                style="cursor: pointer; transform: scale(1.3); margin-left: 0;">
                                        </div>
                                    </td>

                                    {{-- <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="{{ route('governorates.edit', $governorate->id) }}" class="btn btn-sm btn-outline-warning rounded-pill px-3">
                                                <i class="fas fa-edit me-1"></i> تعديل
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3"
                                                data-bs-toggle="modal" data-bs-target="#deleteGovModal{{ $governorate->id }}">
                                                <i class="fas fa-trash me-1"></i> حذف
                                            </button>
                                        </div>
                                    </td> --}}
                                </tr>

                                <div class="modal fade modal-creative" id="deleteGovModal{{ $governorate->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="icon-box"><i class="fas fa-trash-alt"></i></div>
                                                <h3 class="modal-title-custom">تأكيد الحذف</h3>
                                                <p class="modal-desc">أنت على وشك حذف محافظة <strong>{{ $governorate->name_ar }}</strong> نهائياً من النظام.</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-custom-cancel" data-bs-dismiss="modal">إلغاء الأمر</button>
                                                <form action="{{ route('governorates.destroy', $governorate->id) }}" method="POST" class="d-inline flex-grow-1">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-custom-delete w-100">نعم، احذف نهائياً</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-5">
                                        <i class="fas fa-map fs-1 text-light mb-3 d-block"></i>
                                        <h5>لا توجد محافظات مضافة حتى الآن</h5>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- 🚀 سكربت الـ AJAX لتفعيل وتعطيل المحافظة 🚀 --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggleSwitches = document.querySelectorAll('.toggle-active-switch');

            toggleSwitches.forEach(switchEl => {
                switchEl.addEventListener('change', function () {
                    const govId = this.getAttribute('data-id');
                    const isChecked = this.checked;
                    const originalState = !isChecked; 

                    this.disabled = true;

                    fetch(`/governorates/${govId}/toggle-active`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        this.disabled = false;
                        if (data.success) {
                            Swal.fire({
                                toast: true, position: 'top-end', icon: 'success',
                                title: isChecked ? 'تم تفعيل المحافظة بنجاح' : 'تم تعطيل المحافظة بنجاح',
                                showConfirmButton: false, timer: 3000, timerProgressBar: true
                            });
                        } else {
                            this.checked = originalState;
                            Swal.fire('خطأ', data.message || 'حدث خطأ أثناء تغيير الحالة.', 'error');
                        }
                    })
                    .catch(error => {
                        this.disabled = false;
                        this.checked = originalState;
                        console.error('Error:', error);
                        Swal.fire('خطأ اتصال', 'يرجى التحقق من اتصال الإنترنت والمحاولة مجدداً.', 'error');
                    });
                });
            });
        });
    </script>
    {{-- 🚀 سكربت الـ AJAX والـ SweetAlert 🚀 --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            
            // ---------------------------------------------------------
            // 1. كود تفعيل وتعطيل المحافظة (الـ Switch)
            // ---------------------------------------------------------
            const toggleSwitches = document.querySelectorAll('.toggle-active-switch');

            toggleSwitches.forEach(switchEl => {
                switchEl.addEventListener('change', function () {
                    const govId = this.getAttribute('data-id');
                    const isChecked = this.checked;
                    const originalState = !isChecked; 

                    this.disabled = true;

                    fetch(`/governorates/${govId}/toggle-active`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        this.disabled = false;
                        if (data.success) {
                            Swal.fire({
                                toast: true, position: 'top-end', icon: 'success',
                                title: isChecked ? 'تم تفعيل المحافظة بنجاح' : 'تم تعطيل المحافظة بنجاح',
                                showConfirmButton: false, timer: 3000, timerProgressBar: true
                            });
                        } else {
                            this.checked = originalState;
                            Swal.fire('خطأ', data.message || 'حدث خطأ أثناء تغيير الحالة.', 'error');
                        }
                    })
                    .catch(error => {
                        this.disabled = false;
                        this.checked = originalState;
                        console.error('Error:', error);
                        Swal.fire('خطأ اتصال', 'يرجى التحقق من اتصال الإنترنت والمحاولة مجدداً.', 'error');
                    });
                });
            });

            // ---------------------------------------------------------
            // 2. كود الـ SweetAlert لزر مزامنة المناطق
            // ---------------------------------------------------------
            const syncBtn = document.getElementById('sync-btn');
            const syncForm = document.getElementById('sync-locations-form');

            if (syncBtn && syncForm) {
                syncBtn.addEventListener('click', function(e) {
                    e.preventDefault(); // نمنع إرسال الفورم العادي

                    Swal.fire({
                        title: 'تأكيد المزامنة',
                        text: "هل أنت متأكد أنك تريد جلب أحدث المناطق من شركة التوصيل؟ قد تستغرق العملية بضع ثوانٍ.",
                        icon: 'info',
                        showCancelButton: true,
                        confirmButtonColor: '#36b9cc', // لون أزرق فاتح يناسب زر الـ info
                        cancelButtonColor: '#858796', // لون رمادي للإلغاء
                        confirmButtonText: 'نعم، ابدأ المزامنة',
                        cancelButtonText: 'إلغاء'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // إذا وافق، نظهر رسالة تحميل (Loading) حتى لا يضغط مرة أخرى
                            Swal.fire({
                                title: 'جاري المزامنة...',
                                html: 'يرجى الانتظار، جلب البيانات قيد التنفيذ.',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                            
                            // نرسل الفورم
                            syncForm.submit();
                        }
                    });
                });
            }

        });
    </script>
@endsection