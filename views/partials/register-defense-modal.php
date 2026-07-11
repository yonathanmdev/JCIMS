<!-- Register Defense Applicant Modal -->
<div class="modal fade" id="registerDefenseModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
           
            <!-- አድራሻውን ወደ ዋናው መቆጣጠሪያ ራውትህ አዙረነዋል -->
           <form action="<?= rtrim($_ENV['BASE_URL'], '/') ?>/defense-direct-registration-process"
      method="POST"
      data-client-validate="true"
      novalidate>
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

    <!-- Modal Header -->
    <div class="modal-header bg-danger text-white">
        <h6 class="modal-title font-weight-bold" id="recruitmentModalLabel">
            <i class="fas fa-shield-alt mr-1"></i>
            የአገር መከላከያ ሰራዊት ቅጥር ቀጥታ ምዝገባ ፎርም
        </h6>
        <button type="button" class="close text-white" data-dismiss="modal">
            <span>&times;</span>
        </button>
    </div>

    <!-- Modal Body -->
    <div class="modal-body">
        
        <!-- 1. ሙሉ ስም እና ብሄራዊ መለያ (National ID) -->
        <div class="row">
            <div class="col-md-8">
                <div class="form-group mb-2">
                    <label for="fullname">
                        <small class="font-weight-bold">ሙሉ ስም (የአያት ስምን ጨምሮ)</small>
                    </label>
                    <input type="text"
                           class="form-control form-control-sm"
                           id="fullname"
                           name="fullname"
                           maxlength="100"
                           data-restrict="letters"
                           required>
                    <div class="invalid-feedback">እባክዎ ሙሉ ስም ያስገቡ።</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group mb-2">
                    <label for="national_id">
                        <small class="font-weight-bold">ብሄራዊ መለያ ቁጥር (Fayda ID)</small> <span class="text-muted text-xs">(አማራጭ)</span>
                    </label>
                    <input type="text"
                           class="form-control form-control-sm"
                           id="national_id"
                           name="national_id"
                           maxlength="50">
                </div>
            </div>
        </div>

        <!-- 2. ጾታ፣ እድሜ እና ስልክ ቁጥር -->
        <div class="row">
            <div class="col-md-4">
                <div class="form-group mb-2">
                    <label for="sex">
                        <small class="font-weight-bold">ጾታ</small>
                    </label>
                    <select class="form-control form-control-sm" id="sex" name="sex" required>
                        <option value="">-- ይምረጡ --</option>
                        <option value="ወንድ">ወንድ</option>
                        <option value="ሴት">ሴት</option>
                    </select>
                    <div class="invalid-feedback">እባክዎ ጾታ ይምረጡ።</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group mb-2">
                    <label for="age">
                        <small class="font-weight-bold">እድሜ</small>
                    </label>
                    <input type="number"
                           class="form-control form-control-sm"
                           id="age"
                           name="age"
                           min="18"
                           max="30"
                           required>
                    <div class="invalid-feedback">እባክዎ ትክክለኛ እድሜ ያስገቡ (18-30)።</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group mb-2">
                    <label for="phone">
                        <small class="font-weight-bold">ስልክ ቁጥር</small>
                    </label>
                    <input type="tel"
                           class="form-control form-control-sm"
                           id="phone"
                           name="phone"
                           placeholder="09..."
                           maxlength="10"
                           required>
                    <div class="invalid-feedback">እባክዎ ስልክ ቁጥር ያስገቡ።</div>
                </div>
            </div>
        </div>

       <!-- 3. የትምህርት ደረጃ እና የተማረው የትምህርት መስክ (Study Field) -->
<div class="row">
    <div class="col-md-6">
        <div class="form-group mb-2">
            <label for="education_level">
                <small class="font-weight-bold">የትምህርት ደረጃ</small>
            </label>
            <select class="form-control form-control-sm" id="education_level" name="education_level" required>
                <option value="">-- ይምረጡ --</option>
                <option value="8ኛ ክፍል ያጠናቀቀ">8ኛ ክፍል ያጠናቀቀ</option>
                <option value="10ኛ/12ኛ ክፍል">10ኛ/12ኛ ክፍል ያጠናቀቀ</option>
                <option value="ሰርተፊኬት (Level I/II)">ሰርተፊኬት (Level I/II)</option>
                <option value="ዲፕሎማ (Level III/IV)">ዲፕሎማ (Level III/IV)</option>
                <option value="የመጀመሪያ ዲግሪ">የመጀመሪያ ዲግሪ</option>
                <option value="ከዛ በላይ">ከዛ በላይ</option>
            </select>
            <div class="invalid-feedback">እባክዎ የትምህርት ደረጃ ይምረጡ።</div>
        </div>
    </div>
    
    <!-- በቀላሉ ለመደበቅ እና ለማሳየት id="study_field_wrapper" ተጨምሯል -->
    <div class="col-md-6" id="study_field_wrapper" style="display: none;">
        <div class="form-group mb-2">
            <label for="educated_study">
                <small class="font-weight-bold">የተማሩት የትምህርት መስክ / ሙያ</small>
            </label>
            <input type="text"
                   class="form-control form-control-sm"
                   id="educated_study"
                   name="educated_study"
                   placeholder="ምሳሌ፦ ማህበራዊ ሳይንስ፣ መካኒክ፣ ነርሲንግ...">
            <div class="invalid-feedback">እባክዎ የትምህርት መስክ ያስገቡ።</div>
        </div>
    </div>
