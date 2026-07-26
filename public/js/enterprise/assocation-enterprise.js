// Helper functions for inline validation errors (Red text span)
function showFieldError($field, message) {
    const $formGroup = $field.closest('.form-group');
    let $error = $formGroup.find('.field-error-msg');
    
    if ($error.length === 0) {
        $error = $('<div class="field-error-msg text-danger small mt-1 font-weight-bold"></div>');
        // Append inside form group safely
        if ($field.attr('type') === 'hidden') {
            $formGroup.append($error);
        } else {
            $field.after($error);
        }
    }
    $error.text(message);
    $field.addClass('is-invalid');
}

function clearFieldError($field) {
    const $formGroup = $field.closest('.form-group');
    $formGroup.find('.field-error-msg').remove();
    $field.removeClass('is-invalid');
}

// Fiscal Year Date validation function for association modal
function getFiscalMinMaxAssoc() {
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    const year = today.getFullYear();
    let fiscalStart = new Date(year, 6, 8); // month 6 = July (0-indexed)

    if (today < fiscalStart) {
        fiscalStart = new Date(year - 1, 6, 8);
    }

    return { min: fiscalStart, max: today };
}

function validateEstablishedDateAssoc($modal) {
    const $ethField = $modal.find('#eth_start_date_assoc');
    const $hiddenField = $modal.find('#established_date_assoc');
    const rawValue = $hiddenField.val();

    if (!rawValue) {
        showFieldError($ethField, 'እባክዎ የተመሰረትበት ቀን ይምረጡ።');
        return false;
    }

    const picked = new Date(rawValue);
    picked.setHours(0, 0, 0, 0);

    if (isNaN(picked.getTime())) {
        showFieldError($ethField, 'ልክ ያልሆነ የቀን ቅርጸት።');
        return false;
    }

    const { min, max } = getFiscalMinMaxAssoc();

    if (picked > max) {
        showFieldError($ethField, 'የተመሰረትበት ቀን ወደፊት ሊሆን አይችልም።');
        return false;
    }

    if (picked < min) {
        const minStr = String(min.getDate()).padStart(2, '0') + '/' +
                        String(min.getMonth() + 1).padStart(2, '0') + '/' +
                        min.getFullYear();
        showFieldError($ethField, 'የተመሰረትበት ቀን ከ' + minStr + ' በኋላ መሆን አለበት።');
        return false;
    }

    clearFieldError($ethField);
    return true;
}

