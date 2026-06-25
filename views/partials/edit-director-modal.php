<div class="modal fade" id="editDirectorModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="editDirectorForm">
        <!-- 1. Modal Header -->
        <div class="modal-header">
          <h6 class="modal-title font-weight-bold">
            <i class="fas fa-edit mr-1"></i> የስራ ልምድ አስተካክል
          </h6>
          <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="edit_director_id" name="id">
          <div class="form-group mb-2">
            <label for="edit_director_name" class="mb-1"><small class="font-weight-bold">ስም</small></label>
            <input type="text" id="edit_director_name" name="director_name" class="form-control form-control-sm" required>
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