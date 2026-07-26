<div class="modal fade" id="individualEnterpriseModal" data-enterprise-type="0" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <form id="individualEnterpriseForm" action="<?= rtrim($_ENV['BASE_URL'], '/') ?>/individual-enterprise-registration-process" method="POST">
       <div class="modal-header">
          <h6 class="modal-title font-weight-bold" id="enterpriseModalTitle">
            <i class="fas fa-plus mr-1"></i> የግል ኢንተርፕራይዝ መመዝገቢያ ፎርም
          </h6>
          <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>

        <div class="modal-body">

          <div class="progress mb-3" style="height: 6px;">
            <div class="progress-bar bg-primary" id="formProgressBar" role="progressbar" style="width: 100%;"></div>
          </div>

          <div class="form-step" id="step-1">
            <div class="row">
              <div class="col-12 col-sm-6 col-md-3">
    <div class="form-group mb-2 position-relative linked-entity-group">
      <label class="mb-1"><small class="font-weight-bold">የስራ ፈላጊ ስም ወይም መታወቂያ ያስገቡ <span class="text-danger">*</span></small></label>
      <div class="position-relative">
        <input type="text" class="form-control form-control-sm linked-entity-search" placeholder="የስራ ፈላጊ ስም ወይም መታወቂያ ያስገቡ" autocomplete="off" required>
        <span class="spinner-border spinner-border-sm text-primary position-absolute linked-entity-spinner"
              style="right:10px; top:8px; display:none;" role="status" aria-hidden="true"></span>
      </div>
      <input type="hidden" class="linked-entity-id" name="linked_entity_id">
      <input type="hidden"  name="enterprise_type" value="0">
      <input type="hidden" class="linked-entity-project-type" name="linked_entity_project_type">
      <div class="list-group position-absolute linked-entity-results"
           style="z-index:1050; width:100%; max-height:200px; overflow-y:auto; display:none;"></div>
    </div>
  </div>

              <div class="col-12 col-sm-6 col-md-3">
                <div class="form-group mb-2">
                  <label class="mb-1" for="enterprise_name"><small class="font-weight-bold">ኢንተርፕራይዝ ስም <span class="text-danger">*</span></small></label>
                  <input type="text" class="form-control form-control-sm" id="enterprise_name" name="enterprise_name" required readonly>
                </div>
              </div>

              <div class="col-12 col-sm-6 col-md-3">
                <div class="form-group mb-2">
                  <label class="mb-1" for="tin_number"><small class="font-weight-bold">የግብር መክፈያ መለያ ቁጥር <span class="text-danger">*</span></small></label>
                  <input type="text" class="form-control form-control-sm" id="tin_number" name="tin_number" required>
                </div>
              </div>
            
              <div class="col-12 col-sm-6 col-md-3">
                <div class="form-group mb-2">
                  <label class="mb-1" for="yeedget_dereja"><small class="font-weight-bold">የእድገት ደረጃ <span class="text-danger">*</span></small></label>
                  <select class="form-control form-control-sm" id="yeedget_dereja" name="yeedget_dereja" required>
                    <option value="" selected disabled>ይምረጡ</option>
                    <option value="0">ጥቃቅን ጀማሪ</option>
                    <option value="1">ጥቃቅን ታዳጊ</option>
                    <option value="2">ጥቃቅን የበቃ</option>
                    <option value="3">አነስተኛ ጀማሪ</option>
                    <option value="4">አነስተኛ ታዳጊ</option>
                    <option value="5">አነስተኛ የበቃ</option>
                    <option value="6">ታዳጊ መካከለኛ</option>
                  </select>
                </div>
              </div>
           
              <div class="col-12 col-sm-6 col-md-3">
                <div class="form-group mb-2">
                  <label class="mb-1" for="initial_capital"><small class="font-weight-bold">መነሻ ካፒታል <span class="text-danger">*</span></small></label>
                  <input type="number" step="any" class="form-control form-control-sm" id="initial_capital" name="initial_capital" required>
                </div>
              </div>

              <div class="col-12 col-sm-6 col-md-3">
                <div class="form-group mb-2">
                  <label class="mb-1" for="yehabtu_mnch"><small class="font-weight-bold">የሃብቱ ምንጭ <span class="text-danger">*</span></small></label>
                  <select class="form-control form-control-sm" id="yehabtu_mnch" name="yehabtu_mnch" required>
                    <option value="" selected disabled>ይምረጡ</option>
                    <option value="0">በራስ ተቀማጭ</option>
                    <option value="1">ከቤተሰብ</option>
                    <option value="2">ከመንግስት</option>
                    <option value="3">ብድር</option>
                  </select>
                </div>
              </div>

              <div class="col-12 col-sm-6 col-md-3">
                <div class="form-group mb-2">
                  <label class="mb-1" for="wektawi_yehabt_meten"><small class="font-weight-bold">ወቅታዊ የሃብት መጠን <span class="text-danger">*</span></small></label>
                  <input type="text" class="form-control form-control-sm" id="wektawi_yehabt_meten" name="wektawi_yehabt_meten" required>
                </div>
              </div>
            
              <div class="col-12 col-sm-6 col-md-3">
                <div class="form-group mb-2">
                  <label class="mb-1" for="yemrt_ayinet"><small class="font-weight-bold">የምርት ዓይነት <span class="text-danger">*</span></small></label>
                  <input type="text" class="form-control form-control-sm" id="yemrt_ayinet" name="yemrt_ayinet" required>
                </div>
              </div>
          
              <div class="col-12 col-sm-6 col-md-3">
                <div class="form-group mb-2">
                  <label class="mb-1" for="yemikerb_hager_weys_lewuch"><small class="font-weight-bold">ምርቱ የሚቀርበው </small></label>
                  <select class="form-control form-control-sm" id="yemikerb_hager_weys_lewuch" name="yemikerb_hager_weys_lewuch" required>
                    <option value="" selected disabled>ይምረጡ</option>
                    <option value="ለሃገር ውስጥ">ለሃገር ውስጥ</option>
                    <option value="ለውጭ ሃገር">ለውጭ ሃገር</option>
                  </select>
                </div>
              </div>

              <div class="col-12 col-sm-6 col-md-3">
                <div class="form-group mb-2">
                  <label class="mb-1" for="starting_capital_in_kind">
                    <small class="font-weight-bold">መነሻ ሃብት በአይነት( ምሳሌ፦ 500 ዶሮ) </small>
                  </label>
                  <input type="text" class="form-control form-control-sm" id="starting_capital_in_kind" name="starting_capital_in_kind">
                </div>
              </div>

              <div class="col-12 col-sm-6 col-md-3">
                <div class="form-group mb-2">
                  <label class="mb-1" for="yeaderejajet_ayinet"><small class="font-weight-bold">ኢንተርፕሪዙ የተመሰረተበት ሁኔታ <span class="text-danger">*</span></small></label>
                  <select class="form-control form-control-sm" id="yeaderejajet_ayinet" name="yeaderejajet_ayinet">
                    <option value="" selected disabled>ይምረጡ</option>
                    <option value="የመንግስት">በመንግስት</option>
                    <option value="በራስ ፍላጎት">በራስ ፍላጎት</option>
                    <option value="NGO">በNGO</option>
                    <option value="ሌላ">በሌላ</option>
                  </select>
                </div>
              </div>
          
              <div class="col-12 col-sm-6 col-md-3">
                <div class="form-group mb-2">
                  <label class="mb-1" for="ngo_id"><small class="font-weight-bold">NGO ይምረጡ</small></label>
                  <select class="form-control form-control-sm" id="ngo_id" name="ngo_id">
                    <option value="">-- NGO ይምረጡ --</option>
                    <?php foreach ($projects ?? [] as $project): ?>
                      <option value="<?= htmlspecialchars($project['pid']) ?>"><?= htmlspecialchars($project['pname']) ?></option>
                    <?php endforeach; ?>
                  </select>
                  <div class="invalid-feedback">እባክዎ NGO ይምረጡ!</div>
                </div>
              </div>

              <div class="col-12 col-sm-6 col-md-3">
                <div class="form-group mb-2">
                  <label class="mb-1" for="sector_id"><small class="font-weight-bold">ዘርፍ <span class="text-danger">*</span></small></label>
                  <select class="form-control form-control-sm" id="sector_id" name="sector_id" data-cascade-target="subsector_id">
                    <option value="" selected disabled>ይምረጡ</option>
                    <?php foreach ($sectorData['sectors'] as $sector): ?>
                      <option value="<?= htmlspecialchars($sector['id']) ?>"><?= htmlspecialchars($sector['name']) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>

              <div class="col-12 col-sm-6 col-md-3">
                <div class="form-group mb-2">
                  <label class="mb-1" for="subsector_id"><small class="font-weight-bold">ንዑስ ዘርፍ <span class="text-danger">*</span></small></label>
                  <select class="form-control form-control-sm" id="subsector_id" name="subsector_id">
                    <option value="">-- ንዑስ ዘርፍ ይምረጡ --</option>
                  </select>
                </div>
              </div>
            
              <div class="col-12 col-sm-6 col-md-3">
                <div class="form-group mb-2">
                  <label class="mb-1" for="yesra_mesk"><small class="font-weight-bold">የስራ መስክ</small></label>
                  <input type="text" class="form-control form-control-sm" id="yesra_mesk" name="yesra_mesk">
                </div>
              </div>

              <div class="col-12 col-sm-6 col-md-3">
                <div class="form-group mb-2">
                  <label class="mb-1" for="yetederege_dgaf"><small class="font-weight-bold">የተደረገ ድጋፍ <span class="text-danger">*</span></small></label>
                  <select class="form-control form-control-sm" id="yetederege_dgaf" name="yetederege_dgaf" required>
                    <option value="" selected disabled>ይምረጡ</option>
                    <option value="1">አለ</option>
                    <option value="0">የለም</option>
                  </select>
                </div>
              </div>
          
              <div class="col-12 col-sm-6 col-md-3">
                <div class="form-group mb-2">
                  <label class="mb-1" for="org-selectsuport"><small class="font-weight-bold">ድጋፍ የተደርገለት በማን ነው?</small></label>
                  <select class="form-control form-control-sm" name="org_type_suport" id="org-selectsuport">
                    <option value="" selected disabled>-- ይምረጡ --</option>
                    <option value="bemengst">በመንግስት</option>
                    <option value="bgelu">በግል</option>
                    <option value="benterprise">በኢንተርፕራይዝ</option>
                    <option value="beproject">በፕሮጀክት(NGO)</option>
                    <option value="belela">በሌላ</option>
                  </select>
                </div>
              </div>

              <div class="col-12 col-sm-6 col-md-3">
                <div class="form-group mb-2">
                  <label class="mb-1" for="supported_by"><small class="font-weight-bold">ድጋፍ ያደረገው አካል<span class="text-danger">*</span></small></label>
                  <input type="text" class="form-control form-control-sm" id="supported_by" name="supported_by">
                </div>
              </div>

              
               <div class="col-12 col-sm-6 col-md-3">
                <div class="form-group mb-2">
                  <label class="mb-1" for="supported_items"><small class="font-weight-bold">የድጋፍ ዓይነት<span class="text-danger">*</span></small></label>
                  <input type="text" class="form-control form-control-sm" id="supported_items" name="supported_items">
                </div>
              </div>

              <div class="col-12 col-sm-6 col-md-3">
                <div class="form-group mb-2">
                  <label class="mb-1" for="yeminorubet_acababi"><small class="font-weight-bold">የተመሰረተበት አካባቢ <span class="text-danger">*</span></small></label>
                  <select class="form-control form-control-sm" id="yeminorubet_acababi" name="yeminorubet_acababi">
                    <option value="" selected disabled>ይምረጡ</option>
                    <option value="ከተማ">ከተማ</option>
                    <option value="ገጠር">ገጠር</option>
                  </select>
                </div>
              </div>
         
              <div class="col-12 col-sm-6 col-md-3">
                <div class="form-group mb-2">
                  <label class="mb-1" for="eth_start_date">
                    <small class="font-weight-bold">የተመሰረትበት ቀን <span class="text-danger">*</span></small>
                  </label>
                  <input type="text"
                         class="ethiopian-date form-control"
                         name="eth_start_date"
                         id="eth_start_date"
                         data-rule="past"
                         data-gregorian="#established_date"
                         placeholder="ቀን/ወር/ዓ.ም"
                         readonly
                         style="background-color: #fff; cursor: pointer;">
                  <input type="date" class="d-none" id="established_date" name="established_date">
                </div>
              </div>
            </div>

          </div>

          <p class="text-muted">ከመመዝገብዎ በፊት የሞሉቱን መረጃ ትክክለኛነት ያረጋግጡ።</p>

        </div>

        <div class="modal-footer d-flex justify-content-between">
          <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">ዝጋ</button>
          <div>
            <button type="submit" class="btn btn-primary btn-sm" id="submitBtn">መዝግብ</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>


