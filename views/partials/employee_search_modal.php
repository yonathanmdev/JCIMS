<div class="modal fade" id="employeeSearchModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title font-weight-bold">የሰራተኛ ስም ያስገቡ</h6>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- Secure Hidden Input to hold the source context -->
        <input type="hidden" name="source_context" id="modalSourceContext" value="">
        <!-- Error message only if API fails -->
        <div id="search_error_msg" class="alert alert-danger small" style="display:none;"></div>
        
        <div class="form-group position-relative mb-0">
          <div class="input-group">
            <div class="input-group-prepend">
              <span class="input-group-text"><i class="fas fa-search"></i></span>
            </div>
            <input type="text" id="emp_search_input" class="form-control form-control-sm" placeholder="የሰራተኛ ስም ያስገቡ..." autocomplete="off">
          </div>
          
          <!-- Suggestion List -->
          <ul id="emp_search_suggestions" class="list-group position-absolute w-100" style="z-index: 1051; display:none; max-height: 250px; overflow-y: auto; box-shadow: 0 4px 8px rgba(0,0,0,0.1);"></ul>
        </div>
      </div>
    </div>
  </div>
</div>