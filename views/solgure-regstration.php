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
  <!-- በ <div class="card-header"> ... </div> ውስጥ የሚቀመጥ -->
<div class="d-flex justify-content-between align-items-center w-100">
    <h3 class="card-title text-bold">የመከላከያ ምልመላ  ዝርዝር</h3>
    <?php if (AuthHelper::hasRole(['team_leader', 'officer'], [3, 4])): ?>
        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#registerDefenseModal">
            <i class="fas fa-user-plus mr-1"></i> አዲስ መዝግብ
        </button>
    <?php endif; ?>
</div>
      
    </div>
     <div class="card-body">
      <small class="text-muted">
        <!-- ጠቅላላ የመዘገቡት መከላከያ ምልመላ ብዛት፦ -->
     <span class="badge badge-primary"> 
        <?= $totalCount ?>
  </span>
   </small>
        <!-- Example Table (optional) -->
      <table id="example1" data-empty-msg="ምንም መከላከያ ምልመላ የለም።" class="table table-bordered table-hover small">
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
                    <td><?= htmlspecialchars($js['fullname']) ?></td>
                    <td><?= htmlspecialchars($js['sex']) ?></td>
                    <td class="text-center align-middle">
    <button class="btn btn-outline-primary btn-sm view-defense-btn"
            data-id="<?= htmlspecialchars($js['id']) ?>"
            title="ሙሉ መረጃ ይመልከቱ">
        <i class="fas fa-eye"></i>
    </button>
    <?php if (AuthHelper::hasRole(['team_leader', 'officer'], [3, 4])): ?>
        <button class="btn btn-outline-warning btn-sm edit-defense-btn"
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
                <td colspan="4" class="text-center text-muted py-3">ዛሬ ምንም መከላከያ ምልመላ አልመዘገቡም።</td>
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
<!-- የጥቅል መረጃ ማሳያ ሞዳል -->
<div class="modal fade" id="viewDefenseModal" tabindex="-1" aria-labelledby="viewDefenseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white py-2">
                <h5 class="modal-title" id="viewDefenseModalLabel"><i class="fas fa-user-shield me-2"></i> የመከላከያ ምዝገባ ሙሉ መረጃ</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- ሎደር (ዳታው እስኪመጣ ማሳያ) -->
                <div id="modalLoader" class="text-center my-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">በመጫን ላይ...</span>
                    </div>
                </div>
                
                <!-- የመረጃ ማሳያ ሰንጠረዥ -->
                <div id="modalDataContent" style="display: none;">
                    <table class="table table-bordered table-striped sm-table">
                        <tbody>
                            <tr>
                                <th style="width: 35%;">ሙሉ ስም</th>
                                <td id="d_fullname" class="fw-bold text-navy"></td>
                            </tr>
                            <tr>
                                <th>ብሄራዊ መለያ (Fayda ID)</th>
                                <td id="d_national_id"></td>
                            </tr>
                            <tr>
                                <th>ጾታ</th>
                                <td id="d_sex"></td>
                            </tr>
                            <tr>
                                <th>እድሜ</th>
                                <td id="d_age"></td>
                            </tr>
                            <tr>
                                <th>ስልክ ቁጥር</th>
                                <td id="d_phone"></td>
                            </tr>
                            <tr>
                                <th>የትምህርት ደረጃ</th>
                                <td id="d_education_level"></td>
                            </tr>
                            <tr>
                                <th>የተማሩት የትምህርት መስክ / ሙያ</th>
                                <td id="d_educated_study"></td>
                            </tr>
                            <tr>
                                <th>ተጨማሪ ክህሎት</th>
                                <td id="d_additional_skill"></td>
                            </tr>
                            <tr>
                                <th>ፍላጎት / ምደባ</th>
                                <td id="d_flagot"></td>
                            </tr>
                            <tr>
                                <th>የተመዘገቡበት ዘርፍ</th>
                                <td id="d_sector"></td>
                            </tr>
                            <tr>
                                <th>የተመዘገቡበት መዋቅር (Branch)</th>
                                <td id="d_branch_name"></td>
                            </tr>
                            <tr>
                                <th>ቀበሌ</th>
                                <td id="d_kebele"></td>
                            </tr>
                            <tr>
                                <th>የተመዘገቡበት ቦታ</th>
                                <td id="d_yetemezegebebet"></td>
                            </tr>
                            <tr>
                                <th>የምዝገባ ቀንና ሰዓት</th>
                                <td id="d_created_at"></td>
                            </tr>
                            <tr>
                                <th>መዝጋቢ አካል</th>
                                <td id="d_registered_by"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
          <div class="modal-footer py-2 d-flex justify-content-between">
    <!-- በስተግራ በኩል መረጃውን ማተሚያ አዝራር (የአማራጭ) -->
    <div>
        <button type="button" class="btn btn-outline-secondary btn-sm me-2" onclick="window.print();" title="ይህንን ገጽ አትም">
            <i class="fas fa-print me-1"></i> አትም
        </button>
    </div>

    <!-- በስተቀኝ በኩል ማስተካከያ እና መዝጊያ አዝራሮች -->
    <div>
        <?php if (AuthHelper::hasRole(['team_leader', 'officer'], [3, 4])): ?>
            <button type="button" class="btn btn-warning btn-sm me-2 edit-defense-modal-btn" id="modal_edit_btn" data-id="">
                <i class="fas fa-edit me-1"></i> አስተካክል
            </button>
        <?php endif; ?>
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
            <i class="fas fa-times me-1"></i> ዝጋ
        </button>
    </div>
