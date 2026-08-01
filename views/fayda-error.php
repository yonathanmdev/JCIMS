<div class="alert alert-danger">
    <h4>ስህተት ተከስቷል</h4>
    <p>ምክንያት: <?= htmlspecialchars($reason) ?></p>
    <a href="<?= rtrim($_ENV['BASE_URL'], '/') ?>/index.php?action=fayda-start" class="btn btn-secondary">እንደገና ይሞክሩ</a>
</div>