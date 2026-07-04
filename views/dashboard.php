<?php
// Ensure session and variables are ready
$is_dashboard = true;
$branch_id = $_SESSION['user']['branch_id'] ?? null;
$role = $_SESSION['user']['role'] ?? ''; 
?>

<link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">

<section class="content">
  <div class="container-fluid">

    <div class="row mb-3">
      <div class="col-12">
        <div class="card card-default shadow-sm border-0" style="border-radius: 12px; background: #ffffff;">
          <div class="card-body py-4 px-4 d-flex align-items-center justify-content-start" style="gap: 24px;">
            
            <?php if (!empty($_SESSION['user']['logo_url'])): ?>
              <img src="<?= rtrim($_ENV['BASE_URL'] ?? '', '/') ?>/serve-file?file=<?= htmlspecialchars($_SESSION['user']['logo_url']) ?>&type=image" 
                   alt="<?= htmlspecialchars($_SESSION['user']['alt_name'] ?? '') ?>"    class="JCIMS-bureau-logo" style="height: 65px; width: auto; object-fit: contain;">
            <?php else: ?>
              <img src="images/logo_transparent.png" 
                   alt="System Logo" 
                   class="JCIMS-bureau-logo" 
                   style="height: 65px; width: auto; object-fit: contain; background: transparent !important; flex-shrink: 0;">
            <?php endif; ?>
            
            <div class="JCIMS-title-container">
              <p class="JCIMS-title-sub text-muted font-weight-bold" style="font-size: 13px; color: #4a5568; letter-spacing: 0.4px; margin: 0;">
               ስራ እድል ፈጠራ እና ኢንተርፕራይዝ ምስረታ ሲስተም
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row">

      <?php if ($role === 'system_admin' || $role === 'org_admin'): ?>
        
        <div class="col-md-4 col-sm-6 mb-3">
          <div class="report-type-card card card-outline card-info h-100 shadow-sm" style="border-radius: 12px;">
            <div class="card-body text-center py-4 d-flex flex-column align-items-center justify-content-center position-relative">
              <span class="badge badge-info float-right position-absolute px-2 py-1" style="top: 12px; right: 12px; font-size: 11px; border-radius: 20px;">
                <?= number_format($total_users) ?> Total
              </span>
              <i class="fas fa-users-cog fa-2x text-info mb-3 mt-2"></i>
              <h6 class="font-weight-bold mb-1" style="color: #1a365d; font-size: 15px;">ጠቅላላ ተጠቃሚዎች</h6>
            </div>
          </div>
        </div>

        <div class="col-md-4 col-sm-6 mb-3">
          <div class="report-type-card card card-outline card-success h-100 shadow-sm" style="border-radius: 12px;">
            <div class="card-body text-center py-4 d-flex flex-column align-items-center justify-content-center position-relative">
              <span class="badge badge-success float-right position-absolute px-2 py-1" style="top: 12px; right: 12px; font-size: 11px; border-radius: 20px;">
                <?= number_format($active_users) ?> Active
              </span>
              <i class="fas fa-user-check fa-2x text-success mb-3 mt-2"></i>
              <h6 class="font-weight-bold mb-1" style="color: #1a365d; font-size: 15px;">Active ተቆጣጣሪዎች</h6>
            </div>
          </div>
        </div>

        <div class="col-md-4 col-sm-6 mb-3">
          <div class="report-type-card card card-outline card-secondary h-100 shadow-sm" style="border-radius: 12px;">
            <div class="card-body text-center py-4 d-flex flex-column align-items-center justify-content-center position-relative">
              <span class="badge badge-secondary float-right position-absolute px-2 py-1" style="top: 12px; right: 12px; font-size: 11px; border-radius: 20px;">
                <?= number_format($total_branches) ?> Managed
              </span>
              <i class="fas fa-network-wired fa-2x text-secondary mb-3 mt-2"></i>
              <h6 class="font-weight-bold mb-1" style="color: #1a365d; font-size: 15px;">የቅርንጫፎች ብዛት</h6>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <?php if ($role === 'team_leader' || $role === 'officer'): ?>

        <div class="col-md-4 col-sm-6 mb-3">
          <div class="report-type-card card card-outline card-primary h-100 shadow-sm" style="border-radius: 12px;">
            <div class="card-body text-center py-4 d-flex flex-column align-items-center justify-content-center position-relative">
              <span class="badge badge-primary float-right position-absolute px-2 py-1" style="top: 12px; right: 12px; font-size: 11px; border-radius: 20px;">
                <?= number_format($total_employees) ?> Active
              </span>
              <i class="fas fa-users fa-2x text-primary mb-3 mt-2"></i>
              <h6 class="font-weight-bold mb-1" style="color: #1a365d; font-size: 15px;">ጠቅላላ ሰራተኛ ብዛት</h6>
            </div>
          </div>
        </div>

        <div class="col-md-4 col-sm-6 mb-3">
          <div class="report-type-card card card-outline card-success h-100 shadow-sm" style="border-radius: 12px;">
            <div class="card-body text-center py-4 d-flex flex-column align-items-center justify-content-center position-relative">
              <span class="badge badge-success float-right position-absolute px-2 py-1" style="top: 12px; right: 12px; font-size: 11px; border-radius: 20px;">
                <?= number_format($studyleave_employees) ?> Study
              </span>
              <i class="fas fa-graduation-cap fa-2x text-success mb-3 mt-2"></i>
              <h6 class="font-weight-bold mb-1" style="color: #1a365d; font-size: 15px;">በትምህርት ላይ ያሉ</h6>
            </div>
          </div>
        </div>

        <div class="col-md-4 col-sm-6 mb-3">
          <div class="report-type-card card card-outline card-warning h-100 shadow-sm" style="border-radius: 12px;">
            <div class="card-body text-center py-4 d-flex flex-column align-items-center justify-content-center position-relative">
              <span class="badge badge-warning float-right position-absolute px-2 py-1" style="top: 12px; right: 12px; font-size: 11px; border-radius: 20px; color: #ffffff;">
                <?= number_format($onleave_employees) ?> Leave
              </span>
              <i class="fas fa-calendar-check fa-2x text-warning mb-3 mt-2"></i>
              <h6 class="font-weight-bold mb-1" style="color: #1a365d; font-size: 15px;">እረፍት ላይ ያሉ</h6>
            </div>
          </div>
        </div>

      <?php endif; ?>

    </div>
  </div>
</section>