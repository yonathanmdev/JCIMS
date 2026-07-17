(function (TF) {
    const searchInput = document.getElementById('jobseeker-add-search');
    const resultsBox = document.getElementById('jobseeker-search-results');
    const selectedTable = document.getElementById('selected-jobseekers-table');
    const teamForm = document.getElementById('team-formation-form');
    const { escapeHtml, updateSelectionState } = TF;

    if (!searchInput || !resultsBox || !selectedTable || !teamForm) return;

    let searchDebounce = null;
    let searchAbort = null;

    function getSelectedIds() {
        return new Set(
            Array.from(selectedTable.querySelectorAll('tr.selected-row')).map(r => r.dataset.id)
        );
    }

    function ensurePlaceholderRow() {
        const hasRows = selectedTable.querySelectorAll('tr.selected-row').length > 0;
        let placeholder = document.getElementById('no-selected-row');
        if (!hasRows && !placeholder) {
            selectedTable.insertAdjacentHTML('afterbegin',
                `<tr id="no-selected-row"><td colspan="3" class="text-muted small text-center p-2">ምንም ስራ ፈላጊ አልተመረጠም</td></tr>`);
        } else if (hasRows && placeholder) {
            placeholder.remove();
        }
    }

    function renderResults(term) {
        if (!term || term.length < 2) {
            resultsBox.style.display = 'none';
            resultsBox.innerHTML = '';
            return;
        }

        clearTimeout(searchDebounce);
        searchDebounce = setTimeout(() => {
            if (searchAbort) searchAbort.abort();
            searchAbort = new AbortController();

            resultsBox.innerHTML = `
                <div class="list-group-item small text-muted">
                    <span class="spinner-border spinner-border-sm mr-1" style="width:0.8rem;height:0.8rem;"></span>
                    በመፈለግ ላይ...
                </div>`;
            resultsBox.style.display = 'block';

            fetch(`${window.BASE_URL}/jobseekers-search-for-organizing?term=${encodeURIComponent(term)}`, {
                signal: searchAbort.signal
            })
                .then(res => res.json())
                .then(data => {
                    if (!data.success) {
                        resultsBox.innerHTML = `<div class="list-group-item small text-danger">ፍለጋ አልተሳካም</div>`;
                        return;
                    }

                    const selectedIds = getSelectedIds();
                    const matches = (data.results || []).filter(js => !selectedIds.has(String(js.id)));

                    if (matches.length === 0) {
                        resultsBox.innerHTML = `<div class="list-group-item small text-muted">ውጤት አልተገኘም</div>`;
                        return;
                    }

                    resultsBox.innerHTML = matches.map(js => {
                        const fullName = `${js.first_name} ${js.father_name} ${js.last_name}`;
                        return `
                            <button type="button" class="list-group-item list-group-item-action small search-result-item"
                                    data-id="${escapeHtml(js.id)}"
                                    data-code="${escapeHtml(js.job_seeker_id)}"
                                    data-name="${escapeHtml(fullName)}">
                                <strong>${escapeHtml(js.job_seeker_id)}</strong> - ${escapeHtml(fullName)}
                            </button>`;
                    }).join('');
                })
                .catch(err => {
                    if (err.name === 'AbortError') return;
                    console.error('Job seeker search error:', err);
                    resultsBox.innerHTML = `<div class="list-group-item small text-danger">ፍለጋ አልተሳካም</div>`;
                });
        }, 300);
    }

    function addSelectedRow(uuid, code, name) {
        // Dynamically create the checkbox that actually gets submitted with the form.
        const hiddenCb = document.createElement('input');
        hiddenCb.type = 'checkbox';
        hiddenCb.name = 'selected_jobseekers[]';
        hiddenCb.value = uuid;
        hiddenCb.checked = true;
        hiddenCb.style.display = 'none';
        hiddenCb.dataset.code = code.toLowerCase();
        hiddenCb.dataset.name = name.toLowerCase();
        hiddenCb.dataset.displayCode = code;
        hiddenCb.dataset.displayName = name;
        hiddenCb.className = 'search-added-checkbox';
        teamForm.appendChild(hiddenCb);

        const row = document.createElement('tr');
        row.className = 'selected-row';
        row.dataset.id = uuid;
        row.innerHTML = `
            <td>${escapeHtml(code)}</td>
            <td>${escapeHtml(name)}</td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger remove-selected-jobseeker"
                        data-id="${escapeHtml(uuid)}" title="አስወግድ">
                    <i class="fas fa-times"></i>
                </button>
            </td>`;
        selectedTable.appendChild(row);
        ensurePlaceholderRow();
        updateSelectionState();
    }

    function removeSelectedRow(uuid) {
        const cb = teamForm.querySelector(`input.search-added-checkbox[value="${CSS.escape(uuid)}"]`);
        if (cb) cb.remove();

        const row = selectedTable.querySelector(`tr[data-id="${CSS.escape(uuid)}"]`);
        if (row) row.remove();

        ensurePlaceholderRow();
        updateSelectionState();
    }

    searchInput.addEventListener('keyup', function () {
        renderResults(this.value.trim());
    });

    document.addEventListener('click', function (e) {
        if (!resultsBox.contains(e.target) && e.target !== searchInput) {
            resultsBox.style.display = 'none';
        }
    });

    resultsBox.addEventListener('click', function (e) {
        const btn = e.target.closest('.search-result-item');
        if (!btn) return;

        addSelectedRow(btn.dataset.id, btn.dataset.code, btn.dataset.name);

        searchInput.value = '';
        resultsBox.style.display = 'none';
        resultsBox.innerHTML = '';
    });

    selectedTable.addEventListener('click', function (e) {
        const btn = e.target.closest('.remove-selected-jobseeker');
        if (!btn) return;
        removeSelectedRow(btn.dataset.id);
    });

    function reset() {
        teamForm.querySelectorAll('input.search-added-checkbox').forEach(cb => cb.remove());
        selectedTable.querySelectorAll('tr.selected-row').forEach(row => row.remove());
        ensurePlaceholderRow();
        searchInput.value = '';
        resultsBox.style.display = 'none';
        resultsBox.innerHTML = '';
        updateSelectionState();
    }

    ensurePlaceholderRow();

    TF.searchSelect = { reset };
})(window.TeamFormation);