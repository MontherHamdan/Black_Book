@extends('admin.layout')

@section('content')
    <div class="container-fluid mt-4" style="direction: rtl; text-align: right; font-family: 'Cairo', sans-serif;">

        <div class="row border-bottom pb-3 mb-4 align-items-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center gap-4">
                    <div class="header-icon-box bg-gradient-primary text-white shadow-sm">
                        <i class="fas fa-chart-line fa-2x"></i>
                    </div>
                    <div>
                        <h2 class="fw-bolder text-dark mb-1" style="letter-spacing: -0.5px;">الإدارة المالية</h2>
                        <p class="text-muted mb-0 fs-6">متابعة أرصدة المصممين وتسوية المستحقات المالية بدقة وسهولة</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-start d-none d-md-block">
                <div class="badge bg-soft-info text-info p-2 rounded-pill px-3 fw-bold fs-6 border border-info">
                    <i class="fas fa-users me-1"></i> إجمالي المصممين: {{ $designers->count() }}
                </div>
            </div>
        </div>

        <div class="row g-4">
            @forelse($designers as $designer)
                <div class="col-xl-4 col-lg-6 col-md-6">
                    <div class="card designer-card h-100 border-0 rounded-4 overflow-hidden">
                        <div class="card-top-bar bg-gradient-primary"></div>

                        <div class="card-body p-4 position-relative z-1">
                            <div class="d-flex align-items-center mb-4">
                                <div class="position-relative">
                                    @if($designer->image)
                                        <img src="{{ asset('storage/' . $designer->image) }}" class="rounded-circle shadow-sm"
                                            style="width: 70px; height: 70px; object-fit: cover; border: 3px solid #fff;"
                                            alt="{{ $designer->name }}">
                                    @else
                                        <div class="avatar-placeholder rounded-circle shadow-sm">
                                            {{ mb_substr($designer->name, 0, 1) }}
                                        </div>
                                    @endif
                                    <span class="status-dot bg-success border border-white border-2 rounded-circle"></span>
                                </div>

                                <div class="me-3">
                                    <h5 class="fw-bold mb-1 text-dark">{{ $designer->name }}</h5>
                                    <span class="badge bg-soft-primary text-primary rounded-pill px-3 py-1 fw-semibold">
                                        {{ $designer->title ?? 'مصمم محترف' }}
                                    </span>
                                </div>
                            </div>

                            <div class="finance-stats-box p-3 rounded-4 mb-4">
                                <div class="row g-0">
                                    <div class="col-6 text-center border-start border-light-subtle">
                                        <p class="text-muted small mb-1 fw-semibold">المستحق حالياً</p>
                                        <h4
                                            class="fw-bolder text-danger mb-0 d-flex align-items-center justify-content-center gap-1">
                                            <i class="fas fa-exclamation-circle fs-6 text-danger opacity-75"></i>
                                            {{ number_format($designer->unpaid_commission ?? 0, 2) }}
                                        </h4>
                                    </div>
                                    <div class="col-6 text-center">
                                        <p class="text-muted small mb-1 fw-semibold">إجمالي المدفوع</p>
                                        <h4
                                            class="fw-bolder text-success mb-0 d-flex align-items-center justify-content-center gap-1">
                                            <i class="fas fa-check-circle fs-6 text-success opacity-75"></i>
                                            {{ number_format($designer->paid_commission ?? 0, 2) }}
                                        </h4>
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid mt-auto">
                                <a href="{{ route('designer-accounting.show', $designer->id) }}"
                                    class="btn btn-action py-2 rounded-pill fw-bold d-flex align-items-center justify-content-center gap-2">
                                    <i class="fas fa-wallet fs-5"></i>
                                    <span>إدارة المحفظة المالية</span>
                                </a>
                            </div>
                        </div>

                        <i class="fas fa-coins card-bg-icon"></i>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="empty-state text-center py-5 bg-white rounded-4 shadow-sm border border-dashed">
                        <div class="empty-icon-wrap mb-4 mx-auto">
                            <i class="fas fa-wallet text-muted fa-3x"></i>
                        </div>
                        <h4 class="fw-bold text-dark mb-2">لا يوجد مصممين حالياً</h4>
                        <p class="text-muted">لم يتم إضافة أي مصممين إلى النظام بعد للبدء في تتبع أرصدتهم.</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <style>
        .bg-gradient-primary {
            background: linear-gradient(135deg, #727cf5 0%, #4a54c1 100%);
        }

        .bg-soft-primary {
            background-color: rgba(114, 124, 245, 0.12);
        }

        .text-primary {
            color: #727cf5 !important;
        }

        .bg-soft-info {
            background-color: rgba(57, 175, 209, 0.12);
        }

        .text-info {
            color: #39afd1 !important;
        }

        .border-info {
            border-color: rgba(57, 175, 209, 0.3) !important;
        }

        .header-icon-box {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            transform: rotate(-5deg);
            transition: transform 0.3s ease;
        }

        .header-icon-box:hover {
            transform: rotate(0deg);
        }

        .designer-card {
            background: #fff;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.04);
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        .designer-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(114, 124, 245, 0.15);
        }

        .card-top-bar {
            height: 6px;
            width: 100%;
            position: absolute;
            top: 0;
            left: 0;
            z-index: 2;
        }

        .avatar-placeholder {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #f1f3fa 0%, #e3e6f0 100%);
            color: #727cf5;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            font-weight: 800;
            border: 3px solid #fff;
        }

        .status-dot {
            width: 16px;
            height: 16px;
            position: absolute;
            bottom: 2px;
            right: 2px;
            z-index: 3;
        }

        .finance-stats-box {
            background-color: #f8f9fa;
            border: 1px solid #edf2f9;
        }

        .btn-action {
            background-color: #fff;
            color: #727cf5;
            border: 2px solid #727cf5;
            transition: all 0.2s ease;
        }

        .btn-action:hover {
            background-color: #727cf5;
            color: #fff;
            box-shadow: 0 8px 15px rgba(114, 124, 245, 0.3);
        }

        .card-bg-icon {
            position: absolute;
            bottom: -20px;
            left: -20px;
            font-size: 120px;
            color: rgba(114, 124, 245, 0.03);
            transform: rotate(-15deg);
            z-index: 0;
            pointer-events: none;
        }

        .empty-icon-wrap {
            width: 90px;
            height: 90px;
            background-color: #f8f9fa;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
@endsection