// public/js/archived-organization.js

document.addEventListener('DOMContentLoaded', function () {
    

    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.restore-archive');
        if (!btn) return;

        const originalId = btn.dataset.originalId;
        const name       = btn.dataset.name;
        const branches   = btn.dataset.branches;
        const users      = btn.dataset.users;

        // ============================================================
        // Step 1 — Confirm restore + require admin password
        // ============================================================
        Swal.fire({
            title: 'ከ Archive መልስ?',
            html: `
                <p><strong>"${name}"</strong> ከ archive ወደ ዋናው ስርዓት ይመለስ?</p>
                <div style="display:flex; gap:8px; justify-content:center; margin:10px 0;">
                    <span class="badge bg-info text-dark">${branches} ቅርንጫፍ</span>
                    <span class="badge bg-secondary">${users} ተጠቃሚ</span>
                </div>
                <p class="text-muted" style="font-size:12px;">
                    ለማረጋገጥ የእርስዎን ሚስጥራዊ ቁጥር ያስገቡ:
                </p>
                <input id="archivePasswordInput" 
                       type="password" 
                       class="swal2-input" 
                       placeholder="Password">`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'አዎ, መልስ!',
            cancelButtonText: 'ሰርዝ',
            preConfirm: () => {
                const password = document.getElementById('archivePasswordInput').value;
                if (!password) {
                    Swal.showValidationMessage('ሚስጥራዊ ቁጥርዎን ያስገቡ');
                    return false;
                }
                return password;
            }
        }).then(result => {
            if (!result.isConfirmed) return;

            // ============================================================
            // Step 2 — Send restore request
            // ============================================================
            Swal.fire({
                title: 'በመመለስ ላይ...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            fetch(BASE_URL + '/?action=restore-from-archive', {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    original_id:      originalId,
                    confirm_password: result.value
                })
            })
            .then(res => res.text().then(text => {
                try { return JSON.parse(text); }
                catch { throw new Error('ሰርቨሩ የላከው መረጃ ትክክል አይደለም፦ ' + text); }
            }))
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire('ተመልሷል!', `"${name}" ከ archive ተመልሷል።`, 'success')
                        .then(() => {
                            // Mark row as restored without full reload
                            const row = document.getElementById(`archive-row-${btn.dataset.archiveId}`);
                            if (row) {
                                row.querySelector('.restore-archive').replaceWith(
                                    Object.assign(document.createElement('span'), {
                                        className: 'text-muted small',
                                        textContent: '—'
                                    })
                                );
                                const statusCell = row.querySelector('td:nth-child(6)');
                                if (statusCell) {
                                    statusCell.innerHTML = '<span class="badge bg-success">ተመልሷል</span>';
                                }
                            }
                        });
                } else {
                    Swal.fire('ስህተት', data.message, 'error');
                }
            })
            .catch(err => {
                console.error(err);
                Swal.fire('ስህተት', 'መመለስ አልተቻለም። እባክዎ ኮንሶሉን (F12) ይፈትሹ።', 'error');
            });
        });
    });
});