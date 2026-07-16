<?php
use App\Helpers\EthiopianDateHelper; 

$report = $report1 ?? [
    'urban_m_parents' => 0, 'urban_f_parents' => 0, 'rural_m_parents' => 0, 'rural_f_parents' => 0,
    'urban_m_others' => 0, 'urban_f_others' => 0, 'rural_m_others' => 0, 'rural_f_others' => 0,
    'urban_m_advice' => 0, 'urban_f_advice' => 0, 'rural_m_advice' => 0, 'rural_f_advice' => 0,
    'urban_m_age15_29' => 0, 'urban_f_age15_29' => 0, 'rural_m_age15_29' => 0, 'rural_f_age15_29' => 0,
    'urban_m_age30_64' => 0, 'urban_f_age30_64' => 0, 'rural_m_age30_64' => 0, 'rural_f_age30_64' => 0,
    'urban_m_uni' => 0, 'urban_f_uni' => 0, 'rural_m_uni' => 0, 'rural_f_uni' => 0,
    'urban_m_tvt' => 0, 'urban_f_tvt' => 0, 'rural_m_tvt' => 0, 'rural_f_tvt' => 0,
    'urban_m_phy' => 0, 'urban_f_phy' => 0, 'rural_m_phy' => 0, 'rural_f_phy' => 0,
    'urban_m_immg' => 0, 'urban_f_immg' => 0, 'rural_m_immg' => 0, 'rural_f_immg' => 0,
    'urban_m_teff' => 0, 'urban_f_teff' => 0, 'rural_m_teff' => 0, 'rural_f_teff' => 0,
    'urban_m_noh' => 0, 'urban_f_noh' => 0, 'rural_m_noh' => 0, 'rural_f_noh' => 0,
    'urban_m_ajs' => 0, 'urban_f_ajs' => 0, 'rural_m_ajs' => 0, 'rural_f_ajs' => 0,
    'urban_m_ajs15_29' => 0, 'urban_f_ajs15_29' => 0, 'rural_m_ajs15_29' => 0, 'rural_f_ajs15_29' => 0,
    'urban_m_ajsuni' => 0, 'urban_f_ajsuni' => 0, 'rural_m_ajsuni' => 0, 'rural_f_ajsuni' => 0,
    'urban_m_ajstvt' => 0, 'urban_f_ajstvt' => 0, 'rural_m_ajstvt' => 0, 'rural_f_ajstvt' => 0,
    'urban_m_ajsdis' => 0, 'urban_f_ajsdis' => 0, 'rural_m_ajsdis' => 0, 'rural_f_ajsdis' => 0,
    'urban_m_ajsimmg' => 0, 'urban_f_ajsimmg' => 0, 'rural_m_ajsimmg' => 0, 'rural_f_ajsimmg' => 0,
    'urban_m_ajsteff' => 0, 'urban_f_ajsteff' => 0, 'rural_m_ajsteff' => 0, 'rural_f_ajsteff' => 0,
    'urban_m_ajsnoh' => 0, 'urban_f_ajsnoh' => 0, 'rural_m_ajsnoh' => 0, 'rural_f_ajsnoh' => 0,
];

$selectedBranchName = $branchData['name'] ?? $_SESSION['user']['branch_name'];

// Calculations
$totalUrbanparents = $report['urban_m_parents'] + $report['urban_f_parents'];
$totalRuralparents = $report['rural_m_parents'] + $report['rural_f_parents'];
$totalMaleparents  = $report['urban_m_parents'] + $report['rural_m_parents'];
$totalFemaleparents = $report['urban_f_parents'] + $report['rural_f_parents'];
$grandTotalparents = $totalUrbanparents + $totalRuralparents;

$totalUrbanothers = $report['urban_m_others'] + $report['urban_f_others'];
$totalRuralothers = $report['rural_m_others'] + $report['rural_f_others'];
$totalMaleothers  = $report['urban_m_others'] + $report['rural_m_others'];
$totalFemaleothers = $report['urban_f_others'] + $report['rural_f_others'];
$grandTotalothers = $totalUrbanothers + $totalRuralothers;

