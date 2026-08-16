<?php $is_sra_edl_page = true; ?>
<section class="content">
  <div class="container-fluid">
    <div class="card card-default">
      <div class="card card-primary card-outline">
        <div class="card-body">
          <div class="row mb-3">
            <div class="col-md-12">
              <h1 class="h3 mb-0 text-gray-800">የኢ-መደበኛ ንግድ ተዘማርተዉ ወደ መደበያ ኢንተርፕራይዝ መቀየሪያ</h1>
            </div>  
          </div>

          <div class="container mt-2">
            <div class="card shadow-sm">
              <div class="card-body">
               
                <form action="informal-trade-update-process" method="POST">
                  
                  <!-- መለያ ቁጥር (ID) በ Hidden ተይዟል -->
                  <input type="hidden" name="id" value="<?php echo htmlspecialchars($tradeData['id'] ?? ''); ?>">

                  <!-- ================= 1. የግል መረጃ ================= -->
                  <h5 class="text-primary border-bottom pb-2 mb-3">1. የግል መረጃ</h5>
                  <div class="row">
                    <div class="col-md-6 form-group">
                      <label>ሙሉ ስም (ከነ አያት) *</label>
                      <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($tradeData['full_name'] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-3 form-group">
                      <label>ጾታ *</label>
                      <select name="gender" class="form-control" required>
                        <option value="" disabled>-- ይምረጡ --</option>
                        <option value="Male" <?php echo (isset($tradeData['gender']) && $tradeData['gender'] == 'Male') ? 'selected' : ''; ?>>ወንድ</option>
                        <option value="Female" <?php echo (isset($tradeData['gender']) && $tradeData['gender'] == 'Female') ? 'selected' : ''; ?>>ሴት</option>
                      </select>
                    </div>
                    <div class="col-md-3 form-group">
                      <label>ዕድሜ *</label>
                      <input type="number" name="age" class="form-control" min="15" max="65" value="<?php echo htmlspecialchars($tradeData['age'] ?? ''); ?>" required>
                    </div>
                  </div>

                  <div class="row mt-2">
                    <div class="col-md-4 form-group">
                      <label>National ID *</label>
                      <input type="text" name="nid" class="form-control" maxlength="16" minlength="16" required>
                    </div>
                  </div>
 
                  <!-- ================= 2. የሥራ ቦታ እና አዲስ የዘርፍ ሽግግር መረጃ ================= -->
                  <h5 class="text-primary border-bottom pb-2 mt-4 mb-3">3. የሥራ ቦታ እና አዲስ የዘርፍ ሽግግር መረጃ</h5>
                  
                  <div class="row">
                    <div class="col-md-6 form-group">
                      <label>ንግዱ የሚገኝበት አካባቢ *</label>
                      <select name="trade_area_type" class="form-control" required>
                          <option value="" disabled>-- ይምረጡ --</option>
                        <option value="1" <?php echo (isset($tradeData['trade_area_type']) && $tradeData['trade_area_type'] == '1') ? 'selected' : ''; ?>>ከተማ</option>
                        <option value="2" <?php echo (isset($tradeData['trade_area_type']) && $tradeData['trade_area_type'] == '2') ? 'selected' : ''; ?>>ገጠር</option>
                      </select>
                    </div>
                  </div>
 
                  <!-- ወደ መደበኛ ሲሸጋገር የሚመረጥበት አዲስ የሴክተር እና ንዑስ ሴክተር ክፍል -->
                  <div class="row mt-2">
                    <div class="col-md-6 form-group">
                      <label>የሚሸጋገረዉ አዲስ የስራ ዘርፍ *</label>
                      <select class="form-control" name="sector" id="sector_select" required>
                        <option value="" selected disabled>-- ይምረጡ --</option>
                        <?php if (!empty($sectors)){ ?>
                          <?php foreach ($sectors as $sector): ?>
                            <option value="<?php echo htmlspecialchars($sector['sectorid']); ?>">
                              <?php echo htmlspecialchars($sector['sector']); ?>
                            </option>
                          <?php endforeach; ?>
                        <?php }else{ ?>
                          <option value="" disabled>የስራ ዘርፍ አልተገኘም</option>
                        <?php } ?>
                      </select>
                    </div>
                    <div class="col-md-6 form-group">
                      <label>የሚሸጋገረዉ አዲስ ንዑስ ዘርፍ *</label>
                      <select class="form-control" name="sub_sector" id="sub_sector_select" required>
                        <option value="" selected disabled>-- ይምረጡ --</option>
                      </select>
                    </div>
                  </div>

                  <div class="row mt-2">
                    <div class="col-md-4 form-group">
                      <label>የሥራ መስክ *</label>
                      <input type="text" name="job_position" class="form-control" value="<?php echo htmlspecialchars($tradeData['job_position'] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-4 form-group">
                      <label> ኢንተርፕራይዝ የሆነበት አመት *</label>
                      <input type="number" name="start_year" class="form-control" min="1950" max="2030" value="<?php echo htmlspecialchars($tradeData['start_year'] ?? ''); ?>" required>
                    </div>
                  </div>

                  <!-- ================= 3. የድጋፍ መረጃ (Transition Support Section) ================= -->
                  <h5 class="text-primary border-bottom pb-2 mt-4 mb-3">3. ወደ መደበኛ ሲሸጋገር የተደረገ ድጋፍ</h5>

                  <div class="row mb-3">
                    <div class="col-md-6 form-group">
                        <label>ድጋፍ ተደርጓል ወይ? *</label>
                        <select name="has_support" id="has_support" class="form-control" required>
                            <option value="" disabled>-- ይምረጡ --</option>
                            <option value="no" selected>2. አልተደረገም</option>
                            <option value="yes">1. ተደርጓል</option>
                        </select>
                    </div>
                  </div>

                  <!-- ድጋፍ ተደርጓል 'አዎ' ሲባል የሚከፈት የድጋፍ ዓይነቶች እና መለኪያዎች መሙያ <div> -->
                  <div id="support_details_section" style="display: none;" class="border p-3 bg-light rounded mb-3">
                      <h6 class="text-success mb-3">የተደረጉ የድጋፍ ዓይነቶች እና መለኪያዎች (ብር/ቁጥር)</h6>

                      <!-- 1. የገንዘብ (የበረሃ ድጋፍ) ድጋፍ -->
                      <div class="row mb-2 align-items-center border-bottom pb-2">
                          <div class="col-md-4">
                              <div class="form-check">
                                  <input class="form-check-input support-checkbox" type="checkbox" name="support_types[]" value="financial" id="sup_financial">
                                  <label class="form-check-input-label" for="sup_financial">የእይት ድጋፍ በብር ተቀይሮ</label>
                              </div>
                          </div>
                          <div class="col-md-4">
                              <input type="number" step="0.01" name="financial_amount" class="form-control form-control-sm" placeholder="መጠኑ በብር (ብር)" disabled>
                          </div>
                      </div>

                      <!-- 2. የብድር አቅርቦት -->
                      <div class="row mb-2 align-items-center border-bottom pb-2">
                          <div class="col-md-4">
                              <div class="form-check">
                                  <input class="form-check-input support-checkbox" type="checkbox" name="support_types[]" value="loan" id="sup_loan">
                                  <label class="form-check-input-label" for="sup_loan">የብድር አቅርቦት</label>
                              </div>
                          </div>
                          <div class="col-md-4">
                              <input type="number" step="0.01" name="loan_amount" class="form-control form-control-sm" placeholder="መጠኑ በብር (ብር)" disabled>
                          </div>
                      </div>

                      <!-- 3. የማሽነሪ / ሉህ አቅርቦት -->
                      <div class="row mb-2 align-items-center border-bottom pb-2">
                          <div class="col-md-4">
                              <div class="form-check">
                                  <input class="form-check-input support-checkbox" type="checkbox" name="support_types[]" value="machinery" id="sup_machinery">
                                  <label class="form-check-input-label" for="sup_machinery">የማሽነሪ አቅርቦት</label>
                              </div>
                          </div>
                          <div class="col-md-4">
                              <input type="text" name="machinery_unit" class="form-control form-control-sm" placeholder="መለኪያ (በቁጥር/ብር)" disabled>
                          </div>
                      </div>

                      <!-- 4. የመሬት አቅርቦት -->
                      <div class="row mb-2 align-items-center border-bottom pb-2">
                          <div class="col-md-4">
                              <div class="form-check">
                                  <input class="form-check-input support-checkbox" type="checkbox" name="support_types[]" value="land" id="sup_land">
                                  <label class="form-check-input-label" for="sup_land">የመሬት አቅርቦት</label>
                              </div>
                          </div>
                          <div class="col-md-4">
                              <input type="text" name="land_unit" class="form-control form-control-sm" placeholder="መለኪያ (በካሬ/ቁጥር)" disabled>
                          </div>
                      </div>

                      <!-- 5. የሼድ አቅርቦት -->
                      <div class="row mb-2 align-items-center border-bottom pb-2">
                          <div class="col-md-4">
                              <div class="form-check">
                                  <input class="form-check-input support-checkbox" type="checkbox" name="support_types[]" value="shed" id="sup_shed">
                                  <label class="form-check-input-label" for="sup_shed">የሼድ አቅርቦት</label>
                              </div>
                          </div>
                          <div class="col-md-4">
                              <input type="text" name="shed_unit" class="form-control form-control-sm" placeholder="መለኪያ (በቁጥር)" disabled>
                          </div>
                      </div>

                      <!-- 6. የገበያ ትስስር -->
                      <div class="row mb-2 align-items-center border-bottom pb-2">
                          <div class="col-md-4">
                              <div class="form-check">
                                  <input class="form-check-input support-checkbox" type="checkbox" name="support_types[]" value="market" id="sup_market">
                                  <label class="form-check-input-label" for="sup_market">የገበያ ትስስር</label>
                              </div>
                          </div>
                          <div class="col-md-4">
                              <input type="number" step="0.01" name="market_amount" class="form-control form-control-sm" placeholder="መጠኑ በብር (ብር)" disabled>
                          </div>
                      </div>

                      <!-- 7. ሌሎች ድጋፎች -->
                      <div class="row mb-2 align-items-center">
                          <div class="col-md-4">
                              <div class="form-check">
                                  <input class="form-check-input support-checkbox" type="checkbox" name="support_types[]" value="other" id="sup_other">
                                  <label class="form-check-input-label" for="sup_other">ሌሎች ድጋፎች</label>
                              </div>
                          </div>
                          <div class="col-md-4">
                              <input type="text" name="other_unit" class="form-control form-control-sm" placeholder="መለኪያ በብር" disabled>
                          </div>
                      </div>
                  </div>
               
                  <div class="row mt-4">
                    <div class="col-md-12">
                      <button type="submit" class="btn btn-success px-5">ወደ መደበኛ ኢንተርፕራይዝ አሸጋግርልኝ</button>
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
  var baseUrl = "<?= htmlspecialchars(rtrim($_ENV['BASE_URL'] ?? '', '/'), ENT_QUOTES, 'UTF-8') ?>";

document.getElementById('has_kebele_id')?.addEventListener('change', function() {
    const kebeleInput = document.getElementById('kebele_id_number');
    if (kebeleInput) {
        if (this.value === '1') {
            kebeleInput.disabled = false;
            kebeleInput.required = true;
        } else {
            kebeleInput.disabled = true;
            kebeleInput.required = false;
            kebeleInput.value = '';
        }
    }
});

// የዘርፍ እና ንዑስ ዘርፍ ዳይናሚክ ዌብ ጥያቄ (AJAX)
document.getElementById('sector_select').addEventListener('change', function() {
    const sectorId = this.value;
    const subSectorSelect = document.getElementById('sub_sector_select');
    
    subSectorSelect.innerHTML = '<option value="" disabled selected>ይጫናል...</option>';

    if (sectorId) {
      fetch(baseUrl + '/get-sub-sectors?sector_id=' + sectorId)
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
 
// 1. ድጋፍ ተደርጓል የሚለውን ዋና ምርጫ መቆጣጠር
document.getElementById('has_support').addEventListener('change', function() {
    const supportSection = document.getElementById('support_details_section');
    if (this.value === 'yes') {
        supportSection.style.display = 'block';
    } else {
        supportSection.style.display = 'none';
        document.querySelectorAll('.support-checkbox').forEach(cb => {
            cb.checked = false;
            let row = cb.closest('.row');
            // ሁለቱንም text እና number input አይነቶች ማጽዳትና ማሰናከል
            row.querySelectorAll('input[type="text"], input[type="number"]').forEach(input => {
                input.disabled = true;
                input.value = '';
            });
        });
    }
});

// 2. እያንዳንዱን የድጋፍ ዓይነት ቼክቦክስ ሲመረጥ ተዛማጅ ግብዓቶችን ማቀጣጠፍ (Enable/Disable)
document.querySelectorAll('.support-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        let row = this.closest('.row');
        let inputs = row.querySelectorAll('input[type="text"], input[type="number"]');
        
        inputs.forEach(input => {
            input.disabled = !this.checked;
            if (!this.checked) {
                input.value = ''; 
            }
        });
    });
});
</script>