<!-- ===== Edit Experience Modal ===== -->
<div class="modal fade" id="editExperienceModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <form id="editExperienceForm" action="<?= rtrim($_ENV['BASE_URL'], '/') ?>/employee-experience-update" method="post" enctype="multipart/form-data">

        <!-- 1. Modal Header -->
        <div class="modal-header">
          <h6 class="modal-title font-weight-bold">
            <i class="fas fa-edit mr-1"></i> የስራ ልምድ አስተካክል
          </h6>
          <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>

        <!-- 2. Modal Body -->
        <div class="modal-body">
          <input type="hidden" name="_method" value="PUT">
          <input type="hidden" name="experience_id"   id="edit_experience_id">
          <input type="hidden" name="employee_uuid"   id="edit_employee_uuid">

          <div class="row">
            <!-- Company Name -->
            <div class="col-md-6 form-group mb-2 position-relative">
              <label for="edit_company_name" class="mb-1">
                <small class="font-weight-bold">የሰሩበት መስሪያ ቤት </small>
                <span class="text-danger">*</span>
              </label>
              <input type="text" name="company_name" id="edit_company_name"
                     class="form-control form-control-sm"
                     placeholder="የመስሪያ ቤቱን ስም ያስገቡ..."
                     autocomplete="off" required>
              <ul id="edit_company_suggestions" class="list-group position-absolute w-100"
                  style="z-index:9999; display:none;"></ul>
            </div>

            <!-- Job Title -->
            <div class="col-md-6 form-group mb-2 position-relative">
              <label for="edit_job_title" class="mb-1">
                <small class="font-weight-bold">የስራ መደብ </small>
                <span class="text-danger">*</span>
              </label>
              <input type="text" name="job_title" id="edit_job_title"
                     class="form-control form-control-sm"
                     placeholder="የስራ መደቡን ያስገቡ..."
                     autocomplete="off" required>
              <ul id="edit_job_suggestions" class="list-group position-absolute w-100"
                  style="z-index:9999; display:none;"></ul>
            </div>
          </div>

          <div class="row">
            <!-- Employment Type -->
            <div class="col-md-4 form-group">
              <label for="edit_employment_type" class="mb-1">
                <small class="font-weight-bold">የቅጥር አይነት </small>
                <span class="text-danger">*</span>
              </label>
              <select name="employment_type" id="edit_employment_type"
                      class="form-control form-control-sm" required>
                <option value="">-- ይምረጡ --</option>
                <option value="Full-time">ቋሚ</option>
                <option value="Contract">ኮንትራት</option>
                 <option value="Delegate">ውክልና</option>
              </select>
            </div>

            <!-- Start Date -->
            <div class="col-md-4 form-group">
              <label class="mb-1">
                <small class="font-weight-bold">የጀመሩበት ቀን </small>
                <span class="text-danger">*</span>
              </label>
              <input type="text"
                     class="ethiopian-date form-control form-control-sm"
                     id="edit_eth_start_date"
                     data-gregorian="#edit_start_date"
                     placeholder="ቀን/ወር/ዓ.ም ይምረጡ"
                     readonly
                     style="background-color: #fff; cursor: pointer;" required>
              <input type="date" name="start_date" id="edit_start_date" class="d-none" required>
           <!-- Edit Modal — below #edit_eth_start_date input -->
<span id="edit_start_date_error_msg" class="text-danger mt-1" style="display:none; font-size:11px; font-weight:bold;">
    <i class="fas fa-exclamation-circle mr-1"></i>
</span>
            </div>

            <!-- End Date -->
            <div class="col-md-4 form-group">
              <label class="mb-1">
                <small class="font-weight-bold">የጨረሱበት ቀን </small>
                <span class="text-danger">*</span>
              </label>
              <input type="text"
                     class="ethiopian-date form-control form-control-sm"
                     id="edit_eth_end_date"
                     data-gregorian="#edit_end_date"
                     placeholder="ቀን/ወር/ዓ.ም ይምረጡ"
                     readonly
                     style="background-color: #fff; cursor: pointer;" required>
              <input type="date" name="end_date" id="edit_end_date" class="d-none" required>

              <!-- Error Message -->
              <span id="edit_date_error_msg" class="text-danger mt-1"
                    style="display:none; font-size:11px; font-weight:bold;">
                <i class="fas fa-exclamation-circle mr-1"></i>
                ስህተት፡ ስራ የጨረሱበት ቀን ከጀመሩበት ቀን ማነስ የለበትም።
              </span>
            </div>
          </div>

        </div>

        <!-- 3. Modal Footer -->
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">ዝጋ</button>
          <button type="submit" class="btn btn-warning btn-sm" id="editSubmitBtn">
            <i class="fas fa-save mr-1"></i> አስተካክል
          </button>
        </div>

      </form>
    </div>
  </div>
</div>