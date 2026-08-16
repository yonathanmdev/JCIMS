<!-- የፊልተር ምርጫ ፎርም (Filter Form) - efficiency_statusy.php -->
<div class="card shadow-sm border-0 rounded-3 p-3 bg-white mb-4">
    <div class="card-body p-0">
        <form id="efficiencyForm" class="row g-2 align-items-end">
            
            <!-- የሚያዩት የአፈጻጸም ሁኔታ (efficiency_status) -->
            <div class="col-md-6">
                <label for="efficiency_status" class="form-label fw-bold text-dark mb-1" style="font-size: 0.85rem;">
                    የኮድ 003 ዝርዝር ለማየት፦
                </label>
                <select name="efficiency_status" id="efficiency_status"  class="form-control form-control-sm" required>
                    <option value="">-- የሚፈልጉትን ይምረጡ --</option>
                    <option value="ኮድ003አዲስ">አዲስ</option>
                    <!-- <option value="ኮድ003ነባር">ነባር</option> -->
                </select>
            </div>

            <!-- አዝራር (Button) - ከሳጥኑ ጋር ተጠግቶ እንዲቀመጥ -->
            <div class="col-md-3">
                <button type="button" id="searchBtn" class="btn btn-primary btn-sm px-4 shadow-sm py-1" style="height: 31px;">
                      አሳይ / ፈልግ
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
            alert('እባክዎ የሚያዩትን ኮድ 003 ይምረጡ!');
            return;
        }
        
        let targetUrl = '';

if (selectedValue === 'ኮድ003አዲስ') {
    targetUrl = '<?= rtrim($_ENV['BASE_URL'], '/') ?>/code003';
} else if (selectedValue === 'ኮድ003ነባር') {
    targetUrl = '<?= rtrim($_ENV['BASE_URL'], '/') ?>/pcode003_old';
} 
        
        let finalUrl = targetUrl + '?efficiency_status=' + encodeURIComponent(selectedValue);
        
        window.open(finalUrl, '_blank');
    });
</script>