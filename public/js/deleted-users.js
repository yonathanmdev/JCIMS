// public/js/deleted-users.js

document.addEventListener('DOMContentLoaded', function () {
    

    // ============================================================
    // RESTORE
    // ============================================================
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.restore-user');
        if (!btn) return;

        const id       = btn.dataset.id;
        const name     = btn.dataset.name;

        Swal.fire({
            title: 'መልስ ያረጋግጡ?',
            html: `
                <p><strong>"${name}"</strong> ተመልሶ ይሰራ?</p>
                <div style="display:flex; gap:8px; justify-content:center; margin-top:12px;">
                       </div>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'አዎ, መልስ!',
            cancelButtonText: 'ሰርዝ'
        }).then(result => {
            if (!result.isConfirmed) return;

            Swal.fire({
                title: 'በመመለስ ላይ...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            fetch(BASE_URL + '/?action=restore-user', {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ id })
            })
            .then(res => res.text().then(text => {
                try { return JSON.parse(text); }
                catch { throw new Error('ሰርቨሩ የላከው መረጃ ትክክል አይደለም፦ ' + text); }
            }))
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire('ተመልሷል!', `"${name}" በተሳካ ሁኔታ ተመልሷል።`, 'success')
                        .then(() => document.getElementById(`row-${id}`)?.remove());
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

    // ============================================================
// PURGE — admin must type password to confirm
// ============================================================

});