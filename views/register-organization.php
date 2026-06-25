<?php $is_organization_page = true; ?>
<!-- Main content -->
<section class="content">
  <div class="container-fluid">

    <!-- Card -->
    <div class="card card-default">
     <div class="card card-primary card-outline">
      <div class="card-header">
<div class="card-header bg-white d-flex align-items-center">

  <div class="ml-auto">
    <button 
      type="button" 
      class="btn btn-primary btn-sm"
      data-toggle="modal" 
      data-target="#orgModal"
    >
      <i class="fas fa-plus mr-1"></i>
      ተቋም መዝግብ
    </button>
  </div>

</div>

      </div>

      <div class="card-body">
        <!-- Example Table (optional) -->
      <table id="example1" data-empty-msg="ምንም ተቋም የለም።" class="table table-bordered table-hover dataTable dtr-inline small" style="color: #000;" aria-describedby="example2_info">
    <thead class="thead-light">
      <tr>
        <th>#</th>
        <th>የተቋሙ ስም </th>
        <th>የተቋሙ ዓይነት</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($organizations)): ?>
        <?php foreach ($organizations as $index => $row): ?>
         <tr id="row-<?= $row['id'] ?>">
            <td><?= $index + 1 ?></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['organization_type']) ?></td>
           <td class="text-center align-middle">
  <div class="btn-group btn-group-sm shadow-sm" role="group">
               <button class="btn btn-outline-secondary btn-sm edit-org" 
                      data-id="<?= $row['id'] ?>" 
                      data-branch-id="<?= $row['branch_id'] ?>"
                      data-name="<?= htmlspecialchars($row['name']) ?>"
                      data-description="<?= htmlspecialchars($row['organization_type']) ?>"
                      data-alt-name="<?= htmlspecialchars($row['alt_name']) ?>"
                      data-phone-number="<?= htmlspecialchars($row['phone_number']) ?>"
                      data-postal-code="<?= htmlspecialchars($row['postal_code']) ?>"
                    data-logo-url="<?= rtrim($_ENV['BASE_URL'], '/') ?>?action=serve-file&file=<?= htmlspecialchars($row['logo_url']) ?>&type=image"
                      title="አስተካክል"  >
                <i class="fas fa-edit"></i>
              </button> 
              <button class="btn btn-outline-danger btn-sm delete-org"
            data-id="<?= $row['id'] ?>"
            data-name="<?= htmlspecialchars($row['name']) ?>">
             <i class="fas fa-trash-alt me-1"></i>
        </button>
  </div>
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
<?php include 'partials/edit-organization-modal.php'; ?>

<!-- Modal (place OUTSIDE card) -->
<div class="modal fade" id="orgModal">
  <div class="modal-dialog modal-md">
    <div class="modal-content">

      <form id="orgForm" method="POST" action="<?= rtrim($_ENV['BASE_URL'], '/') ?>/register-organization-process" enctype="multipart/form-data">

        <!-- 1. Modal Header -->
        <div class="modal-header">
          <h6 class="modal-title font-weight-bold">
            <i class="fas fa-plus mr-1"></i> አዲስ ተቋም መዝግብ
          </h6>
          <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>

        <!-- Body -->
        <div class="modal-body">
          <div class="form-group mb-2">
            <label for="org_name" class="mb-1"><small class="font-weight-bold">የተቋሙ ስም</small></label>
            <input 
              type="text" 
              id="org_name" 
              class="form-control form-control-sm" 
              name="org_name" 
              placeholder="ስም ያስገቡ" 
              required
            >
          </div>
          <div class="form-group mb-2">
            <label for="org_alternate_name" class="mb-1"><small class="font-weight-bold">English Name</small></label>
            <input 
              type="text" 
              id="org_alternate_name" 
              class="form-control form-control-sm" 
              name="org_alternate_name" 
              placeholder="English Name" 
            >
          </div>
          <div class="form-group mb-2">
            <label for="org_description" class="mb-1"><small class="font-weight-bold">የተቋሙ ዓይነት</small></label>
            <input 
              type="text" 
              id="org_description" 
              class="form-control form-control-sm" 
              name="org_description" 
              placeholder="ዓይነት ያስገቡ" 
              required
            >
          </div>
          <div class="form-group mb-2">
            <label for="phone_number" class="mb-1"><small class="font-weight-bold">ስ.ቁ</small></label>
            <input 
              type="text" 
              id="phone_number" 
              class="form-control form-control-sm" 
              name="phone_number" 
              placeholder="ስ.ቁ ያስገቡ" 
              required
            >
          </div>
          <div class="form-group mb-2">
            <label for="postal_code" class="mb-1"><small class="font-weight-bold">ፖ.ሳ.ቁ.</small></label>
            <input 
              type="text" 
              id="postal_code" 
              class="form-control form-control-sm" 
              name="postal_code" 
              placeholder="ፖ.ሳ.ቁ. ያስገቡ" 
              required
            >
          </div>
          <div class="form-group mb-2">
                <label class="mb-1" for="logo"><small class="font-weight-bold">ሎጎ</small></label>
                <input type="file" class="form-control-file" id="logo" name="logo" accept="image/*" required>
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

