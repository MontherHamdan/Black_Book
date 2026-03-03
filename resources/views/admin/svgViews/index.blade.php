@extends('admin.layout')

@section('content')
    <style>
        /* ستايلات الـ Modal الفخم */
        .modal-creative .modal-content {
            border-radius: 24px;
            border: none;
            overflow: hidden;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
        }

        .modal-creative .modal-header {
            border-bottom: none;
            padding: 30px 30px 10px;
            display: flex;
            justify-content: flex-end;
        }

        .modal-creative .btn-close {
            background-color: #f1f3f9;
            border-radius: 50%;
            padding: 12px;
            opacity: 0.7;
            transition: all 0.3s;
        }

        .modal-creative .btn-close:hover {
            background-color: #e3e6f0;
            opacity: 1;
        }

        .modal-creative .modal-body {
            padding: 0 40px 30px;
            text-align: center;
        }

        .modal-creative .icon-box {
            width: 90px;
            height: 90px;
            background: rgba(231, 74, 59, 0.1);
            color: #e74a3b;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 45px;
            margin: 0 auto 25px;
            box-shadow: 0 0 0 10px rgba(231, 74, 59, 0.05);
            animation: pulse-danger 2s infinite;
        }

        @keyframes pulse-danger {
            0% {
                box-shadow: 0 0 0 0 rgba(231, 74, 59, 0.2);
            }

            70% {
                box-shadow: 0 0 0 15px rgba(231, 74, 59, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(231, 74, 59, 0);
            }
        }

        .modal-creative .modal-title-custom {
            font-weight: 800;
            color: #3a3b45;
            margin-bottom: 10px;
            font-size: 1.5rem;
        }

        .modal-creative .modal-desc {
            color: #858796;
            font-size: 1.1rem;
            margin-bottom: 30px;
        }

        .modal-creative .svg-name-highlight {
            background: #f8f9fc;
            padding: 10px 20px;
            border-radius: 12px;
            display: inline-block;
            font-weight: 700;
            color: #4e73df;
            border: 1px dashed #d1d3e2;
            margin-top: 10px;
        }

        .modal-creative .modal-footer {
            border-top: none;
            padding: 0 40px 40px;
            display: flex;
            gap: 15px;
            justify-content: center;
        }

        .modal-creative .btn-custom-cancel {
            background: #f8f9fc;
            color: #5a5c69;
            border: 1px solid #e3e6f0;
            border-radius: 14px;
            padding: 12px 25px;
            font-weight: 600;
            transition: all 0.3s;
            flex: 1;
        }

        .modal-creative .btn-custom-cancel:hover {
            background: #eaedf4;
        }

        .modal-creative .btn-custom-delete {
            background: #e74a3b;
            color: #fff;
            border: none;
            border-radius: 14px;
            padding: 12px 25px;
            font-weight: 600;
            box-shadow: 0 5px 15px rgba(231, 74, 59, 0.3);
            transition: all 0.3s;
            flex: 1;
        }

        .modal-creative .btn-custom-delete:hover {
            background: #be2617;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(231, 74, 59, 0.4);
        }
    </style>

    <div class="row">
        <div class="col-12">

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert"
                    style="border-radius: 10px;">
                    <i class="fas fa-check-circle me-2"></i><strong>نجاح!</strong> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert" style="border-radius: 10px;">
                    <i class="fas fa-exclamation-triangle me-2"></i><strong>تنبيه!</strong> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card shadow-sm border-0">
                <div class="d-flex justify-content-between align-items-center p-3">
                    <h1 class="mb-0 text-primary">SVGs</h1>
                    <a href="{{ route('svgs.create') }}" class="btn btn-success">
                        <i class="fas fa-plus me-1"></i> Add New SVG
                    </a>
                </div>

                <div class="card-body">
                    <table id="responsive-datatable" class="table table-striped table-bordered dt-responsive ">
                        <thead>
                            <tr>
                                <th class="text-center">ID</th>
                                <th class="text-center">Title</th>
                                <th class="text-center">Category</th>
                                <th class="text-center">Preview</th>
                                <th class="text-center">Copy Code</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($svgs as $svg)
                                <tr>
                                    <td class="text-center align-middle">{{ $svg->id }}</td>
                                    <td class="text-center align-middle">{{ $svg->title ?? 'No Title' }}</td>

                                    <td class="text-center align-middle">
                                        <span class="badge bg-info text-dark rounded-pill px-3 py-2">
                                            {{ $svg->category->name ?? 'بدون قسم' }}
                                        </span>
                                    </td>

                                    <td class="text-center align-middle">
                                        <div
                                            class="svg-preview-container d-flex justify-content-center align-items-center h-100">
                                            <div class="svg-preview img-thumbnail d-flex justify-content-center align-items-center overflow-hidden"
                                                style="width: 70px; height: 70px;">
                                                {!! $svg->svg_code !!}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <button class="btn btn-outline-primary btn-sm copy-svg-button rounded-pill px-3">
                                            <i class="fas fa-copy me-1"></i> Copy
                                        </button>
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="{{ route('svgs.edit', $svg->id) }}"
                                                class="btn btn-sm btn-outline-warning rounded-circle" title="Edit SVG"
                                                style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <button type="button" class="btn btn-sm btn-outline-danger rounded-circle"
                                                title="Delete SVG" data-bs-toggle="modal"
                                                data-bs-target="#deleteModal{{ $svg->id }}"
                                                style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <div class="modal fade modal-creative" id="deleteModal{{ $svg->id }}" tabindex="-1"
                                    aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="icon-box">
                                                    <i class="fas fa-trash-alt"></i>
                                                </div>
                                                <h3 class="modal-title-custom">تأكيد الحذف</h3>
                                                <p class="modal-desc">أنت على وشك حذف هذه العبارة نهائياً من النظام. لا يمكن
                                                    التراجع عن هذا الإجراء.</p>

                                                <div class="svg-name-highlight">
                                                    SVG ID: #{{ $svg->id }}
                                                    @if($svg->title)
                                                        <br> <span class="text-dark">{{ $svg->title }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-custom-cancel"
                                                    data-bs-dismiss="modal">إلغاء الأمر</button>
                                                <form action="{{ route('svgs.destroy', $svg->id) }}" method="POST"
                                                    class="d-inline flex-grow-1">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-custom-delete w-100">نعم، احذف
                                                        نهائياً</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">No SVGs found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const copyButtons = document.querySelectorAll('.copy-svg-button');
            const toastContainer = document.createElement('div');
            toastContainer.id = 'toast-container';
            toastContainer.style.position = 'fixed';
            toastContainer.style.bottom = '20px';
            toastContainer.style.right = '20px';
            toastContainer.style.zIndex = '9999';
            document.body.appendChild(toastContainer);

            copyButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const svgPreviewDiv = this.closest('tr').querySelector('.svg-preview');
                    const svgCode = svgPreviewDiv.innerHTML.trim();

                    navigator.clipboard.writeText(svgCode)
                        .then(() => { showToast('تم نسخ الكود بنجاح!', 'success'); })
                        .catch(err => { showToast('فشل النسخ، يرجى المحاولة.', 'error'); });
                });
            });

            function showToast(message, type = 'success') {
                const toast = document.createElement('div');
                toast.textContent = message;
                toast.style.padding = '12px 25px';
                toast.style.marginTop = '10px';
                toast.style.borderRadius = '8px';
                toast.style.color = '#fff';
                toast.style.fontWeight = 'bold';
                toast.style.boxShadow = '0 5px 15px rgba(0,0,0,0.1)';
                toast.style.opacity = '0';
                toast.style.transition = 'all 0.3s ease';
                toast.style.backgroundColor = type === 'success' ? '#1cc88a' : '#e74a3b';

                toastContainer.appendChild(toast);
                setTimeout(() => { toast.style.opacity = '1'; toast.style.transform = 'translateY(-10px)'; }, 100);
                setTimeout(() => { toast.style.opacity = '0'; toast.style.transform = 'translateY(0)'; setTimeout(() => toast.remove(), 300); }, 3000);
            }
        });
    </script>
@endsection