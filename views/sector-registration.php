<?php
use App\Helpers\EthiopianDateHelper;
$is_sector_registration_page = true;
?>
<section class="content">
  <div class="container-fluid">
    <div class="card-header bg-white d-flex flex-column flex-md-row align-items-md-center card-primary card-outline">
      <div class="ml-md-auto">
        <button
          type="button"
          class="btn btn-primary btn-sm w-100 w-md-auto"
          data-toggle="modal"
          data-target="#sectorRegistrationModal"
        >
          <i class="fas fa-user-plus mr-2"></i>
          ዘርፍ መዝግብ
        </button>
      </div>
    </div>

    <div class="container-fluid mt-3">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-list mr-2"></i>
                የተመዘገቡ ዘርፎች
            </h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
              <table id="example1" data-empty-msg="ምንም የተመዘገበ ዘርፍ የለም።" class="table table-bordered table-hover dataTable dtr-inline small" style="color: #000;" aria-describedby="example2_info">
                <thead class="thead-light">
                        <tr>
                            <th style="width:60px;">#</th>
                            <th>ዘርፍ ስም</th>
                            <th style="width:180px;">የተመዘገበበት ቀን</th>
                            <th style="width:140px;" class="text-center">ድርጊት</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($sectors)): ?>
                            <?php foreach ($sectors as $index => $sector): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td>
                                        <?= htmlspecialchars($sector['sector']) ?>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($sector['created_at'] ?? '') ?>
                                    </td>
                                    <td class="text-center">
                                        <button
                                            class="btn btn-info btn-xs"
                                            title="አስተካክል">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button
                                            class="btn btn-danger btn-xs"
                                            title="ሰርዝ">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>


</div>
  </div>
</section>

<div class="modal fade" id="sectorRegistrationModal" tabindex="-1" role="dialog" aria-labelledby="sectorRegistrationModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    <form
  id="sectorRegistrationForm"
  data-client-validate="true"
  action="<?= rtrim($_ENV['BASE_URL'], '/') ?>/sector-registration-process"
  method="POST"
  novalidate
>  
    <!-- 1. Modal Header -->
        <div class="modal-header">
          <h6 class="modal-title font-weight-bold" id="sectorRegistrationModalLabel">
            <i class="fas fa-plus mr-1"></i> የዘርፍ መመዝገቢያ ፎርም
          </h6>
          <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <div class="form-group mb-2">
                <label class="mb-1" for="sector_name"><small class="font-weight-bold">ዘርፍ ስም</small></label>
                <input
                  type="text"
                  class="form-control form-control-sm"
                  id="sector_name"
                  name="sector_name"
                  minlength="2"
                  maxlength="150"
                  data-restrict="letters"
                  required
                >
                <div class="invalid-feedback">
                  እባክዎ የዘርፍ ስም ያስገቡ (ቢያንስ 2 ፊደላት)
                </div>
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