<?php
use App\Helpers\EthiopianDateHelper; 

$startdate = !empty($startdate) ? $startdate : date('Y-m-d');
$startdateParts = explode('-', $startdate);
$ethstartDate = EthiopianDateHelper::toEthCalendar($startdateParts[2], $startdateParts[1], $startdateParts[0]);

$enddate = !empty($enddate) ? $enddate : date('Y-m-d');
$enddateParts = explode('-', $enddate);
$ethendDate = EthiopianDateHelper::toEthCalendar($enddateParts[2], $enddateParts[1], $enddateParts[0]);

if (isset($_GET['export']) && $_GET['export'] == 'excel') {
    header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
    header("Content-Disposition: attachment; filename=Inclusive_Report_08.xls");
    header("Pragma: no-cache");
    header("Expires: 0");
    echo "\xEF\xBB\xBF"; 
}

// 8ቱ የሥራ እድል መፍጠሪያ አማራጮች
$jobCreationReasons = [
    'አዳዲስ ኢንተርፕራይዞች በማቋቋም የተፈጠረ ሥራ',
    'ነባር ኢንተርፕራይዞችን በማስፋፋት የተቀጠሩ',
    'የግል ዘርፍ ኢንቨስትመንት/ድርጅቶች የተቀጠሩ',
    'በመንግስት ኢንተርፕራይዞች/ግዙፍ ፕሮጀክቶች የተቀጠሩ',
    'በህ/ስ/ማህበራት የተቀጠሩ',
    'መንግስታዊ ያልሆኑ ድርጅቶች ቅጥር',
    'በመንግስት መ/ቤቶች የተቀጠሩ',
    'የውጭ አገር ሥራ ስምሪት'
];

$sectorsList = ['ግብርና', 'ኢንዱስትሪ', 'አገልግሎት'];

// ዳታውን ለመፈለግ እንዲመች በ Key ማዘጋጀት [reason][sector]
$indexedData = [];
foreach ($reportData as $row) {
    $rKey = preg_replace('/\s+/', ' ', trim($row['job_reason'] ?? ''));
    $sKey = preg_replace('/\s+/', ' ', trim($row['sector_name'] ?? ''));
    $indexedData[$rKey][$sKey] = $row;
}
?>

