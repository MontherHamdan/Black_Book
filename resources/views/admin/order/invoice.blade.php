<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فاتورة طلب #{{ $order->id }} - Black Book</title>

    <!-- Google Fonts: Cairo -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary-color: #111827;
            --primary-accent: #0f172a;
            --secondary-color: #D1232A;
            --text-dark: #1f2937;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --bg-light: #f8fafc;
        }

        .text-primary {
            color: #D1232A !important;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Cairo', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        body {
            background-color: #f1f5f9;
            color: var(--text-dark);
            direction: rtl;
            text-align: right;
            padding: 14px 12px 24px;
            font-size: 12.5px;
            line-height: 1.35;
        }

        /* 🔹 Action Bar (Toolbar) */
        .invoice-actions-bar {
            max-width: 820px;
            margin: 0 auto 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            background: #ffffff;
            padding: 10px 18px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--border-color);
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 7px 18px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.88rem;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
            border: none;
        }

        .btn-download-pdf {
            background-color: #D1232A;
            color: #ffffff;
            box-shadow: 0 2px 8px rgba(209, 35, 42, 0.25);
        }

        .btn-download-pdf:hover {
            background-color: #b81d24;
            box-shadow: 0 4px 12px rgba(209, 35, 42, 0.35);
            transform: translateY(-1px);
        }

        .btn-download-image {
            background-color: #0f172a;
            color: #ffffff;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.2);
        }

        .btn-download-image:hover {
            background-color: #1e293b;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.3);
            transform: translateY(-1px);
        }

        .btn-print-sheet {
            background-color: #f8fafc;
            color: #334155;
            border: 1px solid #cbd5e1;
        }

        .btn-print-sheet:hover {
            background-color: #e2e8f0;
            color: #0f172a;
            transform: translateY(-1px);
        }

        .btn-back {
            background-color: #f1f5f9;
            color: #334155;
            border: 1px solid #cbd5e1;
        }

        .btn-back:hover {
            background-color: #e2e8f0;
        }

        /* 🔹 Main Invoice Sheet - Compact to strictly fit 1 A4 page */
        .invoice-sheet {
            max-width: 820px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 12px;
            padding: 18px 22px;
            box-shadow: 0 6px 20px rgba(15, 23, 42, 0.05);
            border: 1px solid var(--border-color);
            position: relative;
        }

        /* 🔹 Header */
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 10px;
            border-bottom: 2px solid #e2e8f0;
            margin-bottom: 10px;
        }

        .brand-section {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .brand-logo,
        .brand-info img {
            height: 42px;
            width: auto;
            max-width: 185px;
            object-fit: contain;
            display: block;
            margin-bottom: 3px;
        }

        .brand-info h2 {
            font-size: 1.3rem;
            font-weight: 900;
            color: #0f172a;
            margin-bottom: 1px;
            line-height: 1.15;
            letter-spacing: 0.5px;
        }

        .brand-info p {
            font-size: 0.75rem;
            color: var(--text-muted);
            font-weight: 600;
        }

        .invoice-meta {
            text-align: left;
            direction: rtl;
        }

        .invoice-title-wrapper {
            display: flex;
            align-items: baseline;
            justify-content: flex-start;
            gap: 6px;
            direction: rtl;
        }

        .invoice-title {
            font-size: 1.25rem;
            font-weight: 900;
            color: #0f172a;
            white-space: nowrap;
        }

        .invoice-order-num {
            font-size: 1.25rem;
            font-weight: 900;
            color: #D1232A;
            direction: ltr;
            display: inline-block;
        }

        .invoice-date {
            font-size: 0.78rem;
            color: var(--text-muted);
            font-weight: 600;
            margin-top: 2px;
            direction: ltr;
            text-align: left;
        }

        /* 🔹 Info Grid (Customer & Delivery) */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 10px;
        }

        .info-card {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 10px 14px;
        }

        .info-card-title {
            font-size: 0.88rem;
            font-weight: 800;
            color: #0f172a;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 5px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .info-card-title i {
            color: #D1232A;
            font-size: 0.85rem;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            font-size: 0.82rem;
            margin-bottom: 4px;
            line-height: 1.4;
        }

        .info-row:last-child {
            margin-bottom: 0;
        }

        .info-label {
            color: var(--text-muted);
            font-weight: 600;
            white-space: nowrap;
        }

        .info-val {
            color: #0f172a;
            font-weight: 700;
            text-align: left;
        }

        /* 🔹 Details Table */
        .table-section {
            margin-bottom: 12px;
        }

        .section-heading {
            font-size: 0.88rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .section-heading i {
            color: #D1232A;
            font-size: 0.85rem;
        }

        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.82rem;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #cbd5e1;
        }

        .invoice-table th {
            background-color: #0f172a;
            color: #ffffff;
            padding: 7px 12px;
            font-weight: 700;
            text-align: right;
            font-size: 0.82rem;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .invoice-table td {
            padding: 7px 12px;
            border-bottom: 1px solid #e2e8f0;
            color: #1e293b;
            vertical-align: middle;
            line-height: 1.4;
        }

        .invoice-table tr:last-child td {
            border-bottom: none;
        }

        .invoice-table tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .item-spec-name {
            font-weight: 700;
            color: #0f172a;
            white-space: nowrap;
        }

        .item-spec-val {
            font-weight: 600;
            color: #334155;
        }

        /* 🔹 Financial Summary & Terms Row */
        .financial-summary-wrap {
            display: flex;
            justify-content: space-between;
            align-items: stretch;
            gap: 14px;
            margin-bottom: 12px;
        }

        .terms-card {
            flex: 1;
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 10px 14px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .terms-title {
            font-size: 0.86rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .terms-title i {
            color: #D1232A;
            font-size: 0.85rem;
        }

        .terms-content {
            font-size: 0.77rem;
            color: #475569;
            line-height: 1.45;
        }

        .terms-content p {
            margin-bottom: 2px;
        }

        .terms-content p:last-child {
            margin-bottom: 0;
        }

        .financial-card {
            width: 290px;
            flex-shrink: 0;
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .financial-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 5px 12px;
            font-size: 0.82rem;
            border-bottom: 1px solid #e2e8f0;
        }

        .financial-row:last-child {
            border-bottom: none;
        }

        .financial-label {
            color: var(--text-muted);
            font-weight: 600;
        }

        .financial-val {
            font-weight: 700;
            color: #0f172a;
        }

        .financial-val.discount {
            color: #D1232A;
            font-weight: 800;
        }

        .financial-row.total-row {
            background-color: #0f172a !important;
            color: #ffffff !important;
            padding: 8px 12px;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .financial-row.total-row .financial-label {
            color: #ffffff !important;
            font-size: 0.88rem;
            font-weight: 800;
        }

        .financial-row.total-row .financial-val {
            color: #ff6b72 !important;
            font-size: 1.05rem;
            font-weight: 900;
        }

        /* 🔹 Signatures & Verification Bar */
        .invoice-signatures-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 9px 14px;
            background: #ffffff;
            border: 1px dashed #cbd5e1;
            border-radius: 8px;
            margin-bottom: 12px;
            font-size: 0.8rem;
        }

        .sig-block {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .sig-title {
            color: var(--text-muted);
            font-weight: 600;
            font-size: 0.78rem;
        }

        .sig-val {
            color: #0f172a;
            font-weight: 700;
            font-size: 0.8rem;
        }

        .sig-stamp-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 10px;
            border: 1.5px solid #D1232A;
            color: #D1232A;
            border-radius: 20px;
            font-weight: 800;
            font-size: 0.78rem;
            background: rgba(209, 35, 42, 0.04);
            transform: rotate(-1deg);
        }

        .sig-line {
            color: #94a3b8;
            letter-spacing: 1px;
            direction: ltr;
            font-size: 0.78rem;
        }

        /* 🔹 Footer Notes */
        .invoice-footer {
            border-top: 1.5px dashed #cbd5e1;
            padding-top: 8px;
            margin-top: auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.74rem;
            color: #64748b;
            line-height: 1.35;
        }

        .footer-note {
            max-width: 520px;
        }

        .footer-contact {
            text-align: left;
            direction: ltr;
            font-weight: 600;
            white-space: nowrap;
        }

        /* 🖨️ PRINT STYLES - Perfectly Proportioned to Fill Exactly 1 A4 Page */
        @media print {
            @page {
                size: A4 portrait;
                margin: 8mm 10mm;
            }

            html, body {
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
                height: 100% !important;
                max-height: 100% !important;
                overflow: hidden !important;
                font-size: 13px !important;
                line-height: 1.4 !important;
                color: #0f172a !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .no-print {
                display: none !important;
            }

            .invoice-sheet {
                max-width: 100% !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
                border: none !important;
                border-radius: 0 !important;
                background: #ffffff !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
                page-break-after: avoid !important;
                display: flex !important;
                flex-direction: column !important;
                justify-content: space-between !important;
                min-height: 252mm !important;
            }

            .invoice-header {
                padding-bottom: 10px !important;
                margin-bottom: 12px !important;
                border-bottom: 2px solid #cbd5e1 !important;
            }

            .brand-logo,
            .brand-info img {
                height: 48px !important;
                max-width: 190px !important;
            }

            .brand-info p {
                font-size: 0.78rem !important;
            }

            .invoice-title {
                font-size: 1.35rem !important;
            }

            .invoice-order-num {
                font-size: 1.35rem !important;
            }

            .invoice-date {
                font-size: 0.8rem !important;
            }

            .info-grid {
                gap: 12px !important;
                margin-bottom: 12px !important;
            }

            .info-card {
                padding: 9px 12px !important;
                border: 1px solid #cbd5e1 !important;
                border-radius: 8px !important;
                background: #f8fafc !important;
            }

            .info-card-title {
                font-size: 0.86rem !important;
                padding-bottom: 5px !important;
                margin-bottom: 6px !important;
                border-bottom: 1px solid #e2e8f0 !important;
            }

            .info-row {
                font-size: 0.82rem !important;
                margin-bottom: 3px !important;
                line-height: 1.35 !important;
            }

            .table-section {
                margin-bottom: 12px !important;
            }

            .section-heading {
                font-size: 0.88rem !important;
                margin-bottom: 6px !important;
            }

            .invoice-table {
                font-size: 0.82rem !important;
                border: 1px solid #cbd5e1 !important;
            }

            .invoice-table th {
                padding: 6px 10px !important;
                font-size: 0.82rem !important;
                background-color: #0f172a !important;
                color: #ffffff !important;
            }

            .invoice-table td {
                padding: 6px 10px !important;
                font-size: 0.8rem !important;
                border-bottom: 1px solid #e2e8f0 !important;
            }

            .financial-summary-wrap {
                gap: 12px !important;
                margin-bottom: 12px !important;
            }

            .terms-card {
                padding: 8px 12px !important;
                border: 1px solid #cbd5e1 !important;
            }

            .terms-title {
                font-size: 0.84rem !important;
                margin-bottom: 4px !important;
            }

            .terms-content {
                font-size: 0.76rem !important;
                line-height: 1.45 !important;
            }

            .financial-card {
                width: 280px !important;
                border: 1px solid #cbd5e1 !important;
            }

            .financial-row {
                padding: 4px 10px !important;
                font-size: 0.82rem !important;
            }

            .financial-row.total-row {
                padding: 7px 10px !important;
            }

            .financial-row.total-row .financial-label {
                font-size: 0.88rem !important;
            }

            .financial-row.total-row .financial-val {
                color: #ff6b72 !important;
                font-size: 1.05rem !important;
            }

            .invoice-signatures-bar {
                padding: 8px 12px !important;
                margin-bottom: 10px !important;
                font-size: 0.78rem !important;
                border: 1px dashed #cbd5e1 !important;
            }

            .invoice-footer {
                padding-top: 8px !important;
                margin-top: auto !important;
                border-top: 1px dashed #cbd5e1 !important;
                font-size: 0.74rem !important;
            }

            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>
</head>

<body>

    <!-- 🔹 Action Bar (Top Controls - Hidden during Print) -->
    <div class="invoice-actions-bar no-print">
        <div class="d-flex align-items-center gap-2">
            <span class="fw-bold" style="font-weight: 800; color: #0f172a;">
                <i class="fas fa-file-invoice me-1 text-primary"></i> فاتورة الطلب #{{ $order->id }}
            </span>
        </div>

        <div class="action-buttons">
            <button type="button" class="btn-action btn-download-pdf" id="btnDirectPdf" onclick="downloadDirectPdf();">
                <i class="fas fa-file-pdf"></i> تنزيل PDF مباشر
            </button>

            <button type="button" class="btn-action btn-download-image" id="btnDownloadImage" onclick="downloadImage();">
                <i class="fas fa-file-image"></i> تنزيل صورة (PNG)
            </button>

            <button type="button" class="btn-action btn-print-sheet" onclick="window.print();">
                <i class="fas fa-print"></i> طباعة
            </button>

            @auth
            <a href="{{ route('orders.show', $order->id) }}" class="btn-action btn-back">
                <i class="fas fa-arrow-left"></i> العودة للطلب
            </a>
            @else
            <button type="button" class="btn-action btn-back" onclick="window.history.length > 1 ? window.history.back() : window.close();">
                <i class="fas fa-times"></i> إغلاق النافذة
            </button>
            @endauth
        </div>
    </div>

    <!-- 🔹 Invoice Printable Sheet -->
    <div class="invoice-sheet" id="invoiceArea">

        <!-- 1️⃣ Header -->
        <div class="invoice-header">
            <div class="brand-section">
                <div class="brand-info">
                    <img src="{{ asset('assets/images/logo/logo.png') }}" onerror="this.src='{{ asset('assets/images/logo/logo.svg') }}';" alt="Black Book" class="brand-logo" height="42">
                    <p>المتجر المتخصص في دفاتر التخرج والذكريات الفاخرة</p>
                </div>
            </div>

            <div class="invoice-meta">
                <div class="invoice-title-wrapper">
                    <span class="invoice-title">فاتورة طلب</span>
                    <span class="invoice-order-num">#{{ $order->id }}</span>
                </div>
                <div class="invoice-date">
                    <span>{{ $order->created_at->format('Y-m-d') }}</span> |
                    <span>{{ $order->created_at->format('h:i A') }}</span>
                </div>
            </div>
        </div>

        <!-- 2️⃣ Info Grid -->
        <div class="info-grid">
            <!-- Customer Card -->
            <div class="info-card">
                <div class="info-card-title">
                    <i class="fas fa-user-graduate"></i> بيانات العميل والخريج
                </div>
                <div class="info-row">
                    <span class="info-label">اسم الخريج:</span>
                    <span class="info-val">{{ $order->username_ar ?: '-' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">رقم الهاتف:</span>
                    <span class="info-val" style="direction: ltr;">{{ $order->user_phone_number ?: '-' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">الصرح الأكاديمي:</span>
                    <span class="info-val">{{ $order->school_name ?: '-' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">التخصص:</span>
                    <span class="info-val">{{ $order->major_name ?: '-' }}</span>
                </div>
                @if($order->discountCode)
                <div class="info-row">
                    <span class="info-label">المجموعة / الكود:</span>
                    <span class="info-val">{{ $order->discountCode->code_name ?: $order->discountCode->discount_code }}</span>
                </div>
                @endif
            </div>

            <!-- Delivery Card -->
            <div class="info-card">
                <div class="info-card-title">
                    <i class="fas fa-truck"></i> تفاصيل الاستلام والتوصيل
                </div>
                <div class="info-row">
                    <span class="info-label">طريقة الاستلام:</span>
                    <span class="info-val">
                        @if($order->delivery_target === 'university')
                            استلام من الجامعة
                        @elseif($order->delivery_target === 'home')
                            توصيل للعنوان
                        @else
                            {{ $order->delivery_target ?: '-' }}
                        @endif
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">مكان التسليم:</span>
                    <span class="info-val">
                        @if($order->delivery_target === 'university')
                            {{ $order->deliveryUniversity->name ?? 'الجامعة المحددة' }}
                        @else
                            {{ implode(' - ', array_filter([$order->governorate->name_ar ?? null, $order->city->name_ar ?? null, $order->area->name_ar ?? null])) ?: 'عنوان العميل' }}
                        @endif
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">هاتف التوصيل 1:</span>
                    <span class="info-val" style="direction: ltr;">{{ $order->delivery_number_one ?: $order->user_phone_number ?: '-' }}</span>
                </div>
                @if($order->delivery_number_two)
                <div class="info-row">
                    <span class="info-label">هاتف التوصيل 2:</span>
                    <span class="info-val" style="direction: ltr;">{{ $order->delivery_number_two }}</span>
                </div>
                @endif
                @if($order->logestechs_order_id)
                <div class="info-row">
                    <span class="info-label">رقم بوليصة الشحن:</span>
                    <span class="info-val" style="direction: ltr;">{{ $order->logestechs_order_id }}</span>
                </div>
                @endif
            </div>
        </div>

        <!-- 3️⃣ Specifications Table -->
        <div class="table-section">
            <div class="section-heading">
                <i class="fas fa-book-open"></i> مواصفات وتفاصيل الدفتر
            </div>

            <table class="invoice-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th style="width: 32%;">البند / المواصفة</th>
                        <th style="width: 63%;">التفاصيل المختارة</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td class="item-spec-name">نوع الدفتر واللون</td>
                        <td class="item-spec-val">
                            <span style="font-weight: 700; color: #0f172a; display: inline-block; vertical-align: middle;">
                                <bdi dir="rtl">{{ $order->bookType->name_ar ?? '-' }}</bdi>
                            </span>
                            @if($order->bookColor)
                                <span style="display: inline-block; vertical-align: middle; background-color: #f1f5f9 !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; border: 1px solid #cbd5e1; padding: 2px 8px; border-radius: 12px; margin-right: 8px;">
                                    @if($order->bookColor->color_code)
                                        <span style="display: inline-block; width: 12px; height: 12px; border-radius: 50%; background-color: {{ $order->bookColor->color_code }} !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; border: 1px solid rgba(0,0,0,0.25); vertical-align: middle; margin-left: 4px;"></span>
                                    @endif
                                    <span style="display: inline-block; vertical-align: middle; font-weight: 700; color: #1e293b; font-size: 0.8rem;">اللون: {{ $order->bookColor->name_ar }}</span>
                                </span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td class="item-spec-name">التصميم المختار</td>
                        <td class="item-spec-val">{{ $order->bookDesign->name ?? 'تصميم مخصص' }}</td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td class="item-spec-name">عدد الصفحات</td>
                        <td class="item-spec-val">{{ $order->pages_number ?? '-' }} صفحة</td>
                    </tr>
                    <!-- <tr>
                        <td>4</td>
                        <td class="item-spec-name">نوع التجليد</td>
                        <td class="item-spec-val">{{ $order->is_sponge ? 'تجليد إسفنجي فاخر' : 'تجليد عادي' }}</td>
                    </tr> -->
                    <tr>
                        <td>4</td>
                        <td class="item-spec-name">الإضافات الخاصة</td>
                        <td class="item-spec-val">{{ $order->is_with_additives ? 'مع إضافات' : 'بدون إضافات' }}</td>
                    </tr>
                    @if($order->bookDecoration)
                    <tr>
                        <td>5</td>
                        <td class="item-spec-name">الزخرفة / الإكسسوار</td>
                        <td class="item-spec-val">{{ $order->bookDecoration->name }}</td>
                    </tr>
                    @endif
                    @if($order->svg_title)
                    <tr>
                        <td>6</td>
                        <td class="item-spec-name">عبارة الدفتر</td>
                        <td class="item-spec-val">{{ $order->svg_title }}</td>
                    </tr>
                    @endif
                    @if($order->gift_type && $order->gift_type !== 'none')
                    <tr>
                        <td>7</td>
                        <td class="item-spec-name">الهدية المرفقة</td>
                        <td class="item-spec-val">{{ $order->gift_title ?: $order->gift_type }}</td>
                    </tr>
                    @endif
                    @if($order->note)
                    <tr>
                        <td>8</td>
                        <td class="item-spec-name">ملاحظات الطلب</td>
                        <td class="item-spec-val">{{ $order->note }}</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- 4️⃣ Financial Summary & Quality Guarantee -->
        <div class="financial-summary-wrap">
            <!-- <div class="terms-card">
                <div class="terms-title">
                    <i class="fas fa-shield-alt"></i> شروط وضمان الاستلام
                </div>
                <div class="terms-content">
                    <p>• تضمن <strong>Black Book</strong> أعلى معايير الجودة للورق، دقة الألوان، والتجليد الإسفنجي الفاخر.</p>
                    <p>• يرجى معاينة ومطابقة مواصفات الدفتر، الاسم، والتخصص الأكاديمي فور الاستلام.</p>
                    <p>• في حال وجود أي استفسار، يرجى تزويد خدمة العملاء برقم الفاتورة أعلاه.</p>
                </div>
            </div> -->

            <div class="financial-card">
                @php
                    $originalPrice = (float) ($order->final_price ?? 0);
                    $discountedPrice = $order->final_price_with_discount !== null ? (float) $order->final_price_with_discount : $originalPrice;
                    $discountAmount = max(0, $originalPrice - $discountedPrice);
                @endphp

                <div class="financial-row">
                    <span class="financial-label">السعر الأساسي:</span>
                    <span class="financial-val">{{ number_format($originalPrice, 2) }} د.أ</span>
                </div>

                @if($discountAmount > 0)
                <div class="financial-row">
                    <span class="financial-label">الخصم ({{ $order->discountCode ? ($order->discountCode->code_name ?: $order->discountCode->discount_code) : 'كوبون' }}):</span>
                    <span class="financial-val discount">- {{ number_format($discountAmount, 2) }} د.أ</span>
                </div>
                @endif

                <div class="financial-row total-row">
                    <span class="financial-label">المجموع الصافي:</span>
                    <span class="financial-val">{{ number_format($discountedPrice, 2) }} د.أ</span>
                </div>
            </div>
        </div>

        <!-- 4️⃣.5 Signatures & Verification -->
        <div class="invoice-signatures-bar">
            <div class="sig-block">
                <span class="sig-title">تجهيز ومراجعة:</span>
                <span class="sig-val"><i class="fas fa-check-circle" style="color: #10b981;"></i> قسم الإنتاج والجودة</span>
            </div>
            <div class="sig-block text-center">
                <div class="sig-stamp-badge">
                    <i class="fas fa-certificate"></i> طلب معتمد ومسجل
                </div>
            </div>
            <div class="sig-block text-left">
                <span class="sig-title">توقيع المستلم:</span>
                <span class="sig-line">...................................................</span>
            </div>
        </div>

        <!-- 5️⃣ Footer -->
        <div class="invoice-footer">
            <div class="footer-note">
                <i class="fas fa-heart text-danger me-1"></i> شكراً لاختياركم <strong>Black Book</strong> لتخليد لحظاتكم الجامعية.<br>
                في حال وجود أي استفسار يرجى تزويدنا برقم الفاتورة أعلاه.
            </div>
            <div class="footer-contact">
                <div>Black Book Jordan</div>
                <div>Instagram: @black_book.jo</div>
            </div>
        </div>

    </div>

    <!-- 🔹 html2canvas & jsPDF for Direct Image & PDF Generation -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <!-- 🔹 Scripts for Direct PDF Download, Image Download, and Print -->
    <script>
        function downloadPdf() {
            window.print();
        }

        async function downloadDirectPdf() {
            const btn = document.getElementById('btnDirectPdf');
            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.style.opacity = '0.7';
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري تنزيل PDF...';

            try {
                if (document.fonts) {
                    await document.fonts.ready;
                }

                const invoiceArea = document.getElementById('invoiceArea');

                // Ensure all images are fully loaded
                const images = invoiceArea.querySelectorAll('img');
                await Promise.all(Array.from(images).map(img => {
                    if (img.complete) return Promise.resolve();
                    return new Promise(resolve => {
                        img.onload = resolve;
                        img.onerror = resolve;
                    });
                }));

                const canvas = await html2canvas(invoiceArea, {
                    scale: 2.5,
                    useCORS: true,
                    allowTaint: true,
                    backgroundColor: '#ffffff',
                    logging: false,
                    scrollX: 0,
                    scrollY: 0,
                    onclone: (clonedDoc) => {
                        const clonedArea = clonedDoc.getElementById('invoiceArea');
                        if (clonedArea) {
                            clonedArea.style.boxShadow = 'none';
                            clonedArea.style.margin = '0 auto';
                        }
                    }
                });

                const { jsPDF } = window.jspdf;
                const pdf = new jsPDF({
                    orientation: 'portrait',
                    unit: 'mm',
                    format: 'a4'
                });

                const pageWidth = 210;
                const pageHeight = 297;
                const marginX = 8;
                const marginY = 8;
                const printableWidth = pageWidth - (marginX * 2); // 194mm
                const printableHeight = (canvas.height * printableWidth) / canvas.width;

                pdf.addImage(canvas.toDataURL('image/png', 1.0), 'PNG', marginX, marginY, printableWidth, printableHeight, undefined, 'FAST');
                pdf.save('فاتورة-طلب-#{{ $order->id }}-BlackBook.pdf');
            } catch (error) {
                console.error('Error generating PDF:', error);
                alert('حدث خطأ أثناء تنزيل ملف الـ PDF، يرجى المحاولة مرة أخرى.');
            } finally {
                btn.disabled = false;
                btn.style.opacity = '1';
                btn.innerHTML = originalHtml;
            }
        }

        async function downloadImage() {
            const btn = document.getElementById('btnDownloadImage');
            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.style.opacity = '0.7';
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري إنشاء الصورة...';

            try {
                if (document.fonts) {
                    await document.fonts.ready;
                }

                const invoiceArea = document.getElementById('invoiceArea');

                // Ensure all images are fully loaded before rendering
                const images = invoiceArea.querySelectorAll('img');
                await Promise.all(Array.from(images).map(img => {
                    if (img.complete) return Promise.resolve();
                    return new Promise(resolve => {
                        img.onload = resolve;
                        img.onerror = resolve;
                    });
                }));

                const canvas = await html2canvas(invoiceArea, {
                    scale: 2.5,
                    useCORS: true,
                    allowTaint: true,
                    backgroundColor: '#ffffff',
                    logging: false,
                    scrollX: 0,
                    scrollY: 0,
                    onclone: (clonedDoc) => {
                        const clonedArea = clonedDoc.getElementById('invoiceArea');
                        if (clonedArea) {
                            clonedArea.style.boxShadow = 'none';
                            clonedArea.style.margin = '0 auto';
                        }
                    }
                });

                const link = document.createElement('a');
                link.download = 'فاتورة-طلب-#{{ $order->id }}-BlackBook.png';
                link.href = canvas.toDataURL('image/png', 1.0);
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            } catch (error) {
                console.error('Error generating image:', error);
                alert('حدث خطأ أثناء تنزيل الفاتورة كصورة، يرجى المحاولة مرة أخرى.');
            } finally {
                btn.disabled = false;
                btn.style.opacity = '1';
                btn.innerHTML = originalHtml;
            }
        }

        // Auto print if requested via query parameter ?auto_print=1
        @if(request()->has('auto_print'))
            window.addEventListener('load', function() {
                setTimeout(function() {
                    window.print();
                }, 500);
            });
        @endif

        // 🔹 Auto download handlers for API endpoints (/api/v1/orders/{id}/pdf & /api/v1/orders/{id}/image)
        @php
            $isAutoPdf = (isset($autoDownload) && $autoDownload === 'pdf') || request()->get('auto_download') === 'pdf' || request()->get('download') === 'pdf';
            $isAutoImage = (isset($autoDownload) && $autoDownload === 'image') || request()->get('auto_download') === 'image' || request()->get('download') === 'image';
        @endphp

        @if($isAutoPdf)
            window.addEventListener('load', function() {
                showDownloadNotice('جاري تحضير وتنزيل ملف PDF تلقائياً...');
                setTimeout(async function() {
                    await downloadDirectPdf();
                    updateDownloadNotice('تم تنزيل فاتورة الـ PDF بنجاح!');
                }, 600);
            });
        @elseif($isAutoImage)
            window.addEventListener('load', function() {
                showDownloadNotice('جاري تحضير وتنزيل الصورة تلقائياً...');
                setTimeout(async function() {
                    await downloadImage();
                    updateDownloadNotice('تم تنزيل صورة الفاتورة بنجاح!');
                }, 600);
            });
        @endif

        function showDownloadNotice(text) {
            let notice = document.getElementById('autoDownloadNotice');
            if (!notice) {
                notice = document.createElement('div');
                notice.id = 'autoDownloadNotice';
                notice.className = 'no-print';
                notice.style.cssText = 'position:fixed; top:20px; left:50%; transform:translateX(-50%); z-index:99999; background:#0f172a; color:#ffffff; padding:12px 26px; border-radius:30px; box-shadow:0 10px 30px rgba(0,0,0,0.3); display:flex; align-items:center; gap:12px; font-weight:700; font-size:0.92rem; border:1px solid rgba(255,255,255,0.2); transition:all 0.3s ease;';
                document.body.appendChild(notice);
            }
            notice.innerHTML = '<i class="fas fa-spinner fa-spin" style="color:#D1232A;"></i> <span>' + text + '</span>';
        }

        function updateDownloadNotice(text) {
            const notice = document.getElementById('autoDownloadNotice');
            if (notice) {
                notice.innerHTML = '<i class="fas fa-check-circle" style="color:#22c55e;"></i> <span>' + text + '</span>';
                setTimeout(function() {
                    notice.style.opacity = '0';
                    notice.style.transform = 'translateX(-50%) translateY(-20px)';
                    setTimeout(() => notice.remove(), 400);
                }, 3500);
            }
        }
    </script>
</body>

</html>
