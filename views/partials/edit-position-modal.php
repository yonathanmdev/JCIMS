
<div class="modal fade" id="editPositionModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="editPositionForm">
        <!-- 1. Modal Header -->
        <div class="modal-header">
          <h6 class="modal-title font-weight-bold">
            <i class="fas fa-edit mr-1"></i>መደብ ማስተካከያ
          </h6>
          <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="edit_position_id" name="id">
          <div class="row">
    <div class="col-md-6">
          <div class="form-group mb-2">
            <label for="edit_director_name" class="mb-1"><small class="font-weight-bold">የስራ ክፍል</small></label>
            <select name="director_name" id="edit_director_name" class="form-control form-control-sm" required>
              <option value="" selected disabled>ይምረጡ</option>
              <?php if (!empty($directors)): ?>
                <?php foreach ($directors as $row): ?>
                  <option value="<?= htmlspecialchars($row['id']) ?>">
                    <?= htmlspecialchars($row['director_name']) ?>
                  </option>
                <?php endforeach; ?>
              <?php endif; ?>
            </select>
          </div>
    </div>
    <div class="col-md-6">
          
          <div class="form-group mb-2">
            <label for="edit_position_name" class="mb-1"><small class="font-weight-bold">የስራ መደቡ መጠሪያ</small></label>
            <input type="text" id="edit_position_name" name="position_name" class="form-control form-control-sm" required>
          </div>
    </div>
    </div>
    <div class="row">
    <div class="col-md-6">
          <div class="form-group mb-2">
            <label for="edit_position_code" class="mb-1"><small class="font-weight-bold">የመደቡ መለያ ቁጥር</small></label>
            <input type="text" id="edit_position_code" name="position_code" class="form-control form-control-sm" required>
          </div>
    </div>
    <div class="col-md-6">
           <div class="form-group mb-2">
            <label for="edit_sera_dereja" class="mb-1"><small class="font-weight-bold">የስራ ደረጃ</small></label>
            <select name="sera_dereja" id="edit_sera_dereja" class="form-control form-control-sm" required>
              <option value="" disabled="disabled">← ይምረጡ →</option>
              <option>ሹመት</option>
              <option>I</option>
              <option>II</option>
              <option>III</option>
              <option>IV</option>
              <option>V</option>
              <option>VI</option>
              <option>VII</option>
              <option>VIII</option>
              <option>IX</option>
              <option>X</option>
              <option>XI</option>
              <option>XII</option>
              <option>XIII</option>
              <option>XIV</option>
              <option>XV</option>
              <option>XVI</option>
              <option>XVII</option>
              <option>XVIII</option>
              <option>XIX</option>
              <option>XX</option>
            </select>
          </div>
    </div>
    </div>
    <div class="row">
    <div class="col-md-4">
          <div class="form-group mb-2">
            <label for="edit_sera_rken" class="mb-1"><small class="font-weight-bold">የደረጃ እርከን</small></label>
            <select name="sera_rken" id="edit_sera_rken" class="form-control form-control-sm" required>
              <option value="" disabled="disabled">← ይምረጡ →</option>
              <option>1</option>
              <option>2</option>
              <option>3</option>
              <option>4</option>
              <option>5</option>
              <option>6</option>
              <option>7</option>
              <option>8</option>
              <option>9</option>
              <option>10</option>
              <option>11</option>
              <option>12</option>
              <option>13</option>
              <option>14</option>
              <option>15</option>
            </select>
          </div>
    </div>
    <div class="col-md-4">
          
          <div class="form-group mb-2">
            <label for="edit_salary" class="mb-1"><small class="font-weight-bold">ደመወዝ</small></label>
            <input type="number" id="edit_salary" name="salary" class="form-control form-control-sm" required>
          </div>
    </div>
    <div class="col-md-4">
          <div class="form-group">
            <label for="edit_yeteyash_huneta" class="mb-1"><small class="font-weight-bold">የተያያዥ ሁኔታ</small></label>
           <select name="yeteyash_huneta" id="edit_yeteyash_huneta" class="form-control form-control-sm" required>
              <option value="" disabled="disabled">← ይምረጡ →</option>
               <option value="ተያዥ የሚያስፈልገዉ">ተያዥ የሚያስፈልገዉ </option>
	    <option value="ተያዥ የማያስፈልገዉ">ተያዥ የማያስፈልገዉ </option> 
            </select>
           </div>
    </div>
      </div>    
     
     <!-- Allow Multiple -->
