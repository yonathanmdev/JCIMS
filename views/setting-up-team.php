<?php
use App\Helpers\EthiopianDateHelper; 
use App\Helpers\AuthHelper;
$fiscal_year = AuthHelper::checkFiscalYear();
$is_jobseeker_team_page = true; 
?>
<section class="content">
  <div class="container-fluid">
    <div class="card card-primary card-outline">

      <div class="card-header bg-white d-flex flex-column flex-md-row align-items-md-center">
        <h3 class="card-title">ስራ ፈላጊ አደረጃጀት</h3>
      </div>

      <!-- Team formation panel -->
      <div class="card-body border-bottom">
        <div class="card card-outline card-info mb-0">
          <div class="card-body">
           <form id="team-formation-form">
  <div class="row">
    <div class="col-md-6">
      <div class="form-group mb-2">
        <label class="mb-1" for="sector_id"><small class="font-weight-bold">ዘርፍ</small></label>
        <select class="form-control form-control-sm" id="sector_id" name="sector_id"
                data-cascade-target="sector_name" required>
          <option value="">-- ዘርፍ ይምረጡ --</option>
          <?php foreach ($sectors as $sector): ?>
            <option value="<?= htmlspecialchars($sector['id']) ?>">
              <?= htmlspecialchars($sector['sector']) ?>
            </option>
          <?php endforeach; ?>
        </select>
        <div class="invalid-feedback">እባክዎ ይምረጡ!</div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group mb-2">
        <label class="mb-1" for="sector_name"><small class="font-weight-bold">ንዑስ ዘርፍ</small></label>
        <select class="form-control form-control-sm" id="sector_name" name="sector_name" required disabled>
          <option value="">-- መጀመሪያ ዘርፍ ይምረጡ --</option>
        </select>
        <div class="invalid-feedback">እባክዎ ንዑስ ዘርፍ ይምረጡ!</div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-6">
      <div class="form-group mb-2">
        <label class="mb-1" for="organization_type"><small class="font-weight-bold">የአደረጃጀት ዓይነት</small></label>
        <select class="form-control form-control-sm" id="organization_type" name="organization_type" required>
          <option value="">-- ይምረጡ --</option>
          <option value="family">የቤተሰብ</option>
          <option value="self_interest">በራስ ፍላጎት</option>
          <option value="government_project">የመንግስት ፕሮጀክት</option>
          <option value="special_case">በልዩ ሁኔታ</option>
          <option value="ngo">NGO</option>
        </select>
        <div class="invalid-feedback">እባክዎ የአደረጃጀት ዓይነት ይምረጡ!</div>
      </div>
    </div>

    <!-- NGO -->
    <div class="col-md-6 org-type-field" id="ngo-field-wrapper" style="display:none;">
      <div class="form-group mb-2">
        <label class="mb-1" for="ngo_id"><small class="font-weight-bold">NGO ይምረጡ</small></label>
        <select class="form-control form-control-sm" id="ngo_id" name="ngo_id">
          <option value="">-- NGO ይምረጡ --</option>
          <?php foreach ($projects ?? [] as $project): ?>
            <option value="<?= htmlspecialchars($project['pid']) ?>"><?= htmlspecialchars($project['pname']) ?></option>
          <?php endforeach; ?>
        </select>
        <div class="invalid-feedback">እባክዎ NGO ይምረጡ!</div>
      </div>
    </div>

    <!-- Government project -->
    <div class="col-md-6 org-type-field" id="govt-field-wrapper" style="display:none;">
      <div class="form-group mb-2">
        <label class="mb-1" for="jobseeker_count"><small class="font-weight-bold">የስራ ፈላጊዎች ብዛት</small></label>
        <input type="number" min="1" class="form-control form-control-sm" id="jobseeker_count" name="jobseeker_count">
        <div class="invalid-feedback">እባክዎ ቁጥር ያስገቡ!</div>
      </div>
    </div>
  </div>

  <!-- Government project: AJAX-fetched, auto-replaced checklist -->
  <div class="row org-type-field" id="jobseeker-list-wrapper" style="display:none;">
    <div class="col-md-12">
      <div class="form-group mb-2">
        <label class="mb-1"><small class="font-weight-bold">ስራ ፈላጊዎች ይምረጡ</small></label>
        <input type="text" class="form-control form-control-sm mb-2" id="jobseeker-search" placeholder="በስም ወይም በመ/ቁ ይፈልጉ...">

        <div class="border rounded" style="max-height:280px; overflow-y:auto;">
          <table class="table table-sm table-hover mb-0">
            <thead class="thead-light" style="position:sticky; top:0; z-index:1;">
              <tr>
                <th style="width:40px;"></th>
                <th style="width:120px;">መ/ቁ</th>
                <th>ሙሉ ስም</th>
                <th style="width:60px;" class="text-center">ማስወገድ</th>
              </tr>
            </thead>
            <tbody id="jobseeker-checklist"></tbody>
          </table>
        </div>

        <small class="text-muted"><span id="selected-count">0</span> ስራ ፈላጊ(ዎች) ተመርጠዋል</small>
      </div>
    </div>
  </div>

  <!-- Family / self_interest / ngo: search-to-add, manual remove, no auto-replacement -->
  <div class="row org-type-field" id="jobseeker-search-add-wrapper" style="display:none;">
    <div class="col-md-12">
      <div class="form-group mb-2 position-relative">
        <label class="mb-1"><small class="font-weight-bold">ስራ ፈላጊ ይፈልጉ እና ይምረጡ</small></label>
        <input type="text" class="form-control form-control-sm" id="jobseeker-add-search"
               placeholder="በስም ወይም በመ/ቁ ይፈልጉ..." autocomplete="off">
        <div id="jobseeker-search-results" class="list-group position-absolute w-100"
             style="z-index:1000; max-height:220px; overflow-y:auto; display:none;"></div>
      </div>

      <div class="border rounded" style="max-height:250px; overflow-y:auto;">
        <table class="table table-sm table-hover mb-0">
          <thead class="thead-light" style="position:sticky; top:0; z-index:1;">
            <tr>
              <th style="width:120px;">መ/ቁ</th>
              <th>ሙሉ ስም</th>
              <th style="width:60px;" class="text-center">አስወግድ</th>
            </tr>
          </thead>
          <tbody id="selected-jobseekers-table">
            <tr id="no-selected-row"><td colspan="3" class="text-muted small text-center p-2">ምንም ስራ ፈላጊ አልተመረጠም</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>


  <!-- Carries removal/replacement history for government_project mode, synced by JS before submit -->
  <input type="hidden" id="removed-jobseekers-data" name="removed_jobseekers_data" value="[]">

  <div class="row">
    <div class="col-md-12 text-right">
      <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-save"></i> ቡድን መስርት</button>
    </div>
  </div>
