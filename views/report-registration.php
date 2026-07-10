<?php $is_report_page = true; ?>
<section class="content">
    <div class="container-fluid">
        <div class="card card-primary card-outline shadow-sm">
            <!-- action is set dynamically via JavaScript -->
            <form action="" method="POST" target="_blank" id="reportForm">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                <div class="card-header bg-white py-3">
                    <div class="row align-items-end">
                        
                        <!-- Branch Selection -->
                        <div class="col-md-3">
                            <div class="form-group mb-0">
                                <label for="branchSearchInput" class="mb-1 text-dark">
                                    <small class="font-weight-bold"><i class="fas fa-code-branch mr-1"></i> መዋቅር/ቅርንጫፍ ይምረጡ</small>
                                </label>
                                <div class="branch-select-wrapper" style="position: relative;">
                                    <input type="text"
                                           class="form-control"
                                           id="branchSearchInput"
                                           placeholder="-- ፈልገው ይምረጡ --"
                                           autocomplete="off">
                                    <input type="hidden" id="branchSelect" name="branch_id">
                                    
                                    <div id="branchDropdownList"
                                         class="list-group shadow"
                                         style="position: absolute; z-index: 1050; width: 100%; max-height: 250px; overflow-y: auto; display: none; background: #fff; border: 1px solid #ced4da; border-radius: 4px; margin-top: 2px;">
                                    </div>
                                </div>
                            </div>
                        </div>

                        

                        <!-- Date Range: From -->
                        <div class="col-md-2">
                            <div class="form-group mb-0">
                                <label for="eth_start_date" class="mb-1 text-dark">
                                    <small class="font-weight-bold"><i class="fas fa-calendar-alt mr-1"></i> ከቀን</small>
                                </label>
                                <div class="input-group input-group-sm">
                                    <input type="text" 
                                           class="ethiopian-date form-control" 
                                           name="eth_start_date"
                                           id="eth_start_date" 
                                           data-rule="past"  
                                           data-gregorian="#start_date" 
                                           placeholder="ቀን/ወር/ዓ.ም" 
                                           readonly 
                                           style="background-color: #fff; cursor: pointer;">
                                    <input type="date" class="d-none" id="start_date" name="start_date">
                                </div>
                            </div>
                        </div>

                        <!-- Date Range: To -->
                        <div class="col-md-2">
                            <div class="form-group mb-0">
                                <label for="eth_end_date" class="mb-1 text-dark">
                                    <small class="font-weight-bold"><i class="fas fa-calendar-alt mr-1"></i> እስከ ቀን</small>
                                </label>
                                <div class="input-group input-group-sm">
                                    <input type="text" 
                                           class="ethiopian-date form-control" 
                                           name="eth_end_date"
                                           id="eth_end_date" 
                                           data-rule="past"  
                                           data-gregorian="#end_date" 
                                           placeholder="ቀን/ወር/ዓ.ም" 
                                           readonly 
                                           style="background-color: #fff; cursor: pointer;">
                                    <input type="date" class="d-none" id="end_date" name="end_date">
                                </div>
                            </div>
                        </div>
<!-- Report Type Selection -->
                        <div class="col-md-4">
                            <div class="form-group mb-0">
                                <label for="reportSelect" class="mb-1 text-dark">
                                    <small class="font-weight-bold"><i class="fas fa-file-alt mr-1"></i> የሪፖርት ዓይነት ይምረጡ</small>
                                </label>
                                <select class="form-control custom-select" id="reportSelect" name="report_type" required>
                                    <option value="" selected disabled>-- የሪፖርት ዓይነት ይምረጡ --</option>
                                    <option value="ሠ1">የስራ ፈላጊዎች ምዝገባና ግንዛቤ ፈጠራ ሪፖርት (ሠ1)</option>
                                    <option value="ሠ2">በከተማ አዲስ የተመሰረቱ ኢንተርፕራይዝ ሪፖርት (ሠ2)</option>
                                    <option value="ሠ3">በገጠር አዲስ የተመሰረቱ ኢንተርፕራይዝ ሪፖርት (ሠ3)</option>
                                    <option value="ሠ4">አጠቃላይ በከተማ የተፈጠረ የስራ እድል ሪፖርት (ሠ4)</option>
                                    <option value="ሠ5">አጠቃላይ በገጠር የተፈጠረ የስራ እድል ሪፖርት (ሠ5)</option>
                                    <option value="ሠ6">የከተማ የስራ እድል ሪፖርት በአንቀሳቃሾች (ሠ6)</option>
                                    <option value="ሠ7">የገጠር የስራ እድል ሪፖርት በአንቀሳቃሾች (ሠ7)</option>
                                    <option value="ሠ8">የከተማ የስራ እድል ሪፖርት በስራ ዓይነት (ሠ8)</option>
                                    <option value="ሠ9">የገጠር የስራ እድል ሪፖርት በስራ ዓይነት (ሠ9)</option>
                                    <option value="ሠ10">የስራ እድል ፈጠራ አካታችነት ሪፖርት በከተማና ገጠር (ሠ10)</option>
                                </select>
                            </div>
                        </div>
                        <!-- Action Button -->
                        <div class="col-md-1 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary btn-block shadow-sm" id="submitBtn">
                                አሳይ
                            </button>
                        </div>

                    </div>
                </div>
            </form>

            <div class="card-body bg-light border-top">
                <div class="d-flex align-items-center justify-content-center">
                    <i class="fas fa-info-circle text-muted mr-2"></i>
                    <p class="text-muted mb-0 small">የሚፈልጉትን መዋቅር፣ የሪፖርት ዓይነት እና የጊዜ ገደብ መርጠው "አሳይ" የሚለውን ቁልፍ ሲጫኑ ሪፖርቱ በአዲስ ታብ ላይ ይከፈታል።</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php if (!empty($branches)): ?>
