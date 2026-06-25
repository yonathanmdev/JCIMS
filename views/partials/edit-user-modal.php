<div class="modal fade" id="editUserModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="editUserForm" action="<?= rtrim($_ENV['BASE_URL'], '/') ?>/edit-user-process" method="POST">
       <!-- 1. Modal Header -->
        <div class="modal-header">
          <h6 class="modal-title font-weight-bold">
            <i class="fas fa-edit mr-1"></i> የተቆጣጣሪ ማስተካከያ
          </h6>
          <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="edit_user_id" name="id">
          <div class="row">
    <div class="col-md-6">
          <div class="form-group mb-2">
            <label for="edit_firstname" class="mb-1"><small class="font-weight-bold">ስም</small></label>
        <input type="text" class="form-control form-control-sm" placeholder="ስም ያስገቡ" name="edit_firstname" id="edit_firstname" required>
        </div>
    </div>
    <div class="col-md-6">
           <div class="form-group mb-2">
            <label for="edit_fathername" class="mb-1"><small class="font-weight-bold">የአባት ስም</small></label>
        <input type="text" class="form-control form-control-sm" placeholder="የአባት ስም ያስገቡ" name="edit_fathername" id="edit_fathername" required>
     </div>
          </div>
          </div>
    <div class="row">
    <div class="col-md-6">
           <div class="form-group mb-2">
            <label for="edit_grandfathername" class="mb-1"><small class="font-weight-bold">የአያት ስም</small></label>
        <input type="text" class="form-control form-control-sm" placeholder="የአያት ስም ያስገቡ" name="edit_grandfathername" id="edit_grandfathername" required>
   </div>
    </div>
    <div class="col-md-6">
           <div class="form-group mb-2">
            <label for="edit_phone" class="mb-1"><small class="font-weight-bold">ስልክ ቁጥር</small></label>
        <input type="text" class="form-control form-control-sm" placeholder="ስልክ ቁጥር ያስገቡ" name="edit_phone" id="edit_phone"  required>
          </div>
    </div>
    </div>
    <div class="row">
    <div class="col-md-6">
          
   <div class="form-group mb-2">
        <label for="edit_email" class="mb-1"><small class="font-weight-bold">ኢሜይል</small></label>
        <input type="email" class="form-control form-control-sm" placeholder="ኢሜይል ያስገቡ" name="edit_email"  id="edit_email">
   </div>
    </div>
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
