<?php
// የደረጃ ባጅ Helper
function getRankBadge($rank, $totalWork = 0) {
    if ($totalWork <= 0 || $rank <= 0) return '-';
    if ($rank == 1) return '<span class="badge bg-warning text-dark px-2 py-1">🥇 1ኛ</span>';
    if ($rank == 2) return '<span class="badge bg-secondary text-white px-2 py-1">🥈 2ኛ</span>';
    if ($rank == 3) return '<span class="badge text-white px-2 py-1" style="background-color: #cd7f32;">🥉 3ኛ</span>';
    return '<span class="badge bg-light text-dark border px-2 py-1">' . $rank . '</span>';
}

$displayBranchName = !empty($selected_branch_name) ? $selected_branch_name : (!empty($branch_name) ? $branch_name : 'ክልል (ሁሉም ቅርንጫፎች)');
?>

<!-- ቀለል ያለ እና አንድ ላይ የታመቀ የቴብል ቦርደር ስታይል -->
<style>
    #expertReportTable {
        border-collapse: collapse;
        width: 100%;
        font-size: 13px;
    }
    #expertReportTable th, 
    #expertReportTable td {
        border: 1px solid #dcdcdc !important;
        padding: 6px 10px !important;
        vertical-align: middle;
    }
    #expertReportTable thead th {
        background-color: #343a40;
        color: #ffffff;
    }
</style>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center py-2">
        <div>
            <h5 class="mb-0 fs-6"><i class="fa-solid fa-user-check me-2"></i><center><h3>የባለሙያዎች አፈጻጸም እና የደረጃ ተዋረድ ሪፖርት</h3></center></h5>
<?php /*
            <small class="text-warning fw-bold" style="font-size: 12px;">
                📍 የተመረጠው ቅርንጫፍ/አስተዳደር፦ <?= htmlspecialchars($displayBranchName); ?>
            </small>
*/ ?>
        </div>

    </div>
    
    <div class="card-body p-2">
        <div class="table-responsive">
            <table border="1" class="table table-striped text-center mb-0" id="expertReportTable">
                <thead>
                    <tr>
                        <th rowspan="2">ተ.ቁ</th>
                        <th rowspan="2" class="text-start">የባለሙያዋ/ው ሙሉ ስም</th>
                        <th rowspan="2">የመዘገበው/ችው ስራ ፈላጊ</th>
                        <th rowspan="2">የፈጠረው/ችው ግንዛቤ</th>
                        <th rowspan="2">የፈጠረው/ችው የስራ እድል</th>
                        <th rowspan="2">የመሰረተው/ችው ኢንተርፕራይዝ</th>
                        <th rowspan="2">አማካይ የሁሉም ስራ</th>
                        <th colspan="5">የደረጃ ተዋረድ</th>
                    </tr>
                    <tr>
                        <th>እንደ ክልል ያለበት ደረጃ</th>
                        <th>እንደ ዞን ያለበት ደረጃ</th>
                        <th>እንደ ወረዳ/ክፍለ ከተማ ያለበት ደረጃ</th>
                        <th>እንደ ማዕከል ያለበት ደረጃ</th>
                        <th>አማካይ በሁሉም የሚገኝበት ደረጃ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($experts) && is_array($experts)): $i = 1; foreach ($experts as $row): 
                        $totalWork = $row['total_work_avg'] ?? 0;
                        $avgRank = ($totalWork > 0) ? round(($row['region_rank'] + $row['zone_rank'] + $row['woreda_rank'] + $row['center_rank']) / 4, 1) : '-';
                    ?>
                        <tr>
                            <td><?= $i++; ?></td>
                            <td class="text-start fw-bold"><?= htmlspecialchars($row['expert_name'] ?? ''); ?></td>
                            <td><?= number_format($row['reg_job_seekers'] ?? 0); ?></td>
                            <td><?= number_format($row['awareness_created'] ?? 0); ?></td>
                            <td><?= number_format($row['jobs_created'] ?? 0); ?></td>
                            <td><?= number_format($row['ent_created'] ?? 0); ?></td>
                            <td class="fw-bold bg-light"><?= number_format($totalWork); ?></td>
                            
                            <td><?= getRankBadge($row['region_rank'] ?? 0, $totalWork); ?></td>
                            <td><?= getRankBadge($row['zone_rank'] ?? 0, $totalWork); ?></td>
                            <td><?= getRankBadge($row['woreda_rank'] ?? 0, $totalWork); ?></td>
                            <td><?= getRankBadge($row['center_rank'] ?? 0, $totalWork); ?></td>
                            <td class="fw-bold bg-warning text-dark"><?= $avgRank; ?></td>
                        </tr>
                    <?php endforeach; else: ?>
                        <tr>
                            <td colspan="12" class="text-center py-3 text-muted">ምንም አይነት የባለሙያ መረጃ አልተገኘም።</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function exportTableToExcel(tableID, filename = ''){
    let downloadLink;
    let dataType = 'application/vnd.ms-excel';
    let tableSelect = document.getElementById(tableID);
    let tableHTML = tableSelect.outerHTML.replace(/ /g, '%20');
    
    filename = filename ? filename + '.xls' : 'expert_report.xls';
    downloadLink = document.createElement("a");
    document.body.appendChild(downloadLink);
    
    if(navigator.msSaveOrOpenBlob){
        var blob = new Blob(['\ufeff' + tableHTML], { type: dataType });
        navigator.msSaveOrOpenBlob(blob, filename);
    } else {
        downloadLink.href = 'data:' + dataType + ', ' + tableHTML;
        downloadLink.download = filename;
        downloadLink.click();
    }
}
</script>