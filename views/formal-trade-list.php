 
               <section class="content">
  <div class="container-fluid">
    <div class="card card-default">
      <div class="card card-primary card-outline">
        <div class="card-body">
          
          <!-- የገጽ ራስጌ እና አዲስ መመዝገቢያ አዝራር -->
          <div class="row mb-3 align-items-center">
            <div class="col-md-8">
              <h1 class="h3 mb-0 text-gray-800">የመደበኛ ንግድ ተሰማሪዎች ዝርዝር</h1>
            </div>
          <!--  <div class="col-md-4 text-md-right mt-2 mt-md-0">
              <a href="informal-entrerprise-regstration" class="btn btn-primary btn-sm shadow-sm">
                <i class="fas fa-plus-circle mr-1"></i> አዲስ መዝግብ
              </a>
            </div> -->
          </div>
               <div class="table-responsive">
            <table id="example1" class="table table-bordered table-hover small text-center align-middle">
              <thead class="bg-light">   <tr>
                                    <th>ተ.ቁ</th>
                                    <th>ሙሉ ስም (Full Name)</th>
                                    <th>ስልክ ቁጥር</th>
                                    <th>ጾታ</th>
                                 
                                    <th>የስራ ዘርፍ (Sector)</th>
                                    <th>ንዑስ ዘርፍ</th>
                                    <th>መስክ</th>
                                    <th>ድርጊት</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($enterprises) && is_array($enterprises)): ?>
                                    <?php $no = 1; foreach ($enterprises as $row): ?>
                                        <tr>
                                            <td class="text-center"><?= $no++; ?></td>
                                            <!-- htmlspecialchars እና Null Coalescing (?? '') በመጠቀም ዋርኒንግ እና XSS ጥቃትን መከላከል -->
                                            <td><?= htmlspecialchars($row['registry_full_name'] ?? ''); ?></td>
                                            <td><?= htmlspecialchars($row['registry_phone'] ?? ''); ?></td>
                                            <td class="text-center"><?= htmlspecialchars($row['registry_gender'] ?? ''); ?></td>
                                             <td><?= htmlspecialchars($row['sector_name'] ?? ''); ?></td>
                                            <td><?= htmlspecialchars($row['subsector_name'] ?? ''); ?></td>
                                             <td class="text-center"><?= htmlspecialchars($row['conversion_job_position'] ?? ''); ?></td>
                                          
                                            <td class="text-center">
                                                <a href="details?id=<?= htmlspecialchars($row['conversion_id'] ?? 0); ?>" class="btn btn-sm btn-info text-white">
                                                    ዝርዝር
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center text-danger py-4 fw-bold">
                                            ምንም የተመዘገበ መረጃ አልተገኘም!
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    </div>
                    </div>
                    </div>
                    </div>
                    </section>
                    
                    
  