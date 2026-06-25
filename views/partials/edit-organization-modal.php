<div class="modal fade" id="editOrgModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <form 
        id="editOrgForm" 
        method="POST" 
        action="<?= rtrim($_ENV['BASE_URL'], '/') ?>/update-organization"
        enctype="multipart/form-data"
      >

        <!-- Modal Header -->
        <div class="modal-header">
          <h6 class="modal-title font-weight-bold">
            <i class="fas fa-edit mr-1"></i> ተቋም ማስተካከያ
          </h6>
          <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>

        <div class="modal-body">

          <!-- Hidden fields -->
          <input type="hidden" id="edit_org_id"       name="id">
          <input type="hidden" id="edit_org_branch_id" name="branch_id">
          <input type="hidden" id="edit_org_logo_url" name="existing_logo_url">

          <!-- Organization name -->
          <div class="form-group mb-2">
            <label for="edit_org_name" class="mb-1">
              <small class="font-weight-bold">የተቋሙ ስም</small>
            </label>
            <input 
              type="text" 
              id="edit_org_name" 
              name="org_name" 
              class="form-control form-control-sm" 
              required
            >
          </div>

          <!-- Alternate name -->
          <div class="form-group mb-2">
            <label for="edit_org_alt_name" class="mb-1">
              <small class="font-weight-bold">English Name</small>
            </label>
            <input 
              type="text" 
              id="edit_org_alt_name"
              name="org_alternate_name" 
              class="form-control form-control-sm" 
              placeholder="English Name"
            >
          </div>

          <!-- Description -->
          <div class="form-group mb-2">
            <label for="edit_org_description" class="mb-1">
              <small class="font-weight-bold">የተቋሙ ዓይነት</small>
            </label>
            <input 
              type="text" 
              id="edit_org_description"
              name="org_description" 
              class="form-control form-control-sm" 
              placeholder="ዓይነት ያስገቡ" 
              required
            >
          </div>

          <div class="form-group mb-2">
            <label for="edit_org_phone_number" class="mb-1">
              <small class="font-weight-bold">ስ.ቁ.</small>
            </label>
            <input 
              type="text" 
              id="edit_org_phone_number"
              name="phone_number" 
              class="form-control form-control-sm" 
              placeholder="ስ.ቁ. ያስገቡ" 
              required
            >
          </div>
          <div class="form-group mb-2">
            <label for="edit_org_postal_code" class="mb-1">
              <small class="font-weight-bold">ፖ.ሳ.ቁ.</small>
            </label>
            <input 
              type="text" 
              id="edit_org_postal_code"
              name="postal_code" 
              class="form-control form-control-sm" 
              placeholder="ፖ.ሳ.ቁ. ያስገቡ" 
              required
            >
          </div>

          

          <!-- Logo upload -->
          <div class="form-group mb-2">
            <label for="edit_org_logo" class="mb-1">
              <small class="font-weight-bold">ሎጎ</small>
            </label>
            <input 
              type="file" 
              class="form-control-file" 
              id="edit_org_logo" 
              name="logo" 
              accept="image/*"
            >
            <!-- Current logo link -->
            <small class="form-text text-muted">
              አሁን ያለው ሎጎ:
              <a id="current_logo_preview" href="#" target="_blank" style="display:none;">ተመልክት</a>
            </small>
          </div>

        </div>

        <!-- Footer -->
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
            ዝጋ
          </button>
          <button type="submit" class="btn btn-warning btn-sm">
            <i class="fas fa-save mr-1"></i> አስተካክል
          </button>
        </div>

      </form>
    </div>
  </div>
</div>