$totalUrbanadvice = $report['urban_m_advice'] + $report['urban_f_advice'];
$totalRuraladvice = $report['rural_m_advice'] + $report['rural_f_advice'];
$totalMaleadvice  = $report['urban_m_advice'] + $report['rural_m_advice'];
$totalFemaleadvice = $report['urban_f_advice'] + $report['rural_f_advice'];
$grandTotaladvice = $totalUrbanadvice + $totalRuraladvice;

$totalUrbanage15_29 = $report['urban_m_age15_29'] + $report['urban_f_age15_29'];
$totalRuralage15_29 = $report['rural_m_age15_29'] + $report['rural_f_age15_29'];
$totalMaleage15_29  = $report['urban_m_age15_29'] + $report['rural_m_age15_29'];
$totalFemaleage15_29 = $report['urban_f_age15_29'] + $report['rural_f_age15_29'];
$grandTotalage15_29 = $totalUrbanage15_29 + $totalRuralage15_29;

$totalUrbanage30_64 = $report['urban_m_age30_64'] + $report['urban_f_age30_64'];
$totalRuralage30_64 = $report['rural_m_age30_64'] + $report['rural_f_age30_64'];
$totalMaleage30_64  = $report['urban_m_age30_64'] + $report['rural_m_age30_64'];
$totalFemaleage30_64 = $report['urban_f_age30_64'] + $report['rural_f_age30_64'];
$grandTotalage30_64 = $totalUrbanage30_64 + $totalRuralage30_64;

$totalUrbanuni = $report['urban_m_uni'] + $report['urban_f_uni'];
$totalRuraluni = $report['rural_m_uni'] + $report['rural_f_uni'];
$totalMaleuni  = $report['urban_m_uni'] + $report['rural_m_uni'];
$totalFemaleuni = $report['urban_f_uni'] + $report['rural_f_uni'];
$grandTotaluni = $totalUrbanuni + $totalRuraluni;

$totalUrbantvt = $report['urban_m_tvt'] + $report['urban_f_tvt'];
$totalRuraltvt = $report['rural_m_tvt'] + $report['rural_f_tvt'];
$totalMaletvt  = $report['urban_m_tvt'] + $report['rural_m_tvt'];
$totalFemaletvt = $report['urban_f_tvt'] + $report['rural_f_tvt'];
$grandTotaltvt = $totalUrbantvt + $totalRuraltvt;

$totalUrbanphy = $report['urban_m_phy'] + $report['urban_f_phy'];
$totalRuralphy = $report['rural_m_phy'] + $report['rural_f_phy'];
$totalMalephy  = $report['urban_m_phy'] + $report['rural_m_phy'];
$totalFemalephy = $report['urban_f_phy'] + $report['rural_f_phy'];
$grandTotalphy = $totalUrbanphy + $totalRuralphy;

$totalUrbanimmg = $report['urban_m_immg'] + $report['urban_f_immg'];
$totalRuralimmg = $report['rural_m_immg'] + $report['rural_f_immg'];
$totalMaleimmg  = $report['urban_m_immg'] + $report['rural_m_immg'];
$totalFemaleimmg = $report['urban_f_immg'] + $report['rural_f_immg'];
$grandTotalimmg = $totalUrbanimmg + $totalRuralimmg;

$totalUrbanteff = $report['urban_m_teff'] + $report['urban_f_teff'];
$totalRuralteff = $report['rural_m_teff'] + $report['rural_f_teff'];
$totalMaleteff  = $report['urban_m_teff'] + $report['rural_m_teff'];
$totalFemaleteff = $report['urban_f_teff'] + $report['rural_f_teff'];
$grandTotalteff = $totalUrbanteff + $totalRuralteff;

$totalUrbannoh = $report['urban_m_noh'] + $report['urban_f_noh'];
$totalRuralnoh = $report['rural_m_noh'] + $report['rural_f_noh'];
$totalMalenoh  = $report['urban_m_noh'] + $report['rural_m_noh'];
$totalFemalenoh = $report['urban_f_noh'] + $report['rural_f_noh'];
$grandTotalnoh = $totalUrbannoh + $totalRuralnoh;

