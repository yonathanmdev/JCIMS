document.addEventListener('DOMContentLoaded', function () {

    const orgType = document.getElementById('organization_type');
    const jobseekerCountInput = document.getElementById('jobseeker_count');
    const checklist = document.getElementById('jobseeker-checklist');
    const selectedCountEl = document.getElementById('selected-count');
    const searchInput = document.getElementById('jobseeker-search');

    // Cache the originally server-rendered rows (used by family / self_interest),
    // since the government_project path overwrites checklist.innerHTML with fetched rows.
    const originalChecklistHTML = checklist ? checklist.innerHTML : '';
    let allItems = Array.from(document.querySelectorAll('.jobseeker-item'));

    let govDebounceTimer = null;
    let govAbortController = null;

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

    // Client-side filter/limit — only used for family / self_interest,
    // where the full list is preloaded in the DOM.
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
                // Don't re-enable the auto-selected, server-limited checkboxes for government_project.
                cb.disabled = false;
            }
        });
    }

    // Restore the original preloaded checklist markup (used when leaving government_project).
    function restoreOriginalChecklist() {
        checklist.innerHTML = originalChecklistHTML;
        allItems = Array.from(document.querySelectorAll('.jobseeker-item'));
        if (searchInput) searchInput.value = '';
        renderVisibleRows();
    }

    function fetchGovernmentProjectJobSeekers(limit) {
        if (govAbortController) govAbortController.abort();
        govAbortController = new AbortController();

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

                checklist.innerHTML = data.results.map(js => `
                    <div class="form-check jobseeker-item">
                        <input class="form-check-input" type="checkbox" name="selected_jobseekers[]"
                               value="${escapeHtml(js.id)}" id="js_${escapeHtml(js.id)}" checked disabled>
                        <label class="form-check-label" for="js_${escapeHtml(js.id)}">
                            ${escapeHtml(js.job_seeker_id)} - ${escapeHtml(js.first_name)} ${escapeHtml(js.father_name)} ${escapeHtml(js.last_name)}
                        </label>
                    </div>
                `).join('');

                updateSelectionState();
            })
            .catch(err => {
                if (err.name === 'AbortError') return;
                console.error('Government project jobseeker fetch error:', err);
                checklist.innerHTML = `<div class="text-danger small p-2">መጫን አልተሳካም</div>`;
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
                if (searchInput) searchInput.style.display = 'none'; // search doesn't apply here — selection is server-driven
                checklist.innerHTML = '';
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

    // Government project: debounced fetch every time the count changes
    if (jobseekerCountInput) {
        jobseekerCountInput.addEventListener('input', function () {
            clearTimeout(govDebounceTimer);
            const n = parseInt(this.value, 10);

            if (!Number.isFinite(n) || n < 1) {
                checklist.innerHTML = '';
                selectedCountEl.textContent = '0';
                return;
            }

            govDebounceTimer = setTimeout(() => fetchGovernmentProjectJobSeekers(n), 350);
        });
    }

    // Search filter — only meaningful for family / self_interest (client-side preloaded list)
    if (searchInput) {
        searchInput.addEventListener('keyup', renderVisibleRows);
    }

    if (checklist) {
        checklist.addEventListener('change', function (e) {
            if (e.target.name === 'selected_jobseekers[]') {
                updateSelectionState();
            }
        });
    }
});