</div>

        <!-- 4. ተጨማሪ ክህሎት እና የተመረጠው ዘርፍ/ፍላጎት -->
        <div class="row">
            <div class="col-md-6">
                <div class="form-group mb-2">
                    <label for="additional_skill">
                        <small class="font-weight-bold">ተጨማሪ ክህሎት (ካለ)</small>
                    </label>
                    <input type="text"
                           class="form-control form-control-sm"
                           id="additional_skill"
                           name="additional_skill"
                           placeholder="ምሳሌ፦ ሹፌር (መንጃ ፈቃድ ያለው)፣ ኮምፒውተር...">
                </div>
            </div>
          
            <div class="col-md-3">
                <div class="form-group mb-2">
                    <label for="sector">
                        <small class="font-weight-bold">ምድብ / ሴክተር</small>
                    </label>
                    <select class="form-control form-control-sm" id="sector" name="sector" required>
                        <option value="">-- ይምረጡ --</option>
                        <option value="መደበኛ ሰራዊት">መደበኛ ሰራዊት</option>
                        <option value="ቴክኒካል/እዝ">ቴክኒካል/እዝ</option>
                        <option value="የጤና ባለሙያ ቡድን">የጤና ባለሙያ ቡድን</option>
                    </select>
                    <div class="invalid-feedback">እባክዎ ሴክተር ይምረጡ።</div>
                </div>
            </div>
        </div>

        <!-- 5. መዋቅር፦ ቅርንጫፍ (Branch ID)፣ ቀበሌ እና የተመዘገበበት ቦታ ye ssetionun brachiid new emnwesd  -->
        <div class="row">
       
            <div class="col-md-4">
                <div class="form-group mb-2">
                    <label for="kebele">
                        <small class="font-weight-bold">የመኖሪያ ቀበሌ</small>
                    </label>
                    <input type="text"
                           class="form-control form-control-sm"
                           id="kebele"
                           name="kebele"
                           placeholder="ምሳሌ፦ 01 ወይም የገጠር ቀበሌ ስም"
                           required>
                    <div class="invalid-feedback">እባክዎ ቀበሌ ያስገቡ።</div>
                </div>
            </div>
        
        </div>

    </div>

    <!-- Modal Footer -->
    <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">ዝጋ</button>
        <button type="submit" class="btn btn-danger btn-sm">
            <i class="fas fa-user-plus mr-1"></i> የእጩ መከላከያ ምዝገባን አጽድቅ
        </button>
    </div>
</form>
        </div>
    </div>
