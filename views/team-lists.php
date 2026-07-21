<?php
use App\Helpers\AuthHelper;
$is_team_list_page = true;
$teams = $data['teams'] ?? [];
$totalCount = $data['pagination']['total'] ?? 0;
$currentPage = $data['pagination']['current'] ?? 1;
$totalPages = $data['pagination']['last_page'] ?? 1;
$offset = ($currentPage - 1) * 15;
?>

<section class="content">
  <div class="container-fluid">
    <div class="card card-primary card-outline">
      <div class="card-header bg-white d-flex flex-column flex-md-row align-items-md-center">
        <h3 class="card-title">የተደራጁ ቡድኖች ዝርዝር</h3>
      </div>

      <div class="card-body">
        <small class="text-muted">
          ጠቅላላ የተደራጁ ቡድኖች ብዛት፦
          <span class="badge badge-primary"><?= $totalCount ?></span>
        </small>

        <table class="table table-bordered table-hover small mt-3" id="example1">
          <thead class="thead-light">
            <tr>
              <th>#</th>
              <th>የማህበሩ መለያ</th>
              <th>የማህበሩ ስም</th>
              <th>የአደረጃጀት ዓይነት</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($teams)): ?>
              <?php foreach ($teams as $index => $team): ?>
                <tr>
                  <td><?= $offset + $index + 1 ?></td>
                  <td><?= htmlspecialchars($team['table_id']) ?></td>
                  <td><?= htmlspecialchars($team['association_name']) ?></td>
                  <td><?= htmlspecialchars($team['project_type'] ?? '') ?></td>
                  <td class="text-center align-middle">
 <a href="<?= $_ENV['BASE_URL'] ?>/team-members-view/<?= urlencode($team['id']) ?>"
   class="btn btn-outline-primary btn-sm"
   title="ሙሉ መረጃ ይመልከቱ">
    <i class="fas fa-eye"></i>
</a>
                       <?php 
// Assume $myBranchId is available here, e.g., from a session or user object
// $myBranchId = AuthHelper::getUserBranchId(); // Example of how you might get it

if (AuthHelper::hasRole(['team_leader', 'officer'], [3, 4]) && $team['branch_id'] === $_SESSION['user']['branch_id']): ?>
    <button class="btn btn-outline-danger btn-sm delete-team-btn"
        data-id="<?= htmlspecialchars($team['id']) ?>"
        data-name="<?= htmlspecialchars($team['association_name']) ?>"
        title="አጥፋ">
   <i class="fas fa-trash-alt me-1"></i>
</button>
<?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
          <nav aria-label="Page navigation" class="mt-3">
            <ul class="pagination justify-content-end">
              <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                <a class="page-link" href="<?= rtrim($_ENV['BASE_URL'], '/') ?>/team-lists?page=<?= $currentPage - 1 ?>">ቀዳሚ</a>
              </li>
              <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                  <a class="page-link" href="<?= rtrim($_ENV['BASE_URL'], '/') ?>/team-lists?page=<?= $i ?>"><?= $i ?></a>
                </li>
              <?php endfor; ?>
              <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                <a class="page-link" href="<?= rtrim($_ENV['BASE_URL'], '/') ?>/team-lists?page=<?= $currentPage + 1 ?>">ቀጣይ</a>
              </li>
            </ul>
          </nav>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>
