@extends('admin.layout')

@push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        /* ╔══════════════════════════════════════════════════╗
                           ║  BINDING GALLERY — SCOPED UNDER .nb-page        ║
                           ║  All rules use !important to override style.css  ║
                           ╚══════════════════════════════════════════════════╝ */

        /* ── Reset global overrides ── */
        .nb-page,
        .nb-page * {
            font-family: 'Cairo', sans-serif !important;
            box-sizing: border-box !important;
        }

        /* Restore Font Awesome font-family on icon pseudo-elements */
        .nb-page i::before,
        .nb-page .fas::before,
        .nb-page .far::before,
        .nb-page .fab::before,
        .nb-page .fa::before {
            font-family: 'Font Awesome 5 Free' !important;
        }

        .nb-page .fab::before {
            font-family: 'Font Awesome 5 Brands' !important;
        }

        .nb-page {
            background: transparent !important;
            direction: rtl !important;
            text-align: right !important;
        }

        /* ── Hero Header ── */
        .nb-hero {
            position: relative !important;
            padding: 36px 40px !important;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 40%, #312e81 70%, #4c1d95 100%) !important;
            border-radius: 24px !important;
            overflow: hidden !important;
            margin-bottom: 32px !important;
            box-shadow: 0 20px 60px rgba(15, 23, 42, 0.25) !important;
        }

        .nb-hero::before {
            content: '' !important;
            position: absolute !important;
            top: -60% !important;
            left: -20% !important;
            width: 500px !important;
            height: 500px !important;
            background: radial-gradient(circle, rgba(139, 92, 246, 0.25) 0%, transparent 70%) !important;
            border-radius: 50% !important;
            pointer-events: none !important;
        }

        .nb-hero::after {
            content: '' !important;
            position: absolute !important;
            bottom: -40% !important;
            right: -10% !important;
            width: 400px !important;
            height: 400px !important;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.2) 0%, transparent 70%) !important;
            border-radius: 50% !important;
            pointer-events: none !important;
        }

        .nb-hero-content {
            position: relative !important;
            z-index: 2 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            flex-wrap: wrap !important;
            gap: 20px !important;
        }

        .nb-hero-right {
            display: flex !important;
            align-items: center !important;
            gap: 18px !important;
        }

        .nb-hero-icon {
            width: 64px !important;
            height: 64px !important;
            min-width: 64px !important;
            border-radius: 20px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            background: rgba(255, 255, 255, 0.1) !important;
            backdrop-filter: blur(12px) !important;
            border: 1px solid rgba(255, 255, 255, 0.15) !important;
        }

        .nb-hero-icon i {
            color: #a5b4fc !important;
            font-size: 1.6rem !important;
        }

        .nb-hero-text h2 {
            margin: 0 !important;
            font-size: 1.5rem !important;
            font-weight: 900 !important;
            color: #fff !important;
            letter-spacing: -0.5px !important;
        }

        .nb-hero-text p {
            margin: 4px 0 0 !important;
            font-size: 0.85rem !important;
            color: rgba(165, 180, 252, 0.8) !important;
            font-weight: 500 !important;
        }

        .nb-hero-stats {
            display: flex !important;
            gap: 12px !important;
        }

        .nb-stat-card {
            padding: 12px 22px !important;
            border-radius: 16px !important;
            background: rgba(255, 255, 255, 0.08) !important;
            backdrop-filter: blur(12px) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            text-align: center !important;
        }

        .nb-stat-number {
            display: block !important;
            font-size: 1.5rem !important;
            font-weight: 900 !important;
            color: #fff !important;
            line-height: 1.2 !important;
        }

        .nb-stat-label {
            display: block !important;
            font-size: 0.7rem !important;
            color: rgba(165, 180, 252, 0.7) !important;
            font-weight: 600 !important;
            margin-top: 2px !important;
        }

        /* ── Bulk Download Bar ── */
        .nb-bulk-bar {
            position: relative !important;
            z-index: 2 !important;
            display: flex !important;
            flex-wrap: wrap !important;
            align-items: center !important;
            gap: 8px !important;
            margin-top: 18px !important;
            padding-top: 18px !important;
            border-top: 1px solid rgba(255, 255, 255, 0.1) !important;
        }

        .nb-bulk-bar .nb-bulk-label {
            color: rgba(165, 180, 252, 0.8) !important;
            font-size: 0.75rem !important;
            font-weight: 700 !important;
            margin-left: 6px !important;
        }

        .nb-bulk-btn {
            display: inline-flex !important;
            align-items: center !important;
            gap: 6px !important;
            padding: 7px 16px !important;
            border-radius: 12px !important;
            border: 1px solid rgba(255, 255, 255, 0.15) !important;
            background: rgba(255, 255, 255, 0.08) !important;
            color: #e0e7ff !important;
            font-size: 0.72rem !important;
            font-weight: 700 !important;
            cursor: pointer !important;
            transition: all 0.25s ease !important;
            text-decoration: none !important;
            white-space: nowrap !important;
        }

        .nb-bulk-btn:hover {
            background: rgba(255, 255, 255, 0.18) !important;
            border-color: rgba(255, 255, 255, 0.3) !important;
            color: #fff !important;
            transform: translateY(-1px) !important;
        }

        .nb-bulk-btn i {
            font-size: 0.8rem !important;
        }

        .nb-bulk-btn.nb-bulk-range {
            background: rgba(99, 102, 241, 0.25) !important;
            border-color: rgba(129, 140, 248, 0.3) !important;
        }

        .nb-popup-note {
            color: rgba(165, 180, 252, 0.5) !important;
            font-size: 0.62rem !important;
            font-weight: 500 !important;
            margin-right: auto !important;
        }

        /* ── Empty State ── */
        .nb-empty {
            text-align: center !important;
            padding: 80px 20px !important;
            background: #fff !important;
            border-radius: 24px !important;
            border: 2px dashed rgba(99, 102, 241, 0.15) !important;
        }

        .nb-empty-visual {
            width: 100px !important;
            height: 100px !important;
            border-radius: 50% !important;
            background: linear-gradient(135deg, #eef2ff, #e0e7ff) !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            margin-bottom: 20px !important;
        }

        .nb-empty-visual i {
            font-size: 2.5rem !important;
            color: #818cf8 !important;
        }

        .nb-empty h5 {
            font-weight: 800 !important;
            color: #1e1b4b !important;
            font-size: 1.1rem !important;
            margin: 0 0 8px !important;
        }

        .nb-empty p {
            color: #94a3b8 !important;
            font-size: 0.88rem !important;
            margin: 0 !important;
        }

        /* ── Grid Layout ── */
        .nb-grid {
            display: grid !important;
            grid-template-columns: repeat(auto-fill, minmax(420px, 1fr)) !important;
            gap: 18px !important;
        }

        /* ── Order Card ── */
        .nb-card {
            background: #fff !important;
            border-radius: 20px !important;
            border: 1px solid rgba(226, 232, 240, 0.8) !important;
            padding: 0 !important;
            overflow: hidden !important;
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1) !important;
            position: relative !important;
            box-shadow: 0 2px 12px rgba(15, 23, 42, 0.04) !important;
        }

        .nb-card:hover {
            transform: translateY(-4px) !important;
            box-shadow: 0 20px 50px rgba(99, 102, 241, 0.12), 0 8px 20px rgba(15, 23, 42, 0.06) !important;
            border-color: rgba(129, 140, 248, 0.3) !important;
        }

        /* Accent stripe */
        .nb-card::before {
            content: '' !important;
            position: absolute !important;
            top: 0 !important;
            right: 0 !important;
            width: 4px !important;
            height: 100% !important;
            background: linear-gradient(180deg, #6366f1, #8b5cf6, #a78bfa) !important;
            opacity: 0 !important;
            transition: opacity 0.35s ease !important;
        }

        .nb-card:hover::before {
            opacity: 1 !important;
        }

        /* ── Card Top ── */
        .nb-card-top {
            display: flex !important;
            align-items: center !important;
            gap: 14px !important;
            padding: 18px 20px 14px !important;
            background: transparent !important;
        }

        .nb-order-badge {
            width: 50px !important;
            height: 50px !important;
            min-width: 50px !important;
            border-radius: 16px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            background: linear-gradient(135deg, #6366f1, #8b5cf6) !important;
            color: #fff !important;
            font-weight: 900 !important;
            font-size: 1rem !important;
            box-shadow: 0 4px 14px rgba(99, 102, 241, 0.25) !important;
            flex-shrink: 0 !important;
        }

        .nb-order-meta {
            flex: 1 !important;
            min-width: 0 !important;
        }

        .nb-order-meta h4 {
            margin: 0 0 4px !important;
            font-size: 0.95rem !important;
            font-weight: 800 !important;
            color: #1e293b !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }

        .nb-group-tag {
            display: inline-flex !important;
            align-items: center !important;
            gap: 6px !important;
            padding: 3px 12px !important;
            border-radius: 999px !important;
            background: linear-gradient(135deg, #f1f5f9, #e2e8f0) !important;
            color: #475569 !important;
            font-size: 0.7rem !important;
            font-weight: 700 !important;
        }

        .nb-group-tag i {
            color: #6366f1 !important;
            font-size: 0.6rem !important;
        }

        .nb-open-btn {
            width: 40px !important;
            height: 40px !important;
            min-width: 40px !important;
            border-radius: 14px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            background: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
            text-decoration: none !important;
            flex-shrink: 0 !important;
            transition: all 0.25s ease !important;
            cursor: pointer !important;
        }

        .nb-open-btn i {
            color: #6366f1 !important;
            font-size: 0.85rem !important;
            transition: all 0.25s ease !important;
        }

        .nb-open-btn:hover {
            background: linear-gradient(135deg, #6366f1, #8b5cf6) !important;
            border-color: transparent !important;
            box-shadow: 0 6px 18px rgba(99, 102, 241, 0.35) !important;
            transform: scale(1.08) !important;
        }

        .nb-open-btn:hover i {
            color: #fff !important;
        }

        /* ── Additive Badges ── */
        .nb-additives {
            display: flex !important;
            flex-wrap: wrap !important;
            gap: 5px !important;
            padding: 0 20px 12px !important;
        }

        .nb-additive-badge {
            display: inline-flex !important;
            align-items: center !important;
            gap: 4px !important;
            padding: 3px 10px !important;
            border-radius: 8px !important;
            font-size: 0.62rem !important;
            font-weight: 700 !important;
            border: 1px solid #e2e8f0 !important;
            background: #f8fafc !important;
            color: #475569 !important;
        }

        .nb-additive-badge i {
            font-size: 0.58rem !important;
        }

        .nb-additive-badge.nb-add-yes {
            background: rgba(16, 185, 129, 0.08) !important;
            border-color: rgba(16, 185, 129, 0.25) !important;
            color: #059669 !important;
        }

        .nb-additive-badge.nb-add-no {
            background: rgba(239, 68, 68, 0.05) !important;
            border-color: rgba(239, 68, 68, 0.15) !important;
            color: #dc2626 !important;
        }

        .nb-additive-badge.nb-add-info {
            background: rgba(99, 102, 241, 0.06) !important;
            border-color: rgba(99, 102, 241, 0.18) !important;
            color: #4f46e5 !important;
        }

        /* ── Mark Printed Button ── */
        .nb-printed-btn {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 6px !important;
            width: calc(100% - 40px) !important;
            margin: 0 20px 16px !important;
            padding: 9px 0 !important;
            border-radius: 14px !important;
            border: 1px solid rgba(16, 185, 129, 0.25) !important;
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.06), rgba(5, 150, 105, 0.06)) !important;
            color: #059669 !important;
            font-size: 0.78rem !important;
            font-weight: 800 !important;
            cursor: pointer !important;
            transition: all 0.3s ease !important;
        }

        .nb-printed-btn:hover {
            background: linear-gradient(135deg, #10b981, #059669) !important;
            color: #fff !important;
            border-color: transparent !important;
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.3) !important;
            transform: translateY(-1px) !important;
        }

        .nb-printed-btn:disabled {
            opacity: 0.6 !important;
            cursor: not-allowed !important;
        }

        /* ── Thumbnails Gallery ── */
        .nb-gallery {
            display: flex !important;
            align-items: center !important;
            gap: 10px !important;
            padding: 0 20px 18px !important;
            flex-wrap: wrap !important;
        }

        .nb-file-item {
            position: relative !important;
            width: 72px !important;
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            gap: 6px !important;
        }

        .nb-file-thumb {
            width: 64px !important;
            height: 64px !important;
            min-width: 64px !important;
            max-width: 64px !important;
            min-height: 64px !important;
            max-height: 64px !important;
            flex: 0 0 64px !important;
            border-radius: 16px !important;
            overflow: hidden !important;
            position: relative !important;
            border: 2px solid #eef2ff !important;
            box-shadow: 0 3px 10px rgba(15, 23, 42, 0.05) !important;
            cursor: pointer !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            padding: 0 !important;
            margin: 0 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            background: #f8fafc !important;
        }

        .nb-file-thumb:hover {
            transform: scale(1.1) translateY(-2px) !important;
            box-shadow: 0 8px 24px rgba(99, 102, 241, 0.2) !important;
            border-color: #818cf8 !important;
        }

        /* Force image dimensions */
        .nb-file-thumb img {
            width: 64px !important;
            height: 64px !important;
            min-width: 64px !important;
            min-height: 64px !important;
            max-width: 64px !important;
            max-height: 64px !important;
            object-fit: cover !important;
            display: block !important;
            margin: 0 !important;
            padding: 0 !important;
            border: none !important;
            border-radius: 0 !important;
            flex-shrink: 0 !important;
            transition: transform 0.3s ease !important;
        }

        .nb-file-thumb:hover img {
            transform: scale(1.15) !important;
        }

        /* Download overlay */
        .nb-dl-overlay {
            position: absolute !important;
            inset: 0 !important;
            width: 100% !important;
            height: 100% !important;
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.85), rgba(139, 92, 246, 0.85)) !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            opacity: 0 !important;
            transition: opacity 0.25s ease !important;
            text-decoration: none !important;
            z-index: 2 !important;
            border-radius: 14px !important;
        }

        .nb-dl-overlay i {
            color: #fff !important;
            font-size: 1.1rem !important;
        }

        .nb-file-thumb:hover .nb-dl-overlay {
            opacity: 1 !important;
        }

        /* Downloaded check overlay */
        .nb-check-overlay {
            position: absolute !important;
            top: -4px !important;
            left: -4px !important;
            width: 22px !important;
            height: 22px !important;
            border-radius: 50% !important;
            background: #fff !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            z-index: 3 !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12) !important;
            animation: nbCheckPop 0.3s ease !important;
        }

        .nb-check-overlay i {
            color: #10b981 !important;
            font-size: 0.85rem !important;
        }

        @keyframes nbCheckPop {
            0% {
                transform: scale(0);
            }

            60% {
                transform: scale(1.2);
            }

            100% {
                transform: scale(1);
            }
        }

        /* Count badge */
        .nb-count-badge {
            position: absolute !important;
            inset: 0 !important;
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.55), rgba(30, 27, 75, 0.55)) !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            color: #fff !important;
            font-weight: 900 !important;
            font-size: 0.85rem !important;
            pointer-events: none !important;
            z-index: 1 !important;
            border-radius: 14px !important;
        }

        .nb-file-thumb:hover .nb-count-badge {
            opacity: 0 !important;
        }

        /* File label */
        .nb-file-label {
            font-size: 0.62rem !important;
            font-weight: 700 !important;
            color: #64748b !important;
            text-align: center !important;
            line-height: 1.3 !important;
            max-width: 72px !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }

        /* Empty file thumb */
        .nb-file-empty {
            background: linear-gradient(135deg, #fef2f2, #fff1f2) !important;
            border: 2px dashed rgba(239, 68, 68, 0.2) !important;
            cursor: default !important;
        }

        .nb-file-empty:hover {
            transform: none !important;
            box-shadow: 0 3px 10px rgba(15, 23, 42, 0.05) !important;
            border-color: rgba(239, 68, 68, 0.2) !important;
        }

        .nb-file-empty i {
            font-size: 1.1rem !important;
            color: #fca5a5 !important;
        }

        /* ── Separator ── */
        .nb-file-sep {
            width: 1px !important;
            height: 40px !important;
            background: linear-gradient(180deg, transparent, #e2e8f0, transparent) !important;
            flex-shrink: 0 !important;
            margin: 0 2px !important;
            align-self: center !important;
        }

        /* ── Pagination ── */
        .nb-pages {
            display: flex !important;
            justify-content: center !important;
            margin-top: 36px !important;
        }

        .nb-pages nav {
            background: transparent !important;
        }

        .nb-pages .pagination {
            gap: 6px !important;
        }

        .nb-pages .page-item .page-link {
            border-radius: 12px !important;
            border: 1px solid #e2e8f0 !important;
            color: #475569 !important;
            font-weight: 600 !important;
            font-size: 0.85rem !important;
            padding: 8px 14px !important;
            transition: all 0.2s ease !important;
            background: #fff !important;
        }

        .nb-pages .page-item.active .page-link {
            background: linear-gradient(135deg, #6366f1, #8b5cf6) !important;
            border-color: transparent !important;
            color: #fff !important;
            box-shadow: 0 4px 14px rgba(99, 102, 241, 0.3) !important;
        }

        .nb-pages .page-item .page-link:hover {
            background: #eef2ff !important;
            border-color: #818cf8 !important;
            color: #4f46e5 !important;
        }

        /* ── Lightbox Modal ── */
        .nb-lightbox-backdrop {
            position: fixed !important;
            inset: 0 !important;
            background: rgba(0, 0, 0, 0.85) !important;
            backdrop-filter: blur(8px) !important;
            z-index: 10000 !important;
            display: none !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 40px !important;
            cursor: zoom-out !important;
        }

        .nb-lightbox-backdrop.active {
            display: flex !important;
        }

        .nb-lightbox-img {
            max-width: 90vw !important;
            max-height: 85vh !important;
            border-radius: 16px !important;
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.5) !important;
            object-fit: contain !important;
            cursor: default !important;
            animation: nbZoomIn 0.3s ease !important;
        }

        .nb-lightbox-close {
            position: absolute !important;
            top: 24px !important;
            left: 24px !important;
            width: 44px !important;
            height: 44px !important;
            border-radius: 50% !important;
            background: rgba(255, 255, 255, 0.1) !important;
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            cursor: pointer !important;
            transition: all 0.2s ease !important;
            z-index: 10001 !important;
        }

        .nb-lightbox-close:hover {
            background: rgba(255, 255, 255, 0.2) !important;
        }

        .nb-lightbox-close i {
            color: #fff !important;
            font-size: 1.2rem !important;
        }

        .nb-lightbox-download {
            position: absolute !important;
            bottom: 32px !important;
            left: 50% !important;
            transform: translateX(-50%) !important;
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
            padding: 10px 24px !important;
            border-radius: 999px !important;
            background: linear-gradient(135deg, #6366f1, #8b5cf6) !important;
            border: none !important;
            color: #fff !important;
            font-size: 0.85rem !important;
            font-weight: 700 !important;
            text-decoration: none !important;
            cursor: pointer !important;
            transition: all 0.2s ease !important;
            box-shadow: 0 8px 24px rgba(99, 102, 241, 0.4) !important;
        }

        .nb-lightbox-download:hover {
            transform: translateX(-50%) translateY(-2px) !important;
            box-shadow: 0 12px 32px rgba(99, 102, 241, 0.5) !important;
        }

        .nb-lightbox-download i {
            color: #fff !important;
            font-size: 0.9rem !important;
        }

        @keyframes nbZoomIn {
            from {
                transform: scale(0.85);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        /* ── Range Modal ── */
        .nb-range-modal-backdrop {
            position: fixed !important;
            inset: 0 !important;
            background: rgba(15, 23, 42, 0.6) !important;
            backdrop-filter: blur(4px) !important;
            z-index: 9999 !important;
            display: none !important;
            align-items: center !important;
            justify-content: center !important;
        }

        .nb-range-modal-backdrop.active {
            display: flex !important;
        }

        .nb-range-modal {
            background: #fff !important;
            border-radius: 24px !important;
            padding: 32px !important;
            width: 400px !important;
            max-width: 92vw !important;
            box-shadow: 0 30px 80px rgba(15, 23, 42, 0.25) !important;
            animation: nbZoomIn 0.25s ease !important;
        }

        .nb-range-modal h5 {
            font-weight: 900 !important;
            color: #1e293b !important;
            margin: 0 0 18px !important;
            font-size: 1rem !important;
        }

        .nb-range-modal label {
            font-size: 0.78rem !important;
            font-weight: 700 !important;
            color: #475569 !important;
            margin-bottom: 4px !important;
        }

        .nb-range-modal input {
            border-radius: 12px !important;
            border: 1px solid #e2e8f0 !important;
            padding: 10px 14px !important;
            font-size: 0.85rem !important;
            width: 100% !important;
            margin-bottom: 12px !important;
        }

        .nb-range-modal input:focus {
            border-color: #818cf8 !important;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1) !important;
            outline: none !important;
        }

        .nb-range-modal-actions {
            display: flex !important;
            gap: 10px !important;
            margin-top: 8px !important;
        }

        .nb-range-go {
            flex: 1 !important;
            padding: 10px !important;
            border-radius: 14px !important;
            border: none !important;
            background: linear-gradient(135deg, #6366f1, #8b5cf6) !important;
            color: #fff !important;
            font-weight: 800 !important;
            font-size: 0.82rem !important;
            cursor: pointer !important;
            transition: all 0.2s ease !important;
        }

        .nb-range-go:hover {
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.35) !important;
            transform: translateY(-1px) !important;
        }

        .nb-range-cancel {
            padding: 10px 20px !important;
            border-radius: 14px !important;
            border: 1px solid #e2e8f0 !important;
            background: #fff !important;
            color: #64748b !important;
            font-weight: 700 !important;
            font-size: 0.82rem !important;
            cursor: pointer !important;
            transition: all 0.2s ease !important;
        }

        .nb-range-cancel:hover {
            background: #f8fafc !important;
        }

        /* ── Responsive ── */
        @media (max-width: 768px) {
            .nb-hero {
                padding: 24px 20px !important;
            }

            .nb-hero-icon {
                width: 48px !important;
                height: 48px !important;
                min-width: 48px !important;
                border-radius: 14px !important;
            }

            .nb-hero-icon i {
                font-size: 1.2rem !important;
            }

            .nb-hero-text h2 {
                font-size: 1.15rem !important;
            }

            .nb-grid {
                grid-template-columns: 1fr !important;
            }

            .nb-gallery {
                flex-wrap: wrap !important;
                gap: 8px !important;
            }

            .nb-hero-stats {
                flex-wrap: wrap !important;
            }

            .nb-bulk-bar {
                gap: 6px !important;
            }
        }

        @media (max-width: 480px) {
            .nb-grid {
                grid-template-columns: 1fr !important;
            }

            .nb-file-item {
                width: 60px !important;
            }

            .nb-file-thumb,
            .nb-file-thumb img {
                width: 52px !important;
                height: 52px !important;
                min-width: 52px !important;
                max-width: 52px !important;
                min-height: 52px !important;
                max-height: 52px !important;
                flex: 0 0 52px !important;
            }
        }
    </style>
@endpush

@section('content')
    <div class="nb-page" style="padding: 24px 0;">

        {{-- ═══════════════════════════════════
        HERO HEADER
        ═══════════════════════════════════ --}}
        <div class="nb-hero">
            <div class="nb-hero-content">
                <div class="nb-hero-right">
                    <div class="nb-hero-icon">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <div class="nb-hero-text">
                        <h2>معرض ملفات التجليد</h2>
                        <p>طلبات قيد التجهيز — مراجعة وتحميل ملفات المصممين النهائية</p>
                    </div>
                </div>
                <div class="nb-hero-stats">
                    <div class="nb-stat-card">
                        <span class="nb-stat-number">{{ $orders->total() }}</span>
                        <span class="nb-stat-label">إجمالي الطلبات</span>
                    </div>
                    <div class="nb-stat-card">
                        <span class="nb-stat-number">{{ $orders->count() }}</span>
                        <span class="nb-stat-label">في هذه الصفحة</span>
                    </div>
                </div>
            </div>

            {{-- ── Bulk Download Bar ── --}}
            <div class="nb-bulk-bar">
                <span class="nb-bulk-label"><i class="fas fa-download"></i> تحميل جماعي:</span>
                <button class="nb-bulk-btn" onclick="nbBulkDownload('design')">
                    <i class="fas fa-palette"></i> التصميم
                </button>
                <button class="nb-bulk-btn" onclick="nbBulkDownload('decoration')">
                    <i class="fas fa-paint-brush"></i> الزخرفة
                </button>
                <button class="nb-bulk-btn" onclick="nbBulkDownload('gift')">
                    <i class="fas fa-gift"></i> الإهداء
                </button>
                <button class="nb-bulk-btn" onclick="nbBulkDownload('internal')">
                    <i class="fas fa-images"></i> صور داخلية
                </button>
                <button class="nb-bulk-btn nb-bulk-range" onclick="nbOpenRangeModal()">
                    <i class="fas fa-sliders-h"></i> تحميل حسب النطاق
                </button>
                <span class="nb-popup-note"><i class="fas fa-info-circle"></i> قد يطلب المتصفح السماح بالتحميلات
                    المتعددة</span>
            </div>
        </div>

        {{-- ═══════════════════════════════════
        EMPTY STATE
        ═══════════════════════════════════ --}}
        @if($orders->isEmpty())
            <div class="nb-empty">
                <div class="nb-empty-visual">
                    <i class="fas fa-inbox"></i>
                </div>
                <h5>لا يوجد طلبات قيد التجهيز حالياً</h5>
                <p>ستظهر الطلبات هنا عند تحويل حالتها إلى "قيد التجهيز".</p>
            </div>
        @else

            {{-- ═══════════════════════════════════
            ORDER CARDS GRID
            ═══════════════════════════════════ --}}
            <div class="nb-grid">
                @foreach($orders as $order)
                    <div class="nb-card" data-order-id="{{ $order->id }}">

                        {{-- Card Top: Order ID + Name + Group + Open Button --}}
                        <div class="nb-card-top">
                            <div class="nb-order-badge">{{ $order->id }}</div>
                            <div class="nb-order-meta">
                                <h4>{{ $order->username_ar }}</h4>
                                <span class="nb-group-tag">
                                    <i class="fas fa-users"></i>
                                    {{ $order->discountCode->code_name ?? ($order->discountCode->discount_code ?? 'بدون مجموعة') }}
                                </span>
                            </div>
                            <a href="{{ route('orders.show', $order->id) }}#tab-binding" class="nb-open-btn" title="فتح التفاصيل">
                                <i class="fas fa-arrow-left"></i>
                            </a>
                        </div>

                        {{-- Additive Badges --}}
                        <div class="nb-additives">
                            {{-- Sponge --}}
                            @if($order->is_sponge)
                                <span class="nb-additive-badge nb-add-yes"><i class="fas fa-check"></i> مع إسفنج</span>
                            @else
                                <span class="nb-additive-badge nb-add-no"><i class="fas fa-times"></i> بدون إسفنج</span>
                            @endif

                            {{-- Additives --}}
                            @if($order->is_with_additives)
                                <span class="nb-additive-badge nb-add-yes"><i class="fas fa-plus-circle"></i> مع إضافات</span>
                            @else
                                <span class="nb-additive-badge nb-add-no"><i class="fas fa-minus-circle"></i> بدون إضافات</span>
                            @endif

                            {{-- Pages --}}
                            @if($order->pages_number)
                                <span class="nb-additive-badge nb-add-info"><i class="fas fa-file-alt"></i> {{ $order->pages_number }}
                                    صفحة</span>
                            @endif

                            {{-- Transparent Printing --}}
                            @if($order->transparent_printing_id)
                                <span class="nb-additive-badge nb-add-info"><i class="fas fa-print"></i> طباعة شفافة</span>
                            @endif

                            {{-- Book Type --}}
                            @if($order->bookType)
                                <span class="nb-additive-badge nb-add-info"><i class="fas fa-book"></i>
                                    {{ $order->bookType->name_ar }}</span>
                            @endif
                        </div>

                        {{-- File Thumbnails Gallery --}}
                        <div class="nb-gallery">

                            {{-- 1. التصميم النهائي --}}
                            <div class="nb-file-item" data-file-type="design">
                                @if($order->designer_design_file)
                                    <div class="nb-file-thumb"
                                        onclick="nbOpen('{{ asset('storage/' . $order->designer_design_file) }}')">
                                        <img src="{{ asset('storage/' . $order->designer_design_file) }}" alt="التصميم">
                                        <a href="{{ asset('storage/' . $order->designer_design_file) }}" download class="nb-dl-overlay"
                                            onclick="event.stopPropagation(); nbMarkDownloaded({{ $order->id }}, 'design', this)">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        @if($order->is_design_downloaded)
                                            <div class="nb-check-overlay"><i class="fas fa-check-circle"></i></div>
                                        @endif
                                    </div>
                                @else
                                    <div class="nb-file-thumb nb-file-empty">
                                        <i class="fas fa-file-image"></i>
                                    </div>
                                @endif
                                <span class="nb-file-label">التصميم</span>
                            </div>

                            {{-- 2. الزخرفة --}}
                            <div class="nb-file-item" data-file-type="decoration">
                                @if($order->designer_decoration_file)
                                    <div class="nb-file-thumb"
                                        onclick="nbOpen('{{ asset('storage/' . $order->designer_decoration_file) }}')">
                                        <img src="{{ asset('storage/' . $order->designer_decoration_file) }}" alt="الزخرفة">
                                        <a href="{{ asset('storage/' . $order->designer_decoration_file) }}" download
                                            class="nb-dl-overlay"
                                            onclick="event.stopPropagation(); nbMarkDownloaded({{ $order->id }}, 'decoration', this)">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        @if($order->is_decoration_downloaded)
                                            <div class="nb-check-overlay"><i class="fas fa-check-circle"></i></div>
                                        @endif
                                    </div>
                                @else
                                    <div class="nb-file-thumb nb-file-empty">
                                        <i class="fas fa-paint-brush"></i>
                                    </div>
                                @endif
                                <span class="nb-file-label">الزخرفة</span>
                            </div>

                            {{-- 3. الإهداء المخصص --}}
                            @if($order->gift_type === 'custom')
                                <div class="nb-file-item" data-file-type="gift">
                                    @if($order->designer_gift_file)
                                        <div class="nb-file-thumb" onclick="nbOpen('{{ asset('storage/' . $order->designer_gift_file) }}')">
                                            <img src="{{ asset('storage/' . $order->designer_gift_file) }}" alt="الإهداء">
                                            <a href="{{ asset('storage/' . $order->designer_gift_file) }}" download class="nb-dl-overlay"
                                                onclick="event.stopPropagation(); nbMarkDownloaded({{ $order->id }}, 'gift', this)">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            @if($order->is_gift_downloaded)
                                                <div class="nb-check-overlay"><i class="fas fa-check-circle"></i></div>
                                            @endif
                                        </div>
                                    @else
                                        <div class="nb-file-thumb nb-file-empty">
                                            <i class="fas fa-gift"></i>
                                        </div>
                                    @endif
                                    <span class="nb-file-label">الإهداء</span>
                                </div>
                            @endif

                            <div class="nb-file-sep"></div>

                            {{-- 4. الصور الداخلية --}}
                            @php
                                $internalFiles = is_array($order->designer_internal_files) ? $order->designer_internal_files : [];
                                $count = count($internalFiles);
                            @endphp

                            <div class="nb-file-item" data-file-type="internal">
                                @if($count > 0)
                                    <div class="nb-file-thumb" onclick="nbOpen('{{ asset('storage/' . $internalFiles[0]) }}')">
                                        <img src="{{ asset('storage/' . $internalFiles[0]) }}" alt="داخلي">
                                        @if($count > 1)
                                            <div class="nb-count-badge">+{{ $count }}</div>
                                        @endif
                                        <a href="{{ asset('storage/' . $internalFiles[0]) }}" download class="nb-dl-overlay"
                                            onclick="event.stopPropagation(); nbMarkDownloaded({{ $order->id }}, 'internal', this)">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        @if($order->is_internal_downloaded)
                                            <div class="nb-check-overlay"><i class="fas fa-check-circle"></i></div>
                                        @endif
                                    </div>
                                @else
                                    <div class="nb-file-thumb nb-file-empty">
                                        <i class="fas fa-images"></i>
                                    </div>
                                @endif
                                <span class="nb-file-label">صور داخلية{{ $count > 0 ? ' (' . $count . ')' : '' }}</span>
                            </div>

                        </div>

                        {{-- Mark as Printed Button --}}
                        <button class="nb-printed-btn" onclick="nbMarkPrinted({{ $order->id }}, this)">
                            <i class="fas fa-print"></i> تم الطباعة — نقل إلى التوصيل
                        </button>

                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="nb-pages">
                {{ $orders->links() }}
            </div>
        @endif

    </div>

    {{-- ═══════════════════════════════════
    LIGHTBOX VIEWER
    ═══════════════════════════════════ --}}
    <div class="nb-lightbox-backdrop" id="nbLightbox" onclick="nbClose()">
        <div class="nb-lightbox-close" onclick="nbClose()">
            <i class="fas fa-times"></i>
        </div>
        <img class="nb-lightbox-img" id="nbLightboxImg" src="" alt="" onclick="event.stopPropagation()">
        <a class="nb-lightbox-download" id="nbLightboxDl" href="" download onclick="event.stopPropagation()">
            <i class="fas fa-download"></i>
            تحميل الملف
        </a>
    </div>

    {{-- ═══════════════════════════════════
    RANGE DOWNLOAD MODAL
    ═══════════════════════════════════ --}}
    <div class="nb-range-modal-backdrop" id="nbRangeModal" onclick="nbCloseRangeModal()">
        <div class="nb-range-modal" onclick="event.stopPropagation()">
            <h5><i class="fas fa-sliders-h" style="color:#6366f1;margin-left:8px;"></i> تحميل حسب نطاق الطلبات</h5>
            <label>من رقم الطلب:</label>
            <input type="number" id="nbRangeFrom" placeholder="مثال: 100">
            <label>إلى رقم الطلب:</label>
            <input type="number" id="nbRangeTo" placeholder="مثال: 120">
            <label>نوع الملف:</label>
            <select id="nbRangeType"
                style="border-radius:12px;border:1px solid #e2e8f0;padding:10px 14px;font-size:0.85rem;width:100%;margin-bottom:12px;">
                <option value="design">التصميم النهائي</option>
                <option value="decoration">الزخرفة</option>
                <option value="gift">الإهداء المخصص</option>
                <option value="internal">الصور الداخلية</option>
            </select>
            <div class="nb-range-modal-actions">
                <button class="nb-range-go" onclick="nbRangeDownload()"><i class="fas fa-download"></i> تحميل</button>
                <button class="nb-range-go"
                    onclick="nbBulkDownload(document.getElementById('nbRangeType').value); nbCloseRangeModal();"
                    style="background:linear-gradient(135deg,#10b981,#059669)!important;">
                    <i class="fas fa-layer-group"></i> تحميل الكل بالصفحة
                </button>
                <button class="nb-range-cancel" onclick="nbCloseRangeModal()">إلغاء</button>
            </div>
        </div>
    </div>

    <script>
        // ═══════════ CSRF Token ═══════════
        const NB_CSRF = '{{ csrf_token() }}';

        // ═══════════ Lightbox ═══════════
        function nbOpen(src) {
            document.getElementById('nbLightboxImg').src = src;
            document.getElementById('nbLightboxDl').href = src;
            document.getElementById('nbLightbox').classList.add('active');
            document.body.style.overflow = 'hidden';
        }
        function nbClose() {
            document.getElementById('nbLightbox').classList.remove('active');
            document.body.style.overflow = '';
        }
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') { nbClose(); nbCloseRangeModal(); }
        });

        // ═══════════ Mark as Printed ═══════════
        function nbMarkPrinted(orderId, btn) {
            Swal.fire({
                title: 'تأكيد الطباعة',
                html: `هل تريد تحويل حالة الطلب <strong>#${orderId}</strong> إلى "تم الطباعة"؟`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'نعم، تم الطباعة',
                cancelButtonText: 'إلغاء',
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#94a3b8',
                reverseButtons: true
            }).then((result) => {
                if (!result.isConfirmed) return;
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري التحديث...';

                fetch('{{ route("orders.updateStatus") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': NB_CSRF,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ id: orderId, status: 'Printed' })
                })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            const card = btn.closest('.nb-card');
                            card.style.transition = 'all 0.5s ease';
                            card.style.opacity = '0';
                            card.style.transform = 'scale(0.9) translateY(-20px)';
                            setTimeout(() => card.remove(), 500);
                            Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'تم تحويل الطلب بنجاح', showConfirmButton: false, timer: 2000, timerProgressBar: true });
                        } else {
                            Swal.fire({ title: 'خطأ', text: data.message || 'حدث خطأ أثناء تحديث الحالة', icon: 'error', confirmButtonColor: '#dc2626' });
                            btn.disabled = false;
                            btn.innerHTML = '<i class="fas fa-print"></i> تم الطباعة — نقل إلى التوصيل';
                        }
                    })
                    .catch(() => {
                        Swal.fire({ title: 'خطأ في الاتصال', text: 'يرجى المحاولة مرة أخرى', icon: 'error', confirmButtonColor: '#dc2626' });
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fas fa-print"></i> تم الطباعة — نقل إلى التوصيل';
                    });
            });
        }

        // ═══════════ Mark Individual File Downloaded ═══════════
        function nbMarkDownloaded(orderId, fileType, linkEl) {
            // Add checkmark to UI immediately
            const thumb = linkEl.closest('.nb-file-thumb');
            if (thumb && !thumb.querySelector('.nb-check-overlay')) {
                const check = document.createElement('div');
                check.className = 'nb-check-overlay';
                check.innerHTML = '<i class="fas fa-check-circle"></i>';
                thumb.appendChild(check);
            }

            // Send AJAX in background
            fetch('{{ route("notebook-binding.mark-downloaded") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': NB_CSRF,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ order_id: orderId, file_type: fileType })
            }).catch(() => { }); // silent — download already triggered
        }

        // ═══════════ Bulk Download (All on Page) ═══════════
        function nbBulkDownload(fileType) {
            const cards = document.querySelectorAll('.nb-card');
            const urls = [];
            const orderIds = [];

            cards.forEach(card => {
                const orderId = card.dataset.orderId;
                const fileItems = card.querySelectorAll('.nb-file-item[data-file-type="' + fileType + '"]');

                fileItems.forEach(item => {
                    const thumb = item.querySelector('.nb-file-thumb:not(.nb-file-empty)');
                    if (thumb) {
                        // Get the download link's href
                        const dlLink = thumb.querySelector('.nb-dl-overlay');
                        if (dlLink && dlLink.href) {
                            urls.push({ url: dlLink.href, orderId: orderId, thumb: thumb });
                            if (!orderIds.includes(parseInt(orderId))) {
                                orderIds.push(parseInt(orderId));
                            }
                        }

                        // For internal files, also get extra files
                        if (fileType === 'internal') {
                            // Internal files might have multiple — we store all internal file URLs as data attributes
                            const extraUrls = card.dataset.internalUrls;
                            if (extraUrls) {
                                try {
                                    JSON.parse(extraUrls).forEach(u => urls.push({ url: u, orderId: orderId, thumb: thumb }));
                                } catch (e) { }
                            }
                        }
                    }
                });
            });

            if (urls.length === 0) {
                Swal.fire({ title: 'تنبيه', text: 'لا توجد ملفات من هذا النوع للتحميل في هذه الصفحة', icon: 'info', confirmButtonColor: '#6366f1' });
                return;
            }

            // Sequential download with delay
            let i = 0;
            function downloadNext() {
                if (i >= urls.length) return;
                const item = urls[i];
                const a = document.createElement('a');
                a.href = item.url;
                a.download = '';
                a.style.display = 'none';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);

                // Add checkmark
                if (item.thumb && !item.thumb.querySelector('.nb-check-overlay')) {
                    const check = document.createElement('div');
                    check.className = 'nb-check-overlay';
                    check.innerHTML = '<i class="fas fa-check-circle"></i>';
                    item.thumb.appendChild(check);
                }

                i++;
                if (i < urls.length) {
                    setTimeout(downloadNext, 500);
                }
            }
            downloadNext();

            // Bulk mark as downloaded in backend
            if (orderIds.length > 0) {
                fetch('{{ route("notebook-binding.bulk-mark-downloaded") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': NB_CSRF,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ order_ids: orderIds, file_type: fileType })
                }).catch(() => { });
            }
        }

        // ═══════════ Range Download ═══════════
        function nbOpenRangeModal() {
            document.getElementById('nbRangeModal').classList.add('active');
        }
        function nbCloseRangeModal() {
            document.getElementById('nbRangeModal').classList.remove('active');
        }
        function nbRangeDownload() {
            const from = parseInt(document.getElementById('nbRangeFrom').value);
            const to = parseInt(document.getElementById('nbRangeTo').value);
            const fileType = document.getElementById('nbRangeType').value;

            if (isNaN(from) || isNaN(to) || from > to) {
                Swal.fire({ title: 'خطأ', text: 'يرجى إدخال نطاق صحيح', icon: 'warning', confirmButtonColor: '#f59e0b' });
                return;
            }

            const cards = document.querySelectorAll('.nb-card');
            const urls = [];
            const orderIds = [];

            cards.forEach(card => {
                const oid = parseInt(card.dataset.orderId);
                if (oid >= from && oid <= to) {
                    const fileItems = card.querySelectorAll('.nb-file-item[data-file-type="' + fileType + '"]');
                    fileItems.forEach(item => {
                        const thumb = item.querySelector('.nb-file-thumb:not(.nb-file-empty)');
                        if (thumb) {
                            const dlLink = thumb.querySelector('.nb-dl-overlay');
                            if (dlLink && dlLink.href) {
                                urls.push({ url: dlLink.href, orderId: oid, thumb: thumb });
                                if (!orderIds.includes(oid)) orderIds.push(oid);
                            }
                        }
                    });
                }
            });

            if (urls.length === 0) {
                Swal.fire({ title: 'تنبيه', text: 'لا توجد ملفات ضمن هذا النطاق', icon: 'info', confirmButtonColor: '#6366f1' });
                return;
            }

            nbCloseRangeModal();

            let i = 0;
            function downloadNext() {
                if (i >= urls.length) return;
                const item = urls[i];
                const a = document.createElement('a');
                a.href = item.url;
                a.download = '';
                a.style.display = 'none';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);

                if (item.thumb && !item.thumb.querySelector('.nb-check-overlay')) {
                    const check = document.createElement('div');
                    check.className = 'nb-check-overlay';
                    check.innerHTML = '<i class="fas fa-check-circle"></i>';
                    item.thumb.appendChild(check);
                }

                i++;
                if (i < urls.length) setTimeout(downloadNext, 500);
            }
            downloadNext();

            if (orderIds.length > 0) {
                fetch('{{ route("notebook-binding.bulk-mark-downloaded") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': NB_CSRF,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ order_ids: orderIds, file_type: fileType })
                }).catch(() => { });
            }
        }
    </script>
@endsection