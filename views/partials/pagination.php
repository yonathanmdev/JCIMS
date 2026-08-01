<?php
$window = $window ?? 2;
$searchParam = !empty($search) ? '&search=' . urlencode($search) : '';
?>

<?php if ($totalPages > 1): ?>
<nav aria-label="Page navigation">
    <ul class="pagination justify-content-end">
        <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
            <a class="page-link" href="<?= $basePath ?>?page=<?= $currentPage - 1 ?><?= $searchParam ?>">ቀዳሚ</a>
        </li>

        <?php
        $start = max(1, $currentPage - $window);
        $end   = min($totalPages, $currentPage + $window);
        ?>

        <?php if ($start > 1): ?>
            <li class="page-item">
                <a class="page-link" href="<?= $basePath ?>?page=1<?= $searchParam ?>">1</a>
            </li>
            <?php if ($start > 2): ?>
                <li class="page-item disabled"><span class="page-link">…</span></li>
            <?php endif; ?>
        <?php endif; ?>

        <?php for ($i = $start; $i <= $end; $i++): ?>
            <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                <a class="page-link" href="<?= $basePath ?>?page=<?= $i ?><?= $searchParam ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>

        <?php if ($end < $totalPages): ?>
            <?php if ($end < $totalPages - 1): ?>
                <li class="page-item disabled"><span class="page-link">…</span></li>
            <?php endif; ?>
            <li class="page-item">
                <a class="page-link" href="<?= $basePath ?>?page=<?= $totalPages ?><?= $searchParam ?>"><?= $totalPages ?></a>
            </li>
        <?php endif; ?>

        <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
            <a class="page-link" href="<?= $basePath ?>?page=<?= $currentPage + 1 ?><?= $searchParam ?>">ቀጣይ</a>
        </li>
    </ul>
</nav>
<?php endif; ?>