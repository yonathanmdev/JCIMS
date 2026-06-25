// ============================================================
// UNIVERSAL DELETE/PURGE MODAL — use confirmDelete() anywhere
// ============================================================
function confirmDelete({
    endpoint,
    id,
    name,
    type = null,
    task,
    title,
    warning,
    confirmText,
    successTitle,
    successText,
    onSuccess,
    requireReason   = true,
    requirePassword = true,
   
}) {
    const isDelete = (task === 'delete');
    const iconClass = isDelete ? 'fa-trash-alt' : 'fa-undo';
    Swal.fire({
        title: title || `"${name}" ይሰረዝ?`,
        html: `
            <div style="text-align:center; margin-bottom:16px;">
                <div style="
                    width:70px; height:70px; border-radius:50%;
                    background:linear-gradient(135deg,#ff4757,#c0392b);
                    display:flex; align-items:center; justify-content:center;
                    margin:0 auto 12px;
                    box-shadow:0 4px 15px rgba(220,53,69,0.4);
                ">
                
                    <i class="fas ${iconClass}" style="color:#fff; font-size:28px;"></i>
                </div>
                <p style="color:#c0392b; font-weight:700; font-size:15px; margin:0;">
                    
                </p>
            </div>

            ${requireReason ? `
            <div style="
                background:#fff5f5; border:1px solid #ffcccc;
                border-radius:10px; padding:16px; margin-bottom:16px;
                text-align:left;
            ">
                <label style="
                    display:block; font-size:13px; font-weight:600;
                    color:#555; margin-bottom:6px;
                ">
                    <i class="fas fa-comment-alt" style="color:#dc3545; margin-right:5px;"></i>
                    ምክንያት <span style="color:#dc3545;">*</span>
                </label>
                <textarea
                    id="swalReason"
                    rows="3"
                    placeholder="ምክንያት ያስገቡ..."
                    style="
                        width:100%; padding:10px 12px;
                        border:1.5px solid #ffaaaa; border-radius:8px;
                        font-size:13px; resize:none; outline:none;
                        background:#fff; color:#333;
                        transition:border-color 0.2s;
                        box-sizing:border-box;
                    "
                    onfocus="this.style.borderColor='#dc3545'"
                    onblur="this.style.borderColor='#ffaaaa'"
                ></textarea>
            </div>` : ''}

            ${requirePassword ? `
            <div style="
                background:#fff5f5; border:1px solid #ffcccc;
                border-radius:10px; padding:16px; margin-bottom:16px;
                text-align:left;
            ">
                <label style="
                    display:block; font-size:13px; font-weight:600;
                    color:#555; margin-bottom:6px;
                ">
                    <i class="fas fa-lock" style="color:#dc3545; margin-right:5px;"></i>
                    ሚስጥራዊ ቁጥር (Password) <span style="color:#dc3545;">*</span>
                </label>
                <div style="position:relative;">
                    <input
                        type="password"
                        id="swalPassword"
                        autocomplete="new-password"
                        placeholder="የእርስዎን ፓስዋርድ ያስገቡ"
                        style="
                            width:100%; padding:10px 40px 10px 12px;
                            border:1.5px solid #ffaaaa; border-radius:8px;
                            font-size:13px; outline:none;
                            background:#fff; color:#333;
                            transition:border-color 0.2s;
                            box-sizing:border-box;
                        "
                        onfocus="this.style.borderColor='#dc3545'"
                        onblur="this.style.borderColor='#ffaaaa'"
                    >
                    <i
                        class="fas fa-eye"
                        id="swalTogglePassword"
                        style="
                            position:absolute; right:12px; top:50%;
                            transform:translateY(-50%);
                            cursor:pointer; color:#aaa; font-size:14px;
                        "
                        onclick="
                            const inp  = document.getElementById('swalPassword');
                            const icon = document.getElementById('swalTogglePassword');
                            if (inp.type === 'password') {
                                inp.type = 'text';
                                icon.classList.replace('fa-eye','fa-eye-slash');
                            } else {
                                inp.type = 'password';
                                icon.classList.replace('fa-eye-slash','fa-eye');
                            }
                        "
                    ></i>
                </div>
            </div>` : ''}

            <div style="
                margin-top:4px; padding:10px 14px;
                background:#fff3cd; border:1px solid #ffc107;
                border-radius:8px; font-size:12px; color:#856404;
                text-align:left;
            ">
                <i class="fas fa-exclamation-triangle" style="margin-right:5px;"></i>
                ${warning || `<strong>"${name}"</strong> ን ከስርዓቱ ለማስወገድ ነው።`}
            </div>
        `,
        icon: false,
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor:  '#6c757d',
        confirmButtonText:  confirmText || '<i class="fas fa-trash-alt"></i> አዎ፣ ሰርዝ!',
        cancelButtonText:   '<i class="fas fa-times"></i> ተመለስ',
        customClass: {
            popup:         'purge-popup',
            confirmButton: 'purge-confirm-btn',
            cancelButton:  'purge-cancel-btn',
        },
        buttonsStyling: true,
        focusConfirm: false,
        didOpen: () => {
            const first = document.getElementById('swalReason')
                       || document.getElementById('swalPassword');
            if (first) first.focus();
        },
        preConfirm: () => {
            const reason   = requireReason   ? document.getElementById('swalReason')?.value.trim()   : null;
            const password = requirePassword ? document.getElementById('swalPassword')?.value.trim() : null;

            if (requireReason && !reason) {
                Swal.showValidationMessage('<i class="fas fa-exclamation-circle"></i> እባክዎ ምክንያት ያስገቡ');
                document.getElementById('swalReason').focus();
                return false;
            }
            if (requireReason && reason.length < 10) {
                Swal.showValidationMessage('<i class="fas fa-exclamation-circle"></i> ምክንያቱ ቢያንስ 10 ፊደል መሆን አለበት');
                document.getElementById('swalReason').focus();
                return false;
            }
            if (requirePassword && !password) {
                Swal.showValidationMessage('<i class="fas fa-exclamation-circle"></i> እባክዎ ፓስዋርድ ያስገቡ');
                document.getElementById('swalPassword').focus();
                return false;
            }

            return { password, reason };
        }
    }).then(result => {
        if (!result.isConfirmed) return;

        Swal.fire({
            title: 'በመሰረዝ ላይ...',
            html: '<p style="color:#666;">እባክዎ ይጠብቁ...</p>',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => Swal.showLoading()
        });

        fetch(BASE_URL + '/?action=' + endpoint, {
    method: 'POST',
    credentials: 'same-origin',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
        id,
        confirm_password: result.value.password ?? null,
        reason:           result.value.reason   ?? null,
        type:            type        ?? null,
    })
})
.then(res => {
    if (!res.ok) {
        throw new Error('HTTP error: ' + res.status);
    }
    return res.json();
})
.then(data => {
    if (data.status === 'success') {
        Swal.fire({
            title: successTitle || 'ተሰርዟል!',
            html: `
                <p style="color:#28a745; font-weight:600;">
                    <i class="fas fa-check-circle fa-2x"></i><br><br>
                    ${successText || 'በትክክል ተሰርዟል።'}
                </p>`,
            icon: false,
            confirmButtonColor: '#28a745',
            confirmButtonText:  'እሺ'
        }).then(() => {
            if (typeof onSuccess === 'function') onSuccess();
        });
    } else {
        // ← this is what should fire for your case
        Swal.fire({
            title: 'ስህተት!',
            html:  `<p style="color:#dc3545;">${data.message}</p>`,
            icon:  'error',
            confirmButtonColor: '#dc3545'
        });
    }
})
.catch(err => {
    console.error('CATCH FIRED:', err);
    console.error('ERROR TYPE:', err.name);
    console.error('ERROR MESSAGE:', err.message);

    Swal.close(); // ← also close here
    Swal.fire({
        title: 'ስህተት!',
        text:  'መሰረዝ አልተቻለም። ሰርቨሩ ምላሽ አልሰጠም።',
        icon:  'error',
        confirmButtonColor: '#dc3545'
    });
});
    });
}
