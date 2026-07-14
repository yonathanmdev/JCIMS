<?php
use App\Helpers\EthiopianDateHelper; 
use App\Helpers\AuthHelper;
$fiscal_year = AuthHelper::checkFiscalYear();
$is_jobseeker_list_page = true; 
$totalCount =0;
$offset = 0;
$currentPage =1;
$totalPages =1;
?>
<section class="content">
  <div class="container-fluid">
    <div class="card-header bg-white d-flex flex-column flex-md-row align-items-md-center card-primary card-outline">
  
      
  
     <div class="card-body">
      <small class="text-muted">
        ጠቅላላ የመዘገቡት ስራ ፈላጊ ብዛት፦
     <span class="badge badge-primary"> 
        <?= $totalCount ?>
  </span>
   </small>
   <div class="d-flex justify-content-between align-items-center mb-3">
    <div class="search-controls position-relative">
        <div class="search-input-wrapper">
            <i class="fas fa-search search-icon"></i>
            <input type="text"
                   id="jobSeekerSearchInput"
                   class="form-control search-input"
                   placeholder="ስም ወይም መ/ቁ ይፈልጉ... (Search by name or ID)"
                   autocomplete="off">
            <span id="searchSpinner" class="search-spinner d-none">
                <i class="fas fa-spinner fa-spin"></i>
            </span>
        </div>
        <div id="jobSeekerSearchResults" class="search-results-dropdown d-none"></div>
    </div>

    <a href="<?= rtrim($_ENV['BASE_URL'], '/') ?>/jobseekers-list-export-excel"
       class="btn btn-secondary shadow-sm"
       id="exportBtn">
        <i class="fas fa-file-excel mr-1"></i> Export to Excel
    </a>
</div>
        <!-- Example Table (optional) -->
      <table id="example1" data-empty-msg="ምንም ስራ ፈላጊ የለም።" class="table table-bordered table-hover small">
    <thead class="thead-light">
        <tr>
            <th>#</th>
            <th>መ/ቁ</th>
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
                    <td><?=$js['job_seeker_id'] ?></td>
                    <td><?= htmlspecialchars($js['first_name']) . ' ' . htmlspecialchars($js['father_name']) . ' ' . htmlspecialchars($js['last_name']) ?></td>
                    <td><?= htmlspecialchars($js['gender']) ?></td>
                    <td class="text-center align-middle">
                        <button class="btn btn-outline-primary btn-sm view-jobseeker-btn"
                                data-id="<?= htmlspecialchars($js['id']) ?>"
                                title="ሙሉ መረጃ ይመልከቱ">
                            <i class="fas fa-eye"></i>
                        </button>
                      <?php 
// Assume $myBranchId is available here, e.g., from a session or user object
// $myBranchId = AuthHelper::getUserBranchId(); // Example of how you might get it

if (AuthHelper::hasRole(['team_leader', 'officer'], [3, 4]) && $js['branch_id'] === $_SESSION['user']['branch_id']): ?>
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
  </div>
</section>
<?php include 'partials/jobseeker-modal.php'; ?>
<?php include 'partials/jobseeker-views-modal.php'; ?>