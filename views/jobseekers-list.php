<?php
use App\Helpers\EthiopianDateHelper; 
use App\Helpers\AuthHelper;
$fiscal_year = AuthHelper::checkFiscalYear();
$is_jobseeker_list_page = true; 
?>
<section class="content">
  <div class="container-fluid">
    <div class="card-header bg-white d-flex flex-column flex-md-row align-items-md-center card-primary card-outline">
  
      
    </div>
     <div class="card-body">
      <small class="text-muted">
        ጠቅላላ የመዘገቡት ስራ ፈላጊ ብዛት፦
     <span class="badge badge-primary"> 
        <?= $totalCount ?>
  </span>
   </small>
        <!-- Example Table (optional) -->
      <table id="example1" data-empty-msg="ምንም ስራ ፈላጊ የለም።" class="table table-bordered table-hover small">
    <thead class="thead-light">
        <tr>
            <th>#</th>
            <th>ስም</th>
            <th>ጾታ</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($jobSeekers)): ?>
            <?php foreach ($jobSeekers as $index => $js): ?>
                <tr id="row-<?= $js['id'] ?>">
                    <td><?= $offset + $index + 1 ?></td>
                    <td><?= htmlspecialchars($js['first_name']) . ' ' . htmlspecialchars($js['father_name']) . ' ' . htmlspecialchars($js['last_name']) ?></td>
                    <td><?= htmlspecialchars($js['gender']) ?></td>
                    <td class="text-center align-middle">
                        <button class="btn btn-outline-primary btn-sm view-jobseeker-btn"
                                data-id="<?= htmlspecialchars($js['id']) ?>"
                                title="ሙሉ መረጃ ይመልከቱ">
                            <i class="fas fa-eye"></i>
                        </button>
                        <?php if (AuthHelper::hasRole(['team_leader', 'officer'], [3, 4])): ?>
                            <button class="btn btn-outline-warning btn-sm edit-jobseeker-btn"
                                    data-id="<?= htmlspecialchars($js['id']) ?>"
                                    title="አስተካክል">
                                <i class="fas fa-edit"></i>
                            </button>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="4" class="text-center text-muted py-3">ዛሬ ምንም ስራ ፈላጊ አልመዘገቡም።</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
<?php
$basePath = rtrim($_ENV['BASE_URL'], '/') . '/jobseekers-list'; // your actual working route
?>

<?php if ($totalPages > 1): ?>
<nav aria-label="Page navigation">
    <ul class="pagination justify-content-end">
        <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
            <a class="page-link" href="<?= $basePath ?>?page=<?= $currentPage - 1 ?>">ቀዳሚ</a>
        </li>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                <a class="page-link" href="<?= $basePath ?>?page=<?= $i ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>

        <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
            <a class="page-link" href="<?= $basePath ?>?page=<?= $currentPage + 1 ?>">ቀጣይ</a>
        </li>
    </ul>
</nav>
<?php endif; ?>
  </div>
  </div>
</section>
<?php include 'partials/jobseeker-modal.php'; ?>
<?php include 'partials/jobseeker-views-modal.php'; ?>