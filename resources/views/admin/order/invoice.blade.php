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
            --secondary-color: #4f46e5;
            --text-dark: #1f2937;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --bg-light: #f8fafc;
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
            background-color: #4f46e5;
            color: #ffffff;
            box-shadow: 0 2px 8px rgba(79, 70, 229, 0.25);
        }

        .btn-download-pdf:hover {
            background-color: #4338ca;
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
            color: #4f46e5;
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
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 7px 11px;
        }

        .info-card-title {
            font-size: 0.82rem;
            font-weight: 800;
            color: #0f172a;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 3px;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .info-card-title i {
            color: #4f46e5;
            font-size: 0.8rem;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            font-size: 0.78rem;
            margin-bottom: 2px;
            line-height: 1.3;
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
            margin-bottom: 8px;
        }

        .section-heading {
            font-size: 0.84rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .section-heading i {
            color: #4f46e5;
            font-size: 0.8rem;
        }

        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.78rem;
            border-radius: 6px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }

        .invoice-table th {
            background-color: #0f172a;
            color: #ffffff;
            padding: 4px 8px;
            font-weight: 700;
            text-align: right;
            font-size: 0.78rem;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .invoice-table td {
            padding: 4px 8px;
            border-bottom: 1px solid #e2e8f0;
            color: #1e293b;
            vertical-align: middle;
            line-height: 1.35;
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

        /* 🔹 Financial Summary */
        .financial-summary-wrap {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 8px;
        }

        .financial-card {
            width: 270px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            overflow: hidden;
        }

        .financial-row {
            display: flex;
            justify-content: space-between;
            padding: 3px 8px;
            font-size: 0.78rem;
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
            color: #dc2626;
        }

        .financial-row.total-row {
            background-color: #0f172a !important;
            color: #ffffff !important;
            padding: 5px 8px;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .financial-row.total-row .financial-label {
            color: #ffffff !important;
            font-size: 0.84rem;
            font-weight: 800;
        }

        .financial-row.total-row .financial-val {
            color: #38bdf8 !important;
            font-size: 0.98rem;
            font-weight: 900;
        }

        /* 🔹 Footer Notes */
        .invoice-footer {
            border-top: 1px dashed #cbd5e1;
            padding-top: 6px;
            margin-top: 4px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.7rem;
            color: #64748b;
            line-height: 1.3;
        }

        .footer-note {
            max-width: 500px;
        }

        .footer-contact {
            text-align: left;
            direction: ltr;
            font-weight: 600;
            white-space: nowrap;
        }

        /* 🖨️ PRINT STYLES - Strictly Fits on Exactly 1 A4 Page */
        @media print {
            @page {
                size: A4 portrait;
                margin: 5mm 7mm;
            }

            html, body {
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
                height: 100% !important;
                max-height: 100% !important;
                overflow: hidden !important;
                font-size: 11px !important;
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
                page-break-inside: avoid !important;
                break-inside: avoid !important;
                page-break-after: avoid !important;
            }

            .invoice-header,
            .info-grid,
            .table-section,
            .financial-summary-wrap,
            .invoice-footer,
            .invoice-table,
            .invoice-table tr,
            .info-card,
            .financial-card {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
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
            <button type="button" class="btn-action btn-download-pdf" onclick="window.print();">
                <i class="fas fa-file-pdf"></i> حفظ الفاتورة (PDF) / طباعة
            </button>

            <a href="{{ route('orders.show', $order->id) }}" class="btn-action btn-back">
                <i class="fas fa-arrow-left"></i> العودة للطلب
            </a>
        </div>
    </div>

    <!-- 🔹 Invoice Printable Sheet -->
    <div class="invoice-sheet" id="invoiceArea">

        <!-- 1️⃣ Header -->
        <div class="invoice-header">
            <div class="brand-section">
                <div class="brand-info">
                    <h2>BLACK BOOK</h2>
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
                    <tr>
                        <td>4</td>
                        <td class="item-spec-name">نوع التجليد</td>
                        <td class="item-spec-val">{{ $order->is_sponge ? 'تجليد إسفنجي فاخر' : 'تجليد عادي' }}</td>
                    </tr>
                    <tr>
                        <td>5</td>
                        <td class="item-spec-name">الإضافات الخاصة</td>
                        <td class="item-spec-val">{{ $order->is_with_additives ? 'مع إضافات' : 'بدون إضافات' }}</td>
                    </tr>
                    @if($order->bookDecoration)
                    <tr>
                        <td>6</td>
                        <td class="item-spec-name">الزخرفة / الإكسسوار</td>
                        <td class="item-spec-val">{{ $order->bookDecoration->name }}</td>
                    </tr>
                    @endif
                    @if($order->svg_title)
                    <tr>
                        <td>7</td>
                        <td class="item-spec-name">عبارة الدفتر</td>
                        <td class="item-spec-val">{{ $order->svg_title }}</td>
                    </tr>
                    @endif
                    @if($order->gift_type && $order->gift_type !== 'none')
                    <tr>
                        <td>8</td>
                        <td class="item-spec-name">الهدية المرفقة</td>
                        <td class="item-spec-val">{{ $order->gift_title ?: $order->gift_type }}</td>
                    </tr>
                    @endif
                    @if($order->note)
                    <tr>
                        <td>9</td>
                        <td class="item-spec-name">ملاحظات الطلب</td>
                        <td class="item-spec-val">{{ $order->note }}</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- 4️⃣ Financial Summary -->
        <div class="financial-summary-wrap">
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

    <!-- 🔹 Script for Direct Vector PDF Print / Download -->
    <script>
        function downloadPdf() {
            window.print();
        }

        // Auto print if requested via query parameter ?auto_print=1
        @if(request()->has('auto_print'))
            window.addEventListener('load', function() {
                setTimeout(function() {
                    window.print();
                }, 500);
            });
        @endif
    </script>
</body>

</html>
