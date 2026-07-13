<?php
use App\Helpers\EthiopianDateHelper; 
use App\Helpers\AuthHelper;
$fiscal_year = AuthHelper::checkFiscalYear();
 
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
        <th>Action</th>
        <th>#</th>
        <th>ስም</th>
          <th>የተመዘገበበት ከተማ/ወረዳ</th>
         <th>ቀበሌ</th>
      
        <th>ጾታ</th>
        <th>እድሜ</th>
        <th>ስልክ ቁጥር</th>
        <th>የትምህርት ደረጃ</th>
        <th>የትምህርት መስክ</th>
        <th>ሴክተር/ምድብ</th>
        <th>ተጨማሪ ክህሎት</th>
      
        
         <th>ብሄራዊ መለያ (ID)</th>
         
        <th>የተመዘገበበት ቀን</th>
      
    </tr>
</thead>
<tbody>
    <?php if (!empty($jobSeekers)): ?>
        <?php foreach ($jobSeekers as $index => $js): ?>
            <tr id="row-<?= $js['id'] ?>">
             <td >
    <button class="btn btn-outline-primary btn-sm view-defense-btn"
            data-id="<?= htmlspecialchars($js['id']) ?>"
            title="ሙሉ መረጃ ይመልከቱ">
        <i class="fas fa-eye"></i>
    </button>
    <?php if (AuthHelper::hasRole(['team_leader', 'officer'], [3, 4])): ?>
     <button class="btn btn-outline-warning btn-sm edit-defense-btn"
            data-info="<?= htmlspecialchars(json_encode($js), ENT_QUOTES, 'UTF-8') ?>"
            title="አስተካክል">
        <i class="fas fa-edit"></i>
    </button>
    <?php endif; ?>
</td>
              
                <td><?= $offset + $index + 1 ?></td>
                <td><?= htmlspecialchars($js['fullname']) ?></td>
                <td><?= htmlspecialchars($js['branch_name'] ?? '-') ?></td>
                <td><?= htmlspecialchars($js['kebele'] ?? '-') ?></td>
                <td><?= htmlspecialchars($js['sex']) ?></td>
                <td><?= htmlspecialchars($js['age'] ?? '-') ?></td>
                <td><?= htmlspecialchars($js['phone'] ?? '-') ?></td>
                <td><?= htmlspecialchars($js['education_level'] ?? '-') ?></td>
                <td><?= htmlspecialchars($js['educated_study'] ?? '-') ?></td>
                  <td><?= htmlspecialchars($js['sector'] ?? '-') ?></td>
                <td><?= htmlspecialchars($js['additional_skill'] ?? '-') ?></td>
              
              
                
                <td><?= htmlspecialchars($js['national_id'] ?? '-') ?></td>
                <td><?= htmlspecialchars(date('d/m/Y', strtotime($js['created_at']))) ?></td>
                   
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
$basePath = rtrim($_ENV['BASE_URL'], '/') . '/solgure-registration'; // your actual working route
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
 <!--    <button type="button" class="btn btn-outline-secondary btn-sm me-2" id="printDetailsBtn" title="ይህንን ገጽ አትም">
    <i class="fas fa-print me-1"></i> አትም
