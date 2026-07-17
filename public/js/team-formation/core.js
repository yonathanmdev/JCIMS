window.TeamFormation = (function () {
    const orgType = document.getElementById('organization_type');
    const jobseekerCountInput = document.getElementById('jobseeker_count');
    const checklist = document.getElementById('jobseeker-checklist');       // gov-only tbody
    const selectedCountEl = document.getElementById('selected-count');
    const searchInput = document.getElementById('jobseeker-search');        // gov-only filter
    const removedDataInput = document.getElementById('removed-jobseekers-data');
    const teamForm = document.getElementById('team-formation-form');

    function escapeHtml(str) {
        if (str === null || str === undefined) return '';
        const div = document.createElement('div');
        div.textContent = String(str);
        return div.innerHTML;
    }

    function spinnerRow(message, extraClass = 'text-muted') {
        return `
            <tr>
                <td colspan="4" class="text-center p-3 ${extraClass}">
                    <div class="spinner-border spinner-border-sm mr-2" role="status" style="width:1rem;height:1rem;">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <span class="small">${escapeHtml(message)}</span>
                </td>
            </tr>`;
    }

    function messageRow(message, extraClass = 'text-muted') {
        return `<tr><td colspan="4" class="text-center small p-2 ${extraClass}">${escapeHtml(message)}</td></tr>`;
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

    // Gov-only: filters already-fetched rows inside #jobseeker-checklist.
    function renderGovVisibleRows() {
        const term = searchInput?.value.toLowerCase().trim() || '';
        checklist.querySelectorAll('.jobseeker-item').forEach(function (item) {
            const matchesSearch = !term
                || (item.dataset.name || '').includes(term)
                || (item.dataset.code || '').includes(term);
            item.style.display = matchesSearch ? '' : 'none';
        });
    }

    function syncRemovedDataField(govRemovedReasons) {
        if (removedDataInput) {
            removedDataInput.value = JSON.stringify(Object.values(govRemovedReasons));
        }
    }

    return {
        el: { orgType, jobseekerCountInput, checklist, selectedCountEl, searchInput, removedDataInput, teamForm },
        escapeHtml,
        spinnerRow,
        messageRow,
        getCheckedCount,
        getLimit,
        updateSelectionState,
        renderGovVisibleRows,
        syncRemovedDataField,
        gov: {},         // populated by government-project.js
        searchSelect: {}, // populated by search-select.js
    };
})();