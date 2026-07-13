<?php
use App\Helpers\EthiopianDateHelper; 
use App\Helpers\AuthHelper;
$fiscal_year = AuthHelper::checkFiscalYear();
$is_jobseeker_team_page = true; 
?>
<section class="content">
  <div class="container-fluid">
    <div class="card card-primary card-outline">

      <div class="card-header bg-white d-flex flex-column flex-md-row align-items-md-center">
        <h3 class="card-title">ስራ ፈላጊ አደረጃጀት</h3>
      </div>

      <!-- Team formation panel -->
      <div class="card-body border-bottom">
        <div class="card card-outline card-info mb-0">
          <div class="card-body">
            <form id="team-formation-form">
              <div class="row">
  <div class="col-md-6">
    <div class="form-group mb-2">
      <label class="mb-1" for="sector_id"><small class="font-weight-bold">ዘርፍ</small></label>
      <select class="form-control form-control-sm" id="sector_id" name="sector_id"
              data-cascade-target="sector_name" required>
        <option value="">-- ዘርፍ ይምረጡ --</option>
        <?php foreach ($sectors as $sector): ?>
          <option value="<?= htmlspecialchars($sector['id']) ?>">
            <?= htmlspecialchars($sector['sector']) ?>
          </option>
        <?php endforeach; ?>
      </select>
      <div class="invalid-feedback">እባክዎ ይምረጡ!</div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-group mb-2">
      <label class="mb-1" for="sector_name"><small class="font-weight-bold">ንዑስ ዘርፍ</small></label>
      <select class="form-control form-control-sm" id="sector_name" name="sector_name" required disabled>
        <option value="">-- መጀመሪያ ዘርፍ ይምረጡ --</option>
      </select>
      <div class="invalid-feedback">እባክዎ ንዑስ ዘርፍ ይምረጡ!</div>
    </div>
  </div>
</div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group mb-2">
                    <label class="mb-1" for="organization_type"><small class="font-weight-bold">የአደረጃጀት ዓይነት</small></label>
                    <select class="form-control form-control-sm" id="organization_type" name="organization_type" required>
                      <option value="">-- ይምረጡ --</option>
                      <option value="family">የቤተሰብ</option>
                      <option value="self_interest">በራስ ፍላጎት</option>
                      <option value="government_project">የመንግስት ፕሮጀክት</option>
                      <option value="ngo">NGO</option>
                    </select>
                    <div class="invalid-feedback">እባክዎ የአደረጃጀት ዓይነት ይምረጡ!</div>
                  </div>
                </div>

                <!-- NGO -->
                <div class="col-md-6 org-type-field" id="ngo-field-wrapper" style="display:none;">
                  <div class="form-group mb-2">
                    <label class="mb-1" for="ngo_id"><small class="font-weight-bold">NGO ይምረጡ</small></label>
                    <select class="form-control form-control-sm" id="ngo_id" name="ngo_id">
                      <option value="">-- NGO ይምረጡ --</option>
                      <?php foreach ($ngos ?? [] as $ngo): ?>
                        <option value="<?= htmlspecialchars($ngo['id']) ?>"><?= htmlspecialchars($ngo['name']) ?></option>
                      <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback">እባክዎ NGO ይምረጡ!</div>
                  </div>
                </div>

                <!-- Government project -->
                <div class="col-md-6 org-type-field" id="govt-field-wrapper" style="display:none;">
                  <div class="form-group mb-2">
                    <label class="mb-1" for="jobseeker_count"><small class="font-weight-bold">የስራ ፈላጊዎች ብዛት</small></label>
                    <input type="number" min="1" class="form-control form-control-sm" id="jobseeker_count" name="jobseeker_count">
                    <div class="invalid-feedback">እባክዎ ቁጥር ያስገቡ!</div>
                  </div>
                </div>
              </div>

              <!-- Family / self-interest: pick from existing jobseeker list -->
              <div class="row org-type-field" id="jobseeker-list-wrapper" style="display:none;">
                <div class="col-md-12">
                  <div class="form-group mb-2">
                    <label class="mb-1"><small class="font-weight-bold">ስራ ፈላጊዎች ይምረጡ</small></label>
                    <input type="text" class="form-control form-control-sm mb-2" id="jobseeker-search" placeholder="በስም ወይም በመ/ቁ ይፈልጉ...">
                    <div class="border rounded p-2" style="max-height:250px; overflow-y:auto;" id="jobseeker-checklist">
                      <?php foreach ($jobSeekers as $js): ?>
                        <div class="form-check jobseeker-item"
                             data-name="<?= htmlspecialchars(strtolower($js['first_name'].' '.$js['father_name'].' '.$js['last_name'])) ?>"
                             data-code="<?= htmlspecialchars(strtolower($js['job_seeker_id'])) ?>">
                          <input class="form-check-input" type="checkbox" name="selected_jobseekers[]"
                                 value="<?= htmlspecialchars($js['id']) ?>" id="js_<?= $js['id'] ?>">
                          <label class="form-check-label" for="js_<?= $js['id'] ?>">
                            <?= htmlspecialchars($js['job_seeker_id']) ?> -
                            <?= htmlspecialchars($js['first_name'].' '.$js['father_name'].' '.$js['last_name']) ?>
                          </label>
                        </div>
                      <?php endforeach; ?>
                    </div>
                    <small class="text-muted"><span id="selected-count">0</span> ስራ ፈላጊ(ዎች) ተመርጠዋል</small>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-12 text-right">
                  <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-save"></i> ቡድን መስርት</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- Jobseeker list -->
      <div class="card-body">
        <table id="example1" data-empty-msg="ምንም ስራ ፈላጊ የለም።" class="table table-bordered table-hover small">
          <thead class="thead-light">
            <tr>
              <th>#</th>
              <th>መ/ቁ</th>
              <th>ስም</th>
              <th>ጾታ</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($jobSeekers as $index => $js): ?>
              <tr id="row-<?= $js['id'] ?>">
                <td><?= $index + 1 ?></td>
                <td><?= $js['job_seeker_id'] ?></td>
                <td><?= htmlspecialchars($js['first_name']) . ' ' . htmlspecialchars($js['father_name']) . ' ' . htmlspecialchars($js['last_name']) ?></td>
                <td><?= htmlspecialchars($js['gender']) ?></td>
                <td class="text-center align-middle">

                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>

        <?php
        $basePath = rtrim($_ENV['BASE_URL'], '/') . '/jobseekers-renewal';
        ?>

        <?php if ($totalPages > 1): ?>
          <nav aria-label="Page navigation">
            <ul class="pagination justify-content-end">
              <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                <a class="page-link" href="<?= $basePath ?>?page=<?= $currentPage - 1 ?>">ቀዳሚ</a>
              </li>

              <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                  <a class="page-link" href="<?= $basePath ?>?page=<?= $i ?>"><?= $i ?></a>
                </li>
              <?php endfor; ?>

              <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                <a class="page-link" href="<?= $basePath ?>?page=<?= $currentPage + 1 ?>">ቀጣይ</a>
              </li>
            </ul>
          </nav>
        <?php endif; ?>
      </div>

    </div>
  </div>
