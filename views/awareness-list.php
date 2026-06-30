<?php $is_organization_page = true; ?>
<section class="content">
  <div class="container-fluid">

    <div class="card card-default">
      <div class="card card-primary card-outline">

        <div class="card-body">
        <?php
// 🔒 ከኮንትሮለር የሚመጡ ቫሪያብሎችን ደህንነታቸውን አረጋግጦ ዝግጁ ማድረግ
$currentPage = isset($currentPage) ? (int)$currentPage : 1;
$totalPages  = isset($totalPages) ? (int)$totalPages : 1;
$awarenessList = $awarenessList ?? [];
// የፍለጋ ቃሉ በሳጥኑ ውስጥ እንዳይጠፋ መያዝ
$searchName = isset($_GET['search_name']) ? htmlspecialchars($_GET['search_name'], ENT_QUOTES, 'UTF-8') : '';
?>
<div class="row mb-3">
    <div class="col-md-6">
        <form action="<?= htmlspecialchars(rtrim($_ENV['BASE_URL'], '/')) ?>/awareness-list" method="GET" class="form-inline">
            <div class="input-group input-group-sm w-100">
                <input type="text" 
                       id="search_name_input"
                       name="search_name" 
                       class="form-control" 
                       placeholder="በሙሉ ስም ይፈልጉ..." 
                       value="<?= isset($_GET['search_name']) ? htmlspecialchars($_GET['search_name'], ENT_QUOTES, 'UTF-8') : '' ?>" 
                       maxlength="100"
                       autocomplete="off"
                       list="names_recommendation">
                
                <datalist id="names_recommendation"></datalist>

                <div class="input-group-append">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search mr-1"></i> ፈልግ
                    </button>
                    <?php if (!empty($_GET['search_name'])): ?>
                        <a href="<?= htmlspecialchars(rtrim($_ENV['BASE_URL'], '/')) ?>/awareness-list" class="btn btn-secondary">
                            <i class="fas fa-sync-alt"></i> አጽዳ
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>
</div>
<table id="example1" data-empty-msg="ምንም መረጃ የለም。" class="table table-bordered table-hover dataTable dtr-inline small" style="color: #000;" aria-describedby="example2_info">
    <thead class="thead-light">
        <tr>
            <th>#</th>
            <th>ስም</th>
            <th>ጾታ</th>
            <th>የሚኖርበት አካባቢ</th>
            <th>የግንዛቤ አይነት</th>
            <th>መስሪያ ቤት</th>
            <th class="text-center">ተግባር (Action)</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($awarenessList)): ?>
            <?php foreach ($awarenessList as $index => $awareness): ?>
                <tr>
                    <td><?= (($currentPage - 1) * 5) + ($index + 1) ?></td>
                    <td><?= htmlspecialchars($awareness['fullname'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($awareness['sex'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($awareness['yemenoriya_akababi'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($awareness['awareness_type'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($awareness['name'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="text-center">
                        <button type="button" 
                                class="btn btn-sm btn-primary edit-awareness-btn" 
                                data-id="<?= (int)$awareness['tbleid'] ?>"
                                data-fullname="<?= htmlspecialchars($awareness['fullname'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                data-sex="<?= htmlspecialchars($awareness['sex'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                data-akababi="<?= htmlspecialchars($awareness['yemenoriya_akababi'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                data-type="<?= htmlspecialchars($awareness['awareness_type'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                            <i class="fas fa-edit me-1"></i> አስተካክል
                        </button>
                    </td>  
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="7" class="text-center text-muted py-4">
                    <i class="fas fa-folder-open mr-1"></i> ምንም የግንዛቤ ፈጠራ መረጃ አልተገኘም።
                </td>
            </tr>
        <?php endif; ?> 
    </tbody> 
</table>
<?php if ($totalPages > 1): ?>
<div class="card-footer clearfix bg-white border-top-0 px-0">
    <nav aria-label="Page navigation">
        <ul class="pagination pagination-sm m-0 justify-content-end">
            
            <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                <a class="page-link" href="<?= htmlspecialchars(rtrim($_ENV['BASE_URL'], '/')) ?>/awareness-list?page=<?= $currentPage - 1 ?>">ቀዳሚ</a>
            </li>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= $currentPage == $i ? 'active' : '' ?>">
                    <a class="page-link" href="<?= htmlspecialchars(rtrim($_ENV['BASE_URL'], '/')) ?>/awareness-list?page=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>

            <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                <a class="page-link" href="<?= htmlspecialchars(rtrim($_ENV['BASE_URL'], '/')) ?>/awareness-list?page=<?= $currentPage + 1 ?>">ቀጣይ</a>
            </li>

        </ul>
    </nav>
</div>
<?php endif; ?>

        </div> </div> </div> </div> </section>
        <div class="modal fade" id="editAwarenessModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            
            <form action="<?= htmlspecialchars(rtrim($_ENV['BASE_URL'], '/')) ?>/awareness-update-other-process" method="POST" novalidate>
                
                <input type="hidden" id="edit_tbleid" name="tbleid">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                <div class="modal-header bg-primary text-white">
                    <h6 class="modal-title font-weight-bold" id="editModalLabel">
                        <i class="fas fa-edit mr-1"></i> የግንዛቤ ፈጠራ መረጃ ማስተካከያ
                    </h6>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group mb-2">
                                <label for="edit_fullname"><small class="font-weight-bold">ሙሉ ስም</small></label>
                                <input type="text" class="form-control form-control-sm" id="edit_fullname" name="fullname" maxlength="100" required>
                                <div class="invalid-feedback">እባክዎ ሙሉ ስም ያስገቡ።</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-2">
                                <label for="edit_sex"><small class="font-weight-bold">ጾታ</small></label>
                                <select class="form-control form-control-sm" id="edit_sex" name="sex" required>
                                    <option value="ወንድ">ወንድ</option>
                                    <option value="ሴት">ሴት</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-2">
                                <label for="edit_yemenoriya_akababi"><small class="font-weight-bold">የመኖሪያ አካባቢ</small></label>
                                <select class="form-control form-control-sm" id="edit_yemenoriya_akababi" name="yemenoriya_akababi" required>
                                    <option value="ከተማ">ከተማ</option>
                                    <option value="ገጠር">ገጠር</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group mb-2">
                                <label for="edit_awareness_type"><small class="font-weight-bold">የግንዛቤ አይነት</small></label>
                                <select class="form-control form-control-sm" id="edit_awareness_type" name="awareness_type" required>
                                    <option value="ለሌሎች ህብረተሰብ ክፍሎች">ለሌሎች ህብረተሰብ ክፍሎች</option>
                                    <option value="ለስራ ፈላጊ ወላጆች">ለስራ ፈላጊ ወላጆች</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">ዝጋ</button>
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="fas fa-check-circle mr-1"></i> አሻሽል (Update)
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<script nonce="<?php echo htmlspecialchars($GLOBALS['nonce'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
document.addEventListener('DOMContentLoaded', function () {
    $('.edit-awareness-btn').on('click', function () {
        // 1. መረጃዎቹን ከቁልፉ ላይ መውሰድ
        const tbleid = $(this).data('id');
        const fullname = $(this).data('fullname');
        const sex = $(this).data('sex');
        const akababi = $(this).data('akababi');
        const type = $(this).data('type');

        // 2. በሞዳሉ ፎርም ውስጥ ያሉትን ቦታዎች መሙላት
        $('#edit_tbleid').val(tbleid);
        $('#edit_fullname').val(fullname);
        $('#edit_sex').val(sex);
        $('#edit_yemenoriya_akababi').val(akababi);
        $('#edit_awareness_type').val(type);

        // 3. ሞዳሉን በስክሪኑ ላይ ማሳየት
        $('#editAwarenessModal').modal('show');
    });
}); 
</script>

<script nonce="<?php echo htmlspecialchars($GLOBALS['nonce'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
$(document).ready(function() {
    $('#search_name_input').on('input', function() {
        var query = $(this).val();
        
        // ቢያንስ 2 ፊደል ሲጻፍ መፈለግ እንዲጀምር
        if (query.length >= 2) {
            $.ajax({
                // 🔗 ያንተኑ ዋና ሊንክ በመጠቀም በስተጀርባ ጥያቄ መላክ
                url: '<?= htmlspecialchars(rtrim($_ENV['BASE_URL'], '/')) ?>/awareness-list',
                method: 'GET',
                // ገጹ መደበኛ HTML ሳይሆን የላከውን ስም ዝርዝር (JSON) ብቻ እንዲመልስ መለየት
                data: { q: query, ajax: 1 }, 
                dataType: 'json',
                success: function(data) {
                    var datalist = $('#names_recommendation');
                    datalist.empty(); // የቆየውን ማጽዳት
                    
                    if(data.length > 0) {
                        $.each(data, function(index, value) {
                            datalist.append('<option value="' + value + '">');
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.log("ስህተት፡ " + error);
                }
            });
        }
    });
});
</script>