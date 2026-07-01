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
  const validators = {
    'name-only': {
      pattern: NAME_ONLY_PATTERN,
      message: 'ቁጥር፣ ልዩ ምልክት ወይም ክፍተት መጠቀም አይቻልም።'
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

    if (value !== '' && !validator.pattern.test(value)) {
      input.classList.add('is-invalid');
      input.classList.remove('is-valid');
      feedback.textContent = validator.message;
      input.setCustomValidity(validator.message);
      return false;
    }

    input.classList.remove('is-invalid');
    if (value !== '') input.classList.add('is-valid');
    input.setCustomValidity('');
    return true;
  }

  function sanitizeOnInput(input) {
    const type = input.getAttribute('data-validate');
    if (type === 'name-only') {
      const cleaned = input.value.replace(/[^\p{L}]/gu, '');
      if (cleaned !== input.value) {
        input.value = cleaned;
      }
    }
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