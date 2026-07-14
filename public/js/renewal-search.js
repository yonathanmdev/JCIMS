document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('jobSeekerSearchInput');
    const resultsBox = document.getElementById('jobSeekerSearchResults');
    const spinner = document.getElementById('searchSpinner');

    if (!input || !resultsBox) return;

    let debounceTimer = null;
    let abortController = null;

    input.addEventListener('input', () => {
        const query = input.value.trim();
        clearTimeout(debounceTimer);

        if (query.length < 2) {
            resultsBox.classList.add('d-none');
            resultsBox.innerHTML = '';
            spinner.classList.add('d-none');
            return;
        }

        spinner.classList.remove('d-none');
        showLoadingState();
        debounceTimer = setTimeout(() => runSearch(query), 300);
    });

    function showLoadingState() {
        resultsBox.innerHTML = `
            <div class="search-loading-state">
                <span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>
                በመፈለግ ላይ... (Searching...)
            </div>`;
        resultsBox.classList.remove('d-none');
    }

    async function runSearch(query) {
        if (abortController) abortController.abort();
        abortController = new AbortController();

        try {
            const response = await fetch(
                `${window.BASE_URL}/job-seekers-renewal-search?q=${encodeURIComponent(query)}`,
                { signal: abortController.signal }
            );
            if (!response.ok) throw new Error('Search request failed');

            const data = await response.json();
            renderResults(data.results || []);
        } catch (err) {
            if (err.name === 'AbortError') return;
            console.error('Renewal search error:', err);
            resultsBox.innerHTML = `
                <div class="search-empty-state">
                    <i class="fas fa-exclamation-triangle"></i>
                    ፍለጋ አልተሳካም (Search failed)
                </div>`;
            resultsBox.classList.remove('d-none');
        } finally {
            spinner.classList.add('d-none');
        }
    }

    function renderResults(results) {
        if (results.length === 0) {
            resultsBox.innerHTML = `
                <div class="search-empty-state">
                    <i class="fas fa-search"></i>
                    ምንም አልተገኘም (No results found)
                </div>`;
            resultsBox.classList.remove('d-none');
            return;
        }

        resultsBox.innerHTML = results.map(r => `
            <div class="search-result-item" data-jobseeker-id="${escapeHtml(r.id)}" role="button" tabindex="0">
                <span class="search-result-name">
                    ${escapeHtml(r.first_name)} ${escapeHtml(r.father_name)} ${escapeHtml(r.last_name)}
                </span>
                <span class="search-result-meta">
                    <span class="search-result-badge">${escapeHtml(r.job_seeker_id)}</span>
                    <span class="dot"></span>
                    ${escapeHtml(r.branch_name)}
                </span>
            </div>
        `).join('');

        resultsBox.classList.remove('d-none');
    }

    resultsBox.addEventListener('click', (e) => {
        const item = e.target.closest('.search-result-item');
        if (!item) return;
        openRenewalForm(item.dataset.jobseekerId);
    });

    resultsBox.addEventListener('keydown', (e) => {
        if (e.key !== 'Enter') return;
        const item = e.target.closest('.search-result-item');
        if (!item) return;
        openRenewalForm(item.dataset.jobseekerId);
    });

    function openRenewalForm(jobseekerId) {
        if (!jobseekerId) return;

        const form = document.getElementById('jobseekerForm');
        if (!form) {
            console.error('openRenewalForm: #jobseekerForm not found — is jobseeker-modal.php included on this page?');
            return;
        }

        fetch(`${window.BASE_URL}/jobseeker-renewal-data?job_seeker_id=${encodeURIComponent(jobseekerId)}`)
            .then(res => res.json())
            .then(function (data) {
                if (!data.success) {
                    Swal.fire({ icon: 'error', title: data.message || 'ስራ ፈላጊ አልተገኘም' });
                    return;
                }

                form.reset();

                document.getElementById('formMode').value = 'renewal';
                document.getElementById('jobseekerIdField').value = jobseekerId;
                document.getElementById('jobseekerModalTitle').innerHTML =
                    '<i class="fas fa-user-clock mr-1"></i> የስራ ፈላጊ እድሳት ፎርም';

                const submitBtn = document.getElementById('submitBtn');
                submitBtn.textContent = 'እድስ';
                submitBtn.classList.remove('btn-success', 'btn-warning');
                submitBtn.classList.add('btn-info');

                populateFormFromData(form, data.jobseeker);

                resultsBox.classList.add('d-none');
                input.value = '';

                $('#jobseekerRegistrationModal').modal('show');
                $('#jobseekerRegistrationModal').one('shown.bs.modal', function () {

                });
            })
            .catch((err) => {
                console.error('openRenewalForm error:', err);
                Swal.fire({ icon: 'error', title: 'መረጃ መጫን አልተሳካም' });
            });
    }

    function escapeHtml(str) {
        if (str === null || str === undefined) return '';
        const div = document.createElement('div');
        div.textContent = String(str);
        return div.innerHTML;
    }

    document.addEventListener('click', (e) => {
        if (!input.contains(e.target) && !resultsBox.contains(e.target)) {
            resultsBox.classList.add('d-none');
        }
    });
});