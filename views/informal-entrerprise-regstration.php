<?php $is_sra_edl_page = true; ?>
<section class="content">
  <div class="container-fluid">
    <div class="card card-default">
      <div class="card card-primary card-outline">
        <div class="card-body">
          <div class="row mb-3">
            <div class="col-md-12">
              <h1 class="h3 mb-0 text-gray-800">የኢ-መደበኛ ንግድ ተሰማሪዎች ምዝገባ ፎርም</h1>
            </div>  
          </div>

          <div class="container mt-2">
            <div class="card shadow-sm">
              <div class="card-body">
                <form action="informal-trade-registration-process" method="POST">
                  
                  <!-- ================= 1. የግል መረጃ ================= -->
                  <h5 class="text-primary border-bottom pb-2 mb-3">1. የግል መረጃ</h5>
                  <div class="row">
                    <div class="col-md-6 form-group">
                      <label>ሙሉ ስም (ከነ አያት) *</label>
                      <input type="text" name="full_name" class="form-control" placeholder="ስም እስከ አያት ያስገቡ" required>
                    </div>
                    <div class="col-md-3 form-group">
                      <label>ጾታ *</label>
                      <select name="gender" class="form-control" required>
                        <option value="" disabled selected>-- ይምረጡ --</option>
                        <option value="Male">ወንድ</option>
                        <option value="Female">ሴት</option>
                      </select>
                    </div>
                    <div class="col-md-3 form-group">
                      <label>ዕድሜ *</label>
                      <input type="number" name="age" class="form-control" min="15" max="100" placeholder="ዕድሜ" required>
                    </div>
                  </div>

                  <div class="row mt-2">
                    <div class="col-md-4 form-group">
                      <label>ስልክ ቁጥር</label>
                      <input type="text" name="phone" class="form-control" placeholder="09xxxxxxxx">
                    </div>
                    <div class="col-md-4 form-group">
                      <label>የቀበሌ መታወቂያ *</label>
                      <select name="has_kebele_id" id="has_kebele_id" class="form-control" required>
                        <option value="1">1. አለ</option>
                        <option value="2" selected>2. የለም</option>
                      </select>
                    </div>
                    <div class="col-md-4 form-group">
                      <label>የመታወቂያ ቁጥር</label>
                      <input type="text" name="kebele_id_number" id="kebele_id_number" class="form-control" placeholder="የመታወቂያ ቁጥር ያስገቡ" disabled>
                    </div>
                  </div>

                  <!-- ================= 2. የመኖሪያ አድራሻ ================= -->
                  <h5 class="text-primary border-bottom pb-2 mt-4 mb-3">2. የመኖሪያ አድራሻ</h5>
                  <div class="row">
                    <div class="col-md-6 form-group">
                      <label>መኖሪያ ቅርንጫፍ (ዞን/ወረዳ) *</label>
                      <select name="resbranch_id" class="form-control" required>
                        <option value="" disabled selected>-- ይምረጡ --</option>
                        <?php if (!empty($branches)): ?>
                          <?php foreach ($branches as $branch): ?>
                            <option value="<?= htmlspecialchars($branch['internal_id']) ?>">
                              <?= htmlspecialchars($branch['name']) ?>
                            </option>
                          <?php endforeach; ?>
                        <?php endif; ?>
                      </select>
                    </div>
                    <div class="col-md-6 form-group">
                      <label>መኖሪያ ቀበሌ *</label>
                      <input type="text" name="res_kebele" class="form-control" placeholder="የቀበሌ ስም/ቁጥር" required>
                    </div>
                  </div>

                  <!-- ================= 3. የሥራ ቦታ እና የንግድ መረጃ ================= -->
                  <h5 class="text-primary border-bottom pb-2 mt-4 mb-3">3. የሥራ ቦታ እና ዘርፍ መረጃ</h5>
                  <div class="row">
                    <div class="col-md-6 form-group">
                      <label>የሥራ ቦታ  *</label>
                      <select name="work_branch_id" class="form-control" required>
                        <option value="" disabled selected>-- ይምረጡ --</option>
                        <?php if (!empty($branches)): ?>
                          <?php foreach ($branches as $branch): ?>
                            <option value="<?= htmlspecialchars($branch['internal_id']) ?>">
                              <?= htmlspecialchars($branch['name']) ?>
                            </option>
                          <?php endforeach; ?>
                        <?php endif; ?>
                      </select>
                    </div>
                    <div class="col-md-6 form-group">
                      <label>ንግዱ የሚገኝበት አካባቢ *</label>
                      <select name="trade_area_type" class="form-control" required>
                        <option value="1">ከተማ</option>
                        <option value="2">ገጠር</option>
                      </select>
                    </div>
                  </div>

                  <!-- ያልተነካው የሴክተር እና ንዑስ ሴክተር ክፍል -->
                  <div class="row mt-2">
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
                        <?php }else{ ?>
                          <option value="" disabled>የስራ ዘርፍ አልተገኘም</option>
                        <?php } ?>
                      </select>
                    </div>
                    <div class="col-md-6 form-group">
                      <label>ንዑስ ዘርፍ *</label>
                      <select class="form-control" name="sub_sector" id="sub_sector_select" required>
                        <option value="" selected disabled>-- ይምረጡ --</option>
                      </select>
                    </div>
                  </div>

                  <div class="row mt-2">
                    <div class="col-md-4 form-group">
                      <label>የሥራ መደብ *</label>
                      <input type="text" name="job_position" class="form-control" placeholder="ምሳሌ፡ የመንገድ ዳር ቸርቻሪ" required>
                    </div>
                    <div class="col-md-4 form-group">
                      <label>የተሰማራበት ዓ.ም *</label>
                      <input type="number" name="start_year" class="form-control" min="1950" max="2020" placeholder="ምሳሌ፡ 2015" required>
                    </div>
                    <div class="col-md-4 form-group">
                      <label>አቅራቢያ የሚገኝ ማዕከል ስም</label>
                      <input type="text" name="nearby_center_name" class="form-control" placeholder="የማዕከሉ ስም ካለ ያስገቡ">
                    </div>
                  </div>

                  <!-- ================= አዝራር ================= -->
                  <div class="row mt-4">
                    <div class="col-md-12">
                      <button type="submit" class="btn btn-primary px-5">መዝግብ</button>
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

// የቀበሌ መታወቂያ ቁጥር ማስገቢያውን እንደ ምርጫው ማብራት/ማጥፋት
document.getElementById('has_kebele_id').addEventListener('change', function() {
    const kebeleInput = document.getElementById('kebele_id_number');
    if (this.value === '1') {
        kebeleInput.disabled = false;
        kebeleInput.required = true;
    } else {
        kebeleInput.disabled = true;
        kebeleInput.required = false;
        kebeleInput.value = '';
    }
});

function attachLiveSearch() {
    const input = document.getElementById("enterpriseSearch");
    console.log("Live search attached!"); 
}

// ያልተነካው ያንተ የ dynamic sub-sector script
document.getElementById('sector_select').addEventListener('change', function() {
    const sectorId = this.value;
    const subSectorSelect = document.getElementById('sub_sector_select');
    
    // ንዑስ ዘርፍን ማፅዳት
    subSectorSelect.innerHTML = '<option value="" disabled selected>ይጫናል...</option>';

    if (sectorId) {
        fetch('./get-sub-sectors?sector_id=' + sectorId)
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
</script>