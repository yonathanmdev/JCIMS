<?php
use App\Helpers\AuthHelper;

if (!isset($title)) { $title = 'የኢ-መደበኛ ንግድ ተሰማሪዎች ዝርዝር'; }
if (!isset($traders)) { $traders = []; }
?>

<section class="content">
  <div class="container-fluid">
    <div class="card card-default">
      <div class="card card-primary card-outline">
        <div class="card-body">
          
          <!-- የገጽ ራስጌ እና አዲስ መመዝገቢያ አዝራር -->
          <div class="row mb-3 align-items-center">
            <div class="col-md-8">
              <h1 class="h3 mb-0 text-gray-800"><?= htmlspecialchars($title) ?></h1>
            </div>
            <div class="col-md-4 text-md-right mt-2 mt-md-0">
              <a href="informal-entrerprise-regstration" class="btn btn-primary btn-sm shadow-sm">
                <i class="fas fa-plus-circle mr-1"></i> አዲስ መዝግብ
              </a>
            </div>
          </div>

          <!-- የውጤት መልዕክቶች ማሳያ -->
          <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              <?= $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
          <?php endif; ?>

          <!-- ዋናው የዳታ ሰንጠረዥ -->
          <div class="table-responsive">
            <table id="example1" class="table table-bordered table-hover small text-center align-middle">
              <thead class="bg-light">
                <tr>
                  <th>ተ.ቁ</th>
                  <th>ሙሉ ስም</th>
                  <th>ጾታ</th>
                  <th>ዕድሜ</th>
                  <th>ስልክ</th>
                  <th>መኖሪያ (ዞን/ወረዳ/ቀበሌ)</th>
                  <th>ንዑስ ዘርፍ / የሥራ መደብ</th>
                  <th>ቦታ</th>
                  <th>የተመዘገበበት ቀን</th>
                  <th>ድርጊት</th>
                </tr>
              </thead>
              <tbody>
                <?php $i = 1; foreach ($traders as $row): ?>
                  <tr>
                    <td><?= $i++ ?></td>
                    <td class="text-left font-weight-bold"><?= htmlspecialchars($row['full_name']) ?></td>
                    <td><?= ($row['gender'] === 'Male') ? 'ወንድ' : 'ሴት' ?></td>
                    <td><?= htmlspecialchars($row['age']) ?></td>
                    <td><?= htmlspecialchars($row['phone'] ?? '-') ?></td>
                    <td class="text-left">
                      <?= htmlspecialchars($row['reszone'] . ' / ' . $row['resworeda'] . ' / ቀበሌ ' . $row['res_kebele']) ?>
                    </td>
                    <td class="text-left">
                      <span class="badge badge-info d-block mb-1"><?= htmlspecialchars($row['sub_sector_name'] ?? 'ያልተጠቀሰ') ?></span>
                      <small><?= htmlspecialchars($row['job_position']) ?></small>
                    </td>
                    <td>
                      <span class="badge <?= ($row['trade_area_type'] == 1) ? 'badge-success' : 'badge-warning' ?>">
                        <?= ($row['trade_area_type'] == 1) ? 'ከተማ' : 'ገጠር' ?>
                      </span>
                    </td>
                    <td><?= date('Y-m-d', strtotime($row['created_at'])) ?></td>
                    <td>
                      <!-- ሙሉ ዝርዝር ማሳያ አዝራር -->
                      <button type="button" class="btn btn-info btn-xs" data-toggle="modal" data-target="#traderModal<?= $row['id'] ?>">
                        <i class="fas fa-eye"></i> ዝርዝር
                      </button>
                      <!-- የተጨመረው የ ማጥፊያ/Delete አዝራር -->
<button type="button" class="btn btn-danger btn-xs ml-1" data-toggle="modal" data-target="#deleteModal<?= $row['id'] ?>">
  <i class="fas fa-trash"></i> ሰርዝ
