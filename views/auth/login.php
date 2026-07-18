<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
    
<head>
    <base href="<?= $_ENV['BASE_URL'] ?>/">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Log in — Warka Hub JCIMS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:wght@300;400;500&display=swap">
    <link rel="stylesheet" href="public/plugins/fontawesome-free/css/all.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --ink:       #1a1a18;
            --ink-muted: #6b6b63;
            --surface:   #f7f5f0;
            --card:      #ffffff;
            --border:    rgba(26,26,24,0.12);
            --accent:    #2d6a4f;
            --accent-lt: #e8f5ee;
            --accent-dk: #1b4332;
            --danger:    #a32d2d;
            --danger-lt: #fcebeb;
            --radius:    14px;
        }

        html, body {
            height: 100%;
            font-family: 'DM Sans', sans-serif;
            background: var(--surface);
            color: var(--ink);
        }

        /* Subtle dot grid on right side background */
        body::after {
            content: '';
            position: fixed;
            inset: 0;
            background-image: radial-gradient(circle, rgba(26,26,24,0.06) 1px, transparent 1px);
            background-size: 28px 28px;
            pointer-events: none;
            z-index: 0;
        }

        /* — Layout — */
        .page {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1fr 1fr;
        }

        /* ─── Left panel: full banner image ─── */
        .panel-left {
            position: relative;
            overflow: hidden;
        }

        /* The banner fills the whole left panel */
        .panel-left img.banner {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: left center;
            display: block;
        }

        /* Subtle bottom scrim so the footer text is readable */
        .panel-left::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(
                to bottom,
                transparent 60%,
                rgba(13, 37, 24, 0.55) 100%
            );
            pointer-events: none;
        }

        /* Footer credit inside banner */
        .banner-footer {
            position: absolute;
            bottom: 1.5rem;
            left: 1.75rem;
            z-index: 2;
            font-size: 11px;
            color: rgba(255,255,255,0.45);
            letter-spacing: 0.04em;
        }

        /* ─── Right panel ─── */
        .panel-right {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 4rem 5rem;
            background: var(--surface);
        }

        .login-card {
            width: 100%;
            max-width: 400px;
        }

        /* Top brand row above the form */
        .card-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 2rem;
        }

        .card-brand img.logo {
            width: 38px;
            height: 38px;
            border-radius: 9px;
            object-fit: contain;
            background: var(--accent-dk);
            padding: 4px;
        }

        /* Fallback if no logo file — show icon box */
        .card-brand .brand-icon {
            width: 38px; height: 38px;
            background: var(--accent-dk);
            border-radius: 9px;
            display: flex; align-items: center; justify-content: center;
            font-size: 16px; color: #fff;
            flex-shrink: 0;
        }

        .card-brand-name {
            font-family: 'DM Serif Display', serif;
            font-size: 17px;
            color: var(--ink);
            line-height: 1.2;
        }

        .card-brand-sub {
            font-size: 11px;
            color: var(--ink-muted);
            margin-top: 1px;
        }

        .login-header {
            margin-bottom: 2rem;
        }

        .login-eyebrow {
            font-size: 11px;
            font-weight: 500;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--accent);
            margin-bottom: 0.5rem;
        }

        .login-title {
            font-family: 'DM Serif Display', serif;
            font-size: 2rem;
            color: var(--ink);
            line-height: 1.2;
        }

        .login-title span {
            font-style: italic;
            color: var(--ink-muted);
        }

        /* Amharic badge */
        .amharic-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--accent-lt);
            color: var(--accent-dk);
            font-size: 13px;
            font-weight: 500;
            padding: 5px 12px;
            border-radius: 20px;
            margin-bottom: 1.25rem;
            border: 1px solid rgba(45,106,79,0.15);
        }

        /* — Alert — */
        .alert {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            background: var(--danger-lt);
            border: 1px solid rgba(163,45,45,0.2);
            border-left: 3px solid var(--danger);
            border-radius: 10px;
            padding: 12px 14px;
            font-size: 14px;
            color: var(--danger);
            margin-bottom: 1.5rem;
            animation: slideIn 0.25s ease;
        }

        .alert i { margin-top: 2px; flex-shrink: 0; }

        .alert-close {
            margin-left: auto;
            background: none;
            border: none;
            cursor: pointer;
            color: var(--danger);
            opacity: 0.6;
            padding: 0;
            font-size: 16px;
            line-height: 1;
            flex-shrink: 0;
        }
        .alert-close:hover { opacity: 1; }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-6px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* — Form fields — */
        .field { margin-bottom: 1.25rem; }

        .field label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: var(--ink-muted);
            margin-bottom: 6px;
            letter-spacing: 0.02em;
        }

        .input-wrap { position: relative; }

        .input-wrap .field-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 14px;
            color: var(--ink-muted);
            pointer-events: none;
            transition: color 0.2s;
            z-index: 1;
        }

        .field input {
            width: 100%;
            height: 48px;
            padding: 0 14px 0 40px;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 10px;
            font-family: 'DM Sans', sans-serif;
            font-size: 15px;
            color: var(--ink);
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
            appearance: none;
        }

        .field input::placeholder { color: rgba(26,26,24,0.3); }

        .field input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(45,106,79,0.12);
        }

        .input-wrap:focus-within .field-icon { color: var(--accent); }

        /* Show/hide password toggle */
        .toggle-pw {
            position: absolute;
            right: 13px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            font-size: 14px;
            color: var(--ink-muted);
            padding: 4px;
            line-height: 1;
            z-index: 1;
        }
        .toggle-pw:hover { color: var(--accent); }

        /* — Forgot password link (standalone, below password) — */
        .forgot-link {
            display: block;
            text-align: right;
            font-size: 12px;
            color: var(--accent);
            text-decoration: none;
            margin-top: -0.75rem;
            margin-bottom: 1.25rem;
            transition: opacity 0.15s;
        }
        .forgot-link:hover { opacity: 0.7; text-decoration: underline; }

        /* — Form footer — */
        .form-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 1.5rem;
            gap: 12px;
        }

        .remember {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            user-select: none;
        }

        .remember input[type="checkbox"] {
            width: 16px; height: 16px;
            accent-color: var(--accent);
            cursor: pointer;
            flex-shrink: 0;
        }

        .remember span {
            font-size: 13px;
            color: var(--ink-muted);
        }

        .btn-signin {
            flex-shrink: 0;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--accent);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 0 24px;
            height: 48px;
            font-family: 'DM Sans', sans-serif;
            font-size: 15px;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.2s, transform 0.15s;
            letter-spacing: 0.01em;
        }

        .btn-signin:hover  { background: var(--accent-dk); }
        .btn-signin:active { transform: scale(0.97); }
        .btn-signin i { font-size: 13px; transition: transform 0.2s; }
        .btn-signin:hover i { transform: translateX(3px); }

        /* — Footer note — */
        .login-note {
            margin-top: 2rem;
            font-size: 12px;
            color: var(--ink-muted);
            text-align: center;
            opacity: 0.7;
        }

        /* — Responsive — */
        @media (max-width: 768px) {
            .page { grid-template-columns: 1fr; }
            .panel-left { display: none; }
            .panel-right { padding: 3rem 1.5rem; }
        }
    </style>
