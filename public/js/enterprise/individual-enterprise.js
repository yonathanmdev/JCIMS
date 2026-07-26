document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('individualEnterpriseForm');
    if (!form) return;

    const yeaderejajetSelect = form.querySelector('#yeaderejajet_ayinet');
    const ngoGroup = form.querySelector('#ngo_id').closest('.form-group');
    const ngoSelect = form.querySelector('#ngo_id');

    const yetederegeSelect = form.querySelector('#yetederege_dgaf');
    const orgSupportGroup = form.querySelector('#org-selectsuport').closest('.form-group');
    const orgSupportSelect = form.querySelector('#org-selectsuport');
    const supportedByGroup = form.querySelector('#supported_by').closest('.form-group');
    const supportedByInput = form.querySelector('#supported_by');
    const supportedItemsGroup = form.querySelector('#supported_items').closest('.form-group');
    const supportedItemsInput = form.querySelector('#supported_items');

    function showError(inputElement, message) {
        clearError(inputElement);
        inputElement.classList.add('is-invalid');
        const feedback = document.createElement('div');
        feedback.className = 'invalid-feedback text-danger';
        feedback.innerText = message;
        inputElement.parentNode.appendChild(feedback);
    }

    function clearError(inputElement) {
        inputElement.classList.remove('is-invalid');
        const feedback = inputElement.parentNode.querySelector('.invalid-feedback');
        if (feedback) {
            feedback.remove();
        }
    }

    // Fiscal Year Date validation functions
    function getFiscalMinMax() {
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        const year = today.getFullYear();
        let fiscalStart = new Date(year, 6, 8); // month 6 = July (0-indexed)

        if (today < fiscalStart) {
            fiscalStart = new Date(year - 1, 6, 8);
        }

        return { min: fiscalStart, max: today };
    }

    function validateEstablishedDate($modal) {
        const $ethField = $modal.find('#eth_start_date');
        const $hiddenField = $modal.find('#established_date');
        const rawValue = $hiddenField.val();

        if (!rawValue) {
            showError($ethField[0], 'እባክዎ የተመሰረትበት ቀን ይምረጡ።');
            return false;
        }

        const picked = new Date(rawValue);
        picked.setHours(0, 0, 0, 0);

        if (isNaN(picked.getTime())) {
            showError($ethField[0], 'ልክ ያልሆነ የቀን ቅርጸት።');
            return false;
        }

        const { min, max } = getFiscalMinMax();

        if (picked > max) {
            showError($ethField[0], 'የተመሰረትበት ቀን ወደፊት ሊሆን አይችልም።');
            return false;
        }

        if (picked < min) {
            const minStr = String(min.getDate()).padStart(2, '0') + '/' +
                            String(min.getMonth() + 1).padStart(2, '0') + '/' +
                            min.getFullYear();
            showError($ethField[0], 'የተመሰረትበት ቀን ከ' + minStr + ' በኋላ መሆን አለበት።');
            return false;
        }

        clearError($ethField[0]);
        return true;
    }

    function handleFormLogic() {
        if (yeaderejajetSelect.value === 'NGO') {
            ngoGroup.style.display = 'block';
            ngoSelect.setAttribute('required', 'required');
        } else {
            ngoGroup.style.display = 'none';
            ngoSelect.removeAttribute('required');
            ngoSelect.value = '';
            clearError(ngoSelect);
        }

        const supportVal = yetederegeSelect.value;
        if (supportVal === '0') {
            orgSupportGroup.style.display = 'none';
            supportedByGroup.style.display = 'none';
            supportedItemsGroup.style.display = 'none';
            orgSupportSelect.removeAttribute('required');
            supportedByInput.removeAttribute('required');
            supportedItemsInput.removeAttribute('required');
            clearError(orgSupportSelect);
            clearError(supportedByInput);
            clearError(supportedItemsInput);
        } else if (supportVal === '1') {
            orgSupportGroup.style.display = 'block';
            orgSupportSelect.setAttribute('required', 'required');

            if (yeaderejajetSelect.value === 'NGO') {
                orgSupportGroup.style.display = 'none';
                supportedByGroup.style.display = 'none';
                supportedItemsGroup.style.display = 'block';
                orgSupportSelect.removeAttribute('required');
                supportedByInput.removeAttribute('required');
                supportedItemsInput.setAttribute('required', 'required');
                clearError(orgSupportSelect);
                clearError(supportedByInput);
            } else {
                orgSupportGroup.style.display = 'block';
                orgSupportSelect.setAttribute('required', 'required');

                let activeSupportedBy = supportedByInput;
                if (orgSupportSelect.value === 'beproject') {
                    supportedByInput.style.display = 'none';
                    supportedByInput.removeAttribute('required');
                    
                    if (!form.querySelector('#supported_by_ngo_select')) {
                        const ngoClone = ngoSelect.cloneNode(true);
                        ngoClone.id = 'supported_by_ngo_select';
                        ngoClone.name = 'supported_by';
                        supportedByInput.parentNode.appendChild(ngoClone);
                    }
                    const clonedNgo = form.querySelector('#supported_by_ngo_select');
                    clonedNgo.style.display = 'block';
                    clonedNgo.setAttribute('required', 'required');
                    activeSupportedBy = clonedNgo;
                    supportedByInput.value = '';
                } else {
                    supportedByInput.style.display = 'block';
                    supportedByInput.setAttribute('required', 'required');
                    const clonedNgo = form.querySelector('#supported_by_ngo_select');
                    if (clonedNgo) {
                        clonedNgo.style.display = 'none';
                        clonedNgo.removeAttribute('required');
                        clearError(clonedNgo);
                    }
                }

                supportedByGroup.style.display = 'block';
                supportedItemsGroup.style.display = 'block';
                supportedItemsInput.setAttribute('required', 'required');
            }
        }
    }

    yeaderejajetSelect.addEventListener('change', handleFormLogic);
    yetederegeSelect.addEventListener('change', handleFormLogic);
    orgSupportSelect.addEventListener('change', handleFormLogic);

    handleFormLogic();

    form.addEventListener('submit', function (e) {
        let isValid = true;

        const linkedEntityId = form.querySelector('.linked-entity-id');
        const enterpriseName = form.querySelector('#enterprise_name');
        const tinNumber = form.querySelector('#tin_number');
        const yeedgetDereja = form.querySelector('#yeedget_dereja');
        const initialCapital = form.querySelector('#initial_capital');
        const yehabtuMnch = form.querySelector('#yehabtu_mnch');
        const wektawiYehabtMeten = form.querySelector('#wektawi_yehabt_meten');
        const yemikerb = form.querySelector('#yemikerb_hager_weys_lewuch');

        // Clear all previous errors
        form.querySelectorAll('.form-control').forEach(el => clearError(el));

        if (!linkedEntityId.value.trim()) {
            showError(form.querySelector('.linked-entity-search'), 'እባክዎ የስራ ፈላጊ ስም ወይም መታወቂያ ይምረጡ!');
            isValid = false;
        }

        if (!enterpriseName.value.trim()) {
            showError(enterpriseName, 'ኢንተርፕራይዝ ስም ባዶ መሆን አይችልም!');
            isValid = false;
        }

        if (!/^\d+$/.test(tinNumber.value.trim())) {
            showError(tinNumber, 'የግብር መክፈያ መለያ ቁጥር (TIN) ቁጥር ብቻ መሆን አለበት!');
            isValid = false;
        }

        if (yeedgetDereja.value === '' || parseInt(yeedgetDereja.value) < 0 || parseInt(yeedgetDereja.value) > 6) {
            showError(yeedgetDereja, 'እባክዎ ትክክለኛ የእድገት ደረጃ ይምረጡ (ከ 0 እስከ 6)።');
            isValid = false;
        }

        if (isNaN(initialCapital.value) || initialCapital.value.trim() === '') {
            showError(initialCapital, 'መነሻ ካፒታል ቁጥር መሆን አለበት!');
            isValid = false;
        }

        if (yehabtuMnch.value === '' || parseInt(yehabtuMnch.value) < 0 || parseInt(yehabtuMnch.value) > 3) {
            showError(yehabtuMnch, 'እባክዎ ትክክለኛ የሃብቱ ምንጭ ይምረጡ (ከ 0 እስከ 3)።');
            isValid = false;
        }

        if (isNaN(wektawiYehabtMeten.value) || wektawiYehabtMeten.value.trim() === '') {
            showError(wektawiYehabtMeten, 'ወቅታዊ የሃብት መጠን ቁጥር መሆን አለበት!');
            isValid = false;
        }

        if (yemikerb.value !== 'ለሃገር ውስጥ' && yemikerb.value !== 'ለውጭ ሃገር') {
            showError(yemikerb, 'እባክዎ ምርቱ የሚቀርበውን ትክክለኛ አማራጭ ይምረጡ።');
            isValid = false;
        }

        // Validate Established Date
        const $modal = form.closest('.modal') ? $(form.closest('.modal')) : $(form);
        if (!validateEstablishedDate($modal)) {
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
            // 🛑 Tell the global submit handler to abort disabling buttons
            $(form).data('prevent-submit', true);
            
            // Re-enable buttons immediately so the user can fix errors and resubmit
            $(form).data('submitting', false);
            $(form).find('button[type="submit"], input[type="submit"]').prop('disabled', false);
        } else {
            // Clear the flag if validation passes successfully
            $(form).data('prevent-submit', false);
        }
    });
});