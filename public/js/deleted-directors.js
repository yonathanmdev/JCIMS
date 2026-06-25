// public/js/deleted-directors.js

document.addEventListener('DOMContentLoaded', function () {
    

    // ============================================================
    // RESTORE
    // ============================================================
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.restore-director');
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

            fetch(BASE_URL + '/?action=restore-director', {
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
document.addEventListener('click', function (e) {
    const btn = e.target.closest('.purge-director');
    if (!btn) return;

    const id       = btn.dataset.id;
    const name     = btn.dataset.name;
    Swal.fire({
        title: `"${name}" በቋሚነት ይጥፋ?`,
        html: `
            <p class="text-danger fw-bold">ይህ ድርጊት ሊቀለበስ አይችልም!</p>
            <div style="display:flex; gap:8px; justify-content:center; margin:10px 0;">
                       </div>
            <p style="font-size:14px; padding-bottom:10px;">ለማረጋገጥ የእርስዎን <b>ሚስጥራዊ ቁጥር (Password)</b> ያስገቡ:</p>
            <input type="password" id="confirmPasswordInput" class="swal1-input" placeholder="Password">`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'አዎ, በቋሚነት አጥፋ!',
        cancelButtonText: 'ተመለስ',
        preConfirm: () => {
            const password = document.getElementById('confirmPasswordInput').value;
            if (!password) {
                Swal.showValidationMessage('እባክዎ ፓስዋርድ ያስገቡ');
                return false;
            }
            return password; // ይህ ለ Controller ይላካል
        }
    }).then(result => {
        if (!result.isConfirmed) return;

        Swal.fire({
            title: 'በመሰረዝ ላይ...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        fetch(BASE_URL + '/?action=purge-director', {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ 
                id: id, 
                confirm_password: result.value // ፓስዋርዱን እዚህ እንልካለን
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                Swal.fire('ተሰርዟል!', 'ቡድን መሪ በቋሚነት ተወግዷል።', 'success')
                    .then(() => document.getElementById(`row-${id}`)?.remove());
            } else {
                Swal.fire('ስህተት', data.message, 'error');
            }
        })
        .catch(err => {
            Swal.fire('ስህተት', 'መሰረዝ አልተቻለም። ሰርቨሩ ምላሽ አልሰጠም።', 'error');
        });
    });
});
});