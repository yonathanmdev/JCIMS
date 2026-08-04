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

$supportedByLabels = [
    'bemengst'    => 'በመንግስት',
    'bgelu'       => 'በግል',
    'benterprise' => 'በኢንተርፕራይዝ',
    'beproject'   => 'በፕሮጀክት(NGO)',
    'belela'      => 'በሌላ',
];

?>
<section class="content">
  <div class="container-fluid">

    <div class="card card-primary card-outline">
      <div class="card-header bg-white d-flex flex-column flex-md-row align-items-md-center">
     <h3 class="card-title text-center"><strong><?= $isAssociation ? ' የማህበር' : ' የግል' ?> ኢንተርፕራይዝ መረጃ</strong></h3>
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
      <!-- Branch hierarchy path — full width, its own row -->
       <?php if($enterprise['branch_display_path']): ?>
  <div class="mb-4 p-3 bg-light border rounded d-flex align-items-center">
    <i class="bi bi-geo-alt-fill text-primary me-2 fs-5"></i>
    <div>
      <div class="text-muted small fw-semibold text-uppercase">ኢንተርፕራይዙ የተመሰረተበት ቦታ</div>
      <div class="fs-6 fw-medium"><?= htmlspecialchars($enterprise['branch_display_path'] ?? '—') ?></div>
    </div>
  </div>
<?php endif; ?>
        <div class="row">
        <div class="col-md-4 mb-3">
            <strong>የኢንተርፕራይዝ ስም</strong>
            <div><?= htmlspecialchars($enterprise['enterprisename'] ?? '—') ?></div>
          </div>
          
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
            <strong>ምርቱ የሚቀርበው</strong>
            <div><?= htmlspecialchars($enterprise['yemikerb_hager_weys_lewuch'] ?? '—') ?></div>
          </div>

          <?php if (!empty($enterprise['supported_by'])): ?>
            <div class="col-md-4 mb-3">
              <strong>ድጋፍ ያደረገው አካል</strong>
              <div> <?php $supportedByValue = $enterprise['supported_by'] ?? '';
              $supportedByText  = $supportedByLabels[$supportedByValue] ?? '—';?></div>
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


