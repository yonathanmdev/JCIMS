<?php
use App\Helpers\AuthHelper;

// Assuming $data is passed from the controller, which contains: 
// ['data' => [...], 'total' => X, 'page' => X, 'last_page' => X]
$teams = $data['data'] ?? [];
$totalCount = $data['total'] ?? 0;
$currentPage = $data['page'] ?? 1;
$totalPages = $data['last_page'] ?? 1;
$offset = ($currentPage - 1) * ($data['per_page'] ?? 15);
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

        <table class="table table-bordered table-hover small mt-3">
          <thead class="thead-light">
            <tr>
              <th>#</th>
              <th>የማህበሩ ስም</th>
              <th>የአደረጃጀት ዓይነት</th>
              <th>ዘርፍ/ንዑስ ዘርፍ</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($teams)): ?>
              <?php foreach ($teams as $index => $team): ?>
                <tr>
                  <td><?= $offset + $index + 1 ?></td>
                  <td><?= htmlspecialchars($team['association_name']) ?></td>
                  <td><?= htmlspecialchars($team['project_type']) ?></td>
                  <td><?= htmlspecialchars($team['sub_sector']) ?></td>
                  <td class="text-center align-middle">
                    <button class="btn btn-outline-primary btn-sm" title="ሙሉ መረጃ ይመልከቱ">
                      <i class="fas fa-eye"></i>
                    </button>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="5" class="text-center text-muted py-3">ምንም የተደራጀ ቡድን አልተገኘም።</td>
              </tr>
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