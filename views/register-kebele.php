<?php $is_branch_page = true; ?>
<!-- Main content -->
<section class="content">
  <div class="container-fluid">
    <!-- Card -->
    <div class="card card-default">
      <div class="card-header">

        <h3 class="card-title">ቀበለ መመዝገቢያ ከዚህ ላይ ቀበሌ ብቻ ነዉ እሚመዘገብ ማእከል የሚመዘገበዉ በቅርጫፍ በኩል ነዉ </h3>

        <div class="card-tools">
           <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#branchModal">
            <i class="fas fa-plus mr-1"></i>ቀበለ መዝግብ
          </button>
          
        </div>

      </div>

      <div class="card-body">
        <!-- Example Table (optional) -->
      <table id="example1" data-empty-msg="ምንም መስሪያ ቤት የለም።" class="table table-bordered table-hover dataTable dtr-inline small" style="color: #000;" aria-describedby="example2_info">
    <thead class="thead-light">
      <tr>
        <th>#</th>
        <th>የቀበለ ስም </th>
        <th>የተመዘገበበት ቀን</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($kebeledata)): ?>
        <?php foreach ($kebeledata as $index => $row): ?>
          <tr id="row-<?= htmlspecialchars($row['id']) ?>">
            <td><?= $index + 1 ?></td>
            <td><?= htmlspecialchars($row['kebele']) ?></td>
            <td><?= htmlspecialchars($row['created_at']) ?></td>
            <td>
             <!--  <button class="btn btn-primary btn-sm edit-branch" 
                      data-id="<?= $row['id'] ?>" 
                      data-name="<?= htmlspecialchars($row['kebele']) ?>" 
                       
                      title="አስተካክል"  >
                <i class="fas fa-edit"></i>
              </button> -->
              <button class="btn btn-danger btn-sm delete-kebele" 
                      data-id="<?= $row['id'] ?>" 
                      data-name="<?= htmlspecialchars($row['kebele']) ?>" title="ሰርዝ">

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
<?php include 'partials/edit-kebele-modal.php'; ?>

<!-- Modal (place OUTSIDE card) -->
<div class="modal fade" id="branchModal">
  <div class="modal-dialog modal-md">
    <div class="modal-content">

      <form id="orgForm" method="POST" action="<?= rtrim($_ENV['BASE_URL'], '/') ?>/register-kebele-process" enctype="multipart/form-data">

        <div class="modal-header">
        <h6 class="modal-title font-weight-bold">
          <i class="fas fa-plus mr-1"></i> አዲስ ቀበሌ መዝግብ
        </h6>
        <button type="button" class="close" data-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>

        <!-- Body -->
        <div class="modal-body">
          <div class="form-group mb-2">
            <label for="org_name" class="mb-1"><small class="font-weight-bold">የቀበሌ ስም</small></label>
            <input 
              type="text" 
              id="branch_name" 
              class="form-control form-control-sm" 
              name="kebele_name" 
              placeholder="የቀበሌ ስም ያስገቡ" 
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

