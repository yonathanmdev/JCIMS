<?php
$title = $data['title'] ?? 'JCIMS - የፋይዳ መረጃ አስመዝግብ';
$baseUrl = rtrim($_ENV['BASE_URL'], '/');
?>

<section class="content py-4">
  <div class="container-fluid">
    <div class="row justify-content-center">
      <div class="col-xl-9 col-lg-10">

        <div class="card fayda-card shadow-sm border-0">

          <div class="card-header bg-white border-bottom py-4">
            <div class="d-flex align-items-center">
              <div class="fayda-header-icon mr-3">
                <i class="fas fa-id-card"></i>
              </div>
              <div>
                <h3 class="card-title font-weight-bold text-dark m-0">ምዝገባ እና እድሳት</h3>
                <p class="text-muted small mb-0 mt-2">የፋይዳ ብሔራዊ መታወቂያ በመጠቀም አዲስ ስራ ፈላጊ ይመዝገቡ ወይም የነባር ስራ ፈላጊ መረጃን ያድሱ</p>
              </div>
            </div>
          </div>

          <div class="card-body p-4">

            <!-- Mode Selection Tabs (3-way) -->
            <div class="fayda-toggle mb-4" role="tablist" aria-label="የምዝገባ አይነት ይምረጡ">
              <input type="radio" class="fayda-toggle-input" name="mode" id="mode-new" value="new" checked autocomplete="off">
              <label class="fayda-toggle-label" for="mode-new" role="tab">
                <i class="fas fa-user-plus"></i>
                <span>አዲስ ምዝገባ</span>
              </label>

              <input type="radio" class="fayda-toggle-input" name="mode" id="mode-renewal" value="renewal" autocomplete="off">
              <label class="fayda-toggle-label" for="mode-renewal" role="tab">
                <i class="fas fa-sync-alt"></i>
                <span>እድሳት</span>
              </label>

              <input type="radio" class="fayda-toggle-input" name="mode" id="mode-region-renewal" value="region-renewal" autocomplete="off">
              <label class="fayda-toggle-label" for="mode-region-renewal" role="tab">
                <i class="fas fa-map-marked-alt"></i>
                <span>እድሳት እንደ ክልል</span>
              </label>

              <div class="fayda-toggle-slider" id="toggle-slider" aria-hidden="true"></div>
            </div>

            <!-- NEW -->
            <div id="new-section" class="fayda-panel">
              <div class="fayda-info-box">
                <div class="fayda-info-icon">
                  <i class="fas fa-info-circle"></i>
                </div>
                <div class="flex-grow-1">
                  <h6 class="font-weight-bold text-dark mb-1">አዲስ ስራ ፈላጊ ለመመዝገብ</h6>
                  <p class="text-secondary small mb-3">ምንም ተጨማሪ መረጃ ሳያስፈልግ በቀጥታ ወደ ፋይዳ ብሔራዊ መታወቂያ ማረጋገጫ ይቀጥሉ።</p>
                  <a href="<?= $baseUrl ?>/index.php?action=fayda-redirect&registration_type=new"
                     class="btn fayda-btn-primary px-4">
                     ወደ ፋይዳ ማረጋገጫ ይቀጥሉ <i class="fas fa-arrow-right ml-2"></i>
                  </a>
                </div>
              </div>
            </div>

            <!-- RENEWAL: search by name or id, scoped to user's branch, live search -->
            <div id="renewal-section" class="fayda-panel" style="display:none;">

              <label class="small font-weight-bold text-muted mb-2 d-block" for="jobseeker-search">
                ስም ወይም መታወቂያ ቁጥር ይፈልጉ
              </label>

              <div class="fayda-search-wrap mb-3">
                <div class="fayda-search-box">
                  <i class="fas fa-search fayda-search-icon"></i>
                  <input type="text" id="jobseeker-search" class="fayda-search-input"
                         placeholder="ስም ወይም ቁጥር ይተይቡ..." autocomplete="off"
                         aria-describedby="search-results">
                  <div class="fayda-search-spinner" id="search-spinner">
                    <i class="fas fa-circle-notch fa-spin"></i>
                  </div>
                </div>
                <div id="search-results" class="fayda-results" role="listbox"></div>
              </div>

              <div id="selected-preview" class="fayda-preview-card mb-3" style="display:none;">
                <div class="fayda-preview-header">
                  <i class="fas fa-check-circle text-success mr-2"></i>
                  <span class="small font-weight-bold text-dark">የተመረጠ መዝገብ</span>
                </div>
                <div class="fayda-preview-body">
                  <div class="fayda-preview-item">
                    <span class="fayda-preview-label">ሙሉ ስም</span>
                    <span class="fayda-preview-value font-weight-bold" id="preview-name"></span>
                  </div>
                  <div class="fayda-preview-item">
                    <span class="fayda-preview-label">መታወቂያ ቁጥር</span>
                    <span class="fayda-preview-value" id="preview-id"></span>
                  </div>
                  <div class="fayda-preview-item">
                    <span class="fayda-preview-label">ቅርንጫፍ</span>
                    <span class="fayda-preview-value" id="preview-branch"></span>
                  </div>
                </div>
              </div>

              <form method="get" action="<?= $baseUrl ?>/index.php">
                <input type="hidden" name="action" value="fayda-redirect">
                <input type="hidden" name="registration_type" value="renewal">
                <input type="hidden" name="job_seeker_id" id="job_seeker_id" value="">

                <button type="submit" class="btn fayda-btn-primary px-4" id="continue-btn" disabled>
                  ወደ ፋይዳ ማረጋገጫ ይቀጥሉ <i class="fas fa-arrow-right ml-2"></i>
                </button>
              </form>
            </div>

            <!-- REGION RENEWAL: search by ID ONLY, system-wide (all branches), button-triggered -->
            <div id="region-renewal-section" class="fayda-panel" style="display:none;">

              <label class="small font-weight-bold text-muted mb-2 d-block" for="jobseeker-region-search">
                መታወቂያ ቁጥር/ስም ያስገቡ
              </label>
             <div class="fayda-alert-box mb-3" role="alert">
  <div class="fayda-alert-icon">
    <i class="fas fa-exclamation-triangle"></i>
  </div>
  <div class="flex-grow-1">
    <p class="text-secondary small mb-2">በዚህ ክፍል ማንኛውም ከዚህ በፊት የተመዘገበ ስራ ፈላጊ እንደ ክልል በመታወቂያ ቁጥር / ስም ፈልገው ማደስ ይችላሉ።</p>
    <div class="fayda-alert-message">
      <i class="fas fa-exclamation-circle mr-2"></i>
      <span>ስራ ፈላጊው አሁን ላይ በእናንተ ወረዳ/ማዕከል መሆኑን ያረጋግጡ። ሳያረጋግጡ ቢመዘግቡ ኃላፊነቱን ራስዎ ይወስዳሉ።</span>
    </div>
  </div>