$totalUrbanajs = $report['urban_m_ajs'] + $report['urban_f_ajs'];
$totalRuralajs = $report['rural_m_ajs'] + $report['rural_f_ajs'];
$totalMaleajs  = $report['urban_m_ajs'] + $report['rural_m_ajs'];
$totalFemaleajs = $report['urban_f_ajs'] + $report['rural_f_ajs'];
$grandTotalajs = $totalUrbanajs + $totalRuralajs;

$totalUrbanajs15_29 = $report['urban_m_ajs15_29'] + $report['urban_f_ajs15_29'];
$totalRuralajs15_29 = $report['rural_m_ajs15_29'] + $report['rural_f_ajs15_29'];
$totalMaleajs15_29  = $report['urban_m_ajs15_29'] + $report['rural_m_ajs15_29'];
$totalFemaleajs15_29 = $report['urban_f_ajs15_29'] + $report['rural_f_ajs15_29'];
$grandTotalajs15_29 = $totalUrbanajs15_29 + $totalRuralajs15_29;

$totalUrbanajsuni = $report['urban_m_ajsuni'] + $report['urban_f_ajsuni'];
$totalRuralajsuni = $report['rural_m_ajsuni'] + $report['rural_f_ajsuni'];
$totalMaleajsuni  = $report['urban_m_ajsuni'] + $report['rural_m_ajsuni'];
$totalFemaleajsuni = $report['urban_f_ajsuni'] + $report['rural_f_ajsuni'];
$grandTotalajsuni = $totalUrbanajsuni + $totalRuralajsuni;

$totalUrbanajstvt = $report['urban_m_ajstvt'] + $report['urban_f_ajstvt'];
$totalRuralajstvt = $report['rural_m_ajstvt'] + $report['rural_f_ajstvt'];
$totalMaleajstvt  = $report['urban_m_ajstvt'] + $report['rural_m_ajstvt'];
$totalFemaleajstvt = $report['urban_f_ajstvt'] + $report['rural_f_ajstvt'];
$grandTotalajstvt = $totalUrbanajstvt + $totalRuralajstvt;

$totalUrbanajsdis = $report['urban_m_ajsdis'] + $report['urban_f_ajsdis'];
$totalRuralajsdis = $report['rural_m_ajsdis'] + $report['rural_f_ajsdis'];
$totalMaleajsdis  = $report['urban_m_ajsdis'] + $report['rural_m_ajsdis'];
$totalFemaleajsdis = $report['urban_f_ajsdis'] + $report['rural_f_ajsdis'];
$grandTotalajsdis = $totalUrbanajsdis + $totalRuralajsdis;

$totalUrbanajsimmg = $report['urban_m_ajsimmg'] + $report['urban_f_ajsimmg'];
$totalRuralajsimmg = $report['rural_m_ajsimmg'] + $report['rural_f_ajsimmg'];
$totalMaleajsimmg  = $report['urban_m_ajsimmg'] + $report['rural_m_ajsimmg'];
$totalFemaleajsimmg = $report['urban_f_ajsimmg'] + $report['rural_f_ajsimmg'];
$grandTotalajsimmg = $totalUrbanajsimmg + $totalRuralajsimmg;

$totalUrbanajsteff = $report['urban_m_ajsteff'] + $report['urban_f_ajsteff'];
$totalRuralajsteff = $report['rural_m_ajsteff'] + $report['rural_f_ajsteff'];
$totalMaleajsteff  = $report['urban_m_ajsteff'] + $report['rural_m_ajsteff'];
$totalFemaleajsteff = $report['urban_f_ajsteff'] + $report['rural_f_ajsteff'];
$grandTotalajsteff = $totalUrbanajsteff + $totalRuralajsteff;

