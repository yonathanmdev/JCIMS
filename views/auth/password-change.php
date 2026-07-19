<!DOCTYPE html>
<html lang="am">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ፓስወርድ ይቀይሩ | JCIMS</title>
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, "Segoe UI", Roboto, "Noto Sans Ethiopic", Arial, sans-serif;
            background: #f4f6f5;
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }
        .change-wrap {
            max-width: 460px;
            width: 100%;
        }
        .change-brand {
            text-align: center;
            margin-bottom: 1.25rem;
        }
        .change-brand .logo-badge {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: #2d6a4f;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0.75rem;
        }
        .change-brand .logo-badge svg {
            width: 26px;
            height: 26px;
            stroke: #fff;
        }
        .change-brand h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e2d27;
            margin: 0;
        }
        .change-card {
            background: #fff;
            border-radius: 10px;
            padding: 2.25rem 2.5rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            border-top: 4px solid #2d6a4f;
        }
        .change-card h2 {
            font-size: 1.35rem;
            font-weight: 700;
            color: #2d6a4f;
            margin: 0 0 0.4rem;
        }
        .change-card p.subtitle {
            color: #6c757d;
            font-size: 0.92rem;
            margin: 0 0 1.5rem;
        }
        .form-group {
            margin-bottom: 1.1rem;
        }
        .form-label {
            display: block;
            font-size: 0.88rem;
            font-weight: 500;
            color: #33403a;
            margin-bottom: 0.35rem;
        }
        .required-mark {
            color: #c0392b;
            margin-left: 2px;
        }
        .form-control {
            display: block;
            width: 100%;
            height: 50px;
            padding: 0.6rem 2.75rem 0.6rem 0.95rem;
            border: 1px solid #dde3e0;
            border-radius: 8px;
            font-size: 1rem;
            font-family: inherit;
            color: #1e2d27;
            background: #fff;
        }
        .form-control:focus {
            outline: none;
            border-color: #2d6a4f;
            box-shadow: 0 0 0 3px rgba(45,106,79,0.12);
        }
        .form-control.is-invalid {
            border-color: #c0392b;
        }
        .form-control.is-invalid:focus {
            border-color: #c0392b;
            box-shadow: 0 0 0 3px rgba(192,57,43,0.12);
        }
        .password-field {
            position: relative;
            width: 100%;
        }
        .password-field .toggle-visibility {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: none;
            padding: 0;
            display: flex;
            align-items: center;
            color: #9aa5a0;
            cursor: pointer;
        }
        .password-field .toggle-visibility svg {
            width: 19px;
            height: 19px;
        }
        .password-field .toggle-visibility:hover {
            color: #2d6a4f;
        }
        .field-hint {
            font-size: 0.78rem;
            color: #8a938e;
            margin: 0.35rem 0 0;
        }

        /* Password requirement checklist */
        .req-list {
            list-style: none;
            padding: 0;
            margin: 0.5rem 0 0;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.3rem 0.75rem;
        }
        .req-list li {
            font-size: 0.78rem;
            color: #9aa5a0;
            display: flex;
            align-items: center;
            gap: 0.4rem;
            transition: color 0.15s ease;
        }
        .req-list li svg {
            width: 13px;
            height: 13px;
            flex-shrink: 0;
        }
        .req-list li .dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            border: 1.5px solid #c3cac7;
            flex-shrink: 0;
        }
        .req-list li .check {
            display: none;
        }
        .req-list li.met {
            color: #2d6a4f;
        }
        .req-list li.met .dot {
            display: none;
        }
        .req-list li.met .check {
            display: block;
        }

        .match-hint {
            font-size: 0.8rem;
            margin: 0.4rem 0 0;
            min-height: 1.1rem;
        }
        .match-hint.ok { color: #2d6a4f; }
        .match-hint.bad { color: #c0392b; }

        .btn-warka {
            display: block;
            width: 100%;
            background: #2d6a4f;
            border: 1px solid #2d6a4f;
            color: #fff;
            height: 46px;
            border-radius: 8px;
            font-size: 0.98rem;
            font-weight: 500;
            font-family: inherit;
            margin-top: 0.5rem;
            cursor: pointer;
        }
        .btn-warka:hover {
            background: #1e4d38;
            border-color: #1e4d38;
        }
        .btn-warka:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .alert {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.88rem;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            margin-bottom: 1.1rem;
        }
        .alert-danger {
            background: #fdecea;
            color: #c0392b;
            border: 1px solid #f5c6c0;
        }
        .alert svg {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
        }
    </style>
</head>
<body>

<div class="change-wrap">

    <div class="change-brand">
        <div class="logo-badge">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
            </svg>
        </div>
        <h1>JCIMS</h1>
    </div>

    <div class="change-card">
        <h2>ፓስወርድ ይቀይሩ</h2>
        <p class="subtitle">ይህ የመጀመሪያ Login ሙከራዎ ስለሆነ አዲስ ፓስወርድ ማስገባት አለብዎት።</p>

        <?php if (!empty($_SESSION['error'])): ?>
            <div class="alert alert-danger" role="alert">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <span><?= htmlspecialchars($_SESSION['error']) ?></span>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <form method="POST" action="<?= $_ENV['BASE_URL'] ?>/password-change-process" id="passwordChangeForm" novalidate>

            <div class="form-group">
                <label for="current_password" class="form-label">የአሁኑ ፓስወርድ <span class="required-mark">*</span></label>
                <div class="password-field">
                    <input type="password" class="form-control" id="current_password" name="current_password" required autocomplete="current-password">
                    <button type="button" class="toggle-visibility" data-target="current_password" aria-label="ፓስወርድ አሳይ/ደብቅ">
                        <svg class="icon-eye" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                        <svg class="icon-eye-off" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none">
                            <path d="M17.94 17.94A10.94 10.94 0 0 1 12 20c-7 0-11-8-11-8a18.6 18.6 0 0 1 5.06-5.94M9.9 4.24A10.94 10.94 0 0 1 12 4c7 0 11 8 11 8a18.6 18.6 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                            <line x1="1" y1="1" x2="23" y2="23"/>
                        </svg>
                    </button>
                </div>
                <p class="field-hint">የመለያዎን አሁን ያለውን ፓስወርድ ያስገቡ</p>
            </div>

            <div class="form-group">
                <label for="new_password" class="form-label">አዲስ ፓስወርድ <span class="required-mark">*</span></label>
                <div class="password-field">
                    <input type="password" class="form-control" id="new_password" name="new_password" minlength="8" required autocomplete="new-password">
                    <button type="button" class="toggle-visibility" data-target="new_password" aria-label="ፓስወርድ አሳይ/ደብቅ">
                        <svg class="icon-eye" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                        <svg class="icon-eye-off" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none">
                            <path d="M17.94 17.94A10.94 10.94 0 0 1 12 20c-7 0-11-8-11-8a18.6 18.6 0 0 1 5.06-5.94M9.9 4.24A10.94 10.94 0 0 1 12 4c7 0 11 8 11 8a18.6 18.6 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                            <line x1="1" y1="1" x2="23" y2="23"/>
                        </svg>
                    </button>
                </div>

                <ul class="req-list" id="reqList">
                    <li id="req-length"><span class="dot"></span><svg class="check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> ቢያንስ 8 ፊደላት/ቁጥሮች</li>
                    <li id="req-upper"><span class="dot"></span><svg class="check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> ትልቅ ፊደል (A-Z)</li>
                    <li id="req-lower"><span class="dot"></span><svg class="check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> ትንሽ ፊደል (a-z)</li>
                    <li id="req-number"><span class="dot"></span><svg class="check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> ቁጥር (0-9)</li>
                    <li id="req-special"><span class="dot"></span><svg class="check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> ልዩ ምልክት (!@#$%)</li>
                </ul>
            </div>

            <div class="form-group">
                <label for="confirm_password" class="form-label">አዲሱን ፓስወርድ ያረጋግጡ <span class="required-mark">*</span></label>
                <div class="password-field">
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" minlength="8" required autocomplete="new-password">
                    <button type="button" class="toggle-visibility" data-target="confirm_password" aria-label="ፓስወርድ አሳይ/ደብቅ">
                        <svg class="icon-eye" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                        <svg class="icon-eye-off" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none">
                            <path d="M17.94 17.94A10.94 10.94 0 0 1 12 20c-7 0-11-8-11-8a18.6 18.6 0 0 1 5.06-5.94M9.9 4.24A10.94 10.94 0 0 1 12 4c7 0 11 8 11 8a18.6 18.6 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                            <line x1="1" y1="1" x2="23" y2="23"/>
                        </svg>
                    </button>
                </div>
                <p class="field-hint">ከላይ ያስገቡትን አዲስ ፓስወርድ በድጋሚ ያረጋግጡ</p>
                <p class="match-hint" id="matchHint"></p>
            </div>

            <button type="submit" class="btn-warka" id="submitBtn">ፓስወርድ ቀይር</button>
        </form>
    </div>
</div>

 <script nonce="<?php echo $GLOBALS['nonce']; ?>">

document.querySelectorAll('.toggle-visibility').forEach(function (btn) {
    btn.addEventListener('click', function () {
        var input = document.getElementById(btn.getAttribute('data-target'));
        var eyeIcon = btn.querySelector('.icon-eye');
        var eyeOffIcon = btn.querySelector('.icon-eye-off');
        if (input.type === 'password') {
            input.type = 'text';
            eyeIcon.style.display = 'none';
            eyeOffIcon.style.display = 'block';
        } else {
            input.type = 'password';
            eyeIcon.style.display = 'block';
            eyeOffIcon.style.display = 'none';
        }
    });
});

var currentPass = document.getElementById('current_password');
var newPass = document.getElementById('new_password');
var confirmPass = document.getElementById('confirm_password');
var matchHint = document.getElementById('matchHint');
var submitBtn = document.getElementById('submitBtn');
var form = document.getElementById('passwordChangeForm');

var passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z\d]).+$/;

