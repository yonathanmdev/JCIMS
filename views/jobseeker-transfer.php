<!-- views/jobseeker_transfer_view.php -->
<?php $is_organization_page = true; ?>

<section class="content">
  <div class="container-fluid">
    <div class="card card-default">
      <div class="card card-primary card-outline">
        <div class="card-body">
          
          <!-- ሰንጠረዥ -->
          <table id="example1" data-empty-msg="ምንም ስራ ፈላጊ የለም።" class="table table-bordered table-hover dataTable dtr-inline small" style="color: #000;">
            <thead class="thead-light">
              <tr>
                <th>#</th>
                <th>ስም </th>
                <th>ጾታ</th>
                <th>ቅርንጫፍ</th>
                <th style="width: 150px;">ተግባር (Action)</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($jobSeekers)): ?>
                <?php foreach ($jobSeekers as $index => $row): ?>
                  <tr id="row-<?= (int)$row['id'] ?>">
                    <td><?= $index + 1 ?></td>
                    <td>
                      <?= htmlspecialchars($row['first_name'] ?? '', ENT_QUOTES, 'UTF-8') . ' ' . 
                          htmlspecialchars($row['father_name'] ?? '', ENT_QUOTES, 'UTF-8') . ' ' . 
                          htmlspecialchars($row['last_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                    </td>
                    <td><?= htmlspecialchars($row['gender'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($row['branch_name'] ?? 'የለም', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="text-center align-middle">
                      <div class="btn-group btn-group-sm shadow-sm" role="group">
                        <button type="button" 
                                class="btn btn-outline-primary btn-sm transfer-jobseeker-btn" 
                                data-id="<?= (int)$row['job_seeker_id'] ?>"
                                data-fullname="<?= htmlspecialchars(($row['first_name'] ?? '').' '.($row['father_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                title="የሥራ ፈላጊ ዝውውር">
                          <i class="fas fa-exchange-alt me-1"></i> ዝውውር
                        </button> 
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>

        </div>
      </div>
    </div>
  </div>
</section>

<!-- የሥራ ፈላጊ ዝውውር ማስተናገጃ ሞዳል -->
<div class="modal fade" id="transferJobSeekerModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="transferModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="transferModalLabel"><i class="fas fa-exchange-alt mr-2"></i> የሥራ ፈላጊ ዝውውር ማስተናገጃ</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="transferJobSeekerForm" autocomplete="off">
        <div class="modal-body">
          
          <!-- የደህንነት ማስጠበቂያ ቶክን (CSRF Token) -->
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
          <input type="hidden" id="transfer_job_seeker_id" name="job_seeker_id">
          
          <div class="alert alert-info py-2 mb-3">
            <strong>የሥራ ፈላጊ ስም፦ </strong> <span id="transfer_seeker_name" class="font-weight-bold"></span>
          </div>

          <!-- 💡 አዲስ የተጨመረ፡ ከክልል በላይ የሚቀመጥ የዝውውር ደረጃ መምረጫ (Radio Box) -->
          <div class="card card-outline card-secondary p-3 mb-3 bg-light">
            <label class="font-weight-bold mb-2"><i class="fas fa-layer-group text-primary mr-1"></i> ስራ ፈላጊዉ ለማን ነዉ ዝዉዉሩን እምሰሩለት <span class="text-danger">*</span></label>
            <div class="d-flex">
              <div class="custom-control custom-radio mr-4">
                <input type="radio" id="type_center" name="transfer_level_type" value="center" class="custom-control-input" checked>
                <label class="custom-control-label font-weight-bold" for="type_center" style="cursor:pointer;">ለአንድ ማዕከል</label>
              </div>
              <div class="custom-control custom-radio">
                <input type="radio" id="type_woreda" name="transfer_level_type" value="woreda" class="custom-control-input">
                <label class="custom-control-label font-weight-bold" for="type_woreda" style="cursor:pointer;">ለወረዳ / ክፍለ ከተማ ጽ/ቤት</label>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 form-group">
              <label for="transfer_region"><i class="fas fa-map-marker-alt text-danger mr-1"></i> ክልል <span class="text-danger">*</span></label>
              <select class="form-control select-hierarchy" id="transfer_region" name="region_id" data-next="transfer_zone" data-level="1" required>
                <option value="">-- ክልል ይምረጡ --</option>
              </select>
            </div>

            <div class="col-md-6 form-group">
              <label id="label_zone" for="transfer_zone"><i class="fas fa-map text-primary mr-1"></i> ዞን / ከተማ አስተዳደር <span class="text-danger">*</span></label>
              <select class="form-control select-hierarchy" id="transfer_zone" name="zone_id" data-next="transfer_woreda" data-level="2" disabled required>
                <option value="">-- መጀመሪያ ክልል ይምረጡ --</option>
              </select>
            </div>
          </div>

          <div class="row mt-2">
            <div class="col-md-6 form-group">
              <label id="label_woreda" for="transfer_woreda"><i class="fas fa-landmark text-success mr-1"></i> ወረዳ / ክፍለ ከተማ <span class="text-danger">*</span></label>
              <select class="form-control select-hierarchy" id="transfer_woreda" name="woreda_id" data-next="transfer_center" data-level="3" disabled required>
                <option value="">-- መጀመሪያ ዞን ይምረጡ --</option>
              </select>
            </div>

            <!-- 💡 የማዕከል መያዣ ሳጥን በጃቫስክሪፕት በቀላሉ ለመቆጣጠር id="center_box_container" ተሰጥቶታል -->
            <div class="col-md-6 form-group" id="center_box_container">
              <label id="label_center" for="transfer_center"><i class="fas fa-hubspot text-warning mr-1"></i> ማዕከል <span class="text-danger" id="center_required_mark">*</span></label>
              <select class="form-control" id="transfer_center" name="destination_branch_id" disabled required>
                <option value="">-- መጀመሪያ ወረዳ/ክፍለ ከተማ ይምረጡ --</option>
              </select>
            </div>
          </div>

        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">ዝጋ</button>
          <button type="submit" class="btn btn-success shadow"><i class="fas fa-paper-plane mr-1"></i> ዝውውሩን አጽድቅ</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script nonce="<?php echo htmlspecialchars($GLOBALS['nonce'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
window.addEventListener('load', function() {
    $(document).ready(function() {
        var baseUrl = "<?= htmlspecialchars(rtrim($_ENV['BASE_URL'] ?? '', '/'), ENT_QUOTES, 'UTF-8') ?>";

        // 💡 አዲስ የተጨመረ፦ የራዲዮ በተን ምርጫ ለውጥን የሚከታተል ሎጂክ
        $('input[name="transfer_level_type"]').change(function() {
            var selectedType = $(this).val();
            
            if (selectedType === 'woreda') {
                // 1. ለወረዳ/ክፍለ ከተማ ከሆነ ማዕከሉን በዕይታ ደብቅ
                $('#center_box_container').slideUp('fast');
                // 2. የማዕከሉን ዋጋ ባዶ አድርገው
                $('#transfer_center').val('').trigger('change');
                // 3. የነበረውን የ validation required ህግ እና ምልክት አንሳ
                $('#transfer_center').prop('required', false).prop('disabled', true);
                $('#center_required_mark').hide();
            } else {
                // 4. ለማዕከል ከሆነ መልሰህ አሳይ
                $('#center_box_container').slideDown('fast');
                // 5. ወረዳው አስቀድሞ ከተመረጠ ብቻ ሳጥኑ ክፍት እንዲሆን አድርግ
                if ($('#transfer_woreda').val()) {
                    $('#transfer_center').prop('disabled', false);
                }
                // 6. ግዴታ (Required) አድርገው
                $('#transfer_center').prop('required', true);
                $('#center_required_mark').show();
            }
        });

        // 1. የዝውውር ሞዳል ሲከፈት - መጀመሪያ ደረጃ 1 (ክልሎችን) ብቻ መጫን
        $(document).on('click', '.transfer-jobseeker-btn', function() {
            var id = $(this).data('id');
            var fullname = $(this).data('fullname');
            
            $('#transfer_job_seeker_id').val(id);
            $('#transfer_seeker_name').text(fullname);
            
            // የራዲዮ በተኑን ወደ መጀመሪያው (center) መልስ
            $('#type_center').prop('checked', true).trigger('change');
            
            resetHierarchyFrom('transfer_region');
            $('#transferJobSeekerModal').modal('show');
            
            // የደረጃ 1 (ክልል) ጥሪ
            loadBranches(1, null, 'transfer_region');
        });

        // 2. ተዋረዶቹ (Region -> Zone -> Woreda -> Center) ሲቀያየሩ የሚሰራ
        $('.select-hierarchy').on('change', function() {
            var nextSelectId = $(this).data('next');
            var currentLevel = parseInt($(this).data('level'));
            var parentId = $(this).val(); 
            
            resetHierarchyFrom(nextSelectId);
            if (!parentId) return;
            
            // 💡 የከተማ አስተዳደር መለያ (ደረጃ 2 ሲመረጥ የሚሰራ)
            if (currentLevel === 2) {
                var ketemaValue = $(this).find(':selected').data('ketema');
                
                if (ketemaValue === 'on') {
                    $('#label_woreda').html('<i class="fas fa-landmark text-success mr-1"></i> ክፍለ ከተማ <span class="text-danger">*</span>');
                    $('#transfer_woreda').html('<option value="">-- ክፍለ ከተማ ይምረጡ --</option>');
                } else {
                    $('#label_woreda').html('<i class="fas fa-landmark text-success mr-1"></i> ወረዳ <span class="text-danger">*</span>');
                    $('#transfer_woreda').html('<option value="">-- ወረዳ ይምረጡ --</option>');
                }
            }
            
            // ቀጣዩን ደረጃ ለመጫን ጥሪ ማድረግ
            loadBranches(currentLevel + 1, parentId, nextSelectId);
        });

        // 3. የ AJAX ቅርንጫፍ መጫኛ ፈንክሽን
        function loadBranches(level, parentId, targetSelectId) {
            // ዝውውሩ ለወረዳ ከሆነ እና አሁን ሊጫን የመጣው ማዕከል (ደረጃ 4) ከሆነ ጥሪውን አታድርግ
            var selectedType = $('input[name="transfer_level_type"]:checked').val();
            if (selectedType === 'woreda' && level === 4) {
                return;
            }

            var $target = $('#' + targetSelectId);
            $.ajax({
                url: baseUrl + '/get-branches-by-hierarchy-ajax',
                method: 'GET',
                data: { level: level, parent_id: parentId },
                dataType: 'json',
                success: function(response) {
                    if (response.success && response.data.length > 0) {
                        $target.prop('disabled', false);
                        $.each(response.data, function(i, branch) {
                            var option = $('<option></option>')
                                            .val(branch.internal_id) 
                                            .text(branch.name)
                                            .attr('data-ketema', branch.ketema_astedader); 
                            $target.append(option);
                        });
                    }
                }
            });
        }

        // 4. ምርጫ ሲቀየር የበታች የነበሩትን select ሳጥኖች ማጽጃ
        function resetHierarchyFrom(selectId) {
            if (selectId === 'transfer_region') {
                $('#transfer_region').html('<option value="">-- ክልል ይምረጡ --</option>').prop('disabled', false);
                selectId = 'transfer_zone';
            }
            if (selectId === 'transfer_zone') {
                $('#transfer_zone').html('<option value="">-- መጀመሪያ ክልል ይምረጡ --</option>').prop('disabled', true);
                selectId = 'transfer_woreda';
            }
            if (selectId === 'transfer_woreda') {
                $('#label_woreda').html('<i class="fas fa-landmark text-success mr-1"></i> ወረዳ / ክፍለ ከተማ <span class="text-danger">*</span>');
                $('#transfer_woreda').html('<option value="">-- መጀመሪያ ዞን ይምረጡ --</option>').prop('disabled', true);
                selectId = 'transfer_center';
            }
            if (selectId === 'transfer_center') {
                var selectedType = $('input[name="transfer_level_type"]:checked').val();
                $('#transfer_center').html('<option value="">-- መጀመሪያ ወረዳ/ክፍለ ከተማ ይምረጡ --</option>').prop('disabled', true);
                
                // ደረጃው ለማዕከል ከሆነ ብቻ ሪሴት ሲደረግ required ማድረጉን አስቀጥል
                if (selectedType === 'center') {
                    $('#transfer_center').prop('required', true);
                }
            }
        }

        // 5. የቅጽ ማቅረቢያ (Submit) ከደህንነት ቁጥጥር ጋር
      $('#transferJobSeekerForm').on('submit', function(e) {
            e.preventDefault();

            // 💡 1. ዳታውን በ Array መልክ እንሰበስባለን (ለፖፕ-አፕ ዝርዝር እንዲመቸን)
            var formDataArray = $(this).serializeArray();
            var transferType = $('input[name="transfer_level_type"]:checked').val();

            // ዝውውሩ ለወረዳ ከሆነ መዳረሻውን 0 አድርገን እንጨምራለን
            if (transferType === 'woreda') {
                formDataArray.push({ name: 'destination_branch_id', value: '0' });
            }

            // 💡 2. ለፖፕ-አፕ (Alert) የሚሆን የጽሑፍ ዝግጅት
            var debugMessage = "📊 ለመላክ የተዘጋጀው የዝውውር መረጃ ዝርዝር፦\n";
            debugMessage += "----------------------------------------\n";
            
            $.each(formDataArray, function(index, field) {
                debugMessage += "🔹 " + field.name + " ➔ " + field.value + "\n";
            });

            debugMessage += "----------------------------------------\n";
            debugMessage += "ይህንን መረጃ ወደ ጀርባ (PHP) ለመላክ 'OK' ን ይጫኑ።";

            // 💡 3. መረጃውን በፖፕ-አፕ ማሳየት
            alert(debugMessage);

            // 💡 4. አሁን ወደ እውነተኛው የ AJAX ጥሪ ይቀጥላል
            // አሬይ የተደረገውን ዳታ ወደ መደበኛ የ serialize ቴክስት ቀይሮ ለመላክ $.param እንጠቀማለን
            var finalData = $.param(formDataArray);

            $.ajax({
                url: baseUrl + '/process-jobseeker-transfer',
                method: 'POST',
                data: finalData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        $('#transferJobSeekerModal').modal('hide');
                        location.reload();
                    } else {
                        alert(response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error(error);
                    alert('የስርዓት ስህተት ተፈጥሯል። እባክዎ ከጊዜ ወደ ጊዜ ይሞክሩ።');
                }
            });
        });
    });
});
</script>