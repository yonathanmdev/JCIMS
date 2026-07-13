<?php
use App\Helpers\EthiopianDateHelper;

// ከኮንትሮለር የመጣው ዳታ መኖሩን ማረጋገጥ (ከሌለ በባዶዎች ይተካል)
$r = $report10 ?? [];

// ዳታው ከሌለ 0 እንዲያሳይ የማድረጊያ አጋዥ ፈንክሽን
function formatNum($val) {
    return isset($val) && $val !== '' ? (int)$val : 0;
}
?>
<!DOCTYPE html>
<html lang="am">
<head>
    <meta charset="UTF-8">
    <title>የስራ እድል ፈጠራ አካታችነት ሪፖርት በከተማና ገጠር (ሠ10)</title>
    <style>
        .report-container {
            width: 100%;
            margin: 0 auto;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
        }
        .report-header {
            text-align: center;
            margin-bottom: 10px;
        }
        .report-header h2 {
            margin: 2px 0;
            font-size: 16px;
        }
        .report-header h3 {
            margin: 2px 0;
            font-size: 13px;
            color: #555;
        }
        .table-responsive {
            overflow-x: auto;
            margin-bottom: 15px;
        }
        .report-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            white-space: nowrap;
        }
        .report-table th, .report-table td {
            border: 1px solid #444;
            padding: 4px 5px;
            text-align: center;
        }
        .report-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .text-left {
            text-align: left !important;
            padding-left: 5px !important;
        }
        .bg-group {
            background-color: #f9f9f9;
            font-weight: bold;
        }
        .bg-total {
            background-color: #e9ecef;
            font-weight: bold;
        }
        .btn-success {
            background-color: #28a745;
            color: white;
            padding: 6px 12px;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            font-family: sans-serif;
            font-size: 13px;
            border: none;
            cursor: pointer;
        }
        
        @media print {
            @page {
                size: A4 landscape;
                margin: 5mm 8mm;
            }
            html, body {
                width: 100%;
                height: 100%;
                margin: 0;
                padding: 0;
                background-color: #fff;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .no-print { 
                display: none !important; 
            }
            .table-responsive {
                overflow: visible !important;
                margin-bottom: 0;
            }
            .report-header {
                margin-bottom: 8px;
            }
            .report-table { 
                width: 100% !important;
                font-size: 9px;
                table-layout: fixed;
            }
            .report-table th, .report-table td { 
                padding: 2px 3px !important;
                border: 1px solid #000 !important;
            }
        }
    </style>
</head>
<body>

