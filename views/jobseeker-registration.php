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
            <small id="stepLabel" class="font-weight-bold text-muted">ደረጃ 1 / 3 — መግቢያ</small>
          </div>

          <!-- STEP 1: Personal Info -->
          <div class="form-step" id="step-1">
            <div class="row">
              <div class="col-12 col-sm-6 col-md-3">
                <div class="form-group mb-2">
                  <label class="mb-1" for="first_name"><small class="font-weight-bold">ስም <span class="text-danger">*</span></small></label>
                  <input type="text" class="form-control form-control-sm" id="first_name" name="first_name" data-validate="name-only" required>
                </div>
              </div>
              <div class="col-12 col-sm-6 col-md-3">
                <div class="form-group mb-2">
                  <label class="mb-1" for="father_name"><small class="font-weight-bold">የአባት ስም <span class="text-danger">*</span></small></label>
                  <input type="text" class="form-control form-control-sm" id="father_name" name="father_name" data-validate="name-only" required>
                </div>
              </div>
             
              <div class="col-12 col-sm-6 col-md-3">
                <div class="form-group mb-2">
                  <label class="mb-1" for="g_father_name"><small class="font-weight-bold">የአያት ስም <span class="text-danger">*</span></small></label>
                  <input type="text" class="form-control form-control-sm" id="g_father_name" name="g_father_name" data-validate="name-only"required>
                </div>
              </div>
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
            <div class="row">
              <div class="col-12 col-sm-6 col-md-3">
                <div class="form-group mb-2">
                  <label class="mb-1" for="age"><small class="font-weight-bold">እድሜ <span class="text-danger">*</span></small></label>
                  <input type="number" class="form-control form-control-sm" id="age" name="age" min="15" max="65" required>
                </div>
              </div>
              <div class="col-12 col-sm-6 col-md-3">
                <div class="form-group mb-2">
                  <label class="mb-1" for="srafelagi_huneta"><small class="font-weight-bold">የስራ ፈላጊ ሁኔታ <span class="text-danger">*</span></small></label>
                  <select class="form-control form-control-sm" id="srafelagi_huneta" name="srafelagi_huneta" required>
                    <option value="" selected disabled>ይምረጡ</option>
                    <option value="ስራ ፈላጊ">ስራ ፈላጊ</option>
                    <option value="ተፈናቃይ">ተፈናቃይ</option>
                    <option value="ከስደት ተመላሽ">ከስደት ተመላሽ</option>
                    <option value="የቤት እመቤት">የቤት እመቤት</option>
                    <option value="አካል ጉዳተኛ">አካል ጉዳተኛ</option>
                    <option value="ዓለም አቀፍ ፍልሰተኛ">ዓለም አቀፍ ፍልሰተኛ</option>
                  </select>
                </div>
              </div>
              <div class="col-12 col-sm-6 col-md-3">
                <div class="form-group mb-2">
                  <label class="mb-1" for="Labor_ID"><small class="font-weight-bold">Labor_ID <span class="text-danger">*</span></small></label>
                  <input type="text" class="form-control form-control-sm" id="Labor_ID" name="Labor_ID" required>
                </div>
              </div>
              <div class="col-12 col-sm-6 col-md-3">
                <div class="form-group mb-2">
                  <label class="mb-1" for="maritalstatus"><small class="font-weight-bold">የጋብቻ ሁኔታ <span class="text-danger">*</span></small></label>
                  <select class="form-control form-control-sm" id="maritalstatus" name="maritalstatus" required>
                    <option value="" selected disabled>ይምረጡ</option>
                    <option value="ያላገባ/ች">ያላገባ/ች</option>
                    <option value="ያገባ/ች">ያገባ/ች</option>
                    <option value="የፈታ/ች">የፈታ/ች</option>
                    <option value="የሞተበት/ባት">የሞተበት/ባት</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-12 col-sm-6 col-md-3">
                <div class="form-group mb-2">
                  <label class="mb-1" for="age"><small class="font-weight-bold">FAN <span class="text-danger">*</span></small></label>
                  <input type="number" class="form-control form-control-sm" id="FAN" name="FAN"  required>
                </div>
              </div>
              <div class="col-12 col-sm-6 col-md-3">
                <div class="form-group mb-2">
                  <label class="mb-1" for="mender"><small class="font-weight-bold">መንደር </small></label>
                  <input type="text" class="form-control form-control-sm" id="mender" name="mender">
                </div>
              </div>
              <div class="col-12 col-sm-6 col-md-3">
                <div class="form-group mb-2">
                  <label class="mb-1" for="kebele_id_no"><small class="font-weight-bold">የቀበሌ መታወቂያ ቁጥር <span class="text-danger">*</span></small></label>
                  <input type="text" class="form-control form-control-sm" id="kebele_id_no" name="kebele_id_no" required>
                </div>
              </div>
              <div class="col-12 col-sm-6 col-md-3">
                <div class="form-group mb-2">
                  <label class="mb-1" for="residence_status"><small class="font-weight-bold">የሚኖርበት አካባቢ <span class="text-danger">*</span></small></label>
                  <select class="form-control form-control-sm" id="residence_status" name="residence_status" required>
                    <option value="" selected disabled>ይምረጡ</option>
                    <option value="ከተማ">ከተማ</option>
                    <option value="ገጠር">ገጠር</option>
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
                  <label class="mb-1" for="school_type"><small class="font-weight-bold">የት/ቤቱ/የኮሌጁ ዓይነት <span class="text-danger">*</span></small></label>
                  <select class="form-control form-control-sm" id="school_type" name="school_type" required>
                    <option value="" selected disabled>ይምረጡ</option>
                    <option value="የመንግስት">የመንግስት</option>
                    <option value="የግል">የግል</option>
                  </select>
                </div>
              </div>
              <div class="col-12 col-sm-6 col-md-3 field-grade8">
                <div class="form-group mb-2">
                  <label class="mb-1" for="gradeEight"><small class="font-weight-bold">የ8ኛ ክፍል መለያ ቁጥር <span class="text-danger">*</span></small></label>
                  <input type="text" name="gradeEight" id="gradeEight" class="form-control form-control-sm" placeholder="የ8ኛ ክፍል መለያ ቁጥር">
                </div>
              </div>
              <div class="col-12 col-sm-6 col-md-3 field-grade8">
                <div class="form-group mb-2">
                 <label class="mb-1" for="phone_number"><small class="font-weight-bold">ስልክ ቁጥር <span class="text-danger">*</span></small></label>
                  <input type="number" class="form-control form-control-sm" id="phone_number" name="phone_number" required>
                </div>
              </div>
              <div class="col-12 col-sm-6 col-md-3 field-grade8">
                <div class="form-group mb-2">
                 <label class="mb-1" for="graguation_catagory"><small class="font-weight-bold">ያጠናቀቁበት ሙያ የስራ ምድብ<span class="text-danger">*</span></small></label>
                  <select class="form-control" name="graguation_catagory" id="graguation_catagory" >
    <option value="" selected="selected">&larr; ይምረጡ &rarr;</option>
 <option  >Accounting and Finance Jobs</option>
