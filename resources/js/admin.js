/* =====================================================
   SAZARA GLOBAL — Admin JS
   Media Picker, Image Preview, Sidebar Toggle,
   Delete Confirm, Drag & Drop Upload
   ===================================================== */

document.addEventListener('DOMContentLoaded', () => {

    /* ==== 1. Sidebar Toggle (Mobile) ==== */
    const sidebar  = document.getElementById('admSidebar');
    const menuBtn  = document.getElementById('admMenuBtn');
    const overlay  = document.getElementById('admOverlay');

    const closeSidebar = () => {
        sidebar?.classList.remove('open');
        overlay?.classList.remove('open');
    };

    menuBtn?.addEventListener('click', () => {
        sidebar?.classList.toggle('open');
        overlay?.classList.toggle('open');
    });

    overlay?.addEventListener('click', closeSidebar);

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeSidebar();
    });

    /* ==== 2. Auto-dismiss alerts ==== */
    document.querySelectorAll('.adm-alert[data-autohide]').forEach((el) => {
        setTimeout(() => {
            el.style.transition = 'opacity 0.4s';
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 400);
        }, 4000);
    });

    /* ==== 3. Image Preview (direct file upload) ==== */
    document.querySelectorAll('[data-preview-for]').forEach((input) => {
        input.addEventListener('change', () => {
            const targetId = input.dataset.previewFor;
            const preview  = document.getElementById(targetId);
            if (! preview || ! input.files[0]) return;

            const reader = new FileReader();
            reader.onload = (e) => {
                preview.src = e.target.result;
                preview.style.display = 'block';
                preview.closest('.adm-image-preview')?.querySelector('.adm-image-preview-placeholder')?.remove();
            };
            reader.readAsDataURL(input.files[0]);
        });
    });

    /* ==== 4. Drop Zone ====  */
    document.querySelectorAll('.adm-drop-zone').forEach((zone) => {
        const input = zone.querySelector('input[type="file"]');

        zone.addEventListener('click', () => input?.click());

        zone.addEventListener('dragover', (e) => {
            e.preventDefault();
            zone.classList.add('drag-over');
        });

        zone.addEventListener('dragleave', () => zone.classList.remove('drag-over'));

        zone.addEventListener('drop', (e) => {
            e.preventDefault();
            zone.classList.remove('drag-over');
            if (input && e.dataTransfer.files.length) {
                input.files = e.dataTransfer.files;
                input.dispatchEvent(new Event('change'));
            }
        });
    });

    /* ==== 5. Media Picker Modal ==== */
    const pickerModal    = document.getElementById('mediaPickerModal');
    const pickerGrid     = document.getElementById('mediaPickerGrid');
    const pickerSearch   = document.getElementById('mediaPickerSearch');
    const pickerConfirm  = document.getElementById('mediaPickerConfirm');
    const pickerUpload   = document.getElementById('mediaPickerUploadInput');
    const pickerSelected = { item: null, id: null, url: null, path: null };
    let   activePickerTarget = null; // The field group that triggered the picker

    const openPicker = (triggerBtn) => {
        activePickerTarget = triggerBtn.closest('[data-media-group]');
        pickerModal?.classList.add('open');
        pickerSelected.item = null;
        pickerSelected.id   = null;
        pickerSelected.url  = null;
        pickerSelected.path = null;
        document.querySelectorAll('.adm-media-item').forEach(el => el.classList.remove('selected'));
        if (pickerConfirm) pickerConfirm.disabled = true;
    };

    const closePicker = () => {
        pickerModal?.classList.remove('open');
        activePickerTarget = null;
    };

    document.querySelectorAll('[data-open-picker]').forEach((btn) => {
        btn.addEventListener('click', () => openPicker(btn));
    });

    document.getElementById('mediaPickerClose')?.addEventListener('click', closePicker);
    pickerModal?.addEventListener('click', (e) => {
        if (e.target === pickerModal) closePicker();
    });

    // Click on media item in picker
    pickerGrid?.addEventListener('click', (e) => {
        const item = e.target.closest('.adm-media-item[data-media-id]');
        if (!item) return;

        document.querySelectorAll('.adm-media-item').forEach(el => el.classList.remove('selected'));
        item.classList.add('selected');
        pickerSelected.id   = item.dataset.mediaId;
        pickerSelected.url  = item.dataset.mediaUrl;
        pickerSelected.path = item.dataset.mediaPath;
        if (pickerConfirm) pickerConfirm.disabled = false;
    });

    // Confirm selection
    pickerConfirm?.addEventListener('click', () => {
        if (!activePickerTarget || !pickerSelected.id) return;

        // Update hidden inputs
        const idInput   = activePickerTarget.querySelector('[data-media-id-input]');
        const pathInput = activePickerTarget.querySelector('[data-media-path-input]');
        const preview   = activePickerTarget.querySelector('[data-media-preview]');
        const label     = activePickerTarget.querySelector('[data-media-label]');

        if (idInput)   idInput.value   = pickerSelected.id;
        if (pathInput) pathInput.value = pickerSelected.path;
        if (preview) {
            preview.src = pickerSelected.url;
            preview.style.display = 'block';
            preview.closest('.adm-image-preview')?.querySelector('.adm-image-preview-placeholder')?.remove();
        }
        if (label) {
            const item = pickerGrid?.querySelector(`[data-media-id="${pickerSelected.id}"]`);
            label.textContent = item?.querySelector('.adm-media-name')?.textContent || 'Image selected';
        }

        closePicker();
    });

    // Search in picker
    pickerSearch?.addEventListener('input', () => {
        const q = pickerSearch.value.toLowerCase();
        pickerGrid?.querySelectorAll('.adm-media-item').forEach((item) => {
            const name = item.querySelector('.adm-media-name')?.textContent?.toLowerCase() || '';
            item.style.display = name.includes(q) ? '' : 'none';
        });
    });

    // Quick upload inside picker
    pickerUpload?.addEventListener('change', () => {
        if (!pickerUpload.files[0]) return;
        const form = document.getElementById('mediaPickerUploadForm');
        if (!form) return;
        form.submit();
    });

    /* ==== 6. Delete Confirmation ==== */
    document.querySelectorAll('[data-confirm-delete]').forEach((btn) => {
        btn.addEventListener('click', (e) => {
            const msg = btn.dataset.confirmDelete || 'Are you sure you want to delete this?';
            if (!confirm(msg)) e.preventDefault();
        });
    });

    /* ==== 7. Slug Auto-generate ==== */
    const nameInput = document.getElementById('fieldName');
    const slugInput = document.getElementById('fieldSlug');

    if (nameInput && slugInput && !slugInput.dataset.manual) {
        nameInput.addEventListener('input', () => {
            const slug = nameInput.value
                .toLowerCase()
                .trim()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-');
            slugInput.value = slug;
        });

        slugInput.addEventListener('input', () => {
            slugInput.dataset.manual = 'true';
        });
    }

    /* ==== 8. Form: prevent accidental double-submit ==== */
    document.querySelectorAll('form[data-single-submit]').forEach((form) => {
        form.addEventListener('submit', () => {
            const btn = form.querySelector('[type="submit"]');
            if (btn) {
                btn.disabled = true;
                btn.textContent = btn.dataset.loadingText || 'Saving...';
            }
        });
    });

});