</div>
              <div class="fayda-search-wrap mb-3">
                <div class="fayda-search-box">
                  <i class="fas fa-hashtag fayda-search-icon"></i>
                  <input type="text" id="jobseeker-region-search" class="fayda-search-input"
                         placeholder="መታወቂያ ቁጥር /ስም ያስገቡ..." autocomplete="off"
                         aria-describedby="region-search-results">
                  <button type="button" id="region-search-btn" class="fayda-search-btn" aria-label="ፈልግ">ፈልግ
                    <i class="fas fa-search"></i>
                  </button>
                </div>
                <div id="region-search-results" class="fayda-results" role="listbox"></div>
              </div>

              <div id="region-selected-preview" class="fayda-preview-card mb-3" style="display:none;">
                <div class="fayda-preview-header">
                  <i class="fas fa-check-circle text-success mr-2"></i>
                  <span class="small font-weight-bold text-dark">የተመረጠ መዝገብ</span>
                </div>
                <div class="fayda-preview-body">
                  <div class="fayda-preview-item">
                    <span class="fayda-preview-label">ሙሉ ስም</span>
                    <span class="fayda-preview-value font-weight-bold" id="region-preview-name"></span>
                  </div>
                  <div class="fayda-preview-item">
                    <span class="fayda-preview-label">መታወቂያ ቁጥር</span>
                    <span class="fayda-preview-value" id="region-preview-id"></span>
                  </div>
                  <div class="fayda-preview-item">
                    <span class="fayda-preview-label">ከዚህ ቀደም የተመዘገበበት</span>
                    <span class="fayda-preview-value" id="region-preview-branch"></span>
                  </div>
                </div>
              </div>

              <form method="get" action="<?= $baseUrl ?>/index.php">
                <input type="hidden" name="action" value="fayda-redirect">
                <input type="hidden" name="registration_type" value="region-renewal">
                <input type="hidden" name="job_seeker_id" id="region_job_seeker_id" value="">

                <button type="submit" class="btn fayda-btn-primary px-4" id="region-continue-btn" disabled>
                  ወደ ፋይዳ ማረጋገጫ ይቀጥሉ <i class="fas fa-arrow-right ml-2"></i>
                </button>
              </form>
            </div>

          </div>
        </div>

      </div>
    </div>
  </div>
