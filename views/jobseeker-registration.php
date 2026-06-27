<?php
use App\Helpers\EthiopianDateHelper; 
 $is_jobseeker_registration_page = true; ?>
<section class="content">
  <div class="container-fluid">
    <div class="card-header bg-white d-flex flex-column flex-md-row align-items-md-center card-primary card-outline">
  <div class="ml-md-auto">
    <button 
      type="button" 
      class="btn btn-primary btn-sm w-100 w-md-auto"
      data-toggle="modal" 
      data-target="#jobseekerRegistrationModal"
    >
      <i class="fas fa-user-plus mr-2"></i>
      ስራ ፈላጊ መዝግብ
    </button>
  </div>

</div>
  </div>
</section>

<div class="modal fade" id="jobseekerRegistrationModal" tabindex="-1" role="dialog" aria-labelledby="employeeRegistrationModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <form action="<?= rtrim($_ENV['BASE_URL'], '/') ?>/jobseeker-registration-process" method="POST" enctype="multipart/form-data">
        <!-- 1. Modal Header -->
        <div class="modal-header">
          <h6 class="modal-title font-weight-bold">
            <i class="fas fa-plus mr-1"></i> የስራ ፈላጊ መመዝገቢያ ፎርም
          </h6>
          <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group mb-2">
                <label class="mb-1" for="first_name"><small class="font-weight-bold">ስም</small></label>
                <input type="text" class="form-control form-control-sm" id="first_name" name="first_name" required>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group mb-2">
                <label class="mb-1" for="father_name"><small class="font-weight-bold">የአባት ስም</small></label>
                <input type="text" class="form-control form-control-sm" id="father_name" name="father_name" required>
              </div>
            </div>

               <div class="col-md-4">
              <div class="form-group mb-2">
                <label class="mb-1" for="g_father_name"><small class="font-weight-bold">የአያት ስም</small></label>
                <input type="text" class="form-control form-control-sm" id="g_father_name" name="g_father_name" required>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-4">
              <div class="form-group mb-2">
                <label class="mb-1" for="sex"><small class="font-weight-bold">ጾታ</small></label>
                <select class="form-control form-control-sm" id="sex" name="sex" required>
                     <option value="">ይምረጡ</option>
                  <option value="ወንድ">ወንድ</option>
                  <option value="ሴት">ሴት</option>
                </select>
              </div>
            </div>

          </div>


         

        </div>

        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">ዝጋ</button>
          <button type="submit" class="btn btn-primary btn-sm">መዝግብ</button>
        </div>
      </form>
    </div>
  </div>
</div>
