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
    reasonOptions   = null,   // ← NEW: array of {value, label}

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

${requireReason ? (reasonOptions ? `
<div style="background:#fff5f5; border:1px solid #ffcccc; border-radius:10px; padding:16px; margin-bottom:16px; text-align:left;">
    <label style="display:block; font-size:13px; font-weight:600; color:#555; margin-bottom:6px;">
        <i class="fas fa-comment-alt" style="color:#dc3545; margin-right:5px;"></i>
        ምክንያት <span style="color:#dc3545;">*</span>
    </label>
    <select
        id="swalReasonSelect"
        style="width:100%; padding:10px 12px; border:1.5px solid #ffaaaa; border-radius:8px; font-size:13px; outline:none; background:#fff; color:#333; box-sizing:border-box;"
    >
        <option value="">-- ይምረጡ --</option>
        ${reasonOptions.map(opt => `<option value="${opt.value}">${opt.label}</option>`).join('')}
        <option value="other">ሌላ (Other)</option>
    </select>
    <textarea
        id="swalReasonOther"
        rows="3"
        placeholder="ምክንያት ያስገቡ..."
        style="width:100%; padding:10px 12px; border:1.5px solid #ffaaaa; border-radius:8px; font-size:13px; resize:none; outline:none; background:#fff; color:#333; transition:border-color 0.2s; box-sizing:border-box; margin-top:10px; display:none;"
    ></textarea>
</div>` : `
<div style="background:#fff5f5; border:1px solid #ffcccc; border-radius:10px; padding:16px; margin-bottom:16px; text-align:left;">
    <label style="display:block; font-size:13px; font-weight:600; color:#555; margin-bottom:6px;">
        <i class="fas fa-comment-alt" style="color:#dc3545; margin-right:5px;"></i>
        ምክንያት <span style="color:#dc3545;">*</span>
    </label>
    <textarea
        id="swalReason"
        rows="3"
        placeholder="ምክንያት ያስገቡ..."
        style="width:100%; padding:10px 12px; border:1.5px solid #ffaaaa; border-radius:8px; font-size:13px; resize:none; outline:none; background:#fff; color:#333; transition:border-color 0.2s; box-sizing:border-box;"
    ></textarea>
</div>`) : ''}

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
                    >
                    <i
                        class="fas fa-eye"
                        id="swalTogglePassword"
                        style="
                            position:absolute; right:12px; top:50%;
                            transform:translateY(-50%);
                            cursor:pointer; color:#aaa; font-size:14px;
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
            const reasonEl   = document.getElementById('swalReason');
            const passwordEl = document.getElementById('swalPassword');
            const toggleEl   = document.getElementById('swalTogglePassword');

            // Replace inline onfocus/onblur with real listeners
            [reasonEl, passwordEl].forEach(el => {
                if (!el) return;
                el.addEventListener('focus', () => { el.style.borderColor = '#dc3545'; });
                el.addEventListener('blur',  () => { el.style.borderColor = '#ffaaaa'; });
            });
const reasonSelectEl = document.getElementById('swalReasonSelect');
const reasonOtherEl  = document.getElementById('swalReasonOther');
if (reasonSelectEl && reasonOtherEl) {
    reasonSelectEl.addEventListener('change', () => {
        if (reasonSelectEl.value === 'other') {
            reasonOtherEl.style.display = 'block';
            reasonOtherEl.focus();
        } else {
            reasonOtherEl.style.display = 'none';
        }
    });
}
            // Replace inline onclick on the eye icon
            if (toggleEl && passwordEl) {
                toggleEl.addEventListener('click', () => {
                    if (passwordEl.type === 'password') {
                        passwordEl.type = 'text';
                        toggleEl.classList.replace('fa-eye', 'fa-eye-slash');
                    } else {
                        passwordEl.type = 'password';
                        toggleEl.classList.replace('fa-eye-slash', 'fa-eye');
                    }
                });
            }

            const first = reasonEl || passwordEl;
            if (first) first.focus();
        },
preConfirm: () => {
    const password = requirePassword ? document.getElementById('swalPassword')?.value.trim() : null;
    let reason = null;

    if (requireReason) {
        if (reasonOptions) {
            const selectEl = document.getElementById('swalReasonSelect');
            const otherEl  = document.getElementById('swalReasonOther');
            const selected = selectEl?.value;

            if (!selected) {
                Swal.showValidationMessage('<i class="fas fa-exclamation-circle"></i> እባክዎ ምክንያት ይምረጡ');
                selectEl.focus();
                return false;
            }

            if (selected === 'other') {
                reason = otherEl?.value.trim();
                if (!reason) {
                    Swal.showValidationMessage('<i class="fas fa-exclamation-circle"></i> እባክዎ ምክንያት ያስገቡ');
                    otherEl.focus();
                    return false;
                }
                if (reason.length < 10) {
                    Swal.showValidationMessage('<i class="fas fa-exclamation-circle"></i> ምክንያቱ ቢያንስ 10 ፊደል መሆን አለበት');
                    otherEl.focus();
                    return false;
                }
            } else {
                reason = selectEl.options[selectEl.selectedIndex].value;
            }
        } else {
            reason = document.getElementById('swalReason')?.value.trim();
            if (!reason) {
                Swal.showValidationMessage('<i class="fas fa-exclamation-circle"></i> እባክዎ ምክንያት ያስገቡ');
                document.getElementById('swalReason').focus();
                return false;
            }
            if (reason.length < 10) {
                Swal.showValidationMessage('<i class="fas fa-exclamation-circle"></i> ምክንያቱ ቢያንስ 10 ፊደል መሆን አለበት');
                document.getElementById('swalReason').focus();
                return false;
            }
        }
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

            Swal.close();
            Swal.fire({
                title: 'ስህተት!',
                text:  'መሰረዝ አልተቻለም። ሰርቨሩ ምላሽ አልሰጠም።',
                icon:  'error',
                confirmButtonColor: '#dc3545'
            });
        });
    });
}