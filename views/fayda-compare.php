<?php

use App\Helpers\FaydaProfileHelper;

$profile = $_SESSION['fayda_profile'] ?? [];
?>
<div class="card">
    <div class="card-header">የፋይዳ መረጃ ማረጋገጫ</div>
    <div class="card-body">
        <?php if (!empty($existing)): ?>
            <div class="alert alert-info">ይህ ተጠቃሚ አስቀድሞ ተመዝግቧል — መረጃው ይሻሻላል።</div>
        <?php endif; ?>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Field</th>
                    <th>English</th>
                    <th>አማርኛ</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Sub ID</strong></td>
                    <td colspan="2"><?= htmlspecialchars($profile['sub'] ?? '') ?></td>
                </tr>
                <tr>
                    <td><strong>ID Number / የመታወቂያ ቁጥር</strong></td>
                    <td colspan="2"><?= htmlspecialchars($_SESSION['fayda_id_number'] ?? '') ?></td>
                </tr>
                <tr>
                    <td><strong>Name / ሙሉ ስም</strong></td>
                    <td><?= htmlspecialchars($profile['name#en'] ?? '') ?></td>
                    <td><?= htmlspecialchars($profile['name#am'] ?? '') ?></td>
                </tr>
                <tr>
                    <td><strong>Gender / ጾታ</strong></td>
                    <td><?= htmlspecialchars($profile['gender#en'] ?? '') ?></td>
                    <td><?= htmlspecialchars($profile['gender#am'] ?? '') ?></td>
                </tr>
                <tr>
                    <td><strong>Birthdate / የልደት ቀን</strong></td>
                    <td colspan="2"><?= htmlspecialchars($profile['birthdate'] ?? '') ?></td>
                </tr>
                <tr>
                    <td><strong>Phone / ስልክ</strong></td>
                    <td colspan="2"><?= htmlspecialchars($profile['phone_number'] ?? '') ?></td>
                </tr>
            </tbody>
        </table>

        <a href="<?= rtrim($_ENV['BASE_URL'], '/') ?>/fayda-confirm" class="btn btn-primary">
            አረጋግጥ እና ቀጥል
        </a>
    </div>
</div>