<script nonce="<?php echo $GLOBALS['nonce']; ?>">
    window.ALL_BRANCHES = <?= json_encode(array_map(fn($b) => [
        'id'    => $b['internal_id'],
        'name'  => $b['name'],
        'level' => (int)$b['level'],
    ], $branches)) ?>;
</script>
<?php endif; ?>

<script nonce="<?php echo $GLOBALS['nonce']; ?>">
document.addEventListener('DOMContentLoaded', function() {
    const reportForm = document.getElementById('reportForm');
    const reportSelect = document.getElementById('reportSelect');

    reportForm.addEventListener('submit', function(e) {
        const reportType = reportSelect.value;
        
        // 1. መጀመሪያ ምርጫ መደረጉን ማረጋገጥ
        if (!reportType) {
            e.preventDefault();
            alert('እባክዎ መጀመሪያ ማየት የሚፈልጉትን ሪፖርት ይምረጡ!');
            return false;
        }

        // 2. 'ሠ' የሚለውን ፊደል አጥፍቶ ቁጥሯን ብቻ መለየት (ሠ1 -> 1)
        const reportNumber = reportType.replace('ሠ', ''); 

        // 3. የፎርሙን መሄጃ አድራሻ (action) በቁጥሩ መሠረት በዲናሚክ መቀየር
        reportForm.action = `${BASE_URL}/report-${reportNumber}`;
        location.reload();
        // ፎርሙ በ POST እና በ target="_blank" መሠረት ሁለቱንም (branch_id እና report_type) ይዞ በአዲስ ታብ ይከፈታል
    });
});
(function () {
    const searchInput = document.getElementById('branchSearchInput');
    const hiddenInput = document.getElementById('branchSelect');
    const dropdownList = document.getElementById('branchDropdownList');
    const branches = window.ALL_BRANCHES || [];

    function renderList(filterText) {
        const term = filterText.trim().toLowerCase();
        const filtered = term === ''
            ? branches
            : branches.filter(b => b.name.toLowerCase().includes(term));

        if (!filtered.length) {
            dropdownList.innerHTML = '<div class="list-group-item text-muted">ምንም አልተገኘም</div>';
            dropdownList.style.display = 'block';
            return;
        }

        dropdownList.innerHTML = filtered.map(b => {
            const indent = '&nbsp;&nbsp;&nbsp;&nbsp;'.repeat(b.level);
            const bullet = b.level > 0 ? '▪ ' : '';
            return `<button type="button" class="list-group-item list-group-item-action branch-option"
                        data-id="${b.id}" data-name="${b.name.replace(/"/g, '&quot;')}">
                        ${indent}${bullet}${b.name}
                    </button>`;
        }).join('');

        dropdownList.style.display = 'block';
    }

    searchInput.addEventListener('focus', function () {
        renderList(searchInput.value);
    });

    searchInput.addEventListener('input', function () {
        hiddenInput.value = ''; // clear selection while typing a new search
        renderList(searchInput.value);
    });

    dropdownList.addEventListener('click', function (e) {
        const btn = e.target.closest('.branch-option');
        if (!btn) return;

        searchInput.value = btn.dataset.name;
        hiddenInput.value = btn.dataset.id;
        dropdownList.style.display = 'none';
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function (e) {
        if (!e.target.closest('.branch-select-wrapper')) {
            dropdownList.style.display = 'none';
        }
    });
})();
</script>
