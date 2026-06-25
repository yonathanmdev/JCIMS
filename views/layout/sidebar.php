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

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          <li class="nav-item menu-open small">
            <a href="<?= rtrim($_ENV['BASE_URL'], '/') ?>/dashboard" class="nav-link active">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Dashboard
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>

          </li>
          
         
         <?php 
        $userRole = $_SESSION['user']['role'] ?? null; 

        // 1. Only show the "Registration" menu if the user has one of these three roles
          if (in_array($userRole, ['system_admin', 'org_admin', 'team_leader', 'officer'])): 
          ?>
           <?php if ($userRole === 'system_admin' || $userRole === 'org_admin'): ?>
     <li class="nav-item small">
    <a href="javascript:void(0)" class="nav-link">
      <i class="nav-icon fas fa-edit"></i>
      <p>
        መመዝገብ
        <i class="fas fa-angle-left right"></i>
      </p>
    </a>
    <ul class="nav nav-treeview">
      
      <?php if ($userRole === 'system_admin'): ?>
        <li class="nav-item">
            <a href="<?= rtrim($_ENV['BASE_URL'], '/') ?>/register-developer" class="nav-link">
              <i class="nav-icon fas fa-th"></i>
              <p>
                register-developer
                <span class="right badge badge-danger">New</span>
              </p>
            </a>
          </li>
          
        <li class="nav-item">
          <a href="<?= rtrim($_ENV['BASE_URL'], '/') ?>/register-organization" class="nav-link">
            <i class="far fa-circle nav-icon"></i>
            <p>ድርጅት</p>
          </a>
        </li>
        
      <?php elseif ($userRole === 'org_admin'): ?>
        <li class="nav-item">
          <a href="<?= rtrim($_ENV['BASE_URL'], '/') ?>/register-branch" class="nav-link">
            <i class="far fa-circle nav-icon"></i>
            <p>ቅርንጫፍ</p>
          </a>
        </li>
      <?php endif; ?>
         <li class="nav-item">
          <a href="<?= rtrim($_ENV['BASE_URL'], '/') ?>/register-user" class="nav-link">
            <i class="far fa-circle nav-icon"></i>
            <p>ተቆጣጣሪ</p>
          </a>
        </li>
         

    </ul>
  </li>
  <?php endif; ?>
  <?php if ($userRole === 'system_admin'): ?>
  <li class="nav-item small">
    <a href="javascript:void(0)" class="nav-link">
      <i class="nav-icon fas fa-edit"></i>
      <p>
        BSC / Efficiency Settings
        <i class="fas fa-angle-left right"></i>
      </p>
    </a>
    <ul class="nav nav-treeview">
       <?php if ($userRole === 'system_admin'): ?>
<li class="nav-item">
          <a href="<?= rtrim($_ENV['BASE_URL'], '/') ?>/evaluation-settings" class="nav-link">
            <i class="far fa-circle nav-icon"></i>
            <p>BSC / Efficiency Settings</p>
          </a>
        </li>
<?php endif; ?>
    </ul>
  </li>
  <?php endif; ?>
 <?php if ($userRole === 'team_leader' || $userRole === 'officer'): ?>

  <li class="nav-item small">
    <a href="javascript:void(0)" class="nav-link">
      <i class="nav-icon fas fa-edit"></i>
      <p>
        ቡድን መሪ እና መደብ
        <i class="fas fa-angle-left right"></i>
      </p>
    </a>
    <ul class="nav nav-treeview">
              <li class="nav-item">
          <a href="<?= rtrim($_ENV['BASE_URL'], '/') ?>/register-director" class="nav-link">
            <i class="far fa-circle nav-icon"></i>
            <p>ዲይሬክተር</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="<?= rtrim($_ENV['BASE_URL'], '/') ?>/register-position" class="nav-link">
            <i class="far fa-circle nav-icon"></i>
            <p>መደብ</p>
          </a>
        </li>
    </ul>
  </li>
<li class="nav-item small">
    <a href="javascript:void(0)" class="nav-link">
      <i class="nav-icon fas fa-edit"></i>
      <p>
       ሰራተኛ
        <i class="fas fa-angle-left right"></i>
      </p>
    </a>
    <ul class="nav nav-treeview">

        <li class="nav-item">
          <a href="<?= rtrim($_ENV['BASE_URL'], '/') ?>/employee-registration" class="nav-link">
            <i class="far fa-circle nav-icon"></i>
            <p>መመዝገብ</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="<?= rtrim($_ENV['BASE_URL'], '/') ?>/employee-active" class="nav-link">
            <i class="far fa-circle nav-icon"></i>
            <p>ዝርዝር</p>
          </a>
        </li>

    </ul>
  </li>

  <li class="nav-item small">
    <a href="javascript:void(0)" class="nav-link">
        <i class="nav-icon fas fa-edit"></i>
        <p>
            ስራ ልምድ
            <i class="fas fa-angle-left right"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <!-- This link triggers the modal by ID -->
            <a href="javascript:void(0)" class="nav-link" data-toggle="modal" data-target="#employeeSearchModal">
                <i class="far fa-circle nav-icon"></i>
                <p>መመዝገብ/መረጃ</p>
            </a>
        </li>
    </ul>