</section>

<style nonce="<?= $GLOBALS['nonce'] ?? '' ?>">
  .fayda-alert-box {
  display: flex; align-items: flex-start; gap: 14px;
  background: #fdeceb; border: 1.5px solid #f1a9a4; border-radius: 12px;
  padding: 18px 20px;
}
.fayda-alert-icon {
  width: 38px; height: 38px; border-radius: 50%; flex-shrink: 0;
  background: #f8d3d0; color: #c0392b;
  display: flex; align-items: center; justify-content: center; font-size: 17px;
  animation: alertPulse 1.8s ease-in-out infinite;
}
@keyframes alertPulse {
  0%, 100% { box-shadow: 0 0 0 0 rgba(192, 57, 43, 0.35); }
  50% { box-shadow: 0 0 0 6px rgba(192, 57, 43, 0); }
}
.fayda-alert-message {
  display: flex; align-items: flex-start;
  background: #fff; border-left: 4px solid #c0392b; border-radius: 6px;
  padding: 10px 14px; margin-top: 4px;
  font-weight: 700; font-size: 0.88rem; color: #a52a1f;
}
.fayda-alert-message i { color: #c0392b; flex-shrink: 0; margin-top: 2px; }

  .fayda-card { border-radius: 14px; }

  .fayda-header-icon {
    width: 48px; height: 48px; border-radius: 12px;
    background: linear-gradient(135deg, #4e73df 0%, #3756c9 100%);
    color: #fff; display: flex; align-items: center; justify-content: center;
    font-size: 20px; flex-shrink: 0;
  }

  .fayda-toggle {
    position: relative;
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    background: #f1f3f9;
    border-radius: 10px;
    padding: 4px;
  }
  .fayda-toggle-input { position: absolute; opacity: 0; pointer-events: none; }
  .fayda-toggle-label {
    position: relative; z-index: 2;
    display: flex; align-items: center; justify-content: center; gap: 8px;
    padding: 10px 10px; margin: 0; border-radius: 8px;
    font-weight: 600; font-size: 0.85rem; color: #6c757d;
    cursor: pointer; transition: color 0.25s ease;
    user-select: none; text-align: center;
  }
  .fayda-toggle-input:checked + .fayda-toggle-label { color: #3756c9; }
  .fayda-toggle-slider {
    position: absolute; top: 4px; left: 4px;
    width: calc(33.333% - 5.33px); height: calc(100% - 8px);
    background: #fff; border-radius: 8px;
    box-shadow: 0 2px 6px rgba(55,86,201,0.18);
    transition: transform 0.28s cubic-bezier(.4,0,.2,1);
  }
  .fayda-toggle-input#mode-renewal:checked ~ .fayda-toggle-slider { transform: translateX(100%); }
  .fayda-toggle-input#mode-region-renewal:checked ~ .fayda-toggle-slider { transform: translateX(200%); }

  .fayda-panel { animation: fadeIn 0.25s ease; }
  @keyframes fadeIn { from { opacity: 0; transform: translateY(4px);} to { opacity: 1; transform: translateY(0);} }

  .fayda-info-box {
    display: flex; align-items: flex-start; gap: 14px;
    background: #f8f9fc; border: 1px solid #e9ecf5; border-radius: 12px;
    padding: 20px;
  }
  .fayda-info-icon {
    width: 38px; height: 38px; border-radius: 50%; flex-shrink: 0;
    background: #e7ecfc; color: #3756c9;
    display: flex; align-items: center; justify-content: center; font-size: 16px;
  }

  .fayda-btn-primary {
    background: linear-gradient(135deg, #4e73df 0%, #3756c9 100%);
    color: #fff; font-weight: 600; border: none; border-radius: 8px;
    padding: 10px 20px; font-size: 0.9rem;
    box-shadow: 0 2px 8px rgba(55,86,201,0.25);
    transition: all 0.2s ease;
  }
  .fayda-btn-primary:hover:not(:disabled) {
    color: #fff; transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(55,86,201,0.35);
  }
  .fayda-btn-primary:disabled { opacity: 0.5; cursor: not-allowed; box-shadow: none; }

  .fayda-search-wrap { position: relative; }
  .fayda-search-box {
    position: relative; display: flex; align-items: center;
    background: #fff; border: 1.5px solid #dde1ec; border-radius: 10px;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
  }
  .fayda-search-box:focus-within {
    border-color: #4e73df; box-shadow: 0 0 0 3px rgba(78,115,223,0.12);
  }
  .fayda-search-icon { color: #9aa1b5; margin: 0 12px; font-size: 14px; }
  .fayda-search-input {
    flex: 1; border: none; outline: none; padding: 11px 6px;
    font-size: 0.9rem; background: transparent; color: #2c3345;
  }
  .fayda-search-spinner {
    display: none; color: #4e73df; margin-right: 12px; font-size: 14px;
  }
  .fayda-search-spinner.active { display: block; }

  .fayda-search-btn {
    border: none; background: #4e73df; color: #fff;
    padding: 10px 16px; margin: 3px; border-radius: 7px;
    font-size: 0.85rem; cursor: pointer; transition: background 0.2s ease;
    display: flex; align-items: center; gap: 6px;
  }
  .fayda-search-btn:hover { background: #3756c9; }
  .fayda-search-btn:disabled { opacity: 0.5; cursor: not-allowed; }
  .fayda-search-btn.loading i { animation: spin 0.7s linear infinite; }
  @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }

  .fayda-results {
    position: absolute; top: calc(100% + 6px); left: 0; right: auto; z-index: 1050;
    width: 100%; background: #fff; border-radius: 10px;
    box-shadow: 0 8px 24px rgba(20,25,50,0.14);
    max-height: 340px; overflow-y: auto;
  }
  .fayda-result-item {
    display: block; width: 100%; text-align: left; border: none;
    background: #fff; padding: 9px 14px; cursor: pointer;
    border-bottom: 1px solid #f0f1f6; transition: background 0.15s ease;
  }
  .fayda-result-item:last-child { border-bottom: none; }
  .fayda-result-item:hover, .fayda-result-item:focus { background: #f5f7fd; }
  .fayda-result-name { color: #2c3345; font-weight: 700; font-size: 0.88rem; }
  .fayda-result-meta { color: #8a90a3; font-size: 0.78rem; display: block; margin-top: 2px; }
  .fayda-result-empty, .fayda-result-error {
    padding: 14px; text-align: center; font-size: 0.85rem; color: #9aa1b5;
  }
  .fayda-result-error { color: #e02424; }

  .fayda-preview-card {
    border: 1px solid #d9ecdf; background: #f6fcf8; border-radius: 12px; overflow: hidden;
  }
  .fayda-preview-header {
    padding: 10px 16px; background: #eaf8ee; border-bottom: 1px solid #d9ecdf;
    display: flex; align-items: center;
  }
  .fayda-preview-body { padding: 14px 16px; }
  .fayda-preview-item {
    display: flex; justify-content: space-between; align-items: center;
    padding: 6px 0; font-size: 0.88rem; border-bottom: 1px dashed #e3f0e6;
  }
  .fayda-preview-item:last-child { border-bottom: none; }
  .fayda-preview-label { color: #7c8697; }
  .fayda-preview-value { color: #1f2937; }
</style>
<script nonce="<?= $GLOBALS['nonce'] ?? '' ?>">
(function () {
    const modeNew           = document.getElementById('mode-new');
    const modeRenewal       = document.getElementById('mode-renewal');
    const modeRegionRenewal = document.getElementById('mode-region-renewal');

    const newSection           = document.getElementById('new-section');
    const renewalSection       = document.getElementById('renewal-section');
    const regionRenewalSection = document.getElementById('region-renewal-section');

    const modeInputs = [modeNew, modeRenewal, modeRegionRenewal];
    const panelsByValue = {
        'new': newSection,
        'renewal': renewalSection,
        'region-renewal': regionRenewalSection
    };

    function toggleMode() {
        const checked = modeInputs.find(input => input && input.checked);
        if (!checked) return;
        Object.entries(panelsByValue).forEach(([value, panel]) => {
            if (!panel) return;
            panel.style.display = (value === checked.value) ? 'block' : 'none';
        });
    }

    modeInputs.forEach(input => {
        if (input) input.addEventListener('change', toggleMode);
    });

    function escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    /* ---------- Existing RENEWAL: live search by name/id, own branch ---------- */
    (function initBranchRenewalSearch() {
        const searchInput   = document.getElementById('jobseeker-search');
        const resultsBox    = document.getElementById('search-results');
        const spinner       = document.getElementById('search-spinner');
        const jobSeekerId   = document.getElementById('job_seeker_id');
        const continueBtn   = document.getElementById('continue-btn');
        const preview       = document.getElementById('selected-preview');
        const previewName   = document.getElementById('preview-name');
        const previewId     = document.getElementById('preview-id');
        const previewBranch = document.getElementById('preview-branch');

        let debounceTimer = null;
        let activeRequest  = null;

        function clearSelection() {
            if (jobSeekerId) jobSeekerId.value = '';
            if (continueBtn) continueBtn.disabled = true;
            if (preview) preview.style.display = 'none';
        }

        function renderResults(results) {
            if (!resultsBox) return;
            resultsBox.innerHTML = '';

            if (results.length === 0) {
                resultsBox.innerHTML = '<div class="fayda-result-empty">ምንም ውጤት አልተገኘም</div>';
                return;
            }

            results.forEach(item => {
                const fullName = [item.first_name, item.father_name, item.last_name]
                    .filter(Boolean).join(' ');

                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'fayda-result-item';
                btn.setAttribute('role', 'option');
                btn.innerHTML =
                    '<span class="fayda-result-name">' + escapeHtml(fullName) + '</span>' +
                    '<span class="fayda-result-meta">መታወቂያ ቁጥር: ' + escapeHtml(String(item.job_seeker_id)) +
                    (item.branch_name ? ' &middot; ' + escapeHtml(item.branch_name) : '') + '</span>';

                btn.addEventListener('click', () => {
                    if (jobSeekerId) jobSeekerId.value = item.job_seeker_id;
                    if (previewName) previewName.textContent   = fullName;
                    if (previewId) previewId.textContent     = item.job_seeker_id;
                    if (previewBranch) previewBranch.textContent = item.branch_name || '—';
                    if (preview) preview.style.display = '';
                    if (continueBtn) continueBtn.disabled = false;
                    resultsBox.innerHTML = '';
                    if (searchInput) searchInput.value = fullName;
                });
                resultsBox.appendChild(btn);
            });
        }

        if (searchInput) {
            searchInput.addEventListener('input', function () {
                clearTimeout(debounceTimer);
                clearSelection();
                if (resultsBox) resultsBox.innerHTML = '';

                const q = this.value.trim();
                if (q.length < 2) {
                    if (spinner) spinner.classList.remove('active');
                    return;
                }

                debounceTimer = setTimeout(() => {
                    if (activeRequest) activeRequest.abort();
                    const controller = new AbortController();
                    activeRequest = controller;
                    if (spinner) spinner.classList.add('active');

                    fetch('<?= $baseUrl ?>/index.php?action=renewal-search-by-fayda&q=' + encodeURIComponent(q), {
                        signal: controller.signal
                    })
                        .then(r => r.json())
                        .then(data => renderResults(data.results || []))
                        .catch(err => {
                            if (err.name === 'AbortError') return;
                            if (resultsBox) resultsBox.innerHTML = '<div class="fayda-result-error">ፍለጋ አልተሳካም</div>';
                        })
                        .finally(() => {
                            if (spinner) spinner.classList.remove('active');
                        });
                }, 300);
            });
        }

        document.addEventListener('click', function (e) {
            if (searchInput && resultsBox && !searchInput.contains(e.target) && !resultsBox.contains(e.target)) {
                resultsBox.innerHTML = '';
            }
        });
    })();

    /* ---------- REGION RENEWAL: button-triggered, name OR id, system-wide ---------- */
    (function initRegionRenewalSearch() {
        const searchInput   = document.getElementById('jobseeker-region-search');
        const searchBtn     = document.getElementById('region-search-btn');
        const resultsBox    = document.getElementById('region-search-results');
        const jobSeekerId   = document.getElementById('region_job_seeker_id');
        const continueBtn   = document.getElementById('region-continue-btn');
        const preview       = document.getElementById('region-selected-preview');
        const previewName   = document.getElementById('region-preview-name');
        const previewId     = document.getElementById('region-preview-id');
        const previewBranch = document.getElementById('region-preview-branch');

        let activeRequest = null;

        function clearSelection() {
            if (jobSeekerId) jobSeekerId.value = '';
            if (continueBtn) continueBtn.disabled = true;
            if (preview) preview.style.display = 'none';
        }

        function renderResults(results) {
            if (!resultsBox) return;
            resultsBox.innerHTML = '';

            if (results.length === 0) {
                resultsBox.innerHTML = '<div class="fayda-result-empty">ምንም ውጤት አልተገኘም</div>';
                return;
            }

            results.forEach(item => {
                const fullName = [item.first_name, item.father_name, item.last_name]
                    .filter(Boolean).join(' ');

                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'fayda-result-item';
                btn.setAttribute('role', 'option');
                btn.innerHTML =
                    '<span class="fayda-result-name">' + escapeHtml(fullName) + '</span>' +
                    '<span class="fayda-result-meta">መታወቂያ ቁጥር: ' + escapeHtml(String(item.job_seeker_id)) +
                    (item.branch_name ? ' &middot; ' + escapeHtml(item.branch_name) : '') + '</span>';

                btn.addEventListener('click', () => {
                    if (jobSeekerId) jobSeekerId.value = item.job_seeker_id;
                    if (previewName) previewName.textContent   = fullName;
                    if (previewId) previewId.textContent     = item.job_seeker_id;
                    if (previewBranch) previewBranch.textContent = item.name || '—';
                    if (preview) preview.style.display = '';
                    if (continueBtn) continueBtn.disabled = false;
                    resultsBox.innerHTML = '';
                    if (searchInput) searchInput.value = fullName;
                });
                resultsBox.appendChild(btn);
            });
        }

        function runSearch() {
            if (!searchInput) return;
            const q = searchInput.value.trim();
            clearSelection();
            if (resultsBox) resultsBox.innerHTML = '';

            if (q.length < 2) {
                if (resultsBox) resultsBox.innerHTML = '<div class="fayda-result-error">እባክዎ ስም ወይም መታወቂያ ቁጥር ያስገቡ</div>';
                return;
            }

            if (activeRequest) activeRequest.abort();
            const controller = new AbortController();
            activeRequest = controller;

            if (searchBtn) { searchBtn.disabled = true; searchBtn.classList.add('loading'); }

            fetch('<?= $baseUrl ?>/index.php?action=renewal-search-by-fayda&scope=region&q=' + encodeURIComponent(q), {
                signal: controller.signal
            })
                .then(r => r.json())
                .then(data => renderResults(data.results || []))
                .catch(err => {
                    if (err.name === 'AbortError') return;
                    if (resultsBox) resultsBox.innerHTML = '<div class="fayda-result-error">ፍለጋ አልተሳካም</div>';
                })
                .finally(() => {
                    if (searchBtn) { searchBtn.disabled = false; searchBtn.classList.remove('loading'); }
                });
        }

        if (searchBtn) {
            searchBtn.addEventListener('click', runSearch);
        }
        if (searchInput) {
            // free text (name or id) — Enter triggers the same search as the button
            searchInput.addEventListener('input', function () {
                clearSelection();
                if (resultsBox) resultsBox.innerHTML = '';
            });
            searchInput.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    runSearch();
                }
            });
        }

        document.addEventListener('click', function (e) {
            if (searchInput && resultsBox &&
                !searchInput.contains(e.target) &&
                !resultsBox.contains(e.target) &&
                !(searchBtn && searchBtn.contains(e.target))) {
                resultsBox.innerHTML = '';
            }
        });
    })();
})();
</script