<div class="row mt-1">
    <div class="col-md-6">
        <div class="toggle-card p-2" id="edit_card_multiple">
            <label class="card-label mb-1">
                <small class="font-weight-bold">የሰራተኛ ብዛት ፍቃድ</small>
            </label>
            <div class="toggle-row">
                <label class="switch">
                    <input type="checkbox" id="edit_multiple_check">
                    <!-- ← removed onchange -->
                    <span class="slider"></span>
                </label>
                <span class="toggle-label-text ml-2">
                    <small>ብዙ ሰራተኞች ይፈቀዳል</small>
                </span>
                <span class="badge-status ml-2" id="edit_multiple_badge">
                    አይፈቀድም
                </span>
            </div>
            <input type="hidden" name="allow_multiple" id="edit_allow_multiple" value="0">
            <small class="text-muted mt-1 d-block">
                <i class="fas fa-info-circle"></i>
                ካልተፈቀደ አንድ ሰራተኛ ብቻ ሊያዝ ይችላል።
            </small>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group mb-2">
            <label class="mb-1">
                <small class="font-weight-bold">
                    የቦታ ብዛት
                    <span class="text-muted">(ባዶ = ያልተወሰነ)</span>
                </small>
            </label>
            <input type="number"
                   class="form-control form-control-sm"
                   placeholder="የቦታ ብዛት (ለምሳሌ 5)"
                   name="vacancy_count"
                   id="edit_vacancy_count"
                   min="1"
                   disabled>
            <small class="text-muted">
                <i class="fas fa-info-circle"></i>
                ብዙ ሰራተኞች ከተፈቀደ ብቻ ያስገቡ።
            </small>
        </div>
    </div>
</div>

<!-- Nesa + Cloth -->
<div class="row">
    <div class="col-md-6">
        <div class="form-group mb-2">
            <div class="toggle-card" id="edit_card_nesa">
                <label class="card-label">
                    <small class="font-weight-bold">ነፃ ህክምና</small>
                </label>
                <div class="toggle-row">
                    <label class="switch">
                        <input type="checkbox" id="edit_nesa_check">
                        <!-- ← removed onchange -->
                        <span class="slider"></span>
                    </label>
                    <span class="toggle-label-text">ተጠቃሚ ነዉ</span>
                    <span class="badge-status" id="edit_nesa_badge">አይደለም</span>
                </div>
                <input type="hidden" name="nesa_hkmna" id="edit_nesa_hkmna" value="no">
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group mb-2">
            <div class="toggle-card" id="edit_card_cloth">
                <label class="card-label">
                    <small class="font-weight-bold">ደንብ ልብስ ተጠቃሚ</small>
                </label>
                <div class="toggle-row">
                    <label class="switch">
                        <input type="checkbox" id="edit_cloth_check">
                        <!-- ← removed onchange -->
                        <span class="slider"></span>
                    </label>
                    <span class="toggle-label-text">ተጠቃሚ ነዉ</span>
                    <span class="badge-status" id="edit_cloth_badge">አይደለም</span>
                </div>
                <input type="hidden" name="cloth_enabled" id="edit_cloth_value" value="no">
                <select name="cloth_duration" id="edit_cloth_duration"
                        class="form-control form-control-sm mt-1" disabled>
                    <option value="" disabled selected>&larr; ይምረጡ &rarr;</option>
                    <option value="በአመት አንድ">በአመት አንድ</option>
                    <option value="በአመት ሁለት">በአመት ሁለት</option>
                    <option value="በአመት አንድ ጥንድ">በአመት አንድ ጥንድ</option>
                    <option value="በአመት ሁለት ጥንድ">በአመት ሁለት ጥንድ</option>
                    <option value="በአመት ሶስት ጥንድ">በአመት ሶስት ጥንድ</option>
                    <option value="እንደ ስራ መሳሪያ የሚሰጥ በአመት ሁለት">እንደ ስራ መሳሪያ የሚሰጥ በአመት ሁለት</option>
                    <option value="እንደ ስራ መሳሪያ የሚሰጥ">እንደ ስራ መሳሪያ የሚሰጥ</option>
                    <option value="በሦስት ዓመት አንድ">በሦስት ዓመት አንድ</option>
                    <option value="እንደ ስራ መሳሪያ የሚሰጥ በአመት አንድ">እንደ ስራ መሳሪያ የሚሰጥ በአመት አንድ</option>
                </select>
            </div>
        </div>
    </div>
</div>
          <div class="form-group">
            <label for="edit_description">Remark</label>
            <textarea id="edit_description" name="description" class="form-control" rows="3"></textarea>
          </div>
        </div>
        
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
            ዝጋ
          </button>
          <button type="submit" class="btn btn-warning btn-sm">አስተካክል</button>
        </div>
      </form>
    </div>
  </div>
</div>