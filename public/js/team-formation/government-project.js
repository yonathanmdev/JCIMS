(function (TF) {
    const { checklist, removedDataInput } = TF.el;
    const { escapeHtml, spinnerRow, messageRow, updateSelectionState } = TF;

    let govDebounceTimer = null;
    let govAbortController = null;
    let govCurrentIds = [];
    let govRemovedReasons = {}; // { jobseeker_id: { id, name, reason, replaced_by } }

    function syncRemovedDataField() {
        TF.syncRemovedDataField(govRemovedReasons);
    }

    function reset() {
        govCurrentIds = [];
        govRemovedReasons = {};
        syncRemovedDataField();
    }

    function renderGovRow(js) {
        const fullName = `${js.first_name} ${js.father_name} ${js.last_name}`;
        return `
            <tr class="jobseeker-item"
                data-id="${escapeHtml(js.id)}"
                data-name="${escapeHtml(fullName.toLowerCase())}"
                data-code="${escapeHtml(String(js.job_seeker_id).toLowerCase())}">
                <td>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="selected_jobseekers[]"
                               value="${escapeHtml(js.id)}" id="js_${escapeHtml(js.id)}" checked disabled>
                    </div>
                </td>
                <td><label class="mb-0" for="js_${escapeHtml(js.id)}">${escapeHtml(js.job_seeker_id)}</label></td>
                <td><label class="mb-0" for="js_${escapeHtml(js.id)}">${escapeHtml(fullName)}</label></td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-gov-member"
                            data-id="${escapeHtml(js.id)}"
                            data-name="${escapeHtml(js.job_seeker_id + ' - ' + fullName)}"
                            title="አስወግድ">
                        <i class="fas fa-times"></i>
                    </button>
                </td>
            </tr>`;
    }

    function fetchGovernmentProjectJobSeekers(limit) {
        if (govAbortController) govAbortController.abort();
        govAbortController = new AbortController();

        reset();
        checklist.innerHTML = spinnerRow('በመፈለግ ላይ...');

        fetch(`${window.BASE_URL}/jobseekers-for-government-project?limit=${encodeURIComponent(limit)}`, {
            signal: govAbortController.signal
        })
            .then(res => res.json())
            .then(data => {
                if (!data.success || !data.results.length) {
                    checklist.innerHTML = messageRow('ምንም ስራ ፈላጊ አልተገኘም');
                    TF.el.selectedCountEl.textContent = `0 / ${limit}`;
                    return;
                }

                govCurrentIds = data.results.map(js => String(js.id));
                checklist.innerHTML = data.results.map(renderGovRow).join('');
                updateSelectionState();
            })
            .catch(err => {
                if (err.name === 'AbortError') return;
                console.error('Government project jobseeker fetch error:', err);
                checklist.innerHTML = messageRow('መጫን አልተሳካም', 'text-danger');
            });
    }

    function fetchReplacementMember(removedId) {
        const excludeIds = govCurrentIds.concat(Object.keys(govRemovedReasons));
        const rowPlaceholder = document.createElement('tbody');
        rowPlaceholder.className = 'gov-replacement-loading';
        rowPlaceholder.innerHTML = spinnerRow('ምትክ በመፈለግ ላይ...');
        Array.from(rowPlaceholder.children).forEach(tr => checklist.appendChild(tr));

        fetch(`${window.BASE_URL}/jobseekers-for-government-project?limit=1&exclude=${encodeURIComponent(excludeIds.join(','))}`)
            .then(res => res.json())
            .then(data => {
                checklist.querySelectorAll('tr').forEach(tr => {
                    if (tr.querySelector('.spinner-border')) tr.remove();
                });

                if (!data.success || !data.results.length) {
                    checklist.insertAdjacentHTML('beforeend', messageRow('ምትክ ስራ ፈላጊ አልተገኘም', 'text-warning'));
                    updateSelectionState();
                    return;
                }

                const replacement = data.results[0];
                govCurrentIds.push(String(replacement.id));

                if (removedId && govRemovedReasons[removedId]) {
                    govRemovedReasons[removedId].replaced_by = replacement.id;
                    syncRemovedDataField();

                    const entry = govRemovedReasons[removedId];
                    const body = new URLSearchParams({
                        job_seeker_id: entry.id,
                        reason: entry.reason,
                        replaced_by_job_seeker_id: entry.replaced_by || ''
                    });

                    fetch(`${window.BASE_URL}/jobseekers-record-removal`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body
                    }).catch(err => console.error('Record removal failed:', err));
                }

                checklist.insertAdjacentHTML('beforeend', renderGovRow(replacement));
                updateSelectionState();
            })
            .catch(err => {
                checklist.querySelectorAll('tr').forEach(tr => {
                    if (tr.querySelector('.spinner-border')) tr.remove();
                });
                console.error('Replacement fetch error:', err);
            });
    }


    function handleRemoveClick(e) {
        const btn = e.target.closest('.remove-gov-member');
        if (!btn) return;

        const id = btn.dataset.id;
        const name = btn.dataset.name;

        Swal.fire({
            icon: 'warning',
            title: 'ማስጠንቀቂያ',
            html: `
                <div class="text-left small text-muted mb-2">${escapeHtml(name)}</div>
                <div class="alert alert-danger text-left small mb-3">
                    ይህን ስራ ፈላጊ ካስወገዱ ከአደረጃጀት ወረፋቸው ላይ ቋሚ በሆነ መልኩ ይወገዳሉ እና
                    ወደፊት በሚደረጉ ማናቸውም አደረጃጀቶች ላይ አይካተቱም። ይህ እርምጃ መልሶ
                    ሊቀለበስ አይችልም።
                </div>
                <label class="d-block text-left small font-weight-bold mb-1">ለምን ማንሳት ፈለጉ?</label>
            `,
            input: 'textarea',
            inputPlaceholder: 'ምክንያት ያስገቡ...',
            showCancelButton: true,
            confirmButtonText: 'አዎ፣ አስወግድ',
            cancelButtonText: 'ይቅር',
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            reverseButtons: true,
            focusCancel: true,
            inputValidator: (value) => !value?.trim() ? 'ምክንያት ያስፈልጋል' : undefined
        }).then((result) => {
            if (!result.isConfirmed) return;

            const reason = result.value.trim();
            govRemovedReasons[id] = { id, name, reason, replaced_by: null };
            syncRemovedDataField();

            govCurrentIds = govCurrentIds.filter(x => x !== id);
            const row = checklist.querySelector(`tr.jobseeker-item[data-id="${CSS.escape(id)}"]`);
            if (row) row.remove();

            fetchReplacementMember(id);
        });
    }

    function startFetch(limit) {
        clearTimeout(govDebounceTimer);
        govDebounceTimer = setTimeout(() => fetchGovernmentProjectJobSeekers(limit), 350);
    }

    function abortAndClear() {
        if (govAbortController) govAbortController.abort();
        clearTimeout(govDebounceTimer);
        checklist.innerHTML = '';
        reset();
    }

    function getRemovedReasons() { return govRemovedReasons; }

   checklist.addEventListener('click', handleRemoveClick);

    if (TF.el.searchInput) {
        TF.el.searchInput.addEventListener('keyup', TF.renderGovVisibleRows);
    }

    TF.gov = { startFetch, abortAndClear, reset, syncRemovedDataField, getRemovedReasons };
})(window.TeamFormation);