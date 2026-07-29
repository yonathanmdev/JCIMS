<?php
use App\Helpers\EthiopianDateHelper;
use App\Helpers\AuthHelper;
$is_enterprise_details_page = true;
$isAssociation = $enterprise['type'] === 'association';
$linked = $enterprise['linked_entity'] ?? [];
$members = $enterprise['members'] ?? [];
?>
<?php
$yeedgetDerejaLabels = [
    '0' => 'ጥቃቅን ጀማሪ',
    '1' => 'ጥቃቅን ታዳጊ',
    '2' => 'ጥቃቅን የበቃ',
    '3' => 'አነስተኛ ጀማሪ',
    '4' => 'አነስተኛ ታዳጊ',
    '5' => 'አነስተኛ የበቃ',
    '6' => 'ታዳጊ መካከለኛ',
];

$yehabtuMnchLabels = [
    '0' => 'በራስ ተቀማጭ',
    '1' => 'ከቤተሰብ',
    '2' => 'ከመንግስት',
    '3' => 'ብድር',
];
?>
<section class="content">
  <div class="container-fluid">

    <div class="card card-primary card-outline">
      <div class="card-header bg-white d-flex flex-column flex-md-row align-items-md-center">
        <h3 class="card-title">የኢንተርፕራይዝ ስም፡ <?= htmlspecialchars($enterprise['enterprisename']) ?><?= $isAssociation ? ' የማህበር' : ' የግል' ?> ኢንተርፕራይዝ መረጃ</h3>
        <div class="ml-md-auto mt-2 mt-md-0">
          <?php if (AuthHelper::hasRole(['team_leader', 'officer'], [3, 4]) && $enterprise['branch_id'] === $_SESSION['user']['branch_id']): ?>
            <button type="button"
                    class="editEnterpriseBtn btn btn-outline-primary btn-sm"
                    data-toggle="modal"
                    data-target="#editEnterpriseModal"
                    data-id="<?= htmlspecialchars($enterprise['id']) ?>">
              <i class="fas fa-edit"></i> አስተካክል
            </button>
          <?php endif; ?>
          <a href="<?= rtrim($_ENV['BASE_URL'], '/') ?>/enterprise-lists" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left"></i>
          </a>
        </div>
      </div>

      <div class="card-body">
        <div class="row">
          <div class="col-md-4 mb-3">
            <strong>TIN</strong>
            <div><?= htmlspecialchars($enterprise['tine_number'] ?? '—') ?></div>
          </div>
          <div class="col-md-4 mb-3">
            <strong>የእድገት ደረጃ</strong>
           <div><?= htmlspecialchars($yeedgetDerejaLabels[$enterprise['yeedget_dereja'] ?? null] ?? '—') ?></div>
          </div>
          <div class="col-md-4 mb-3">
            <strong>የመነሻ ካፒታል</strong>
            <div><?= htmlspecialchars($enterprise['initial_capital'] ?? '—') ?></div>
          </div>
          <div class="col-md-4 mb-3">
            <strong>በዓይነት የመነሻ ካፒታል</strong>
            <div><?= htmlspecialchars($enterprise['starting_capital_in_kind'] ?? '—') ?></div>
          </div>
          <div class="col-md-4 mb-3">
            <strong>የሃብት ምንጭ</strong>
           <div><?= htmlspecialchars($yehabtuMnchLabels[$enterprise['yehabtu_mnch'] ?? null] ?? '—') ?></div>
          </div>
          <div class="col-md-4 mb-3">
            <strong>ወቅታዊ የሃብት መጠን</strong>
            <div><?= htmlspecialchars($enterprise['wektawi_yehabt_meten'] ?? '—') ?></div>
          </div>
          <div class="col-md-4 mb-3">
            <strong>የምርት አይነት</strong>
            <div><?= htmlspecialchars($enterprise['yemrt_ayinet'] ?? '—') ?></div>
          </div>
          <div class="col-md-4 mb-3">
            <strong>የገበያ አድራሻ</strong>
            <div><?= htmlspecialchars($enterprise['yemikerb_hager_weys_lewuch'] ?? '—') ?></div>
          </div>

          <?php if (!empty($enterprise['supported_by'])): ?>
            <div class="col-md-4 mb-3">
              <strong>የተደገፈው በ</strong>
              <div><?= htmlspecialchars($enterprise['supported_by']) ?></div>
            </div>
            <?php if (!empty($enterprise['supporter_NGO'])): ?>
              <div class="col-md-4 mb-3">
                <strong>ደጋፊ NGO</strong>
                <div><?= htmlspecialchars($enterprise['supporter_NGO']) ?></div>
              </div>
            <?php endif; ?>
            <?php if (!empty($enterprise['supporter_other'])): ?>
              <div class="col-md-4 mb-3">
                <strong>ሌላ ደጋፊ</strong>
                <div><?= htmlspecialchars($enterprise['supporter_other']) ?></div>
              </div>
            <?php endif; ?>
            <?php if (!empty($enterprise['supported_items'])): ?>
              <div class="col-md-4 mb-3">
                <strong>የተደገፉ ዕቃዎች</strong>
                <div><?= htmlspecialchars($enterprise['supported_items']) ?></div>
              </div>
            <?php endif; ?>
          <?php endif; ?>

          <div class="col-md-4 mb-3">
            <strong>የተቋቋመበት ቀን</strong>
            <div>
              <?php if (!empty($enterprise['established_date'])):
                  $parts = explode('-', $enterprise['established_date']);
                  $eth = EthiopianDateHelper::toEthCalendar($parts[2], $parts[1], $parts[0]);
              ?>
                <?= EthiopianDateHelper::getMonthName($eth['month']) ?> <?= $eth['day'] ?> <?= $eth['year'] ?>
              <?php else: ?>
                —
              <?php endif; ?>
            </div>
          </div>
          <div class="col-md-4 mb-3">
              <strong>የተቋቋመበት አካባቢ</strong>
              <div><?= htmlspecialchars($linked['yetederajubet_akababi'] ?? '—') ?></div>
            </div>
             <div class="col-md-4 mb-3">
              <strong>ዘርፍ</strong>
              <div><?= htmlspecialchars($linked['sector_name'] ?? '—') ?></div>
            </div>
            <div class="col-md-4 mb-3">
              <strong>ንዑስ ዘርፍ</strong>
              <div><?= htmlspecialchars($linked['subsector_name'] ?? '—') ?></div>
            </div>
              <div class="col-md-4 mb-3">
              <strong>የስራ መስክ</strong>
              <div><?= htmlspecialchars($linked['yesra_mesk'] ?? '—') ?></div>
            </div>
            <div class="col-md-4 mb-3">
              <strong>ኢንተርፕሪዙ የተመሰረተበት ሁኔታ</strong>
              <div><?= htmlspecialchars($linked['yeaderejajet_ayinet'] ?? '—') ?></div>
            </div>
        </div>
      

    <?php if ($isAssociation): ?>
      <!-- Association / Group details -->
          <div class="row">
            <div class="col-md-4 mb-3">
              <strong>የስራ አስኪያጅ ስልክ ቁጥር</strong>
              <div><?= htmlspecialchars($linked['manager_phone'] ?? '—') ?></div>
            </div>
          </div>
          <hr>
          <div class="row">
            <div class="col-md-3 mb-3">
              <strong>ሊቀመንበር</strong>
              <div><?= htmlspecialchars($linked['teamleader_id'] ?? '—') ?></div>
            </div>
            <div class="col-md-3 mb-3">
              <strong>ም/ሊቀመንበር</strong>
              <div><?= htmlspecialchars($linked['vice_teamleader_id'] ?? '—') ?></div>
            </div>
            <div class="col-md-3 mb-3">
              <strong>ገንዘብ ያዥ</strong>
              <div><?= htmlspecialchars($linked['treasurer'] ?? '—') ?></div>
            </div>
            <div class="col-md-3 mb-3">
              <strong>ግዥ</strong>
              <div><?= htmlspecialchars($linked['procurement'] ?? '—') ?></div>
            </div>
          </div>
        </div>
      </div>
    <?php endif; ?>

    <!-- Members -->
    <div class="card card-primary card-outline">
 
    <div class="card-header bg-white d-flex flex-column flex-md-row align-items-md-center">
        <h3 class="card-title"><?= $isAssociation ? ' አባላት' : '' ?></h3>
      </div>
      
      <div class="card-body">
        <small class="text-muted">
          <?= $isAssociation ? ' ጠቅላላ አባላት ብዛት፦' : ' ' ?>
          <span class="badge badge-primary">   <?= $isAssociation ? count($members) : ' ' ?></span>
        </small>
        <table class="table table-bordered table-hover small mt-3">
          <thead class="thead-light">
            <tr>
              <th>#</th>
              <th>የስራ ፈላጊ መ/ቁ</th>
              <th>ሙሉ ስም</th>
              <th>ጾታ</th>
              <th>ስልክ ቁጥር</th>
              <th> Action</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($members)): ?>
              <?php foreach ($members as $index => $m): ?>
                <tr>
                  <td><?= $index + 1 ?></td>
                  <td><?= htmlspecialchars($m['job_seeker_id']) ?></td>
                  <td><?= htmlspecialchars(trim(($m['first_name'] ?? '') . ' ' .($m['father_name'] ?? '').' '.($m['last_name'] ?? ''))) ?></td>
                  <td><?= htmlspecialchars($m['gender'] ?? '—') ?></td>
                  <td><?= htmlspecialchars($m['phone_number'] ?? '—') ?></td>
                                  <td class="text-center align-middle">
        <?php if (AuthHelper::hasRole(['team_leader', 'officer'], [3, 4]) && $m['branch_id'] === $_SESSION['user']['branch_id'] && $isAssociation && $m['member']===1): ?>
 <a href="<?= $_ENV['BASE_URL'] ?>/team-members-view/<?= urlencode($linked['id']) ?>"
   class="btn btn-outline-danger btn-sm"
      title="አባል አጥፋ">
   <i class="fas fa-trash-alt me-1"></i>
</a> <?php endif; ?>   
            </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="5" class="text-center text-muted py-3">አባላት አልተመዘገቡም</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</section>