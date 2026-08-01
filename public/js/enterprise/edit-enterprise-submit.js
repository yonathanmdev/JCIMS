document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('editEnterpriseForm');
  if (!form) return;

  const tinInput = document.getElementById('edit_tin_number');

  // Restrict TIN input to digits only as the user types
  tinInput.addEventListener('input', function () {
    this.value = this.value.replace(/[^0-9]/g, '');
  });

  // Helper to show/clear Bootstrap-style validation state
  function setInvalid(el, message) {
    el.classList.add('is-invalid');
    let feedback = el.parentElement.querySelector('.invalid-feedback');
    if (!feedback) {
      feedback = document.createElement('div');
      feedback.className = 'invalid-feedback';
      el.parentElement.appendChild(feedback);
    }
    feedback.textContent = message;
  }

  function clearInvalid(el) {
    el.classList.remove('is-invalid');
    const feedback = el.parentElement.querySelector('.invalid-feedback');
    if (feedback) feedback.textContent = '';
  }

  function requireText(el, message) {
    if (!el) return true; // element not present in DOM (e.g. association case)
    if (el.value.trim() === '') {
      setInvalid(el, message);
      return false;
    }
    clearInvalid(el);
    return true;
  }

  form.addEventListener('submit', function (e) {
    let isValid = true;

    // --- TIN number: required, digits only ---
    const tinValue = tinInput.value.trim();
    if (tinValue === '') {
      setInvalid(tinInput, 'የግብር መክፈያ መለያ ቁጥር ያስፈልጋል።');
      isValid = false;
    } else if (!/^\d+$/.test(tinValue)) {
      setInvalid(tinInput, 'እባክዎ ቁጥር ብቻ ያስገቡ።');
      isValid = false;
    } else {
      clearInvalid(tinInput);
    }

    // --- yeedget_dereja: required select ---
    if (!requireText(document.getElementById('edit_yeedget_dereja'), 'እባክዎ ይምረጡ።')) isValid = false;

    // --- initial_capital: required, must be a valid non-negative number ---
    const initialCapital = document.getElementById('edit_initial_capital');
    const capitalValue = initialCapital.value.trim();
    if (capitalValue === '' || isNaN(capitalValue) || Number(capitalValue) < 0) {
      setInvalid(initialCapital, 'እባክዎ ትክክለኛ የካፒታል መጠን ያስገቡ።');
      isValid = false;
    } else {
      clearInvalid(initialCapital);
    }

    // --- starting_capital_in_kind: now required ---
    if (!requireText(document.getElementById('edit_starting_capital_in_kind'), 'ይህ መስክ ያስፈልጋል።')) isValid = false;

    // --- yehabtu_mnch: required select ---
    if (!requireText(document.getElementById('edit_yehabtu_mnch'), 'እባክዎ ይምረጡ።')) isValid = false;

    // --- wektawi_yehabt_meten: required text ---
    if (!requireText(document.getElementById('edit_wektawi_yehabt_meten'), 'ይህ መስክ ያስፈልጋል።')) isValid = false;

    // --- yemrt_ayinet: required text ---
    if (!requireText(document.getElementById('edit_yemrt_ayinet'), 'ይህ መስክ ያስፈልጋል።')) isValid = false;

    // --- yemikerb_hager_weys_lewuch: required select ---
    if (!requireText(document.getElementById('edit_yemikerb'), 'እባክዎ ይምረጡ።')) isValid = false;

    // --- sector_id / subsector_id: only present & required when NOT an association ---
    if (!requireText(document.getElementById('edit_sector_id'), 'እባክዎ ዘርፍ ይምረጡ።')) isValid = false;
    if (!requireText(document.getElementById('edit_subsector_id'), 'እባክዎ ንዑስ ዘርፍ ይምረጡ።')) isValid = false;

    // --- yesra_mesk: now required ---
    if (!requireText(document.getElementById('edit_yesra_mesk'), 'ይህ መስክ ያስፈልጋል።')) isValid = false;

    if (!isValid) {
      e.preventDefault();
    }
  });
});