document.addEventListener('DOMContentLoaded', function() {
    

    // ============================================================
    // 1. EDIT — ሞዳሉን መረጃ ሞልቶ መክፈት
    // ============================================================
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.edit-director');
        if (btn) {
            const id   = btn.getAttribute('data-id');
            const name = btn.getAttribute('data-name');
            
            document.getElementById('edit_director_id').value  = id;
            document.getElementById('edit_director_name').value = name;

            $('#editDirectorModal').modal('show');
        }
    });

    // ============================================================
    // 2. EDIT — የተስተካከለውን መረጃ መላክ
    // ============================================================
    const editForm = document.getElementById('editDirectorForm');
    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData  = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');

            submitBtn.disabled  = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> በማደስ ላይ...';

            fetch(BASE_URL + '/?action=update-director-process', {
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
                    $('#editDirectorModal').modal('hide');
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

    document.addEventListener('click', function (e) {

    // ── Delete position
        const deleteDirector = e.target.closest('.delete-director');
        if (deleteDirector) {
            confirmDelete({
                endpoint:    'delete-director-process',
                id:          deleteDirector.dataset.id,
                name:        deleteDirector.dataset.name,
                task:        'delete',
                title:       `"${deleteDirector.dataset.name}" ይሰረዝ?`,
                warning:     `<strong>"${deleteDirector.dataset.name}ን"</strong> ከቡድን መሪ ዝርዝር ለማስወገድ ነው።`,
                confirmText: '<i class="fas fa-user-times"></i> አዎ፣ ሰርዝ!',
                successText: 'ዳይሬክትሩ ተሰርዟል።',
                requireReason:   true,
                requirePassword: true,
                onSuccess: () => document.getElementById(`row-${deleteDirector.dataset.id}`)?.remove()
            });
            return;
        }
  });
    
});