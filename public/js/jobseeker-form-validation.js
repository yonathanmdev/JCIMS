(function () {
    let currentStep = 1;
    const totalSteps = 3;
    const stepLabels = {
        1: 'ደረጃ 1 / 3 — መግቢያ',
        2: 'ደረጃ 2 / 3 — የትምህርትና ሌሎች አስፈላጊ መረጃ',
        3: 'ደረጃ 3 / 3 — ምርጫና ሌሎች ጉዳዮች'
    };

    // ── Custom field validators config ─────────────────────────────────

    const AGRI_LABEL = 'ግብርና';
    const SECTOR_SELECT_IDS = ['choice_sector1', 'choice_sector2', 'choice_sector3'];

    const NAME_ONLY_PATTERN = /^\p{L}*$/u;
    const TEXT_WITH_SPACES_PATTERN = /^[\p{L}]+(\s[\p{L}]+)*$/u; // multiple words, single spaces between, no leading/trailing/double spaces
    const NUMERIC_PATTERN = /^\d*$/;
    const NUMERIC_DASH_SLASH_SPACE_PATTERN = /^[\d\-\/\s]*$/;
    const GENERAL_SAFE_PATTERN = /^[\p{L}\d\-\/\s፣]*$/u;
    const DECIMAL_PATTERN = /^\d+(\.\d+)?$/;

    const validators = {
        'name-only': {
            pattern: NAME_ONLY_PATTERN,
            message: 'ቁጥር፣ ልዩ ምልክት ወይም ክፍተት መጠቀም አይቻልም።'
        },
        'text-with-spaces': {
            pattern: TEXT_WITH_SPACES_PATTERN,
            message: 'ቁጥር ወይም ልዩ ምልክት መጠቀም አይቻልም።' // no numbers or special characters (spaces allowed between words)
        },
        'numeric-only': {
            pattern: NUMERIC_PATTERN,
            message: 'ቁጥር ብቻ መጠቀም ይቻላል።' // Only numbers are allowed.
        },
        'numeric-dash-slash': {
            pattern: NUMERIC_DASH_SLASH_SPACE_PATTERN,
            message: 'ቁጥር፣ "-"፣ "/" እና ክፍተት ብቻ መጠቀም ይቻላል።' // Only numbers, "-", "/", and spaces are allowed.
        },
        'general-safe': {
            pattern: GENERAL_SAFE_PATTERN,
            message: 'ልዩ ምልክት (እንደ <, >, %, ;) መጠቀም አይቻልም።' // Special characters like <, >, %, ; are not allowed.
        },
        'decimal': {
            pattern: DECIMAL_PATTERN,
            message: 'ቁጥር ብቻ መጠቀም ይቻላል።' // Only numbers or decimal numbers are allowed.
        }
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

    function validateField(input) {
        const type = input.getAttribute('data-validate');
        const validator = validators[type];
        if (!validator) return true;

        const value = input.value;
        const feedback = getFeedbackEl(input);

        // Empty value: only invalid if field is required (numeric optional fields skip this check)
        if (value === '') {
            input.classList.remove('is-invalid', 'is-valid');
            input.setCustomValidity('');
            return true;
        }

        // Pattern check (numbers only, letters only, etc.)
        if (!validator.pattern.test(value)) {
            input.classList.add('is-invalid');
            input.classList.remove('is-valid');
            feedback.textContent = validator.message;
            input.setCustomValidity(validator.message);
            return false;
        }

        // Exact-length check, only applies if data-length is set
        const requiredLength = input.getAttribute('data-length');
        if (requiredLength && value.length !== parseInt(requiredLength, 10)) {
            const msg = getExactLengthMessage(requiredLength);
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
                .replace(/[^\p{L}፣\s]/gu, '') // Added ፣ to allow Ethiopic comma
                .replace(/\s{2,}/g, ' ')
                .replace(/^\s+/, '');
            if (cleaned !== input.value) input.value = cleaned;

        } else if (type === 'numeric-only') {
            let cleaned = input.value.replace(/\D/g, ''); // strip anything non-digit

            // Length is taken from the form attribute 'data-length'
            const maxLength = input.getAttribute('data-length');
            if (maxLength) {
                cleaned = cleaned.slice(0, parseInt(maxLength, 10));
            }

            if (cleaned !== input.value) input.value = cleaned;

        } else if (type === 'decimal') {
            let cleaned = input.value
                .replace(/[^\d.]/g, '')      // Allow only digits and .
                .replace(/(\..*)\./g, '$1'); // Allow only one decimal point

            if (cleaned !== input.value) {
                input.value = cleaned;
            }
        }
        else if (type === 'numeric-dash-slash') {
            let cleaned = input.value
                .replace(/[^\d\-\/\s]/g, '')   // strip anything not digit, dash, slash, or space
                .replace(/\s{2,}/g, ' ')        // collapse multiple spaces into one
                .replace(/^\s+/, '');           // strip leading space

            // Length check removed for this type
            if (cleaned !== input.value) input.value = cleaned;

        } else if (type === 'general-safe') {
            let cleaned = input.value
                .replace(/[^\p{L}\d\-\/\s፣]/gu, '')  // allow letters, digits, dash, slash, space, and Ethiopic comma
                .replace(/\s{2,}/g, ' ')             // FIX: collapse multiple spaces into one (was collapsing to '')
                .replace(/^\s+/, '');                // FIX: strip leading space, consistent with other types

            // Length check removed for this type
            if (cleaned !== input.value) input.value = cleaned;
        }
    }

    function getExactLengthMessage(length) {
        return `ይህ Field በትክክል ${length} digits  መሆን አለበት።`; // This field must contain exactly {length} digits.
    }

    function validateAllFields(container) {
        const scope = container || document;
        let allValid = true;

        scope.querySelectorAll('[data-validate]').forEach(function (input) {
            if (!validateField(input)) {
                allValid = false;
            }
        });

        // Run the cross-field validation if the relevant fields are in this scope
        if (
            scope.querySelector('#number_of_dependents') &&
            scope.querySelector('#children_under_five')
        ) {
            if (!validateChildrenUnderFive()) {
                allValid = false;
            }
        }

        return allValid;
    }

    // ── Step navigation ───────────────────────────────────────────────
    function changeStep(direction) {
        const currentStepEl = document.getElementById(`step-${currentStep}`);

        if (direction === 1) {
            // Native required-field check
            const inputs = currentStepEl.querySelectorAll('[required]');
            for (const input of inputs) {
                if (!input.checkValidity()) {
                    input.reportValidity();
                    return;
                }
            }

            // Custom pattern validation (name-only fields etc.), scoped to current step
            if (!validateAllFields(currentStepEl)) {
                return;
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
        // Conditional logic no longer runs here — it now runs after the
        // create/edit trigger has either cleared or populated the form,
        // via applyAllConditionalLogic() bound to shown.bs.modal.
    }

    // ── Conditional Logic Functions ────────────────────────────────────
    function applyEducationLevelLogic() {
        const eduLevel = document.getElementById('educational_level').value;

        const hideFor = ['ማንበብና መፃፍ የማይችሉ', 'መሰረተ ትምህርት'];

        const educated_dptLevels = [
            'ደረጃ 2',
            'ደረጃ 3',
            'ደረጃ 4',
            'ደረጃ 5',
            'የመጀመሪያ ዲግሪ',
            'ሁለተኛ ዲግሪ'
        ];

        const g8idLevels = [
            '8ኛ ያጠናቀቁ',
            'ከ9-10ኛ',
            'ከ11-12ኛ',
            'ደረጃ 2',
            'ደረጃ 3',
            'ደረጃ 4',
            'ደረጃ 5',
            'የመጀመሪያ ዲግሪ',
            'ሁለተኛ ዲግሪ'
        ];

        // education_trmnet_finsh_year ─────────────────────────
        const generalFields = document.querySelectorAll('.field-school, .field-year');

        if (hideFor.includes(eduLevel)) {
            generalFields.forEach(el => {
                el.classList.add('d-none');
                const input = el.querySelector('input, select');
                if (input) {
                    input.removeAttribute('required');
                    input.value = '';
                }
            });
        } else {
            generalFields.forEach(el => el.classList.remove('d-none'));
            document.getElementById('education_trmnet_finsh_year')?.setAttribute('required', 'required');
        }

        // ── educated_dpt, graguation_catagory, CGPA, school_type: hide + toggle required ──
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
                if (input) {
                    input.removeAttribute('required');
                    input.value = '';
                }
            });
        }

        // ── phone_number: always visible, only required toggles ─────────────
        const phoneInput = document.getElementById('phone_number');

        if (educated_dptLevels.includes(eduLevel)) {
            phoneInput?.setAttribute('required', 'required');
        } else {
            phoneInput?.removeAttribute('required');
        }

        // ── g8id: shown/required for a broader level set ──────────────
        const g8idField = document.querySelector('.field-grade8');
        const g8idInput = document.getElementById('g8id');

        if (g8idLevels.includes(eduLevel)) {
            g8idField?.classList.remove('d-none');
            g8idInput?.setAttribute('required', 'required');
        } else {
            g8idField?.classList.add('d-none');
            if (g8idInput) {
                g8idInput.removeAttribute('required');
                g8idInput.value = '';
            }
        }
    }

    function applyExperienceLogic() {
        const haveExp = document.querySelector('input[name="haveexp"]:checked')?.value
            ?? document.getElementById('haveexp')?.value;

        // field-language is handled separately by applyLanguageLogic() — excluded here
        const expFields = document.querySelectorAll(
            '.field-experience, .field-workplace, .field-profession'
        );

        if (haveExp === '1') {
            expFields.forEach(el => {
                el.classList.remove('d-none');
                const input = el.querySelector('input, select');
                if (input) {
                    input.setAttribute('required', 'required');
                }
            });
        } else {
            expFields.forEach(el => {
                el.classList.add('d-none');
                const input = el.querySelector('input, select');
                if (input) {
                    input.removeAttribute('required');
                    input.value = '';
                }
            });
        }

        // Re-evaluate language field whenever experience toggle changes,
        // since hiding experience should also hide/clear language.
        applyLanguageLogic();
    }

    function applyLanguageLogic() {
        const haveExp = document.querySelector('input[name="haveexp"]:checked')?.value
            ?? document.getElementById('haveexp')?.value;

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
            // FIX: hiding these fields must also clear their required flag and
            // value — otherwise a hidden countryInput can be left "required",
            // silently blocking step navigation / form submission, and a
            // stale value could get submitted with the form.
            countryField?.classList.add('d-none');
            languageField.classList.add('d-none');

            if (countryInput) {
                countryInput.removeAttribute('required');
                countryInput.value = '';
            }
            if (input) {
                input.removeAttribute('required');
                input.value = '';
            }
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
            if (physicalDescInput) {
                physicalDescInput.removeAttribute('required');
                physicalDescInput.value = '';
            }
        }
    }
    SECTOR_SELECT_IDS.forEach(id => {
    const el = document.getElementById(id);

    el?.addEventListener('change', function () {
        console.log(id, "changed to:", getSelectedText(this));
        applyAgriStatusVisibility();
    });
});

    // Step 1: show/hide the agri STATUS field itself, based on sector selection
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
            if (agriStatusSelect) {
                agriStatusSelect.removeAttribute('required');
                agriStatusSelect.value = '';
            }
        }

        // If the status field just got hidden/reset, the dependent
        // experience field must also be re-evaluated (it should collapse too).
        applyAgriExperienceLogic();
    }

    // Step 2: show/hide the agri EXPERIENCE field, based on the status field's value
    function applyAgriExperienceLogic() {
        const agriStatusSelect = document.getElementById('agri_business_experience_status');
        const agriStatus = agriStatusSelect?.value;

        const agriExpField = document.querySelector('.field-agri-experience');
        const agriExpInput = document.getElementById('agri_business_experience');

        if (agriStatus === '1') {
            agriExpField?.classList.remove('d-none');
            agriExpInput?.setAttribute('required', 'required');
        } else {
            agriExpField?.classList.add('d-none');
            if (agriExpInput) {
                agriExpInput.removeAttribute('required');
                agriExpInput.value = '';
            }
        }
    }

    function applyGenderDependentsVisibility() {
    const genderSelect = document.getElementById('gender');
    const genderValue = genderSelect?.value;

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
        if (dependentsSelect) {
            dependentsSelect.removeAttribute('required');
            dependentsSelect.value = '';
        }

        housewifeField?.classList.add('d-none');
        if (housewifeSelect) {
            housewifeSelect.removeAttribute('required');
            housewifeSelect.value = '';
        }
    }

    // If has_dependents just got hidden/reset (e.g. gender switched away from ሴት),
    // the fields depending on ITS value must also collapse/clear.
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
            if (numberofDependentsInput) {
                numberofDependentsInput.removeAttribute('required');
                numberofDependentsInput.value = '';
            }
            if (childrenUnderFiveInput) {
                childrenUnderFiveInput.removeAttribute('required');
                childrenUnderFiveInput.value = '';
            }
        }
    }

    function validateChildrenUnderFive() {
        const numberOfDependentsInput = document.getElementById('number_of_dependents');
        const childrenUnderFiveInput = document.getElementById('children_under_five');

        if (!numberOfDependentsInput || !childrenUnderFiveInput) {
            return true;
        }

        const maxDependents = parseInt(numberOfDependentsInput.value, 10) || 0;
        const childrenValue = parseInt(childrenUnderFiveInput.value, 10) || 0;

        const feedback = getFeedbackEl(childrenUnderFiveInput);

        if (
            childrenUnderFiveInput.value !== '' &&
            childrenValue > maxDependents
        ) {
            const msg = `ከ5 ዓመት በታች ያሉ ልጆች ቁጥር ከጠቅላላ ቤተሰብ ብዛት (${maxDependents}) መብለጥ አይችልም።`;

            childrenUnderFiveInput.classList.add('is-invalid');
            childrenUnderFiveInput.classList.remove('is-valid');

            if (feedback) {
                feedback.textContent = msg;
            }

            childrenUnderFiveInput.setCustomValidity(msg);

            return false;
        }

        childrenUnderFiveInput.classList.remove('is-invalid');
        childrenUnderFiveInput.classList.add('is-valid');

        if (feedback) {
            feedback.textContent = '';
        }

        childrenUnderFiveInput.setCustomValidity('');

        return true;
    }

    function clearValidationState(form) {
        form.querySelectorAll('[data-validate], [required]').forEach(function (input) {
            input.classList.remove('is-valid', 'is-invalid');
            input.setCustomValidity('');
        });
        form.querySelectorAll('.invalid-feedback[data-validator-feedback]').forEach(function (fb) {
            fb.textContent = '';
        });
    }

    
