(function (TF) {
    const { orgType, jobseekerCountInput, checklist, selectedCountEl, searchInput, teamForm } = TF.el;
    const { escapeHtml, restoreOriginalChecklist, updateSelectionState } = TF;

   function reEnableSubmit() {
    const $form = $(teamForm);
    $form.data('submitting', false);
    $form.find('button[type="submit"], input[type="submit"]').prop('disabled', false);
}
    function getSelectedJobSeekerMap() {
    const map = {};
    document.querySelectorAll('input[name="selected_jobseekers[]"]:checked').forEach(function (cb) {
        const row = cb.closest('.jobseeker-item');
        const code = row
            ? (row.dataset.code || '').trim().toLowerCase()
            : (cb.dataset.code || '').trim().toLowerCase();
        if (code) map[code] = cb.value;
    });
    return map;
}

function populateRoleSelects() {
    const checkedBoxes = document.querySelectorAll('input[name="selected_jobseekers[]"]:checked');
    const optionsHtml = ['<option value="">-- ይምረጡ --</option>'];

    checkedBoxes.forEach(function (cb) {
        const row = cb.closest('.jobseeker-item');
        const code = row
            ? (row.dataset.code || '').toUpperCase()
            : (cb.dataset.displayCode || cb.dataset.code || '').toUpperCase();
        const name = row
            ? (row.dataset.name || '').replace(/\b\w/g, c => c.toUpperCase())
            : (cb.dataset.displayName || cb.dataset.name || '');
        optionsHtml.push(`<option value="${escapeHtml(cb.value)}">${escapeHtml(code)} - ${escapeHtml(name)}</option>`);
    });

    document.querySelectorAll('.role-select').forEach(function (select) {
        select.innerHTML = optionsHtml.join('');
    });
}

if (orgType) {
    orgType.addEventListener('change', function () {
        const type = this.value;

        document.querySelectorAll('.org-type-field').forEach(function (field) {
            field.style.display = 'none';
            field.querySelectorAll('select, input').forEach(el => el.required = false);
        });

        TF.gov.abortAndClear();
        TF.searchSelect.reset();

        if (type === 'ngo') {
            document.getElementById('ngo-field-wrapper').style.display = '';
            document.getElementById('ngo_id').required = true;
            document.getElementById('jobseeker-search-add-wrapper').style.display = '';
            selectedCountEl.textContent = '0';

        } else if (type === 'government_project') {
            document.getElementById('govt-field-wrapper').style.display = '';
            jobseekerCountInput.required = true;
            document.getElementById('jobseeker-list-wrapper').style.display = '';
            selectedCountEl.textContent = '0';

        } else if (type === 'family' || type === 'self_interest' || type === 'special_case') {
            document.getElementById('jobseeker-search-add-wrapper').style.display = '';
            selectedCountEl.textContent = '0';

        } else {
            selectedCountEl.textContent = '0';
        }

        updateSelectionState();
    });
}

    if (jobseekerCountInput) {
        jobseekerCountInput.addEventListener('input', function () {
            const n = parseInt(this.value, 10);
            if (!Number.isFinite(n) || n < 1) {
                TF.gov.abortAndClear();
                selectedCountEl.textContent = '0';
                return;
            }
            TF.gov.startFetch(n);
        });
    }

    const teamDetailsError = document.getElementById('teamDetailsError');

    if (teamForm) {
    teamForm.addEventListener('submit', function (e) {
        e.preventDefault();
        e.stopPropagation();       // ← stops the global $(document).on('submit', 'form', ...) from ever firing
        e.stopImmediatePropagation(); // ← also blocks any other listeners on this same element

        if (!teamForm.checkValidity()) {
            teamForm.reportValidity();
            return;
        }

        TF.gov.syncRemovedDataField();

        const selectedCheckboxes = document.querySelectorAll('input[name="selected_jobseekers[]"]:checked');
        if (selectedCheckboxes.length === 0) {
            Swal.fire('ስህተት', 'እባክዎ ቢያንስ ሁለት ስራ ፈላጊዎች ይምረጡ', 'warning');
            return;
        }

        populateRoleSelects();
        $('#teamDetailsModal').modal('show');
    });
}

    document.getElementById('btn-confirm-team')?.addEventListener('click', function () {
        const requiredFields = ['place', 'asso_name', 'leaderid', 'co_name', 'finance_name', 'procurement_name', 'manager_phone'];
        const missing = requiredFields.filter(id => !document.getElementById(id).value.trim());

        if (missing.length > 0) {
            teamDetailsError.textContent = 'እባክዎ ሁሉንም መስኮች ይሙሉ';
            teamDetailsError.style.display = 'block';
            return;
        }

        const roleValues = [
            document.getElementById('leaderid').value,
            document.getElementById('co_name').value,
            document.getElementById('finance_name').value,
            document.getElementById('procurement_name').value,
        ];

        teamDetailsError.style.display = 'none';

        const submitBtn = this;
        const originalBtnHtml = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = `<span class="spinner-border spinner-border-sm mr-1" role="status"></span> በመላክ ላይ...`;

        const formData = new FormData(teamForm);
        
        // 2. Clear out any partial/disabled checkboxes from the auto-serialization
    formData.delete('selected_jobseekers[]');

    // 3. Manually collect EVERY selected checkbox from the DOM
    // This works for both SEARCH-ADDED inputs and GOVERNMENT PROJECT inputs
    const allCheckboxes = document.querySelectorAll('input[name="selected_jobseekers[]"]');
    allCheckboxes.forEach(cb => {
        // We add them if they are either 'checked' OR 'disabled' (for gov project)
        if (cb.checked || cb.disabled) {
            formData.append('selected_jobseekers[]', cb.value);
        }
    });
        formData.append('place', document.getElementById('place').value);
        formData.append('asso_name', document.getElementById('asso_name').value.trim());
        formData.append('leader_id', document.getElementById('leaderid').value);
        formData.append('co_id', document.getElementById('co_name').value);
        formData.append('finance_id', document.getElementById('finance_name').value);
        formData.append('procurement_id', document.getElementById('procurement_name').value);
        formData.append('manager_phone', document.getElementById('manager_phone').value.trim());

        // ADD HERE
for (const [key, value] of formData.entries()) {
    console.log(key, value);
}

        fetch(`${window.BASE_URL}/team-formation`, {
    method: 'POST',
    body: formData
})
.then(async (res) => {
    const text = await res.text();

    console.log('HTTP Status:', res.status);
    console.log('Server Response:', text);

    try {
        return JSON.parse(text);
    } catch (e) {
        throw new Error('Server did not return valid JSON.\n\n' + text);
    }
})
.then(data => {
    if (data.success) {
        $('#teamDetailsModal').modal('hide');

        Swal.fire({
            icon: 'success',
            title: 'ተሳክቷል',
            text: data.message || 'ቡድኑ በተሳካ ሁኔታ ተመዝግቧል',
            confirmButtonText: 'እሺ'
        }).then(() => {
            window.location.href = data.redirect || `${window.BASE_URL}/setting-up-team`;
        });

    } else {
        teamDetailsError.textContent = data.message || 'ችግር ተከስቷል';
        teamDetailsError.style.display = 'block';
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnHtml;
    }
})
.catch(err => {
    console.error('Team formation submit error:', err);

    Swal.fire({
        icon: 'error',
        title: 'Server Error',
        html: `<pre style="text-align:left;white-space:pre-wrap;">${err.message}</pre>`
    });

    teamDetailsError.textContent = 'ግንኙነት አልተሳካም';
    teamDetailsError.style.display = 'block';
    submitBtn.disabled = false;
    submitBtn.innerHTML = originalBtnHtml;
});
    });
})(window.TeamFormation);