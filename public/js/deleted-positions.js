// public/js/deleted-positions.js

document.addEventListener('DOMContentLoaded', function () {
    

    // ============================================================
    // RESTORE
    // ============================================================
  // Restore position
document.addEventListener('click', function(e) {
    const btn = e.target.closest('.restore-position');
    if (!btn) return;

    const id           = btn.getAttribute('data-id');
    const name         = btn.getAttribute('data-name');
    const affectedCount = parseInt(btn.getAttribute('data-affected')) || 0;

    Swal.fire({
        title: 'መደቡን መልስ?',
        html: affectedCount > 0
            ? `"<strong>${name}</strong>" እና <strong>${affectedCount}</strong> ሰራተኛ አብረው ይመለሳሉ።`
            : `"<strong>${name}</strong>" ይመለሳል።`,
        icon: 'question',
        showCancelButton:    true,
        confirmButtonColor:  '#28a745',
        cancelButtonColor:   '#6c757d',
        confirmButtonText:   '<i class="fas fa-undo me-1"></i> አዎን፣ መልስ',
        cancelButtonText:    'ይቅር',
    }).then(result => {
        if (!result.isConfirmed) return;

        fetch(BASE_URL + '/?action=restore-position', {
            method:  'POST',
            headers: { 'Content-Type': 'application/json' },
            body:    JSON.stringify({ id })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                Swal.fire({
                    icon:   'success',
                    title:  'ተሳክቷል!',
                    text:   data.message,
                    timer:  2000
                }).then(() => location.reload());
            } else {
                Swal.fire('ስህተት', data.message, 'error');
            }
        })
       .catch(error => {
    console.error('Error:', error);
    Swal.fire('ስህተት', error.message || 'እንደገና ይሞክሩ።', 'error');
});
    });
});
    // ============================================================
// PURGE — admin must type password to confirm
// ============================================================
document.addEventListener('click', function (e) {
    const btn = e.target.closest('.purge-position');
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

        fetch(BASE_URL + '/?action=purge-position', {
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