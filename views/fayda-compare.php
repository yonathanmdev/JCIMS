<?php
$profile = $_SESSION['fayda_profile'];

// TEMP DEBUG — remove after diagnosing
echo '<pre style="background:#eee;padding:1rem;font-size:12px;">';
var_dump($profile);
echo '</pre>';
?>
<div class="card">
    <div class="card-header">የፋይዳ መረጃ ማረጋገጫ</div>
    <div class="card-body">
        <?php if (!empty($existing)): ?>
            <div class="alert alert-info">ይህ ተጠቃሚ አስቀድሞ ተመዝግቧል — መረጃው ይሻሻላል።</div>
        <?php endif; ?>
        <table class="table">
            <tr><th>የመታወቂያ ቁጥር</th><td><?= htmlspecialchars($_SESSION['fayda_id_number'] ?? '') ?></td></tr>
            <tr><th>ሙሉ ስም</th><td><?= htmlspecialchars($profile['name'] ?? '') ?></td></tr>
            <tr><th>ስልክ</th><td><?= htmlspecialchars($profile['phone_number'] ?? '') ?></td></tr>
            <tr><th>ጾታ</th><td><?= htmlspecialchars($profile['gender'] ?? '') ?></td></tr>
            <tr><th>የልደት ቀን</th><td><?= htmlspecialchars($profile['birthdate'] ?? '') ?></td></tr>
        </table>
        <a href="<?= rtrim($_ENV['BASE_URL'], '/') ?>/index.php?action=fayda-confirm" class="btn btn-primary">
            አረጋግጥ እና ቀጥል
        </a>
    </div>
</div>