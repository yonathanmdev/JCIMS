<section class="content">
  <div class="container-fluid">
    <div class="card card-primary card-outline">
      <form action="report-registration" method="POST" target="_blank" id="reportForm">
    
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
        
        <div class="card-header bg-white d-flex flex-column flex-md-row align-items-md-center">
          <div class="ml-md-auto d-flex align-items-end">
            
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

            <div class="form-group mb-0 mr-2">
              <label for="yearSelect" class="mb-1"><small class="font-weight-bold">ዓመተ ምህረት</small></label>
              <select class="form-control" id="yearSelect" name="year">
                <option value="">-- ሁሉንም --</option>
                <option value="2016">2016</option>
                <option value="2017">2017</option>
                <option value="2018">2018</option>
              </select>
            </div>

            <div class="form-group mb-0">
              <button type="submit" class="btn btn-primary" id="submitBtn">
                <i class="fas fa-eye"></i> አሳይ
              </button>
            </div>

          </div>
        </div>
      </form>
      
      <div class="card-body">
         <p class="text-center text-muted">የሚፈልጉትን ሪፖርት እና ዓመተ ምህረት መርጠው "አሳይ" የሚለውን ቁልፍ ሲጫኑ ሪፖርቱ በአዲስ ታብ ላይ ይከፈታል።</p>
      </div>
    </div>
  </div>
</section>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // ፎርሙ ሰብሚት ከመደረጉ በፊት የሪፖርት ዓይነት መመረጡን ጃቫስክሪፕቱ ያረጋግጣል
    $('#reportForm').submit(function(e) {
        var reportType = $('#reportSelect').val();
        if(!reportType) {
            e.preventDefault();
            alert('እባክዎ መጀመሪያ ማየት የሚፈልጉትን ሪፖርት ይምረጡ!');
        }
    });
});
</script>