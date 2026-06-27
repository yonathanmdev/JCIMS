<?php $is_organization_page = true; ?>
<!-- Main content -->
<section class="content">
  <div class="container-fluid">

    <!-- Card -->
    <div class="card card-default">
     <div class="card card-primary card-outline">

      <div class="card-body">
        <!-- Example Table (optional) -->
      <table id="example1" data-empty-msg="ምንም ስራ ፈላጊ የለም።" class="table table-bordered table-hover dataTable dtr-inline small" style="color: #000;" aria-describedby="example2_info">
    <thead class="thead-light">
      <tr>
        <th>#</th>
        <th>ስም </th>
        <th>ጾታ</th>
        <th>ቅርንጫፍ</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($jobSeekers)): ?>
        <?php foreach ($jobSeekers as $index => $row): ?>
         <tr id="row-<?= $row['id'] ?>">
            <td><?= $index + 1 ?></td>
            <td><?= htmlspecialchars($row['first_name']).' '.htmlspecialchars($row['father_name']).' '.htmlspecialchars($row['last_name']) ?></td>
           <td><?= htmlspecialchars($row['gender']) ?></td>
           <td><?= htmlspecialchars($row['branch_name']) ?></td>
            <td class="text-center align-middle">
  <div class="btn-group btn-group-sm shadow-sm" role="group">
               <button class="btn btn-outline-secondary btn-sm edit-org" 
                      data-id="<?= $row['id'] ?>"
                      title="አስተካክል"  >
                <i class="fas fa-edit"></i>
              </button> 
              <button class="btn btn-outline-danger btn-sm delete-org"
            data-id="<?= $row['id'] ?>">
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