<div class="report-container">
    
    <!-- ኤክስፖርት ማድረጊያ በተን -->
    <?php if (!isset($_GET['export'])): ?>
    <div class="no-print" style="margin-bottom: 15px; text-align: right;">
        <a href="?export=excel&branch_id=<?= urlencode($myBranchId ?? ''); ?>&start_date=<?= urlencode($startdate ?? ''); ?>&end_date=<?= urlencode($enddate ?? ''); ?>" 
           class="btn btn-success">
            Export to Excel (.xlsx)
        </a>
    </div>
    <?php endif; ?>

    <!-- የሪፖርቱ ራስጌ (Header) -->
    <div class="report-header">
        <h2>የስራ እድል ፈጠራ አካታችነት ሪፖርት በከተማና ገጠር (ሠ10)</h2>
        <h3>ቅርንጫፍ/ቢሮ፦ <?= htmlspecialchars($branchData['branch_name'] ?? 'ያልታወቀ'); ?></h3>
        <h3>የሪፖርት ዘመን፦ ከ <?= htmlspecialchars($startdate ?? ''); ?> እስከ <?= htmlspecialchars($enddate ?? ''); ?></h3>
    </div>

    <div class="table-responsive">
        <table class="report-table">
            <thead>
                <tr>
                    <th rowspan="2" style="width: 12%;">አመላካች</th>
                    <th colspan="2" style="width: 18%;">መለኪያ</th>
                    <th rowspan="2" style="width: 16%;">የስራ ፈላጊዎች ምዝገባ</th>
                    <th rowspan="2" style="width: 16%;">ግንዛቤ ማስጨበጫ</th>
                    <th colspan="3" style="width: 38%;">ስራ እድል ፈጠራ</th>
                </tr>
                <tr>
                    <th>አካባቢ</th>
                    <th>ጾታ</th>
                    <th>ቋሚ</th>
                    <th>ጊዜያዊ</th>
                    <th>ድምር</th>
                </tr>
            </thead>
            <tbody>

                <!-- ================= 1. የሴቶች አመላካች ================= -->
                <?php
                // የሞዴል ቫልዩዎች
                $r_f_reg = formatNum($r['rural_f_reg'] ?? 0);
                $r_f_aw  = formatNum($r['rural_f_awareness'] ?? 0);
                $r_f_p   = formatNum($r['rural_f_perm'] ?? 0);
                $r_f_t   = formatNum($r['rural_f_temp'] ?? 0);
                $r_f_tot = $r_f_p + $r_f_t;

                $u_f_reg = formatNum($r['urban_f_reg'] ?? 0);
                $u_f_aw  = formatNum($r['urban_f_awareness'] ?? 0);
                $u_f_p   = formatNum($r['urban_f_perm'] ?? 0);
                $u_f_t   = formatNum($r['urban_f_temp'] ?? 0);
                $u_f_tot = $u_f_p + $u_f_t;
                ?>
                <tr>
                    <td rowspan="3" class="bg-group" style="vertical-align: middle;">የሴቶች</td>
                    <td style="vertical-align: middle;">ገጠር</td>
                    <td>ሴት</td>
                    <td><?= $r_f_reg ?></td><td><?= $r_f_aw ?></td><td><?= $r_f_p ?></td><td><?= $r_f_t ?></td><td><?= $r_f_tot ?></td>
                </tr>
                <tr>
                    <td style="vertical-align: middle;">ከተማ</td>
                    <td>ሴት</td>
                    <td><?= $u_f_reg ?></td><td><?= $u_f_aw ?></td><td><?= $u_f_p ?></td><td><?= $u_f_t ?></td><td><?= $u_f_tot ?></td>
                </tr>
                <tr class="bg-total">
                    <td style="vertical-align: middle;">ከተማ እና ገጠር ድምር</td>
                    <td>ሴት</td>
                    <td><?= $r_f_reg + $u_f_reg ?></td>
                    <td><?= $r_f_aw + $u_f_aw ?></td>
                    <td><?= $r_f_p + $u_f_p ?></td>
                    <td><?= $r_f_t + $u_f_t ?></td>
                    <td><?= $r_f_tot + $u_f_tot ?></td>
                </tr>

                <!-- ================= 2. ሌሎች 7 ተከታታይ አመላካቾች ================= -->
                <?php
                $fullIndicators = [
                    'ወጣቶች' => 'youth',
                    'አካል ጉዳተኞች' => 'disabled',
                    'ከስደት ተመላሽ ዜጎች' => 'returnee',
                    'የሀገር ውስጥ ተፈናቃይ' => 'idp',
                    'መኖሪያቸው ጎዳና የሆኑ ዜጎች' => 'homeless',
                    'ከዩኒቨርሲቲ ተመራቂዎች' => 'uni',
                    'ከቴክኒክ እና ሙያ ተመራቂዎች' => 'tvet'
                ];

                foreach ($fullIndicators as $label => $key):
                    // ገጠር - ወንድ
                    $rm_reg = formatNum($r["rural_m_{$key}_reg"] ?? 0);
                    $rm_aw  = formatNum($r["rural_m_{$key}_awareness"] ?? 0);
                    $rm_p   = formatNum($r["rural_m_{$key}_perm"] ?? 0);
                    $rm_t   = formatNum($r["rural_m_{$key}_temp"] ?? 0);
                    $rm_tot = $rm_p + $rm_t;

                    // ገጠር - ሴት
                    $rf_reg = formatNum($r["rural_f_{$key}_reg"] ?? 0);
                    $rf_aw  = formatNum($r["rural_f_{$key}_awareness"] ?? 0);
                    $rf_p   = formatNum($r["rural_f_{$key}_perm"] ?? 0);
                    $rf_t   = formatNum($r["rural_f_{$key}_temp"] ?? 0);
                    $rf_tot = $rf_p + $rf_t;

                    // ከተማ - ወንድ
                    $um_reg = formatNum($r["urban_m_{$key}_reg"] ?? 0);
                    $um_aw  = formatNum($r["urban_m_{$key}_awareness"] ?? 0);
                    $um_p   = formatNum($r["urban_m_{$key}_perm"] ?? 0);
                    $um_t   = formatNum($r["urban_m_{$key}_temp"] ?? 0);
                    $um_tot = $um_p + $um_t;

                    // ከተማ - ሴት
                    $uf_reg = formatNum($r["urban_f_{$key}_reg"] ?? 0);
                    $uf_aw  = formatNum($r["urban_f_{$key}_awareness"] ?? 0);
                    $uf_p   = formatNum($r["urban_f_{$key}_perm"] ?? 0);
                    $uf_t   = formatNum($r["urban_f_{$key}_temp"] ?? 0);
                    $uf_tot = $uf_p + $uf_t;

                    // ድምር ስሌቶች
                    $r_total_reg = $rm_reg + $rf_reg;
                    $r_total_aw  = $rm_aw + $rf_aw;
                    $r_total_p   = $rm_p + $rf_p;
                    $r_total_t   = $rm_t + $rf_t;
                    $r_total_tot = $rm_tot + $rf_tot;

                    $u_total_reg = $um_reg + $uf_reg;
                    $u_total_aw  = $um_aw + $uf_aw;
                    $u_total_p   = $um_p + $uf_p;
                    $u_total_t   = $um_t + $uf_t;
                    $u_total_tot = $um_tot + $uf_tot;

                    $grand_m_reg = $rm_reg + $um_reg;
                    $grand_m_aw  = $rm_aw + $um_aw;
                    $grand_m_p   = $rm_p + $um_p;
                    $grand_m_t   = $rm_t + $um_t;
                    $grand_m_tot = $rm_tot + $um_tot;

                    $grand_f_reg = $rf_reg + $uf_reg;
                    $grand_f_aw  = $rf_aw + $uf_aw;
                    $grand_f_p   = $rf_p + $uf_p;
                    $grand_f_t   = $rf_t + $uf_t;
                    $grand_f_tot = $rf_tot + $uf_tot;
                ?>
                    <!-- ገጠር ክፍል -->
                    <tr>
                        <td rowspan="9" class="bg-group" style="vertical-align: middle;"><?= $label ?></td>
                        <td rowspan="3" style="vertical-align: middle;">ገጠር</td>
                        <td>ወንድ</td>
                        <td><?= $rm_reg ?></td><td><?= $rm_aw ?></td><td><?= $rm_p ?></td><td><?= $rm_t ?></td><td><?= $rm_tot ?></td>
                    </tr>
                    <tr>
                        <td>ሴት</td>
                        <td><?= $rf_reg ?></td><td><?= $rf_aw ?></td><td><?= $rf_p ?></td><td><?= $rf_t ?></td><td><?= $rf_tot ?></td>
                    </tr>
                    <tr class="bg-total">
                        <td>ድምር</td>
                        <td><?= $r_total_reg ?></td><td><?= $r_total_aw ?></td><td><?= $r_total_p ?></td><td><?= $r_total_t ?></td><td><?= $r_total_tot ?></td>
                    </tr>

                    <!-- <b>ከተማ ክፍል</b> -->
                    <tr>
                        <td rowspan="3" style="vertical-align: middle;">ከተማ</td>
                        <td>ወንድ</td>
                        <td><?= $um_reg ?></td><td><?= $um_aw ?></td><td><?= $um_p ?></td><td><?= $um_t ?></td><td><?= $um_tot ?></td>
                    </tr>
                    <tr>
                        <td>ሴት</td>
                        <td><?= $uf_reg ?></td><td><?= $uf_aw ?></td><td><?= $uf_p ?></td><td><?= $uf_t ?></td><td><?= $uf_tot ?></td>
                    </tr>
                    <tr class="bg-total">
                        <td>ድምር</td>
                        <td><?= $u_total_reg ?></td><td><?= $u_total_aw ?></td><td><?= $u_total_p ?></td><td><?= $u_total_t ?></td><td><?= $u_total_tot ?></td>
                    </tr>

                    <!-- <b>ከተማ እና ገጠር ድምር ክፍል</b> -->
                    <tr class="bg-total">
                        <td rowspan="3" style="vertical-align: middle;">ከተማ እና ገጠር ድምር</td>
                        <td>ወንድ</td>
                        <td><?= $grand_m_reg ?></td><td><?= $grand_m_aw ?></td><td><?= $grand_m_p ?></td><td><?= $grand_m_t ?></td><td><?= $grand_m_tot ?></td>
                    </tr>
                    <tr class="bg-total">
                        <td>ሴት</td>
                        <td><?= $grand_f_reg ?></td><td><?= $grand_f_aw ?></td><td><?= $grand_f_p ?></td><td><?= $grand_f_t ?></td><td><?= $grand_f_tot ?></td>
                    </tr>
                    <tr style="background-color: #d1ecf1; font-weight: bold;">
                        <td>አጠቃላይ ድምር</td>
                        <td><?= $r_total_reg + $u_total_reg ?></td>
                        <td><?= $r_total_aw + $u_total_aw ?></td>
                        <td><?= $r_total_p + $u_total_p ?></td>
                        <td><?= $r_total_t + $u_total_t ?></td>
                        <td><?= $r_total_tot + $u_total_tot ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>