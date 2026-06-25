<?php
use App\Helpers\EthiopianDateHelper; 
 $is_employee_registration_page = true; ?>
<section class="content">
  <div class="container-fluid">
    <div class="card-header bg-white d-flex flex-column flex-md-row align-items-md-center card-primary card-outline">
  <div class="ml-md-auto">
    <button 
      type="button" 
      class="btn btn-primary btn-sm w-100 w-md-auto"
      data-toggle="modal" 
      data-target="#employeeRegistrationModal"
    >
      <i class="fas fa-user-plus mr-2"></i>
      ሰራተኛ መዝግብ
    </button>
  </div>

</div>

   <div class="card shadow-sm border-0">

  <!-- Header -->
  <div class="card-header bg-white d-flex justify-content-between align-items-center">
    <h6 class="mb-0 font-weight-bold text-dark">
      ምዝገባቸው ያልጽደቀ ሰራተኞች
    </h6>
  </div>

      <div class="card-body">
        <table id="example1" data-empty-msg="ምንም ተመዝግቦ ያልጸደቀ ሰራተኛ የለም።" class="table table-bordered table-striped table-hover small" style="color: #000;" aria-describedby="example2_info">
          <thead class="thead-light">
            <tr>
              <th>#</th>
              <th>መለያ ቁጥር</th>
              <th>ስም</th>
              <th>የስራ መደብ</th>
              <th>ጾታ</th>
              <th>የልደት ቀን</th>
              <th>የምዝገባ ቀን</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($employees)): ?>
              <?php foreach ($employees as $index => $employee): 
                             
                // Split the database date (YYYY-MM-DD)
$dateParts = explode('-', $employee['birth_date']);
$ethDate = EthiopianDateHelper::toEthCalendar($dateParts[2], $dateParts[1], $dateParts[0]);
$regdateParts = explode('-', $employee['rdate']);
$regethDate = EthiopianDateHelper::toEthCalendar($regdateParts[2], $regdateParts[1], $regdateParts[0]);
?>
  

                <tr>
                  <td><?= $index + 1 ?></td>
                  <td><?= htmlspecialchars($employee['employee_id'] ?? '') ?></td>
                  <td><?= htmlspecialchars(trim(($employee['first_name'] ?? '') . ' ' . ($employee['father_name'] ?? '') . ' ' . ($employee['g_father_name'] ?? ''))) ?></td>
                  <td><?= htmlspecialchars($employee['job_name'] ?? 'N/A') ?></td>
                  <td><?= ($employee['sex'] ?? '') === 'Male' ? 'ወንድ' : 'ሴት' ?></td>
                  <td><?= EthiopianDateHelper::getMonthName($ethDate['month']) ?> <?= $ethDate['day'] ?> <?= $ethDate['year'] ?></td>
                   <td><?= EthiopianDateHelper::getMonthName($regethDate['month']) ?> <?= $regethDate['day'] ?> <?= $regethDate['year'] ?></td>

                  <td>
                    <a href="<?= rtrim($_ENV['BASE_URL'], '/') ?>/employee-onboarding-views/<?= htmlspecialchars($employee['uuid'] ?? '') ?>" class="btn btn-sm btn-secondary" title="እይ">
                      <i class="fas fa-eye"></i> 
                    </a>
                     <?php if (isset($userRole) && $userRole === 'team_leader'): ?>
                    <a href="<?= rtrim($_ENV['BASE_URL'], '/') ?>/employee-onboarding-views/<?= htmlspecialchars($employee['uuid'] ?? '') ?>" class="btn btn-sm btn-success" title="አጽድቅ">
                    
<i class="fas fa-check-circle"></i>  <!-- checkmark in circle -->
                    </a>
                  <?php endif; ?>
            <a href="<?= rtrim($_ENV['BASE_URL'], '/') ?>/employee-edit/<?= htmlspecialchars($employee['uuid'] ?? '') ?>/employee-registration" 
   title="አስተካክል" 
   class="btn btn-sm btn-outline-secondary">
    <i class="fas fa-edit"></i>
</a>         
                  </td>
                </tr>
              <?php endforeach; ?>
            
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</section>

