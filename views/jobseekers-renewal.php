<?php
use App\Helpers\EthiopianDateHelper; 
use App\Helpers\AuthHelper;
$fiscal_year = AuthHelper::checkFiscalYear();
$is_jobseeker_renewal_page = true; 
?>
<section class="content">
  <div class="container-fluid">
    <div class="card-header bg-white d-flex flex-column flex-md-row align-items-md-center card-primary card-outline">
  
      
  
     <div class="card-body">
      <small class="text-muted">
        ጠቅላላ በዚህ በጀት ዓመት እድሳት የተደረገላቸው ስራ ፈላጊዎች፦
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
</div>
<a href="<?= rtrim($_ENV['BASE_URL'], '/') ?>/index.php?action=fayda-start" class="btn btn-primary">
    🆔 በፋይዳ ተጠቃሚ አስመዝግብ
</a>
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
        <?php foreach ($jobSeekers as $index => $js): ?>
            <tr id="row-<?= $js['id'] ?>">
                <td><?= $offset + $index + 1 ?></td>
                <td><?=$js['job_seeker_id'] ?></td>
                <td><?= htmlspecialchars($js['first_name']) . ' ' . htmlspecialchars($js['father_name']) . ' ' . htmlspecialchars($js['last_name']) ?></td>
                <td><?= htmlspecialchars($js['gender']) ?></td>
                <td class="text-center align-middle">
                    
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php
$basePath = rtrim($_ENV['BASE_URL'], '/') . '/jobseekers-renewal'; // your actual working route
?>
<?php include 'partials/pagination.php'; ?>
  </div>
  </div>
  </div>
</section>
<?php include 'partials/jobseeker-modal.php'; ?>