</div>
<script nonce="<?php echo htmlspecialchars($GLOBALS['nonce'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
document.addEventListener('DOMContentLoaded', function () {
    // 'data-client-validate="true"' ያለውን ፎርም መምረጥ
    const registrationForm = document.querySelector('form[data-client-validate="true"]');
    if (!registrationForm) return;

    // ሁሉንም የፎርም ኤለመንቶች በ ID መያዝ
    const eduLevelSelect   = document.getElementById('education_level');
    const educatedStudy    = document.getElementById('educated_study');
    const studyFieldWrapper = document.getElementById('study_field_wrapper');
    const phoneInput       = document.getElementById('phone');
    const fullnameInput    = document.getElementById('fullname');
    const ageInput         = document.getElementById('age');
    const nationalIdInput  = document.getElementById('national_id');

    /**
     * 1. የትምህርት ደረጃን አይቶ የትምህርት መስክ ፊልድን የመደበቅ/ማሳየት ህግ
     */
    function toggleStudyField() {
        if (!eduLevelSelect || !educatedStudy || !studyFieldWrapper) return;

        const selectedValue = eduLevelSelect.value;
        
        // የትምህርት መስክ መሙላት የሚያስገድዱ ደረጃዎች
        const requiredLevels = [
            "ሰርተፊኬት (Level I/II)",
            "ዲፕሎማ (Level III/IV)",
            "የመጀመሪያ ዲግሪ",
            "ከዛ በላይ"
        ];

        if (requiredLevels.includes(selectedValue)) {
            // ከተመረጡት ውስጥ ከሆነ -> አሳይ እና አስገዳጅ አድርግ
            studyFieldWrapper.style.display = 'block';
            educatedStudy.setAttribute('required', 'required');
        } else {
            // 8ኛ፣ 10ኛ/12ኛ ከሆኑ ወይም ምንም ካልተመረጠ -> ደብቅ፣ አስገዳጅነቱን አንሳ፣ ዳታውን አጽዳ
            studyFieldWrapper.style.display = 'none';
            educatedStudy.removeAttribute('required');
            educatedStudy.value = ''; 
            educatedStudy.setCustomValidity(''); 
        }
    }

    // ተጠቃሚው በትምህርት ደረጃ ማውጫ ላይ ሲመርጥ ወዲያውኑ እንዲቀያየር ማድረግ
    if (eduLevelSelect) {
        eduLevelSelect.addEventListener('change', toggleStudyField);
        // ገጹ ሲከፈት መጀመሪያ ያለውን ሁኔታ ቼክ ለማድረግ
        toggleStudyField();
    }

    /**
     * 2. የዳታ ኢንተግሪቲ ማጣሪያዎች (Real-time Input Validation)
     */

    // ስልክ ቁጥር ቁጥጥር (በ 09 ወይም 07 ብቻ እንዲጀምር እና ከ 10 አሃዝ እንዳይበልጥ)
    if (phoneInput) {
        phoneInput.addEventListener('input', function () {
            this.value = this.value.replace(/[^0-9]/g, ''); // ከቁጥር ውጪ ማጥፊያ
            if (this.value.length > 0 && this.value[0] !== '0') this.value = '';
            if (this.value.length > 1 && !['9', '7', '1'].includes(this.value[1])) this.value = this.value[0];
        });
    }

    // ሙሉ ስም ቁጥጥር (ፊደላትን እና ቦታን ብቻ መፍቀድ፣ ቁጥርና ልዩ ምልክቶችን መከልከል)
    if (fullnameInput) {
        fullnameInput.addEventListener('input', function () {
            this.value = this.value.replace(/[0-9!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?`~]/g, '');
        });
    }

    // የእድሜ ቁጥጥር (ከ 30 በላይ እንዳይተየብ መግታት)
    if (ageInput) {
        ageInput.addEventListener('input', function () {
            let val = parseInt(this.value, 10);
            if (val > 30) this.value = 30;
            else if (val < 0) this.value = '';
        });
    }

    // ብሄራዊ መለያ (Fayda ID) ቁጥጥር (ልዩ ምልክቶችን መከልከል)
    if (nationalIdInput) {
        nationalIdInput.addEventListener('input', function () {
            this.value = this.value.replace(/[^a-zA-Z0-9\s\-]/g, '');
        });
    }

    /**
     * 3. ፎርሙ ሰብሚት (Submit) ሲደረግ የመጨረሻ ማረጋገጫ መስራት
     */
    registrationForm.addEventListener('submit', function (event) {
        let isValid = true;

        // የስልክ ቁጥር በትክክል 10 ዲጂት መሆኑን ማረጋገጥ
        if (phoneInput && phoneInput.value.trim().length !== 10) {
            phoneInput.setCustomValidity('የስልክ ቁጥር 10 አሃዝ መሆን አለበት።');
            isValid = false;
        } else if (phoneInput) {
            phoneInput.setCustomValidity('');
        }

        // የእድሜ ክልል (18-30) ማረጋገጥ
        if (ageInput) {
            let ageVal = parseInt(ageInput.value, 10);
            if (isNaN(ageVal) || ageVal < 18 || ageVal > 30) {
                ageInput.setCustomValidity('እድሜ ከ18 እስከ 30 መሆን አለበት።');
                isValid = false;
            } else {
                ageInput.setCustomValidity('');
            }
        }

        // ሙሉ ስም ቢያንስ ሁለት ቃላት (ስም እና የአባት ስም) መያዙን ማረጋገጥ
        if (fullnameInput) {
            const nameParts = fullnameInput.value.trim().split(/\s+/);
            if (nameParts.length < 2 || nameParts[0].length < 2) {
                fullnameInput.setCustomValidity('እባክዎ ቢያንስ ስም እና የአባት ስም በትክክል ያስገቡ።');
                isValid = false;
            } else {
                fullnameInput.setCustomValidity('');
            }
        }

        // ፊልዱ ከተደበቀ የ HTML5required ቫሊዴሽኑን እንዲያልፍ በጽኑ ማረጋገጥ
        if (educatedStudy && !educatedStudy.hasAttribute('required')) {
            educatedStudy.setCustomValidity('');
        }

        // ማረጋገጫውን ካላለፈ ፎርሙ እንዳይላክ መከልከል
        if (!registrationForm.checkValidity() || !isValid) {
            event.preventDefault();
            event.stopPropagation();
        }

        // ስህተቶችን በደማቅ ቀይ ቀለም ለማሳየት የቡትስትራፕ ክላስ መጨመር
        registrationForm.classList.add('was-validated');
    }, false);
});
</script>