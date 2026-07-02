<section class="content">
  <div class="container-fluid">
    <div class="card card-primary card-outline">
      <!-- action መነሻው report-registration ቢሆንም በጃቫስክሪፕት ይቀየራል -->
      <form action="" method="POST" target="_blank" id="reportForm">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        
        <div class="card-header bg-white d-flex flex-column flex-md-row align-items-md-center">
          <div class="ml-md-auto d-flex align-items-end">
            
            <!-- የሪፖርት መምረጫ ብቻ -->
            <div class="form-group mb-0 mr-2">
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
            <div class="form-group mb-0">
              <button type="submit" class="btn btn-primary" id="submitBtn">
                <i class="fas fa-eye"></i> አሳይ
              </button>
            </div>

          </div>
        </div>
      </form>
      
      <div class="card-body">
         <p class="text-center text-muted">የሚፈልጉትን ሪፖርት መርጠው "አሳይ" የሚለውን ቁልፍ ሲጫኑ ሪፖርቱ በአዲስ ታብ ላይ ይከፈታል።</p>
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
        // ይህ ሲሆን አድራሻው በቀጥታ http://localhost/JCIMS/report-1 ይሆናል
        reportForm.action = `${BASE_URL}/report-${reportNumber}`;
        
        // ፎርሙ በ POST እና በ target="_blank" መሠረት በራሱ በአዲስ ታብ ላይ በንጽህና ይከፈታል
    });
});
</script>