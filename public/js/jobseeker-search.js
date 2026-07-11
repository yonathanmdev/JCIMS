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

    // Click a result -> same view flow as .view-jobseeker-btn
    resultsBox.addEventListener('click', (e) => {
        const item = e.target.closest('.search-result-item');
        if (!item) return;
        openJobseekerView(item.dataset.jobseekerId);
    });

    resultsBox.addEventListener('keydown', (e) => {
        if (e.key !== 'Enter') return;
        const item = e.target.closest('.search-result-item');
        if (!item) return;
        openJobseekerView(item.dataset.jobseekerId);
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

        // Close the search dropdown once a result is selected
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