function populateSubSectorsForEdit(js) {
    // Step 1: render each sub-select's options based on its OWN sector value,
    // in order 1 → 2 → 3, so getSelectedSubValues() sees earlier selections
    // already in place when rendering later ones (correct "taken" greying).
    [1, 2, 3].forEach(n => {
        const subSelect = document.getElementById(`sub_choose${n}`);
        if (!subSelect) return;

        // Set the value BEFORE rendering options isn't right either —
        // renderSubOptions rebuilds innerHTML, so instead:
        renderSubOptions(subSelect);              // builds the option list for current sector
        subSelect.value = js[`sub_choose${n}`] ?? ''; // now select the saved value
    });

    // Step 2: one more pass to correctly greltay-out cross-selected values
    // now that all three have their real final values in place
    [1, 2, 3].forEach(n => {
        const subSelect = document.getElementById(`sub_choose${n}`);
        if (subSelect) renderSubOptions(subSelect); // re-render so "taken elsewhere" reflects final state
    });

    // Step 3: since re-rendering wipes value again, restore once more (final)
    [1, 2, 3].forEach(n => {
        const subSelect = document.getElementById(`sub_choose${n}`);
        if (subSelect) subSelect.value = js[`sub_choose${n}`] ?? '';
    });
}
    // ── populateFormFromData — now async, handles cascading dropdowns ──
