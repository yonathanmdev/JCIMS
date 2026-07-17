<?php
use App\Helpers\EthiopianDateHelper;
$is_awaerness_registration_page = true;
?>
<section class="content">
  <div class="container-fluid">
    <div class="card-header bg-white d-flex flex-column flex-md-row align-items-md-center card-primary card-outline">
      <div class="ml-md-auto">
       <div class="form-group mb-0">
    <label for="registrationType" class="mb-1">
        <small class="font-weight-bold">መመዝገብ የምትፈልጉትን ይምረጡ</small>
    </label>

    <label>ግንዛቤ የተፈጠረላቸው</label>

<select class="form-control primary" id="awarenessSelect" name="yetefeterelachew" required>
    <option value="" selected disabled>-- ይምረጡ --</option>

    <option value="job_seekers_modal">ለስራ ፈላጊዎች</option>

    <option value="others_modal">
        ለስራ ፈላጊ ወላጆች ወይም ለሌሎች ህብረተሰብ ክፍሎች
    </option>
</select>
</div>
      </div>
    </div>

 


  </div>
</section>


<div class="modal fade" id="others_modal" tabindex="-1" role="dialog" aria-labelledby="othersModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            <form action="<?= rtrim($_ENV['BASE_URL'], '/') ?>/awareness-registration-other-process"
                  method="POST"
                  data-client-validate="true"
                  novalidate>

                <!-- Modal Header -->
                <div class="modal-header">
                    <h6 class="modal-title font-weight-bold" id="othersModalLabel">
                        <i class="fas fa-plus mr-1"></i>
                        ለሌሎች ህብረተሰብ ክፍሎች / ለስራ ፈላጊ ወላጆች መመዝገቢያ
                    </h6>

                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body">

                    <div class="row">

                        <div class="col-md-12">
                            <div class="form-group mb-2">
                                <label for="fullname">
                                    <small class="font-weight-bold">ሙሉ ስም</small>
                                </label>

                                <input
                                    type="text"
                                    class="form-control form-control-sm"
                                    id="fullname"
                                    name="fullname"
                                    maxlength="100"
                                    data-restrict="letters"
                                    required>

                                <div class="invalid-feedback">
                                    እባክዎ ሙሉ ስም ያስገቡ።
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-6">
                            <div class="form-group mb-2">
                                <label for="sex">
                                    <small class="font-weight-bold">ጾታ</small>
                                </label>

                                <select
                                    class="form-control form-control-sm"
                                    id="sex"
                                    name="sex"
                                    required>

                                    <option value="">-- ይምረጡ --</option>
                                    <option value="ወንድ">ወንድ</option>
                                    <option value="ሴት">ሴት</option>

                                </select>

                                <div class="invalid-feedback">
                                    እባክዎ ጾታ ይምረጡ።
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-2">
                                <label for="yemenoriya_akababi">
                                    <small class="font-weight-bold">የመኖሪያ አካባቢ</small>
                                </label>

                                <select
                                    class="form-control form-control-sm"
                                    id="yemenoriya_akababi"
                                    name="yemenoriya_akababi"
                                    required>

                                    <option value="">-- ይምረጡ --</option>
                                    <option value="ከተማ">ከተማ</option>
                                    <option value="ገጠር">ገጠር</option>

                                </select>

                                <div class="invalid-feedback">
                                    እባክዎ የመኖሪያ አካባቢ ይምረጡ።
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-12">
                            <div class="form-group mb-2">
                                <label for="awareness_type">
                                    <small class="font-weight-bold">የግንዛቤ አይነት</small>
                                </label>

                                <select
                                    class="form-control form-control-sm"
                                    id="awareness_type"
                                    name="awareness_type"
                                    required>

                                    <option value="">-- ይምረጡ --</option>

                                    <option value="ለሌሎች ህብረተሰብ ክፍሎች">
                                        ለሌሎች ህብረተሰብ ክፍሎች
                                    </option>

                                    <option value="ለስራ ፈላጊ ወላጆች">
                                        ለስራ ፈላጊ ወላጆች
                                    </option>

                                </select>

                                <div class="invalid-feedback">
                                    እባክዎ የግንዛቤ አይነት ይምረጡ።
                                </div>
                            </div>
                        </div>

                    </div>

                </div>

                <!-- Modal Footer -->
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                        ዝጋ
                    </button>

                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-save mr-1"></i>
                        መዝግብ
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

