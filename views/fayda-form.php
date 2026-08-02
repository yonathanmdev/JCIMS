<?php

use App\Helpers\FaydaProfileHelper;

$profile   = $_SESSION['fayda_profile'] ?? [];
$formError = $_SESSION['form_error'] ?? null;
unset($_SESSION['form_error']);

$name          = FaydaProfileHelper::field($profile, 'name');
$currentGender = FaydaProfileHelper::field($profile, 'gender', 'en'); // 'Male' / 'Female'
?>
<!DOCTYPE html>
<html lang="am">
<head>
    <meta charset="UTF-8">
    <title>የመመዝገቢያ ቅጽ - Fayda ID</title>
    <link rel="stylesheet" href="<?= rtrim($_ENV['BASE_URL'], '/') ?>/assets/plugins/fontawesome-free/css/all.min.css">
</head>
<body>
<div class="content-wrapper" style="padding:2rem; max-width:700px; margin:0 auto;">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">ከፋይዳ የተገኘ መረጃ ያረጋግጡ</h3>
        </div>
        <div class="card-body">

            <?php if ($formError): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($formError) ?></div>
            <?php endif; ?>

            <form method="POST" action="<?= rtrim($_ENV['BASE_URL'], '/') ?>/index.php?action=fayda-register">
                <div class="form-group">
                    <label>ሙሉ ስም</label>
                    <input type="text" name="full_name" class="form-control"
                           value="<?= htmlspecialchars($name) ?>" required>
                </div>

                <div class="form-group">
                    <label>ስልክ ቁጥር</label>
                    <input type="text" name="phone" class="form-control"
                           value="<?= htmlspecialchars($profile['phone_number'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label>ጾታ</label>
                    <select name="gender" class="form-control" required>
                        <option value="">-- ይምረጡ --</option>
                        <option value="Male"   <?= $currentGender === 'Male'   ? 'selected' : '' ?>>ወንድ</option>
                        <option value="Female" <?= $currentGender === 'Female' ? 'selected' : '' ?>>ሴት</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>የልደት ቀን</label>
                    <input type="date" name="birthdate" class="form-control"
                           value="<?= htmlspecialchars($profile['birthdate'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label>የትምህርት ደረጃ</label>
                    <select name="education_level" class="form-control" required>
                        <option value="">-- ይምረጡ --</option>
                        <option value="primary">አንደኛ ደረጃ</option>
                        <option value="secondary">ሁለተኛ ደረጃ</option>
                        <option value="diploma">ዲፕሎማ</option>
                        <option value="degree">ዲግሪ</option>
                        <option value="masters">ማስተርስ</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>ዘርፍ (Sector)</label>
                    <select name="sector_id" class="form-control">
                        <option value="">-- ይምረጡ --</option>
                        <!-- TODO: populate dynamically from your sectors table -->
                    </select>
                </div>

                <button type="submit" class="btn btn-primary btn-block">መዝግብ</button>
            </form>

        </div>
    </div>
</div>
</body>
</html>