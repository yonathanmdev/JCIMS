<?php
$profile = $_SESSION['fayda_profile'] ?? [];
?>
<?php if (!empty($_SESSION['form_error'])): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['form_error']) ?></div>
    <?php unset($_SESSION['form_error']); ?>
<?php endif; ?>

<form method="post" action="<?= rtrim($_ENV['BASE_URL'], '/') ?>/index.php?action=fayda-register">
    <div class="form-group">
        <label>የመታወቂያ ቁጥር</label>
        <input type="text" class="form-control" value="<?= htmlspecialchars($_SESSION['fayda_id_number'] ?? '') ?>" readonly>
    </div>
    <div class="form-group">
        <label>ሙሉ ስም</label>
        <input type="text" name="full_name" class="form-control"
               value="<?= htmlspecialchars($profile['name'] ?? '') ?>" required>
    </div>
    <div class="form-group">
        <label>ስልክ ቁጥር</label>
        <input type="text" name="phone" class="form-control"
               value="<?= htmlspecialchars($profile['phone_number'] ?? '') ?>" required>
    </div>
    <div class="form-group">
        <label>ጾታ</label>
        <input type="text" name="gender" class="form-control"
               value="<?= htmlspecialchars($profile['gender'] ?? '') ?>" required>
    </div>
    <div class="form-group">
        <label>የልደት ቀን</label>
        <input type="text" name="birthdate" class="form-control"
               value="<?= htmlspecialchars($profile['birthdate'] ?? '') ?>" required>
    </div>

    <div class="form-group">
        <label>የትምህርት ደረጃ</label>
        <select name="education_level" class="form-control" required>
            <option value="">-- ይምረጡ --</option>
            <option value="primary">የመጀመሪያ ደረጃ</option>
            <option value="secondary">ሁለተኛ ደረጃ</option>
            <option value="diploma">ዲፕሎማ</option>
            <option value="degree">ዲግሪ</option>
        </select>
    </div>

    <button type="submit" class="btn btn-success">ላክ / አስመዝግብ</button>
</form>