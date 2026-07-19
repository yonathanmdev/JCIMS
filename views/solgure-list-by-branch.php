<?php
use App\Helpers\EthiopianDateHelper; 
use App\Helpers\AuthHelper;
$fiscal_year = AuthHelper::checkFiscalYear();
 
$totalCount =0;
$offset = 0;
$currentPage =1;
$totalPages =1;

// Ensure variables are defined to avoid undefined variable notices in the view
if (!isset($title) || $title === '') {
    $title = 'Solgure List by Branch';
}

if (!isset($report) || !is_array($report)) {
    $report = [];
}

if (!isset($sectors) || !is_array($sectors)) {
    $sectors = [];
    if (!empty($report) && is_array($report)) {
        $firstRow = reset($report);
        if (is_array($firstRow)) {
            $sectors = array_keys($firstRow);
        }
    }
}
?>
 
<section class="content">
  <div class="container-fluid">
    <div class="card-header bg-white d-flex flex-column flex-md-row align-items-md-center card-primary card-outline">
 
      
    </div>
     <div class="card-body">
         <div class="card-body">
  <h3><?= htmlspecialchars($title) ?></h3>
<table  id="example1"   class="table table-bordered table-hover small">
    <thead>
        <tr>
            <th>ተ.ቁ</th>
            <th>የመዋቅር ስም</th>
            <?php foreach ($sectors as $s): ?>
                <th><?= htmlspecialchars($s) ?></th>
            <?php endforeach; ?>
            <th>ድምር</th>
        </tr>
    </thead>
    <tbody>
        <?php $i = 1; foreach ($report as $zone => $sectorData): ?>
            <tr>
                <td><?= $i++ ?></td>
                <td><?= htmlspecialchars($zone) ?></td>
                <?php 
                $rowSum = 0;
                foreach ($sectors as $s): 
                    $val = $sectorData[$s] ?? 0;
                    $rowSum += $val;
                ?>
                    <td><?= $val ?></td>
                <?php endforeach; ?>
                <td><strong><?= $rowSum ?></strong></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
 </div>
  </div>
  </div>
</section>
<!-- የጥቅል መረጃ ማሳያ ሞዳል -->
 
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