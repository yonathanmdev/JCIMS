<section class="content">
  <div class="container-fluid">
    <div class="card card-primary card-outline">
      <!-- action መነሻው report-registration ቢሆንም በጃቫስክሪፕት ይቀየራል -->
      <form action="" method="POST" target="_blank" id="reportForm">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        
        <div class="card-header bg-white">
          <!-- 💡 ማሻሻያ፦ አሰላለፉን በ Row እና በምንጭነት በመጠቀም ወደ ጎን አደረግነው -->
          <div class="row align-items-end">
            
            <!-- 🟢 አዲስ የተጨመረ፦ የቅርንጫፍ/መዋቅር መምረጫ -->
            <div class="col-md-5 form-group mb-2 mb-md-0">
              <label for="branchSelect" class="mb-1">
                <small class="font-weight-bold">የመዋቅር/ቅርንጫፍ ደረጃ ይምረጡ</small>
              </label>
             <select class="form-control select2-branch" id="branchSelect" name="branch_id" required style="width: 100%;">
    <?php if (empty($branches)): ?>
        <option value="" disabled selected>ምንም የመዋቅር ስም ዝርዝር አልተገኘም</option>
    <?php else: ?>
        <option value="" disabled selected>-- ቅርንጫፍ ይምረጡ --</option>
        <?php foreach ($branches as $b): ?>
            <option value="<?= $b['internal_id']; ?>" data-level="<?= (int)$b['level']; ?>">
                <?= htmlspecialchars($b['name']); ?>
            </option>
        <?php endforeach; ?>
    <?php endif; ?>
</select>
            </div>

            <!-- የሪፖርት መምረጫ -->
            <div class="col-md-5 form-group mb-2 mb-md-0">
              <label for="reportSelect" class="mb-1">
                <small class="font-weight-bold">የምትፈልጉትን ሪፖርት ዓይነት ይምረጡ</small>
              </label>
              <select class="form-control primary" id="reportSelect" name="report_type" required>
                <option value="" selected="selected" disabled="disabled">-- ይምረጡ --</option>   
                <option value="ሠ1">የስራ ፈላጊዎች ምዝገባና ግንዛቤ ፈጠራ ሪፖርት  (ሠ1)</option>
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

            <!-- አሳይ ቁልፍ -->
            <div class="col-md-2 form-group mb-0">
              <button type="submit" class="btn btn-primary btn-block" id="submitBtn">
                <i class="fas fa-eye"></i> አሳይ
              </button>
            </div>

          </div>
        </div>
      </form>
      
      <div class="card-body">
         <p class="text-center text-muted">የሚፈልጉትን መዋቅር እና የሪፖርት ዓይነት መርጠው "አሳይ" የሚለውን ቁልፍ ሲጫኑ ሪፖርቱ በአዲስ ታብ ላይ ይከፈታል።</p>
      </div>
    </div>
  </div>
</section>

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
        
        // ፎርሙ በ POST እና በ target="_blank" መሠረት ሁለቱንም (branch_id እና report_type) ይዞ በአዲስ ታብ ይከፈታል
    });
});
function initBranchSelect2() {
    if (typeof $ === 'undefined' || typeof $.fn.select2 === 'undefined') {
        // jQuery or select2 not loaded yet — try again shortly
        setTimeout(initBranchSelect2, 50);
        return;
    }

    $('.select2-branch').select2({
        placeholder: '-- ቅርንጫፍ ይምረጡ --',
        allowClear: false,
        width: '100%',
        templateResult: formatBranchOption,
        templateSelection: formatBranchSelection,
        matcher: branchMatcher
    });

    function formatBranchOption(option) {
        if (!option.id) return option.text;
        const level = $(option.element).data('level') || 0;
        const indent = '&nbsp;&nbsp;&nbsp;&nbsp;'.repeat(level);
        const bullet = level > 0 ? '▪ ' : '';
        return $(`<span>${indent}${bullet}${$('<div>').text(option.text).html()}</span>`);
    }

    function formatBranchSelection(option) {
        return option.text;
    }

    function branchMatcher(params, data) {
        if ($.trim(params.term) === '') return data;
        if (typeof data.text === 'undefined') return null;
        if (data.text.toUpperCase().indexOf(params.term.toUpperCase()) > -1) {
            return data;
        }
        return null;
    }
}

initBranchSelect2();
</script>