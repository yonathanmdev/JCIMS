document.addEventListener('DOMContentLoaded', function() {
    

    // ============================================================
    // 1. EDIT — ሞዳሉን መረጃ ሞልቶ መክፈት
    // ============================================================
    document.addEventListener('click', function(e) {
    const btn = e.target.closest('.edit-org');
    if (btn) {
        const id          = btn.getAttribute('data-id');
        const branch_id   = btn.getAttribute('data-branch-id');
        const name        = btn.getAttribute('data-name');
        const alt_name    = btn.getAttribute('data-alt-name')    || '';
        const phone_number = btn.getAttribute('data-phone-number') || '';
        const postal_code = btn.getAttribute('data-postal-code') || '';
        const logo_url    = btn.getAttribute('data-logo-url')    || '';
        const description = btn.getAttribute('data-description') || '';

        document.getElementById('edit_org_id').value          = id;
        document.getElementById('edit_org_branch_id').value   = branch_id;
        document.getElementById('edit_org_name').value        = name;
        document.getElementById('edit_org_alt_name').value    = alt_name;      // ✅ fixed
        document.getElementById('edit_org_description').value = description;   // ✅ fixed
        document.getElementById('edit_org_phone_number').value = phone_number;   // ✅ fixed
        document.getElementById('edit_org_postal_code').value = postal_code;   // ✅ fixed

        // Show current logo preview if available
        const preview = document.getElementById('current_logo_preview');
        if (preview) {
            if (logo_url) {
                preview.href        = logo_url;
                preview.style.display = 'inline';
            } else {
                preview.style.display = 'none';
            }
        }

        // Store logo_url in hidden field for controller
        const logoHidden = document.getElementById('edit_org_logo_url');
        if (logoHidden) logoHidden.value = logo_url;

        $('#editOrgModal').modal('show');
    }
});

    // ============================================================
    // 2. EDIT — የተስተካከለውን መረጃ መላክ
    // ============================================================
    const editForm = document.getElementById('editOrgForm');
    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData  = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');

            submitBtn.disabled  = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> በማደስ ላይ...';

            fetch(BASE_URL + '/?action=update-organization-process', {
                method: 'POST',
                body: formData,
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json' }
            })
            .then(response => {
                if (!response.ok) {
                    if (response.status === 403) {
                        return response.text().then(text => {
                            try {
                                const data = JSON.parse(text);
                                if (data.message === 'access_denied') throw new Error('ACCESS_DENIED');
                            } catch (e) {}
                            throw new Error('FORBIDDEN');
                        });
                    }
                    throw new Error(`HTTP ${response.status}`);
                }
                return response.text().then(text => {
                    try {
                        return JSON.parse(text);
                    } catch (err) {
                        throw new Error("ሰርቨሩ የላከው መረጃ ትክክል አይደለም፦ " + text);
                    }
                });
            })
            .then(data => {
                if (data.status === 'success') {
                    $('#editOrgModal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'ተሳክቷል!',
                        text: data.message,
                        timer: 2000
                    }).then(() => location.reload());
                } else {
                    Swal.fire('ስህተት', data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error Details:', error);
                if (error.message === 'ACCESS_DENIED' || error.message === 'FORBIDDEN') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'መግባት አልተሳካም',
                        text: 'መግባት አልተሳካም ወይም ፈቃድ የለዎትም። እባክዎ እንደገና ይግቡ።',
                        confirmButtonText: 'ወደ መግቢያ ገጽ ሂድ'
                    }).then(() => {
                        window.location.href = BASE_URL + '/login';
                    });
                } else {
                    Swal.fire('ስህተት', 'መረጃውን ማደስ አልተቻለም። እባክዎ ኮንሶሉን (F12) ይፈትሹ።', 'error');
                }
            })
            .finally(() => {
                submitBtn.disabled  = false;
                submitBtn.innerHTML = 'አድስ (Update)';
            });
        });
    }

    // ============================================================
    // 3. DELETE — SweetAlert2 confirmation + soft delete
    // ============================================================
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.delete-org');
        if (!btn) return;

        const id   = btn.getAttribute('data-id');
        const name = btn.getAttribute('data-name');

        Swal.fire({
            title: 'እርግጠኛ ነዎት?',
            text: `"${name}"ን መሰረዝ ይፈልጋሉ!`,
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'አዎ, ሰርዝ!',
            cancelButtonText: 'ዝጋ'
        }).then((result) => {
            if (!result.isConfirmed) return;

            // Show loading while deleting
            Swal.fire({
                title: 'በመሰረዝ ላይ...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            fetch(BASE_URL + '/?action=delete-organization-process', {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ id: id,
                    type: 'org'
                 })
            })
            .then(response => {
                if (!response.ok) {
                    if (response.status === 403) throw new Error('FORBIDDEN');
                    throw new Error(`HTTP ${response.status}`);
                }
                return response.text().then(text => {
                    try {
                        return JSON.parse(text);
                    } catch (err) {
                        throw new Error("ሰርቨሩ የላከው መረጃ ትክክል አይደለም፦ " + text);
                    }
                });
            })
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'ተሰርዟል!',
                        text: `"${name}" ተሰርዟል።`,
                        timer: 2000
                    }).then(() => {
                        // Remove the row from table without full reload
                        const row = document.getElementById(`row-${id}`);
                        if (row) row.remove();
                    });
                } else {
                    Swal.fire('ስህተት', data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Delete Error:', error);
                if (error.message === 'FORBIDDEN') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'መግባት አልተሳካም',
                        text: 'ፈቃድ የለዎትም። እባክዎ እንደገና ይግቡ።',
                        confirmButtonText: 'ወደ መግቢያ ገጽ ሂድ'
                    }).then(() => {
                        window.location.href = BASE_URL + '/login';
                    });
                } else {
                    Swal.fire('ስህተት', 'መሰረዝ አልተቻለም። እባክዎ ኮንሶሉን (F12) ይፈትሹ።', 'error');
                }
            });
        });
    });

    
});