<div class="modal fade" id="associationEnterpriseModal" data-enterprise-type="1" tabindex="-1" role="dialog">
<div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <form id="associationEnterpriseForm" action="<?= rtrim($_ENV['BASE_URL'], '/') ?>/enterprise-registration-process" method="POST">
         <div class="modal-header">
          <h6 class="modal-title font-weight-bold" id="enterpriseModalTitle">
            <i class="fas fa-plus mr-1"></i> የማህበር ኢንተርፕራይዝ መመዝገቢያ ፎርም
          </h6>
          <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>



  <div class="modal-body">
    <div class="progress mb-3" style="height: 6px;">
    <div class="progress-bar bg-primary" id="formProgressBar" role="progressbar" style="width: 100%;"></div>
    </div>

    <div class="form-step" id="step-1">
    <div class="row">
    <div class="col-12 col-sm-6 col-md-3">
    <div class="form-group mb-2 position-relative linked-entity-group">
      <label class="mb-1"><small class="font-weight-bold">የማህበሩን ስም(መ/ቁ) ያስገቡ  <span class="text-danger">*</span></small></label>
      <div class="position-relative">
        <input type="text" class="form-control form-control-sm linked-entity-search" placeholder="የማህበሩን ስም( መ/ቁ)" autocomplete="off" required>
        <span class="spinner-border spinner-border-sm text-primary position-absolute linked-entity-spinner"
              style="right:10px; top:8px; display:none;" role="status" aria-hidden="true"></span>
      </div>
      <input type="hidden" class="linked-entity-id" name="linked_entity_id">
      <input type="hidden"  name="enterprise_type" value="1">
      <input type="hidden" class="linked-entity-project-type" name="linked_entity_project_type">
      <div class="list-group position-absolute linked-entity-results"
           style="z-index:1050; width:100%; max-height:200px; overflow-y:auto; display:none;"></div>
    </div>
  </div>

              <div class="col-12 col-sm-6 col-md-3">
                <div class="form-group mb-2">
                  <label class="mb-1" for="enterprise_name_assoc"><small class="font-weight-bold">ኢንተርፕራይዝ ስም <span class="text-danger">*</span></small></label>
                  <input type="text" class="form-control form-control-sm" id="enterprise_name_assoc" name="enterprise_name" required readonly>
                </div>
              </div>

              <div class="col-12 col-sm-6 col-md-3">
                <div class="form-group mb-2">
                  <label class="mb-1" for="tin_number_assoc"><small class="font-weight-bold">የግብር መክፈያ መለያ ቁጥር <span class="text-danger">*</span></small></label>
                  <input type="text" class="form-control form-control-sm" id="tin_number_assoc" name="tin_number" required>
                </div>
              </div>

              <div class="col-12 col-sm-6 col-md-3">
                <div class="form-group mb-2">
                  <label class="mb-1" for="yeedget_dereja_assoc"><small class="font-weight-bold">የእድገት ደረጃ <span class="text-danger">*</span></small></label>
                  <select class="form-control form-control-sm" id="yeedget_dereja_assoc" name="yeedget_dereja" required>
                    <option value="" selected disabled>ይምረጡ</option>
                    <option value="0">ጥቃቅን ጀማሪ</option>
                    <option value="1">ጥቃቅን ታዳጊ</option>
                    <option value="2">ጥቃቅን የበቃ</option>
                    <option value="3">አነስተኛ ጀማሪ</option>
                    <option value="4">አነስተኛ ታዳጊ</option>
                    <option value="5">አነስተኛ የበቃ</option>
                    <option value="6">ታዳጊ መካከለኛ</option>
                  </select>
                </div>
              </div>

              <div class="col-12 col-sm-6 col-md-3">
                <div class="form-group mb-2">
                  <label class="mb-1" for="initial_capital_assoc"><small class="font-weight-bold">መነሻ ካፒታል <span class="text-danger">*</span></small></label>
                  <input type="number" step="any" class="form-control form-control-sm" id="initial_capital_assoc" name="initial_capital" required>
                </div>
              </div>

              <div class="col-12 col-sm-6 col-md-3">
                <div class="form-group mb-2">
                  <label class="mb-1" for="yehabtu_mnch_assoc"><small class="font-weight-bold">የሃብቱ ምንጭ <span class="text-danger">*</span></small></label>
                  <select class="form-control form-control-sm" id="yehabtu_mnch_assoc" name="yehabtu_mnch" required>
                    <option value="" selected disabled>ይምረጡ</option>
                    <option value="0">በራስ ተቀማጭ</option>
                    <option value="1">ከቤተሰብ</option>
                    <option value="2">ከመንግስት</option>
                    <option value="3">ብድር</option>
                  </select>
                </div>
              </div>

              <div class="col-12 col-sm-6 col-md-3">
                <div class="form-group mb-2">
                  <label class="mb-1" for="wektawi_yehabt_meten_assoc"><small class="font-weight-bold">ወቅታዊ የሃብት መጠን <span class="text-danger">*</span></small></label>
                  <input type="text" class="form-control form-control-sm" id="wektawi_yehabt_meten_assoc" name="wektawi_yehabt_meten" required>
                </div>
              </div>
           
              <div class="col-12 col-sm-6 col-md-3">
                <div class="form-group mb-2">
                  <label class="mb-1" for="yemrt_ayinet_assoc"><small class="font-weight-bold">የምርት ዓይነት <span class="text-danger">*</span></small></label>
                  <input type="text" class="form-control form-control-sm" id="yemrt_ayinet_assoc" name="yemrt_ayinet" required>
                </div>
              </div>

              <div class="col-12 col-sm-6 col-md-3">
                <div class="form-group mb-2">
                  <label class="mb-1" for="yemikerb_hager_weys_lewuch_assoc"><small class="font-weight-bold">ምርቱ የሚቀርበው <span class="text-danger">*</span></small></label>
                  <select class="form-control form-control-sm" id="yemikerb_hager_weys_lewuch_assoc" name="yemikerb_hager_weys_lewuch" required>
                    <option value="" selected disabled>ይምረጡ</option>
                    <option value="ለሃገር ውስጥ">ለሃገር ውስጥ</option>
                    <option value="ለውጭ ሃገር">ለውጭ ሃገር</option>
                  </select>
                </div>
              </div>

              <div class="col-12 col-sm-6 col-md-3">
                <div class="form-group mb-2">
                  <label class="mb-1" for="starting_capital_in_kind_assoc">
                    <small class="font-weight-bold">መነሻ ሃብት በአይነት(ምሳሌ፦ 500 ዶሮ) </small>
                  </label>
                  <input type="text" class="form-control form-control-sm" id="starting_capital_in_kind_assoc" name="starting_capital_in_kind">
                </div>
              </div>
       
              <div class="col-12 col-sm-6 col-md-3">
                <div class="form-group mb-2">
                  <label class="mb-1" for="yetederege_dgaf_assoc"><small class="font-weight-bold">የተደረገ ድጋፍ <span class="text-danger">*</span></small></label>
                  <select class="form-control form-control-sm" id="yetederege_dgaf_assoc" name="yetederege_dgaf" required>
                    <option value="" selected disabled>ይምረጡ</option>
                    <option value="1">አለ</option>
                    <option value="0">የለም</option>
                  </select>
                </div>
              </div>       

              <div class="col-12 col-sm-6 col-md-3">
                <div class="form-group mb-2">
                  <label class="mb-1" for="org-selectsuport_assoc"><small class="font-weight-bold">ድጋፍ የተደረገው በማን ነው? <span class="text-danger">*</span></small></label>
                  <select class="form-control form-control-sm" name="org_type_suport" id="org-selectsuport_assoc">
                    <option value="" selected disabled>-- ይምረጡ --</option>
                    <option value="bemengst">በመንግስት</option>
                    <option value="bgelu">በግል</option>
                    <option value="benterprise">በኢንተርፕራይዝ</option>
                    <option value="beproject">በፕሮጀክት(NGO)</option>
                    <option value="belela">በሌላ</option>
                  </select>
                </div>
              </div>
              <div class="col-12 col-sm-6 col-md-3">
                <div class="form-group mb-2">
                  <label class="mb-1" for="ngo_id_assoc"><small class="font-weight-bold">NGO ይምረጡ</small></label>
                  <select class="form-control form-control-sm" id="ngo_id_assoc" name="ngo_id">
                    <option value="">-- NGO ይምረጡ --</option>
                    <?php foreach ($projects ?? [] as $project): ?>
                      <option value="<?= htmlspecialchars($project['pid']) ?>"><?= htmlspecialchars($project['pname']) ?></option>
                    <?php endforeach; ?>
                  </select>
                  <div class="invalid-feedback">እባክዎ NGO ይምረጡ!</div>
                </div>
              </div>

              <div class="col-12 col-sm-6 col-md-3">
                <div class="form-group mb-2">
                  <label class="mb-1" for="supported_by_assoc"><small class="font-weight-bold">ድጋፍ ያደረገው አካል <span class="text-danger">*</span></small></label>
                  <input type="text" class="form-control form-control-sm" id="supported_by_assoc" name="supported_by">
                </div>
              </div>

              
               <div class="col-12 col-sm-6 col-md-3">
                <div class="form-group mb-2">
                  <label class="mb-1" for="supported_items_assoc"><small class="font-weight-bold">የድጋፉ አይነት <span class="text-danger">*</span></small></label>
                  <input type="text" class="form-control form-control-sm" id="supported_items_assoc" name="supported_items">
                </div>
              </div>
  
              <div class="col-12 col-sm-6 col-md-3">
                <div class="form-group mb-2">
                  <label class="mb-1" for="eth_start_date">
                    <small class="font-weight-bold">የተመሰረትበት ቀን <span class="text-danger">*</span></small>
                  </label>
                  <input type="text"
                         class="ethiopian-date form-control"
                         name="eth_start_date"
                         id="eth_start_date_assoc"
                         data-rule="past"
                         data-gregorian="#established_date_assoc"
                         placeholder="ቀን/ወር/ዓ.ም"
                         readonly
                         style="background-color: #fff; cursor: pointer;">
                  <input type="date" class="d-none" id="established_date_assoc" name="established_date">
                </div>
              </div>
            </div>
          </div>

          <p class="text-muted">ከመመዝገብዎ በፊት የሞሉቱን መረጃ ትክክለኛነት ያረጋግጡ።</p>
        </div>

        <div class="modal-footer d-flex justify-content-between">
          <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">ዝጋ</button>
          <div>
            <button type="submit" class="btn btn-primary btn-sm" id="submitBtn">መዝግብ</button>
          </div>
        </div>

      </form>
    </div>
  </div>
</div>