function populateFormFromData(form, js) {
    Object.keys(js).forEach(key => {
        const field = form.elements[key];
        if (!field) return;
        if (key.includes('sub_sector') || key.includes('sub_choose')) return;

        if (field.type === 'checkbox') {
            field.checked = !!js[key];
        } else if (field.type === 'radio') {
            form.querySelectorAll(`input[name="${key}"]`).forEach(r => {
                r.checked = (r.value == js[key]);
            });
        } else {
            field.value = js[key] ?? '';
        }
    });

    populateSubSectorsForEdit(js); // handles sub_choose1/2/3 correctly, no events involved

    applyAllConditionalLogic?.();
}

    // ── Main Initialization ────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function () {
        // Step navigation
        document.getElementById('prevBtn')?.addEventListener('click', () => changeStep(-1));
        document.getElementById('nextBtn')?.addEventListener('click', () => changeStep(1));

        // Conditional logic event listeners
        document.getElementById('educational_level')?.addEventListener('change', applyEducationLevelLogic);
        document.getElementById('haveexp')?.addEventListener('change', applyExperienceLogic);
        document.getElementById('workplace')?.addEventListener('change', applyLanguageLogic);
        document.getElementById('physical_condition')?.addEventListener('change', applyPhysicalConditionLogic);


        document.getElementById('agri_business_experience_status')?.addEventListener('change', applyAgriExperienceLogic);
        document.getElementById('gender')?.addEventListener('change', applyGenderDependentsVisibility);
        document.getElementById('has_dependents')?.addEventListener('change', applyDependentsLogic);
        document.getElementById('children_under_five')?.addEventListener('input', validateChildrenUnderFive);
        document.getElementById('number_of_dependents')?.addEventListener('input', validateChildrenUnderFive);

        // Run all conditional logic once on load to apply initial states
        applyAllConditionalLogic();

       document.getElementById('jobseekerForm')?.addEventListener('submit', function (e) {

    if (!validateChildrenUnderFive()) {

        e.preventDefault();

        const $form = $(this);

        // 🚫 stop global script from disabling buttons
        $form.data('prevent-submit', true);

        // restore UI state
        $form.data('submitting', false);
        $form.removeData('submitButton');

        $form.find('button[type="submit"], input[type="submit"]')
              .prop('disabled', false);

        document.getElementById('children_under_five').focus();

        // cleanup flag
        setTimeout(() => {
            $form.removeData('prevent-submit');
        }, 0);

        return;
    }
});

        // Reset wizard each time modal opens
        $('#jobseekerRegistrationModal').on('show.bs.modal', resetWizard);

        // ── Registration mode — open modal empty, in create mode ──────
        document.getElementById('newJobseekerBtn')?.addEventListener('click', function () {
            const form = document.getElementById('jobseekerForm');
            form.reset();
            clearValidationState(form);
            document.getElementById('formMode').value = 'create';
            document.getElementById('jobseekerIdField').value = '';
            document.getElementById('jobseekerModalTitle').innerHTML =
                '<i class="fas fa-plus mr-1"></i> የስራ ፈላጊ መመዝገቢያ ፎርም';
            const submitBtn = document.getElementById('submitBtn');
            document.getElementById('submitBtn').textContent = 'መዝግብ';
            submitBtn.classList.remove('btn-warning');
            submitBtn.classList.add('btn-success');

            $('#jobseekerRegistrationModal').modal('show');
            $('#jobseekerRegistrationModal').one('shown.bs.modal', function () {
                applyAllConditionalLogic();
            });
        });

        // ── Edit mode — fetch existing data, populate, open modal ─────
        $(document).on('click', '.edit-jobseeker-btn', function () {
            console.time('total-edit-open');
            const jobseekerId = this.dataset.id;
            const form = document.getElementById('jobseekerForm');

            console.time('fetch');
            fetch(`${window.BASE_URL}/retrieve-jobseeker?jobseeker_id=${jobseekerId}`)
                .then(res => res.json())
                .then(function (data) {
                    console.timeEnd('fetch');
                    if (!data.success) {
                        Swal.fire({ icon: 'error', title: data.message || 'መረጃ አልተገኘም' });
                        return;
                    }

                    form.reset();
                    clearValidationState(form);
                    document.getElementById('formMode').value = 'edit';
                    document.getElementById('jobseekerIdField').value = jobseekerId;
                    document.getElementById('jobseekerModalTitle').innerHTML =
                        '<i class="fas fa-edit mr-1"></i> የስራ ፈላጊ መረጃ ማስተካከያ ፎርም';
                    document.getElementById('submitBtn').textContent = 'አስተካክል';
                    const submitBtn = document.getElementById('submitBtn');
                    submitBtn.classList.remove('btn-success');
                    submitBtn.classList.add('btn-warning');

                 console.time('populate');
                 populateFormFromData(form, data.jobseeker); // now synchronous — no await needed
                 // console.timeEnd('populate');

                    $('#jobseekerRegistrationModal').modal('show');
                    $('#jobseekerRegistrationModal').one('shown.bs.modal', function () {
                        applyAllConditionalLogic(); // ONLY AFTER populate and modal shown
                    });
                    console.timeEnd('total-edit-open');
                })
                .catch((err) => {
                    console.error('REAL ERROR:', err);
                    Swal.fire({ icon: 'error', title: 'መረጃ መጫን አልተሳካም' });
                });
        });

        // Live sanitize + validate for name-only fields, across the whole form
        document.querySelectorAll('[data-validate]').forEach(function (input) {
            input.addEventListener('input', function () {
                sanitizeOnInput(input);
                validateField(input);
            });
            input.addEventListener('blur', function () {
                validateField(input);
            });
        });
    });

    // Global function to be called by applyAllConditionalLogic
    function applyAllConditionalLogic() {
        applyEducationLevelLogic();
        applyExperienceLogic();
        applyPhysicalConditionLogic();
        applyGenderDependentsVisibility();
        applyAgriStatusVisibility();
        // No need to explicitly call populateSubSectors here as it's handled by change events
        // on sector selects, which are triggered by populateFormFromData.
    }

})();