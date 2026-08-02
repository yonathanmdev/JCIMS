<!DOCTYPE html>
<html lang="am" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'የስራ እድል እና ኢንተርፕራይዝ አፈጻጸም ሪፖርት'); ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            font-family: 'Nyala', 'PowerGeesz', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f9;
            padding: 15px;
        }

        .report-card {
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            padding: 20px;
        }

        .table-report {
            border: 2px solid #343a40;
            vertical-align: middle;
            text-align: center;
            font-size: 0.82rem;
            width: 100%;
        }

        .table-report th {
            background-color: #f8f9fa;
            color: #111;
            font-weight: 700;
            border: 1px solid #444;
            padding: 6px 3px;
            white-space: nowrap;
        }

        .table-report td {
            border: 1px solid #666;
            padding: 4px 5px;
            white-space: nowrap; /* ቁጥሮች ወደ ታች እንዳይወርዱና ራሳቸውን እንዲይዙ ያደርጋል */
        }

        .section-header {
            background-color: #e9ecef !important;
            font-size: 0.90rem;
            font-weight: bold;
        }

        .sub-header {
            background-color: #f1f3f5 !important;
            font-weight: bold;
        }

        .total-row {
            background-color: #dbe2ef !important;
            font-weight: bold;
            border-top: 2.5px solid #000 !important;
        }

        /* የዞን ስም ዓምድ በቂ ስፋት እንዲኖረውና በግራ እንዲሰለፍ */
        .zone-name {
            text-align: left !important;
            font-weight: 600;
            padding-left: 8px !important;
            min-width: 160px;
        }

        /* አነስተኛ ስፋት ያላቸው ዓምዶች */
        .col-seq { min-width: 35px; }
        .col-rank { min-width: 55px; }
        .col-pct { min-width: 65px; }

        /* ፕሪንት በሚደረግበት ጊዜ የሚሰሩ ስታይሎች */
        @media print {
            @page {
                size: landscape; /* ለብዙ ኮለምኖች በጎን እንዲታተም */
                margin: 8mm;
            }
            body {
                background-color: #fff;
                padding: 0;
            }
            .report-card {
                box-shadow: none;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            .table-report {
                font-size: 0.75rem; /* በህትመት ጊዜ መጠኑ አነስ እንዲል */
            }
            .table-report td, .table-report th {
                padding: 2px 3px !important;
            }
        }
    </style>
</head>
<body>

<div class="container-fluid report-card">
    
    <!-- ርዕስ እና የፕሪንት ቁልፍ -->
<div class="position-relative mb-3">
    <div class="text-center">
        <h5 class="fw-bold text-secondary mt-2">
            የ<?= htmlspecialchars($branchName ?? $defaultBranchName ?? 'ክልል/ቅርንጫፍ'); ?> የስራ እድል ፈጠራ እና የኢንተርፕራይዝ ምሥረታ አፈጻጸም ሪፖርት
        </h5>
    </div>
    <!-- የፕሪንት ቁልፍ ማስተካከያ -->
    <div class="position-absolute top-0 end-0 no-print">
     
    </div>
</div>

<div class="table-responsive">
    <table class="table table-bordered table-hover table-report align-middle text-center">
        <thead>
            <!-- Header Row 1 (Total columns = 1 + 1 + 18 + 7 = 27) -->
            <tr>
                <th rowspan="3" class="col-seq">ተ.ቁ</th>
                <th rowspan="3" class="zone-name">የዞን / ከተማ አስተዳደር ስም</th>
                <th colspan="18" class="section-header">የስራ እድል የተፈጠረላቸው</th>
                <th colspan="7" class="section-header">የኢንተርፕራይዝ ምሥረታ</th>
            </tr>
            <!-- Header Row 2 -->
            <tr>
                <th colspan="6" class="sub-header">ቋሚ</th>
                <th colspan="6" class="sub-header">ጊዜያዊ</th>
                <th colspan="6" class="sub-header">ድምር (ቋሚ + ጊዜያዊ)</th>
                <th colspan="7" class="sub-header">የኢንተርፕራይዝ ምሥረታ በዘርፍ</th>
            </tr>
            <!-- Header Row 3 -->
            <tr>
                <!-- ቋሚ (6 columns) -->
                <th>እቅድ</th><th>ወንድ</th><th>ሴት</th><th>ድምር</th><th class="col-pct">አፈጻጸም %</th><th class="col-rank">ደረጃ</th>
                <!-- ጊዜያዊ (6 columns) -->
                <th>እቅድ</th><th>ወንድ</th><th>ሴት</th><th>ድምር</th><th class="col-pct">አፈጻጸም %</th><th class="col-rank">ደረጃ</th>
                <!-- ድምር (6 columns) -->
                <th>እቅድ</th><th>ወንድ</th><th>ሴት</th><th>ድምር</th><th class="col-pct">አፈጻጸም %</th><th class="col-rank">ደረጃ</th>
                <!-- ኢንተርፕራይዝ (7 columns) -->
                <th>እቅድ</th><th>ግብርና</th><th>ኢንዱስትሪ</th><th>አገልግሎት</th><th>ድምር</th><th class="col-pct">አፈጻጸም %</th><th class="col-rank">ደረጃ</th>
            </tr>
        </thead>
       <tbody>
    <?php
    // የደረጃ ባጅ ማሳያ Helper Function (አፈጻጸማቸው 0 የሆኑትን '-' ያደርጋል)
    function renderRankBadge($rank, $sum = 0) {
        // አፈጻጸሙ/ድምሩ 0 ከሆነ ወይም ደረጃ ከሌለው '-' ያሳያል
        if ($sum <= 0 || $rank <= 0) {
            return '-';
        }
        
        if ($rank == 1) return '<span class="badge bg-warning text-dark p-1 shadow-sm">🥇 1ኛ</span>';
        if ($rank == 2) return '<span class="badge bg-secondary text-white p-1 shadow-sm">🥈 2ኛ</span>';
        if ($rank == 3) return '<span class="badge text-white p-1 shadow-sm" style="background-color: #cd7f32;">🥉 3ኛ</span>';
        if ($rank > 3) return '<span class="badge bg-light text-dark border px-2 py-1">' . $rank . '</span>';
        
        return '-';
    }

    // የጠቅላላ ድምር መያዣ ቫሪያብሎች initialization
    $tot_perm_plan = $tot_perm_m = $tot_perm_f = $tot_perm_sum = 0;
    $tot_temp_plan = $tot_temp_m = $tot_temp_f = $tot_temp_sum = 0;
    $tot_job_plan  = $tot_job_m  = $tot_job_f  = $tot_job_sum  = 0;
    $tot_ent_plan  = $tot_ent_agri = $tot_ent_ind = $tot_ent_serv = $tot_ent_sum = 0;

    if (!empty($branches) && is_array($branches)):
        $i = 1;
        foreach ($branches as $row):
            // የቋሚ ድምሮች
            $tot_perm_plan += $row['perm_plan'] ?? 0;
            $tot_perm_m    += $row['perm_m'] ?? 0;
            $tot_perm_f    += $row['perm_f'] ?? 0;
            $tot_perm_sum  += $row['perm_sum'] ?? 0;

            // የጊዜያዊ ድምሮች
            $tot_temp_plan += $row['temp_plan'] ?? 0;
            $tot_temp_m    += $row['temp_m'] ?? 0;
            $tot_temp_f    += $row['temp_f'] ?? 0;
            $tot_temp_sum  += $row['temp_sum'] ?? 0;

            // የጠቅላላ ስራ እድል ድምሮች
            $tot_job_plan  += $row['tot_job_plan'] ?? 0;
            $tot_job_m     += $row['tot_job_m'] ?? 0;
            $tot_job_f     += $row['tot_job_f'] ?? 0;
            $tot_job_sum   += $row['tot_job_sum'] ?? 0;

            // የኢንተርፕራይዝ ድምሮች
            $tot_ent_plan  += $row['ent_plan'] ?? 0;
            $tot_ent_agri  += $row['ent_agri'] ?? 0;
            $tot_ent_ind   += $row['ent_ind'] ?? 0;
            $tot_ent_serv  += $row['ent_serv'] ?? 0;
            $tot_ent_sum   += $row['ent_sum'] ?? 0;
    ?>
    <tr>
        <td><?= $i++; ?></td>
        <td class="zone-name"><?= htmlspecialchars($row['name'] ?? ''); ?></td>

        <!-- ቋሚ -->
        <td><?= number_format($row['perm_plan'] ?? 0); ?></td>
        <td><?= number_format($row['perm_m'] ?? 0); ?></td>
        <td><?= number_format($row['perm_f'] ?? 0); ?></td>
        <td class="fw-bold"><?= number_format($row['perm_sum'] ?? 0); ?></td>
        <td><?= number_format($row['perm_per'] ?? 0, 2); ?>%</td>
        <td><?= renderRankBadge($row['perm_rank'] ?? 0, $row['perm_sum'] ?? 0); ?></td>

        <!-- ጊዜያዊ -->
        <td><?= number_format($row['temp_plan'] ?? 0); ?></td>
        <td><?= number_format($row['temp_m'] ?? 0); ?></td>
        <td><?= number_format($row['temp_f'] ?? 0); ?></td>
        <td class="fw-bold"><?= number_format($row['temp_sum'] ?? 0); ?></td>
        <td><?= number_format($row['temp_per'] ?? 0, 2); ?>%</td>
        <td><?= renderRankBadge($row['temp_rank'] ?? 0, $row['temp_sum'] ?? 0); ?></td>

        <!-- ድምር (Job Total) -->
        <td class="bg-light"><?= number_format($row['tot_job_plan'] ?? 0); ?></td>
        <td class="bg-light"><?= number_format($row['tot_job_m'] ?? 0); ?></td>
        <td class="bg-light"><?= number_format($row['tot_job_f'] ?? 0); ?></td>
        <td class="fw-bold bg-light"><?= number_format($row['tot_job_sum'] ?? 0); ?></td>
        <td class="bg-light fw-bold"><?= number_format($row['tot_job_per'] ?? 0, 2); ?>%</td>
        <td class="bg-light"><?= renderRankBadge($row['tot_job_rank'] ?? 0, $row['tot_job_sum'] ?? 0); ?></td>

        <!-- ኢንተርፕራይዝ -->
        <td><?= number_format($row['ent_plan'] ?? 0); ?></td>
        <td><?= number_format($row['ent_agri'] ?? 0); ?></td>
        <td><?= number_format($row['ent_ind'] ?? 0); ?></td>
        <td><?= number_format($row['ent_serv'] ?? 0); ?></td>
        <td class="fw-bold"><?= number_format($row['ent_sum'] ?? 0); ?></td>
        <td><?= number_format($row['ent_per'] ?? 0, 2); ?>%</td>
        <td><?= renderRankBadge($row['ent_rank'] ?? 0, $row['ent_sum'] ?? 0); ?></td>
    </tr>
    <?php 
        endforeach;
    else: 
    ?>
    <tr>
        <td colspan="27" class="text-center py-4 text-muted fs-6">
            <i class="fa-solid fa-info-circle me-1"></i> ምንም አይነት የቅርንጫፍ መረጃ አልተገኘም።
        </td>
    </tr>
    <?php endif; ?>
</tbody>

        <?php if (!empty($branches)): ?>
        <tfoot>
            <!-- Total Summary Row -->
            <tr class="total-row fw-bold">
                <td colspan="2" class="text-center">ጠቅላላ በ<?= count($branches); ?> ቅርንጫፎች</td>
                
                <!-- ቋሚ Total -->
                <td><?= number_format($tot_perm_plan); ?></td>
                <td><?= number_format($tot_perm_m); ?></td>
                <td><?= number_format($tot_perm_f); ?></td>
                <td class="fw-bold"><?= number_format($tot_perm_sum); ?></td>
                <td><?= $tot_perm_plan > 0 ? number_format(($tot_perm_sum / $tot_perm_plan) * 100, 2) . '%' : '-'; ?></td>
                <td>-</td>

                <!-- ጊዜያዊ Total -->
                <td><?= number_format($tot_temp_plan); ?></td>
                <td><?= number_format($tot_temp_m); ?></td>
                <td><?= number_format($tot_temp_f); ?></td>
                <td class="fw-bold"><?= number_format($tot_temp_sum); ?></td>
                <td><?= $tot_temp_plan > 0 ? number_format(($tot_temp_sum / $tot_temp_plan) * 100, 2) . '%' : '-'; ?></td>
                <td>-</td>

                <!-- ድምር (Job Total) -->
                <td><?= number_format($tot_job_plan); ?></td>
                <td><?= number_format($tot_job_m); ?></td>
                <td><?= number_format($tot_job_f); ?></td>
                <td class="fw-bold"><?= number_format($tot_job_sum); ?></td>
                <td><?= $tot_job_plan > 0 ? number_format(($tot_job_sum / $tot_job_plan) * 100, 2) . '%' : '-'; ?></td>
                <td>-</td>

                <!-- ኢንተርፕራይዝ Total -->
                <td><?= number_format($tot_ent_plan); ?></td>
                <td><?= number_format($tot_ent_agri); ?></td>
                <td><?= number_format($tot_ent_ind); ?></td>
                <td><?= number_format($tot_ent_serv); ?></td>
                <td class="fw-bold"><?= number_format($tot_ent_sum); ?></td>
                <td><?= $tot_ent_plan > 0 ? number_format(($tot_ent_sum / $tot_ent_plan) * 100, 2) . '%' : '-'; ?></td>
                <td>-</td>
            </tr>
        </tfoot>
        <?php endif; ?>
    </table>
</div>
</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/bootstrap.bundle.min.js"></script>
</body>
</html>