</button> -->
    </div>

    <!-- በስተቀኝ በኩል ማስተካከያ እና መዝጊያ አዝራሮች -->
    <div>
        <?php if (AuthHelper::hasRole(['team_leader', 'officer'], [3, 4])): ?>
           
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
document.addEventListener('DOMContentLoaded', function () {
    
    // ==========================================
    // 1. የ Edit Button Click Event & Data Population
    // ==========================================
    const editButtons = document.querySelectorAll('.edit-defense-btn');
    
    // የ Edit ፎርም ኤለመንቶች
    const editForm = document.getElementById('editDefenseForm');
    const editEduLevelSelect = document.getElementById('edit_education_level');
    const editEducatedStudy = document.getElementById('edit_educated_study');
    const editStudyFieldWrapper = document.getElementById('edit_study_field_wrapper');
    const editPhoneInput = document.getElementById('edit_phone');
    const editFullnameInput = document.getElementById('edit_fullname');
    const editAgeInput = document.getElementById('edit_age');
    const editNationalIdInput = document.getElementById('edit_national_id');

    // የትምህርት መስክን የማሳየት/መደበቅ ፈንክሽን ለ Edit Modal
    function toggleEditStudyField() {
        if (!editEduLevelSelect || !editEducatedStudy || !editStudyFieldWrapper) return;
        const selectedValue = editEduLevelSelect.value;
        const requiredLevels = ["ሰርተፊኬት (Level I/II)", "ዲፕሎማ (Level III/IV)", "የመጀመሪያ ዲግሪ", "ከዛ በላይ"];

        if (requiredLevels.includes(selectedValue)) {
            editStudyFieldWrapper.style.display = 'block';
            editEducatedStudy.setAttribute('required', 'required');
        } else {
            editStudyFieldWrapper.style.display = 'none';
            editEducatedStudy.removeAttribute('required');
            editEducatedStudy.value = ''; 
            editEducatedStudy.setCustomValidity(''); 
        }
    }

    if (editEduLevelSelect) {
        editEduLevelSelect.addEventListener('change', toggleEditStudyField);
    }

    // ዳታውን ከ button ላይ በማንበብ ሞዳሉ ላይ መሙላት (Populate Modal)
    editButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            // በ JSON የተቀመጠውን ዳታ ማንበብ
            const dataStr = this.getAttribute('data-info');
            if(!dataStr) return;
            
            const data = JSON.parse(dataStr);

            // ዳታዎቹን በየቦታቸው ማሳረፍ
            document.getElementById('edit_defense_id').value = data.id;
            document.getElementById('edit_fullname').value = data.fullname || '';
            document.getElementById('edit_national_id').value = data.national_id || '';
            document.getElementById('edit_sex').value = data.sex || '';
            document.getElementById('edit_age').value = data.age || '';
            document.getElementById('edit_phone').value = data.phone || '';
            document.getElementById('edit_education_level').value = data.education_level || '';
            document.getElementById('edit_additional_skill').value = data.additional_skill || '';
            document.getElementById('edit_sector').value = data.sector || '';
            document.getElementById('edit_kebele').value = data.kebele || '';
            
            if(data.educated_study) {
                editEducatedStudy.value = data.educated_study;
            }

            // ትምህርት ደረጃን መሰረት አድርጎ መስኩን ማስተካከል
            toggleEditStudyField();

            // Bootstrap Modal መክፈት
            $('#editDefenseModal').modal('show');
        });
    });

    // ==========================================
    // 2. የማስተካከያ (Edit) ፎርም Validation
    // ==========================================
    if (editPhoneInput) {
        editPhoneInput.addEventListener('input', function () {
            this.value = this.value.replace(/[^0-9]/g, ''); 
            if (this.value.length > 0 && this.value[0] !== '0') this.value = '';
            if (this.value.length > 1 && !['9', '7', '1'].includes(this.value[1])) this.value = this.value[0];
        });
    }

    if (editFullnameInput) {
        editFullnameInput.addEventListener('input', function () {
            this.value = this.value.replace(/[0-9!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?`~]/g, '');
        });
    }

    if (editAgeInput) {
        editAgeInput.addEventListener('input', function () {
            let val = parseInt(this.value, 10);
            if (val > 30) this.value = 30;
            else if (val < 0) this.value = '';
        });
    }

    if (editNationalIdInput) {
        editNationalIdInput.addEventListener('input', function () {
            this.value = this.value.replace(/[^a-zA-Z0-9\s\-]/g, '');
        });
    }

    if (editForm) {
        editForm.addEventListener('submit', function (event) {
            let isValid = true;

            if (editPhoneInput && editPhoneInput.value.trim().length !== 10) {
                editPhoneInput.setCustomValidity('የስልክ ቁጥር 10 አሃዝ መሆን አለበት።');
                isValid = false;
            } else if (editPhoneInput) {
                editPhoneInput.setCustomValidity('');
            }

            if (editAgeInput) {
                let ageVal = parseInt(editAgeInput.value, 10);
                if (isNaN(ageVal) || ageVal < 18 || ageVal > 30) {
                    editAgeInput.setCustomValidity('እድሜ ከ18 እስከ 30 መሆን አለበት።');
                    isValid = false;
                } else {
                    editAgeInput.setCustomValidity('');
                }
            }

            if (editFullnameInput) {
                const nameParts = editFullnameInput.value.trim().split(/\s+/);
                if (nameParts.length < 2 || nameParts[0].length < 2) {
                    editFullnameInput.setCustomValidity('እባክዎ ቢያንስ ስም እና የአባት ስም በትክክል ያስገቡ።');
                    isValid = false;
                } else {
                    editFullnameInput.setCustomValidity('');
                }
            }

            if (editEducatedStudy && !editEducatedStudy.hasAttribute('required')) {
                editEducatedStudy.setCustomValidity('');
            }

            if (!editForm.checkValidity() || !isValid) {
                event.preventDefault();
                event.stopPropagation();
            }

            editForm.classList.add('was-validated');
        }, false);
    }
});
</script> 
<?php include 'partials/register-defense-modal.php'; ?>
<?php include 'partials/edit-defense-modal.php'; ?>