</li>
<li class="nav-item small">
    <a href="javascript:void(0)" class="nav-link">
        <i class="nav-icon fas fa-edit"></i>
        <p>
            BSC እቅድ እና አፈጻጸም
            <i class="fas fa-angle-left right"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">

        <li class="nav-item">
          <a href="<?= rtrim($_ENV['BASE_URL'], '/') ?>/bsc-plan-management" class="nav-link">
            <i class="far fa-circle nav-icon"></i>
            <p>እቅድ ማያያዝ</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="<?= rtrim($_ENV['BASE_URL'], '/') ?>/efficiency-management" class="nav-link">
            <i class="far fa-circle nav-icon"></i>
            <p>አፈጻጻም</p>
          </a>
        </li> 

    </ul>
</li>

  <li class="nav-item small">
    <a href="javascript:void(0)" class="nav-link">
      <i class="nav-icon fas fa-edit"></i>
      <p>
        የትምህርት እድል
        <i class="fas fa-angle-left right"></i>
      </p>
    </a>
     <ul class="nav nav-treeview">
               <li class="nav-item">
          <a href="<?= rtrim($_ENV['BASE_URL'], '/') ?>/employee-scholarship-onleave" class="nav-link">
            <i class="far fa-circle nav-icon"></i>
            <p>መመዝገቢያ</p>
          </a>
        </li>
    </ul>
    <ul class="nav nav-treeview">
               <li class="nav-item">
          <a href="<?= rtrim($_ENV['BASE_URL'], '/') ?>/employee-scholarship" class="nav-link">
            <i class="far fa-circle nav-icon"></i>
            <p>በት/ት ላይ ያሉ</p>
          </a>
        </li>
    

    </ul>

   <ul class="nav nav-treeview">
    <li class="nav-item">
        <a href="<?= rtrim($_ENV['BASE_URL'], '/') ?>/employee-scholarship-returnee" class="nav-link">
            <i class="far fa-circle nav-icon"></i>
            <p>የተጠናቀቁ የትምህርት እድሎች</p>
        </a>
    </li>
</ul>

  </li>

  <li class="nav-item small">
    <a href="javascript:void(0)" class="nav-link">
        <i class="nav-icon fas fa-edit"></i>
        <p>
            ደረጃ እድገት
            <i class="fas fa-angle-left right"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <!-- This link triggers the modal by ID -->
            <a href="javascript:void(0)" class="nav-link" data-toggle="modal" data-target="#employeeSearchModal" data-source="promotion">
                <i class="far fa-circle nav-icon"></i>
                <p>መመዝገብ</p>
            </a>
        </li>
    </ul>
 </li>

<li class="nav-item small">
    <a href="javascript:void(0)" class="nav-link">
      <i class="nav-icon fas fa-edit"></i>
      <p>
        ዋስትና
        <i class="fas fa-angle-left right"></i>
      </p>
    </a>
     <ul class="nav nav-treeview">
               <li class="nav-item">
          <a href="<?= rtrim($_ENV['BASE_URL'], '/') ?>/employee-warranty" class="nav-link">
            <i class="far fa-circle nav-icon"></i>
            <p>መመዝገቢያ</p>
          </a>
        </li>
    </ul>
    <ul class="nav nav-treeview">
               <li class="nav-item">
          <a href="<?= rtrim($_ENV['BASE_URL'], '/') ?>/employee-has-warranty" class="nav-link">
            <i class="far fa-circle nav-icon"></i>
            <p>ዋስትና ያለባቸው</p>
          </a>
        </li>
    

    </ul>
  </li>