</div>
        </div>
    </div>
</div>
<script nonce="<?php echo htmlspecialchars($GLOBALS['nonce'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
// ዳታው መጥቶ ፎርሙ ላይ ከሚሞላበት ኮድ በታች ይህንን አስቀምጥ፡

document.addEventListener('DOMContentLoaded', function () {
    // አዝራሮቹን በክላስ መምረጥ
    const viewButtons = document.querySelectorAll('.view-defense-btn');
    const myModal = new bootstrap.Modal(document.getElementById('viewDefenseModal'));

    viewButtons.forEach(button => {
        button.addEventListener('click', function () {
            const recruitmentId = this.getAttribute('data-id');
            
            // የሞዳል ኤለመንቶችን መያዝ
            document.getElementById('modalLoader').style.display = 'block';
            document.getElementById('modalDataContent').style.display = 'none';
            
            // ሞዳሉን ከፈት ማድረግ
            myModal.show();

            // ወደ ፒኤችፒ የዳታ ምንጭ መላክ (የ URL መንገዱን እንደ ሲስተምህ አስተካክለው)
           // ከዚህ በፊት የነበረውን የ fetch መስመር በዚህ ተካው፡
               fetch(`defense-get-details?id=${recruitmentId}`)
                .then(response => {
                    if (!response.ok) throw new Error('የአውታረ መረብ ግንኙነት ስህተት አለ።');
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        const info = data.record;
                        
                        // መረጃዎቹን በየቦታው ማስገባት (XSS መከላከያ textContent/innerText መጠቀም ግድ ነው)
                        document.getElementById('d_fullname').textContent         = info.fullname || 'ያልተገለጸ';
                        document.getElementById('d_national_id').textContent      = info.national_id || 'የለውም/ያልተያያዘ';
                        document.getElementById('d_sex').textContent              = info.sex || '-';
                        document.getElementById('d_age').textContent              = info.age || '-';
                        document.getElementById('d_phone').textContent            = info.phone || '-';
                        document.getElementById('d_education_level').textContent  = info.education_level || '-';
                        document.getElementById('d_educated_study').textContent   = info.educated_study || 'የለውም';
                        document.getElementById('d_additional_skill').textContent = info.additional_skill || 'የለውም';
                        document.getElementById('d_flagot').textContent           = info.flagot || '-';
                        document.getElementById('d_sector').textContent           = info.sector || '-';
                        document.getElementById('d_branch_name').textContent      = info.branch_name || '-';
                        document.getElementById('d_kebele').textContent           = info.kebele || '-';
                        document.getElementById('d_yetemezegebebet').textContent  = info.yetemezegebebet || '-';
                        document.getElementById('d_created_at').textContent       = info.created_at || '-';
                        document.getElementById('d_registered_by').textContent   = info.registered_by || '-';

                        // ሎደሩን ደብቆ ዳታውን ማሳየት
                        document.getElementById('modalLoader').style.display = 'none';
                        document.getElementById('modalDataContent').style.display = 'block';
                    } else {
                        alert('መረጃውን ማግኘት አልተቻለም፦ ' + data.message);
                        myModal.hide();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('ዳታውን በማምጣት ላይ ስህተት አጋጥሟል!');
                    myModal.hide();
                });
        });
    });
}); 
</script> 
<?php include 'partials/register-defense-modal.php'; ?>