function markInvalid(input) {
    input.classList.add('is-invalid');
}
function clearInvalid(input) {
    input.classList.remove('is-invalid');
}
[currentPass, newPass, confirmPass].forEach(function (input) {
    input.addEventListener('input', function () {
        clearInvalid(input);
    });
});

var requirements = {
    'req-length':  function (v) { return v.length >= 8; },
    'req-upper':   function (v) { return /[A-Z]/.test(v); },
    'req-lower':   function (v) { return /[a-z]/.test(v); },
    'req-number':  function (v) { return /\d/.test(v); },
    'req-special': function (v) { return /[^a-zA-Z\d]/.test(v); }
};

function updateRequirementList() {
    var value = newPass.value;
    Object.keys(requirements).forEach(function (id) {
        var el = document.getElementById(id);
        if (requirements[id](value)) {
            el.classList.add('met');
        } else {
            el.classList.remove('met');
        }
    });
}

newPass.addEventListener('input', function () {
    updateRequirementList();
    checkMatch();
});

function checkMatch() {
    if (confirmPass.value === '') {
        matchHint.textContent = '';
        matchHint.className = 'match-hint';
        return true;
    }
    if (newPass.value === confirmPass.value) {
        matchHint.textContent = 'ትክክል';
        matchHint.className = 'match-hint ok';
        return true;
    }
    matchHint.textContent = 'ፓስወርዶቹ አይመሳሰሉም';
    matchHint.className = 'match-hint bad';s
    return false;
}

