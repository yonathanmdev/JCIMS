   <?php $is_register_user_page = true; ?>
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
      data-target="#userModal"
    >
      <i class="fas fa-user-plus mr-2"></i>
      ተቆጣጣሪ መዝግብ
    </button>
  </div>

</div>

      </div>

      <div class="card-body">
        <!-- Example Table (optional) -->
      <table id="example1" data-empty-msg="ምንም ተቆጣጣሪ የለም።" class="table table-bordered table-hover dataTable dtr-inline small" style="color: #000;" aria-describedby="example2_info">
    <thead class="thead-light">
      <tr>
        <th>#</th>
        <th>ስም </th>
          <th>Username</th>
          <th>መ/ቤት</th>
        <th>Role</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($users)): ?>
        <?php foreach ($users as $index => $row): ?>
          <tr id="row-<?= htmlspecialchars($row['id']) ?>">
            <td><?= $index + 1 ?></td>
            <td><?= htmlspecialchars($row['first_name'].' '.$row['father_name'].' '.$row['grand_father_name']) ?></td>
            <td><?= htmlspecialchars($row['username']) ?></td>
            <td><?= htmlspecialchars($row['branch_name']) ?></td>
            <td>
<?php
$role = $row['role'];

$roleMap = [
    'system_admin' => 'System Admin',
    'org_admin' => 'Admin',
    'team_leader' => 'ቡድን መሪ',
];

echo htmlspecialchars($roleMap[$role] ?? 'ባለሙያ');
?>
</td>
            <td>
               <button class="btn btn-outline-secondary btn-sm edit-user" 
                      data-id="<?= $row['id'] ?>"  title="አስተካክል"  >
                <i class="fas fa-edit"></i>
              </button> 
              <button class="btn btn-outline-danger btn-sm delete-user" 
                      data-id="<?= $row['id'] ?>"  data-name="<?= htmlspecialchars($row['first_name']).' '.htmlspecialchars($row['father_name']).' '.htmlspecialchars($row['grand_father_name']) ?>"  title="ሰርዝ">

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
<?php include 'partials/edit-user-modal.php'; ?>

<!-- Modal (place OUTSIDE card) -->
<div class="modal fade" id="userModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

     <form id="userForm" action="<?= rtrim($_ENV['BASE_URL'], '/') ?>/register-process" method="POST">

        <!-- 1. Modal Header -->
        <div class="modal-header">
          <h6 class="modal-title font-weight-bold">
            <i class="fas fa-plus mr-2"></i> አዲስ ተቆጣጣሪ መዝግብ
          </h6>
          <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>

        <!-- Body -->
        <div class="modal-body">
  
  <div class="row">
    <div class="col-md-6">
      <div class="form-group mb-2">
        <label for="firstname" class="mb-1"><small class="font-weight-bold">ስም</small></label>
        <input type="text" class="form-control form-control-sm" placeholder="ስም ያስገቡ" name="firstname" required>
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group mb-2">
        <label for="fathername" class="mb-1"><small class="font-weight-bold">የአባት ስም</small></label>
        <input type="text" class="form-control form-control-sm" placeholder="የአባት ስም ያስገቡ" name="fathername" required>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-6">
      <div class="form-group mb-2">
        <label for="grandfathername" class="mb-1"><small class="font-weight-bold">የአያት ስም</small></label>
        <input type="text" class="form-control form-control-sm" placeholder="የአያት ስም ያስገቡ" name="grandfathername" required>
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group mb-2">
        <label for="phone" class="mb-1"><small class="font-weight-bold">ስልክ ቁጥር</small></label>
        <input type="text" class="form-control form-control-sm" placeholder="ስልክ ቁጥር ያስገቡ" name="phone" required>
      </div>
    </div>
  </div>

 <div class="row">
  <div class="col-md-6">
    <div class="form-group">
      <label for="roleSelector" class="mb-1"><small class="font-weight-bold">Role</small></label>
      <?php
        $isSystemAdmin    = isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'system_admin';
        $isOrgAdmin       = isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'org_admin';
        $sessionBranchId  = $_SESSION['user']['branch_id'] ?? '';
        $branchNameString = htmlspecialchars($branchName['name'] ?? $branchName->name ?? '');
      ?>

      <select class="form-control" id="roleSelector"
              <?= $isSystemAdmin ? 'disabled' : 'name="role"' ?> required>

        <?php if ($isSystemAdmin): ?>
          <option value="org_admin" selected>Admin</option>

        <?php elseif ($isOrgAdmin): ?>
          <option value="">-- ምረጥ --</option>
          <option value="org_admin">Admin</option>
          <option value="team_leader">ቡድን መሪ</option>
          <option value="officer">ባለሙያ</option>
        <?php endif; ?>
      </select>

      <?php if ($isSystemAdmin): ?>
        <input type="hidden" name="role" value="org_admin">
      <?php endif; ?>
    </div>
  </div>

  <div class="col-md-6">
    <div class="form-group mb-2">
      <label for="orgSelector" class="mb-1"><small class="font-weight-bold">የተቁሙ ስም</small></label>

      <!-- no name here — hidden input is the only POST source -->
      <select class="form-control" id="orgSelector"
              data-session-org="<?= htmlspecialchars($sessionBranchId) ?>"
              data-session-branch-name="<?= $branchNameString ?>">
        <option value="">-- ተቁሙን ይምረጡ --</option>
        <?php if ($isSystemAdmin): ?>
          <?php foreach ($organizations as $row): ?>
            <option value="<?= htmlspecialchars($row['id']) ?>">
              <?= htmlspecialchars($row['name']) ?>
            </option>
          <?php endforeach; ?>
        <?php endif; ?>
      </select>

   <!-- for system_admin: posts selected organization -->
<?php if ($isSystemAdmin): ?>
  <input type="hidden" name="organization_id" id="orgSelectorHidden">
<?php else: ?>
  <!-- for org_admin: posts selected branch -->
  <input type="hidden" name="branch_id" id="orgSelectorHidden">
<?php endif; ?>
    </div>
  </div>
</div>

  <div class="row">
    <div class="col-md-6">
      <div class="form-group mb-2">
         <label for="email" class="mb-1"><small class="font-weight-bold">ኢሜይል</small></label>
        <input type="email" class="form-control form-control-sm" placeholder="ኢሜይል ያስገቡ" name="email">
      </div>
    </div>
    <div class="col-md-6">
  <div class="form-group mb-2">
    <label for="password" class="mb-1"><small class="font-weight-bold">Password</small></label>
    <input type="password" class="form-control form-control-sm" placeholder="Password ያስገቡ" name="password" required>
  </div>
  </div>
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
