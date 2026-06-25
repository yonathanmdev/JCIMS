<?php
use App\Helpers\EthiopianDateHelper; 
$is_branch_deleted_page = true;
 ?>
<section class="content">
  <div class="container-fluid">
      <div class="card card-primary card-outline">
    

      <div class="container-fluid py-4">

  <div class="d-flex align-items-center justify-content-between mb-3">
    <div>
      <h5 class="mb-0">የተሰረዙ ቅርንጫፎች</h5>
      <small class="text-muted">መዝገቦች ከ90 ቀናት በኋላ ይሰረዛሉ</small>
    </div>
    
  </div>

  <div class="card border-0 shadow-sm">
    <div class="table-responsive">
     <table id="example1" class="table table-bordered table-striped">
       
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>ቅርንጫፍ</th>
            <th>የሰረዘው</th>
            <th>የተሰረዘበት ቀን</th>
            <th>ቀሪ ጊዜ</th>
            <th>አብረው የተሰረዙ</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($deletedOrgs)): ?>
            <tr>
              <td colspan="6" class="text-center text-muted py-4">
                <i class="fas fa-inbox me-2"></i> የተሰረዘ ቅርንጫፍ የለም
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($deletedOrgs as $index => $org): ?>
              <tr id="row-<?= htmlspecialchars($org['id']) ?>">
                <td><?= $index + 1 ?></td>
                <td>
                  <p class="mb-0 fw-500"><?= htmlspecialchars($org['name']) ?></p>
                  <small class="text-muted"><?= htmlspecialchars(substr($org['id'], 0, 8)) ?>...</small>
                </td>

                <td><?= htmlspecialchars($org['deleted_by_name'] ?? 'N/A') ?></td>

                <td><?= date('Y-m-d-h:i A', strtotime($org['deleted_at'])) ?></td>

                <td>
                  <?php $days = (int) $org['days_until_purge']; ?>
                  <span class="badge <?= $days <= 7 ? 'bg-danger' : 'bg-warning text-dark' ?>">
                    <?= $days ?> ቀን
                  </span>
                </td>

                <td>
                  <span class="badge bg-info text-dark me-1">
                    <?= $org['branch_count'] ?> ቅርንጫፍ
                  </span>
                  <span class="badge bg-secondary">
                    <?= $org['user_count'] ?> ተጠቃሚ
                  </span>
                </td>

                <td>
                  <button class="btn btn-sm btn-success restore-org me-1"
                    data-id="<?= htmlspecialchars($org['id']) ?>"
                    data-name="<?= htmlspecialchars($org['name']) ?>"
                    data-branches="<?= $org['branch_count'] ?>"
                    data-users="<?= $org['user_count'] ?>">
                    <i class="fas fa-undo me-1"></i> መልስ
                  </button>
                  <button class="btn btn-sm btn-danger purge-org"
                    data-id="<?= htmlspecialchars($org['id']) ?>"
                    data-name="<?= htmlspecialchars($org['name']) ?>"
                    data-branches="<?= $org['branch_count'] ?>"
                    data-users="<?= $org['user_count'] ?>">
                     <i class="fas fa-trash-alt me-1"></i> አጥፋ
                  </button>
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