// Comprehensive Form Submit Validation for Association Form
$(document).on('submit', '#associationEnterpriseForm', function (event) {
    const $form = $(this);
    let isValid = true;

    // 1. linked-entity-id check (must not be null/empty)
    const $linkedEntityInput = $form.find('.linked-entity-id');
    const $linkedSearchInput = $form.find('.linked-entity-search');
    if (!$linkedEntityInput.val() || $linkedEntityInput.val().trim() === '') {
        showFieldError($linkedSearchInput, 'እባክዎ ከዝርዝር ውስጥ አባል/ቡድን ይምረጡ።');
        isValid = false;
    } else {
        clearFieldError($linkedSearchInput);
    }

    // 2. tin_number_assoc (number only)
    const $tin = $form.find('#tin_number_assoc');
    const tinVal = $tin.val().trim();
    if (!/^\d+$/.test(tinVal)) {
        showFieldError($tin, 'የግብር መክፈያ መለያ ቁጥር ቁጥሮች ብቻ መሆን አለበት።');
        isValid = false;
    } else {
        clearFieldError($tin);
    }

    // 3. yeedget_dereja_assoc (must be 0-6)
    const $dereja = $form.find('#yeedget_dereja_assoc');
    const derejaVal = $dereja.val();
    if (derejaVal === null || derejaVal === '' || !['0','1','2','3','4','5','6'].includes(derejaVal)) {
        showFieldError($dereja, 'እባክዎ ትክክለኛ የእድገት ደረጃ ይምረጡ።');
        isValid = false;
    } else {
        clearFieldError($dereja);
    }

    // 4. initial_capital_assoc (number, can accept fraction)
    const $capital = $form.find('#initial_capital_assoc');
    const capitalVal = $capital.val().trim();
    if (!/^\d+(\.\d+)?$/.test(capitalVal)) {
        showFieldError($capital, 'መነሻ ካፒታል ቁጥር ብቻ መሆን አለበት (ክፍልፋይ መቀበል ይችላል)።');
        isValid = false;
    } else {
        clearFieldError($capital);
    }

    // 5. yehabtu_mnch_assoc (must be 0-3)
    const $mnch = $form.find('#yehabtu_mnch_assoc');
    const mnchVal = $mnch.val();
    if (mnchVal === null || mnchVal === '' || !['0','1','2','3'].includes(mnchVal)) {
        showFieldError($mnch, 'እባክዎ ትክክለኛ የሃብት ምንጭ ይምረጡ።');
        isValid = false;
    } else {
        clearFieldError($mnch);
    }

    // 6. wektawi_yehabt_meten_assoc (number, can accept fraction)
    const $wektawi = $form.find('#wektawi_yehabt_meten_assoc');
    const wektawiVal = wektawiValStr = $wektawi.val().trim();
    if (!/^\d+(\.\d+)?$/.test(wektawiVal)) {
        showFieldError($wektawi, 'ወቅታዊ የሃብት መጠን ቁጥር ብቻ መሆን አለበት።');
        isValid = false;
    } else {
        clearFieldError($wektawi);
    }

    // 7. yemikerb_hager_weys_lewuch_assoc (ለሃገር ውስጥ or ለውጭ ሃገር)
    const $mikerb = $form.find('#yemikerb_hager_weys_lewuch_assoc');
    const mikerbVal = $mikerb.val();
    if (mikerbVal !== 'ለሃገር ውስጥ' && mikerbVal !== 'ለውጭ ሃገር') {
        showFieldError($mikerb, 'እባክዎ ምርቱ የሚቀርበውን አቅጣጫ ይምረጡ።');
        isValid = false;
    } else {
        clearFieldError($mikerb);
    }

    // 8. yetederege_dgaf_assoc (0 or 1)
    const $dgaf = $form.find('#yetederege_dgaf_assoc');
    const dgafVal = $dgaf.val();
    if (dgafVal !== '0' && dgafVal !== '1') {
        showFieldError($dgaf, 'እባክዎ የተደረገ ድጋፍ መኖሩን ይምረጡ።');
        isValid = false;
    } else {
        clearFieldError($dgaf);
    }

    // 9. org_type_suport (must be one of the specified values if visible/required)
    const $orgSupport = $form.find('#org-selectsuport_assoc');
    const orgSupportVal = $orgSupport.val();
    const validOrgSupports = ['bemengst', 'bgelu', 'benterprise', 'beproject', 'belela'];
    if ($orgSupport.is(':visible') && !validOrgSupports.includes(orgSupportVal)) {
        showFieldError($orgSupport, 'እባክዎ ድጋፍ የተደረገበትን አካል ትክክለኛ ምርጫ ይምረጡ።');
        isValid = false;
    } else {
        clearFieldError($orgSupport);
    }

    // 10. Established Date validation check
    if (!validateEstablishedDateAssoc($form)) {
        isValid = false;
    }

    // If validation fails, block submission and prevent global button locking logic override
    if (!isValid) {
        event.preventDefault();
        event.stopImmediatePropagation();
        $form.data('prevent-submit', true);
        
        // Ensure buttons remain active/unlocked since validation failed
        $form.data('submitting', false);
        $form.find('button[type="submit"], input[type="submit"]').prop('disabled', false);
        
        return false;
    } else {
        $form.removeData('prevent-submit');
    }
});