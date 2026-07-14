document.addEventListener('DOMContentLoaded', function () {

    const orgType = document.getElementById('organization_type');
    const jobseekerCountInput = document.getElementById('jobseeker_count');
    const checklist = document.getElementById('jobseeker-checklist');
    const selectedCountEl = document.getElementById('selected-count');
    const searchInput = document.getElementById('jobseeker-search');
    const removedDataInput = document.getElementById('removed-jobseekers-data');
    const teamForm = document.getElementById('team-formation-form');

    const originalChecklistHTML = checklist ? checklist.innerHTML : '';
    let allItems = Array.from(document.querySelectorAll('.jobseeker-item'));

    let govDebounceTimer = null;
    let govAbortController = null;

    // Government-project-only state: currently shown ids + removal reasons.
    let govCurrentIds = [];
    let govRemovedReasons = {}; // { jobseeker_id: { name, reason } }

    function escapeHtml(str) {
        if (str === null || str === undefined) return '';
        const div = document.createElement('div');
        div.textContent = String(str);
        return div.innerHTML;
    }

    function getCheckedCount() {
        return document.querySelectorAll('input[name="selected_jobseekers[]"]:checked').length;
    }

    function getLimit() {
        if (orgType && orgType.value === 'government_project') {
            const val = parseInt(jobseekerCountInput.value, 10);
            return Number.isFinite(val) && val > 0 ? val : null;
        }
        return null;
    }

    function renderVisibleRows() {
        if (orgType && orgType.value === 'government_project') return;

        const term = searchInput?.value.toLowerCase().trim() || '';
        allItems.forEach(function (item) {
            const matchesSearch = !term || item.dataset.name.includes(term) || item.dataset.code.includes(term);
            item.style.display = matchesSearch ? '' : 'none';
        });
    }

    function updateSelectionState() {
        const checked = getCheckedCount();
        const limit = getLimit();

        selectedCountEl.textContent = limit ? `${checked} / ${limit}` : checked;

        document.querySelectorAll('input[name="selected_jobseekers[]"]').forEach(function (cb) {
            if (limit && !cb.checked && checked >= limit) {
                cb.disabled = true;
            } else if (orgType.value !== 'government_project') {
                cb.disabled = false;
            }
        });
    }

    function restoreOriginalChecklist() {
        checklist.innerHTML = originalChecklistHTML;
        allItems = Array.from(document.querySelectorAll('.jobseeker-item'));
        if (searchInput) searchInput.value = '';
        govCurrentIds = [];
        govRemovedReasons = {};
        syncRemovedDataField();
        renderVisibleRows();
    }

    function syncRemovedDataField() {
        if (removedDataInput) {
            removedDataInput.value = JSON.stringify(Object.values(govRemovedReasons));
        }
    }

    function renderGovRow(js) {
        return `
            <div class="form-check jobseeker-item d-flex align-items-center justify-content-between" data-id="${escapeHtml(js.id)}">
                <div>
                    <input class="form-check-input" type="checkbox" name="selected_jobseekers[]"
                           value="${escapeHtml(js.id)}" id="js_${escapeHtml(js.id)}" checked disabled>
                    <label class="form-check-label" for="js_${escapeHtml(js.id)}">
                        ${escapeHtml(js.job_seeker_id)} - ${escapeHtml(js.first_name)} ${escapeHtml(js.father_name)} ${escapeHtml(js.last_name)}
                    </label>
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger remove-gov-member"
                        data-id="${escapeHtml(js.id)}"
                        data-name="${escapeHtml(js.job_seeker_id + ' - ' + js.first_name + ' ' + js.father_name + ' ' + js.last_name)}"
                        title="አስወግድ">
                    <i class="fas fa-times"></i>
                </button>
            </div>`;
    }

    function fetchGovernmentProjectJobSeekers(limit) {
        if (govAbortController) govAbortController.abort();
        govAbortController = new AbortController();

        govCurrentIds = [];
        govRemovedReasons = {};
        syncRemovedDataField();

        checklist.innerHTML = `<div class="text-muted small p-2">በመፈለግ ላይ...</div>`;

        fetch(`${window.BASE_URL}/jobseekers-for-government-project?limit=${encodeURIComponent(limit)}`, {
            signal: govAbortController.signal
        })
            .then(res => res.json())
            .then(data => {
                if (!data.success || !data.results.length) {
                    checklist.innerHTML = `<div class="text-muted small p-2">ምንም ስራ ፈላጊ አልተገኘም</div>`;
                    selectedCountEl.textContent = `0 / ${limit}`;
                    return;
                }

                govCurrentIds = data.results.map(js => String(js.id));
                checklist.innerHTML = data.results.map(renderGovRow).join('');
                updateSelectionState();
            })
            .catch(err => {
                if (err.name === 'AbortError') return;
                console.error('Government project jobseeker fetch error:', err);
                checklist.innerHTML = `<div class="text-danger small p-2">መጫን አልተሳካም</div>`;
            });
    }

    // Fetch exactly one replacement, excluding everyone currently shown + everyone already removed.
    function fetchReplacementMember() {
        const excludeIds = govCurrentIds.concat(Object.keys(govRemovedReasons));
        const rowPlaceholder = document.createElement('div');
        rowPlaceholder.className = 'text-muted small p-2 gov-replacement-loading';
        rowPlaceholder.textContent = 'ምትክ በመፈለግ ላይ...';
        checklist.appendChild(rowPlaceholder);

        fetch(`${window.BASE_URL}/jobseekers-for-government-project?limit=1&exclude=${encodeURIComponent(excludeIds.join(','))}`)
            .then(res => res.json())
            .then(data => {
                rowPlaceholder.remove();

                if (!data.success || !data.results.length) {
                    const warn = document.createElement('div');
                    warn.className = 'text-warning small p-2';
                    warn.textContent = 'ምትክ ስራ ፈላጊ አልተገኘም';
                    checklist.appendChild(warn);
                    updateSelectionState();
                    return;
                }

                const replacement = data.results[0];
                govCurrentIds.push(String(replacement.id));
                checklist.insertAdjacentHTML('beforeend', renderGovRow(replacement));
                updateSelectionState();
            })
            .catch(err => {
                rowPlaceholder.remove();
                console.error('Replacement fetch error:', err);
            });
    }

    function handleRemoveClick(e) {
        const btn = e.target.closest('.remove-gov-member');
        if (!btn) return;

        const id = btn.dataset.id;
        const name = btn.dataset.name;

        Swal.fire({
            title: 'ለምን ማንሳት ፈለጉ?',
            input: 'textarea',
            inputPlaceholder: 'ምክንያት ያስገቡ...',
            html: `<div class="text-left small text-muted mb-2">${escapeHtml(name)}</div>`,
            showCancelButton: true,
            confirmButtonText: 'አስወግድ',
            cancelButtonText: 'ይቅር',
            inputValidator: (value) => !value?.trim() ? 'ምክንያት ያስፈልጋል' : undefined
        }).then((result) => {
            if (!result.isConfirmed) return;

            const reason = result.value.trim();
            govRemovedReasons[id] = { id, name, reason };
            syncRemovedDataField();

            govCurrentIds = govCurrentIds.filter(x => x !== id);
            const row = checklist.querySelector(`.jobseeker-item[data-id="${CSS.escape(id)}"]`);
            if (row) row.remove();

            fetchReplacementMember();
        });
    }

    if (orgType) {
        orgType.addEventListener('change', function () {
            const type = this.value;

            document.querySelectorAll('.org-type-field').forEach(function (field) {
                field.style.display = 'none';
                field.querySelectorAll('select, input').forEach(el => el.required = false);
            });

            if (govAbortController) govAbortController.abort();
            clearTimeout(govDebounceTimer);

            if (type === 'ngo') {
                document.getElementById('ngo-field-wrapper').style.display = '';
                document.getElementById('ngo_id').required = true;
                restoreOriginalChecklist();
                selectedCountEl.textContent = '0';

            } else if (type === 'government_project') {
                document.getElementById('govt-field-wrapper').style.display = '';
                jobseekerCountInput.required = true;
                document.getElementById('jobseeker-list-wrapper').style.display = '';
                if (searchInput) searchInput.style.display = 'none';
                checklist.innerHTML = '';
                govCurrentIds = [];
                govRemovedReasons = {};
                syncRemovedDataField();
                selectedCountEl.textContent = '0';

            } else if (type === 'family' || type === 'self_interest') {
                document.getElementById('jobseeker-list-wrapper').style.display = '';
                if (searchInput) searchInput.style.display = '';
                restoreOriginalChecklist();
                selectedCountEl.textContent = '0';

            } else {
                restoreOriginalChecklist();
                selectedCountEl.textContent = '0';
            }

            updateSelectionState();
        });
    }

    if (jobseekerCountInput) {
        jobseekerCountInput.addEventListener('input', function () {
            clearTimeout(govDebounceTimer);
            const n = parseInt(this.value, 10);

            if (!Number.isFinite(n) || n < 1) {
                checklist.innerHTML = '';
                govCurrentIds = [];
                govRemovedReasons = {};
                syncRemovedDataField();
                selectedCountEl.textContent = '0';
                return;
            }

            govDebounceTimer = setTimeout(() => fetchGovernmentProjectJobSeekers(n), 350);
        });
    }

    if (searchInput) {
        searchInput.addEventListener('keyup', renderVisibleRows);
    }

    if (checklist) {
        checklist.addEventListener('change', function (e) {
            if (e.target.name === 'selected_jobseekers[]') {
                updateSelectionState();
            }
        });
        checklist.addEventListener('click', handleRemoveClick);
    }

    if (teamForm) {
        teamForm.addEventListener('submit', syncRemovedDataField);
    }
});