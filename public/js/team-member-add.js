// team-member-add.js
document.addEventListener('DOMContentLoaded', () => {
    const openBtn = document.getElementById('addMemberBtn');
    const modalEl = document.getElementById('addMemberModal');
    const input = document.getElementById('addMemberSearchInput');
    const resultsBox = document.getElementById('addMemberSearchResults');
    const membersTableBody = document.getElementById('membersTableBody');
    const membersCountBadge = document.getElementById('membersCountBadge');

    if (!openBtn || !modalEl || !input || !resultsBox) return;

    const teamId = openBtn.dataset.teamId; // int table_id, set on the button in the view

    let debounceTimer = null;
    let abortController = null;

    input.addEventListener('input', () => {
        const query = input.value.trim();
        clearTimeout(debounceTimer);

        if (query.length < 2) {
            resultsBox.innerHTML = '';
            return;
        }

        debounceTimer = setTimeout(() => runSearch(query), 300);
    });

    async function runSearch(query) {
        if (abortController) abortController.abort();
        abortController = new AbortController();

        try {
            const response = await fetch(
                `${window.BASE_URL}/job-seekers-search?q=${encodeURIComponent(query)}`,
                { signal: abortController.signal }
            );
            if (!response.ok) throw new Error('Search request failed');

            const data = await response.json();
            renderResults(data.results || []);
        } catch (err) {
            if (err.name === 'AbortError') return;
            console.error('Add-member search error:', err);
        }
    }

    function renderResults(results) {
        if (results.length === 0) {
            resultsBox.innerHTML = `<div class="text-muted text-center py-2">ምንም አልተገኘም</div>`;
            return;
        }

        resultsBox.innerHTML = results.map(r => `
            <div class="d-flex align-items-center justify-content-between border-bottom py-2">
                <div>
                    <div class="font-weight-bold">
                        ${escapeHtml(r.first_name)} ${escapeHtml(r.father_name)} ${escapeHtml(r.last_name)}
                    </div>
                    <small class="text-muted">${escapeHtml(r.job_seeker_id)} — ${escapeHtml(r.branch_name)}</small>
                </div>
                <button type="button"
                        class="btn btn-primary btn-sm add-member-pick-btn"
                        data-job-seeker-id="${escapeHtml(r.id)}">
                    <i class="fas fa-plus"></i> ጨምር
                </button>
            </div>
        `).join('');
    }

    resultsBox.addEventListener('click', (e) => {
        const btn = e.target.closest('.add-member-pick-btn');
        if (!btn) return;
        addMember(btn.dataset.jobSeekerId, btn);
    });

    async function addMember(jobSeekerId, triggerBtn) {
        triggerBtn.disabled = true;
        triggerBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

        try {
            const response = await fetch(`${window.BASE_URL}/add-team-member`, {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    team_id: teamId,
                    job_seeker_id: jobSeekerId,
                }),
            });

            if (!response.ok) throw new Error('HTTP error: ' + response.status);

            const data = await response.json();

            if (data.status === 'success') {
                appendMemberRow(data.member);
                triggerBtn.closest('.d-flex').remove();
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon: 'success', title: 'ተጨምሯል!', timer: 1200, showConfirmButton: false });
                }
            } else {
                triggerBtn.disabled = false;
                triggerBtn.innerHTML = '<i class="fas fa-plus"></i> ጨምር';
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon: 'error', title: 'ስህተት!', text: data.message || 'መጨመር አልተቻለም' });
                } else {
                    alert(data.message || 'መጨመር አልተቻለም');
                }
            }
        } catch (err) {
            console.error('Add member error:', err);
            triggerBtn.disabled = false;
            triggerBtn.innerHTML = '<i class="fas fa-plus"></i> ጨምር';
        }
    }

    function appendMemberRow(member) {
        if (!membersTableBody) return;

        // Remove the "no members yet" placeholder row if present
        const emptyRow = membersTableBody.querySelector('.empty-members-row');
        if (emptyRow) emptyRow.remove();

        const rowCount = membersTableBody.querySelectorAll('tr').length + 1;

        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${rowCount}</td>
            <td>${escapeHtml(member.job_seeker_id)}</td>
            <td>${escapeHtml([member.first_name, member.father_name, member.last_name].filter(Boolean).join(' '))}</td>
            <td>${escapeHtml(member.gender ?? '—')}</td>
            <td>${escapeHtml(member.phone_number ?? '—')}</td>
        `;
        membersTableBody.appendChild(tr);

        if (membersCountBadge) {
            membersCountBadge.textContent = String(rowCount);
        }
    }

    function escapeHtml(str) {
        if (str === null || str === undefined) return '';
        const div = document.createElement('div');
        div.textContent = String(str);
        return div.innerHTML;
    }

    // Clear search state each time the modal is closed
    $(modalEl).on('hidden.bs.modal', () => {
        input.value = '';
        resultsBox.innerHTML = '';
    });
});