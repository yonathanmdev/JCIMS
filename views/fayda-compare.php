<?php
$profile = $_SESSION['fayda_profile'] ?? [];

?>
<style>
.fayda-card {
    max-width: 640px;
    margin: 0 auto;
    background: #ffffff;
    border: 1px solid #e2e5e9;
    border-radius: 14px;
    box-shadow: 0 1px 3px rgba(16, 24, 40, 0.06), 0 8px 24px rgba(16, 24, 40, 0.04);
    overflow: hidden;
    font-family: "Inter", "Noto Sans Ethiopic", -apple-system, BlinkMacSystemFont, sans-serif;
}

.fayda-card__header {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 20px 24px;
    background: linear-gradient(135deg, #0f766e 0%, #0d5f58 100%);
    color: #fff;
}

.fayda-card__header-icon {
    width: 36px;
    height: 36px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, 0.15);
    border-radius: 10px;
}

.fayda-card__header-text h2 {
    margin: 0;
    font-size: 17px;
    font-weight: 600;
    letter-spacing: 0.01em;
}

.fayda-card__header-text p {
    margin: 2px 0 0;
    font-size: 12.5px;
    color: rgba(255, 255, 255, 0.75);
}

.fayda-card__body {
    padding: 22px 24px 24px;
}

.fayda-notice {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    background: #eff8ff;
    border: 1px solid #bfe3ff;
    color: #0a5f9c;
    font-size: 13.5px;
    line-height: 1.5;
    padding: 12px 14px;
    border-radius: 10px;
    margin-bottom: 20px;
}

.fayda-fields {
    display: flex;
    flex-direction: column;
    border: 1px solid #eceef1;
    border-radius: 12px;
    overflow: hidden;
}

.fayda-field {
    display: grid;
    grid-template-columns: 150px 1fr;
    gap: 8px 16px;
    padding: 14px 16px;
    border-bottom: 1px solid #eceef1;
    align-items: baseline;
}

.fayda-field:last-child {
    border-bottom: none;
}

.fayda-field:nth-child(odd) {
    background: #fafbfc;
}

.fayda-field__label {
    font-size: 12.5px;
    font-weight: 600;
    color: #667085;
    letter-spacing: 0.01em;
    line-height: 1.4;
}

.fayda-field__label small {
    display: block;
    font-weight: 400;
    color: #98a2b3;
    font-size: 11.5px;
    margin-top: 1px;
}

.fayda-field__value {
    font-size: 14.5px;
    color: #1d2939;
    font-weight: 500;
    word-break: break-word;
}

.fayda-field__value--mono {
    font-family: "SFMono-Regular", Consolas, monospace;
    font-size: 13.5px;
    color: #344054;
}

.fayda-field__bilingual {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.fayda-field__bilingual .am {
    font-size: 14px;
    color: #475467;
}

.fayda-cta {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: 100%;
    margin-top: 22px;
    padding: 13px 20px;
    background: #0f766e;
    color: #fff;
    font-size: 15px;
    font-weight: 600;
    text-decoration: none;
    border-radius: 10px;
    transition: background 0.15s ease;
}

.fayda-cta:hover {
    background: #0d5f58;
    color: #fff;
}

.fayda-footnote {
    text-align: center;
    font-size: 11.5px;
    color: #98a2b3;
    margin-top: 12px;
}

@media (max-width: 480px) {
    .fayda-field {
        grid-template-columns: 1fr;
        gap: 4px;
    }
}
</style>

<div class="fayda-card">
    <div class="fayda-card__header">
        <div class="fayda-card__header-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 2L3 6v6c0 5 3.8 9.4 9 10 5.2-.6 9-5 9-10V6l-9-4z" stroke="#fff" stroke-width="1.6" stroke-linejoin="round"/>
                <path d="M9 12l2 2 4-4" stroke="#fff" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
        <div class="fayda-card__header-text">
            <h2>የፋይዳ መረጃ ማረጋገጫ</h2>
            <p>Fayda National ID Verification</p>
        </div>
    </div>

    <div class="fayda-card__body">
        <?php if (!empty($existing)): ?>
            <div class="fayda-notice">
                <span>ℹ️</span>
                <span>ይህ ተጠቃሚ አስቀድሞ ተመዝግቧል — መረጃው ይሻሻላል። <br><span style="color:#5b8bb0;">This user is already registered — their information will be updated.</span></span>
            </div>
        <?php endif; ?>

        <div class="fayda-fields">
            <div class="fayda-field">
                <div class="fayda-field__label">FAN</div>
                <div class="fayda-field__value fayda-field__value--mono"><?= htmlspecialchars($profile['individual_id'] ?? '') ?></div>
            </div>

            <div class="fayda-field">
                <div class="fayda-field__label">Job Seeker ID<small>የስራ ፈላጊ መለያ ቁጥር</small></div>
                <div class="fayda-field__value fayda-field__value--mono"><?= htmlspecialchars($_SESSION['id_number'] ?? 'TBA') ?></div>
            </div>

            <div class="fayda-field">
                <div class="fayda-field__label">Full Name<small>ሙሉ ስም</small></div>
                <div class="fayda-field__bilingual">
                    <div class="fayda-field__value"><?= htmlspecialchars($profile['name#en'] ?? '') ?></div>
                    <div class="am"><?= htmlspecialchars($profile['name#am'] ?? '') ?></div>
                </div>
            </div>

            <div class="fayda-field">
                <div class="fayda-field__label">Gender<small>ጾታ</small></div>
                <div class="fayda-field__bilingual">
                    <div class="fayda-field__value"><?= htmlspecialchars($profile['gender#en'] ?? '') ?></div>
                    <div class="am"><?= htmlspecialchars($profile['gender#am'] ?? '') ?></div>
                </div>
            </div>

            <div class="fayda-field">
                <div class="fayda-field__label">Birthdate<small>የልደት ቀን</small></div>
                <div class="fayda-field__value"><?= htmlspecialchars($profile['birthdate'] ?? '') ?></div>
            </div>

            <div class="fayda-field">
                <div class="fayda-field__label">Phone<small>ስልክ</small></div>
                <div class="fayda-field__value fayda-field__value--mono"><?= htmlspecialchars($profile['phone_number'] ?? '') ?></div>
            </div>
        </div>

        <a href="<?= rtrim($_ENV['BASE_URL'], '/') ?>/fayda-confirm" class="fayda-cta">
            አረጋግጥ እና ቀጥል
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M5 12h14M13 6l6 6-6 6" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </a>
        <div class="fayda-footnote">Verified via the national Fayda digital ID system</div>
    </div>
</div>