 
               <section class="content">
  <div class="container-fluid">
    <div class="card card-default">
      <div class="card card-primary card-outline">
        <div class="card-body">
          
          <!-- የገጽ ራስጌ እና አዲስ መመዝገቢያ አዝራር -->
          <div class="row mb-3 align-items-center">
            <div class="col-md-8">
              <h1 class="h3 mb-0 text-gray-800">ዝርዝር መረጃ</h1>
            </div>
          <!--  <div class="col-md-4 text-md-right mt-2 mt-md-0">
              <a href="informal-entrerprise-regstration" class="btn btn-primary btn-sm shadow-sm">
                <i class="fas fa-plus-circle mr-1"></i> አዲስ መዝግብ
              </a>
            </div> -->
          </div>
               <div class="table-responsive">
             <div class="container mt-5 mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
           
            <a href="formal-trade-list" class="btn btn-secondary btn-sm">ወደ ኋላ ተመለስ</a>
        </div>

        <div class="row g-4">
            <!-- 1. የመጀመሪያው ክፍል፡ የተመዝጋቢው መሠረታዊ መረጃ -->
            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">የተመዝጋቢው መሠረታዊ መረጃ</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <tr>
                                <th width="40%">ሙሉ ስም:</th>
                                <td><?= htmlspecialchars($enterprise['registry_full_name'] ?? ''); ?></td>
                            </tr>
                            <tr>
                                <th>ጾታ:</th>
                                <td><?= htmlspecialchars($enterprise['registry_gender'] ?? ''); ?></td>
                            </tr>
                            <tr>
                                <th>ዕድሜ:</th>
                                <td><?= htmlspecialchars($enterprise['registry_age'] ?? ''); ?></td>
                            </tr>
                            <tr>
                                <th>ዞን:</th>
                                <td><?= htmlspecialchars($enterprise['registry_reszone'] ?? ''); ?></td>
                            </tr>
                            <tr>
                                <th>ወረዳ:</th>
                                <td><?= htmlspecialchars($enterprise['registry_resworeda'] ?? ''); ?></td>
                            </tr>
                            <tr>
                                <th>ቀበሌ:</th>
                                <td><?= htmlspecialchars($enterprise['registry_res_kebele'] ?? ''); ?></td>
                            </tr>
                            <tr>
                                <th>ስልክ ቁጥር:</th>
                                <td><?= htmlspecialchars($enterprise['registry_phone'] ?? ''); ?></td>
                            </tr>
                            <tr>
                                <th>የንግድ ቦታ ዓይነት:</th>
                                <td><?= htmlspecialchars($enterprise['registry_trade_area_type'] ?? ''); ?></td>
                            </tr>
                            <tr>
                                <th>የስራ መደብ:</th>
                                <td><?= htmlspecialchars($enterprise['registry_job_position'] ?? ''); ?></td>
                            </tr>
                            <tr>
                                <th>ያመዘገበው ሰራተኛ:</th>
                                <td><?= htmlspecialchars($enterprise['registry_regby'] ?? ''); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 2. ሁለተኛው ክፍል፡ ወደ መደብኛ (Conversion) ሲሸጋገር የተሞላው መረጃ -->
            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">የወደ መደበኛ የሄደበትና እና የድጋፍ መረጃዎች</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <tr>
                                <th width="40%">መታወቂያ ቁጥር (ID):</th>
                                <td><?= htmlspecialchars($enterprise['conversion_informalid'] ?? ''); ?></td>
                            </tr>
                            <tr>
                                <th>የበጀት ዓመት:</th>
                                <td><?= htmlspecialchars($enterprise['conversion_bugetamet'] ?? ''); ?></td>
                            </tr>
                            <tr>
                                <th>ብሔራዊ መታወቂያ (NID):</th>
                                <td><?= htmlspecialchars($enterprise['conversion_nid'] ?? ''); ?></td>
                            </tr>
                            <tr>
                                <th>ዋና ዘርፍ (Sector):</th>
                                <td><span class="badge bg-info text-dark"><?= htmlspecialchars($enterprise['sector_name'] ?? ''); ?></span></td>
                            </tr>
                            <tr>
                                <th>ንዑስ ዘርፍ (Sub-sector):</th>
                                <td><span class="badge bg-secondary"><?= htmlspecialchars($enterprise['subsector_name'] ?? ''); ?></span></td>
                            </tr>
                            <tr>
                                <th>የስራ ዘርፍ/ቦታ ዓይነት:</th>
                                <td><?= htmlspecialchars($enterprise['conversion_trade_area_type'] ?? ''); ?></td>
                            </tr>
                            <tr>
                                <th>የጀመረበት ዓመት:</th>
                                <td><?= htmlspecialchars($enterprise['conversion_start_year'] ?? ''); ?></td>
                            </tr>
                            <tr>
                                <th>ድጋፍ አግኝቷል ወይ?:</th>
                                <td><?= htmlspecialchars($enterprise['conversion_has_support'] ?? ''); ?></td>
                            </tr>
                            <tr>
                                <th>የገንዘብ መጠን:</th>
                                <td><?= htmlspecialchars($enterprise['conversion_financial_amount'] ?? '0'); ?> ብር</td>
                            </tr>
                            <tr>
                                <th>የብድር መጠን:</th>
                                <td><?= htmlspecialchars($enterprise['conversion_loan_amount'] ?? '0'); ?> ብር</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
                    </div>
                    </div>
                    </div>
                    </div>
                    </div>
                    </section>
                    
                    
  