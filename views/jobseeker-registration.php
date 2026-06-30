<?php
use App\Helpers\EthiopianDateHelper; 
use App\Helpers\AuthHelper;
$fiscal_year = AuthHelper::checkFiscalYear();
$is_jobseeker_registration_page = true; ?>
<section class="content">
  <div class="container-fluid">
    <div class="card-header bg-white d-flex flex-column flex-md-row align-items-md-center card-primary card-outline">
      <div class="ml-md-auto">
        <button 
          type="button" 
          class="btn btn-primary btn-sm w-100 w-md-auto"
          data-toggle="modal" 
          data-target="#jobseekerRegistrationModal"
        >
          <i class="fas fa-user-plus mr-2"></i>
          ስራ ፈላጊ መዝግብ
        </button>
      </div>
    </div>
  </div>
</section>

<div class="modal fade" id="jobseekerRegistrationModal" tabindex="-1" role="dialog" aria-labelledby="employeeRegistrationModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <form action="<?= rtrim($_ENV['BASE_URL'], '/') ?>/jobseeker-registration-process" method="POST" enctype="multipart/form-data" id="jobseekerForm">
        <!-- Modal Header -->
        <div class="modal-header">
          <h6 class="modal-title font-weight-bold">
            <i class="fas fa-plus mr-1"></i> የስራ ፈላጊ መመዝገቢያ ፎርም
          </h6>
          <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>

        <div class="modal-body">

          <!-- Progress bar -->
          <div class="progress mb-3" style="height: 6px;">
            <div class="progress-bar bg-primary" id="formProgressBar" role="progressbar" style="width: 33%;"></div>
          </div>
          <div class="d-flex justify-content-between mb-3">
            <small id="stepLabel" class="font-weight-bold text-muted">ደረጃ 1 / 3 — የግል መረጃ</small>
          </div>

          <!-- STEP 1: Personal Info -->
          <div class="form-step" id="step-1">
            <div class="row">
              <div class="col-12 col-sm-6 col-md-3">
                <div class="form-group mb-2">
                  <label class="mb-1" for="first_name"><small class="font-weight-bold">ስም <span class="text-danger">*</span></small></label>
                  <input type="text" class="form-control form-control-sm" id="first_name" name="first_name" required>
                </div>
              </div>
              <div class="col-12 col-sm-6 col-md-3">
                <div class="form-group mb-2">
                  <label class="mb-1" for="father_name"><small class="font-weight-bold">የአባት ስም <span class="text-danger">*</span></small></label>
                  <input type="text" class="form-control form-control-sm" id="father_name" name="father_name" required>
                </div>
              </div>
              <div class="col-12 col-sm-6 col-md-3">
                <div class="form-group mb-2">
                  <label class="mb-1" for="g_father_name"><small class="font-weight-bold">የአያት ስም <span class="text-danger">*</span></small></label>
                  <input type="text" class="form-control form-control-sm" id="g_father_name" name="g_father_name" required>
                </div>
              </div>
              <div class="col-12 col-sm-6 col-md-3">
                <div class="form-group mb-2">
                  <label class="mb-1" for="age"><small class="font-weight-bold">እድሜ <span class="text-danger">*</span></small></label>
                  <input type="number" class="form-control form-control-sm" id="age" name="age" min="15" max="65" required>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-12 col-sm-6 col-md-3">
                <div class="form-group mb-2">
                  <label class="mb-1" for="gender"><small class="font-weight-bold">ጾታ <span class="text-danger">*</span></small></label>
                  <select class="form-control form-control-sm" id="gender" name="gender" required>
                    <option value="" selected disabled>ይምረጡ</option>
                    <option value="ወንድ">ወንድ</option>
                    <option value="ሴት">ሴት</option>
                  </select>
                </div>
              </div>
            </div>
          </div>

          <!-- STEP 2: Education Info -->
          <div class="form-step d-none" id="step-2">
            <div class="row">
              <div class="col-12 col-sm-6 col-md-3">
                <div class="form-group mb-2">
                  <label class="mb-1" for="educational_level"><small class="font-weight-bold">የትምህርት ደረጃ <span class="text-danger">*</span></small></label>
                  <select class="form-control form-control-sm" id="educational_level" name="educational_level" data-step-field="true" required>
                    <option value="" selected disabled>ይምረጡ</option>
                    <option value="ማንበብና መፃፍ የማይችሉ">ማንበብና መፃፍ የማይችሉ</option>
                    <option value="መሰረተ ትምህርት">መሰረተ ትምህርት</option>
                    <option value="ከ1-7ኛ">ከ1-7ኛ</option>
                    <option value="8ኛ ያጠናቀቁ">8ኛ ያጠናቀቀ/ች</option>
                    <option value="ከ9-10ኛ">ከ9-10ኛ</option>
                    <option value="ከ11-12ኛ">ከ11-12ኛ</option>
                    <option value="ደረጃ 2">10+1(ደረጃ 1 እና 2)</option>
                    <option value="ደረጃ 3">10+2(ደረጃ 3)</option>
                    <option value="ደረጃ 4">10+3(ደረጃ 4)</option>
                    <option value="ደረጃ 5">ደረጃ 5</option>
                    <option value="የመጀመሪያ ዲግሪ">የመጀመሪያ ዲግሪ</option>
                    <option value="ሁለተኛ ዲግሪ">ሁለተኛ ዲግሪ</option>
                  </select>
                </div>
              </div>

              <div class="col-12 col-sm-6 col-md-3 field-school">
                <div class="form-group mb-2">
                  <label class="mb-1" for="schoolname"><small class="font-weight-bold">የተማረበት ት/ቤት/ኮሌጂ/ዩንቨርስቲ <span class="text-danger">*</span></small></label>
                  <input type="text" name="schoolname" id="schoolname" class="form-control form-control-sm" placeholder="የተማረበት ት/ቤት/ኮሌጂ/ዩንቨርስቲ ">
                </div>
              </div>

              <div class="col-12 col-sm-6 col-md-3 field-dept">
                <div class="form-group mb-2">
                  <label class="mb-1" for="dpt"><small class="font-weight-bold">የተመረቀበት ዲፓርትመንት <span class="text-danger">*</span></small></label>
                  <input type="text" name="dpt" id="dpt" class="form-control form-control-sm" placeholder="የተመረቀበት ዲፓርትመንት">
                </div>
              </div>

              <div class="col-12 col-sm-6 col-md-3 field-year">
                <div class="form-group mb-2">
                  <label class="mb-1" for="education_completion_year"><small class="font-weight-bold">ትምህርት ያጠናቀቀበት (ያቋረጠበት) ዓመት <span class="text-danger">*</span></small></label>
                  <select class="form-control form-control-sm" id="education_completion_year" name="education_completion_year" required>
                    <option value="" selected disabled>ይምረጡ</option>
                    <?php for ($year = $fiscal_year; $year >= 1960; $year--): ?>
                      <option value="<?= $year ?>"><?= $year ?></option>
                    <?php endfor; ?>
                  </select>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-12 col-sm-6 col-md-3 field-grade8">
                <div class="form-group mb-2">
                  <label class="mb-1" for="gradeEight"><small class="font-weight-bold">የ8ኛ ክፍል መለያ ቁጥር <span class="text-danger">*</span></small></label>
                  <input type="text" name="gradeEight" id="gradeEight" class="form-control form-control-sm" placeholder="የ8ኛ ክፍል መለያ ቁጥር">
                </div>
              </div>
            </div>
          </div>

          <!-- STEP 3: Review / Submit -->
          <div class="form-step d-none" id="step-3">
            <p class="text-muted">ከመመዝገብዎ በፊት መረጃዎን ያረጋግጡ።</p>
          </div>

        </div>

        <div class="modal-footer d-flex justify-content-between">
          <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">ዝጋ</button>
          <div>
            <button type="button" class="btn btn-secondary btn-sm" id="prevBtn" data-step-action="prev" disabled>ተመለስ</button>
            <button type="button" class="btn btn-primary btn-sm" id="nextBtn" data-step-action="next">ቀጣይ</button>
            <button type="submit" class="btn btn-success btn-sm d-none" id="submitBtn">መዝግብ</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<script nonce="<?= $GLOBALS['nonce'] ?>">
