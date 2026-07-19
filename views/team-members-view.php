<?php
use App\Helpers\EthiopianDateHelper; 
use App\Helpers\AuthHelper;
$fiscal_year = AuthHelper::checkFiscalYear();
$is_team_member_add_page = true;
$members = $team['members'] ?? [];
?>

<section class="content">
  <div class="container-fluid">

    <div class="card card-primary card-outline">
      <div class="card-header bg-white d-flex flex-column flex-md-row align-items-md-center">
        <h3 class="card-title">የማህበሩ ስም፡ <?= htmlspecialchars($team['association_name']) ?></h3>
        <div class="ml-md-auto mt-2 mt-md-0">
          <a href="<?= rtrim($_ENV['BASE_URL'], '/') ?>/team-lists" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> 
          </a>
        </div>
      </div>

      <div class="card-body">
        <div class="row">
          <div class="col-md-4 mb-3">
            <strong>ዘርፍ (Sector)</strong>
            <div><?= htmlspecialchars($team['sector'] ?? '—') ?></div>
          </div>
          <div class="col-md-4 mb-3">
            <strong>ንዑስ ዘርፍ (Sub-sector)</strong>
            <div><?= htmlspecialchars($team['subsector'] ?? '—') ?></div>
          </div>
           <div class="col-md-4 mb-3">
            <strong>የስራ መስክ </strong>
            <div><?= htmlspecialchars($team['yesra_mesk'] ?? '—') ?></div>
          </div>
          <div class="col-md-4 mb-3">
            <strong>የአደረጃጀት ዓይነት </strong>
            <div><?= htmlspecialchars($team['project_type'] ?? '—') ?></div>
          </div>
          <div class="col-md-4 mb-3">
            <strong>የስራ አስኪያጅ ስልክ ቁጥር</strong>
            <div><?= htmlspecialchars($team['manager_phone'] ?? '—') ?></div>
          </div>
          <div class="col-md-4 mb-3">
    <strong>የተመዘገበበት ቀን</strong>
    <div>
        <?php 
        $createdat = $team['created_at']; $createdatdateParts = explode('-', $createdat);
$ethiopianDate = EthiopianDateHelper::toEthCalendar($createdatdateParts[2], $createdatdateParts[1], $createdatdateParts[0]);?>
     
        <?= EthiopianDateHelper::getMonthName($ethiopianDate['month']) ?>
        <?= $ethiopianDate['day'] ?>
        <?= $ethiopianDate['year'] ?>
    </div>
</div>
        </div>

        <hr>

        <div class="row">
          <div class="col-md-3 mb-3">
            <strong>የቡድን መሪ</strong>
            <div><?= htmlspecialchars(trim($team['teamleader_name'] ?? '') !== '' ? $team['teamleader_name'] : '—') ?></div>
          </div>
          <div class="col-md-3 mb-3">
            <strong>ም/የቡድን መሪ</strong>
            <div><?= htmlspecialchars(trim($team['vice_teamleader_name'] ?? '') !== '' ? $team['vice_teamleader_name'] : '—') ?></div>
          </div>
          <div class="col-md-3 mb-3">
            <strong>ገንዘብ ያዥ</strong>
            <div><?= htmlspecialchars(trim($team['treasurer_name'] ?? '') !== '' ? $team['treasurer_name'] : '—') ?></div>
          </div>
          <div class="col-md-3 mb-3">
            <strong>ግዥ</strong>
            <div><?= htmlspecialchars(trim($team['procurement_name'] ?? '') !== '' ? $team['procurement_name'] : '—') ?></div>
          </div>
        </div>
      </div>
    </div>

    <div class="card card-primary card-outline">
      <div class="card-header bg-white d-flex flex-column flex-md-row align-items-md-center">
        <h3 class="card-title">አባላት</h3>
        <div class="ml-md-auto mt-2 mt-md-0">
          <button type="button"
                  id="addMemberBtn"
                  class="btn btn-primary btn-sm"
                  data-team-id="<?= $team['id'] ?>"
                  data-toggle="modal"
                  data-target="#addMemberModal">
            <i class="fas fa-user-plus"></i> አባል ጨምር
          </button>
        </div>
      </div>

      <div class="card-body">
        <small class="text-muted">
          ጠቅላላ አባላት ብዛት፦
          <span class="badge badge-primary" id="membersCountBadge"><?= count($members) ?></span>
        </small>

        <table class="table table-bordered table-hover small mt-3">
          <thead class="thead-light">
            <tr>
              <th>#</th>
              <th>የስራ ፈላጊ መ/ቁ</th>
              <th>ሙሉ ስም</th>
              <th>ጾታ</th>
              <th>ስልክ ቁጥር</th>
            </tr>
          </thead>
          <tbody id="membersTableBody">
            <?php if (!empty($members)): ?>
              <?php foreach ($members as $index => $m): ?>
                <tr>
                  <td><?= $index + 1 ?></td>
                  <td><?= htmlspecialchars($m['job_seeker_id']) ?></td>
                  <td>
                    <?= htmlspecialchars(
                        trim(($m['first_name'] ?? '') . ' ' . ($m['father_name'] ?? '') . ' ' . ($m['last_name'] ?? ''))
                    ) ?>
                  </td>
                  <td><?= htmlspecialchars($m['gender'] ?? '—') ?></td>
                  <td><?= htmlspecialchars($m['phone_number'] ?? '—') ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr class="empty-members-row">
                <td colspan="5" class="text-center text-muted py-3">አባላት አልተመዘገቡም</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
        <!-- No pagination here — this is a single team's member list, not paginated. -->
      </div>
    </div>

  </div>
</section>

<!-- Add Member Modal -->
<div class="modal fade" id="addMemberModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-user-plus mr-1"></i> አባል ጨምር</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input type="text"
               id="addMemberSearchInput"
               class="form-control mb-3"
               placeholder="ስም ወይም መ/ቁ ይፈልጉ... (Search by name or ID)"
               autocomplete="off">
        <div id="addMemberSearchResults"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">ዝጋ</button>
      </div>
    </div>
  </div>
</div>