</button>
                    </td>
                  </tr>
<!-- ================= የማጥፊያ ማረጋገጫ ሞዳል (Delete Confirmation Modal) ================= -->
<div class="modal fade text-left" id="deleteModal<?= $row['id'] ?>" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title">
          <i class="fas fa-exclamation-triangle mr-1"></i> የመረጃ ማጥፊያ ማረጋገጫ
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="informal-trade-delete-process" method="POST">
        <div class="modal-body">
          <input type="hidden" name="id" value="<?= $row['id'] ?>">
          
          <p class="text-danger font-weight-bold mb-2">
            እርግጠኛ ነዎት የልዩ መለያ ቁጥር #<?= $row['id'] ?> ("<?= htmlspecialchars($row['full_name']) ?>") መረጃ ማጥፋት ይፈልጋሉ?
          </p>
          <small class="text-muted d-block mb-3">
            * መረጃው ከዚህ ሰንጠረዥ ቢጠፋም ለኦዲት እና ለደህንነት ሲባል ወደ <strong>Backup</strong> የሚዛወር ይሆናል።
          </small>

          <div class="form-group">
            <label class="font-weight-bold">የመሰረዝ ምክንያት *</label>
            <textarea name="reason" class="form-control" rows="2" placeholder="መረጃው ለምን እንደሚሰረዝ ምክንያቱን በጥቂቱ ይጻፉ..." required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">ሰርዝ/ተመለስ</button>
          <button type="submit" class="btn btn-danger btn-sm font-weight-bold">አረጋገጣለሁ (ሰርዝ)</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- ================= End Delete Modal ================= -->
                  <!-- ================= ዝርዝር መረጃ ማሳያ ሞዳል (Modal for Item #<?= $row['id'] ?>) ================= -->
                  <div class="modal fade text-left" id="traderModal<?= $row['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="traderModalLabel<?= $row['id'] ?>" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                      <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                          <h5 class="modal-title" id="traderModalLabel<?= $row['id'] ?>">
                            <i class="fas fa-user-circle mr-1"></i> የኢ-መደበኛ ንግድ ተሰማሪ ሙሉ መረጃ
                          </h5>
                          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                          </button>
                        </div>
                        <div class="modal-body">
                          
                          <div class="row">
                            <!-- 1. የግል መረጃ -->
                            <div class="col-md-12 mb-2">
                              <h6 class="text-primary font-weight-bold border-bottom pb-1">1. የግል መረጃ</h6>
                            </div>
                            <div class="col-md-6 mb-2"><strong>ሙሉ ስም:</strong> <?= htmlspecialchars($row['full_name']) ?></div>
                            <div class="col-md-3 mb-2"><strong>ጾታ:</strong> <?= ($row['gender'] === 'Male') ? 'ወንድ' : 'ሴት' ?></div>
                            <div class="col-md-3 mb-2"><strong>ዕድሜ:</strong> <?= htmlspecialchars($row['age']) ?></div>
                            <div class="col-md-6 mb-2"><strong>ስልክ ቁጥር:</strong> <?= htmlspecialchars($row['phone'] ?? 'የለም') ?></div>
                            <div class="col-md-6 mb-2">
                              <strong>የቀበሌ መታወቂያ:</strong> 
                              <?= ($row['has_kebele_id'] == 1) ? 'አለ (ቁጥር: ' . htmlspecialchars($row['kebele_id_number']) . ')' : 'የለም' ?>
                            </div>

                            <!-- 2. የመኖሪያ አድራሻ -->
                            <div class="col-md-12 mt-3 mb-2">
                              <h6 class="text-primary font-weight-bold border-bottom pb-1">2. የመኖሪያ አድራሻ</h6>
                            </div>
                            <div class="col-md-4 mb-2"><strong>ክልል:</strong> አማራ</div>
                            <div class="col-md-4 mb-2"><strong>ዞን:</strong> <?= htmlspecialchars($row['reszone']) ?></div>
                            <div class="col-md-4 mb-2"><strong>ወረዳ/ክፍለ ከተማ:</strong> <?= htmlspecialchars($row['resworeda']) ?></div>
                            <div class="col-md-4 mb-2"><strong>ቀበሌ:</strong> <?= htmlspecialchars($row['res_kebele']) ?></div>

                            <!-- 3. የሥራ ቦታ እና የንግድ ዘርፍ -->
                            <div class="col-md-12 mt-3 mb-2">
                              <h6 class="text-primary font-weight-bold border-bottom pb-1">3. የሥራ ቦታ እና ንግድ ዘርፍ</h6>
                            </div>
                            <div class="col-md-6 mb-2"><strong>የሥራ ቦታ ቅርንጫፍ:</strong> <?= htmlspecialchars($row['work_branch_name'] ?? '-') ?></div>
                            <div class="col-md-6 mb-2"><strong>ንግዱ የሚገኝበት አካባቢ:</strong> <?= ($row['trade_area_type'] == 1) ? 'ከተማ' : 'ገጠር' ?></div>
                            <div class="col-md-6 mb-2"><strong>ንዑስ ዘርፍ:</strong> <?= htmlspecialchars($row['sub_sector_name'] ?? '-') ?></div>
                            <div class="col-md-6 mb-2"><strong>የሥራ መደብ:</strong> <?= htmlspecialchars($row['job_position']) ?></div>
                            <div class="col-md-6 mb-2"><strong>የተሰማራበት ዓ.ም:</strong> <?= htmlspecialchars($row['start_year']) ?> ዓ.ም</div>
                            <div class="col-md-6 mb-2"><strong>አቅራቢያ የሚገኝ ማዕከል:</strong> <?= htmlspecialchars($row['nearby_center_name'] ?? 'የለም') ?></div>

                            <!-- 4. የሲስተም መረጃ -->
                            <div class="col-md-12 mt-3 mb-2">
                              <h6 class="text-secondary font-weight-bold border-bottom pb-1">4. የምዝገባ ኦዲት መረጃ</h6>
                            </div>
                            <div class="col-md-6 mb-2"><strong>የመዘገበው አካል:</strong> <?= htmlspecialchars($row['regby_name'] ?? 'ሲስተም') ?> <?= htmlspecialchars($row['father_name'] ?? 'ሲስተም') ?></div>
                            <div class="col-md-6 mb-2"><strong>የተመዘገበበት ሰዓት:</strong> <?= htmlspecialchars($row['created_at']) ?></div>
                          </div>

                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">ዝጋ</button>
                        </div>
                      </div>
                    </div>
                  </div>
                  <!-- ================= End Modal ================= -->

                <?php endforeach; ?>
              </tbody>
            </table>
          </div>

        </div>
      </div>
    </div>
  </div>
</section>

<!-- DataTables Script -->
<script nonce="<?php echo htmlspecialchars($GLOBALS['nonce'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
  $(function () {
    if ($.fn.DataTable.isDataTable('#example1')) {
      $('#example1').DataTable().destroy();
    }
    
    $("#example1").DataTable({
      "responsive": true,
      "lengthChange": true,
      "autoWidth": false,
      "language": {
        "search": "ፈልግ:",
        "lengthMenu": "በአንድ ገጽ _MENU_ መረጃዎች አሳይ",
        "zeroRecords": "ምንም የተመዘገበ የኢ-መደበኛ ንግድ መረጃ አልተገኘም",
        "info": "ከ _TOTAL_ ውስጥ ከ _START_ እስከ _END_ እየታዩ ነው",
        "infoEmpty": "ምንም መረጃ የለም",
        "paginate": {
          "first": "መጀመሪያ",
          "last": "መጨረሻ",
          "next": "ቀጣይ",
          "previous": "ቀደመ"
        }
      }
    });
  });
</script>