<style>
    body { padding: 10px; background-color: #fff; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #000; }
    .table-container { width: 100%; overflow-x: auto; background: #fff; }
    .table { width: 100%; border-collapse: collapse; margin-top: 10px; text-align: center; font-size: 11px; }
    .table th { background-color: #ffffff; text-align: center; vertical-align: middle !important; font-weight: bold; border: 1px solid #94a3b8 !important; padding: 5px; color: #0f172a; }
    .table td { vertical-align: middle !important; border: 1px solid #cbd5e1 !important; padding: 4px; color: #1e293b; }
    
    .reason-total-row { background-color: #f1f5f9 !important; font-weight: bold !important; }
    .reason-total-row td { font-weight: bold !important; color: #000 !important; }
    
    .grand-total-row { background-color: #e2e8f0 !important; font-weight: bold !important; border-top: 2px solid #000 !important; border-bottom: 2px solid #000 !important; }
    .grand-total-row td { font-weight: bold !important; color: #000 !important; font-size: 12px; }
    
    .text-left { text-align: left !important; padding-left: 8px !important; }
    
    @media print {
        body { padding: 0; }
        .no-print { display: none; }
        .table-container { overflow: visible; }
    }
</style>

<div class="table-container">
    <center class="mb-3">
        <h3 class="font-weight-bold" style="margin-bottom: 5px;">የ<?= htmlspecialchars($residenceStatus ?? 'የለም'); ?> ሥራ እድል መፍጠሪያ አማራጮች በየዘርፉ ሪፖርት <?php echo ($residenceStatus == "ከተማ") ? "(ሠ8)" : "(ሠ9)"; ?></h3>
        <h5 style="margin-top: 0; color: #475569;">የመዋቅር ደረጃ፦ <strong><?= htmlspecialchars($selectedBranchName ?? 'የለም'); ?></strong></h5>
        <h6 style="color: #64748b; margin-top: 0;">
            የሪፖርት ዘመን፦ 
            <?= EthiopianDateHelper::getMonthName($ethstartDate['month']) ?> <?= $ethstartDate['day'] ?>፣ <?= $ethstartDate['year'] ?> 
            <?php if ($startdate != $enddate): ?>
                እስከ <?= EthiopianDateHelper::getMonthName($ethendDate['month']) ?> <?= $ethendDate['day'] ?>፣ <?= $ethendDate['year'] ?>
            <?php endif; ?>
        </h6>
    </center>

    <table class="table">
        <thead>
            <tr>
                <th rowspan="2" style="min-width: 220px;" class="align-middle">የሥራ እድል መፍጠሪያ አማራጮች</th>
                <th rowspan="2" style="min-width: 100px;" class="align-middle">ዋና ዋና ዘርፎች</th>
                <th colspan="3">ክንውን (ቋሚ)</th>
                <th colspan="3">ጊዜያዊ</th>
                <th colspan="3">ቋሚና ጊዜያዊ</th>
            </tr>
            <tr>
                <th>ወ</th><th>ሴ</th><th>ድ</th>
                <th>ወ</th><th>ሴ</th><th>ድ</th>
                <th>ወ</th><th>ሴ</th><th>ድ</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            // ለአጠቃላይ ጠቅላላ ድምር (Grand Totals)
            $grand = array_fill(0, 9, 0);

            foreach ($jobCreationReasons as $reason):
                $cleanReason = preg_replace('/\s+/', ' ', trim($reason));
                $reasonTotals = array_fill(0, 9, 0);
            ?>
                <!-- 3ቱ ዘርፎች በየ Reason ስር -->
                <?php foreach ($sectorsList as $index => $sector): 
                    $cleanSector = preg_replace('/\s+/', ' ', trim($sector));
                    $data = $indexedData[$cleanReason][$cleanSector] ?? [];

                    $pm = (int)($data['perm_m'] ?? 0);
                    $pf = (int)($data['perm_f'] ?? 0);
                    $pt = $pm + $pf;

                    $tm = (int)($data['temp_m'] ?? 0);
                    $tf = (int)($data['temp_f'] ?? 0);
                    $tt = $tm + $tf;

                    $tot_m = $pm + $tm;
                    $tot_f = $pf + $tf;
                    $tot_t = $pt + $tt;

                    // የ Reason ድምር መደመር
                    $reasonTotals[0] += $pm;  $reasonTotals[1] += $pf;  $reasonTotals[2] += $pt;
                    $reasonTotals[3] += $tm;  $reasonTotals[4] += $tf;  $reasonTotals[5] += $tt;
                    $reasonTotals[6] += $tot_m; $reasonTotals[7] += $tot_f; $reasonTotals[8] += $tot_t;
                ?>
                    <tr>
                        <?php if ($index === 0): ?>
                            <td rowspan="3" class="text-left font-weight-bold" style="vertical-align: middle !important;">
                                <?= htmlspecialchars($reason); ?>
                            </td>
                        <?php endif; ?>

                        <td class="text-left"><?= htmlspecialchars($sector); ?></td>
                        <td><?= number_format($pm); ?></td>
                        <td><?= number_format($pf); ?></td>
                        <td><?= number_format($pt); ?></td>
                        <td><?= number_format($tm); ?></td>
                        <td><?= number_format($tf); ?></td>
                        <td><?= number_format($tt); ?></td>
                        <td><?= number_format($tot_m); ?></td>
                        <td><?= number_format($tot_f); ?></td>
                        <td><?= number_format($tot_t); ?></td>
                    </tr>
                <?php endforeach; ?>

                <!-- የየ Reason ድምር (Subtotal Row) -->
                <tr class="reason-total-row">
                    <td colspan="2" class="text-right">ድምር '<?= htmlspecialchars($reason); ?>'</td>
                    <?php for ($i = 0; $i < 9; $i++): ?>
                        <td><?= number_format($reasonTotals[$i]); ?></td>
                    <?php endfor; ?>
                </tr>

            <?php 
                // ወደ Grand Total መደመር
                for ($i = 0; $i < 9; $i++) {
                    $grand[$i] += $reasonTotals[$i];
                }
            endforeach; 
            ?>

            <!-- ጠቅላላ ድምር (Grand Total Row) -->
            <tr class="grand-total-row">
                <td colspan="2" class="text-left">ጠቅላላ ድምር</td>
                <?php for ($i = 0; $i < 9; $i++): ?>
                    <td><?= number_format($grand[$i]); ?></td>
                <?php endfor; ?>
            </tr>
        </tbody>
    </table>
</div>