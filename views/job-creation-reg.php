<?php $is_sra_edl_page = true; ?>
<section class="content">
  <div class="container-fluid">
    <div class="card card-default">
      <div class="card card-primary card-outline">
        <div class="card-body">
<div class="row mb-3">
    <div class="col-md-6">
    <h1 class="h3 mb-0 text-gray-800">የስራ እድል መመዝገቢያ </h1>
    </div>  
    <div class="container mt-4">
    <div class="card">
       
        <div class="card-body">
            <form action="index.php?controller=job&action=save" method="POST">
                <div class="row">
                    <!-- የመጀመሪያ ረድፍ -->
                    <div class="col-md-6 form-group">
                        <label>ስራ እድል የተፈጠረበት የስራ ፈላጊዉ መለያ ቁጥር *</label>
                        <input type="text" class="form-control" name="jid" placeholder="መለያ ቁጥር ያስገቡ" required>
                    </div>
                 <div class="col-md-6 form-group">
                    
    <label>ስራ የፈጠረዉ አካል *</label>
    <!-- id="org-select" ታክሏል፣ እና valueዎቹ ተስተካክለዋል -->
    <select class="form-control" name="org_type" id="org-select" required>
        <option value="" selected="selected" disabled="disabled">-- ይምረጡ --</option> 
        <option value="inorg">ከኢንተርፕራይዝ</option>
        <option value="outorg">ከኢንተርፕራይዝ ዉጭ</option>
    </select>
</div>
                </div>

                <div class="row mt-3">
                    <!-- ሁለተኛ ረድፍ -->
                    <div class="col-md-6 form-group">
                        <label>የተፈጠረዉ የስራ እድል *</label>
                        <select class="form-control" name="job_type" required>
                             <option value="" selected="selected" disabled="disabled">-- ይምረጡ --</option> 
                                   <option value="1">ቋሚ</option>
                                   <option value="2">ጊዜያዊ</option> 
                        </select>
                    </div>
         <div class="col-md-6">
        <div class="form-group">
            <label>የሥራ እድል የተፈጠረባቸው የሥራ ዓይነቶች *</label>
            <select class="form-control" name="job_category" id="secondDropdown" required> <!-- ይህ ID መኖሩን ያረጋግጡ -->
                <option value="">-- ይምረጡ --</option>
            </select>
        </div>
    </div>
                </div>

                <div class="row mt-3">
                    <!-- ሶስተኛ ረድፍ -->
                    <div class="col-md-6 form-group">
                         <div class="form-group" id="input-form">
                        <label>ኢንተርፕራይዝ ስም እና የግብር መክፈያ መለያ ቁጥር *</label>
                        <input type="text" class="form-control" name="enid" placeholder="ስም እና ቲን ቁጥር" required>
                    </div>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>የስራ ዘርፍ *</label>
                        <select class="form-control" name="sector" required>
                            <option value="" selected="selected" disabled="disabled">-- ይምረጡ --</option>
                        </select>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-6 form-group">
                        <label>ንዑስ ዘርፍ *</label>
                        <select class="form-control" name="sub_sector" required>
                            <option value="" selected="selected" disabled="disabled">-- ይምረጡ --</option>
                        </select>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">መዝግብ</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
</div> 
</div> 
</div> 
</section>
 
<script nonce="<?php echo htmlspecialchars($GLOBALS['nonce'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
function attachLiveSearch() {
    const input = document.getElementById("enterpriseSearch");
    // የፍለጋ አመክንዮው እዚህ ይኖራል...
    console.log("Live search attached!"); 
}

document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('org-select').addEventListener('change', showInputForm);
});
function showInputForm() {
    const orgSelect = document.getElementById("org-select");
    const inputForm = document.getElementById("input-form");
    const secondDropdown = document.getElementById("secondDropdown");
    const selectedValue = orgSelect.value;

    // ተጨማሪ ደህንነት፡ አካላቱ መኖራቸውን ማረጋገጥ
    if (!inputForm || !secondDropdown) {
        console.error("ስህተት፡ አስፈላጊ የሆኑ የቅጽ ክፍሎች አልተገኙም (input-form ወይም secondDropdown የሉም)");
        return;
    }
    // Reset dropdown
    secondDropdown.innerHTML = "";
    secondDropdown.disabled = true;

    // Add placeholder
    const defaultOption = new Option("-- ይምረጡ --", "");
    defaultOption.selected = true;
    defaultOption.disabled = true;
    secondDropdown.appendChild(defaultOption);

    // Define options
    let options = [];
    if (selectedValue === "inorg") {
        options = [
            "አዳዲስ ኢንተርፕራይዞች በማቋቋም የተፈጠረ ሥራ",
            "ነባር ኢንተርፕራይዞችን በማስፋፋት የተቀጠሩ"
        ];
    } else if (selectedValue === "outorg") {
        options = [
            "የግል ዘርፍ ኢንቭስትመንት/ድርጅቶች የተቀጠሩ",
            "በመንግስት ኢንተርፕራይዞች/ግዙፍ ፕሮጀክቶች የተቀጠሩ",
            "በህ/ስ/ማህበራት የተቀጠሩ",
            "መንግስታዊ ያልሆኑ ድርጅቶች ቅጥር",
            "በመንግስት መ/ቤቶች የተቀጠሩ",
            "የውጭ አገር ሥራ ስምሪት"
        ];
    }

    // Populate dropdown
    options.forEach(opt => secondDropdown.appendChild(new Option(opt, opt)));
    if (options.length > 0) secondDropdown.disabled = false;

    // Dynamic input form
    if (selectedValue === "inorg") {
        inputForm.innerHTML = `
            <label for="enterpriseSearch">ኢንተርፕራይዝ ስም እና የግብር መክፈያ መለያ ቁጥር *</label>
            <input type="text" id="enterpriseSearch" class="form-control" placeholder="ኢንተርፕራይዝ ስም እና ቲን ቁጥር" name="enid" required>
            <ul id="searchResults" class="list-group"></ul>`;
        attachLiveSearch();
    } else if (selectedValue === "outorg") {
        inputForm.innerHTML = `
            <label for="outorg-text">የመስሪያ ቤቱ ስም *</label>
            <input type="text" id="outorg-text" class="form-control" placeholder="የመስሪያ ቤቱ ስም" name="enid" required>`;
    } else {
        inputForm.innerHTML = "";
    }
}
 

</script>