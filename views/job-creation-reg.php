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
                  <form action="<?= rtrim($_ENV['BASE_URL'], '/') ?>/jobcreation-registration-process" method="POST">
                    <div class="row">
                      <!-- የመጀመሪያ ረድፍ -->
                      <div class="col-md-6 form-group position-relative">
                        <label>ስራ እድል የተፈጠረበት የስራ ፈላጊዉ መለያ ቁጥር *</label>
                        <input type="text" id="jid" name="jid" class="form-control" placeholder="መለያ ቁጥር ወይም ስም ያስገቡ..." autocomplete="off" required>
                        <!-- በከፊል ብራውዘር datalist ምትክ በሚያምር UI የተተካ Custom List -->
                        <div id="job-seeker-results" class="list-group position-absolute w-100 shadow-lg mt-1" style="z-index: 1050; max-height: 250px; overflow-y: auto; display: none;"></div>
                      </div>
                      <div class="col-md-6 form-group">
                        <label>ስራ የፈጠረዉ አካል *</label>
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
                          <select class="form-control" name="job_category" id="secondDropdown" required disabled>
                            <option value="">-- ይምረጡ --</option>
                          </select>
                        </div>
                      </div>
                    </div>

                    <div class="row mt-3">
                      <!-- ሶስተኛ ረድፍ -->
                      <div class="col-md-6 form-group">
                        <div class="form-group" id="input-form">
                          <label>ኢንተርፕራይዝ / የመስሪያ ቤት ስም *</label>
                          <input type="text" class="form-control" name="enid" placeholder="ስም ያስገቡ" required>
                        </div>
                      </div>
                      <div class="col-md-6 form-group">
                        <label>የስራ ዘርፍ *</label>
                        <select class="form-control" name="sector" id="sector_select" required>
                          <option value="" selected disabled>-- ይምረጡ --</option>
                          <?php if (!empty($sectors)){ ?>
                            <?php foreach ($sectors as $sector): ?>
                              <option value="<?php echo htmlspecialchars($sector['sectorid']); ?>">
                                <?php echo htmlspecialchars($sector['sector']); ?>
                              </option>
                            <?php endforeach; ?>
                          <?php } else { ?>
                            <option value="" disabled>የስራ ዘርፍ አልተገኘም</option>
                          <?php } ?>
                        </select>
                      </div>
                    </div>

                    <div class="row mt-3">
                      <div class="col-md-6 form-group">
                        <label>ንዑስ ዘርፍ *</label>
                        <select class="form-control" name="sub_sector" id="sub_sector_select" required>
                          <option value="" selected disabled>-- ይምረጡ --</option>
                        </select>
                      </div>
                      <div class="col-md-6 form-group">
                        <label> የተፈጠረለት የስራ መስክ*</label>
                        <input type="text" class="form-control" name="job_field" placeholder="የስራ መስክ ያስገቡ" required>
                      </div>
                    </div>

                    <div class="row">
                      <!-- 1. የስራ ፈጣሪ አካል ምርጫ -->
                      <div class="col-md-6 form-group">
                        <label>ስራ እዲፈጠርልት ድጋፍ ያደረገ ማን ነዉ *</label>
                        <select class="form-control" name="org_type_suport" id="org-selectsuport" required>
                          <option value="" selected disabled>-- ይምረጡ --</option> 
                          <option value="bemengst">በመንግስት</option>
                          <option value="bgelu">በግል</option>
                          <option value="benterprise">በኢንተርፕራይዝ</option>
                          <option value="beproject">በፕሮጀክት(Ngo)</option>
                          <option value="belela">በሌላ</option>
                        </select>
                      </div>

                      <!-- 2. የፕሮጀክት/NGO ምርጫ -->
                      <div class="col-md-6 form-group" id="project_ngo_container" style="display: none;">
                        <label>የፕሮጀክት/NGO ስም *</label>
                        <select class="form-control" name="pid" id="project_select">
                          <option value="" selected disabled>-- ፕሮጀክት ይምረጡ --</option>
                          <?php if(!empty($projectNgos)) { ?>
                            <?php foreach ($projectNgos as $ngo): ?>
                              <option value="<?php echo htmlspecialchars($ngo['pid']); ?>">
                                <?php echo htmlspecialchars($ngo['pname']); ?>
                              </option>
                            <?php endforeach; ?>
                          <?php } else { ?>
                            <option value="" disabled>የፕሮጀክት/NGO አልተገኙም</option>
                          <?php } ?>
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
  </div>
</section>