(function () {
  let currentStep = 1;
  const totalSteps = 3;
  const stepLabels = {
    1: 'ደረጃ 1 / 3 — የግል መረጃ',
    2: 'ደረጃ 2 / 3 — የትምህርት መረጃ',
    3: 'ደረጃ 3 / 3 — ማረጋገጫ'
  };

  function changeStep(direction) {
    const currentStepEl = document.getElementById(`step-${currentStep}`);

    if (direction === 1) {
      const inputs = currentStepEl.querySelectorAll('[required]');
      for (const input of inputs) {
        if (!input.checkValidity()) {
          input.reportValidity();
          return;
        }
      }
    }

    currentStepEl.classList.add('d-none');
    currentStep += direction;
    document.getElementById(`step-${currentStep}`).classList.remove('d-none');

    updateProgress();
  }

  function updateProgress() {
    const percent = (currentStep / totalSteps) * 100;
    document.getElementById('formProgressBar').style.width = `${percent}%`;
    document.getElementById('stepLabel').textContent = stepLabels[currentStep];

    document.getElementById('prevBtn').disabled = currentStep === 1;
    document.getElementById('nextBtn').classList.toggle('d-none', currentStep === totalSteps);
    document.getElementById('submitBtn').classList.toggle('d-none', currentStep !== totalSteps);
  }

  function resetWizard() {
    currentStep = 1;
    document.querySelectorAll('.form-step').forEach((el, i) => {
      el.classList.toggle('d-none', i !== 0);
    });
    updateProgress();
  }

  // Conditional field visibility based on education level (illiterate/basic = hide school fields)
  function applyEducationLevelLogic() {
    const eduLevel = document.getElementById('educational_level').value;
    const advancedFields = document.querySelectorAll(
      '.field-school, .field-dept, .field-year, .field-grade8'
    );
    const hideFor = ['ማንበብና መፃፍ የማይችሉ', 'መሰረተ ትምህርት'];

    if (hideFor.includes(eduLevel)) {
      advancedFields.forEach(el => {
        el.classList.add('d-none');
        const input = el.querySelector('input, select');
        if (input) {
          input.removeAttribute('required');
          input.value = '';
        }
      });
    } else {
      advancedFields.forEach(el => el.classList.remove('d-none'));
      document.getElementById('schoolname')?.setAttribute('required', 'required');
      document.getElementById('dpt')?.setAttribute('required', 'required');
      document.getElementById('gradeEight')?.setAttribute('required', 'required');
      document.getElementById('education_completion_year')?.setAttribute('required', 'required');
    }
  }

  document.addEventListener('DOMContentLoaded', function () {
    // Step navigation — bound via data-step-action, no inline onclick
    document.getElementById('prevBtn').addEventListener('click', () => changeStep(-1));
    document.getElementById('nextBtn').addEventListener('click', () => changeStep(1));

    // Education level conditional logic — bound via addEventListener, no inline onchange
    document.getElementById('educational_level').addEventListener('change', applyEducationLevelLogic);

    // Reset wizard each time modal opens
    $('#jobseekerRegistrationModal').on('show.bs.modal', resetWizard);
  });
})();
</script>