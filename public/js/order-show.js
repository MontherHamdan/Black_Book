document.addEventListener('DOMContentLoaded', function () {
    // ⬅️ إعدادات قادمة من Blade
    const config = window.orderShowConfig || {};
    const csrfToken = config.csrfToken || document.querySelector('meta[name="csrf-token"]')?.content;
    const updateStatusUrl = config.updateStatusUrl;

    // 🔹 أزرار نسخ SVG
    const copyButtons = document.querySelectorAll('.copy-svg-button');
    const nameSvgButtons = document.querySelectorAll('.copy-name-svg-btn');

    // 🔹 أزرار تغيير حالة الطلب
    const statusLinks = document.querySelectorAll('.change-status-item');

    // 🔹 أزرار نسخ العبارة (gift_title)
    const copyGiftButtons = document.querySelectorAll('.copy-gift-btn');

    // 🔹 كاروْسِلات تحميل صورة واحدة
    const singleDownloadConfigs = [
        { carouselId: 'finalBackImagesCarousel', buttonId: 'downloadCurrentFinalBackImage' },
        { carouselId: 'finalAdditionalImagesCarousel', buttonId: 'downloadCurrentFinalAdditionalImage' },
        // لو بدك ترجع تستخدم القديم:
        // { carouselId: 'additionalImagesCarousel', buttonId: 'downloadCurrentAdditional' },
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
        toast.style.transition = 'opacity 0.3s ease, transform 0.3s ease';

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

            navigator.clipboard.writeText(svgCode)
                .then(function () {
                    showToast('تم نسخ SVG الخاص بالطلب إلى الحافظة ✅', 'success');
                })
                .catch(function (err) {
                    console.error('Failed to copy SVG code: ', err);
                    showToast('فشل نسخ كود SVG. جرّب متصفح آخر.', 'error');
                });
        });
    });

    // 🧩 نسخ SVG للاسم من data-svg
    nameSvgButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            const svgCode = button.getAttribute('data-svg');
            if (!svgCode) {
                showToast('لا يوجد SVG مربوط بهذا الاسم حالياً.', 'error');
                return;
            }

            navigator.clipboard.writeText(svgCode)
                .then(function () {
                    showToast('تم نسخ SVG المرتبط بالاسم إلى الحافظة ✅', 'success');
                })
                .catch(function (err) {
                    console.error('Failed to copy name SVG code: ', err);
                    showToast('فشل نسخ كود SVG للاسم. جرّب متصفح آخر.', 'error');
                });
        });
    });

    // 🔁 تغيير حالة الطلب من show
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
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        id: orderId,
                        status: newStatus
                    }),
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            showToast(data.message || 'فشل تحديث الحالة.', 'error');
                        }
                    })
                    .catch(() => {
                        showToast('حدث خطأ أثناء تحديث الحالة.', 'error');
                    });
            });
        });
    }

    // ✏️ نسخ gift_title
    copyGiftButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            const text = this.dataset.text || '';
            if (!text) return;

            navigator.clipboard.writeText(text)
                .then(() => showToast('تم نسخ العبارة بنجاح! ✅', 'success'))
                .catch(() => showToast('حدث خطأ أثناء النسخ.', 'error'));
        });
    });

    // 🖼️ فنكشن عامة لتحميل الصورة النشطة من أي كاروْسيل
    function attachSingleImageDownloader(carouselId, buttonId) {
        const carouselElem = document.getElementById(carouselId);
        const downloadBtn = document.getElementById(buttonId);

        if (!carouselElem || !downloadBtn) return;

        function getActiveImageSrc() {
            const activeItem = carouselElem.querySelector('.carousel-item.active img');
            return activeItem ? activeItem.getAttribute('src') : null;
        }

        downloadBtn.addEventListener('click', function () {
            const src = getActiveImageSrc();
            if (!src) return;

            const link = document.createElement('a');
            link.href = src;

            const parts = src.split('/');
            link.download = parts[parts.length - 1] || 'image.jpg';

            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });
    }

    // تفعيل كل الكاروسلات المحددة
    singleDownloadConfigs.forEach(cfg => attachSingleImageDownloader(cfg.carouselId, cfg.buttonId));
});