</section>
<script nonce="<?php echo $GLOBALS['nonce']; ?>">
   document.addEventListener('DOMContentLoaded', function () {

    const orgType = document.getElementById('organization_type');
    const jobseekerCountInput = document.getElementById('jobseeker_count');
    const checklist = document.getElementById('jobseeker-checklist');
    const selectedCountEl = document.getElementById('selected-count');
    const allItems = Array.from(document.querySelectorAll('.jobseeker-item'));

    function getCheckedCount() {
        return document.querySelectorAll('input[name="selected_jobseekers[]"]:checked').length;
    }

    function getLimit() {
        if (orgType && orgType.value === 'government_project') {
            const val = parseInt(jobseekerCountInput.value, 10);
            return Number.isFinite(val) && val > 0 ? val : null;
        }
        return null;
    }

    // Show only the first N jobseeker rows (N = entered number).
    // Search filter still applies on top of this when the user types in the search box.
    function renderVisibleRows() {
        const limit = getLimit();
        const term = document.getElementById('jobseeker-search')?.value.toLowerCase().trim() || '';

        let shown = 0;
        allItems.forEach(function (item) {
            const matchesSearch = !term || item.dataset.name.includes(term) || item.dataset.code.includes(term);

            if (!matchesSearch) {
                item.style.display = 'none';
                return;
            }

            if (limit !== null && shown >= limit) {
                item.style.display = 'none';
            } else {
                item.style.display = '';
                shown++;
            }
        });
    }

    function updateSelectionState() {
        const checked = getCheckedCount();
        const limit = getLimit();

        selectedCountEl.textContent = limit ? `${checked} / ${limit}` : checked;

        document.querySelectorAll('input[name="selected_jobseekers[]"]').forEach(function (cb) {
            if (limit && !cb.checked && checked >= limit) {
                cb.disabled = true;
            } else {
                cb.disabled = false;
            }
        });
    }

    if (orgType) {
        orgType.addEventListener('change', function () {
            const type = this.value;

            document.querySelectorAll('.org-type-field').forEach(function (field) {
                field.style.display = 'none';
                field.querySelectorAll('select, input').forEach(el => el.required = false);
            });
            document.querySelectorAll('input[name="selected_jobseekers[]"]').forEach(function (cb) {
                cb.checked = false;
                cb.disabled = false;
            });

            if (type === 'ngo') {
                document.getElementById('ngo-field-wrapper').style.display = '';
                document.getElementById('ngo_id').required = true;
            } else if (type === 'government_project') {
                document.getElementById('govt-field-wrapper').style.display = '';
                jobseekerCountInput.required = true;
                document.getElementById('jobseeker-list-wrapper').style.display = '';
            } else if (type === 'family' || type === 'self_interest') {
                document.getElementById('jobseeker-list-wrapper').style.display = '';
            }

            renderVisibleRows();
            updateSelectionState();
        });
    }

    // Re-filter the visible rows every time the number changes
    if (jobseekerCountInput) {
        jobseekerCountInput.addEventListener('input', function () {
            renderVisibleRows();
            updateSelectionState();
        });
    }

    // Search filter also respects the current limit
    const searchInput = document.getElementById('jobseeker-search');
    if (searchInput) {
        searchInput.addEventListener('keyup', renderVisibleRows);
    }

    if (checklist) {
        checklist.addEventListener('change', function (e) {
            if (e.target.name === 'selected_jobseekers[]') {
                updateSelectionState();
            }
        });
    }
});
</script>
