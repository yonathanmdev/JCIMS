<?php
use App\Helpers\EthiopianDateHelper; 
use App\Helpers\AuthHelper;
$fiscal_year = AuthHelper::checkFiscalYear();
$is_enterprise_registration_page = true; 
$last24HoursCount = $recentCount ?? 0;
?>
<section class="content">
  <div class="container-fluid">
    <div class="card-header bg-white d-flex flex-column flex-md-row align-items-md-center card-primary card-outline">
      <div class="ml-md-auto">
  <button 
    type="button" 
    id="newEnterpriseBtn"
    class="btn btn-primary btn-sm w-100 w-md-auto"
    data-toggle="modal" 
    data-target="#enterpriseTypeChooserModal"
  >
    <i class="fas fa-user-plus mr-2"></i>
    ኢንተርፕራይዝ መዝግብ
  </button>
</div>
      
    </div>
     <div class="card-body">
      <small class="text-muted">
                            ባለፉት 24 ሰዓት ውስጥ የመዘገቡት ኢንተርፕራይዝ፦
                            <span class="badge badge-primary">
                                <?= $last24HoursCount ?>
                            </span>
   </small>
        <!-- Example Table (optional) -->
      <table id="example1" data-empty-msg="ዛሬ ምንም ኢንተርፕራይዝ አልመዘገቡም።" class="table table-bordered table-hover dataTable dtr-inline small" style="color: #000;" aria-describedby="example2_info">
    <thead class="thead-light">
      <tr>
        <th>#</th>
        <th>የኢንተርፕራይዝ ስም</th>
         <th>TIN NO</th>
        <th>ዓይነት</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($recentEnterprises)): ?>
        <?php foreach ($recentEnterprises as $index => $ent): ?>
         <tr id="row-<?= htmlspecialchars($ent['id']) ?>">
            <td><?= $index + 1 ?></td>
            <td><?= htmlspecialchars($ent['enterprisename']) ?></td>
            <td><?= htmlspecialchars($ent['tine_number']) ?></td>
           <td><?= htmlspecialchars($ent['enterprise_type']) ?></td>
            <td class="text-center align-middle">
                       <a href="<?= rtrim($_ENV['BASE_URL'], '/') ?>/enterprises-details/<?= htmlspecialchars($ent['id']) ?>"
   class="btn btn-outline-primary btn-sm"
   title="ሙሉ መረጃ ይመልከቱ">
    <i class="fas fa-eye"></i>
</a>
</td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
  </div>
  </div>
</section>
<div class="modal fade" id="enterpriseTypeChooserModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">የኢንተርፕራይዝ ዓይነት ይምረጡ</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body d-flex flex-column gap-2">
        <button type="button" class="btn btn-outline-primary mb-2 enterprise-type-choice" data-type="0">
          <i class="fas fa-user mr-2"></i> የግል
        </button>
        <button type="button" class="btn btn-outline-primary enterprise-type-choice" data-type="1">
          <i class="fas fa-users mr-2"></i> የማህበር 
        </button>
      </div>
    </div>
  </div>
</div>
<?php include 'partials/enterprise-modal.php'; ?>
