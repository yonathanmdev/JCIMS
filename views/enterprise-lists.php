<?php
use App\Helpers\EthiopianDateHelper; 
use App\Helpers\AuthHelper;
$fiscal_year = AuthHelper::checkFiscalYear();
$is_enterprise_list_page = true; 
?>
<section class="content">
  <div class="container-fluid">
    <div class="card-header bg-white d-flex flex-column flex-md-row align-items-md-center card-primary card-outline">
  
      
  
     <div class="card-body">
      <small class="text-muted">
        ጠቅላላ የተመዘገቡት ኢንተርፕራይዝ ብዛት፦
     <span class="badge badge-primary"> 
        <?= $totalCount ?>
  </span>
   </small>
   <div class="d-flex justify-content-between align-items-center mb-3">
    <div class="search-controls position-relative">
        <div class="search-input-wrapper">
            <i class="fas fa-search search-icon"></i>
            <input type="text"
                   id="enterpriseSearchInput"
                   class="form-control search-input"
                   placeholder="ስም ወይም TIN ይፈልጉ... (Search by name or TIN)"
                   autocomplete="off">
            <span id="searchSpinner" class="search-spinner d-none">
                <i class="fas fa-spinner fa-spin"></i>
            </span>
        </div>
        <div id="enterpriseSearchResults" class="search-results-dropdown d-none"></div>
    </div>

    <a href="<?= rtrim($_ENV['BASE_URL'], '/') ?>/enterprises-list-export-excel"
       class="btn btn-secondary shadow-sm"
       id="exportBtn">
        <i class="fas fa-file-excel mr-1"></i> Export to Excel
    </a>
</div>
        <!-- Example Table (optional) -->
      <table id="example1" data-empty-msg="ምንም ኢንተርፕራይዝ የለም።" class="table table-bordered table-hover small">
    <thead class="thead-light">
        <tr>
            <th>#</th>
            <th>የኢንተርፕራይዝ ስም</th>
            <th>TIN NO.</th>
            <th>ዓይነት</th>
            <th>የተመዘገበበት </th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($enterprises)): ?>
            <?php foreach ($enterprises as $index => $ent): ?>
                <tr id="row-<?= $ent['id'] ?>">
                     <td><?= $offset + $index + 1 ?></td>                  
                    <td><?= htmlspecialchars($ent['enterprisename']) ?></td>
                     <td><?= htmlspecialchars($ent['tine_number']) ?></td>
                    <td><?= htmlspecialchars($ent['enterprise_type']) ?></td>
                     <td>
    <?php if ($ent['branch_id'] == $_SESSION['user']['branch_id']): ?>
        <?= htmlspecialchars($ent['branch_name']) ?>
    <?php else: ?>
        <?= htmlspecialchars($ent['display_branch_name'] ?? $ent['branch_name']) ?>
    <?php endif; ?>
</td>
                    <td class="text-center align-middle">
                       <a href="<?= rtrim($_ENV['BASE_URL'], '/') ?>/enterprises-details/<?= htmlspecialchars($ent['id']) ?>"
   class="btn btn-outline-primary btn-sm"
   title="ሙሉ መረጃ ይመልከቱ">
    <i class="fas fa-eye"></i>
</a>
                      <?php 
if (AuthHelper::hasRole(['team_leader', 'officer'], [3, 4]) && $ent['branch_id'] === $_SESSION['user']['branch_id']): ?>
     <button class="btn btn-outline-danger btn-sm delete-enterprise-btn"
            data-id="<?= htmlspecialchars($ent['id']) ?>"
            data-name="<?= htmlspecialchars($ent['enterprisename']) ?>"
           title="አጥፋ">
   <i class="fas fa-trash-alt me-1"></i>
    </button>
<?php endif; ?>

                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="6" class="text-center text-muted py-3">ምንም ኢንተርፕራይዝ አልተመዘገበም።</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
<?php
$basePath = rtrim($_ENV['BASE_URL'], '/') . '/enterprises-list'; // your actual working route
?>
<?php include 'partials/pagination.php'; ?>
  </div>
  </div>
  </div>
</section>