<li class="nav-item small">
    <a href="javascript:void(0)" class="nav-link">
      <i class="nav-icon fas fa-edit"></i>
      <p>
        እዳ እገዳ
        <i class="fas fa-angle-left right"></i>
      </p>
    </a>
    <ul class="nav nav-treeview">
            <li class="nav-item">
          <a href="<?= rtrim($_ENV['BASE_URL'], '/') ?>/employee-debt-suspension-pending" class="nav-link">
            <i class="far fa-circle nav-icon"></i>
            <p>መመዝገቢያ</p>
          </a>
        </li>
    </ul>
    <ul class="nav nav-treeview">
            <li class="nav-item">
          <a href="<?= rtrim($_ENV['BASE_URL'], '/') ?>/employee-debt-suspension" class="nav-link">
            <i class="far fa-circle nav-icon"></i>
            <p>ያለባቸው</p>
          </a>
  </li>
   </ul>
  </li>

<li class="nav-item small">
    <a href="javascript:void(0)" class="nav-link">
        <i class="nav-icon fas fa-edit"></i>
        <p>
            እረፍት
            <i class="fas fa-angle-left right"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <!-- This link triggers the modal by ID -->
            <a href="javascript:void(0)" class="nav-link" data-toggle="modal" data-target="#employeeSearchModal" data-source="onleave">
                <i class="far fa-circle nav-icon"></i>
                <p>መመዝገብ</p>
            </a>
        </li>
    </ul>
 </li>

  <li class="nav-item small">
    <a href="javascript:void(0)" class="nav-link">
      <i class="nav-icon fas fa-edit"></i>
      <p>
        ሪፖርት
        <i class="fas fa-angle-left right"></i>
      </p>
    </a>
    <ul class="nav nav-treeview">
            <li class="nav-item">
          <a href="<?= rtrim($_ENV['BASE_URL'], '/') ?>/report" class="nav-link">
            <i class="far fa-circle nav-icon"></i>
            <p>ማየት</p>
          </a>
        </li>
    </ul>
    
  </li>
  <?php endif; ?>
<?php endif; ?>
     
<li class="nav-item small">
    <a href="javascript:void(0)" class="nav-link text-warning">
      <i class="nav-icon fas fa-trash-restore"></i>
      <p>
        የተሰረዙ
        <i class="fas fa-angle-left right"></i>
      </p>
    </a>
    <ul class="nav nav-treeview">
       <?php if ($userRole === 'system_admin'): ?>
        <li class="nav-item">
          <a href="<?= rtrim($_ENV['BASE_URL'], '/') ?>/organization-deleted-lists" class="nav-link">
            <i class="far fa-building nav-icon text-warning"></i>
            <p>ድርጅቶች</p>
          </a>
        </li>
        <?php endif; ?>
      <?php if ($userRole === 'org_admin'): ?>
         <li class="nav-item">
          <a href="<?= rtrim($_ENV['BASE_URL'], '/') ?>/deleted-branches" class="nav-link">
            <i class="fas fa-code-branch nav-icon text-warning"></i>
            <p>ቅርንጫፎች</p>
          </a>
        </li>
      <?php endif; ?>
       <?php if ($userRole === 'org_admin' || $userRole === 'system_admin'): ?>
         <li class="nav-item">
          <a href="<?= rtrim($_ENV['BASE_URL'], '/') ?>/deleted-users" class="nav-link">
            <i class="fas fa-users nav-icon text-warning"></i>
            <p>ተቆጣጣሪዎች</p>
          </a>
        </li>
        <?php elseif ($userRole === 'team_leader' || $userRole === 'officer'): ?>
         <li class="nav-item">
          <a href="<?= rtrim($_ENV['BASE_URL'], '/') ?>/deleted-directors" class="nav-link">
            <i class="fas fa-building nav-icon text-warning"></i>
            <p>ቡድን መሪ</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="<?= rtrim($_ENV['BASE_URL'], '/') ?>/deleted-positions" class="nav-link">
            <i class="fas fa-building nav-icon text-warning"></i>
            <p>መደብ</p>
          </a>
        </li>
       <?php endif; ?>
    </ul>
</li>

<?php if ($userRole === 'system_admin'): ?>
    <li class="nav-item small">
    <a href="javascript:void(0)" class="nav-link text-info">
      <i class="nav-icon fas fa-archive"></i>
      <p>
        Archived
        <i class="fas fa-angle-left right"></i>
      </p>
    </a>
    <ul class="nav nav-treeview">

        <li class="nav-item">
          <a href="<?= rtrim($_ENV['BASE_URL'], '/') ?>/archived-organizations" class="nav-link">
            <i class="far fa-building nav-icon text-info"></i>
            <p>Organizations</p>
          </a>
        </li>
    <li class="nav-item">
          <a href="<?= rtrim($_ENV['BASE_URL'], '/') ?>/archived-branches" class="nav-link">
             <i class="fas fa-code-branch nav-icon text-info"></i>
            <p>Branches</p>
          </a>
        </li>
        
    </ul>
</li>   
     <?php endif; ?>    
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>