</head>
<body>

<div class="page">

    <!-- ── Left panel: banner image ── -->
    <div class="panel-left">
        <img
            class="banner"
            src="public/images/JCIMS-login-banner.jpeg"
            alt="Warka Hub JCIMS — Manage your team with ease"
        >
        <span class="banner-footer">© <?php echo date('Y'); ?> Warka Hub JCIMS</span>
    </div>

    <!-- ── Right login panel ── -->
    <div class="panel-right">
        <div class="login-card">

            <!-- Brand row -->
            <div class="card-brand">
                <div class="brand-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <div class="card-brand-name">Job Creation Information Management System</div>
                </div>
            </div>

            <div class="login-header">
                <div class="amharic-badge">
                    <i class="fas fa-door-open" style="font-size:12px;"></i>
                    መግቢያ ገጽ
                </div>
                <div class="login-title">Welcome <span>back.</span></div>
            </div>

            <?php if (isset($_SESSION['error'])): ?>
            <div class="alert" role="alert" id="error-alert">
                <i class="fas fa-exclamation-circle"></i>
                <span><?php echo htmlspecialchars($_SESSION['error']); ?></span>
                <button class="alert-close" onclick="document.getElementById('error-alert').remove()" aria-label="Close">&times;</button>
            </div>
            <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <form action="login_process" method="post" novalidate>

                <div class="field">
                    <label for="email">Username</label>
                    <div class="input-wrap">
                        <i class="fas fa-envelope field-icon" aria-hidden="true"></i>
                        <input
                            type="text"
                            id="email"
                            name="email"
                            placeholder="ANRS000001"
                            required
                            autocomplete="username"
                        >
                    </div>
                </div>

                <div class="field">
                    <label for="password">Password</label>
                    <div class="input-wrap">
                        <i class="fas fa-lock field-icon" aria-hidden="true"></i>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="••••••••"
                            required
                            autocomplete="current-password"
                        >
                        <button type="button" class="toggle-pw" id="togglePw" aria-label="Show password">
                            <i class="fas fa-eye" id="togglePwIcon"></i>
                        </button>
                    </div>
                </div>

                <a href="forgot_password" class="forgot-link">ፓስወርድ እረሳሁ</a>

                <div class="form-footer">
                    <label class="remember">
                        <input type="checkbox" id="remember" name="remember">
                        <span>አስታዉሰኝ</span>
                    </label>

                    <button type="submit" class="btn-signin">
                        ግባ <i class="fas fa-arrow-right"></i>
                    </button>
                </div>

            </form>

            <p class="login-note">Protected by secure session management. &copy; <?php echo date('Y'); ?> Warka Hub JCIMS.</p>

        </div>
    </div>

</div>

<script src="public/plugins/jquery/jquery.min.js"></script>
<script src="public/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script nonce="<?php echo $GLOBALS['nonce']; ?>">
    // Show / hide password toggle
    const toggleBtn  = document.getElementById('togglePw');
    const pwInput    = document.getElementById('password');
    const toggleIcon = document.getElementById('togglePwIcon');

    toggleBtn.addEventListener('click', () => {
        const isHidden = pwInput.type === 'password';
        pwInput.type        = isHidden ? 'text' : 'password';
        toggleIcon.className = isHidden ? 'fas fa-eye-slash' : 'fas fa-eye';
        toggleBtn.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
    });

    // Handle form validation intercept
    const form  = document.querySelector('form');
    const emailInput = document.getElementById('email');

    form.addEventListener('submit', (e) => {
        // Remove any existing error alert box first if they try to click sign-in again
        const existingAlert = document.getElementById('error-alert');
        if (existingAlert) existingAlert.remove();

        // Check if values are completely empty or whitespace
        if (!emailInput.value.trim() || !pwInput.value.trim()) {
            e.preventDefault(); // Stop form from sending to login_process

            // Inject a beautifully matching alert component right above the inputs
            const alertHtml = `
                <div class="alert" role="alert" id="error-alert">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>Please fill in both the email and password fields.</span>
                    <button class="alert-close" onclick="document.getElementById('error-alert').remove()" aria-label="Close">&times;</button>
                </div>
            `;
            
            form.insertAdjacentHTML('beforebegin', alertHtml);
        }
    });
</script>
</body>
</html>