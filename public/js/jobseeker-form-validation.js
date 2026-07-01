(function () {
  let currentStep = 1;
  const totalSteps = 3;
  const stepLabels = {
    1: 'ደረጃ 1 / 3 — መግቢያ',
    2: 'ደረጃ 2 / 3 — የትምህርትና ሌሎች አስፈላጊ መረጃ',
    3: 'ደረጃ 3 / 3 — ምርጫና ሌሎች ጉዳዮች'
  };

  // ── Custom field validators config ─────────────────────────────────
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

  } 
 else if (type === 'decimal') {
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
      .replace(/\s{2,}/g, ' ');

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
      if (!validateField(input)) allValid = false;
    });
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

  // Re-apply conditional logic in case the form retains old values on reopen,
  // or reset dropdowns to blank first if the modal should always start fresh
  applyEducationLevelLogic();
  applyExperienceLogic();
  applyPhysicalConditionLogic();
  applyAgriExperienceLogic();
  applyDependentsLogic();
}

  // Conditional field visibility based on education level (illiterate/basic = hide school fields)
 function applyEducationLevelLogic() {
  const eduLevel = document.getElementById('educational_level').value;

  const hideFor = ['ማንበብና መፃፍ የማይችሉ', 'መሰረተ ትምህርት'];

  const dptLevels = [
    'ደረጃ 2',
    'ደረጃ 3',
    'ደረጃ 4',
    'ደረጃ 5',
    'የመጀመሪያ ዲግሪ',
    'ሁለተኛ ዲግሪ'
  ];

  const gradeEightLevels = [
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

  // ── schoolname / education_completion_year ─────────────────────────
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
    document.getElementById('schoolname')?.setAttribute('required', 'required');
    document.getElementById('education_completion_year')?.setAttribute('required', 'required');
  }

  // ── dpt, graguation_catagory, CGPA, school_type: hide + toggle required ──
  const dptFields = document.querySelectorAll('.field-dept, .field-cgpa, .field-schooltype');

  if (dptLevels.includes(eduLevel)) {
    dptFields.forEach(el => {
      el.classList.remove('d-none');
      const input = el.querySelector('input, select');
      if (input) input.setAttribute('required', 'required');
    });
  } else {
    dptFields.forEach(el => {
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

  if (dptLevels.includes(eduLevel)) {
    phoneInput?.setAttribute('required', 'required');
  } else {
    phoneInput?.removeAttribute('required');
  }

  // ── gradeEight: shown/required for a broader level set ──────────────
  const gradeEightField = document.querySelector('.field-grade8');
  const gradeEightInput = document.getElementById('gradeEight');

  if (gradeEightLevels.includes(eduLevel)) {
    gradeEightField?.classList.remove('d-none');
    gradeEightInput?.setAttribute('required', 'required');
  } else {
    gradeEightField?.classList.add('d-none');
    if (gradeEightInput) {
      gradeEightInput.removeAttribute('required');
      gradeEightInput.value = '';
    }
  }
}
function applyExperienceLogic() {
  const haveExp = document.querySelector('input[name="haveexp"]:checked')?.value
    ?? document.getElementById('haveexp')?.value; // adjust based on whether haveexp is radio or select

  const expFields = document.querySelectorAll('.field-experience, .field-workplace, .field-profession, .field-country, .field-language');

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

function applyAgriExperienceLogic() {
  const agriStatus = document.getElementById('agri_business_experience_status').value;
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
function applyDependentsLogic() {
  const hasDependentsStatus = document.getElementById('has_dependents').value;
  // This selects BOTH containers
  const dependentFields = document.querySelectorAll('.field-number-of-dependents, .field-children-under-five');
  
  const numberofDependentsInput = document.getElementById('number_of_dependents');
  const childrenUnderFiveInput = document.getElementById('children_under_five');

  if (hasDependentsStatus === '1') {
    // Both become VISIBLE
    dependentFields.forEach(field => field.classList.remove('d-none'));
    
    // ONLY this one becomes REQUIRED
    numberofDependentsInput?.setAttribute('required', 'required');
  } else {
    // Both become HIDDEN
    dependentFields.forEach(field => field.classList.add('d-none'));
    
    // Cleanup
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

  document.addEventListener('DOMContentLoaded', function () {
    // Step navigation — bound via data-step-action, no inline onclick
 document.getElementById('prevBtn').addEventListener('click', () => changeStep(-1));
document.getElementById('nextBtn').addEventListener('click', () => changeStep(1));

// Education level conditional logic — bound via addEventListener, no inline onchange
document.getElementById('educational_level').addEventListener('change', applyEducationLevelLogic);
document.getElementById('haveexp').addEventListener('change', applyExperienceLogic);
document.getElementById('physical_condition').addEventListener('change', applyPhysicalConditionLogic);
document.getElementById('agri_business_experience_status').addEventListener('change', applyAgriExperienceLogic);
document.getElementById('has_dependents').addEventListener('change', applyDependentsLogic);

// Run once on load so pre-selected values (e.g. modal reopened) apply correctly
applyEducationLevelLogic();
applyExperienceLogic();
applyPhysicalConditionLogic();
applyAgriExperienceLogic();
applyDependentsLogic();
    // Reset wizard each time modal opens
    $('#jobseekerRegistrationModal').on('show.bs.modal', resetWizard);

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
})();