<div class="modal fade" id="editEnterpriseModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <form id="editEnterpriseForm" action="<?= rtrim($_ENV['BASE_URL'], '/') ?>/enterprise-update-process" method="POST">
        <div class="modal-header">
          <h6 class="modal-title font-weight-bold">
            <i class="fas fa-edit mr-1"></i> ኢንተርፕራይዝ መረጃ ማስተካከያ
          </h6>
          <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <input type="hidden" name="enterprise_id" value="<?= htmlspecialchars($enterprise['id']) ?>">
          <input type="hidden" name="enterprise_type" value="<?= $isAssociation ? '1' : '0' ?>">

          <div class="row">

            <div class="col-12 col-sm-6 col-md-3">
              <div class="form-group mb-2">
                <label class="mb-1"><small class="font-weight-bold">ኢንተርፕራይዝ ስም</small></label>
                <input type="text" class="form-control form-control-sm"
                       value="<?= htmlspecialchars($enterprise['enterprisename'] ?? '—') ?>" disabled>
                <small class="text-muted">ኢንተርፕራይዝ ስም እዚህ ላይ ማስተካከል አይቻልም</small>
              </div>
            </div>

            <div class="col-12 col-sm-6 col-md-3">
              <div class="form-group mb-2">
                <label class="mb-1" for="edit_tin_number"><small class="font-weight-bold">የግብር መክፈያ መለያ ቁጥር <span class="text-danger">*</span></small></label>
                <input type="text" class="form-control form-control-sm" id="edit_tin_number" name="tin_number"
                       value="<?= htmlspecialchars($enterprise['tine_number'] ?? '') ?>" required>
              </div>
            </div>

            <div class="col-12 col-sm-6 col-md-3">
              <div class="form-group mb-2">
                <label class="mb-1" for="edit_yeedget_dereja"><small class="font-weight-bold">የእድገት ደረጃ <span class="text-danger">*</span></small></label>
                <select class="form-control form-control-sm" id="edit_yeedget_dereja" name="yeedget_dereja" required>
                  <option value="" disabled>ይምረጡ</option>
                  <?php foreach ($yeedgetDerejaLabels as $val => $label): ?>
                    <option value="<?= $val ?>" <?= (string)($enterprise['yeedget_dereja'] ?? '') === $val ? 'selected' : '' ?>><?= $label ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>

            <div class="col-12 col-sm-6 col-md-3">
              <div class="form-group mb-2">
                <label class="mb-1" for="edit_initial_capital"><small class="font-weight-bold">መነሻ ካፒታል <span class="text-danger">*</span></small></label>
                <input type="number" step="any" class="form-control form-control-sm" id="edit_initial_capital" name="initial_capital"
                       value="<?= htmlspecialchars($enterprise['initial_capital'] ?? '') ?>" required>
              </div>
            </div>

            <div class="col-12 col-sm-6 col-md-3">
              <div class="form-group mb-2">
                <label class="mb-1" for="edit_starting_capital_in_kind"><small class="font-weight-bold">መነሻ ሃብት በአይነት</small></label>
                <input type="text" class="form-control form-control-sm" id="edit_starting_capital_in_kind" name="starting_capital_in_kind"
                       value="<?= htmlspecialchars($enterprise['starting_capital_in_kind'] ?? '') ?>">
              </div>
            </div>

            <div class="col-12 col-sm-6 col-md-3">
              <div class="form-group mb-2">
                <label class="mb-1" for="edit_yehabtu_mnch"><small class="font-weight-bold">የሃብቱ ምንጭ <span class="text-danger">*</span></small></label>
                <select class="form-control form-control-sm" id="edit_yehabtu_mnch" name="yehabtu_mnch" required>
                  <option value="" disabled>ይምረጡ</option>
                  <?php foreach ($yehabtuMnchLabels as $val => $label): ?>
                    <option value="<?= $val ?>" <?= (string)($enterprise['yehabtu_mnch'] ?? '') === $val ? 'selected' : '' ?>><?= $label ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>

            <div class="col-12 col-sm-6 col-md-3">
              <div class="form-group mb-2">
                <label class="mb-1" for="edit_wektawi_yehabt_meten"><small class="font-weight-bold">ወቅታዊ የሃብት መጠን <span class="text-danger">*</span></small></label>
                <input type="text" class="form-control form-control-sm" id="edit_wektawi_yehabt_meten" name="wektawi_yehabt_meten"
                       value="<?= htmlspecialchars($enterprise['wektawi_yehabt_meten'] ?? '') ?>" required>
              </div>
            </div>

            <div class="col-12 col-sm-6 col-md-3">
              <div class="form-group mb-2">
                <label class="mb-1" for="edit_yemrt_ayinet"><small class="font-weight-bold">የምርት ዓይነት <span class="text-danger">*</span></small></label>
                <input type="text" class="form-control form-control-sm" id="edit_yemrt_ayinet" name="yemrt_ayinet"
                       value="<?= htmlspecialchars($enterprise['yemrt_ayinet'] ?? '') ?>" required>
              </div>
            </div>

            <div class="col-12 col-sm-6 col-md-3">
              <div class="form-group mb-2">
                <label class="mb-1" for="edit_yemikerb"><small class="font-weight-bold">ምርቱ የሚቀርበው <span class="text-danger">*</span></small></label>
                <select class="form-control form-control-sm" id="edit_yemikerb" name="yemikerb_hager_weys_lewuch" required>
                  <option value="" disabled>ይምረጡ</option>
                  <?php $currentMarket = $enterprise['yemikerb_hager_weys_lewuch'] ?? ''; ?>
                  <option value="ለሃገር ውስጥ" <?= $currentMarket === 'ለሃገር ውስጥ' ? 'selected' : '' ?>>ለሃገር ውስጥ</option>
                  <option value="ለውጭ ሃገር" <?= $currentMarket === 'ለውጭ ሃገር' ? 'selected' : '' ?>>ለውጭ ሃገር</option>
                </select>
              </div>
            </div>

            <?php if ($isAssociation): ?>
              <div class="col-12 col-sm-6 col-md-3">
                <div class="form-group mb-2">
                  <label class="mb-1"><small class="font-weight-bold">ዘርፍ</small></label>
                  <input type="text" class="form-control form-control-sm" value="<?= htmlspecialchars($linked['sector_name'] ?? '—') ?>" disabled>
                </div>
              </div>
              <div class="col-12 col-sm-6 col-md-3">
                <div class="form-group mb-2">
                  <label class="mb-1"><small class="font-weight-bold">ንዑስ ዘርፍ</small></label>
                  <input type="text" class="form-control form-control-sm" value="<?= htmlspecialchars($linked['subsector_name'] ?? '—') ?>" disabled>
                </div>
              </div>
              <small class="text-muted col-12 mb-2">ዘርፍና ንዑስ ዘርፍ ለማህበር ዓይነት ኢንተርፕራይዞች እዚህ ላይ ማስተካከል አይቻልም</small>
          <?php else: ?>
  <div class="col-12 col-sm-6 col-md-3">
    <div class="form-group mb-2">
      <label class="mb-1" for="edit_sector_id"><small class="font-weight-bold">ዘርፍ <span class="text-danger">*</span></small></label>
      <select class="form-control form-control-sm" id="edit_sector_id" name="sector_id"
              data-cascade-target="edit_subsector_id"
              data-current-subsector="<?= htmlspecialchars($linked['sub_sectorid'] ?? '') ?>" required>
        <option value="" disabled>ይምረጡ</option>
        <?php foreach ($sectorData['sectors'] as $sector): ?>
          <option value="<?= htmlspecialchars($sector['id']) ?>"
            <?= (string)($linked['sector_id'] ?? '') === (string)$sector['id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($sector['name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>

  <div class="col-12 col-sm-6 col-md-3">
    <div class="form-group mb-2">
      <label class="mb-1" for="edit_subsector_id"><small class="font-weight-bold">ንዑስ ዘርፍ <span class="text-danger">*</span></small></label>
      <select class="form-control form-control-sm" id="edit_subsector_id" name="subsector_id" required>
        <option value="<?= htmlspecialchars($linked['sub_sectorid'] ?? '') ?>" selected>
          <?= htmlspecialchars($linked['subsector_name'] ?? '') ?>
        </option>
      </select>
    </div>
  </div>
<?php endif; ?>

            <div class="col-12 col-sm-6 col-md-3">
              <div class="form-group mb-2">
                <label class="mb-1" for="edit_yesra_mesk"><small class="font-weight-bold">የስራ መስክ</small></label>
                <input type="text" class="form-control form-control-sm" id="edit_yesra_mesk" name="yesra_mesk"
                       value="<?= htmlspecialchars($linked['yesra_mesk'] ?? '') ?>" required>
              </div>
            </div>

          </div>

          <p class="text-muted mb-0">ከማስቀመጥዎ በፊት የቀየሩትን መረጃ ትክክለኛነት ያረጋግጡ።</p>
        </div>

        <div class="modal-footer d-flex justify-content-between">
          <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">ዝጋ</button>
          <button type="submit" class="btn btn-primary btn-sm">አስተካክል</button>
        </div>
      </form>
    </div>
  </div>
</div>