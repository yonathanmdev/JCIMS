document.addEventListener('DOMContentLoaded', function() {
    

    // ============================================================
    // 1. EDIT — ሞዳሉን መረጃ ሞልቶ መክፈት
    // ============================================================
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.edit-branch');
        if (btn) {
            const id   = btn.getAttribute('data-id');
            const name = btn.getAttribute('data-name');
            const alternateName = btn.getAttribute('data-alternate-name');
            const phone_number = btn.getAttribute('data-phone-number');
            const postal_code = btn.getAttribute('data-postal-code');
            const logo_url = btn.getAttribute('data-logo-url');

            document.getElementById('edit_branch_id').value  = id;
            document.getElementById('edit_branch_name').value = name;
            document.getElementById('edit_branch_alt_name').value = alternateName;
            document.getElementById('edit_branch_phone_number').value = phone_number;
            document.getElementById('edit_branch_postal_code').value = postal_code;
            document.getElementById('edit_branch_logo_url').value = logo_url;



              // Show current logo preview if available
        const preview = document.getElementById('edit_current_logo_preview');
        if (preview) {
            if (logo_url) {
                preview.href        = logo_url;
                preview.style.display = 'inline';
            } else {
                preview.style.display = 'none';
            }
        }

        // Store logo_url in hidden field for controller
        const logoHidden = document.getElementById('edit_branch_logo_url');
        if (logoHidden) logoHidden.value = logo_url;

            $('#editBranchModal').modal('show');
        }
    });

    // ============================================================
    // 2. EDIT — የተስተካከለውን መረጃ መላክ
    // ============================================================
 // ============================================================
// 2. EDIT — የተስተካከለውን መረጃ መላክ
// ============================================================
const editForm = document.getElementById('editBranchForm');
if (editForm) {
    editForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData  = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');

        // Disable button to prevent double-clicks
        submitBtn.disabled  = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> በማደስ ላይ...';

        // Ensure BASE_URL fallback logic if it's missing a trailing slash profile
        const targetUrl = (typeof BASE_URL !== 'undefined' ? BASE_URL : '') + '/?action=update-branch-process';

        fetch(targetUrl, {
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
                $('#editBranchModal').modal('hide');
                
                // Clear file selection cleanly on successful submissions
                const fileInput = document.getElementById('edit_branch_logo');
                if (fileInput) fileInput.value = '';

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
                    window.location.href = (typeof BASE_URL !== 'undefined' ? BASE_URL : '') + '/login';
                });
            } else {
                Swal.fire('ስህተት', 'መረጃውን ማደስ አልተቻለም። እባክዎ ኮንሶሉን (F12) ይፈትሹ።', 'error');
            }
        })
        .finally(() => {
            // Re-enable and reset text precisely to match your HTML button layout label
            submitBtn.disabled  = false;
            submitBtn.innerHTML = 'አስተካክል';
        });
    });
}
   document.addEventListener('click', function (e) {

  // ── Delete branch
        const deleteButton = e.target.closest('.delete-branch');
        if (deleteButton) {
            confirmDelete({
                endpoint:    'delete-branch-process',
                id:          deleteButton.dataset.id,
                name:        deleteButton.dataset.name,
                type:        'branch',
                task:        'delete',
                title:       `"${deleteButton.dataset.name}" ይሰረዝ?`,
                warning:     `<strong>"${deleteButton.dataset.name}ን"</strong>  ከቅርንጫፎች ዝርዝር ለማስወገድ ነው።`,
                confirmText: '<i class="fas fa-user-times"></i> አዎ፣ ሰርዝ!',
                successText: 'ቅርንጣፉ ተሰርዟል።',
                requireReason:   true,
                requirePassword: true,
                onSuccess: () => document.getElementById(`row-${deleteButton.dataset.id}`)?.remove()
            });
            return;
        }
    });


});