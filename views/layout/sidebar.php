  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="javascript:void(0)" class="brand-link">
      <img src="images/logo.png" alt="Warka Hub Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light small">JCIMS</span>
    </a>
<?php
$first_name  = $_SESSION['user']['first_name'] ?? '';
$father_name = $_SESSION['user']['father_name'] ?? '';

$full_name    = $first_name . ' ' . $father_name;
$display_name = trim($full_name) ?: 'Guest';

// Get initials
$first_initial  = mb_strtoupper(mb_substr(trim($first_name), 0, 1, "UTF-8"), "UTF-8");
$father_initial = mb_strtoupper(mb_substr(trim($father_name), 0, 1, "UTF-8"), "UTF-8");
$initials       = $first_initial . $father_initial ?: 'GU';
?>

<!-- Sidebar -->
<div class="sidebar">
  <div class="user-panel mt-3 pb-3 mb-3 d-flex">
    <div class="image">
      <!-- Initials avatar instead of image -->
      <div class="img-circle elevation-2 d-flex align-items-center justify-content-center"
           style="width:35px; height:35px; background-color:#007bff; color:#fff; font-weight:bold; font-size:14px; line-height:35px; text-align:center;">
        <?php echo $initials; ?>
      </div>
    </div>
    <div class="info ml-2">
      <a href="javascript:void(0)" class="d-block">
        <?php echo mb_convert_case($display_name, MB_CASE_TITLE, "UTF-8"); ?>
      </a>
    </div>
  </div>

      <!-- SidebarSearch Form -->
      <div class="form-inline">
        <div class="input-group" data-widget="sidebar-search">
          <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
          <div class="input-group-append">
            <button class="btn btn-sidebar">
              <i class="fas fa-search fa-fw"></i>
            </button>
          </div>
        </div>
      </div>

<?php
use App\Config\MenuConfig;

$userRole  = $_SESSION['user']['role'] ?? '';
$userLevel = (int)($_SESSION['user']['level'] ?? 0);

$menuItems = MenuConfig::getMenuForRoleAndLevel($userRole, $userLevel);

$currentUrl = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
?>

<!-- Sidebar Menu -->
<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column"
        data-widget="treeview"
        role="menu"
        data-accordion="false">

<?php foreach ($menuItems as $item): ?>

    <?php $hasChildren = !empty($item['children']); ?>

    <li class="nav-item <?= $hasChildren ? '' : 'small' ?>">

        <?php if ($hasChildren): ?>

            <a href="javascript:void(0)"
               class="nav-link <?= $item['class'] ?? '' ?>">

                <i class="nav-icon <?= $item['icon'] ?? 'far fa-circle' ?>"
                   style="font-size:13px;"></i>

                <p style="font-size:13px;">
                    <?= htmlspecialchars($item['label']) ?>
                    <i class="right fas fa-angle-left"></i>
                </p>
            </a>

            <ul class="nav nav-treeview">

                <?php foreach ($item['children'] as $child): ?>

                    <li class="nav-item">

                        <?php if (isset($child['modal'])): ?>

                            <a href="javascript:void(0)"
                               class="nav-link"
                               data-toggle="modal"
                               data-target="<?= $child['modal']['target'] ?>"
                               <?= isset($child['modal']['source'])
                                   ? 'data-source="' . htmlspecialchars($child['modal']['source']) . '"'
                                   : '' ?>>

                                <i class="<?= $child['icon'] ?? 'far fa-circle nav-icon' ?>"
                                   style="font-size:11px;"></i>

                                <p style="font-size:12px;">
                                    <?= htmlspecialchars($child['label']) ?>
                                </p>

                            </a>

                        <?php else: ?>

                            <a href="<?= rtrim($_ENV['BASE_URL'], '/') . $child['url'] ?>"
                               class="nav-link">

                                <i class="<?= $child['icon'] ?? 'far fa-circle nav-icon' ?>"
                                   style="font-size:11px;"></i>

                                <p style="font-size:12px;">

                                    <?= htmlspecialchars($child['label']) ?>

                                    <?php if (!empty($child['badge'])): ?>
                                        <span class="right badge badge-danger">
                                            <?= htmlspecialchars($child['badge']) ?>
                                        </span>
                                    <?php endif; ?>

                                </p>

                            </a>

                        <?php endif; ?>

                    </li>

                <?php endforeach; ?>

            </ul>

        <?php else: ?>

            <a href="<?= rtrim($_ENV['BASE_URL'], '/') . $item['url'] ?>"
               class="nav-link <?= $item['class'] ?? '' ?>">

                <i class="nav-icon <?= $item['icon'] ?? 'far fa-circle' ?>"
                   style="font-size:13px;"></i>

                <p style="font-size:13px;">
                    <?= htmlspecialchars($item['label']) ?>
                </p>

            </a>

        <?php endif; ?>

    </li>

<?php endforeach; ?>

    </ul>
</nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>