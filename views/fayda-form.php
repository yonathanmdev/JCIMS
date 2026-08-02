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
<div class="card">
    <div class="card-header">
        <h6 class="font-weight-bold"><i class="fas fa-id-card mr-1"></i> በፋይዳ የተረጋገጠ የስራ ፈላጊ ምዝገባ</h6>
    </div>
    <div class="card-body">

        <?php if ($formError): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($formError) ?></div>
        <?php endif; ?>

        <div class="alert alert-info">
            <strong>ከፋይዳ የተረጋገጠ መረጃ</strong> — ሙሉ ስም፣ ጾታ፣ ስልክ፣ የልደት ቀን ሊስተካከሉ አይችሉም።
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
            <input type="hidden" name="gender"       value="<?= htmlspecialchars($faydaGender) ?>">
            <input type="hidden" name="phone_number" value="<?= htmlspecialchars($faydaPhone) ?>">
            <input type="hidden" name="age"          value="<?= htmlspecialchars($faydaAge) ?>">

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
                        <select class="form-control form-control-sm" id="srafelagi_huneta" name="srafelagi_huneta" required>
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
                        <select class="form-control form-control-sm" id="maritalstatus" name="maritalstatus" required>
                            <option value="" selected disabled>ይምረጡ</option>
                            <option value="ያላገባ/ች">ያላገባ/ች</option>
                            <option value="ያገባ/ች">ያገባ/ች</option>
                            <option value="የፈታ/ች">የፈታ/ች</option>
                            <option value="የሞተበት/ባት">የሞተበት/ባት</option>
                        </select>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="form-group mb-2">
                        <label class="mb-1" for="FAN"><small class="font-weight-bold">FAN <span class="text-danger">*</span></small></label>
                        <input type="text" class="form-control form-control-sm" id="FAN" name="FAN" data-validate="numeric-only" data-length="16" required>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="form-group mb-2">
                        <label class="mb-1" for="kebele"><small class="font-weight-bold">ቀበሌ <span class="text-danger">*</span></small></label>
                        <input type="text" class="form-control form-control-sm" id="kebele" name="kebele" required>
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
            </div>

            <div class="row">
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="form-group mb-2">
                        <label class="mb-1" for="school_type"><small class="font-weight-bold">የት/ቤቱ/የኮሌጁ/የዩንቨርሲቲው ዓይነት</small></label>
                        <select class="form-control form-control-sm" id="school_type" name="school_type">
                            <option value="" selected disabled>ይምረጡ</option>
                            <option value="የመንግስት">የመንግስት</option>
                            <option value="የግል">የግል</option>
                        </select>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="form-group mb-2">
                        <label class="mb-1" for="educated_dpt"><small class="font-weight-bold">የተመረቀበት ዲፓርትመንት</small></label>
                        <input type="text" name="educated_dpt" id="educated_dpt" class="form-control form-control-sm">
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="form-group mb-2">
                        <label class="mb-1" for="education_trmnet_finsh_year"><small class="font-weight-bold">ትምህርት ያጠናቀቀበት ዓመት</small></label>
                        <select class="form-control form-control-sm" id="education_trmnet_finsh_year" name="education_trmnet_finsh_year">
                            <option value="" selected disabled>ይምረጡ</option>
                            <?php for ($year = $fiscal_year; $year >= 1960; $year--): ?>
                                <option value="<?= $year ?>"><?= $year ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="form-group mb-2">
                        <label class="mb-1" for="g8id"><small class="font-weight-bold">የ8ኛ ክፍል መለያ ቁጥር</small></label>
                        <input type="text" name="g8id" id="g8id" class="form-control form-control-sm">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="form-group mb-2">
                        <label class="mb-1" for="graguation_catagory"><small class="font-weight-bold">ያጠናቀቁበት ሙያ የስራ ምድብ</small></label>
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
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="form-group mb-2">
                        <label class="mb-1" for="CGPA"><small class="font-weight-bold">CGPA</small></label>
                        <input type="text" class="form-control form-control-sm" id="CGPA" name="CGPA" data-validate="decimal" data-length="4">
                    </div>
                </div>
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
            </div>

            <div class="row">
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="form-group mb-2">
                        <label class="mb-1" for="physical_condition_desc"><small class="font-weight-bold">የአካል ጉዳቱ አይነት</small></label>
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
                        <select class="form-control form-control-sm" id="haveexp" name="haveexp" required>
                            <option value="" selected disabled>ይምረጡ</option>
                            <option value="1">አለ</option>
                            <option value="0">የለም</option>
                        </select>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="form-group mb-2">
                        <label class="mb-1" for="experience"><small class="font-weight-bold">የስራ ልምድ በወር</small></label>
                        <input type="number" step="any" class="form-control form-control-sm" id="experience" name="experience">
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="form-group mb-2">
                        <label class="mb-1" for="workplace"><small class="font-weight-bold">የሰሩበት ሀገር</small></label>
                        <select class="form-control form-control-sm" id="workplace" name="workplace">
                            <option value="" selected disabled>ይምረጡ</option>
                            <option value="ከሀገር ውስጥ">ከሀገር ውስጥ</option>
                            <option value="ከውጭ አገር">ከውጭ አገር</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="form-group mb-2">
                        <label class="mb-1" for="profession"><small class="font-weight-bold">የሰሩበት የሙያ መደብ</small></label>
                        <select class="form-control form-control-sm" id="profession" name="profession">
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
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="form-group mb-2">
                        <label class="mb-1" for="nameofcountry"><small class="font-weight-bold">የሀገሩ ስም</small></label>
                        <input type="text" class="form-control form-control-sm" id="nameofcountry" name="nameofcountry">
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="form-group mb-2">
                        <label class="mb-1" for="language"><small class="font-weight-bold">የሚችሉት ቋንቋ</small></label>
                        <input type="text" class="form-control form-control-sm" id="language" name="language">
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="form-group mb-2">
                        <label class="mb-1" for="wageorself"><small class="font-weight-bold">አሁን መስራት የሚፈልጉት <span class="text-danger">*</span></small></label>
                        <select class="form-control form-control-sm" id="wageorself" name="wageorself" required>
                            <option value="" selected disabled>ይምረጡ</option>
                            <option value="በግል">በግል</option>
                            <option value="በቅጥር">በቅጥር</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="form-group mb-2">
                        <label class="mb-1" for="mothername"><small class="font-weight-bold">የእናት ሙሉ ስም <span class="text-danger">*</span></small></label>
                        <input type="text" class="form-control form-control-sm" id="mothername" name="mothername" required>
                    </div>
                </div>

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
            </div>

            <div class="row">
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="form-group mb-2">
                        <label class="mb-1" for="sub_choose2"><small class="font-weight-bold">የሙያ ምርጫ 2 <span class="text-danger">*</span></small></label>
                        <select class="form-control form-control-sm" id="sub_choose2" name="sub_choose2" required disabled>
                            <option value="" selected disabled>-- መጀመሪያ ዘርፍ ይምረጡ --</option>
                        </select>
                    </div>
                </div>
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
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="form-group mb-2">
                        <label class="mb-1" for="agri_business_experience_status"><small class="font-weight-bold">በግብርና ዘርፍ ልምድ</small></label>
                        <select class="form-control form-control-sm" id="agri_business_experience_status" name="agri_business_experience_status">
                            <option value="" selected disabled>ይምረጡ</option>
                            <option value="1">አለ</option>
                            <option value="0">የለም</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="form-group mb-2">
                        <label class="mb-1" for="agri_business_experience"><small class="font-weight-bold">በግብርና ዘርፍ ያለው ልምድ</small></label>
                        <input type="number" step="any" class="form-control form-control-sm" id="agri_business_experience" name="agri_business_experience">
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="form-group mb-2">
                        <label class="mb-1" for="number_of_dependents"><small class="font-weight-bold">የሚተዳደረው ቤተሰብ ብዛት</small></label>
                        <input type="number" class="form-control form-control-sm" id="number_of_dependents" name="number_of_dependents">
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="form-group mb-2">
                        <label class="mb-1" for="children_under_five"><small class="font-weight-bold">ያሉት ከ5 ዓመት በታች ህፃናት ብዛት</small></label>
                        <input type="number" class="form-control form-control-sm" id="children_under_five" name="children_under_five">
                    </div>
                </div>
            </div>

            <p class="text-muted">ከመመዝገብዎ በፊት የሞሉቱን መረጃ ትክክለኛነት ያረጋግጡ።</p>

            <button type="submit" class="btn btn-success btn-block">መዝግብ</button>
        </form>
    </div>
</div>