<div class="modal fade" id="employeeRegistrationModal" tabindex="-1" role="dialog" aria-labelledby="employeeRegistrationModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <form action="<?= rtrim($_ENV['BASE_URL'], '/') ?>/employee-registration-save" method="POST" enctype="multipart/form-data">
        <!-- 1. Modal Header -->
        <div class="modal-header">
          <h6 class="modal-title font-weight-bold">
            <i class="fas fa-plus mr-1"></i> አዲስ ሰራተኛ መዝግብ
          </h6>
          <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group mb-2">
                <label class="mb-1" for="first_name"><small class="font-weight-bold">ስም</small></label>
                <input type="text" class="form-control form-control-sm" id="first_name" name="first_name" required>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group mb-2">
                <label class="mb-1" for="father_name"><small class="font-weight-bold">የአባት ስም</small></label>
                <input type="text" class="form-control form-control-sm" id="father_name" name="father_name" required>
              </div>
            </div>

               <div class="col-md-4">
              <div class="form-group mb-2">
                <label class="mb-1" for="g_father_name"><small class="font-weight-bold">የአያት ስም</small></label>
                <input type="text" class="form-control form-control-sm" id="g_father_name" name="g_father_name" required>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-4">
              <div class="form-group mb-2">
                <label class="mb-1" for="mother_name"><small class="font-weight-bold">የእናት ሙሉ ስም</small></label>
                <input type="text" class="form-control form-control-sm" id="mother_name" name="mother_name" required>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group mb-2">
                <label class="mb-1" for="sex"><small class="font-weight-bold">ጾታ</small></label>
                <select class="form-control form-control-sm" id="sex" name="sex" required>
                     <option value="">ይምረጡ</option>
                  <option value="Male">ወንድ</option>
                  <option value="Female">ሴት</option>
                </select>
              </div>
            </div>

                  <div class="col-md-4">
              <div class="form-group mb-2">
                <label class="mb-1" for="birth_date"><small class="font-weight-bold">የልደት ቀን</small></label>
  <input type="text" 
       class="ethiopian-date form-control form-control-sm" 
       name="eth_birth_date" 
       id="eth_birth_date"
       data-rule="past" 
       data-gregorian="#birth_date" 
       placeholder="ቀን/ወር/ዓ.ም ይምረጡ" 
       readonly 
       style="background-color: #fff; cursor: pointer;" required>
                <input type="date" class="d-none" id="birth_date" name="birth_date" required >
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-4">
              <div class="form-group mb-2">
                <label class="mb-1" for="phone_number"><small class="font-weight-bold">ስቁ.</small></label>
                <input type="text" class="form-control form-control-sm" id="phone_number" name="phone_number">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group mb-2">
               <label class="mb-1" for="level_of_education"><small class="font-weight-bold">የትምህርት ደረጃ</small></label>
              <select  id="level_of_education" name="level_of_education" class="form-control form-control-sm" required="">
				    <option selected="" disable="" value="">-- ይምረጡ --</option>
				    <option value="የቀለም">የቀለም</option>
				    <option value="8ኛ_ያጠናቀቀ">8ኛ ያጠናቀቀ</option>
				    <option value="10ኛ_ያጠናቀቀ">10ኛ ያጠናቀቀ</option>
				    <option value="12ኛ_ያጠናቀቀ">12ኛ ያጠናቀቀ</option>
				    <option value="ደረጃ_1">ደረጃ 1</option>
				    <option value="ደረጃ_2">ደረጃ 2</option>
				    <option value="ደረጃ_3">ደረጃ 3</option>
				    <option value="ደረጃ_4">ደረጃ 4</option>
				    <option value="ደረጃ_5">ደረጃ 5</option>
				    <option value="የመጀመሪያ_ዲግሪ">የመጀመሪያ ዲግሪ</option>
				    <option value="ሁለተኛ_ዲግሪ">ሁለተኛ ዲግሪ</option>
				    <option value="ሶስተኛ_ዲግሪ">ሶስተኛ ዲግሪ</option>
  				</select>
              </div>
            </div>