$totalUrbanajsnoh = $report['urban_m_ajsnoh'] + $report['urban_f_ajsnoh'];
$totalRuralajsnoh = $report['rural_m_ajsnoh'] + $report['rural_f_ajsnoh'];
$totalMaleajsnoh  = $report['urban_m_ajsnoh'] + $report['rural_m_ajsnoh'];
$totalFemaleajsnoh = $report['urban_f_ajsnoh'] + $report['rural_f_ajsnoh'];
$grandTotalajsnoh = $totalUrbanajsnoh + $totalRuralajsnoh;

$startdate = !empty($startdate) ? $startdate : date('Y-m-d');
$startdateParts = explode('-', $startdate);
$ethstartDate = EthiopianDateHelper::toEthCalendar($startdateParts[2], $startdateParts[1], $startdateParts[0]);

$enddate = !empty($enddate) ? $enddate : date('Y-m-d');
$enddateParts = explode('-', $enddate);
$ethendDate = EthiopianDateHelper::toEthCalendar($enddateParts[2], $enddateParts[1], $enddateParts[0]);

// 🟢 EXPORT LOGIC: Intercepts and downloads as .xlsx if requested
if (isset($_GET['export']) && $_GET['export'] == 'excel') {
    header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
    header("Content-Disposition: attachment; filename=Inclusive_Report_W10.xls");
    header("Pragma: no-cache");
    header("Expires: 0");
    // Ensure Amharic UTF-8 characters display correctly in Excel
    echo "\xEF\xBB\xBF"; 
}
?>

