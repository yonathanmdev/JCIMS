<!-- Edit Team Modal -->
<div class="modal fade" id="editTeamModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <form id="editTeamForm">
        <div class="modal-header">
          <h5 class="modal-title"><i class="fas fa-edit mr-1"></i> የማህበር መረጃ ማስተካከያ</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="edit_team_id" name="team_id">

          <div class="form-group">
            <label>የማህበሩ ስም</label>
            <input type="text" class="form-control" id="edit_association_name" name="association_name" required>
          </div>

          <div class="row">
             <?php
  $isKetemaAstedader = ($_SESSION['user']['ketema_astedader'] ?? null) === 'on';
  $isLevel4 = (int) ($_SESSION['user']['level'] ?? 0) === 4;
  $useTextInput = $isKetemaAstedader || $isLevel4;
  ?>

  <?php if (!$useTextInput): ?>
    <div class="col-md-4 form-group">
      <label for="edit_kebele">ቀበሌ</label>
      <select class="form-control" id="edit_kebele" name="kebele" required>
        <option value="" selected disabled>-- ይምረጡ --</option>
        <?php foreach ($listofKebeles as $kebele): ?>
          <option value="<?= htmlspecialchars($kebele['kebele']) ?>"><?= htmlspecialchars($kebele['kebele']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  <?php endif; ?>
            <div class="col-md-4 form-group">
  <label>የተደራጁበት አካባቢ</label>
  <select class="form-control" id="edit_yetederajubet_akababi" name="yetederajubet_akababi">
    <option value="" selected disabled>-- ይምረጡ --</option>
    <option value="ከተማ">ከተማ</option>
    <option value="ገጠር">ገጠር</option>
  </select>
</div>
            <div class="col-md-4 form-group">
              <label>የስራ አስኪያጅ ስልክ ቁጥር</label>
              <input type="text" class="form-control" id="edit_manager_phone" name="manager_phone">
            </div>
          </div>

          <div class="row">
            <div class="col-md-4 form-group">
              <label>ዘርፍ (Sector)</label>
              <select class="form-control" id="edit_sector" name="sector">
                <option value="" selected disabled>-- ይምረጡ --</option>
                <!-- populated in JS from res.sectors -->
              </select>
            </div>
            <div class="col-md-4 form-group">
              <label>ንዑስ ዘርፍ (Sub-sector)</label>
              <select class="form-control" id="edit_subsector" name="subsector" disabled>
                <option value="" selected disabled>-- መጀመሪያ ዘርፍ ይምረጡ --</option>
                <!-- populated in JS from res.subsectorsBySector[team.sector_id] -->
              </select>
            </div>
            <div class="col-md-4 form-group">
              <label>የስራ መስክ</label>
              <input type="text" class="form-control" id="edit_yesra_mesk" name="yesra_mesk">
            </div>
          </div>

         <div class="form-group">
  <label>የአደረጃጀት ዓይነት</label>
  <select class="form-control" id="edit_project_type" name="project_type">
    <option value="" selected disabled>-- ይምረጡ --</option>
       <option value="የመንግስት">የመንግስት ፕሮጀክት</option>
    <option value="በራስ ፍላጎት">በራስ ፍላጎት</option>
    <option value="የቤተሰብ">የቤተሰብ</option>
    <option value="በልዩ ሁኔታ">በልዩ ሁኔታ</option>
    <option value="NGO">NGO</option>
  </select>
</div>
    <div class="form-group">
  <label>NGO ይምረጡ</label>
  <select class="form-control" id="ngo" name="ngo">
    <option value="" selected disabled>-- NGO ይምረጡ --</option>
  </select>
</div>

          <hr>

          <div class="row">
            <div class="col-md-3 form-group">
              <label>ሊቀመንበር</label>
              <select class="form-control" id="edit_teamleader_id" name="teamleader_id">
                <option value="" selected disabled>-- ይምረጡ --</option>
                <!-- populated in JS from res.members -->
              </select>
            </div>
            <div class="col-md-3 form-group">
              <label>ም/ሊቀመንበር</label>
              <select class="form-control" id="edit_vice_teamleader_id" name="vice_teamleader_id">
                <option value="" selected disabled>-- ይምረጡ --</option>
                <!-- populated in JS from res.members -->
              </select>
            </div>
            <div class="col-md-3 form-group">
              <label>ገንዘብ ያዥ</label>
              <select class="form-control" id="edit_treasurer_id" name="treasurer">
                <option value="" selected disabled>-- ይምረጡ --</option>
                <!-- populated in JS from res.members -->
              </select>
            </div>
            <div class="col-md-3 form-group">
              <label>ግዥ</label>
              <select class="form-control" id="edit_procurement_id" name="procurement">
                <option value="" selected disabled>-- ይምረጡ --</option>
                <!-- populated in JS from res.members -->
              </select>
            </div>
          </div>

          <div class="alert alert-danger d-none" id="editTeamError"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">ዝጋ</button>
          <button type="submit" class="btn btn-primary" id="editTeamSubmitBtn">አስቀምጥ</button>
        </div>
      </form>
    </div>
  </div>
</div>