<div class="modal fade" id="job_seekers_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="<?= htmlspecialchars(rtrim($_ENV['BASE_URL'], '/')) ?>/createupdate-jobseeker-awareness" method="POST">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-users mr-2"></i> ለስራ ፈላጊዎች ግንዛቤ መመዝገቢያ</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span>&times;</span>
                    </button>
                </div>
                
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group position-relative">
                                <label for="search_job_seeker" class="font-weight-bold">ስራ ፈላጊውን ይፈልጉ (በስም፣ ስልክ፣ Labor ID ወይም 8ኛ ክፍል ኮድ)</label>
                                
                                <input type="text" 
                                       id="search_job_seeker" 
                                       class="form-control" 
                                       placeholder="መፃፍ ይጀምሩ (ለምሳሌ፡ Id,ስም፣ ስልክ...)" 
                                       autocomplete="off"
                                       required>
                                       
                                <input type="hidden" id="selected_job_seeker_id" name="job_seeker_id" required readonly>

                                <div id="seeker_suggestions_list" class="list-group position-absolute w-100 mt-1" style="z-index: 1050; display: none; max-height: 220px; overflow-y: auto; box-shadow: 0 4px 12px rgba(0,0,0,0.15); border: 1px solid #ced4da;"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="font-weight-bold">የተመረጠው ስራ ፈላጊ ሙሉ ስም፦</label>
                                <input type="text" id="selected_seeker_name" class="form-control" readonly placeholder="የተመረጠው ሰው ስም እዚህ ይገረፋል...">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-header bg-light">
                    <small class="text-muted"><i class="fas fa-info-circle mr-1"></i> ከዚህ ላይ ስትመዘግቡ የስራ ፈላጊው ግንዛቤ እንደተፈጠረለት ሪፖርት እያደረጋችሁት እንደሆነ ግንዛቤ እንዲኖራችሁ።</small>
                </div>

                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">ዝጋ</button>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-save mr-1"></i> ግንዛቤ ተፈጥሮአል መዝግብ
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<script nonce="<?php echo $GLOBALS['nonce']; ?>">
  document.getElementById('awarenessSelect').addEventListener('change', function () {

    const modalId = this.value;

    if (!modalId) return;

    $('#' + modalId).modal('show');

    // reset select after opening
    this.selectedIndex = 0;
});
</script><script nonce="<?php echo htmlspecialchars($GLOBALS['nonce'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
window.addEventListener('DOMContentLoaded', function() {
    var searchInput = $('#search_job_seeker');
    var suggestionsList = $('#seeker_suggestions_list');
    var hiddenIdInput = $('#selected_job_seeker_id');
    var nameDisplayInput = $('#selected_seeker_name');
    
    // 💡 ቁልፉ መፍትሄ፦ ሰውየው ሲመረጥ የፍለጋ ክስተቱን ለጊዜው ማገጃ ባንዲራ (Flag)
    var isSelecting = false;

    searchInput.on('keyup input', function() {
        // 💡 ሰውየው ከተመረጠ የፍለጋ ስራውን አቁም
        if (isSelecting) {
            return; 
        }

        var query = $(this).val().trim();
        
        if (query.length >= 2) {
            $.ajax({
                url: window.location.origin + '<base href="<?= $_ENV['BASE_URL'] ?>">search-job-seekers-ajax', 
                method: 'GET',
                data: { seeker_q: query },
                dataType: 'json',
                success: function(data) {
                    suggestionsList.empty(); 
                    
                    if (data && data.length > 0) {
                        $.each(data, function(index, item) {
                            var fullName = item.first_name + ' ' + item.father_name + ' ' + item.last_name;
                            var awarenessStatus = (item.awareness == 1) ? ' [ግንዛቤ ከዚህ በፊት እንደወሰደ ሪፖርት ተደርጓል]' : '';
                            var details = fullName + awarenessStatus + ' (Labor ID: ' + (item.Labor_ID ? item.Labor_ID : 'የለም') + ' | ስልክ: ' + (item.phone_number ? item.phone_number : 'የለም') + ')';
                            
                            var option = $('<button type="button" class="list-group-item list-group-item-action py-2 small text-start"></button>');
                            
                            if (item.awareness == 1) {
                                option.addClass('list-group-item-danger');
                            }
                            option.text(details);
                            
                            option.on('click', function(e) {
                                e.preventDefault(); 
                                
                                // 💡 1. ምርጫ መጀመሩን ለሲስተሙ ንገረው (ፍለጋው እንዲቆም)
                                isSelecting = true; 
                                
                                searchInput.val(fullName); 
                                hiddenIdInput.val(item.id); 
                                nameDisplayInput.val(fullName); 
                                suggestionsList.hide(); 
                                
                                // 💡 2. እሴቶቹ ተሞልተው ከተጠናቀቁ በኋላ ፍለጋው ለወደፊቱ ዝግጁ እንዲሆን ባንዲራውን አውርደው
                                setTimeout(function() {
                                    isSelecting = false;
                                }, 200); 
                            });
                            
                            suggestionsList.append(option);
                        });
                        suggestionsList.show(); 
                    } else {
                        suggestionsList.html('<div class="list-group-item text-muted small">ምንም አይነት ስራ ፈላጊ አልተገኘም!</div>').show();
                    }
                },
                error: function(xhr, status, error) {
                    console.error("የ AJAX ስህተት ተከስቷል፦ ", error);
                }
            });
        } else {
            suggestionsList.hide();
            hiddenIdInput.val('');
            nameDisplayInput.val('');
        }
    });

    $(document).on('click', function(e) {
        if (!$(e.target).closest('#search_job_seeker, #seeker_suggestions_list').length) {
            suggestionsList.hide();
        }
    });
});
</script>