<style>
    body { padding: 30px; background-color: #fff; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #000; }
    .table { width: 100%; border-collapse: collapse; }
    .table th { background-color: #f8f9fa; text-align: center; vertical-align: middle !important; font-size: 11px; font-weight: bold; border: 1px solid #000 !important; padding: 4px; }
    .table td { vertical-align: middle !important; font-size: 11px; border: 1px solid #000 !important; padding: 4px; }
    .report-header { text-align: center; margin-bottom: 20px; }
    .text-left { text-align: left !important; padding-left: 8px !important; }
    
    @media print {
        body { padding: 0; }
        .no-print { display: none; }
    }
</style>

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
<center class="mb-4">
    <h4 class="font-weight-bold">የስራ እድል ፈጠራ አካታችነት ሪፖርት በከተማና ገጠር (ሠ10)</h4>
    <h5 class="text-primary mt-2">የመዋቅር ደረጃ፦ <strong><?= htmlspecialchars($selectedBranchName); ?></strong></h5>
    <h6>
        የሪፖርት ቀን፦
        <?php if (isset($ethstartDate) && is_array($ethstartDate) && isset($ethstartDate['month'], $ethstartDate['day'], $ethstartDate['year'])): ?>
            <?= EthiopianDateHelper::getMonthName($ethstartDate['month']) ?>
            <?= $ethstartDate['day'] ?>
            <?= $ethstartDate['year'] ?>
        <?php else: ?>
            <?= htmlspecialchars($startdate ?? '') ?>
        <?php endif; ?>

        <?php if (isset($startdate, $enddate) && $startdate != $enddate): ?>
            -
            <?php if (isset($ethendDate) && is_array($ethendDate) && isset($ethendDate['month'], $ethendDate['day'], $ethendDate['year'])): ?>
                <?= EthiopianDateHelper::getMonthName($ethendDate['month']) ?>
                <?= $ethendDate['day'] ?>
                <?= $ethendDate['year'] ?>
            <?php else: ?>
                <?= htmlspecialchars($enddate ?? '') ?>
            <?php endif; ?>
        <?php endif; ?>
    </h6>
</center>
    
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
                <tr>
                    <td rowspan="3">የሴቶች</td>
                    <td>ገጠር</td>
                    <td>ሴት</td>
                    <td><?= $report['rural_f_advice']; ?></td>
                    <td><?= $report['rural_f_ajs']; ?></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>ከተማ</td>
                    <td>ሴት</td>
                    <td><?= $report['urban_f_advice']; ?></td>
                    <td><?= $report['urban_f_ajs']; ?></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>ከተማ እና ገጠር ድምር</td>
                    <td>ሴት</td>
                    <td><?= $totalFemaleadvice; ?></td>
                    <td><?= $totalFemaleajs; ?></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <!-- ================= 2. የወጣቶች አመላካች ================= -->
                                        <tr>
                        <td rowspan="9">ወጣቶች</td>
                        <td rowspan="3">ገጠር</td>
                        <td>ወንድ</td>
                        <td><?= $report['rural_m_age15_29']; ?></td>
                        <td><?= $report['rural_m_ajs15_29']; ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        </tr>

                        <!-- Row 2 -->
                        <tr>
                        <td>ሴት</td>
                        <td><?= $report['rural_f_age15_29']; ?></td>
                        <td><?= $report['rural_f_ajs15_29']; ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        </tr>

                        <!-- Row 3 -->
                        <tr>
                        <td>ድምር</td>
                        <td><?= $totalRuralage15_29; ?></td>
                        <td><?= $totalRuralajs15_29; ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        </tr>

                        <!-- Row 4 -->
                        <tr>
                        <td rowspan="3">ከተማ</td>
                        <td>ወንድ</td>
                        <td><?= $report['urban_m_age15_29']; ?></td>
                        <td><?= $report['urban_m_ajs15_29']; ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        </tr>

                        <!-- Row 5 -->
                        <tr>
                        <td>ሴት</td>
                        <td><?= $report['urban_f_age15_29']; ?></td>
                        <td><?= $report['urban_f_ajs15_29']; ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        </tr>

                        <!-- Row 6 -->
                        <tr>
                        <td>ድምር</td>
                        <td><?= $totalUrbanage15_29; ?></td>
                        <td><?= $totalUrbanajs15_29; ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        </tr>

                        <!-- Row 7 -->
                        <tr>
                        <td rowspan="3">ከተማ እና ገጠር</td>
                        <td>ወንድ</td>
                        <td><?= $totalMaleage15_29; ?></td>
                        <td><?= $totalMaleajs15_29; ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        </tr>

                        <!-- Row 8 -->
                        <tr>
                        <td>ሴት</td>
                        <td><?= $totalFemaleage15_29; ?></td>
                        <td><?= $totalFemaleajs15_29; ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        </tr>

                        <!-- Row 9 -->
                        <tr>
                        <td>ድምር</td>
                        <td><?= $grandTotalage15_29; ?></td>
                        <td><?= $grandTotalajs15_29; ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        </tr>
                <!-- ================= 3. አካል ጉዳተኞች አመላካች ================= -->
                                        <tr>
                        <td rowspan="9">አካል ጉዳተኞች</td>
                        <td rowspan="3">ገጠር</td>
                        <td>ወንድ</td>
                        <td><?= $report['rural_m_phy']; ?></td>
                        <td><?= $report['rural_m_ajsdis']; ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        </tr>

                        <!-- Row 2 -->
                        <tr>
                        <td>ሴት</td>
                        <td><?= $report['rural_f_phy']; ?></td>
                        <td><?= $report['rural_f_ajsdis']; ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        </tr>

                        <!-- Row 3 -->
                        <tr>
                        <td>ድምር</td>
                        <td><?= $totalRuralphy; ?></td>
                        <td><?= $totalRuralajsdis; ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        </tr>

                        <!-- Row 4 -->
                        <tr>
                        <td rowspan="3">ከተማ</td>
                        <td>ወንድ</td>
                        <td><?= $report['urban_m_phy']; ?></td>
                        <td><?= $report['urban_m_ajsdis']; ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        </tr>

                        <!-- Row 5 -->
                        <tr>
                        <td>ሴት</td>
                        <td><?= $report['urban_f_phy']; ?></td>
                        <td><?= $report['urban_f_ajsdis']; ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        </tr>

                        <!-- Row 6 -->
                        <tr>
                        <td>ድምር</td>
                        <td><?= $totalUrbanphy; ?></td>
                        <td><?= $totalUrbanajsdis; ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        </tr>

                        <!-- Row 7 -->
                        <tr>
                        <td rowspan="3">ከተማ እና ገጠር</td>
                        <td>ወንድ</td>
                        <td><?= $totalMalephy; ?></td>
                        <td><?= $totalMaleajsdis; ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        </tr>

                        <!-- Row 8 -->
                        <tr>
                        <td>ሴት</td>
                        <td><?= $totalFemalephy; ?></td>
                        <td><?= $totalFemaleajsdis; ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        </tr>

                        <!-- Row 9 -->
                        <tr>
                        <td>ድምር</td>
                        <td><?= $grandTotalphy; ?></td>
                        <td><?= $grandTotalajsdis; ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        </tr>
        <!-- ================= 4. ከስደት ተመላሽ ዜጎች አመላካች ================= -->
                                        <tr>
                        <td rowspan="9">ከስደት ተመላሽ ዜጎች</td>
                        <td rowspan="3">ገጠር</td>
                        <td>ወንድ</td>
                        <td><?= $report['rural_m_immg']; ?></td>
                        <td><?= $report['rural_m_ajsimmg']; ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        </tr>

                        <!-- Row 2 -->
                        <tr>
                        <td>ሴት</td>
                        <td><?= $report['rural_f_immg']; ?></td>
                        <td><?= $report['rural_f_ajsimmg']; ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        </tr>

                        <!-- Row 3 -->
                        <tr>
                        <td>ድምር</td>
                        <td><?= $totalRuralimmg; ?></td>
                        <td><?= $totalRuralajsimmg; ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        </tr>

                        <!-- Row 4 -->
                        <tr>
                        <td rowspan="3">ከተማ</td>
                        <td>ወንድ</td>
                        <td><?= $report['urban_m_immg']; ?></td>
                        <td><?= $report['urban_m_ajsimmg']; ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        </tr>

                        <!-- Row 5 -->
                        <tr>
                        <td>ሴት</td>
                        <td><?= $report['urban_f_immg']; ?></td>
                        <td><?= $report['urban_f_ajsimmg']; ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        </tr>

                        <!-- Row 6 -->
                        <tr>
                        <td>ድምር</td>
                        <td><?= $totalUrbanimmg; ?></td>
                        <td><?= $totalUrbanajsimmg; ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        </tr>

                        <!-- Row 7 -->
                        <tr>
                        <td rowspan="3">ከተማ እና ገጠር</td>
                        <td>ወንድ</td>
                        <td><?= $totalMaleimmg; ?></td>
                        <td><?= $totalMaleajsimmg; ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        </tr>

                        <!-- Row 8 -->
                        <tr>
                        <td>ሴት</td>
                        <td><?= $totalFemaleimmg; ?></td>
                        <td><?= $totalFemaleajsimmg; ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        </tr>

                        <!-- Row 9 -->
                        <tr>
                        <td>ድምር</td>
                        <td><?= $grandTotalimmg; ?></td>
                        <td><?= $grandTotalajsimmg; ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        </tr>
        <!-- ================= 5. የሀገር ውስጥ ተፈናቃይ አመላካች ================= -->
                                        <tr>
                        <td rowspan="9">የሀገር ውስጥ ተፈናቃይ</td>
                        <td rowspan="3">ገጠር</td>
                        <td>ወንድ</td>
                        <td><?= $report['rural_m_teff']; ?></td>
                        <td><?= $report['rural_m_ajsteff']; ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        </tr>

                        <!-- Row 2 -->
                        <tr>
                        <td>ሴት</td>
                        <td><?= $report['rural_f_teff']; ?></td>
                        <td><?= $report['rural_f_ajsteff']; ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        </tr>

                        <!-- Row 3 -->
                        <tr>
                        <td>ድምር</td>
                        <td><?= $totalRuralteff; ?></td>
                        <td><?= $totalRuralajsteff; ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        </tr>

                        <!-- Row 4 -->
                        <tr>
                        <td rowspan="3">ከተማ</td>
                        <td>ወንድ</td>
                        <td><?= $report['urban_m_teff']; ?></td>
                        <td><?= $report['urban_m_ajsteff']; ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        </tr>

                        <!-- Row 5 -->
                        <tr>
                        <td>ሴት</td>
                        <td><?= $report['urban_f_teff']; ?></td>
                        <td><?= $report['urban_f_ajsteff']; ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        </tr>

                        <!-- Row 6 -->
                        <tr>
                        <td>ድምር</td>
                        <td><?= $totalUrbanteff; ?></td>
                        <td><?= $totalUrbanajsteff; ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        </tr>

                        <!-- Row 7 -->
                        <tr>
                        <td rowspan="3">ከተማ እና ገጠር</td>
                        <td>ወንድ</td>
                        <td><?= $totalMaleteff; ?></td>
                        <td><?= $totalMaleajsteff; ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        </tr>

                        <!-- Row 8 -->
                        <tr>
                        <td>ሴት</td>
                        <td><?= $totalFemaleteff; ?></td>
                        <td><?= $totalFemaleajsteff; ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        </tr>

                        <!-- Row 9 -->
                        <tr>
                        <td>ድምር</td>
                        <td><?= $grandTotalteff; ?></td>
                        <td><?= $grandTotalajsteff; ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        </tr>
        <!-- ================= 6. መኖሪያቸው ጎዳና የሆኑ ዜጎች አመላካች ================= -->
                        <tr>
                        <td rowspan="9">መኖሪያቸው ጎዳና የሆኑ ዜጎች</td>
                        <td rowspan="3">ገጠር</td>
                        <td>ወንድ</td>
                        <td><?= $report['rural_m_noh']; ?></td>
                        <td><?= $report['rural_m_ajsnoh']; ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        </tr>

                        <!-- Row 2 -->
                        <tr>
                        <td>ሴት</td>
                        <td><?= $report['rural_f_noh']; ?></td>
                        <td><?= $report['rural_f_ajsnoh']; ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        </tr>

                        <!-- Row 3 -->
                        <tr>
                        <td>ድምር</td>
                        <td><?= $totalRuralnoh; ?></td>
                        <td><?= $totalRuralajsnoh; ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        </tr>

                        <!-- Row 4 -->
                        <tr>
                        <td rowspan="3">ከተማ</td>
                        <td>ወንድ</td>
                        <td><?= $report['urban_m_noh']; ?></td>
                        <td><?= $report['urban_m_ajsnoh']; ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        </tr>

                        <!-- Row 5 -->
                        <tr>
                        <td>ሴት</td>
                        <td><?= $report['urban_f_noh']; ?></td>
                        <td><?= $report['urban_f_ajsnoh']; ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        </tr>

                        <!-- Row 6 -->
                        <tr>
                        <td>ድምር</td>
                        <td><?= $totalUrbannoh; ?></td>
                        <td><?= $totalUrbanajsnoh; ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        </tr>

                        <!-- Row 7 -->
                        <tr>
                        <td rowspan="3">ከተማ እና ገጠር</td>
                        <td>ወንድ</td>
                        <td><?= $totalMalenoh; ?></td>
                        <td><?= $totalMaleajsnoh; ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        </tr>

                        <!-- Row 8 -->
                        <tr>
                        <td>ሴት</td>
                        <td><?= $totalFemalenoh; ?></td>
                        <td><?= $totalFemaleajsnoh; ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        </tr>

                        <!-- Row 9 -->
                        <tr>
                        <td>ድምር</td>
                        <td><?= $grandTotalnoh; ?></td>
                        <td><?= $grandTotalajsnoh; ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        </tr>
        <!-- ================= 7. ከዩኒቨርሲቲ ተመራቂዎች አመላካች ================= -->
                        <tr>
                        <td rowspan="9">ከዩኒቨርሲቲ ተመራቂዎች</td>
                        <td rowspan="3">ገጠር</td>
                        <td>ወንድ</td>
                        <td><?= $report['rural_m_uni']; ?></td>
                        <td><?= $report['rural_m_ajsuni']; ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        </tr>

                        <!-- Row 2 -->
                        <tr>
                        <td>ሴት</td>
                        <td><?= $report['rural_f_uni']; ?></td>
                        <td><?= $report['rural_f_ajsuni']; ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        </tr>

                        <!-- Row 3 -->
                        <tr>
                        <td>ድምር</td>
                        <td><?= $totalRuraluni; ?></td>
                        <td><?= $totalRuralajsuni; ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        </tr>

                        <!-- Row 4 -->
                        <tr>
                        <td rowspan="3">ከተማ</td>
                        <td>ወንድ</td>
                        <td><?= $report['urban_m_uni']; ?></td>
                        <td><?= $report['urban_m_ajsuni']; ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        </tr>

                        <!-- Row 5 -->
                        <tr>
                        <td>ሴት</td>
                        <td><?= $report['urban_f_uni']; ?></td>
                        <td><?= $report['urban_f_ajsuni']; ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        </tr>

                        <!-- Row 6 -->
                        <tr>
                        <td>ድምር</td>
                        <td><?= $totalUrbanuni; ?></td>
                        <td><?= $totalUrbanajsuni; ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        </tr>

                        <!-- Row 7 -->
                        <tr>
                        <td rowspan="3">ከተማ እና ገጠር</td>
                        <td>ወንድ</td>
                        <td><?= $totalMaleuni; ?></td>
                        <td><?= $totalMaleajsuni; ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        </tr>

                        <!-- Row 8 -->
                        <tr>
                        <td>ሴት</td>
                        <td><?= $totalFemaleuni; ?></td>
                        <td><?= $totalFemaleajsuni; ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        </tr>

                        <!-- Row 9 -->
                        <tr>
                        <td>ድምር</td>
                        <td><?= $grandTotaluni; ?></td>
                        <td><?= $grandTotalajsuni; ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        </tr>
        <!-- ================= 8. ከቴክኒክ እና ሙያ ተመራቂዎች አመላካች ================= -->
                        <tr>
                        <td rowspan="9">ከቴክኒክ እና ሙያ ተመራቂዎች</td>
                        <td rowspan="3">ገጠር</td>
                        <td>ወንድ</td>
                        <td><?= $report['rural_m_tvt']; ?></td>
                        <td><?= $report['rural_m_ajstvt']; ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        </tr>

                        <!-- Row 2 -->
                        <tr>
                        <td>ሴት</td>
                        <td><?= $report['rural_f_tvt']; ?></td>
                        <td><?= $report['rural_f_ajstvt']; ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        </tr>

                        <!-- Row 3 -->
                        <tr>
                        <td>ድምር</td>
                        <td><?= $totalRuraltvt; ?></td>
                        <td><?= $totalRuralajstvt; ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        </tr>

                        <!-- Row 4 -->
                        <tr>
                        <td rowspan="3">ከተማ</td>
                        <td>ወንድ</td>
                        <td><?= $report['urban_m_tvt']; ?></td>
                        <td><?= $report['urban_m_ajstvt']; ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        </tr>

                        <!-- Row 5 -->
                        <tr>
                        <td>ሴት</td>
                        <td><?= $report['urban_f_tvt']; ?></td>
                        <td><?= $report['urban_f_ajstvt']; ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        </tr>

                        <!-- Row 6 -->
                        <tr>
                        <td>ድምር</td>
                        <td><?= $totalUrbantvt; ?></td>
                        <td><?= $totalUrbanajstvt; ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        </tr>

                        <!-- Row 7 -->
                        <tr>
                        <td rowspan="3">ከተማ እና ገጠር</td>
                        <td>ወንድ</td>
                        <td><?= $totalMaletvt; ?></td>
                        <td><?= $totalMaleajstvt; ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        </tr>

                        <!-- Row 8 -->
                        <tr>
                        <td>ሴት</td>
                        <td><?= $totalFemaletvt; ?></td>
                        <td><?= $totalFemaleajstvt; ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        </tr>

                        <!-- Row 9 -->
                        <tr>
                        <td>ድምር</td>
                        <td><?= $grandTotaltvt; ?></td>
                        <td><?= $grandTotalajstvt; ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        </tr>       
            </tbody>
        </table>
    </div>
</div>

</body>
</html>