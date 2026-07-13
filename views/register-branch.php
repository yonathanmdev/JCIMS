<?php $is_branch_page = true; ?>
<!-- Main content -->
<section class="content">
  <div class="container-fluid">
    <!-- Card -->
    <div class="card card-default">
      <div class="card-header">

        <h3 class="card-title">መስሪያ ቤት</h3>

        <div class="card-tools">
           <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#branchModal">
            <i class="fas fa-plus mr-1"></i>መስሪያ ቤት መዝግብ
          </button>
          
        </div>

      </div>

      <div class="card-body">
        <!-- Example Table (optional) -->
      <table id="example1" data-empty-msg="ምንም መስሪያ ቤት የለም።" class="table table-bordered table-hover dataTable dtr-inline small" style="color: #000;" aria-describedby="example2_info">
    <thead class="thead-light">
      <tr>
        <th>#</th>
        <th>የመስሪያ ቤት ስም </th>
        <th>አማራጭ ስም</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($organizations)): ?>
        <?php foreach ($organizations as $index => $row): ?>
          <tr id="row-<?= htmlspecialchars($row['id']) ?>">
            <td><?= $index + 1 ?></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['alt_name']) ?></td>
            <td>
               <button class="btn btn-primary btn-sm edit-branch" 
                      data-id="<?= $row['id'] ?>" 
                      data-name="<?= htmlspecialchars($row['name']) ?>" 
                       data-alternate-name="<?= htmlspecialchars($row['alt_name']) ?>"
                        data-phone-number="<?= htmlspecialchars($row['phone_number']) ?>"
                       data-postal-code="<?= htmlspecialchars($row['postal_code']) ?>"
                       data-logo-url="<?= rtrim($_ENV['BASE_URL'], '/') ?>?action=serve-file&file=<?= htmlspecialchars($row['logo_url']) ?>&type=image"
                     title="አስተካክል"  >
                <i class="fas fa-edit"></i>
              </button> 
              <button class="btn btn-danger btn-sm delete-branch" 
                      data-id="<?= $row['id'] ?>" 
                      data-name="<?= htmlspecialchars($row['name']) ?>" title="ሰርዝ">

                <i class="fas fa-trash-alt me-1"></i>
              </button>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
      </div>

    </div>
    <!-- /.card -->

  </div>
</section>
<?php include 'partials/edit-branch-modal.php'; ?>

<!-- Modal (place OUTSIDE card) -->
<div class="modal fade" id="branchModal">
  <div class="modal-dialog modal-md">
    <div class="modal-content">

      <form id="orgForm" method="POST" action="<?= rtrim($_ENV['BASE_URL'], '/') ?>/register-branch-process" enctype="multipart/form-data">

        <div class="modal-header">
        <h6 class="modal-title font-weight-bold">
          <i class="fas fa-plus mr-1"></i> አዲስ ቅርንጫፍ መዝግብ
        </h6>
        <button type="button" class="close" data-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>

        <!-- Body -->
        <div class="modal-body">
          <div class="form-group mb-2">
            <label for="org_name" class="mb-1"><small class="font-weight-bold">የቅርንጫፍ ስም</small></label>
            <input 
              type="text" 
              id="branch_name" 
              class="form-control form-control-sm" 
              name="branch_name" 
              placeholder="ስም ያስገቡ" 
              required
            >
          </div>


          <div class="form-group mb-2">
            <label for="org_alternate_name" class="mb-1"><small class="font-weight-bold">English Name</small></label>
            <input 
              type="text" 
              id="branch_alternate_name" 
              class="form-control form-control-sm" 
              name="branch_alternate_name" 
              placeholder="English Name" 
            >
          </div>


          <div class="form-group mb-2">
            <label for="org_alternate_name" class="mb-1"><small class="font-weight-bold">ስ.ቁ.</small></label>
            <input 
              type="text" 
              id="branch_phone_number" 
              class="form-control form-control-sm" 
              name="branch_phone_number" 
              placeholder="ስ.ቁ. ያስገቡ" 
            >
          </div>

          <div class="form-group mb-2">
            <label for="org_alternate_name" class="mb-1"><small class="font-weight-bold">ፖ.ሳ.ቁ.</small></label>
            <input 
              type="text" 
              id="branch_postal_code" 
              class="form-control form-control-sm" 
              name="branch_postal_code" 
              placeholder="ፖ.ሳ.ቁ. ያስገቡ" 
            >
          </div>


          <div class="form-group mb-2">
            <label for="branch_logo" class="mb-1"><small class="font-weight-bold">ሎጎ</small></label>
            <input 
              type="file" 
              class="form-control-file" 
              id="branch_logo" 
              name="logo" 
              accept="image/*"
              required
            >
          </div>
        </div>

        <!-- Footer -->
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
            ዝጋ
          </button>
          <button type="submit" class="btn btn-primary btn-sm">
            መዝግብ
          </button>
        </div>

      </form>

    </div>
  </div>
</div>

