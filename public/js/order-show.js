/**
 * order-show.js
 * ─────────────
 * Handles interactive elements on the order show page:
 *  1. Status dropdown → AJAX update + badge refresh
 *  2. Designer dropdown / assign-me button → AJAX update
 *  3. Dynamic major dropdowns in graduate info modal
 */
document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    var cfg = window.orderShowConfig || {};
    var csrfToken = cfg.csrfToken;

    /* ──────────────────────────────────────────────
        1. STATUS CHANGE  →  Badge update
     ────────────────────────────────────────────── */
    var statusSelect = document.querySelector('.js-order-status-select');
    if (statusSelect) {

        // حفظ الحالة الأولية حتى نتمكن من العودة إليها عند حدوث خطأ
        statusSelect.dataset.currentStatus = statusSelect.value;

        statusSelect.addEventListener('change', function () {
            var selectElement = this;
            var orderId = selectElement.dataset.orderId;
            var newStatus = selectElement.value;

            fetch(cfg.updateStatusUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ id: orderId, status: newStatus }),
            })
                .then(function (res) {
                    // التقاط حالة 422 قبل عمل JSON parse لتجنب مشاكل الـ Promise
                    if (res.status === 422) {
                        return res.json().then(function (errData) {
                            throw { status: 422, data: errData };
                        });
                    }
                    if (!res.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return res.json();
                })
                .then(function (data) {
                    if (data.success) {
                        // تحديث الحالة الحالية المحفوظة
                        selectElement.dataset.currentStatus = newStatus;

                        // Update badge
                        var badge = document.querySelector('.js-order-status-badge-header');
                        if (badge) {
                            var classesToRemove = [];
                            badge.classList.forEach(function (c) {
                                if (c.startsWith('status-') || c.startsWith('bg-')) {
                                    classesToRemove.push(c);
                                }
                            });
                            classesToRemove.forEach(function (c) { badge.classList.remove(c); });

                            var newClasses = data.class.split(' ');
                            newClasses.forEach(function (c) {
                                if (c.trim()) badge.classList.add(c.trim());
                            });

                            var textEl = badge.querySelector('.badge-status-text');
                            if (textEl) {
                                textEl.textContent = data.label;
                            }
                        }
                    } else {
                        // فشل من نوع آخر
                        selectElement.value = selectElement.dataset.currentStatus;
                        alert(data.message || 'حدث خطأ أثناء تحديث الحالة.');
                    }
                })
                .catch(function (error) {
                    // إرجاع القائمة المنسدلة للحالة السابقة
                    selectElement.value = selectElement.dataset.currentStatus;

                    // إذا كان الخطأ هو 422 (نواقص التجليد)
                    if (error.status === 422 && typeof Swal !== 'undefined') {
                        let msg = error.data.message || "توجد ملفات ناقصة في الطلب.";

                        // تحويل علامة (-) إلى نقطة (•) مع إضافة مسافات سطرية
                        let formattedMessage = msg.replace(/-/g, '•').replace(/\n/g, '<br>');

                        Swal.fire({
                            title: '<span style="color: #e74a3b; font-family: Cairo; font-weight: bold;">تنبيه: ملفات غير مكتملة</span>',
                            html: `
                            <div style="text-align: right; direction: rtl; font-family: 'Cairo', sans-serif; line-height: 1.7;">
                                <div style="background: #fff5f5; border-right: 5px solid #e74a3b; padding: 15px; border-radius: 10px; color: #be2617; text-align: right; font-size: 0.95rem; font-weight: 500;">
                                    ${formattedMessage}
                                </div>
                                <p style="margin-top: 15px; font-size: 0.95rem; color: #6c757d;">
                                    يرجى التوجه إلى تبويب <b>"تجليد الدفتر"</b> والنقر على خيار التعديل لرفع الملفات المطلوبة، ثم المحاولة مرة أخرى.
                                </p>
                            </div>
                            `,
                            icon: 'warning',
                            confirmButtonText: 'حسناً',
                            confirmButtonColor: '#4e73df',
                        });
                    } else {
                        // إظهار رسالة خطأ احترافية بدلاً من alert العادي
                        Swal.fire({
                            title: '<span style="font-family: Cairo;">خطأ في الاتصال</span>',
                            text: 'حدث خطأ أثناء التواصل مع الخادم. يرجى المحاولة مرة أخرى.',
                            icon: 'error',
                            confirmButtonText: 'موافق',
                            confirmButtonColor: '#e74a3b'
                        });
                    }
                });
        });
    }

    /* ──────────────────────────────────────────────
       2. DESIGNER CHANGE (Admin dropdown)
    ────────────────────────────────────────────── */
    var designerSelect = document.querySelector('.js-designer-select');
    if (designerSelect) {
        designerSelect.addEventListener('change', function () {
            var orderId = this.dataset.orderId;
            var designerId = this.value;

            fetch(cfg.updateDesignerUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ order_id: orderId, designer_id: designerId || null }),
            })
                .then(function (res) { return res.json(); })
                .then(function (data) {
                    if (data.success) {
                        // Update designer name in pill
                        var nameEl = document.querySelector('.js-designer-name');
                        if (nameEl) {
                            var selectedOption = designerSelect.options[designerSelect.selectedIndex];
                            nameEl.textContent = designerId ? selectedOption.text : 'غير معيّن';
                        }
                    } else {
                        alert(data.message || 'حدث خطأ أثناء تحديث المصمم.');
                    }
                })
                .catch(function () {
                    alert('حدث خطأ في الاتصال.');
                });
        });
    }

    /* ──────────────────────────────────────────────
       3. ASSIGN ME BUTTON (Designer self-assign)
    ────────────────────────────────────────────── */
    var assignBtn = document.querySelector('.js-assign-me-btn');
    if (assignBtn) {
        assignBtn.addEventListener('click', function () {
            var orderId = this.dataset.orderId;
            var designerId = this.dataset.designerId;

            fetch(cfg.updateDesignerUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ order_id: orderId, designer_id: designerId }),
            })
                .then(function (res) { return res.json(); })
                .then(function (data) {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message || 'حدث خطأ أثناء تعيين المصمم.');
                    }
                })
                .catch(function () {
                    alert('حدث خطأ في الاتصال.');
                });
        });
    }

    /* ──────────────────────────────────────────────
       4. DYNAMIC MAJOR DROPDOWNS in Graduate Modal
    ────────────────────────────────────────────── */
    function populateMajors(parentSelect, majorSelect, currentMajorId) {
        majorSelect.innerHTML = '<option value="">اختر التخصص</option>';
        var selected = parentSelect.options[parentSelect.selectedIndex];
        if (!selected || !selected.value) return;

        try {
            var majors = JSON.parse(selected.getAttribute('data-majors') || '[]');
            majors.forEach(function (m) {
                var opt = document.createElement('option');
                opt.value = m.id;
                opt.textContent = m.name;
                if (currentMajorId && parseInt(m.id) === parseInt(currentMajorId)) {
                    opt.selected = true;
                }
                majorSelect.appendChild(opt);
            });
        } catch (e) {
            // ignore JSON parse errors
        }
    }

    var uniSelect = document.getElementById('modalUniversitySelect');
    var uniMajorSelect = document.getElementById('modalUniversityMajorSelect');
    var dipSelect = document.getElementById('modalDiplomaSelect');
    var dipMajorSelect = document.getElementById('modalDiplomaMajorSelect');

    if (uniSelect && uniMajorSelect) {
        // Populate on load if university is already selected
        populateMajors(uniSelect, uniMajorSelect, cfg.currentUniversityMajorId);

        uniSelect.addEventListener('change', function () {
            populateMajors(uniSelect, uniMajorSelect, null);
            // Clear diploma if university is selected
            if (this.value && dipSelect) {
                dipSelect.value = '';
                if (dipMajorSelect) dipMajorSelect.innerHTML = '<option value="">اختر التخصص</option>';
            }
        });
    }

    if (dipSelect && dipMajorSelect) {
        // Populate on load if diploma is already selected
        populateMajors(dipSelect, dipMajorSelect, cfg.currentDiplomaMajorId);

        dipSelect.addEventListener('change', function () {
            populateMajors(dipSelect, dipMajorSelect, null);
            // Clear university if diploma is selected
            if (this.value && uniSelect) {
                uniSelect.value = '';
                if (uniMajorSelect) uniMajorSelect.innerHTML = '<option value="">اختر التخصص</option>';
            }
        });
    }

    /* ──────────────────────────────────────────────
       5. IMAGE DOWNLOAD HANDLING
    ────────────────────────────────────────────── */

    /**
     * Helper to download an image from a URL programmatically
     */
    async function downloadImageURL(url, defaultFilename) {
        if (!url) return;
        try {
            const response = await fetch(url);
            const blob = await response.blob();
            const blobUrl = window.URL.createObjectURL(blob);

            const a = document.createElement('a');
            a.href = blobUrl;

            // Extract filename from URL or use default
            let filename = defaultFilename || 'image.jpg';
            const urlParts = url.split('/');
            const lastPart = urlParts[urlParts.length - 1];
            if (lastPart && lastPart.indexOf('.') !== -1) {
                filename = lastPart.split('?')[0]; // Remove query params if any
            }

            a.download = filename;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(blobUrl);
        } catch (err) {
            console.error('Error downloading image, falling back to window.open', err);
            // Fallback
            const a = document.createElement('a');
            a.href = url;
            a.download = defaultFilename || 'image.jpg';
            a.target = '_blank';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        }
    }

    async function downloadMultipleImages(urls, prefix) {
        if (!urls || urls.length === 0) return;
        for (let i = 0; i < urls.length; i++) {
            await downloadImageURL(urls[i], prefix + '_' + (i + 1) + '.jpg');
            // Small delay to prevent browser blocking all of them at once
            await new Promise(r => setTimeout(r, 300));
        }
    }

    // Helper to get active carousel image
    function getActiveCarouselImageUrl(carouselId) {
        const carousel = document.getElementById(carouselId);
        if (!carousel) return null;
        const activeItem = carousel.querySelector('.carousel-item.active img');
        return activeItem ? activeItem.src : null;
    }

    // Helper to get all carousel images
    function getAllCarouselImageUrls(carouselId) {
        const carousel = document.getElementById(carouselId);
        if (!carousel) return [];
        const imgs = carousel.querySelectorAll('.carousel-item img');
        return Array.from(imgs).map(img => img.src);
    }

    // Helper to get single static image from a block
    function getStaticImageUrl(blockId) {
        const block = document.getElementById(blockId);
        if (!block) return null;
        const img = block.querySelector('img');
        return img ? img.src : null;
    }

    // 1. Internal Images
    const btnDownCurrInternal = document.getElementById('downloadCurrentInternalImage');
    const btnDownAllInternal = document.getElementById('downloadAllInternalImages');
    if (btnDownCurrInternal) {
        btnDownCurrInternal.addEventListener('click', function (e) {
            e.preventDefault();
            const url = getActiveCarouselImageUrl('internalImagesCarousel');
            if (url) downloadImageURL(url, 'internal_image.jpg');
        });
    }
    if (btnDownAllInternal) {
        btnDownAllInternal.addEventListener('click', function (e) {
            e.preventDefault();
            const urls = getAllCarouselImageUrls('internalImagesCarousel');
            downloadMultipleImages(urls, 'internal_image');
        });
    }

    // 2. Transparent Image
    const btnDownCurrTransparent = document.getElementById('downloadCurrentTransparentImage');
    if (btnDownCurrTransparent) {
        btnDownCurrTransparent.addEventListener('click', function (e) {
            e.preventDefault();
            const url = getStaticImageUrl('transparentImageBlock');
            if (url) downloadImageURL(url, 'transparent_image.jpg');
        });
    }

    // 3. Decoration Image
    const btnDownCurrDecoration = document.getElementById('downloadCurrentDecorationImage');
    if (btnDownCurrDecoration) {
        btnDownCurrDecoration.addEventListener('click', function (e) {
            e.preventDefault();
            const url = getStaticImageUrl('decorationImageBlock');
            if (url) downloadImageURL(url, 'decoration_image.jpg');
        });
    }

    // 4. Another Design Images
    const btnDownCurrAnother = document.getElementById('downloadCurrentAnotherImage');
    const btnDownAllAnother = document.getElementById('downloadAllAnotherImages');
    if (btnDownCurrAnother) {
        btnDownCurrAnother.addEventListener('click', function (e) {
            e.preventDefault();
            const url = getActiveCarouselImageUrl('anotherDesignCarousel');
            if (url) downloadImageURL(url, 'another_design.jpg');
        });
    }
    if (btnDownAllAnother) {
        btnDownAllAnother.addEventListener('click', function (e) {
            e.preventDefault();
            const urls = getAllCarouselImageUrls('anotherDesignCarousel');
            downloadMultipleImages(urls, 'another_design');
        });
    }

    // 5. Front Image
    const btnDownCurrFront = document.getElementById('downloadCurrentFrontImage');
    if (btnDownCurrFront) {
        btnDownCurrFront.addEventListener('click', function (e) {
            e.preventDefault();
            const url = getStaticImageUrl('frontImageBlock');
            if (url) downloadImageURL(url, 'front_image.jpg');
        });
    }

    // 6. Final Back Images
    const btnDownCurrBack = document.getElementById('downloadCurrentFinalBackImage');
    const btnDownAllBack = document.getElementById('downloadAllFinalBackImages');
    if (btnDownCurrBack) {
        btnDownCurrBack.addEventListener('click', function (e) {
            e.preventDefault();
            const url = getActiveCarouselImageUrl('finalBackImagesCarousel');
            if (url) downloadImageURL(url, 'back_image.jpg');
        });
    }
    if (btnDownAllBack) {
        btnDownAllBack.addEventListener('click', function (e) {
            e.preventDefault();
            const urls = getAllCarouselImageUrls('finalBackImagesCarousel');
            downloadMultipleImages(urls, 'back_image');
        });
    }

});
