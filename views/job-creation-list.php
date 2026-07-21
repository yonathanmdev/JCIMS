 
<?php
use App\Helpers\EthiopianDateHelper; 
use App\Helpers\AuthHelper;
$fiscal_year = AuthHelper::checkFiscalYear();
 $is_sra_edl_page = true;
$totalCount =0;
$offset = 0;
$currentPage =1;
$totalPages =1;
?>
<section class="content">
  <div class="container-fluid">
    <div class="card card-default">
      <div class="card card-primary card-outline">
        <div class="card-body">
          
          <!-- የራስጌ ክፍል (Header) -->
          <div class="row mb-3">
            <div class="col-md-6">
              <h1 class="h3 mb-0 text-gray-800">ስራ እድል የተፈጠረላቸዉን ዝርዝር</h1>
            </div>  
          </div>

          <!-- የሰንጠረዥ ማሳያ ካርድ -->
          <div class="container-fluid mt-4">
            <div class="card">
              <div class="card-body">
                
                <!-- 1. ዋናው ሰንጠረዥ (ንጹህ እና የተስተካከለ) -->
                <table id="example1" data-empty-msg="ምንም መረጃ የለም" class="table table-bordered table-hover dataTable dtr-inline small" style="color: #000;" aria-describedby="example2_info">
                    <thead class="thead-light">
                        <tr>
                            <th>ተ.ቁ</th>
                            <th>የስራ ፈላጊ ስም</th>
                            <th>የስራ ዘርፍ</th>
                            <th>ንዑስ ሴክተር</th>
                            <th>የስራ አይነት</th>
                            <th>የሥራ እድል የተፈጠረባቸው</th>
                            <th>ተግባር</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($jobCreations)){ ?>
                        <?php foreach ($jobCreations as $index => $row): ?>
                        <tr>
                            <td><?= (($currentPage - 1) * 30) + $index + 1 ?></td>
                            <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['father_name']) ?></td>
                            <td><?= htmlspecialchars($row['sector']) ?></td>
                            <td><?= htmlspecialchars($row['subsector'] ?? 'አልተመረጠም') ?></td>
                            <td><?= $row['employment_type'] == '1' ? 'ቋሚ' : 'ጊዜያዊ' ?></td>
                            <td><?= htmlspecialchars($row['job_creation_reason']) ?></td>
                            <td>
                                <!-- ዝርዝር ማሳያ አዝራር -->
                                <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#detailModal<?= bin2hex($row['uuid']) ?>">
                                    ዝርዝር እይ
                                </button>
                                <!-- ሰርዝ አዝራር -->
          <?php
// 1. የ Role/Level ማጣሪያ (እንግዲህ ይህ ኦፊሰር እና ሌቨል 4 መሆኑን ያረጋግጣል)
$hasPermission = AuthHelper::hasRole(['officer'], [4]);

// 2. የጊዜ ማጣሪያ
// መረጃው የተመዘገበበት ቀን ከዛሬ 7 ቀን በታች ከሆነ (ማለትም አዲስ ከሆነ)
$registrationDate = strtotime($row['created_at']);
$sevenDaysAgo = strtotime('-7 days');

// መረጃው ከ7 ቀን በታች ከሆነ (ማለትም እስካሁን አልሞላውም) እና መብቱ ካለው
$isWithin7Days = ($registrationDate > $sevenDaysAgo);
// ሁለቱም ሁኔታዎች ሲሟሉ ብቻ በተኑ ይታያል
if ($hasPermission && $isWithin7Days): 
?>
    <a href="<?= rtrim($_ENV['BASE_URL'], '/') ?>/job-creation-delete?uuid=<?= $row['uuid'] ?>&branchid=<?= $row['branchid'] ?>&jobseeker_id=<?= $row['jobseeker_id'] ?>" 
       class="btn btn-danger btn-sm delete-btn">
       ሰርዝ
    </a>
<?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php } else { ?>
                        <tr>
                            <td colspan="7" class="text-center">ምንም መረጃ የለም</td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>

                <!-- 2. ገጽ ማውጫ (Pagination) -->
                <nav class="mt-3">
                    <ul class="pagination justify-content-center">
                        <?php for($i=1; $i<=$totalPages; $i++): ?>
                            <li class="page-item <?= ($i == $currentPage) ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>

              </div>
            </div>
          </div>

        </div> <!-- /.card-body -->
      </div> <!-- /.card-primary -->
    </div> <!-- /.card-default -->
  </div> <!-- /.container-fluid -->
</section>

<!-- 3. ሞዳሎች (ከላይ ካለው ሰንጠረዥ እና ዲዛይን ሙሉ በሙሉ ውጭ መሆን አለባቸው) -->
<?php if(!empty($jobCreations)){ ?>
<?php foreach ($jobCreations as $row): ?>
<div class="modal fade" id="detailModal<?= bin2hex($row['uuid']) ?>" tabindex="-1" role="dialog" aria-labelledby="modalTitle<?= bin2hex($row['uuid']) ?>" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle<?= bin2hex($row['uuid']) ?>">የስራ እድል ዝርዝር መረጃ</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-striped table-bordered">
                    <tbody>
                        <tr>
                            <th style="width: 30%;">የስራ ፈላጊ ስም</th>
                            <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['father_name']) ?></td>
                        </tr>
                        <tr>
                            <th>የስራ ዘርፍ</th>
                            <td><?= htmlspecialchars($row['sector']) ?></td>
                        </tr>
                        <tr>
                            <th>ንዑስ ሴክተር</th>
                            <td><?= htmlspecialchars($row['subsector'] ?? 'አልተመረጠም') ?></td>
                        </tr>
                         <tr>
                            <th>የተሰማራበት መስክ</th>
                            <td><?= htmlspecialchars($row['job_field'] ?? 'አልተመረጠም') ?></td>
                        </tr>
                        <tr>
                            <th>የስራ አይነት</th>
                            <td><?= $row['employment_type'] == '1' ? 'ቋሚ' : 'ጊዜያዊ' ?></td>
                        </tr>
                        <tr>
                            <th>የተፈጠረበት ምክንያት</th>
                            <td><?= htmlspecialchars($row['job_creation_reason']) ?></td>
                        </tr>
                        <tr>
                            <th>የተቀጠረበት ተቋም</th>
                            <td><?= htmlspecialchars($row['employed_institution'] ?? 'አልተጠቀሰም') ?></td>
                        </tr>
                        <tr>
                            <th>ድጋፍ ያደረገ</th>
                            <td><?= htmlspecialchars($row['ngo_name'] ?? 'ምንም') ?></td>
                        </tr>
                        <tr>
                            <th>ስራ እድሉ የተፈጠረበት ማእከል/ወረዳ</th>
                            <td><?= htmlspecialchars($row['branch_name'] ?? 'አልተጠቀሰም') ?></td>
                        </tr>
                        <tr>
                            <th>የተመዘገበበት ዓ.ም</th>
                            <td><?= htmlspecialchars($row['fiscal_year'] ?? 'አልተጠቀሰም') ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">ዝጋ</button>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>
<?php } ?>

<!-- 4. የአገር በቀል (Vanilla JS) ሰርዝ ማረጋገጫ ስክሪፕት ከ CSP Nonce ጋር -->
<script nonce="<?php echo htmlspecialchars($GLOBALS['nonce'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm('እርግጠኛ ነዎት ይህን መረጃ መሰረዝ ይፈልጋሉ?')) {
                e.preventDefault();
            }
        });
    });
});
</script>