<option  >Admin, Secretarial and Clerical Jobs</option>
<option  >Advertising and Media Jobs</option>
<option  >Agriculture Jobs</option>
<option  >Architecture and Construction Jobs</option>
<option  >Automotive Jobs</option>
<option  >Banking and Insurance Jobs</option>
<option  >Business Development Jobs</option>
<option  >Business and Administration Jobs</option>
<option  >Communications, PR and Journalism Jobs</option>
<option  >Community Service Jobs</option>
<option  >Consultancy and Training Jobs</option>
<option  >Creative Arts Jobs</option>
<option  >Customer Service Jobs</option>
<option  >Development and Project Management Jobs</option>
<option  >Economics Jobs</option>
<option  >Education Jobs</option>
<option  >Engineering Jobs</option>
<option  >Environment and Natural Resource Jobs</option>
<option  >Event Management Jobs</option>
<option  >Health Care Jobs</option>
<option  >Hotel and Hospitality Jobs</option>
<option  >Human Resource and Recruitment Jobs</option>
<option  >Information Technology Jobs</option>
<option  >Languages Jobs</option>
<option  >Legal Jobs</option>
<option  >Logistics, Transport and Supply Chain Jobs</option>
<option  >Maintenance Jobs</option>
<option  >Management Jobs</option>
<option  >Manufacturing Jobs</option>
<option  >Media and Journalism Jobs</option>
<option  >Natural Sciences Jobs</option>
<option  >Pharmaceutical Jobs</option>
<option  >Purchasing and Procurement Jobs</option>
<option  >Quality Assurance Jobs</option>
<option  >Research and Development Jobs</option>
<option  >Retail, Wholesale and Distribution Jobs</option>
<option  >Sales and Marketing Jobs</option>
<option  >Science and Technology Jobs</option>
<option  >Security Jobs</option>
<option  >Social Sciences and Community Jobs</option>
<option  >Strategic Planning Jobs</option>
<option  >Other</option>              
                    </select> 
                </div>
              </div>
              <div class="col-12 col-sm-6 col-md-3 field-grade8">
                <div class="form-group mb-2">
                 <label class="mb-1" for="CGPA"><small class="font-weight-bold">CGPA <span class="text-danger">*</span></small></label>
                  <input type="number" class="form-control form-control-sm" id="CGPA" name="CGPA">
                </div>
              </div>
              <div class="col-12 col-sm-6 col-md-3 field-grade8">
                <div class="form-group mb-2">
                 <label class="mb-1" for="meteleya_huneta"><small class="font-weight-bold">የመኖሪያ ቤት ሁኔታ <span class="text-danger">*</span></small></label>
                  <select class="form-control form-control-sm" id="meteleya_huneta" name="meteleya_huneta" required>
                    <option value="" selected disabled>ይምረጡ</option>
                    <option value="የመኖሪያ ቤት የሌላቸው">የመኖሪያ ቤት የሌላቸው</option>
                    <option value="የመኖሪያ ቤት የሌላቸው">የመኖሪያ ቤት የሌላቸው</option>
                    <option value="ከወላጅ ቤት የሚኖር">ከወላጅ ቤት የሚኖር</option>
                    <option value="ጎዳና ተዳዳሪ">ጎዳና ተዳዳሪ</option>
                  </select>
                </div>
              </div>
              <div class="col-12 col-sm-6 col-md-3 field-grade8">
                <div class="form-group mb-2">
                 <label class="mb-1" for="physical_condition"><small class="font-weight-bold">የአካል ጉዳት<span class="text-danger">*</span></small></label>
                  <select class="form-control form-control-sm" id="physical_condition" name="physical_condition" required>
                    <option value="" selected disabled>ይምረጡ</option>
                    <option value="ያለበት">ያለበት</option>
                    <option value="የሌሌበት">የሌሌበት</option>
                  </select>
                </div>
              </div>
              <div class="col-12 col-sm-6 col-md-3 field-grade8">
                <div class="form-group mb-2">
                 <label class="mb-1" for="physical_condition_desc"><small class="font-weight-bold">የአካል ጉዳቱ አይነት<span class="text-danger">*</span></small></label>
                  <select class="form-control form-control-sm" id="physical_condition_desc" name="physical_condition_desc" required>
                    <option value="" selected disabled>ይምረጡ</option>
                    <option value="የአካል ብቃት ወይም የእንቅስቃሴ ጉዳት">የአካል ብቃት ወይም የእንቅስቃሴ ጉዳት</option>
                    <option value="ማየት የተሳናቸው">ማየት የተሳናቸው</option>
                    <option value="መስማት የተሳናቸው">መስማት የተሳናቸው</option>
                    <option value="ማየት የተሳናቸው">ማየት የተሳናቸው</option>
                    <option value="የአእምሮ እድገት ወይም የመማር ችግር">የአእምሮ እድገት ወይም የመማር ችግር</option>
                    <option value="የስነ-አእምሮ ጤና እክል">የስነ-አእምሮ ጤና እክል</option>
                    <option value="ከተጠቀሱት ውጭ">ከተጠቀሱት ውጭ</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-12 col-sm-6 col-md-3">
                <div class="form-group mb-2">
                  <label class="mb-1" for="haveexp"><small class="font-weight-bold">ከዚህ ቀደም የስራ ልምድ አለዎት <span class="text-danger">*</span></small></label>
                    <select class="form-control form-control-sm" id="haveexp" name="haveexp">
                    <option value="" selected disabled>ይምረጡ</option>
                    <option value="1">አለ</option>
                    <option value="0">የለም</option>
                  </select>
                </div>
              </div>
              <div class="col-12 col-sm-6 col-md-3">
                <div class="form-group mb-2">
                  <label class="mb-1" for="noofmonth"><small class="font-weight-bold">የስራ ልምድ በወር </small></label>
                  <input type="number" class="form-control form-control-sm" id="noofmonth" name="noofmonth">
                </div>
              </div>
              <div class="col-12 col-sm-6 col-md-3">
                <div class="form-group mb-2">
                  <label class="mb-1" for="workplace"><small class="font-weight-bold">የሰሩበት ሀገር<span class="text-danger">*</span></small></label>
                  <select class="form-control form-control-sm" id="workplace" name="workplace">
                    <option value="" selected disabled>ይምረጡ</option>
                    <option value="ከሀገር ውስጥ">ከሀገር ውስጥ</option>
                    <option value="ከውጭ አገር">ከውጭ አገር</option>
                    </select>
                </div>
              </div>
              <div class="col-12 col-sm-6 col-md-3">
                <div class="form-group mb-2">
                  <label class="mb-1" for="profession"><small class="font-weight-bold">የሰሩበት የሙያ መደብ <span class="text-danger">*</span></small></label>
                  <select class="form-control form-control-sm" id="profession" name="profession" required>
  <option value="" selected disabled>ይምረጡ</option>
  <option value="የሥራ ኃላፊዎች ከፍተኛ ባለስልጣኖች፣ ሥራ አስኪያጆች">የሥራ ኃላፊዎች ከፍተኛ ባለስልጣኖች፣ ሥራ አስኪያጆች</option>
  <option value="ፕሮፌሽናሎች">ፕሮፌሽናሎች</option>
  <option value="ቴክኒሻያን ተባባሪ ፕሮፌሽናሎች">ቴክኒሻያን ተባባሪ ፕሮፌሽናሎች</option>
  <option value="ክለርክ ሰራተኞች">ክለርክ ሰራተኞች</option>
  <option value="የአገልግሎት ሰጭ ሠራተኞች፣ ሱቆች የገበያ ሽያጭ ሰራተኞች">የአገልግሎት ሰጭ ሠራተኞች፣ ሱቆች የገበያ ሽያጭ ሰራተኞች</option>
  <option value="የሰለጠነ የግብርና ዓሳ ምርት ሰራተኞች">የሰለጠነ የግብርና ዓሳ ምርት ሰራተኞች</option>
  <option value="የዕደ ጥበብ (ክራፍትስ እና የመሳሰሉት ሰራተኞች)">የዕደ ጥበብ (ክራፍትስ እና የመሳሰሉት ሰራተኞች)</option>
  <option value="የፋብሪካ ማሽን ኦፕሬተርና ገጣጣሚዎች">የፋብሪካ ማሽን ኦፕሬተርና ገጣጣሚዎች</option>
  <option value="ኢለመንታሪ (አነስተኛ የእጅ መሳሪያ) ሙያዎች">ኢለመንታሪ (አነስተኛ የእጅ መሳሪያ) ሙያዎች</option>
