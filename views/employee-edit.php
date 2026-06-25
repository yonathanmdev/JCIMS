<?php 
use App\Helpers\EthiopianDateHelper; 
$is_employee_edit_page = true; ?>
<section class="content">
  <div class="container-fluid">
    <div class="card card-primary card-outline">
      <div class="card-header bg-white">
        <h3 class="card-title font-weight-bold"><i class="fas fa-user-edit mr-2"></i>የሰራተኛ ማስተካከያ መረጃ ማስተካከያ)</h3>
        <div class="card-tools">
          <a href="<?= rtrim($_ENV['BASE_URL'], '/') ?>/<?= htmlspecialchars($source) ?>" class="btn btn-tool text-secondary">
            <i class="fas fa-times fa-lg"></i>
          </a>
        </div>
      </div>

      <div class="card-body">
        <form action="<?= rtrim($_ENV['BASE_URL'], '/') ?>/employee-edit-save" method="POST" enctype="multipart/form-data" id="employee-edit-form">
          <input type="hidden" name="uuid" value="<?= htmlspecialchars($employee['uuid'] ?? '') ?>">
<input type="hidden" name="source" value="<?= htmlspecialchars($source) ?>">         <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label for="employee_id">የሰራተኛው መለያ ቁጥር</label>
                <input type="text" class="form-control" id="employee_id" name="employee_id" value="<?= htmlspecialchars($employee['employee_id'] ?? '') ?>" required readonly>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="pension_number">የጡረታ መለያ ቁጥር</label>
                <input type="text" class="form-control" id="pension_number" name="pension_number" value="<?= htmlspecialchars($employee['pension_number'] ?? '') ?>">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="first_name">ስም</label>
                <input type="text" class="form-control" id="first_name" name="first_name" value="<?= htmlspecialchars($employee['first_name'] ?? '') ?>" required>
              </div>
            </div>
          </div>

          <div class="row">
            
            <div class="col-md-4">
              <div class="form-group">
                <label for="father_name">የአባት ስም</label>
                <input type="text" class="form-control" id="father_name" name="father_name" value="<?= htmlspecialchars($employee['father_name'] ?? '') ?>" required>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="g_father_name">የአያት ስም</label>
                <input type="text" class="form-control" id="g_father_name" name="g_father_name" value="<?= htmlspecialchars($employee['g_father_name'] ?? '') ?>" required>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="mother_name">የእናት ሙሉ ስም</label>
                <input type="text" class="form-control" id="mother_name" name="mother_name" value="<?= htmlspecialchars($employee['mother_name'] ?? '') ?>" required>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label for="sex">ጾታ</label>
                <select class="form-control" id="sex" name="sex" required>
                  <option value="">ይምረጡ</option>
                  <option value="Male" <?= ($employee['sex'] ?? '') === 'Male' ? 'selected' : '' ?>>ወንድ</option>
                  <option value="Female" <?= ($employee['sex'] ?? '') === 'Female' ? 'selected' : '' ?>>ሴት</option>
                </select>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="birth_date">የትውልድ ቀን</label>
                <?php
                 // Split the database date (YYYY-MM-DD)