confirmPass.addEventListener('input', checkMatch);

form.addEventListener('submit', function (e) {
    if (currentPass.value === '') {
        e.preventDefault();
        markInvalid(currentPass);
        matchHint.textContent = 'የአሁኑን ፓስወርድ ያስገቡ';
        matchHint.className = 'match-hint bad';
        return;
    }
    if (newPass.value.length < 8) {
        e.preventDefault();
        markInvalid(newPass);
        matchHint.textContent = 'ፓስወርዱ ቢያንስ 8 ፊደላት/ቁጥሮች ሊኖረው ይገባል';
        matchHint.className = 'match-hint bad';
        return;
    }
    if (!passwordPattern.test(newPass.value)) {
        e.preventDefault();
        markInvalid(newPass);
        matchHint.textContent = 'ከላይ ያሉትን የፓስወርድ መስፈርቶች ያሟሉ';
        matchHint.className = 'match-hint bad';
        return;
    }
    if (newPass.value === currentPass.value) {
        e.preventDefault();
        markInvalid(newPass);
        markInvalid(currentPass);
        matchHint.textContent = 'አዲሱ ፓስወርድ ከነባሩ የተለየ መሆን አለበት';
        matchHint.className = 'match-hint bad';
        return;
    }
    if (confirmPass.value === '') {
        e.preventDefault();
        markInvalid(confirmPass);
        matchHint.textContent = 'እባክዎ አዲሱን ፓስወርድ በድጋሚ ያስገቡ';
        matchHint.className = 'match-hint bad';
        return;
    }
    if (!checkMatch()) {
        markInvalid(confirmPass);
        e.preventDefault();
        return;
    }
    submitBtn.disabled = true;
    submitBtn.textContent = 'በመቀየር ላይ...';
});
</script>

</body>
</html>