<?php
use App\Helpers\EthiopianDateHelper; 
$is_archived_branches_page = true;
 ?>
<section class="content">
  <div class="container-fluid">
      <div class="card card-primary card-outline">
    
<div class="container-fluid py-4">

    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h5 class="mb-0">የተቀመጡ (Archived) ቅርንጫፎች</h5>
            <small class="text-muted">ቋሚ በሆነ መልኩ ከተሰረዙ ቅርንጫፎች ወደነበሩበት መመለስ ይቻላል</small>
        </div>
        <span class="badge bg-secondary"><?= count($archivedOrgs) ?> archived</span>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table id="example1" class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>ድርጅት</th>
                        <th>የተሰረዘው</th>
                        <th>የ Archive ቀን</th>
                        <th>ምክንያት</th>
                        <th>አብረው የተሰረዙ</th>
                        <th>ሁኔታ</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($archivedOrgs)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fas fa-archive me-2"></i> የተቀመጠ ድርጅት የለም
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($archivedOrgs as $index => $row): ?>
                            <tr id="archive-row-<?= htmlspecialchars($row['archive_id']) ?>">
                                <td><?= $index + 1 ?></td>
                                <td>
                                    <p class="mb-0 fw-500"><?= htmlspecialchars($row['branch_name']) ?></p>
                                    <small class="text-muted"><?= htmlspecialchars(substr($row['original_id'], 0, 8)) ?>...</small>
                                </td>

                                <td><?= htmlspecialchars($row['archived_by_name'] ?? 'N/A') ?></td>

                                <td><?= date('Y-m-d H:i', strtotime($row['archived_at'])) ?></td>

                                <td>
                                    <span class="badge bg-danger">
                                        <?= htmlspecialchars($row['reason']) ?>
                                    </span>
                                </td>

                                <td>
                                    <span class="badge bg-info text-dark me-1">
                                        <?= $row['branch_count'] ?> ቅርንጫፍ
                                    </span>
                                    <span class="badge bg-secondary">
                                        <?= $row['user_count'] ?> ተጠቃሚ
                                    </span>
                                </td>

                                <td>
                                    <?php if (!empty($row['restored_at'])): ?>
                                        <span class="badge bg-success">ተመልሷል</span>
                                        <small class="text-muted d-block">
                                            <?= date('Y-m-d', strtotime($row['restored_at'])) ?>
                                        </small>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">ያልተመለሰ</span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?php if (empty($row['restored_at'])): ?>
                                        <button class="btn btn-sm btn-success restore-archive"
                                            data-archive-id="<?= htmlspecialchars($row['archive_id']) ?>"
                                            data-original-id="<?= htmlspecialchars($row['original_id']) ?>"
                                            data-name="<?= htmlspecialchars($row['branch_name']) ?>"
                                            data-branches="<?= $row['branch_count'] ?>"
                                            data-users="<?= $row['user_count'] ?>">
                                            <i class="fas fa-box-open me-1"></i> ከ Archive መልስ
                                        </button>
                                    <?php else: ?>
                                        <span class="text-muted small">—</span>
                                    <?php endif; ?>
                                </td>

                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
    </div>
  </div>
</section>


