<?php 
// በሴሽን ውስጥ ያለውን የተጠቃሚ ደረጃ (Level) እና የሎግኢን ቅርንጫፍ መለያ መውሰድ
$level = $_SESSION['user']['level'] ?? null;
$current_branch_id = isset($_SESSION['user']['internal_id']) ? (int)$_SESSION['user']['internal_id'] : 0;
?>
<?php $is_deleteawarness = true; ?>
<section class="content">
  <div class="container-fluid">

    <div class="card card-default">
      <div class="card card-primary card-outline">
        <div class="card-header">
          <h3 class="card-title font-weight-bold text-primary">
            <i class="fas fa-exchange-alt mr-2"></i> የሥራ ፈላጊዎች የዝውውር ሁኔታ መከታተያ ማውጫ
          </h3>
        </div>
        
        <div class="card-body">
          <table id="example1" data-empty-msg="ምንም የዝውውር መረጃ የለም።" class="table table-bordered table-striped table-hover dataTable dtr-inline small" style="color: #000;">
            <thead class="thead-light">
              <tr>
                <th>#</th>
                <th>ሙሉ ስም</th>
                <th>ስልክ ቁጥር</th>
                <th>ላኪ መ/ቤት</th>
                <th>ተቀባይ</th>
                <th>የተላከበት ቀን</th>
                <th>የዕይታ ሁኔታ</th>
                <th>የዝውውር ሁኔታ (Status)</th>
                <th style="width: 15%;">ድርጊት (Action)</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($transfers) && is_array($transfers)): ?>
                <?php $count = 1; ?>
                <?php foreach ($transfers as $row): ?>
                  <?php 
                    // የላኪ እና የተቀባይ ቅርንጫፍ መለያዎችን ለቀላል ማወዳደሪያ ማዘጋጀት
                    $sender_id = (int)($row['sender_branch_id'] ?? 0);
                    $receiver_id = (int)($row['recver_branch_id'] ?? 0);
                  ?>
                  <tr>
                    <td><?= $count++; ?></td>
                    
                    <!-- ሙሉ ስም -->
                    <td class="font-weight-bold">
                      <?php 
                        $fullName = trim(($row['first_name'] ?? '') . ' ' . ($row['father_name'] ?? '') . ' ' . ($row['last_name'] ?? ''));
                        echo htmlspecialchars(!empty($fullName) ? $fullName : 'የለም', ENT_QUOTES, 'UTF-8');
                      ?>
                    </td>
                    
                    <!-- ስልክ ቁጥር -->
                    <td><?= htmlspecialchars($row['job_seeker_phone'] ?? 'የለም', ENT_QUOTES, 'UTF-8') ?></td>
                    
                    <!-- ላኪ ቅርንጫፍ -->
                    <td>
                      <span class="badge badge-secondary">
                        <?= htmlspecialchars($row['sender_branch_name'] ?? 'የለም', ENT_QUOTES, 'UTF-8') ?>
                      </span>
                    </td>
                    
                    <!-- መዳረሻ ቅርንጫፍ -->
                    <td>
                      <span class="badge badge-info">
                        <?= htmlspecialchars($row['receiver_branch_name'] ?? 'የለም', ENT_QUOTES, 'UTF-8') ?>
                      </span>
                    </td>
                    
                    <!-- የተላከበት ቀን -->
                    <td>
                      <?= isset($row['transferdate']) ? htmlspecialchars(date('Y-m-d H:i', strtotime($row['transferdate'])), ENT_QUOTES, 'UTF-8') : 'የለም' ?>
                    </td>
                    
                    <!-- የዕይታ ሁኔታ -->
                    <td>
                      <?php if (isset($row['reciver_view_status']) && $row['reciver_view_status'] == '1'): ?>
                        <span class="text-success font-weight-bold"><i class="fas fa-eye"></i> ታይቷል</span>
                      <?php else: ?>
                        <span class="text-muted"><i class="fas fa-eye-slash"></i> አልታየም</span>
                      <?php endif; ?>
                    </td>
                    
                    <!-- የዝውውር ሁኔታ -->
                    <td>
                      <?php if (isset($row['transfer_status']) && $row['transfer_status'] == '0'): ?>
                        <span class="badge badge-warning text-dark"><i class="fas fa-clock"></i> በመጠባበቅ ላይ</span>
                      <?php elseif (isset($row['transfer_status']) && $row['transfer_status'] == '1'): ?>
                        <span class="badge badge-success"><i class="fas fa-check-circle"></i> የጸደቀ (Received)</span>
                      <?php elseif (isset($row['transfer_status']) && $row['transfer_status'] == '2'): ?>
                        <span class="badge badge-danger"><i class="fas fa-times-circle"></i> ውድቅ የተደረገ</span>
                      <?php else: ?>
                        <span class="badge badge-secondary">ያልታወቀ</span>
                      <?php endif; ?>
                    </td>
                    
                    <!-- ⚡ የተሻሻለው የድርጊት ቁልፍ ሎጂክ (Action Logic) -->
                    <td>
                      <div class="btn-group">
                        <?php if (isset($row['transfer_status']) && $row['transfer_status'] == '0'): ?>
                          
                          <?php if ($receiver_id === $current_branch_id): ?>
                            <!-- 🎯 ሁኔታ 1፦ ሎግኢን ያደረገው ሰው ተቀባዩ ቅርንጫፍ ከሆነ ውሳኔ መስጠት ይችላል -->
                            <button type="button" 
                                    class="btn btn-sm btn-primary btn-transfer-action font-weight-bold" 
                                    data-id="<?= htmlspecialchars($row['id'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                    data-name="<?= htmlspecialchars(($row['first_name'] ?? '') . ' ' . ($row['father_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                    title="ውሳኔ ስጥ">
                              <i class="fas fa-gavel mr-1"></i> ውሳኔ ስጥ
                            </button>
                          
                          <?php elseif ($sender_id === $current_branch_id): ?>
                            <!-- 📤 ሁኔታ 2፦ ሎግኢን ያደረገው ሰው ራሱ የላከው (Sender) ከሆነ መከታተል ብቻ ነው የሚችለው -->
                            <span class="badge badge-secondary p-2"><i class="fas fa-paper-plane mr-1"></i> የተላከ (ውሳኔ ይጠብቃል)</span>
                          
                          <?php else: ?>
                            <!-- 🔒 ሁኔታ 3፦ ለሌላ አካል ዝግ ነው -->
                            <span class="text-muted small"><i class="fas fa-lock mr-1"></i> ዝግ ነው</span>
                          <?php endif; ?>

                        <?php else: ?>
                          <!-- 🏁 ዝውውሩ ቀድሞውኑ ውሳኔ ካገኘ -->
                          <span class="text-muted small"><i class="fas fa-check-double mr-1"></i> ተጠናቋል</span>
                        <?php endif; ?>
                      </div>
                    </td>

                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="9" class="text-center text-muted">ምንም የዝውውር መረጃ አልተገኘም።</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

      </div>
    </div>

  </div>
</section>

<!-- 🔔 ውሳኔ መስጫ ፖፕ-አፕ (Bootstrap Modal) -->
<div class="modal fade" id="transferActionModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-user-check mr-2"></i> የዝውውር ውሳኔ መስጫ ሳጥን</h5>
                <button type="button" class="close text-white" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="transferActionForm">
                <div class="modal-body">
                    <input type="hidden" name="transfer_log_id" id="action_transfer_id">
                    
                    <div class="mb-3">
                        <label class="form-label text-muted">የሥራ ፈላጊ ስም፦</label>
                        <div id="action_job_seeker_name" class="p-2 bg-light border rounded font-weight-bold text-primary"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label font-weight-bold"> የውሳኔ ዓይነት ይምረጡ፦</label>
                        <div class="d-flex gap-4 mt-2">
                            <div class="form-check mr-3 d-inline-block">
                                <input class="form-check-input" type="radio" name="action_status" id="status_approve" value="1" checked>
                                <label class="form-check-label text-success font-weight-bold" for="status_approve" style="cursor: pointer;">
                                    <i class="fas fa-check-circle"></i> ዝውውሩን አጽድቅ
                                </label>
                            </div>
                            <div class="form-check d-inline-block">
                                <input class="form-check-input" type="radio" name="action_status" id="status_reject" value="2">
                                <label class="form-check-label text-danger font-weight-bold" for="status_reject" style="cursor: pointer;">
                                    <i class="fas fa-times-circle"></i> ውድቅ አድርግ
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ዝጋ</button>
                    <button type="submit" class="btn btn-primary font-weight-bold"><i class="fas fa-save mr-1"></i> ውሳኔውን መዝግብ</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script nonce="<?php echo htmlspecialchars($GLOBALS['nonce'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
    
 
$(document).ready(function() {
    // 1. "ውሳኔ ስጥ" ቁልፍ ሲጫን ሞዳሉን መክፈት
    $(document).on('click', '.btn-transfer-action', function() {
        var transferId = $(this).data('id');
        var seekerName = $(this).data('name');

        $('#action_transfer_id').val(transferId);
        $('#action_job_seeker_name').text(seekerName);
        $('#transferActionModal').modal('show');
    });

    // 2. ፎርሙ በአጃክስ ሲላክ
    $('#transferActionForm').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();

        $.ajax({
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    $('#transferActionModal').modal('hide');
                    location.reload();
                } else {
                    alert(response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
                alert('የስርዓት ስህተት ተፈጥሯል። እባክዎ በኮንትሮለር ላይ የሁኔታ ማዘመኛውን (Update Method) መኖሩን ያረጋግጡ።');
            }
        });
    });
});
</script>