</form>

<!-- Team leadership & association details modal (unchanged) -->
<div class="modal fade" id="teamDetailsModal" tabindex="-1" role="dialog" aria-labelledby="teamDetailsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title font-weight-bold" id="teamDetailsModalLabel"><i class="fas fa-plus mr-1"></i> የቡድን መረጃ ያሟሉ</h6>
        <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-6">
            <div class="form-group mb-2">
              <label class="mb-1" for="place"><small class="font-weight-bold">የሚደራጁበት አካባቢ</small></label>
              <select class="form-control form-control-sm" id="place" name="place" required>
                <option value="" selected disabled>-- ይምረጡ --</option>
                <option value="ከተማ">ከተማ</option>
                <option value="ገጠር">ገጠር</option>
              </select>
              <div class="invalid-feedback">እባክዎ ይምረጡ!</div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group mb-2">
              <label class="mb-1" for="asso_name"><small class="font-weight-bold">የማህበሩ ስም</small></label>
              <input type="text" id="asso_name" name="asso_name" class="form-control form-control-sm" required>
              <div class="invalid-feedback">እባክዎ ያስገቡ!</div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="form-group mb-2">
              <label class="mb-1" for="leaderid"><small class="font-weight-bold">የሊቀመንበሩ</small></label>
              <select class="form-control form-control-sm role-select" id="leaderid" name="leader_id" required>
                <option value="">-- ይምረጡ --</option>
              </select>
              <div class="invalid-feedback">እባክዎ ይምረጡ!</div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group mb-2">
              <label class="mb-1" for="co_name"><small class="font-weight-bold">የም/ሊቀመንበር</small></label>
              <select class="form-control form-control-sm role-select" id="co_name" name="co_id" required>
                <option value="">-- ይምረጡ --</option>
              </select>
              <div class="invalid-feedback">እባክዎ ይምረጡ!</div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="form-group mb-2">
              <label class="mb-1" for="finance_name"><small class="font-weight-bold">ገንዘብ ያዥ</small></label>
              <select class="form-control form-control-sm role-select" id="finance_name" name="finance_id" required>
                <option value="">-- ይምረጡ --</option>
              </select>
              <div class="invalid-feedback">እባክዎ ይምረጡ!</div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group mb-2">
              <label class="mb-1" for="procurement_name"><small class="font-weight-bold">ግዥ</small></label>
              <select class="form-control form-control-sm role-select" id="procurement_name" name="procurement_id" required>
                <option value="">-- ይምረጡ --</option>
              </select>
              <div class="invalid-feedback">እባክዎ ይምረጡ!</div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="form-group mb-2">
              <label class="mb-1" for="manager_phone"><small class="font-weight-bold">የስራ አስኪያጅ ስልክ ቁጥር</small></label>
              <input type="text" id="manager_phone" name="manager_phone" class="form-control form-control-sm" required>
              <div class="invalid-feedback">እባክዎ ያስገቡ!</div>
            </div>
          </div>
        </div>

        <div class="alert alert-danger small mb-0" id="teamDetailsError" style="display:none;"></div>
      </div>
      <div class="modal-footer">
        <input type="hidden" value="1" name="type">
        <input type="button" class="btn btn-default" data-dismiss="modal" value="ዝጋ">
        <button type="button" id="btn-confirm-team" class="btn btn-primary btn-sm">
          <i class="fas fa-check"></i> አደራጅ
        </button>
      </div>
    </div>
  </div>
</div>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>
