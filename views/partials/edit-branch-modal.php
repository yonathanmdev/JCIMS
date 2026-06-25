<div class="modal fade" id="editBranchModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="editBranchForm">
        <!-- 1. Modal Header -->
        <div class="modal-header">
          <h6 class="modal-title font-weight-bold">
            <i class="fas fa-edit mr-1"></i>ቅርንጫፍ አስተካክል
          </h6>
          <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="edit_branch_id" name="id">
          <input type="hidden" id="edit_branch_logo_url" name="existing_logo_url">

          <div class="form-group mb-2">
            <label for="edit_branch_name" class="mb-1"><small class="font-weight-bold">የተቋሙ ስም</small></label>
            <input type="text" id="edit_branch_name" name="branch_name" class="form-control form-control-sm" required>
          </div>

          <!-- Alternate name -->
          <div class="form-group mb-2">
            <label for="edit_branch_alt_name" class="mb-1">
              <small class="font-weight-bold">English Name</small>
            </label>
            <input 
              type="text" 
              id="edit_branch_alt_name"
              name="branch_alternate_name" 
              class="form-control form-control-sm" 
              placeholder="English Name"
            >
          </div>

          <div class="form-group mb-2">
            <label for="edit_branch_phone_number" class="mb-1">
              <small class="font-weight-bold">ስ.ቁ.</small>
            </label>
            <input 
              type="text" 
              id="edit_branch_phone_number"
              name="branch_phone_number" 
              class="form-control form-control-sm" 
              placeholder="ስ.ቁ. ያስገቡ"
            >
          </div>

          <div class="form-group mb-2">
            <label for="edit_branch_postal_code" class="mb-1">
              <small class="font-weight-bold">ፖ.ሳ.ቁ.</small>
            </label>
            <input 
              type="text" 
              id="edit_branch_postal_code"
              name="branch_postal_code" 
              class="form-control form-control-sm" 
              placeholder="ፖ.ሳ.ቁ. ያስገቡ"
            >
          </div>
 <!-- Logo upload -->
          <div class="form-group mb-2">
            <label for="edit_branch_logo" class="mb-1">
              <small class="font-weight-bold">ሎጎ</small>
            </label>
            <input 
              type="file" 
              class="form-control-file" 
              id="edit_branch_logo" 
              name="logo" 
              accept="image/*"
            >
            <!-- Current logo link -->
            <small class="form-text text-muted">
              አሁን ያለው ሎጎ:
              <a id="edit_current_logo_preview" href="#" target="_blank" style="display:none;">ተመልክት</a>
            </small>
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