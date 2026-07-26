// jobseeker-search.js
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
            console.error('Job seeker search error:', err);
            resultsBox.classList.add('d-none');
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
            <div class="search-result-item" data-jobseeker-id="${escapeHtml(r.id)}">
                <div class="search-result-info" role="button" tabindex="0">
                    <span class="search-result-name">
                        ${escapeHtml(r.first_name)} ${escapeHtml(r.father_name)} ${escapeHtml(r.last_name)}
                    </span>
                    <span class="search-result-meta">
                        <span class="search-result-badge">${escapeHtml(r.job_seeker_id)}</span>
                        <span class="dot"></span>
                        ${escapeHtml(r.branch_name)}
                    </span>
                </div>
                <div class="search-result-actions">
                    <button type="button"
                            class="btn btn-outline-warning btn-sm edit-jobseeker-btn"
                            data-id="${escapeHtml(r.id)}"
                            title="አስተካክል">
                        <i class="fas fa-edit"></i>
                    </button>
                </div>
            </div>
        `).join('');

        resultsBox.classList.remove('d-none');
    }

    // Click handling within the dropdown itself.
    // NOTE: the edit button is intentionally NOT stopPropagation()'d here —
    // it must bubble up to document so the existing delegated handler in
    // jobseeker-form-validation.js ($(document).on('click', '.edit-jobseeker-btn', ...))
    // catches it and runs the real fetch/populate/modal flow. We only close
    // the search dropdown here as a side effect; we don't reimplement the edit logic.
    resultsBox.addEventListener('click', (e) => {
        const editBtn = e.target.closest('.edit-jobseeker-btn');
        if (editBtn) {
            closeSearchDropdown();
            return; // let the click continue bubbling to document
        }

        const infoArea = e.target.closest('.search-result-info');
        if (infoArea) {
            const item = infoArea.closest('.search-result-item');
            if (item) openJobseekerView(item.dataset.jobseekerId);
        }
    });

    resultsBox.addEventListener('keydown', (e) => {
        if (e.key !== 'Enter') return;
        const infoArea = e.target.closest('.search-result-info');
        if (!infoArea) return;
        const item = infoArea.closest('.search-result-item');
        if (item) openJobseekerView(item.dataset.jobseekerId);
    });

    function openJobseekerView(jobseekerId) {
        if (!jobseekerId) return;

        document.getElementById('jobseekerViewBody').innerHTML =
            '<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x"></i></div>';

        $('#jobseekerViewModal').modal('show');

        fetch(`${window.BASE_URL}/retrieve-jobseeker?jobseeker_id=${jobseekerId}`)
            .then(res => res.json())
            .then(data => {
                if (!data.success) {
                    document.getElementById('jobseekerViewBody').innerHTML =
                        `<p class="text-danger text-center">${data.message}</p>`;
                    return;
                }
                renderJobseekerView(data.jobseeker);
            })
            .catch(() => {
                document.getElementById('jobseekerViewBody').innerHTML =
                    '<p class="text-danger text-center">መረጃ መጫን አልተሳካም</p>';
            });

        closeSearchDropdown();
    }

    function closeSearchDropdown() {
        resultsBox.classList.add('d-none');
        input.value = '';
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