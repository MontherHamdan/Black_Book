document.addEventListener('DOMContentLoaded', function () {
    // ⬅️ إعدادات قادمة من Blade
    const config = window.orderShowConfig || {};
    const csrfToken =
        config.csrfToken ||
        document.querySelector('meta[name="csrf-token"]')?.content;
    const updateStatusUrl = config.updateStatusUrl;
    const updateDesignerUrl = config.updateDesignerUrl;

    // 🔹 أزرار نسخ SVG
    const copyButtons = document.querySelectorAll('.copy-svg-button');
    const nameSvgButtons = document.querySelectorAll('.copy-name-svg-btn');

    // 🔹 أزرار تغيير حالة الطلب
    const statusLinks = document.querySelectorAll('.change-status-item');

    // 🔹 أزرار نسخ العبارة (gift_title)
    const copyGiftButtons = document.querySelectorAll('.copy-gift-btn');

    // 🔹 فورمات الملاحظات (AJAX)
    const deliveryFollowupForm = document.querySelector(
        'form.js-delivery-followup-form'
    );
    const designFollowupForm = document.querySelector(
        'form.js-design-followup-form'
    );
    const bindingFollowupForm = document.querySelector(
        'form.js-binding-followup-form'
    );

    // 🔹 كاروْسِلات تحميل صورة واحدة
    const singleDownloadConfigs = [
        {
            carouselId: 'finalBackImagesCarousel',
            buttonId: 'downloadCurrentFinalBackImage'
        },
        {
            carouselId: 'finalAdditionalImagesCarousel',
            buttonId: 'downloadCurrentFinalAdditionalImage'
        },
        {
            carouselId: 'internalImagesCarousel',
            buttonId: 'downloadCurrentInternalImage'
        },
        {
            carouselId: 'anotherDesignCarousel',
            buttonId: 'downloadCurrentAnotherImage'
        }
    ];


    // ✅ إنشاء toast container مرة واحدة فقط
    let toastContainer = document.getElementById('toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toast-container';
        toastContainer.style.position = 'fixed';
        toastContainer.style.bottom = '20px';
        toastContainer.style.right = '20px';
        toastContainer.style.zIndex = '9999';
        document.body.appendChild(toastContainer);
    }

    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.textContent = message;
        toast.style.padding = '10px 20px';
        toast.style.marginTop = '10px';
        toast.style.borderRadius = '5px';
        toast.style.color = '#fff';
        toast.style.fontSize = '14px';
        toast.style.boxShadow = '0 2px 4px rgba(0, 0, 0, 0.2)';
        toast.style.opacity = '0';
        toast.style.transition =
            'opacity 0.3s ease, transform 0.3s ease';

        if (type === 'success') {
            toast.style.backgroundColor = '#28a745';
        } else if (type === 'error') {
            toast.style.backgroundColor = '#dc3545';
        }

        toastContainer.appendChild(toast);

        setTimeout(function () {
            toast.style.opacity = '1';
            toast.style.transform = 'translateY(-10px)';
        }, 100);

        setTimeout(function () {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(0)';
            setTimeout(function () {
                toast.remove();
            }, 300);
        }, 3000);
    }

    // 🧩 نسخ SVG من div.svg-preview
    copyButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            const svgPreviewDiv = document.querySelector('.svg-preview');
            if (!svgPreviewDiv) return;

            const svgCode = svgPreviewDiv.innerHTML.trim();

            navigator.clipboard
                .writeText(svgCode)
                .then(function () {
                    showToast(
                        'تم نسخ SVG الخاص بالطلب إلى الحافظة ✅',
                        'success'
                    );
                })
                .catch(function (err) {
                    console.error('Failed to copy SVG code: ', err);
                    showToast(
                        'فشل نسخ كود SVG. جرّب متصفح آخر.',
                        'error'
                    );
                });
        });
    });

    // 🧩 نسخ SVG للاسم من data-svg
    nameSvgButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            const svgCode = button.getAttribute('data-svg');
            if (!svgCode) {
                showToast(
                    'لا يوجد SVG مربوط بهذا الاسم حالياً.',
                    'error'
                );
                return;
            }

            navigator.clipboard
                .writeText(svgCode)
                .then(function () {
                    showToast(
                        'تم نسخ SVG المرتبط بالاسم إلى الحافظة ✅',
                        'success'
                    );
                })
                .catch(function (err) {
                    console.error(
                        'Failed to copy name SVG code: ',
                        err
                    );
                    showToast(
                        'فشل نسخ كود SVG للاسم. جرّب متصفح آخر.',
                        'error'
                    );
                });
        });
    });

    // 🔁 تغيير حالة الطلب من show (بدون refresh)
    if (statusLinks.length && updateStatusUrl && csrfToken) {
        statusLinks.forEach(function (link) {
            link.addEventListener('click', function (e) {
                e.preventDefault();

                const orderId = this.getAttribute('data-order-id');
                const newStatus = this.getAttribute('data-new-status');

                fetch(updateStatusUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        Accept: 'application/json'
                    },
                    body: JSON.stringify({
                        id: orderId,
                        status: newStatus
                    })
                })
                    .then((response) => response.json())
                    .then((data) => {
                        if (data.success) {
                            // 👇 تحديث البادج بدل ما نعمل reload
                            const statusBadge = document.getElementById('orderStatusDropdownInfo');
                            if (statusBadge) {
                                statusBadge.className =
                                    'badge badge-status dropdown-toggle ' + (data.class || '');

                                const textSpan = statusBadge.querySelector('.badge-status-text');

                                if (textSpan) {
                                    textSpan.textContent = data.label || newStatus;
                                } else {
                                    statusBadge.textContent = data.label || newStatus;
                                }
                            }



                            showToast(
                                'تم تحديث حالة التصميم بنجاح ✅',
                                'success'
                            );
                        } else {
                            showToast(
                                data.message || 'فشل تحديث الحالة.',
                                'error'
                            );
                        }
                    })
                    .catch(() => {
                        showToast(
                            'حدث خطأ أثناء تحديث الحالة.',
                            'error'
                        );
                    });
            });
        });
    }

    // 🔽 تغيير حالة الطلب من الهيدر (select)
    const statusSelects = document.querySelectorAll('.js-order-status-select');

    if (statusSelects.length && updateStatusUrl && csrfToken) {
        const headerStatusClassMap = {
            'Pending': 'status-pending',
            'Completed': 'status-completed',
            'preparing': 'status-preparing',
            'Received': 'status-received',
            'Out for Delivery': 'status-out-for-delivery',
            'Canceled': 'status-canceled',
            'error': 'status-error',
        };

        // كل كلاس محتمل لحالات التصميم
        const allStatusClasses = [
            'status-pending',
            'status-completed',
            'status-preparing',
            'status-received',
            'status-out-for-delivery',
            'status-canceled',
            'status-error',
            'status-unknown',
        ];

        statusSelects.forEach(function (select) {
            select.addEventListener('change', function () {
                const orderId = this.dataset.orderId;
                const newStatus = this.value;

                fetch(updateStatusUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        id: orderId,
                        status: newStatus
                    })
                })
                    .then(res => res.json())
                    .then(data => {
                        if (!data.success) {
                            showToast(
                                data.message || 'فشل تحديث حالة التصميم.',
                                'error'
                            );
                            return;
                        }

                        // ✅ تحديث شارة الهيدر بدون مسح الكلاسات الأخرى
                        const headerBadge = document.querySelector('.js-order-status-badge-header');
                        if (headerBadge) {
                            const statusClass = headerStatusClassMap[newStatus] || 'status-unknown';

                            // شيل كل كلاس status-* قديم
                            headerBadge.classList.remove(...allStatusClasses);
                            // أضف الكلاس الجديد فقط
                            headerBadge.classList.add(statusClass);

                            const textSpan = headerBadge.querySelector('.badge-status-text');
                            if (textSpan) {
                                textSpan.textContent = data.label || newStatus;
                            }
                        }

                        showToast('تم تحديث حالة التصميم من الهيدر بنجاح ✅', 'success');
                    })
                    .catch(() => {
                        showToast('حدث خطأ أثناء تحديث حالة التصميم.', 'error');
                    });
            });
        });
    }


    // 📚 AJAX لحفظ ملاحظات المتابعة على التجليد
    if (bindingFollowupForm && csrfToken) {
        bindingFollowupForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const form = this;
            const url = form.action;
            const formData = new FormData(form); // يشمل الصور لو رفعتها

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
                .then((response) => response.json())
                .then((data) => {
                    if (!data.success) {
                        showToast(
                            data.message ||
                            'فشل حفظ ملاحظات التجليد.',
                            'error'
                        );
                        return;
                    }

                    const box = document.getElementById(
                        'binding-followup-box'
                    );
                    if (box && data.html) {
                        box.innerHTML = data.html;
                    }

                    showToast(
                        data.message ||
                        'تم حفظ ملاحظات التجليد بنجاح ✅',
                        'success'
                    );
                })
                .catch(() => {
                    showToast(
                        'حدث خطأ أثناء حفظ ملاحظات التجليد.',
                        'error'
                    );
                });
        });
    }

    // ✏️ نسخ gift_title (لو موجودة أزرار)
    copyGiftButtons.forEach((btn) => {
        btn.addEventListener('click', function () {
            const text = this.dataset.text || '';
            if (!text) return;

            navigator.clipboard
                .writeText(text)
                .then(() =>
                    showToast(
                        'تم نسخ العبارة بنجاح! ✅',
                        'success'
                    )
                )
                .catch(() =>
                    showToast('حدث خطأ أثناء النسخ.', 'error')
                );
        });
    });

    // 📦 AJAX لحفظ ملاحظات التوصيل
    if (deliveryFollowupForm && csrfToken) {
        deliveryFollowupForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const form = this;
            const url = form.action;
            const formData = new FormData(form);

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
                .then((response) => response.json())
                .then((data) => {
                    if (!data.success) {
                        showToast(
                            data.message ||
                            'فشل حفظ ملاحظات التوصيل.',
                            'error'
                        );
                        return;
                    }

                    const box = document.getElementById(
                        'delivery-followup-box'
                    );
                    if (box && data.html) {
                        box.innerHTML = data.html;
                    }

                    showToast(
                        data.message ||
                        'تم حفظ ملاحظات التوصيل بنجاح ✅',
                        'success'
                    );
                })
                .catch(() => {
                    showToast(
                        'حدث خطأ أثناء حفظ ملاحظات التوصيل.',
                        'error'
                    );
                });
        });
    }

    // 🎨 AJAX لحفظ ملاحظات المتابعة على التصميم
    if (designFollowupForm && csrfToken) {
        designFollowupForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const form = this;
            const url = form.action;
            const formData = new FormData(form);

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
                .then((response) => response.json())
                .then((data) => {
                    if (!data.success) {
                        showToast(
                            data.message ||
                            'فشل حفظ ملاحظات المتابعة.',
                            'error'
                        );
                        return;
                    }

                    const box = document.getElementById(
                        'design-followup-box'
                    );
                    if (box && data.html) {
                        box.innerHTML = data.html;
                    }

                    showToast(
                        data.message ||
                        'تم حفظ ملاحظات المتابعة بنجاح ✅',
                        'success'
                    );
                })
                .catch(() => {
                    showToast(
                        'حدث خطأ أثناء حفظ ملاحظات المتابعة.',
                        'error'
                    );
                });
        });
    }

    // 🖼️ فنكشن عامة لتحميل الصورة النشطة من أي كاروْسيل
    function attachSingleImageDownloader(carouselId, buttonId) {
        const carouselElem =
            document.getElementById(carouselId);
        const downloadBtn =
            document.getElementById(buttonId);

        if (!carouselElem || !downloadBtn) return;

        function getActiveImageSrc() {
            const activeItem =
                carouselElem.querySelector(
                    '.carousel-item.active img'
                );
            return activeItem
                ? activeItem.getAttribute('src')
                : null;
        }

        downloadBtn.addEventListener('click', function () {
            const src = getActiveImageSrc();
            if (!src) return;

            const link = document.createElement('a');
            link.href = src;

            const parts = src.split('/');
            link.download =
                parts[parts.length - 1] || 'image.jpg';

            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });
    }
    // 🖼️ تحميل جميع الصور داخل حاوية معيّنة (كاروسيل أو بلوك عادي)
    function attachAllImagesDownloader(containerSelector, buttonId) {
        const container = document.querySelector(containerSelector);
        const btn = document.getElementById(buttonId);

        if (!container || !btn) return;

        btn.addEventListener('click', function () {
            const imgs = container.querySelectorAll('img');
            if (!imgs.length) return;

            imgs.forEach((img, index) => {
                const src = img.getAttribute('src');
                if (!src) return;

                const a = document.createElement('a');
                a.href = src;

                const parts = src.split('/');
                const baseName = parts[parts.length - 1] || `image-${index + 1}.jpg`;
                a.download = baseName;

                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
            });
        });
    }
    // 📷 تحميل صورة واحدة من خلال سلكتور معيّن
    function attachSingleImageDownloaderBySelector(imgSelector, buttonId) {
        const img = document.querySelector(imgSelector);
        const btn = document.getElementById(buttonId);

        if (!img || !btn) return;

        btn.addEventListener('click', function () {
            const src = img.getAttribute('src');
            if (!src) return;

            const a = document.createElement('a');
            a.href = src;

            const parts = src.split('/');
            const baseName = parts[parts.length - 1] || 'image.jpg';
            a.download = baseName;

            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        });
    }

    // تفعيل كل الكاروسلات المحددة
    singleDownloadConfigs.forEach((cfg) =>
        attachSingleImageDownloader(
            cfg.carouselId,
            cfg.buttonId
        )
    );
    // 🎯 تحميل جميع الصور في كل بلوك
    attachAllImagesDownloader('#internalImagesCarousel .carousel-inner', 'downloadAllInternalImages');
    attachAllImagesDownloader('#transparentImageBlock', 'downloadAllTransparentImages');
    attachAllImagesDownloader('#decorationImageBlock', 'downloadAllDecorationImages');

    // ✅ جديدة لمعلومات الخريج
    attachAllImagesDownloader('#designImageBlock', 'downloadAllDesignImages');
    attachAllImagesDownloader('#anotherDesignBlock', 'downloadAllAnotherImages');
    attachAllImagesDownloader('#frontImageBlock', 'downloadAllFrontImages');

    // 🎯 تحميل الصورة الحالية للبلوكات المفردة
    attachSingleImageDownloaderBySelector('#transparentImageBlock img', 'downloadCurrentTransparentImage');
    attachSingleImageDownloaderBySelector('#decorationImageBlock img', 'downloadCurrentDecorationImage');

    // ✅ جديدة لمعلومات الخريج
    attachSingleImageDownloaderBySelector('#designImageBlock img', 'downloadCurrentDesignImage');
    attachSingleImageDownloaderBySelector('#frontImageBlock img', 'downloadCurrentFrontImage');


    // 🎯 تحديث المصمم المسؤول (أدمن: select) – (مصمم: تعيين نفسه)
    if (updateDesignerUrl && csrfToken) {
        const designerSelects = document.querySelectorAll('.js-designer-select');
        const assignMeButtons = document.querySelectorAll('.js-assign-me-btn');

        // 🟦 1) أدمن يحدد مصمم من الـ select
        designerSelects.forEach(function (select) {
            select.addEventListener('change', function () {
                const orderId = this.dataset.orderId;
                const designerId = this.value || null;

                fetch(updateDesignerUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        order_id: orderId,
                        designer_id: designerId,
                    }),
                })
                    .then(res => res.json())
                    .then(data => {
                        if (!data.success) {
                            showToast(data.message || 'فشل تحديث المصمم.', 'error');
                            return;
                        }

                        // ✅ حدّث الاسم المعروض في الكرت
                        const card = this.closest('.order-header-chip');
                        const nameSpan = card?.querySelector('.js-designer-name');

                        if (nameSpan) {
                            if (designerId) {
                                const selectedOption = this.options[this.selectedIndex];
                                nameSpan.textContent = selectedOption.textContent.trim();
                            } else {
                                nameSpan.innerHTML = '<span class="text-muted">غير معيّن</span>';
                            }
                        }

                        showToast(data.message || 'تم تحديث المصمم بنجاح ✅', 'success');
                    })
                    .catch(() => {
                        showToast('حدث خطأ أثناء تحديث المصمم.', 'error');
                    });
            });
        });

        // 🟨 2) مصمم يعيّن نفسه بالزر
        assignMeButtons.forEach(function (btn) {
            btn.addEventListener('click', function () {
                const orderId = this.dataset.orderId;
                const designerId = this.dataset.designerId;

                fetch(updateDesignerUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        order_id: orderId,
                        designer_id: designerId,
                    }),
                })
                    .then(res => res.json())
                    .then(data => {
                        if (!data.success) {
                            showToast(data.message || 'فشل تعيينك كمصمم.', 'error');
                            return;
                        }

                        const card = this.closest('.order-header-chip');
                        const nameSpan = card?.querySelector('.js-designer-name');

                        // 🔁 حدّث الاسم إلى اسم المصمم الحالي (موجود في الزر نفسه أو في data-name لو حبيت تضيفه)
                        const currentName = '{{ auth()->user()->name }}'; // أو حطها في data-name بالـ Blade
                        if (nameSpan) {
                            nameSpan.textContent = currentName;
                        }

                        // أخفي الزر وأظهر Badge "أنت المصمم المسؤول"
                        this.remove();
                        const badge = document.createElement('span');
                        badge.className = 'badge bg-success mt-1';
                        badge.textContent = 'أنت المصمم المسؤول عن هذا الطلب';
                        card.querySelector('.order-header-body').appendChild(badge);

                        showToast(data.message || 'تم تعيينك كمصمم للطلب ✅', 'success');
                    })
                    .catch(() => {
                        showToast('حدث خطأ أثناء تعيينك كمصمم.', 'error');
                    });
            });
        });
    }

});