</select>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-12 col-sm-6 col-md-3">
                <div class="form-group mb-2">
                  <label class="mb-1" for="nameofcountry"><small class="font-weight-bold">የሀገሩ ስም<span class="text-danger">*</span></small></label>
                   <input type="text" class="form-control form-control-sm" id="nameofcountry" name="nameofcountry">
                </div>
              </div>
              <div class="col-12 col-sm-6 col-md-3">
                <div class="form-group mb-2">
                  <label class="mb-1" for="language"><small class="font-weight-bold">የሚችሉት ቋንቋ </small></label>
                  <input type="text" class="form-control form-control-sm" id="language" name="language">
                </div>
              </div>
              <div class="col-12 col-sm-6 col-md-3">
                <div class="form-group mb-2">
                  <label class="mb-1" for="wageorself"><small class="font-weight-bold">አሁን መስራት የሚፈልጉት <span class="text-danger">*</span></small></label>
                  <select class="form-control form-control-sm" id="wageorself" name="wageorself">
                    <option value="" selected disabled>ይምረጡ</option>
                    <option value="በግል">በግል</option>
                    <option value="በቅጥር">በቅጥር</option>
                  </select>
                </div>
              </div>
              <div class="col-12 col-sm-6 col-md-3">
                <div class="form-group mb-2">
                  <label class="mb-1" for="mothername"><small class="font-weight-bold">የእናት ሙሉ ስም <span class="text-danger">*</span></small></label>
                  <input type="text" class="form-control form-control-sm" id="mothername" name="mothername">
                  </select>
                </div>
              </div>
            </div>
          </div>
          

          <!-- STEP 3: Review / Submit -->
          <div class="form-step d-none" id="step-3">
              <div class="row">
  <div class="col-12 col-sm-6 col-md-3">
    <div class="form-group mb-2">
      <label class="mb-1" for="choice_sector1"><small class="font-weight-bold">የዘርፍ ምርጫ 1 <span class="text-danger">*</span></small></label>
      <select
        class="form-control form-control-sm"
        id="choice_sector1"
        name="choice_sector1"
        data-cascade-target="sub_choose1"
        required
      >
        <option value="">-- ዘርፍ ይምረጡ --</option>
        <?php foreach ($sectors as $sector): ?>
            <option value="<?= htmlspecialchars($sector['id']) ?>">
                <?= htmlspecialchars($sector['sector']) ?>
            </option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>

  <div class="col-12 col-sm-6 col-md-3">
    <div class="form-group mb-2">
      <label class="mb-1" for="sub_choose1"><small class="font-weight-bold">የሙያ ምርጫ 1 <span class="text-danger">*</span></small></label>
      <select
        class="form-control form-control-sm"
        id="sub_choose1"
        name="sub_choose1"
        required
        disabled
      >
        <option value="">-- መጀመሪያ ዘርፍ ይምረጡ --</option>
      </select>
    </div>
  </div>

  <div class="col-12 col-sm-6 col-md-3">
    <div class="form-group mb-2">
      <label class="mb-1" for="choice_sector2"><small class="font-weight-bold">የዘርፍ ምርጫ 2 <span class="text-danger">*</span></small></label>
      <select
        class="form-control form-control-sm"
        id="choice_sector2"
        name="choice_sector2"
        data-cascade-target="sub_choose2"
        required
      >
        <option value="">-- ዘርፍ ይምረጡ --</option>
        <?php foreach ($sectors as $sector): ?>
            <option value="<?= htmlspecialchars($sector['id']) ?>">
                <?= htmlspecialchars($sector['sector']) ?>
            </option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>

  <div class="col-12 col-sm-6 col-md-3">
    <div class="form-group mb-2">
      <label class="mb-1" for="sub_choose2"><small class="font-weight-bold">የሙያ ምርጫ 2 <span class="text-danger">*</span></small></label>
      <select
        class="form-control form-control-sm"
        id="sub_choose2"
        name="sub_choose2"
        required
        disabled
      >
        <option value="">-- መጀመሪያ ዘርፍ ይምረጡ --</option>
      </select>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-12 col-sm-6 col-md-3">
    <div class="form-group mb-2">
      <label class="mb-1" for="choice_sector3"><small class="font-weight-bold">የዘርፍ ምርጫ 3 <span class="text-danger">*</span></small></label>
      <select
        class="form-control form-control-sm"
        id="choice_sector3"
        name="choice_sector3"
        data-cascade-target="sub_choose3"
        required
      >
        <option value="">-- ዘርፍ ይምረጡ --</option>
        <?php foreach ($sectors as $sector): ?>
            <option value="<?= htmlspecialchars($sector['id']) ?>">
                <?= htmlspecialchars($sector['sector']) ?>
            </option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>

  <div class="col-12 col-sm-6 col-md-3">
    <div class="form-group mb-2">
      <label class="mb-1" for="sub_choose3"><small class="font-weight-bold">የሙያ ምርጫ 3 <span class="text-danger">*</span></small></label>
      <select
        class="form-control form-control-sm"
        id="sub_choose3"
        name="sub_choose3"
        required
        disabled
      >
        <option value="">-- መጀመሪያ ዘርፍ ይምረጡ --</option>
      </select>
    </div>
  </div>

              <div class="col-12 col-sm-6 col-md-3">
                <div class="form-group mb-2">
                  <label class="mb-1" for="kebele_id_photo"><small class="font-weight-bold">የቀበሌ መታወቂያ በፎቶ <span class="text-danger">*</span></small></label>
                  <input type="text" class="form-control form-control-sm" id="kebele_id_photo" name="kebele_id_photo" required>
                </div>
              </div>
              <div class="col-12 col-sm-6 col-md-3">
                <div class="form-group mb-2">
                  <label class="mb-1" for="jsphoto"><small class="font-weight-bold">የስራ ፈላጊው ፎቶ <span class="text-danger">*</span></small></label>
                  <input type="text" class="form-control form-control-sm" id="jsphoto" name="jsphoto">
                </div>        
              </div>
              <div class="col-12 col-sm-6 col-md-3">
                <div class="form-group mb-2">
                  <label class="mb-1" for="agri_business_experience"><small class="font-weight-bold">በግብርና ዘርፍ ያለው ልምድ <span class="text-danger">*</span></small></label>
                  <input type="text" class="form-control form-control-sm" id="agri_business_experience" name="agri_business_experience">
                </div>        
              </div>
                            <div class="col-12 col-sm-6 col-md-3">
                <div class="form-group mb-2">
                  <label class="mb-1" for="has_dependents"><small class="font-weight-bold">በስር የሚተዳደር ቤተሰብ <span class="text-danger">*</span></small></label>
                  <select class="form-control form-control-sm" id="has_dependents" name="has_dependents">
                    <option value="" selected disabled>ይምረጡ</option>
                    <option value="1">አለ</option>
                    <option value="0">የለም</option>
                  </select>
                </div>        
              </div>
                <div class="col-12 col-sm-6 col-md-3">
                <div class="form-group mb-2">
                  <label class="mb-1" for="number_of_dependents"><small class="font-weight-bold">የሚተዳደረው ቤተሰብ ብዛት <span class="text-danger">*</span></small></label>
                  <input type="number" class="form-control form-control-sm" id="number_of_dependents" name="number_of_dependents">
                </div>        
              </div>
                <div class="col-12 col-sm-6 col-md-3">
                <div class="form-group mb-2">
                  <label class="mb-1" for="children_under_five"><small class="font-weight-bold">ያሉት ከ5 ዓመት በታች ህፃናት ብዛት<span class="text-danger">*</span></small></label>
                  <input type="text" class="form-control form-control-sm" id="children_under_five" name="children_under_five">
                </div>        
              </div>
            </div>
            <p class="text-muted">ከመመዝገብዎ በፊት የሞሉቱን መረጃ ትክክለኛነት ያረጋግጡ።</p>
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
