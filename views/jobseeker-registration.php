<?php
use App\Helpers\EthiopianDateHelper; 
use App\Helpers\AuthHelper;
$fiscal_year = AuthHelper::checkFiscalYear();
$is_jobseeker_registration_page = true; 
$last24HoursCount = !empty($jobSeekers) ? $jobSeekers[0]['total_job_seekers'] : 0;
?>
<section class="content">
  <div class="container-fluid">
    <div class="card-header bg-white d-flex flex-column flex-md-row align-items-md-center card-primary card-outline">
      <div class="ml-md-auto">
        <button 
          type="button" 
          id="newJobseekerBtn"
          class="btn btn-primary btn-sm w-100 w-md-auto"
          data-toggle="modal" 
          data-target="#jobseekerRegistrationModal"
        >
          <i class="fas fa-user-plus mr-2"></i>
          ስራ ፈላጊ መዝግብ
        </button>
      </div>
      
    </div>
     <div class="card-body">
      <small class="text-muted">
                            ባለፉት 24 ሰዓት ውስጥ የመዘገቡት ስራ ፈላጊ፦
                            <span class="badge badge-primary">
                                <?= $last24HoursCount ?>
                            </span>
   </small>
        <!-- Example Table (optional) -->
      <table id="example1" data-empty-msg="ዛሬ ምንም ስራ ፈላጊ አልመዘገቡም።" class="table table-bordered table-hover dataTable dtr-inline small" style="color: #000;" aria-describedby="example2_info">
    <thead class="thead-light">
      <tr>
        <th>#</th>
        <th>መ/ቁ</th>
        <th>ስም </th>
        <th>ጾታ</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($jobSeekers)): ?>
        <?php foreach ($jobSeekers as $index => $js): ?>
         <tr id="row-<?= $js['id'] ?>">
            <td><?= $index + 1 ?></td>
            <td><?=$js['job_seeker_id'] ?></td>
            <td><?= htmlspecialchars($js['first_name']).' '.htmlspecialchars($js['father_name']).' '.htmlspecialchars($js['last_name']) ?></td>
           <td><?= htmlspecialchars($js['gender']) ?></td>
            <td class="text-center align-middle">
  <div class="btn-group btn-group-sm shadow-sm" role="group">
     <button class="btn btn-outline-primary btn-sm view-jobseeker-btn"
        data-id="<?= htmlspecialchars($js['id']) ?>"
        title="አስተካክል">
    <i class="fas fa-eye"></i>
</button>
             
              <button class="btn btn-outline-warning btn-sm edit-jobseeker-btn"
        data-id="<?= htmlspecialchars($js['id']) ?>"
        title="አስተካክል">
    <i class="fas fa-edit"></i>
</button>
             
  </div>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
  </div>
  </div>
</section>
<?php include 'partials/jobseeker-modal.php'; ?>
<?php include 'partials/jobseeker-views-modal.php'; ?>