<div class="col-md-4">
              <div class="form-group mb-2">
                 <label class="mb-1" for="yegabcha_huneta"><small class="font-weight-bold">የጋብቻ ሁኔታ</small></label>
              <select class="form-control form-control-sm" id="yegabcha_huneta" name="yegabcha_huneta" required>
                            <option value="" selected="selected" disabled="disabled">-- ይምረጡ --</option>   
                            <option value="ያገባ/ች">ያገባ/ች</option>
                            <option value="ያላገባ/ች">ያላገባ/ች</option>
                             <option value="የፈታ/ች">የፈታ/ች</option>
                                </select>
                
              </div>
            </div>

          </div>

          <div class="row">
             
            <div class="col-md-4">
              <div class="form-group mb-2">
                <label class="mb-1" for="job_property_id"><small class="font-weight-bold">የስራ መደቡ መጠሪያ</small></label>
                <select class="form-control form-control-sm" id="job_property_id" name="job_property_id" required>
                  <option value="">-- ይምረጡ --</option>
                  <?php if (!empty($jobs)): ?>
                    <?php foreach ($jobs as $job): ?>
                      <option value="<?= htmlspecialchars($job['id']) ?>" data-wastna="<?= htmlspecialchars($job['wastna']) ?>"><?= htmlspecialchars($job['job_name']) ?> (<?= htmlspecialchars($job['job_identifier_no']) ?>)</option>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </select>
              </div>
            </div>
           
           
            <div class="col-md-4">
              <div class="form-group mb-2">
                <label class="mb-1" for="department"><small class="font-weight-bold">ዲፓርትመንት</small></label>
                <input type="text" class="form-control form-control-sm" id="department" name="department">
              </div>
            </div>

            <div class="col-md-4">
              <div class="form-group mb-2">
                <label class="mb-1" for="employment_situation"><small class="font-weight-bold">የቅጥር ሁኔታ </small></label>
                 <select class="form-control form-control-sm" id="employment_situation" name="employment_situation" required>
                     <option value="">ይምረጡ</option>
                  <option value="ቋሚ">ቋሚ</option>
                    <option value="ጊዜያዊ">ጊዜያዊ</option>
                </select>
               
              </div>
            </div>
          </div>

          <div class="row">
           
             <div class="col-md-4">
              <div class="form-group mb-2">
                <label class="mb-1" for="date_of_employed"><small class="font-weight-bold">የቅጥር ቀን</small></label>
                <input type="text" 
               class="ethiopian-date form-control form-control-sm" 
               name="eth_date_of_employed"
               id="eth_date_of_employed" 
               data-rule="past"  
               data-gregorian="#date_of_employed" 
               placeholder="ቀን/ወር/ዓ.ም" readonly style="background-color: #fff; cursor: pointer;" required>
                <input type="date"  class="d-none" id="date_of_employed" name="date_of_employed" required >
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group mb-2">
                <label class="mb-1" for="immidate_boss"><small class="font-weight-bold">የቅርብ ተጠሪ</small></label>
                <input type="text" class="form-control form-control-sm" id="immidate_boss" name="immidate_boss" required>
              </div>
            </div>
<div class="col-md-4">
              <div class="form-group mb-2">
                <label class="mb-1" for="competency_situation"><small class="font-weight-bold">የብቃት ሁኔታ</small></label>
                 <select class="form-control form-control-sm" id="competency_situation" name="competency_situation" required>
                     <option value="">ይምረጡ</option>
                  <option value="የበቁ">የበቁ</option>
                    <option value="ያልበቁ">ያልበቁ</option>
                    <option value="ያልተመዘኑ">ያልተመዘኑ</option>
                </select>
              </div>
            </div>

          </div>
<div class="row">
  
             <div class="col-md-4">
              <div class="form-group">
                <label class="mb-1" for="displin_situation"><small class="font-weight-bold">የዲሲፕሊን ሁኔታ</small></label>
                
             <select class="form-control form-control-sm"id="displin_situation" name="displin_situation" required>
                            <option selected="" disable="" value="">-- ይምረጡ --</option>   
                            <option value="ምንም የቅጣት ሪኮርድ የሌለባቸው">ምንም የቅጣት ሪኮርድ የሌለባቸው</option>
                             <option value="የጽሁፍ ማስጠንቀቂያ የተሰጣቸው" >የጽሁፍ ማስጠንቀቂያ የተሰጣቸው</option>
                             <option value="እስከ 15 ቀን የሚደርስ የደመወዝ ቅጣት የተቀጡ">እስከ 15 ቀን የሚደርስ የደመወዝ ቅጣት የተቀጡ</option>
                             <option value="እስከ 3 ወር የሚደርስ የደመወዝ ቅጣት የተቀጡ">እስከ 3 ወር የሚደርስ የደመወዝ ቅጣት የተቀጡ</option>
                             <option value="እስከ 2 ዓመት ለሚደርስ ጊዜ ከደረጃና ከደመወዝ ዝቅ የተደረጉ">እስከ 2 ዓመት ለሚደርስ ጊዜ ከደረጃና ከደመወዝ ዝቅ የተደረጉ</option>
                             </select>
              </div>
            </div>
          
 <div class="col-md-4">
              <div class="form-group mb-2">
                <label class="mb-1" for="effeciency"><small class="font-weight-bold">የስራ አፈፃፀም (ለነባር) (%)</small></label>
                <input type="number" step="0.01" min="0" class="form-control form-control-sm" id="effeciency" name="effeciency">
              </div>
            </div>
                  
            
            <div class="col-md-4">
              <div class="form-group">
                <label class="mb-1" for="level_of_effeciency"><small class="font-weight-bold">የአፈፃፀም ደረጃ</small></label>
                <input type="text" class="form-control form-control-sm" id="level_of_effeciency" name="level_of_effeciency" readonly>
              </div>
            </div>