$dateParts = explode('-', $employee['birth_date']);
$ethDate = EthiopianDateHelper::toEthCalendar($dateParts[2], $dateParts[1], $dateParts[0]);
?>
          <input type="text" 
       class="ethiopian-date form-control" 
       name="eth_birth_date" 
       data-rule="past" 
       data-gregorian="#birth_date" 
       placeholder="ቀን/ወር/ዓ.ም ይምረጡ" 
       value="<?= EthiopianDateHelper::getMonthName($ethDate['month']) ?> <?= $ethDate['day'] ?> <?= $ethDate['year'] ?>"
       readonly 
       style="background-color: #fff; cursor: pointer;">
      <input type="date" class="d-none" id="birth_date" name="birth_date" value="<?= htmlspecialchars($employee['birth_date'] ?? '') ?>" required>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="phone_number">ስቁ.</label>
                <input type="text" class="form-control" id="phone_number" name="phone_number" value="<?= htmlspecialchars($employee['phone_number'] ?? '') ?>">
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label for="yegabcha_huneta">የጋብቻ ሁኔታ</label>
              <select class="form-control" id="yegabcha_huneta" name="yegabcha_huneta" required>
                            <option <?= ($employee['yegabcha_huneta'] ?? '') === 'ያገባ/ች' ? 'selected' : '' ?>>ያገባ/ች</option>
                            <option <?= ($employee['yegabcha_huneta'] ?? '') === 'ያላገባ/ች' ? 'selected' : '' ?>>ያላገባ/ች</option>
                             <option <?= ($employee['yegabcha_huneta'] ?? '') === 'የፈታ/ች' ? 'selected' : '' ?>>የፈታ/ች</option>
                                </select>
              </div>
            </div>
            <div class="col-md-4">
    <div class="form-group">
        <label for="job_property_id">የስራ መደብ</label>
        <select class="form-control" id="job_property_id" name="job_property_id"
                data-current-job="<?= htmlspecialchars($employee['job_property_id'] ?? '') ?>"
                required>
            <?php if (!empty($availableJobs)): ?>
                <?php foreach ($availableJobs as $job): ?>
                    <option value="<?= htmlspecialchars($job['id']) ?>"
                            data-wastna="<?= htmlspecialchars($job['wastna']) ?>"
                            <?= ($employee['job_property_id'] ?? '') == $job['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($job['job_name']) ?>
                    </option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
    </div>
</div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="date_of_employed">የቅጥር ቀን</label>
             <?php
                 
                 // Split the database date (YYYY-MM-DD)
$empdateParts = explode('-', $employee['date_of_employed']);
$empethDate = EthiopianDateHelper::toEthCalendar($empdateParts[2], $empdateParts[1], $empdateParts[0]);
?>
 <input type="text" 
       class="ethiopian-date form-control" 
       name="eth_date_of_employed" 
       data-rule="past" 
       data-gregorian="#date_of_employed" 
       placeholder="ቀን/ወር/ዓ.ም ይምረጡ" 
       value="<?= EthiopianDateHelper::getMonthName($empethDate['month']) ?> <?= $empethDate['day'] ?> <?= $empethDate['year'] ?>"
       readonly 
       style="background-color: #fff; cursor: pointer;">
                <input type="date" class="d-none"  id="date_of_employed" name="date_of_employed" value="<?= htmlspecialchars($employee['date_of_employed'] ?? '') ?>">
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label for="level_of_education">የትምህርት ደረጃ</label>
                <select id="level_of_education" name="level_of_education" class="form-control" required="">
                  <option selected="" disable="" value="">-- ይምረጡ --</option>
                  <option value="የቀለም" <?= ($employee['level_of_education'] ?? '') === 'የቀለም' ? 'selected' : '' ?>>የቀለም</option>
                  <option value="8ኛ_ያጠናቀቀ" <?= ($employee['level_of_education'] ?? '') === '8ኛ_ያጠናቀቀ' ? 'selected' : '' ?>>8ኛ ያጠናቀቀ</option>
                  <option value="10ኛ_ያጠናቀቀ" <?= ($employee['level_of_education'] ?? '') === '10ኛ_ያጠናቀቀ' ? 'selected' : '' ?>>10ኛ ያጠናቀቀ</option>
                  <option value="12ኛ_ያጠናቀቀ" <?= ($employee['level_of_education'] ?? '') === '12ኛ_ያጠናቀቀ' ? 'selected' : '' ?>>12ኛ ያጠናቀቀ</option>
                  <option value="ደረጃ_1" <?= ($employee['level_of_education'] ?? '') === 'ደረጃ_1' ? 'selected' : '' ?>>ደረጃ 1</option>
                  <option value="ደረጃ_2" <?= ($employee['level_of_education'] ?? '') === 'ደረጃ_2' ? 'selected' : '' ?>>ደረጃ 2</option>
                  <option value="ደረጃ_3" <?= ($employee['level_of_education'] ?? '') === 'ደረጃ_3' ? 'selected' : '' ?>>ደረጃ 3</option>
                  <option value="ደረጃ_4" <?= ($employee['level_of_education'] ?? '') === 'ደረጃ_4' ? 'selected' : '' ?>>ደረጃ 4</option>
                  <option value="ደረጃ_5" <?= ($employee['level_of_education'] ?? '') === 'ደረጃ_5' ? 'selected' : '' ?>>ደረጃ 5</option>
                  <option value="የመጀመሪያ_ዲግሪ" <?= ($employee['level_of_education'] ?? '') === 'የመጀመሪያ_ዲግሪ' ? 'selected' : '' ?>>የመጀመሪያ ዲግሪ</option>
                  <option value="ሁለተኛ_ዲግሪ" <?= ($employee['level_of_education'] ?? '') === 'ሁለተኛ_ዲግሪ' ? 'selected' : '' ?>>ሁለተኛ ዲግሪ</option>
                  <option value="ሶስተኛ_ዲግሪ" <?= ($employee['level_of_education'] ?? '') === 'ሶስተኛ_ዲግሪ' ? 'selected' : '' ?>>ሶስተኛ ዲግሪ</option>
                </select>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="department">የሙያ ዘርፍ</label>
                <input type="text" class="form-control" id="department" name="department" value="<?= htmlspecialchars($employee['department'] ?? '') ?>">
              </div>
            </div>
          
            <div class="col-md-4">
              <div class="form-group">
                <label for="employment_situation">የቅጥር ሁኔታ </label>
                 <select class="form-control" id="employment_situation" name="employment_situation" required>
                     <option value="">ይምረጡ</option>
                  <option <?= ($employee['employment_situation'] ?? '') === 'ቋሚ' ? 'selected' : '' ?>>ቋሚ</option>
                    <option <?= ($employee['employment_situation'] ?? '') === 'ጊዜያዊ' ? 'selected' : '' ?>>ጊዜያዊ</option>
                </select>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label for="immidate_boss">የቅርብ ተጠሪ</label>
                <input type="text" class="form-control" id="immidate_boss" name="immidate_boss" value="<?= htmlspecialchars($employee['immidate_boss'] ?? '') ?>" required>
              </div>
            </div>
            <div class="col-md-4 d-none">
              <div class="form-group">
                <label for="annual_rest">የዓመት እረፍት</label>
                <input type="number" step="1" min="0" class="form-control" id="annual_rest" name="annual_rest" value="<?= htmlspecialchars($employee['annual_rest'] ?? '0') ?>">
              </div>
            </div>
          
            <div class="col-md-4">
              <div class="form-group">
                <label for="effeciency">Efficiency (%)</label>
                <input type="number" step="0.01" min="0" class="form-control" id="effeciency" name="effeciency" value="<?= htmlspecialchars($employee['effeciency'] ?? '') ?>">
              </div>
            </div>

            <div class="col-md-4">
              <div class="form-group">
                <label for="level_of_effeciency">Level of Efficiency</label>
                <input type="text" class="form-control" id="level_of_effeciency" name="level_of_effeciency" value="<?= htmlspecialchars($employee['level_of_effeciency'] ?? '') ?>" readonly>
              </div>
            </div>
          </div>

          <div class="row">
          
            <div class="col-md-4 d-none">
              <div class="form-group">
                <label for="experience">የስራ ልምድ</label>
                <input type="text" class="form-control" id="experience" name="experience" value="<?= htmlspecialchars($employee['experience'] ?? '') ?>">
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label for="no_of_files_in_folder">የማህደር የፋይል ብዛት</label>
                <input type="number" step="1" min="0" class="form-control" id="no_of_files_in_folder" name="no_of_files_in_folder" value="<?= htmlspecialchars($employee['no_of_files_in_folder'] ?? '0') ?>">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="displin_situation">የዲሲፕሊን ሁኔታ</label>
                 <select class="form-control" id="displin_situation" name="displin_situation" required>
                     <option value="" <?= empty($employee['displin_situation']) ? 'selected' : '' ?>>-- ይምረጡ --</option>   
                     <option value="ምንም የቅጣት ሪኮርድ የሌለባቸው" <?= ($employee['displin_situation'] ?? '') === 'ምንም የቅጣት ሪኮርድ የሌለባቸው' ? 'selected' : '' ?>>ምንም የቅጣት ሪኮርድ የሌለባቸው</option>
                     <option value="የጽሁፍ ማስጠንቀቂያ የተሰጣቸው" <?= ($employee['displin_situation'] ?? '') === 'የጽሁፍ ማስጠንቀቂያ የተሰጣቸው' ? 'selected' : '' ?>>የጽሁፍ ማስጠንቀቂያ የተሰጣቸው</option>
                     <option value="እስከ 15 ቀን የሚደርስ የደመወዝ ቅጣት የተቀጡ" <?= ($employee['displin_situation'] ?? '') === 'እስከ 15 ቀን የሚደርስ የደመወዝ ቅጣት የተቀጡ' ? 'selected' : '' ?>>እስከ 15 ቀን የሚደርስ የደመወዝ ቅጣት የተቀጡ</option>
                     <option value="እስከ 3 ወር የሚደርስ የደመወዝ ቅጣት የተቀጡ" <?= ($employee['displin_situation'] ?? '') === 'እስከ 3 ወር የሚደርስ የደመወዝ ቅጣት የተቀጡ' ? 'selected' : '' ?>>እስከ 3 ወር የሚደርስ የደመወዝ ቅጣት የተቀጡ</option>
                     <option value="እስከ 2 ዓመት ለሚደርስ ጊዜ ከደረጃና ከደመወዝ ዝቅ የተደረጉ" <?= ($employee['displin_situation'] ?? '') === 'እስከ 2 ዓመት ለሚደርስ ጊዜ ከደረጃና ከደመወዝ ዝቅ የተደረጉ' ? 'selected' : '' ?>>እስከ 2 ዓመት ለሚደርስ ጊዜ ከደረጃና ከደመወዝ ዝቅ የተደረጉ</option>
                 </select>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="competency_situation">የብቃት ሁኔታ</label>
                 <select class="form-control" id="competency_situation" name="competency_situation">
                     <option value="" <?= empty($employee['competency_situation']) ? 'selected' : '' ?>>ይምረጡ</option>
                     <option value="የበቁ" <?= ($employee['competency_situation'] ?? '') === 'የበቁ' ? 'selected' : '' ?>>የበቁ</option>
                     <option value="ያልበቁ" <?= ($employee['competency_situation'] ?? '') === 'ያልበቁ' ? 'selected' : '' ?>>ያልበቁ</option>
                     <option value="ያልተመዘኑ" <?= ($employee['competency_situation'] ?? '') === 'ያልተመዘኑ' ? 'selected' : '' ?>>ያልተመዘኑ</option>
                 </select>
              </div>
            </div>
          </div>
<div id="guarantor-section" data-has-guarantor="<?= !empty($guarantor) ? '1' : '0' ?>" style="display: none;">
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label for="edit_guarantor_name">የተያዥ ሙሉ ስም</label>
                <input type="text" class="form-control" id="edit_guarantor_name" name="guarantor_name"
                       value="<?= htmlspecialchars($guarantor['guarantor_name'] ?? '') ?>">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="edit_guarantor_phone">ስልክ</label>
                <input type="text" class="form-control" id="edit_guarantor_phone" name="guarantor_phone"
                       value="<?= htmlspecialchars($guarantor['guarantor_phone'] ?? '') ?>">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="edit_guarantor_letter">የተያዥ ደብዳቤ</label>
                <input type="file" class="form-control-file" id="edit_guarantor_letter" name="guarantor_letter"
                       accept="application/pdf,image/jpeg,image/png,.pdf,.jpg,.jpeg,.png">
                <?php if (!empty($guarantor['guarantor_letter'])): ?>
                    <small class="form-text text-muted">አሁን ያለው ፋይል:
                        <a href="<?= rtrim($_ENV['BASE_URL'], '/') ?>?action=serve-file&file=<?= urlencode($guarantor['guarantor_letter']) ?>&type=guarantor"
                           target="_blank">ተመልከት</a>
                    </small>
                <?php endif; ?>
                <small class="form-text text-muted">አዲስ ፋይል ካልመረጡ ያለው ይቆያል።</small>
            </div>
        </div>
    </div>
</div>
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label for="employee_image">ፎቶ</label>
                <input type="file" class="form-control-file" id="employee_image" name="employee_image" accept="image/*">
                <?php if (!empty($employee['employee_image'])): ?>
                  <small class="form-text text-muted">አሁን የሆነ ፎቶ: 
                    <a href="<?= rtrim($_ENV['BASE_URL'], '/') ?>?action=serve-file&file=<?= htmlspecialchars($employee['employee_image']) ?>&type=image" target="_blank">ተመልክት</a></small>
                  <?php endif; ?>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="employee_file201">የት/ት ማስረጃ እና ሌሎች</label>
                <input type="file" class="form-control-file" id="employee_file201" name="employee_file201">
                <?php if (!empty($employee['employee_file201'])): ?>
          <small class="form-text text-muted"> አሁን የሆነ ፋይል:
          <a href="<?= rtrim($_ENV['BASE_URL'], '/') ?>?action=serve-file&file=<?= htmlspecialchars($employee['employee_file201']) ?>&type=document" target="_blank">ተመልክት</a>
          </small>
          <?php endif; ?>
              </div>
            </div>
          </div>
            <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="remark">Remark</label>
                <textarea class="form-control" id="remark" name="remark" rows="2"><?= htmlspecialchars($employee['remark'] ?? '') ?></textarea>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-12">
              <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-warning mr-2">
                  <i class="fas fa-save"></i> አስተካክል
                </button>
                <a href="<?= rtrim($_ENV['BASE_URL'], '/') ?>/<?= htmlspecialchars($source) ?>" class="btn btn-secondary">
                  <i class="fas fa-times"></i> ዝጋ
                </a>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>
<div class="modal fade" id="jobChangeConfirmModal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h6 class="modal-title">እርግጠኛ ነዎት?</h6>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body py-2">
                <p class="mb-1">የስራ መደቡን መቀየር ይፈልጋሉ?</p>
                <p class="mb-1">ከ: <strong id="job-confirm-from"></strong></p>
                <p class="mb-0">ወደ: <strong id="job-confirm-to"></strong></p>
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">ይቅር</button>
                <button type="button" class="btn btn-sm btn-primary" id="jobConfirmYes">አዎ፣ ቀይር</button>
            </div>
        </div>
    </div>
</div>