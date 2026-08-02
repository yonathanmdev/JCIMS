<?php
$profile = $_SESSION['fayda_profile'] ?? [];

$nameAm = $profile['name#am'] ?? '';
$nameParts = array_values(array_filter(explode(' ', trim($nameAm))));
$faydaFirstName  = $nameParts[0] ?? '';
$faydaFatherName = $nameParts[1] ?? '';
$faydaLastName   = $nameParts[2] ?? '';

$faydaGender = $profile['gender#am'] ?? ''; // already "ወንድ" / "ሴት" — matches modal's <select> values exactly
$faydaPhone  = $profile['phone_number'] ?? '';
$faydaSub    = $profile['sub'] ?? '';
$faydaIdNumber = $_SESSION['job_seeker_id'] ?? '';

// birthdate "1993/04/20" → age
$faydaAge = '';
$faydaBirthdate = $profile['birthdate'] ?? '';
if ($faydaBirthdate !== '') {
    $bd = \DateTime::createFromFormat('Y/m/d', $faydaBirthdate);
    if ($bd) {
        $faydaAge = (string)$bd->diff(new \DateTime())->y;
    }
}

$formError = $_SESSION['form_error'] ?? null;
unset($_SESSION['form_error']);

$fiscal_year = (int)date('Y'); // adjust to your actual fiscal year source if different
$sectors = $sectors ?? []; // expected to be passed in from the controller, same as the modal
?>
<style>
.wizard-progress {
    display: flex;
    align-items: center;
    margin-bottom: 24px;
    padding: 0 4px;
}
.wizard-progress__step {
    display: flex;
    flex-direction: column;
    align-items: center;
    flex: 1;
    position: relative;
}
.wizard-progress__circle {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: #e9ecef;
    color: #6c757d;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    font-weight: 700;
    border: 2px solid #e9ecef;
    transition: all 0.2s ease;
    z-index: 2;
}
.wizard-progress__label {
    font-size: 11px;
    color: #6c757d;
    margin-top: 6px;
    text-align: center;
    max-width: 100px;
    line-height: 1.3;
}
.wizard-progress__step::after {
    content: '';
    position: absolute;
    top: 15px;
    left: 50%;
    width: 100%;
    height: 2px;
    background: #e9ecef;
    z-index: 1;
}
.wizard-progress__step:last-child::after { display: none; }
.wizard-progress__step.is-active .wizard-progress__circle {
    background: #28a745;
    border-color: #28a745;
    color: #fff;
}
.wizard-progress__step.is-active .wizard-progress__label {
    color: #28a745;
    font-weight: 700;
}
.wizard-progress__step.is-complete .wizard-progress__circle {
    background: #d4edda;
    border-color: #28a745;
    color: #28a745;
}
.wizard-progress__step.is-complete::after { background: #28a745; }
.wizard-step { display: none; }
.wizard-step.is-active { display: block; animation: wizardFadeIn 0.2s ease; }
@keyframes wizardFadeIn {
    from { opacity: 0; transform: translateY(4px); }
    to { opacity: 1; transform: translateY(0); }
}
.wizard-nav {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 18px;
    padding-top: 16px;
    border-top: 1px solid #eef0f2;
}
.wizard-nav__spacer { flex: 1; }
</style>
<section class="content">
  <div class="container-fluid">

<div class="card card-outline card-success">
    <div class="card-header">
        <h6 class="font-weight-bold"><i class="fas fa-id-card mr-1"></i> በፋይዳ የተረጋገጠ የስራ ፈላጊ ምዝገባ</h6>
    </div>
    <div class="card-body">

        <?php if ($formError): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($formError) ?></div>
        <?php endif; ?>

       <div class="bg-light border-left border-success p-2 mb-3">
    <i class="fas fa-lock text-success mr-1"></i>
    <strong>ከፋይዳ የተረጋገጠ መረጃ</strong> — ሙሉ ስም፣ ጾታ፣ ስልክ፣ የልደት ቀን ሊስተካከሉ አይችሉም።
</div>

        <div class="wizard-progress" id="wizardProgress">
            <div class="wizard-progress__step" data-step-index="1">
                <div class="wizard-progress__circle">1</div>
                <div class="wizard-progress__label">መግቢያ</div>
            </div>
            <div class="wizard-progress__step" data-step-index="2">
                <div class="wizard-progress__circle">2</div>
                <div class="wizard-progress__label">የትምህርትና ሌሎች አስፈላጊ መረጃ</div>
            </div>
            <div class="wizard-progress__step" data-step-index="3">
                <div class="wizard-progress__circle">3</div>
                <div class="wizard-progress__label">ምርጫና ሌሎች ጉዳዮች</div>
            </div>
        </div>

        <form id="faydaJobseekerForm" action="<?= rtrim($_ENV['BASE_URL'], '/') ?>/index.php?action=fayda-register" method="POST">

            <input type="hidden" name="mode" value="create">
            <input type="hidden" name="fayda_sub" value="<?= htmlspecialchars($faydaSub) ?>">
            <input type="hidden" name="fayda_id_number" value="<?= htmlspecialchars($faydaIdNumber) ?>">

            <!-- Fayda-verified identity fields: locked, submitted as hidden inputs.
                 register() re-derives these from $_SESSION server-side regardless
                 of what's posted here — this display is for the person's confirmation only. -->
            <input type="hidden" name="first_name"   value="<?= htmlspecialchars($faydaFirstName) ?>">
            <input type="hidden" name="father_name"  value="<?= htmlspecialchars($faydaFatherName) ?>">
            <input type="hidden" name="last_name"    value="<?= htmlspecialchars($faydaLastName) ?>">
            <input type="hidden" id="gender" name="gender" value="<?= htmlspecialchars($faydaGender) ?>">
            <input type="hidden" name="phone_number" value="<?= htmlspecialchars($faydaPhone) ?>">
            <input type="hidden" name="age"          value="<?= htmlspecialchars($faydaAge) ?>">

            <!-- ===================== STEP 1 : መግቢያ ===================== -->
            <div class="wizard-step is-active" data-step="1">

                <div class="row">
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="form-group mb-2">
                            <label class="mb-1"><small class="font-weight-bold">ስም</small></label>
                            <input type="text" class="form-control form-control-sm" value="<?= htmlspecialchars($faydaFirstName) ?>" readonly>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="form-group mb-2">
                            <label class="mb-1"><small class="font-weight-bold">የአባት ስም</small></label>
                            <input type="text" class="form-control form-control-sm" value="<?= htmlspecialchars($faydaFatherName) ?>" readonly>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="form-group mb-2">
                            <label class="mb-1"><small class="font-weight-bold">የአያት ስም</small></label>
                            <input type="text" class="form-control form-control-sm" value="<?= htmlspecialchars($faydaLastName) ?>" readonly>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="form-group mb-2">
                            <label class="mb-1"><small class="font-weight-bold">ጾታ</small></label>
                            <input type="text" class="form-control form-control-sm" value="<?= htmlspecialchars($faydaGender) ?>" readonly>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="form-group mb-2">
                            <label class="mb-1"><small class="font-weight-bold">እድሜ</small></label>
                            <input type="text" class="form-control form-control-sm" value="<?= htmlspecialchars($faydaAge) ?>" readonly>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="form-group mb-2">
                            <label class="mb-1"><small class="font-weight-bold">ስልክ ቁጥር</small></label>
                            <input type="text" class="form-control form-control-sm" value="<?= htmlspecialchars($faydaPhone) ?>" readonly>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="form-group mb-2">
                            <label class="mb-1"><small class="font-weight-bold">የመታወቂያ ቁጥር (ID Number)</small></label>
                            <input type="text" class="form-control form-control-sm" value="<?= htmlspecialchars($faydaIdNumber) ?>" readonly>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="form-group mb-2">
                            <label class="mb-1" for="srafelagi_huneta"><small class="font-weight-bold">የስራ ፈላጊ ሁኔታ <span class="text-danger">*</span></small></label>
                            <select class="form-control form-control-sm" id="srafelagi_huneta" name="srafelagi_huneta" data-validate="text-with-spaces" required>
                                <option value="" selected disabled>ይምረጡ</option>
                                <option value="ስራ ፈላጊ">ስራ ፈላጊ</option>
                                <option value="ተፈናቃይ">ተፈናቃይ</option>
                                <option value="ከስደት ተመላሽ">ከስደት ተመላሽ</option>
                                <option value="ዓለም አቀፍ ፍልሰተኛ">ዓለም አቀፍ ፍልሰተኛ</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="form-group mb-2">
                            <label class="mb-1" for="Labor_ID"><small class="font-weight-bold">Labor_ID</small></label>
                            <input type="text" class="form-control form-control-sm" id="Labor_ID" name="Labor_ID" data-validate="general-safe" data-length="10">
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="form-group mb-2">
                            <label class="mb-1" for="maritalstatus"><small class="font-weight-bold">የጋብቻ ሁኔታ <span class="text-danger">*</span></small></label>
                            <select class="form-control form-control-sm" id="maritalstatus" name="maritalstatus" data-validate="general-safe" required>
                                <option value="" selected disabled>ይምረጡ</option>
                                <option value="ያላገባ/ች">ያላገባ/ች</option>
                                <option value="ያገባ/ች">ያገባ/ች</option>
                                <option value="የፈታ/ች">የፈታ/ች</option>
                                <option value="የሞተበት/ባት">የሞተበት/ባት</option>
                            </select>
                        </div>
                    </div>
                    <!-- FAN removed -->
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="form-group mb-2">
                            <label class="mb-1" for="kebele"><small class="font-weight-bold">ቀበሌ <span class="text-danger">*</span></small></label>
                            <input type="text" class="form-control form-control-sm" id="kebele" name="kebele" data-validate="general-safe" required>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3 field-housewife d-none">
                        <div class="form-group mb-2">
                            <label class="mb-1" for="housewife"><small class="font-weight-bold">የቤት እመቤት <span class="text-danger">*</span></small></label>
                            <select class="form-control form-control-sm" id="housewife" name="housewife" data-validate="numeric-only" data-length="1">
                                <option value="" selected disabled>ይምረጡ</option>
                                <option value="1">ነች</option>
                                <option value="0">አይደለችም</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="form-group mb-2">
                            <label class="mb-1" for="mender"><small class="font-weight-bold">መንደር</small></label>
                            <input type="text" class="form-control form-control-sm" id="mender" name="mender">
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="form-group mb-2">
                            <label class="mb-1" for="kebele_id_no"><small class="font-weight-bold">የቀበሌ መታወቂያ ቁጥር <span class="text-danger">*</span></small></label>
                            <input type="text" class="form-control form-control-sm" id="kebele_id_no" name="kebele_id_no" data-validate="general-safe" required>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="form-group mb-2">
                            <label class="mb-1" for="residence_status"><small class="font-weight-bold">የሚኖርበት አካባቢ <span class="text-danger">*</span></small></label>
                            <select class="form-control form-control-sm" id="residence_status" name="residence_status" data-validate="name-only" required>
                                <option value="" selected disabled>ይምረጡ</option>
                                <option value="ከተማ">ከተማ</option>
                                <option value="ገጠር">ገጠር</option>
                            </select>
                        </div>
                    </div>
                </div>

            </div>
            <!-- ===================== END STEP 1 ===================== -->

            <!-- ===================== STEP 2 : የትምህርትና ሌሎች አስፈላጊ መረጃ ===================== -->
            <div class="wizard-step" data-step="2">

                <div class="row">
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="form-group mb-2">
                            <label class="mb-1" for="educational_level"><small class="font-weight-bold">የትምህርት ደረጃ <span class="text-danger">*</span></small></label>
                            <select class="form-control form-control-sm" id="educational_level" name="educational_level" required>
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
                    <div class="col-12 col-sm-6 col-md-3 field-schooltype">
                        <div class="form-group mb-2">
                            <label class="mb-1" for="school_type"><small class="font-weight-bold">የት/ቤቱ/የኮሌጁ/የዩንቨርሲቲው ዓይነት <span class="text-danger">*</span></small></label>
                            <select class="form-control form-control-sm" id="school_type" name="school_type">
                                <option value="" selected disabled>ይምረጡ</option>
                                <option value="የመንግስት">የመንግስት</option>
                                <option value="የግል">የግል</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3 field-dept">
                        <div class="form-group mb-2">
                            <label class="mb-1" for="educated_dpt"><small class="font-weight-bold">የተመረቀበት ዲፓርትመንት <span class="text-danger">*</span></small></label>
                            <input type="text" name="educated_dpt" id="educated_dpt" class="form-control form-control-sm" data-validate="text-with-spaces">
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3 field-year">
                        <div class="form-group mb-2">
                            <label class="mb-1" for="education_trmnet_finsh_year"><small class="font-weight-bold">ትምህርት ያጠናቀቀበት (ያቋረጠበት) ዓመት <span class="text-danger">*</span></small></label>
                            <select class="form-control form-control-sm" id="education_trmnet_finsh_year" name="education_trmnet_finsh_year">
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
                            <label class="mb-1" for="g8id"><small class="font-weight-bold">የ8ኛ ክፍል መለያ ቁጥር <span class="text-danger">*</span></small></label>
                            <input type="text" name="g8id" id="g8id" class="form-control form-control-sm" data-validate="numeric-only">
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3 field-dept">
                        <div class="form-group mb-2">
                            <label class="mb-1" for="graguation_catagory"><small class="font-weight-bold">ያጠናቀቁበት ሙያ የስራ ምድብ <span class="text-danger">*</span></small></label>
                            <select class="form-control form-control-sm" name="graguation_catagory" id="graguation_catagory">
                                <option value="" selected="selected">&larr; ይምረጡ &rarr;</option>
                                <option>Accounting and Finance Jobs</option>
                                <option>Admin, Secretarial and Clerical Jobs</option>
                                <option>Advertising and Media Jobs</option>
                                <option>Agriculture Jobs</option>
                                <option>Architecture and Construction Jobs</option>
                                <option>Automotive Jobs</option>
                                <option>Banking and Insurance Jobs</option>
                                <option>Business Development Jobs</option>
                                <option>Business and Administration Jobs</option>
                                <option>Communications, PR and Journalism Jobs</option>
                                <option>Community Service Jobs</option>
                                <option>Consultancy and Training Jobs</option>
                                <option>Creative Arts Jobs</option>
                                <option>Customer Service Jobs</option>
                                <option>Development and Project Management Jobs</option>
                                <option>Economics Jobs</option>
                                <option>Education Jobs</option>
                                <option>Engineering Jobs</option>
                                <option>Environment and Natural Resource Jobs</option>
                                <option>Event Management Jobs</option>
                                <option>Health Care Jobs</option>
                                <option>Hotel and Hospitality Jobs</option>
                                <option>Human Resource and Recruitment Jobs</option>
                                <option>Information Technology Jobs</option>
                                <option>Languages Jobs</option>
                                <option>Legal Jobs</option>
                                <option>Logistics, Transport and Supply Chain Jobs</option>
                                <option>Maintenance Jobs</option>
                                <option>Management Jobs</option>
                                <option>Manufacturing Jobs</option>
                                <option>Media and Journalism Jobs</option>
                                <option>Natural Sciences Jobs</option>
                                <option>Pharmaceutical Jobs</option>
                                <option>Purchasing and Procurement Jobs</option>
                                <option>Quality Assurance Jobs</option>
                                <option>Research and Development Jobs</option>
                                <option>Retail, Wholesale and Distribution Jobs</option>
                                <option>Sales and Marketing Jobs</option>
                                <option>Science and Technology Jobs</option>
                                <option>Security Jobs</option>
                                <option>Social Sciences and Community Jobs</option>
                                <option>Strategic Planning Jobs</option>
                                <option>Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3 field-cgpa">
                        <div class="form-group mb-2">
                            <label class="mb-1" for="CGPA"><small class="font-weight-bold">CGPA <span class="text-danger">*</span></small></label>
                            <input type="text" class="form-control form-control-sm" id="CGPA" name="CGPA" data-validate="decimal" data-length="4">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="form-group mb-2">
                            <label class="mb-1" for="meteleya_huneta"><small class="font-weight-bold">የመኖሪያ ቤት ሁኔታ <span class="text-danger">*</span></small></label>
                            <select class="form-control form-control-sm" id="meteleya_huneta" name="meteleya_huneta" required>
                                <option value="" selected disabled>ይምረጡ</option>
                                <option value="የመኖሪያ ቤት የሌላቸው">የመኖሪያ ቤት የሌላቸው</option>
                                <option value="ከወላጅ ቤት የሚኖር">ከወላጅ ቤት የሚኖር</option>
                                <option value="ጎዳና ተዳዳሪ">ጎዳና ተዳዳሪ</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="form-group mb-2">
                            <label class="mb-1" for="physical_condition"><small class="font-weight-bold">የአካል ጉዳት <span class="text-danger">*</span></small></label>
                            <select class="form-control form-control-sm" id="physical_condition" name="physical_condition" required>
                                <option value="" selected disabled>ይምረጡ</option>
                                <option value="0">የሌለበት</option>
                                <option value="1">ያለበት</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3 field-physical-desc">
                        <div class="form-group mb-2">
                            <label class="mb-1" for="physical_condition_desc"><small class="font-weight-bold">የአካል ጉዳቱ አይነት <span class="text-danger">*</span></small></label>
                            <select class="form-control form-control-sm" id="physical_condition_desc" name="physical_condition_desc">
                                <option value="" selected disabled>ይምረጡ</option>
                                <option value="የአካል ብቃት ወይም የእንቅስቃሴ ጉዳት">የአካል ብቃት ወይም የእንቅስቃሴ ጉዳት</option>
                                <option value="ማየት የተሳናቸው">ማየት የተሳናቸው</option>
                                <option value="መስማት የተሳናቸው">መስማት የተሳናቸው</option>
                                <option value="የአእምሮ እድገት ወይም የመማር ችግር">የአእምሮ እድገት ወይም የመማር ችግር</option>
                                <option value="የስነ-አእምሮ ጤና እክል">የስነ-አእምሮ ጤና እክል</option>
                                <option value="ከተጠቀሱት ውጭ">ከተጠቀሱት ውጭ</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="form-group mb-2">
                            <label class="mb-1" for="haveexp"><small class="font-weight-bold">ከዚህ ቀደም የስራ ልምድ አለዎት <span class="text-danger">*</span></small></label>
                            <select class="form-control form-control-sm" id="haveexp" name="haveexp" data-validate="numeric-only" data-length="1" required>
                                <option value="" selected disabled>ይምረጡ</option>
                                <option value="1">አለ</option>
                                <option value="0">የለም</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 col-sm-6 col-md-3 field-experience">
                        <div class="form-group mb-2">
                            <label class="mb-1" for="experience"><small class="font-weight-bold">የስራ ልምድ በወር</small></label>
                            <input type="number" step="any" class="form-control form-control-sm" id="experience" name="experience" data-validate="decimal">
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3 field-workplace">
                        <div class="form-group mb-2">
                            <label class="mb-1" for="workplace"><small class="font-weight-bold">የሰሩበት ሀገር <span class="text-danger">*</span></small></label>
                            <select class="form-control form-control-sm" id="workplace" name="workplace" data-validate="text-with-spaces">
                                <option value="" selected disabled>ይምረጡ</option>
                                <option value="ከሀገር ውስጥ">ከሀገር ውስጥ</option>
                                <option value="ከውጭ አገር">ከውጭ አገር</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3 field-profession">
                        <div class="form-group mb-2">
                            <label class="mb-1" for="profession"><small class="font-weight-bold">የሰሩበት የሙያ መደብ <span class="text-danger">*</span></small></label>
                            <select class="form-control form-control-sm" id="profession" name="profession" data-validate="general-safe">
                                <option value="" selected disabled>ይምረጡ</option>
                                <option value="የሥራ ኃላፊዎች ከፍተኛ ባለስልጣኖች፣ ሥራ አስኪያጆች">የሥራ ኃላፊዎች ከፍተኛ ባለስልጣኖች፣ ሥራ አስኪያጆች</option>
                                <option value="ፕሮፌሽናሎች">ፕሮፌሽናሎች</option>
                                <option value="ቴክኒሻያን ተባባሪ ፕሮፌሽናሎች">ቴክኒሻያን ተባባሪ ፕሮፌሽናሎች</option>
                                <option value="ክለርክ ሰራተኞች">ክለርክ ሰራተኞች</option>
                                <option value="የአገልግሎት ሰጭ ሠራተኞች፣ ሱቆች የገበያ ሽያጭ ሰራተኞች">የአገልግሎት ሰጭ ሠራተኞች፣ ሱቆች የገበያ ሽያጭ ሰራተኞች</option>
                                <option value="የሰለጠነ የግብርና ዓሳ ምርት ሰራተኞች">የሰለጠነ የግብርና ዓሳ ምርት ሰራተኞች</option>
                                <option value="የዕደ ጥበብ /ክራፍትስ እና የመሳሰሉት ሰራተኞች/">የዕደ ጥበብ (ክራፍትስ እና የመሳሰሉት ሰራተኞች)</option>
                                <option value="የፋብሪካ ማሽን ኦፕሬተርና ገጣጣሚዎች">የፋብሪካ ማሽን ኦፕሬተርና ገጣጣሚዎች</option>
                                <option value="ኢለመንታሪ /አነስተኛ የእጅ መሳሪያ/ ሙያዎች">ኢለመንታሪ (አነስተኛ የእጅ መሳሪያ) ሙያዎች</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3 field-country">
                        <div class="form-group mb-2">
                            <label class="mb-1" for="nameofcountry"><small class="font-weight-bold">የሀገሩ ስም <span class="text-danger">*</span></small></label>
                            <input type="text" class="form-control form-control-sm" id="nameofcountry" name="nameofcountry" data-validate="text-with-spaces">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 col-sm-6 col-md-3 field-language">
                        <div class="form-group mb-2">
                            <label class="mb-1" for="language"><small class="font-weight-bold">የሚችሉት ቋንቋ</small></label>
                            <input type="text" class="form-control form-control-sm" id="language" name="language" data-validate="text-with-spaces">
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="form-group mb-2">
                            <label class="mb-1" for="wageorself"><small class="font-weight-bold">አሁን መስራት የሚፈልጉት <span class="text-danger">*</span></small></label>
                            <select class="form-control form-control-sm" id="wageorself" name="wageorself" data-validate="name-only" required>
                                <option value="" selected disabled>ይምረጡ</option>
                                <option value="በግል">በግል</option>
                                <option value="በቅጥር">በቅጥር</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="form-group mb-2">
                            <label class="mb-1" for="mothername"><small class="font-weight-bold">የእናት ሙሉ ስም <span class="text-danger">*</span></small></label>
                            <input type="text" class="form-control form-control-sm" id="mothername" name="mothername" data-validate="text-with-spaces" required>
                        </div>
                    </div>
                </div>

            </div>
            <!-- ===================== END STEP 2 ===================== -->

            <!-- ===================== STEP 3 : ምርጫና ሌሎች ጉዳዮች ===================== -->
            <div class="wizard-step" data-step="3">

                <div class="row">
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="form-group mb-2">
                            <label class="mb-1" for="choice_sector1"><small class="font-weight-bold">የዘርፍ ምርጫ 1 <span class="text-danger">*</span></small></label>
                            <select class="form-control form-control-sm" id="choice_sector1" name="choice_sector1" data-cascade-target="sub_choose1" required>
                                <option value="" selected disabled>-- ዘርፍ ይምረጡ --</option>
                                <?php foreach ($sectors as $sector): ?>
                                    <option value="<?= htmlspecialchars($sector['id']) ?>"><?= htmlspecialchars($sector['sector']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="form-group mb-2">
                            <label class="mb-1" for="sub_choose1"><small class="font-weight-bold">የሙያ ምርጫ 1 <span class="text-danger">*</span></small></label>
                            <select class="form-control form-control-sm" id="sub_choose1" name="sub_choose1" required disabled>
                                <option value="" selected disabled>-- መጀመሪያ ዘርፍ ይምረጡ --</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="form-group mb-2">
                            <label class="mb-1" for="choice_sector2"><small class="font-weight-bold">የዘርፍ ምርጫ 2 <span class="text-danger">*</span></small></label>
                            <select class="form-control form-control-sm" id="choice_sector2" name="choice_sector2" data-cascade-target="sub_choose2" required>
                                <option value="" selected disabled>-- ዘርፍ ይምረጡ --</option>
                                <?php foreach ($sectors as $sector): ?>
                                    <option value="<?= htmlspecialchars($sector['id']) ?>"><?= htmlspecialchars($sector['sector']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="form-group mb-2">
                            <label class="mb-1" for="sub_choose2"><small class="font-weight-bold">የሙያ ምርጫ 2 <span class="text-danger">*</span></small></label>
                            <select class="form-control form-control-sm" id="sub_choose2" name="sub_choose2" required disabled>
                                <option value="" selected disabled>-- መጀመሪያ ዘርፍ ይምረጡ --</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="form-group mb-2">
                            <label class="mb-1" for="choice_sector3"><small class="font-weight-bold">የዘርፍ ምርጫ 3 <span class="text-danger">*</span></small></label>
                            <select class="form-control form-control-sm" id="choice_sector3" name="choice_sector3" data-cascade-target="sub_choose3" required>
                                <option value="" selected disabled>-- ዘርፍ ይምረጡ --</option>
                                <?php foreach ($sectors as $sector): ?>
                                    <option value="<?= htmlspecialchars($sector['id']) ?>"><?= htmlspecialchars($sector['sector']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="form-group mb-2">
                            <label class="mb-1" for="sub_choose3"><small class="font-weight-bold">የሙያ ምርጫ 3 <span class="text-danger">*</span></small></label>
                            <select class="form-control form-control-sm" id="sub_choose3" name="sub_choose3" required disabled>
                                <option value="" selected disabled>-- መጀመሪያ ዘርፍ ይምረጡ --</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3 field-agri-status">
                        <div class="form-group mb-2">
                            <label class="mb-1" for="agri_business_experience_status"><small class="font-weight-bold">በግብርና ዘርፍ ልምድ <span class="text-danger">*</span></small></label>
                            <select class="form-control form-control-sm" id="agri_business_experience_status" name="agri_business_experience_status" data-validate="numeric-only" data-length="1">
                                <option value="" selected disabled>ይምረጡ</option>
                                <option value="1">አለ</option>
                                <option value="0">የለም</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3 field-agri-experience">
                        <div class="form-group mb-2">
                            <label class="mb-1" for="agri_business_experience"><small class="font-weight-bold">በግብርና ዘርፍ ያለው ልምድ <span class="text-danger">*</span></small></label>
                            <input type="number" step="any" class="form-control form-control-sm" id="agri_business_experience" name="agri_business_experience" data-validate="decimal">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 col-sm-6 col-md-3 field-has-dependents d-none">
                        <div class="form-group mb-2">
                            <label class="mb-1" for="has_dependents"><small class="font-weight-bold">በስር የሚተዳደር ቤተሰብ <span class="text-danger">*</span></small></label>
                            <select class="form-control form-control-sm" id="has_dependents" name="has_dependents" data-validate="numeric-only" data-length="1">
                                <option value="" selected disabled>ይምረጡ</option>
                                <option value="1">አለ</option>
                                <option value="0">የለም</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3 field-number-of-dependents">
                        <div class="form-group mb-2">
                            <label class="mb-1" for="number_of_dependents"><small class="font-weight-bold">የሚተዳደረው ቤተሰብ ብዛት <span class="text-danger">*</span></small></label>
                            <input type="number" class="form-control form-control-sm" id="number_of_dependents" name="number_of_dependents" data-validate="numeric-only">
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3 field-children-under-five">
                        <div class="form-group mb-2">
                            <label class="mb-1" for="children_under_five"><small class="font-weight-bold">ያሉት ከ5 ዓመት በታች ህፃናት ብዛት <span class="text-danger">*</span></small></label>
                            <input type="number" class="form-control form-control-sm" id="children_under_five" name="children_under_five" data-validate="numeric-only">
                        </div>
                    </div>
                </div>

                <p class="text-muted">ከመመዝገብዎ በፊት የሞሉቱን መረጃ ትክክለኛነት ያረጋግጡ።</p>

            </div>
            <!-- ===================== END STEP 3 ===================== -->

            <div class="wizard-nav">
                <button type="button" class="btn btn-outline-secondary" id="wizardBackBtn" style="display:none;">
                    <i class="fas fa-arrow-left mr-1"></i> ተመለስ
                </button>
                <div class="wizard-nav__spacer"></div>
                <button type="button" class="btn btn-primary" id="wizardNextBtn">
                    ቀጣይ <i class="fas fa-arrow-right ml-1"></i>
                </button>
                <button type="submit" class="btn btn-success" id="wizardSubmitBtn" style="display:none;">
                    መዝግብ
                </button>
            </div>

        </form>
    </div>
      <div class="card-footer bg-light">
        <small class="text-muted">በፋይዳ የተረጋገጠ የስራ ፈላጊ ምዝገባ ስርዓት</small>
    </div>  <!-- ← new footer goes here, as a sibling -->
</div>
 </div>
</section>
<script nonce="<?= $GLOBALS['nonce'] ?? '' ?>">
(function () {
    let wizardCurrentStep = 1;
    const wizardTotalSteps = 3;

    const stepEls = document.querySelectorAll('.wizard-step');
    const progressEls = document.querySelectorAll('.wizard-progress__step');
    const backBtn = document.getElementById('wizardBackBtn');
    const nextBtn = document.getElementById('wizardNextBtn');
    const submitBtn = document.getElementById('wizardSubmitBtn');
    const form = document.getElementById('faydaJobseekerForm');

    const AGRI_LABEL = 'ግብርና';
    const SECTOR_SELECT_IDS = ['choice_sector1', 'choice_sector2', 'choice_sector3'];

    // ── Validators (verbatim from the modal's shared validation script) ──
    const NAME_ONLY_PATTERN = /^\p{L}*$/u;
    const TEXT_WITH_SPACES_PATTERN = /^[\p{L}]+(\s[\p{L}]+)*$/u;
    const NUMERIC_PATTERN = /^\d*$/;
    const GENERAL_SAFE_PATTERN = /^[\p{L}\d\-\/\s፣]*$/u;
    const DECIMAL_PATTERN = /^\d+(\.\d+)?$/;

    const validators = {
        'name-only': { pattern: NAME_ONLY_PATTERN, message: 'ቁጥር፣ ልዩ ምልክት ወይም ክፍተት መጠቀም አይቻልም።' },
        'text-with-spaces': { pattern: TEXT_WITH_SPACES_PATTERN, message: 'ቁጥር ወይም ልዩ ምልክት መጠቀም አይቻልም።' },
        'numeric-only': { pattern: NUMERIC_PATTERN, message: 'ቁጥር ብቻ መጠቀም ይቻላል።' },
        'general-safe': { pattern: GENERAL_SAFE_PATTERN, message: 'ልዩ ምልክት (እንደ <, >, %, ;) መጠቀም አይቻልም።' },
        'decimal': { pattern: DECIMAL_PATTERN, message: 'ቁጥር ብቻ መጠቀም ይቻላል።' }
    };

    function getSelectedText(selectEl) {
        if (!selectEl || selectEl.selectedIndex < 0) return '';
        return selectEl.options[selectEl.selectedIndex].text.trim();
    }

    function getFeedbackEl(input) {
        let feedback = input.parentElement.querySelector('.invalid-feedback[data-validator-feedback]');
        if (!feedback) {
            feedback = document.createElement('div');
            feedback.className = 'invalid-feedback';
            feedback.setAttribute('data-validator-feedback', '');
            input.parentElement.appendChild(feedback);
        }
        return feedback;
    }

    function getExactLengthMessage(length, type) {
        if (type === 'numeric-only' || type === 'decimal') {
            return `ይህ Field በትክክል ${length} digits መሆን አለበት።`;
        }
        return `ይህ Field በትክክል ${length} ፊደል/ቁጥር መሆን አለበት።`;
    }

    function validateField(input) {
        const type = input.getAttribute('data-validate');
        const validator = validators[type];
        if (!validator) return true;

        const value = input.value;
        const feedback = getFeedbackEl(input);

        if (value === '') {
            input.classList.remove('is-invalid', 'is-valid');
            input.setCustomValidity('');
            return true;
        }

        if (!validator.pattern.test(value)) {
            input.classList.add('is-invalid');
            input.classList.remove('is-valid');
            feedback.textContent = validator.message;
            input.setCustomValidity(validator.message);
            return false;
        }

        const requiredLength = input.getAttribute('data-length');
        if (requiredLength && value.length !== parseInt(requiredLength, 10)) {
            const msg = getExactLengthMessage(requiredLength, type);
            input.classList.add('is-invalid');
            input.classList.remove('is-valid');
            feedback.textContent = msg;
            input.setCustomValidity(msg);
            return false;
        }

        input.classList.remove('is-invalid');
        input.classList.add('is-valid');
        input.setCustomValidity('');
        return true;
    }

    function sanitizeOnInput(input) {
        const type = input.getAttribute('data-validate');

        if (type === 'name-only') {
            const cleaned = input.value.replace(/[^\p{L}]/gu, '');
            if (cleaned !== input.value) input.value = cleaned;

        } else if (type === 'text-with-spaces') {
            let cleaned = input.value
                .replace(/[^\p{L}፣\s]/gu, '')
                .replace(/\s{2,}/g, ' ')
                .replace(/^\s+/, '');
            if (cleaned !== input.value) input.value = cleaned;

        } else if (type === 'numeric-only') {
            let cleaned = input.value.replace(/\D/g, '');
            const maxLength = input.getAttribute('data-length');
            if (maxLength) cleaned = cleaned.slice(0, parseInt(maxLength, 10));
            if (cleaned !== input.value) input.value = cleaned;

        } else if (type === 'decimal') {
            let cleaned = input.value.replace(/[^\d.]/g, '').replace(/(\..*)\./g, '$1');
            if (cleaned !== input.value) input.value = cleaned;

        } else if (type === 'general-safe') {
            let cleaned = input.value
                .replace(/[^\p{L}\d\-\/\s፣]/gu, '')
                .replace(/\s{2,}/g, ' ')
                .replace(/^\s+/, '');
            if (cleaned !== input.value) input.value = cleaned;
        }
    }

    function validateChildrenUnderFive() {
        const numberOfDependentsInput = document.getElementById('number_of_dependents');
        const childrenUnderFiveInput = document.getElementById('children_under_five');
        if (!numberOfDependentsInput || !childrenUnderFiveInput) return true;

        const maxDependents = parseInt(numberOfDependentsInput.value, 10) || 0;
        const childrenValue = parseInt(childrenUnderFiveInput.value, 10) || 0;
        const feedback = getFeedbackEl(childrenUnderFiveInput);

        if (childrenUnderFiveInput.value !== '' && childrenValue > maxDependents) {
            const msg = `ከ5 ዓመት በታች ያሉ ልጆች ቁጥር ከጠቅላላ ቤተሰብ ብዛት (${maxDependents}) መብለጥ አይችልም።`;
            childrenUnderFiveInput.classList.add('is-invalid');
            childrenUnderFiveInput.classList.remove('is-valid');
            feedback.textContent = msg;
            childrenUnderFiveInput.setCustomValidity(msg);
            return false;
        }

        childrenUnderFiveInput.classList.remove('is-invalid');
        childrenUnderFiveInput.classList.add('is-valid');
        feedback.textContent = '';
        childrenUnderFiveInput.setCustomValidity('');
        return true;
    }

    function validateAllFields(container) {
        const scope = container || document;
        let allValid = true;

        scope.querySelectorAll('[data-validate]').forEach(function (input) {
            if (!validateField(input)) allValid = false;
        });

        if (scope.querySelector('#number_of_dependents') && scope.querySelector('#children_under_five')) {
            if (!validateChildrenUnderFive()) allValid = false;
        }

        return allValid;
    }

    // ── Conditional-logic functions (verbatim rules from the modal script) ──
    function applyEducationLevelLogic() {
        const eduLevel = document.getElementById('educational_level').value;
        const hideFor = ['ማንበብና መፃፍ የማይችሉ', 'መሰረተ ትምህርት'];
        const educated_dptLevels = ['ደረጃ 2', 'ደረጃ 3', 'ደረጃ 4', 'ደረጃ 5', 'የመጀመሪያ ዲግሪ', 'ሁለተኛ ዲግሪ'];
        const g8idLevels = ['8ኛ ያጠናቀቁ', 'ከ9-10ኛ', 'ከ11-12ኛ', 'ደረጃ 2', 'ደረጃ 3', 'ደረጃ 4', 'ደረጃ 5', 'የመጀመሪያ ዲግሪ', 'ሁለተኛ ዲግሪ'];

        const generalFields = document.querySelectorAll('.field-year');
        if (hideFor.includes(eduLevel)) {
            generalFields.forEach(el => {
                el.classList.add('d-none');
                const input = el.querySelector('input, select');
                if (input) { input.removeAttribute('required'); input.value = ''; }
            });
        } else {
            generalFields.forEach(el => el.classList.remove('d-none'));
            document.getElementById('education_trmnet_finsh_year')?.setAttribute('required', 'required');
        }

        const educated_dptFields = document.querySelectorAll('.field-dept, .field-cgpa, .field-schooltype');
        if (educated_dptLevels.includes(eduLevel)) {
            educated_dptFields.forEach(el => {
                el.classList.remove('d-none');
                const input = el.querySelector('input, select');
                if (input) input.setAttribute('required', 'required');
            });
        } else {
            educated_dptFields.forEach(el => {
                el.classList.add('d-none');
                const input = el.querySelector('input, select');
                if (input) { input.removeAttribute('required'); input.value = ''; }
            });
        }

        const g8idField = document.querySelector('.field-grade8');
        const g8idInput = document.getElementById('g8id');
        if (g8idLevels.includes(eduLevel)) {
            g8idField?.classList.remove('d-none');
            g8idInput?.setAttribute('required', 'required');
        } else {
            g8idField?.classList.add('d-none');
            if (g8idInput) { g8idInput.removeAttribute('required'); g8idInput.value = ''; }
        }
    }

    function applyExperienceLogic() {
        const haveExp = document.getElementById('haveexp')?.value;
        const expFields = document.querySelectorAll('.field-experience, .field-workplace, .field-profession');

        if (haveExp === '1') {
            expFields.forEach(el => {
                el.classList.remove('d-none');
                const input = el.querySelector('input, select');
                if (input) input.setAttribute('required', 'required');
            });
        } else {
            expFields.forEach(el => {
                el.classList.add('d-none');
                const input = el.querySelector('input, select');
                if (input) { input.removeAttribute('required'); input.value = ''; }
            });
        }
        applyLanguageLogic();
    }

    function applyLanguageLogic() {
        const haveExp = document.getElementById('haveexp')?.value;
        const workplaceSelect = document.querySelector('.field-workplace select, .field-workplace input');
        const workplaceValue = workplaceSelect?.value;

        const languageField = document.querySelector('.field-language');
        const countryField = document.querySelector('.field-country');
        if (!languageField) return;

        const input = languageField.querySelector('input, select');
        const countryInput = countryField?.querySelector('input, select');
        const shouldShow = haveExp === '1' && workplaceValue === 'ከውጭ አገር';

        if (shouldShow) {
            languageField.classList.remove('d-none');
            countryField.classList.remove('d-none');
            countryInput?.setAttribute('required', 'required');
        } else {
            countryField?.classList.add('d-none');
            languageField.classList.add('d-none');
            if (countryInput) { countryInput.removeAttribute('required'); countryInput.value = ''; }
            if (input) { input.removeAttribute('required'); input.value = ''; }
        }
    }

    function applyPhysicalConditionLogic() {
        const physicalConditionValue = document.getElementById('physical_condition').value;
        const physicalDescField = document.querySelector('.field-physical-desc');
        const physicalDescInput = document.getElementById('physical_condition_desc');

        if (physicalConditionValue === '1') {
            physicalDescField?.classList.remove('d-none');
            physicalDescInput?.setAttribute('required', 'required');
        } else {
            physicalDescField?.classList.add('d-none');
            if (physicalDescInput) { physicalDescInput.removeAttribute('required'); physicalDescInput.value = ''; }
        }
    }

    SECTOR_SELECT_IDS.forEach(id => {
        document.getElementById(id)?.addEventListener('change', function () {
            applyAgriStatusVisibility();
        });
    });

    function applyAgriStatusVisibility() {
        const isAgriSelected = SECTOR_SELECT_IDS.some(id => {
            const el = document.getElementById(id);
            return el && getSelectedText(el) === AGRI_LABEL;
        });

        const agriStatusField = document.querySelector('.field-agri-status');
        const agriStatusSelect = document.getElementById('agri_business_experience_status');

        if (isAgriSelected) {
            agriStatusField?.classList.remove('d-none');
            agriStatusSelect?.setAttribute('required', 'required');
        } else {
            agriStatusField?.classList.add('d-none');
            if (agriStatusSelect) { agriStatusSelect.removeAttribute('required'); agriStatusSelect.value = ''; }
        }
        applyAgriExperienceLogic();
    }

    function applyAgriExperienceLogic() {
        const agriStatus = document.getElementById('agri_business_experience_status')?.value;
        const agriExpField = document.querySelector('.field-agri-experience');
        const agriExpInput = document.getElementById('agri_business_experience');

        if (agriStatus === '1') {
            agriExpField?.classList.remove('d-none');
            agriExpInput?.setAttribute('required', 'required');
        } else {
            agriExpField?.classList.add('d-none');
            if (agriExpInput) { agriExpInput.removeAttribute('required'); agriExpInput.value = ''; }
        }
    }

    // Fayda's gender is locked/fixed — this runs once on load, no change listener needed.
    function applyGenderDependentsVisibility() {
        const genderValue = document.getElementById('gender')?.value;

        const dependentsField = document.querySelector('.field-has-dependents');
        const dependentsSelect = document.getElementById('has_dependents');
        const housewifeField = document.querySelector('.field-housewife');
        const housewifeSelect = document.getElementById('housewife');

        const shouldShow = genderValue === 'ሴት';

        if (shouldShow) {
            dependentsField?.classList.remove('d-none');
            dependentsSelect?.setAttribute('required', 'required');
            housewifeField?.classList.remove('d-none');
            housewifeSelect?.setAttribute('required', 'required');
        } else {
            dependentsField?.classList.add('d-none');
            if (dependentsSelect) { dependentsSelect.removeAttribute('required'); dependentsSelect.value = ''; }
            housewifeField?.classList.add('d-none');
            if (housewifeSelect) { housewifeSelect.removeAttribute('required'); housewifeSelect.value = ''; }
        }
        applyDependentsLogic();
    }

    function applyDependentsLogic() {
        const hasDependentsStatus = document.getElementById('has_dependents').value;
        const dependentFields = document.querySelectorAll('.field-number-of-dependents, .field-children-under-five');
        const numberofDependentsInput = document.getElementById('number_of_dependents');
        const childrenUnderFiveInput = document.getElementById('children_under_five');

        if (hasDependentsStatus === '1') {
            dependentFields.forEach(field => field.classList.remove('d-none'));
            numberofDependentsInput?.setAttribute('required', 'required');
        } else {
            dependentFields.forEach(field => field.classList.add('d-none'));
            if (numberofDependentsInput) { numberofDependentsInput.removeAttribute('required'); numberofDependentsInput.value = ''; }
            if (childrenUnderFiveInput) { childrenUnderFiveInput.removeAttribute('required'); childrenUnderFiveInput.value = ''; }
        }
    }

    function applyAllConditionalLogic() {
        applyEducationLevelLogic();
        applyExperienceLogic();
        applyPhysicalConditionLogic();
        applyGenderDependentsVisibility();
        applyAgriStatusVisibility();
    }

    // ── Wizard step navigation ──
    function currentStepEl() {
        return document.querySelector(`.wizard-step[data-step="${wizardCurrentStep}"]`);
    }

    function validateCurrentStep() {
        const stepEl = currentStepEl();
        const requiredFields = stepEl.querySelectorAll('[required]');
        for (const input of requiredFields) {
            if (input.closest('.d-none')) continue;
            if (!input.checkValidity()) {
                input.reportValidity();
                return false;
            }
        }
        return validateAllFields(stepEl);
    }

    function renderStep() {
        stepEls.forEach(el => {
            el.classList.toggle('is-active', parseInt(el.getAttribute('data-step'), 10) === wizardCurrentStep);
        });

        progressEls.forEach(el => {
            const idx = parseInt(el.getAttribute('data-step-index'), 10);
            el.classList.remove('is-active', 'is-complete');
            if (idx === wizardCurrentStep) el.classList.add('is-active');
            else if (idx < wizardCurrentStep) el.classList.add('is-complete');
        });

        backBtn.style.display = wizardCurrentStep === 1 ? 'none' : 'inline-block';
        const isLast = wizardCurrentStep === wizardTotalSteps;
        nextBtn.style.display = isLast ? 'none' : 'inline-block';
        submitBtn.style.display = isLast ? 'inline-block' : 'none';

        currentStepEl().scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function wizardNext() {
        if (!validateCurrentStep()) return;
        if (wizardCurrentStep < wizardTotalSteps) {
            wizardCurrentStep++;
            renderStep();
        }
    }

    function wizardGoTo(step) {
        if (step < 1 || step > wizardTotalSteps) return;
        wizardCurrentStep = step;
        renderStep();
    }

    nextBtn.addEventListener('click', wizardNext);
    backBtn.addEventListener('click', () => wizardGoTo(wizardCurrentStep - 1));

    // Conditional-logic event bindings
    document.getElementById('educational_level')?.addEventListener('change', applyEducationLevelLogic);
    document.getElementById('haveexp')?.addEventListener('change', applyExperienceLogic);
    document.getElementById('workplace')?.addEventListener('change', applyLanguageLogic);
    document.getElementById('physical_condition')?.addEventListener('change', applyPhysicalConditionLogic);
    document.getElementById('agri_business_experience_status')?.addEventListener('change', applyAgriExperienceLogic);
    document.getElementById('has_dependents')?.addEventListener('change', applyDependentsLogic);
    document.getElementById('children_under_five')?.addEventListener('input', validateChildrenUnderFive);
    document.getElementById('number_of_dependents')?.addEventListener('input', validateChildrenUnderFive);

    // Live sanitize + validate for all data-validate fields
    form.querySelectorAll('[data-validate]').forEach(input => {
        input.addEventListener('input', function () {
            sanitizeOnInput(input);
            validateField(input);
        });
        input.addEventListener('blur', function () {
            validateField(input);
        });
    });

    // Final safety net on submit
    form.addEventListener('submit', function (e) {
        if (!validateChildrenUnderFive()) {
            e.preventDefault();
            wizardGoTo(3);
            document.getElementById('children_under_five').focus();
        }
    });

    applyAllConditionalLogic();
    renderStep();
})();
</script>