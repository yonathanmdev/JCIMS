<?php
use App\Helpers\EthiopianDateHelper;
$is_awaerness_registration_page = true;
?>
<section class="content">
  <div class="container-fluid">
    <div class="card-header bg-white d-flex flex-column flex-md-row align-items-md-center card-primary card-outline">
      <div class="ml-md-auto">
       <div class="form-group mb-0">
    <label for="registrationType" class="mb-1">
        <small class="font-weight-bold">መመዝገብ የምትፈልጉትን ይምረጡ</small>
    </label>

    <label>ግንዛቤ የተፈጠረላቸው</label>

<select class="form-control primary" id="awarenessSelect" name="yetefeterelachew" required>
    <option value="" selected disabled>-- ይምረጡ --</option>

    <option value="job_seekers_modal">ለስራ ፈላጊዎች</option>

    <option value="others_modal">
        ለስራ ፈላጊ ወላጆች ወይም ለሌሎች ህብረተሰብ ክፍሎች
    </option>
</select>
</div>
      </div>
    </div>

 


  </div>
</section>


<div class="modal fade" id="others_modal" tabindex="-1" role="dialog" aria-labelledby="othersModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            <form action="<?= rtrim($_ENV['BASE_URL'], '/') ?>/awareness-registration-other-process"
                  method="POST"
                  data-client-validate="true"
                  novalidate>

                <!-- Modal Header -->
                <div class="modal-header">
                    <h6 class="modal-title font-weight-bold" id="othersModalLabel">
                        <i class="fas fa-plus mr-1"></i>
                        ለሌሎች ህብረተሰብ ክፍሎች / ለስራ ፈላጊ ወላጆች መመዝገቢያ
                    </h6>

                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body">

                    <div class="row">

                        <div class="col-md-12">
                            <div class="form-group mb-2">
                                <label for="fullname">
                                    <small class="font-weight-bold">ሙሉ ስም</small>
                                </label>

                                <input
                                    type="text"
                                    class="form-control form-control-sm"
                                    id="fullname"
                                    name="fullname"
                                    maxlength="100"
                                    data-restrict="letters"
                                    required>

                                <div class="invalid-feedback">
                                    እባክዎ ሙሉ ስም ያስገቡ።
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-6">
                            <div class="form-group mb-2">
                                <label for="sex">
                                    <small class="font-weight-bold">ጾታ</small>
                                </label>

                                <select
                                    class="form-control form-control-sm"
                                    id="sex"
                                    name="sex"
                                    required>

                                    <option value="">-- ይምረጡ --</option>
                                    <option value="ወንድ">ወንድ</option>
                                    <option value="ሴት">ሴት</option>

                                </select>

                                <div class="invalid-feedback">
                                    እባክዎ ጾታ ይምረጡ።
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-2">
                                <label for="yemenoriya_akababi">
                                    <small class="font-weight-bold">የመኖሪያ አካባቢ</small>
                                </label>

                                <select
                                    class="form-control form-control-sm"
                                    id="yemenoriya_akababi"
                                    name="yemenoriya_akababi"
                                    required>

                                    <option value="">-- ይምረጡ --</option>
                                    <option value="ከተማ">ከተማ</option>
                                    <option value="ገጠር">ገጠር</option>

                                </select>

                                <div class="invalid-feedback">
                                    እባክዎ የመኖሪያ አካባቢ ይምረጡ።
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-12">
                            <div class="form-group mb-2">
                                <label for="awareness_type">
                                    <small class="font-weight-bold">የግንዛቤ አይነት</small>
                                </label>

                                <select
                                    class="form-control form-control-sm"
                                    id="awareness_type"
                                    name="awareness_type"
                                    required>

                                    <option value="">-- ይምረጡ --</option>

                                    <option value="ለሌሎች ህብረተሰብ ክፍሎች">
                                        ለሌሎች ህብረተሰብ ክፍሎች
                                    </option>

                                    <option value="ለስራ ፈላጊ ወላጆች">
                                        ለስራ ፈላጊ ወላጆች
                                    </option>

                                </select>

                                <div class="invalid-feedback">
                                    እባክዎ የግንዛቤ አይነት ይምረጡ።
                                </div>
                            </div>
                        </div>

                    </div>

                </div>

                <!-- Modal Footer -->
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                        ዝጋ
                    </button>

                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-save mr-1"></i>
                        መዝግብ
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>


<div class="modal fade" id="job_seekers_modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5>ለስራ ፈላጊዎች</h5>
            </div>
            <div class="modal-body">
                ...
            </div>
        </div>
    </div>
</div>
<script nonce="<?php echo $GLOBALS['nonce']; ?>">
  document.getElementById('awarenessSelect').addEventListener('change', function () {

    const modalId = this.value;

    if (!modalId) return;

    $('#' + modalId).modal('show');

    // reset select after opening
    this.selectedIndex = 0;
});
</script>