<!-- የፊልተር ምርጫ ፎርም (Filter Form) - efficiency_statusy.php -->
<div class="card shadow-sm border-0 rounded-3 p-3 bg-white mb-4">
    <div class="card-body p-0">
        <form id="efficiencyForm" class="row g-2 align-items-end">
            
            <!-- የሚያዩት የአፈጻጸም ሁኔታ (efficiency_status) -->
            <div class="col-md-6">
                <label for="efficiency_status" class="form-label fw-bold text-dark mb-1" style="font-size: 0.85rem;">
                    የሚያዩት የአፈጻጸም ሁኔታ ይምረጡ፦
                </label>
                <select name="efficiency_status" id="efficiency_status" class="form-select form-select-sm" required>
                    <option value="">-- እባክዎ የአፈጻጸም ሁኔታ ይምረጡ --</option>
                    <option value="ምዝገባና ግንዛቤ">ምዝገባና ግንዛቤ</option>
                    <option value="የስራ እድል ፈጠራና ኢንተርፕራይዝ ምስረታ">የስራ እድል ፈጠራና ኢንተርፕራይዝ ምስረታ</option>
                    <option value="የባለሙያዎች የአፈጻጸም ሁኔታ">የባለሙያዎች የአፈጻጸም ሁኔታ</option>
                </select>
            </div>

            <!-- አዝራር (Button) - ከሳጥኑ ጋር ተጠግቶ እንዲቀመጥ -->
            <div class="col-md-3">
                <button type="button" id="searchBtn" class="btn btn-primary btn-sm px-4 shadow-sm py-1" style="height: 31px;">
                    <i class="fa-solid fa-filter me-1"></i> አሳይ / ፈልግ
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ስክሪፕት (ያለ ምንም ሪሎድ በአዲስ ታብ የሚከፍት) -->
<script nonce="<?php echo $GLOBALS['nonce'] ?? ''; ?>">
    document.getElementById('searchBtn').addEventListener('click', function(e) {
        e.preventDefault();
        
        const selectedValue = document.getElementById('efficiency_status').value;
        
        if (!selectedValue) {
            alert('እባክዎ የሚያዩትን የአፈጻጸም ሁኔታ ይምረጡ!');
            return;
        }
        
        let targetUrl = '';
        
        if (selectedValue === 'ምዝገባና ግንዛቤ') {
            targetUrl = 'performance_view';
        } else if (selectedValue === 'የስራ እድል ፈጠራና ኢንተርፕራይዝ ምስረታ') {
            targetUrl = 'performance_job_creation_view';
        } else if (selectedValue === 'የባለሙያዎች የአፈጻጸም ሁኔታ') {
            targetUrl = 'expert_level_view';
        }
        
        let finalUrl = targetUrl + '?efficiency_status=' + encodeURIComponent(selectedValue);
        
        window.open(finalUrl, '_blank');
    });
</script>