</div>
          <div class="row">

             
            <div class="col-md-6">
              <div class="form-group mb-2">
                <label class="mb-1" for="no_of_files_in_folder"><small class="font-weight-bold">ከማህደራቸው ያለ ጠቅላላ ፋይል ብዛት</small></label>
                <input type="number" step="1" min="0" class="form-control form-control-sm" id="no_of_files_in_folder" name="no_of_files_in_folder" value="0">
              </div>
                    </div>  
<div class="col-md-6">
              <div class="form-group mb-2">
                   <label class="mb-1" for="pension_number"><small class="font-weight-bold">የጡረታ መለያ ቁጥር </small></label>
                <input type="text" class="form-control form-control-sm" id="pension_number" name="pension_number">
            </div>
            </div>
 <div class="col-md-4">
              <div class="form-group mb-2 d-none">
                <label class="mb-1" for="annual_rest"><small class="font-weight-bold">የዓመት እረፍት</small></label>
                <input type="number" step="1" min="0" class="form-control form-control-sm" id="annual_rest" name="annual_rest" value="0">
              </div>
            </div>
                    </div> 
                    <!-- Guarantor fields - hidden by default -->
<div id="guarantor-section" style="display: none;">
  <div class="row">
    <div class="col-md-4">
      <div class="form-group mb-2">
        <label class="mb-1" for="guarantor_name">
          <small class="font-weight-bold">የተያዥ ሙሉ ስም</small>
        </label>
        <input type="text" class="form-control form-control-sm" 
               id="guarantor_name" name="guarantor_name"
               placeholder="ሙሉ ስም ያስገቡ">
      </div>
    </div>
    <div class="col-md-4">
      <div class="form-group mb-2">
        <label class="mb-1" for="guarantor_phone">
          <small class="font-weight-bold">ስልክ</small>
        </label>
        <input type="text" class="form-control form-control-sm" 
               id="guarantor_phone" name="guarantor_phone"
               placeholder="ስልክ ቁጥር ያስገቡ">
      </div>
    </div>
    <div class="col-md-4">
      <div class="form-group mb-2">
        <label class="mb-1" for="guarantor_letter">
          <small class="font-weight-bold">የተያዥ ደብዳቤ</small>
        </label>
        <input type="file" class="form-control form-control-sm" 
       id="guarantor_letter" name="guarantor_letter"
       accept="application/pdf,image/jpeg,image/png,.pdf,.jpg,.jpeg,.png">
      </div>
    </div>
  </div>
</div> 
                     <div class="row">
<div class="col-md-4">
  <div class="form-group">
    <label class="mb-1" for="employee_image">
      <small class="font-weight-bold">ፎቶ</small>
    </label>
    <input type="file" class="form-control-file" id="employee_image" name="employee_image"
           accept="image/jpeg,image/png,.jpg,.jpeg,.png" required>
  </div>
</div>

<div class="col-md-4">
  <div class="form-group">
    <label class="mb-1" for="employee_file201">
      <small class="font-weight-bold">የት/ት ማስረጃ እና ሌሎች</small>
    </label>
    <input type="file" class="form-control-file" id="employee_file201" name="employee_file201"
           accept="application/pdf,image/jpeg,image/png,.pdf,.jpg,.jpeg,.png" required>
  </div>
</div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label class="mb-1" for="remark"><small class="font-weight-bold">Remark</small></label>
                <textarea class="form-control form-control-sm" id="remark" name="remark" rows="3"></textarea>
              </div>
            </div>
          </div>
        </div>

        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">ዝጋ</button>
          <button type="submit" class="btn btn-primary btn-sm">መዝግብ</button>
        </div>
      </form>
    </div>
  </div>
</div>
