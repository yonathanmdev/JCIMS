<?php $is_deleteawarness = true; ?>
<section class="content">
  <div class="container-fluid">

    <div class="card card-default">
      <div class="card card-primary card-outline">
        <div class="card-header">
          <h3 class="card-title font-weight-bold text-primary">
            <i class="fas fa-user-graduate mr-2"></i> ግንዛቤ የተፈጠረላቸዉ የስራ ፈላጊዎች ዝርዝር
          </h3>
        </div>

        <div class="card-body">
          <table id="example1" data-empty-msg="ምንም ስራ ፈላጊ የለም።" class="table table-bordered table-striped table-hover dataTable dtr-inline small" style="color: #000;">
            <thead class="thead-light">
              <tr>
                <th>#</th>
                <th>ሙሉ ስም</th>
                <th>ጾታ</th>
                <th>እድሜ</th>
                <th>ስልክ ቁጥር</th>
                <th>Labor ID / FAN</th>
                <th>የቅጥር ሁኔታ</th>
                <th>ግንዛቤ</th>
                <th>ድርጊት (Action)</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($job_seekers) && is_array($job_seekers)): ?>
                <?php $count = 1; ?>
                <?php foreach ($job_seekers as $seeker): ?>
                  <tr>
                    <td><?= $count++; ?></td>
                    
                    <td class="font-weight-bold">
                      <?= htmlspecialchars($seeker['first_name'] . ' ' . $seeker['father_name'] . ' ' . $seeker['last_name'], ENT_QUOTES, 'UTF-8') ?>
                    </td>
                    
                    <td><?= htmlspecialchars($seeker['gender'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($seeker['age'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($seeker['phone_number'] ?? 'የለም', ENT_QUOTES, 'UTF-8') ?></td>
                    
                    <td>
                      <span class="badge badge-secondary">L-ID: <?= htmlspecialchars($seeker['Labor_ID'] ?? 'የለም', ENT_QUOTES, 'UTF-8') ?></span><br>
                        </td>
                    
                    <td>
                      <?php if (isset($seeker['employment_status']) && $seeker['employment_status'] == '1'): ?>
                        <span class="badge badge-success"><i class="fas fa-briefcase"></i> ቋሚ ስራ የተፈጠረለት</span>
                      <?php elseif (isset($seeker['employment_status']) && $seeker['employment_status'] == '2'): ?>
                        <span class="badge badge-warning"><i class="fas fa-clock"></i> ጊዚያዊ ስራ የተፈጠረለት</span>
                      <?php else: ?>
                        <span class="badge badge-danger"><i class="fas fa-times-circle"></i> ስራ ያልተፈጠረለት</span>
                      <?php endif; ?>
                    </td>

                    <td>
                      <?php if (isset($seeker['awareness']) && $seeker['awareness'] == '1'): ?>
                        <span class="badge badge-info"><i class="fas fa-check-double"></i> ወስዷል</span>
                      <?php else: ?>
                        <span class="badge badge-secondary"><i class="fas fa-minus-circle"></i> አልወሰደም</span>
                      <?php endif; ?>
                    </td>
                    
                   <td>
  <div class="btn-group">
    <button type="button" 
            class="btn btn-sm btn-danger remove-awareness-btn" 
            data-id="<?= htmlspecialchars($seeker['id'], ENT_QUOTES, 'UTF-8') ?>"
            data-fullname="<?= htmlspecialchars($seeker['first_name'] . ' ' . $seeker['father_name'] . ' ' . $seeker['last_name'], ENT_QUOTES, 'UTF-8') ?>"
            title="የግንዛቤ ሪፖርት ሰርዝ">
      <i class="fas fa-trash-alt"></i>
    </button>
  </div>
</td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="9" class="text-center text-muted">ምንም ስራ ፈላጊ አልተገኘም።</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </div>
</section>
 <script nonce="<?php echo htmlspecialchars($GLOBALS['nonce'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
window.addEventListener('DOMContentLoaded', function() {
    
    // የመደለዣ (የሪፖርት መሰረዣ) ቁልፉ ሲጫን
    $(document).on('click', '.remove-awareness-btn', function(e) {
        e.preventDefault();
        
        var button = $(this);
        var seekerId = button.data('id');
        var fullName = button.data('fullname');
        
        // 🚨 ጥብቅ የኃላፊነት ማስጠንቀቂያ ፖፕ-አፕ (SweetAlert2)
        Swal.fire({
            title: '⚠️ ጥብቅ ማስጠንቀቂያ እና ማረጋገጫ!',
            html: '<div class="text-start small text-dark" style="line-height: 1.6; text-align: left;">' +
                  'እርግጠኛ ነህ ለስራ ፈላጊ <b>' + fullName + '</b> ግንዛቤ ፈጥሬለታለሁ ብለህ ቀደም ሲል ሪፖርት አድርገሃል?<br><br>' +
                  '<span class="text-danger font-weight-bold">የፈጠርክለት ግንዛቤ በስህተት እንደሆነና አሁን በምታጠፋው ዳታ ሙሉ ኃላፊነት ትወስዳለህ!</span><br><br>' +
                  'ሁሉም የሲስተም ዳታዎች በኦዲት መዝገብ (Log) ላይ ሪከርድ ስለሚሆኑ ከእነ ሰራዉ ሰዉ እስከመጨረሻው ይቀመጣል/አይጠፋም' +
                  '</div>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'አዎ፣ ስህተት ነው ኃላፊነት እወስዳለሁ!',
            cancelButtonText: 'አይ፣ ተመለስ',
            allowOutsideClick: false
        }).then((result) => {
            if (result.isConfirmed) {
                
                // 🚀 ተጠቃሚው እሺ ካለ ፍጹም አድራሻን በመጠቀም የ AJAX ጥሪ ይጀምራል
                $.ajax({
                    url: '<?= htmlspecialchars(rtrim($_ENV['BASE_URL'] ?? '', '/'), ENT_QUOTES, 'UTF-8') ?>/remove-job-seeker-awareness-ajax',
                    method: 'POST',
                    data: { 
                        job_seeker_id: seekerId 
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'ተስተካክሏል!',
                                text: response.message,
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            });
                            
                            // ገጹን ሳናድስ መስመሩን ከሰንጠረዡ (DataTables) ላይ በውበት ማጥፋት
                            button.closest('tr').fadeOut(400, function() {
                                $(this).remove();
                            });
                        } else {
                            Swal.fire('ስህተት', response.message, 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        // ለዴቨሎፕመንት ፍተሻ እንዲረዳ እውነተኛው ስህተት በኮንሶል ላይ ይታያል
                        console.error("AJAX Error Output:", xhr.responseText, error);
                        Swal.fire('ስህተት', 'የሰርቨር ግንኙነት ተቋርጧል ወይም የስርዓት ስህተት ተከስቷል።', 'error');
                    }
                });
            }
        });
    });
});
</script>