<script nonce="<?php echo htmlspecialchars($GLOBALS['nonce'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
  var baseUrl = "<?= htmlspecialchars(rtrim($_ENV['BASE_URL'] ?? '', '/'), ENT_QUOTES, 'UTF-8') ?>";

  // Live Search ለአዳዲስ (በሲስተሙ ለተመዘገቡ) ኢንተርፕራይዞች - የተሻሻለ UI/UX
  function attachLiveSearch() {
    const input = document.getElementById("enterpriseSearch");
    const resultsContainer = document.getElementById("searchResults");
    const hiddenIdInput = document.getElementById("selected_enterprise_id");

    if (!input || !resultsContainer) return;

    input.addEventListener("input", function() {
      const q = this.value.trim();
      if (q.length < 2) {
        resultsContainer.innerHTML = "";
        resultsContainer.style.display = "none";
        return;
      }

      fetch(baseUrl + '/get-enterprise-list?q=' + encodeURIComponent(q))
        .then(response => response.json())
        .then(data => {
          resultsContainer.innerHTML = "";
          if (data.length === 0) {
            resultsContainer.innerHTML = '<div class="list-group-item text-muted p-3">ምንም ኢንተርፕራይዝ አልተገኘም</div>';
            resultsContainer.style.display = "block";
            return;
          }

          data.forEach(item => {
            const a = document.createElement("a");
            a.href = "javascript:void(0);";
            a.className = "list-group-item list-group-item-action d-flex justify-content-between align-items-center py-2 px-3 border-bottom";
            a.style.cursor = "pointer";
            
            a.innerHTML = `
              <div>
                <strong>${item.enterprisename}</strong>
              </div>
              <span class="badge badge-info badge-pill">TIN: ${item.tine_number || 'የለውም'}</span>
            `;
            
            a.addEventListener("click", function(e) {
              e.preventDefault();
              input.value = `${item.enterprisename} - TIN: ${item.tine_number || 'N/A'}`;
              if (hiddenIdInput) hiddenIdInput.value = item.code003_id;
              resultsContainer.innerHTML = "";
              resultsContainer.style.display = "none";
            });
            
            resultsContainer.appendChild(a);
          });
          resultsContainer.style.display = "block";
        })
        .catch(err => {
          console.error("Enterprise search error:", err);
        });
    });

    document.addEventListener("click", function(e) {
      if (e.target !== input && !resultsContainer.contains(e.target)) {
        resultsContainer.innerHTML = "";
        resultsContainer.style.display = "none";
      }
    });
  }

  document.addEventListener('DOMContentLoaded', () => {
    const orgSelect = document.getElementById('org-select');
    const secondDropdown = document.getElementById('secondDropdown');

    orgSelect.addEventListener('change', handleOrgSelectChange);
    secondDropdown.addEventListener('change', handleSecondDropdownChange);
  });

  // 1. ስራ የፈጠረው አካል ሲቀየር
  function handleOrgSelectChange() {
    const orgSelect = document.getElementById("org-select");
    const inputForm = document.getElementById("input-form");
    const secondDropdown = document.getElementById("secondDropdown");
    const selectedValue = orgSelect.value;

    secondDropdown.innerHTML = "";
    secondDropdown.disabled = true;
    inputForm.innerHTML = ""; 

    const defaultOption = new Option("-- ይምረጡ --", "");
    defaultOption.selected = true;
    defaultOption.disabled = true;
    secondDropdown.appendChild(defaultOption);

    let options = [];
    if (selectedValue === "inorg") {
      options = [
        "አዳዲስ ኢንተርፕራይዞች በማቋቋም የተፈጠረ ሥራ",
        "ነባር ኢንተርፕራይዞችን በማስፋፋት የተቀጠሩ"
      ];
      secondDropdown.disabled = false;
    } else if (selectedValue === "outorg") {
      options = [ 
        "የግል ዘርፍ ኢንቨስትመንት/ድርጅቶች የተቀጠሩ",
        "በመንግስት ኢንተርፕራይዞች/ግዙፍ ፕሮጀክቶች የተቀጠሩ",
        "በህ/ስ/ማህበራት የተቀጠሩ",
        "መንግስታዊ ያልሆኑ ድርጅቶች ቅጥር",
        "በመንግስት መ/ቤቶች የተቀጠሩ",
        "የውጭ አገር ሥራ ስምሪት"
      ];
      secondDropdown.disabled = false;
      renderPlainTextInput("የመስሪያ ቤቱ ስም *", "የመስሪያ ቤቱ ስም ያስገቡ");
    }

    options.forEach(opt => secondDropdown.appendChild(new Option(opt, opt)));
  }

  // 2. የተፈጠረው የሥራ ዓይነት ሲመረጥ ያለው የተስተካከለ ሎጂክ
  function handleSecondDropdownChange() {
    const secondDropdown = document.getElementById("secondDropdown");
    const selectedValue = secondDropdown.value;

    if (selectedValue === "አዳዲስ ኢንተርፕራይዞች በማቋቋም የተፈጠረ ሥራ") {
      renderEnterpriseLiveSearch();
    } else if (selectedValue === "ነባር ኢንተርፕራይዞችን በማስፋፋት የተቀጠሩ") {
      renderPlainTextInput("የነባር ኢንተርፕራይዝ ስም *", "የነባር ኢንተርፕራይዝ ስም ያስገቡ");
    }
  }

  // የ Live Search መስክ ማሳያ (ለአዳዲስ ኢንተርፕራይዞች - የተስተካከለ ድዛይን)
  function renderEnterpriseLiveSearch() {
    const inputForm = document.getElementById("input-form");
    inputForm.className = "form-group position-relative";
    inputForm.innerHTML = `
      <label for="enterpriseSearch">የአዲሱ ኢንተርፕራይዝ ስም ወይም TIN ቁጥር ይፈልጉ *</label>
      <input type="text" id="enterpriseSearch" class="form-control" placeholder="የአዲሱ ኢንተርፕራይዝ ስም ወይም ቲን ያስገቡ..." autocomplete="off" required>
      <input type="hidden" id="selected_enterprise_id" name="enid">
      <div id="searchResults" class="list-group position-absolute w-100 shadow-lg mt-1" style="z-index: 1050; max-height: 250px; overflow-y: auto; display: none;"></div>`;
    attachLiveSearch();
  }

  // የጽሁፍ ማስገቢያ መስክ ማሳያ
  function renderPlainTextInput(labelText, placeholderText) {
    const inputForm = document.getElementById("input-form");
    inputForm.className = "form-group";
    inputForm.innerHTML = `
      <label for="outorg-text">${labelText}</label>
      <input type="text" id="outorg-text" class="form-control" placeholder="${placeholderText}" name="enid" required>`;
  }

  // Sector እና Sub-sector cascading
  document.getElementById('sector_select').addEventListener('change', function() {
    const sectorId = this.value;
    const subSectorSelect = document.getElementById('sub_sector_select');
    
    subSectorSelect.innerHTML = '<option value="" disabled selected>ይጫናል...</option>';

    if (sectorId) {
      fetch(baseUrl + '/get-sub-sectors?sector_id=' + encodeURIComponent(sectorId))
        .then(response => response.json())
        .then(data => {
          subSectorSelect.innerHTML = '<option value="" disabled selected>-- ይምረጡ --</option>';
          data.forEach(item => {
            let option = document.createElement('option');
            option.value = item.sub_sectorid;
            option.text = item.subsector;
            subSectorSelect.appendChild(option);
          });
        })
        .catch(error => {
          console.error('Error:', error);
          subSectorSelect.innerHTML = '<option value="">ስህተት ተፈጥሯል</option>';
        });
    }
  });

  // Support NGO container toggle
  document.addEventListener('DOMContentLoaded', function() {
    const orgSelect = document.getElementById('org-selectsuport');
    const projectContainer = document.getElementById('project_ngo_container');
    const projectSelect = document.getElementById('project_select');

    orgSelect.addEventListener('change', function() {
      const value = this.value;
      if (value === 'beproject') {
        projectContainer.style.display = 'block';
        projectSelect.setAttribute('required', 'required');
      } else {
        projectContainer.style.display = 'none';
        projectSelect.removeAttribute('required');
        projectSelect.value = "";
      }
    });
  });

  // Job Seeker Search - የተሻሻለ Custom Dropdown List UI
  const jidInput = document.getElementById('jid');
  const jobSeekerResults = document.getElementById('job-seeker-results');

  jidInput.addEventListener('input', function() {
    const q = this.value.trim();

    if(q.length >= 2) {
      fetch(baseUrl + '/get-job-seeker-route?q=' + encodeURIComponent(q))
        .then(res => res.json())
        .then(data => {
          jobSeekerResults.innerHTML = '';
          if (data.length === 0) {
            jobSeekerResults.innerHTML = '<div class="list-group-item text-muted p-3">ምንም ስራ ፈላጊ አልተገኘም</div>';
            jobSeekerResults.style.display = 'block';
            return;
          }

          data.forEach(item => {
            const a = document.createElement('a');
            a.href = 'javascript:void(0);';
            a.className = 'list-group-item list-group-item-action d-flex justify-content-between align-items-center py-2 px-3 border-bottom';
            a.style.cursor = 'pointer';

            const fullName = `${item.first_name || ''} ${item.father_name || ''} ${item.last_name || ''}`.trim();
            
            a.innerHTML = `
              <div>
                <strong>${fullName}</strong>
              </div>
              <span class="badge badge-primary badge-pill">ID: ${item.job_seeker_id}</span>
            `;

            a.addEventListener('click', function(e) {
              e.preventDefault();
              jidInput.value = item.job_seeker_id;
              jobSeekerResults.innerHTML = '';
              jobSeekerResults.style.display = 'none';
            });

            jobSeekerResults.appendChild(a);
          });
          jobSeekerResults.style.display = 'block';
        })
        .catch(err => {
          console.error("Job seeker search error:", err);
        });
    } else {
      jobSeekerResults.innerHTML = '';
      jobSeekerResults.style.display = 'none';
    }
  });

  document.addEventListener('click', function(e) {
    if (e.target !== jidInput && !jobSeekerResults.contains(e.target)) {
      jobSeekerResults.innerHTML = '';
      jobSeekerResults.style.display = 'none';
    }
  });
</script>