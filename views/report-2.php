<!-- report-2.php -->
<?php
use App\Helpers\EthiopianDateHelper; 

$startdate = !empty($startdate) ? $startdate : date('Y-m-d');
$startdateParts = explode('-', $startdate);
$ethstartDate = EthiopianDateHelper::toEthCalendar($startdateParts[2], $startdateParts[1], $startdateParts[0]);

$enddate = !empty($enddate) ? $enddate : date('Y-m-d');
$enddateParts = explode('-', $enddate);
$ethendDate = EthiopianDateHelper::toEthCalendar($enddateParts[2], $enddateParts[1], $enddateParts[0]);

// ከController የሚመጣውን $reports አደራደር መነሻ በማድረግ Re-index ማድረግ
$data = [];
if (!empty($reports)) {
    foreach ($reports as $row) {
        $data[$row->sub_sector_name] = $row;
    }
}

// ድምሮችን ለመደመርና ለማሳየት የሚረዳ Helper Function
function getVal($data, $subSector, $field) {
    return isset($data[$subSector]) ? (int)$data[$subSector]->$field : 0;
}
?>
<style>
    body { padding: 10px; background-color: #fff; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #000; }
    .table-container { width: 100%; overflow-x: auto; background: #fff; }
    .table { width: 100%; border-collapse: collapse; margin-top: 10px; text-align: center; font-size: 11px; }
    .table th { background-color: #ffffff; text-align: center; vertical-align: middle !important; font-weight: bold; border: 1px solid #94a3b8 !important; padding: 5px; color: #0f172a; }
    .table td { vertical-align: middle !important; border: 1px solid #cbd5e1 !important; padding: 4px; color: #1e293b; }
    
    .text-left { text-align: left !important; padding-left: 8px !important; }
    .ps-4 { padding-left: 25px !important; } /* ለንዑስ ዘርፎች ገብ ብሎ እንዲታይ */
    
    @media print {
        body { padding: 0; }
        .no-print { display: none; }
        .table-container { overflow: visible; }
    }
</style>

<div class="table-container">
    <div style="text-align: right; margin-bottom: 15px;" class="no-print">
        <a href="?<?= http_build_query(array_merge($_GET, ['export' => 'excel'])) ?>" class="btn btn-success">
            <i class="fa fa-file-excel"></i> ወደ Excel አውርድ (Export to Excel)
        </a>
    </div>
    <center class="mb-3">
        <h3 class="font-weight-bold" style="margin-bottom: 5px;"> ኢንተርፕራይዝ በማቋቋም በ<?= htmlspecialchars($residenceStatus ?? 'የለም'); ?> የተፈጠረ የሥራ ዕድል ሪፖርት <?php echo ($residenceStatus == "ከተማ") ? "(ሠ2)" : "(ሠ3)"; ?></h3>
        <h5 style="margin-top: 0; color: #475569;">የመዋቅር ደረጃ፦ <strong><?= htmlspecialchars($selectedBranchName ?? 'የለም'); ?></strong></h5>
        <h6 style="color: #64748b; margin-top: 0;">
            የሪፖርት ዘመን፦ 
            <?= EthiopianDateHelper::getMonthName($ethstartDate['month']) ?> <?= $ethstartDate['day'] ?>፣ <?= $ethstartDate['year'] ?> 
            <?php if ($startdate != $enddate): ?>
                እስከ <?= EthiopianDateHelper::getMonthName($ethendDate['month']) ?> <?= $ethendDate['day'] ?>፣ <?= $ethendDate['year'] ?>
            <?php endif; ?>
        </h6>
    </center>

    <table class="table table-bordered table-hover align-middle text-center small mb-0">
        <thead class="table-light sticky-top" style="z-index: 10;">
            <tr>
                <th rowspan="3" class="align-middle text-left" style="min-width: 240px;">ዘርፍ / ንዑስ ዘርፍ</th>
                <th colspan="3" class="align-middle">የአንተርፕራይዝ ቁጥር</th>
                <th colspan="9" class="align-middle">የተፈጠረ የሥራ እድል</th>
            </tr>
            <tr>
                <th rowspan="2" class="align-middle">በንግድ ማህበር</th>
                <th rowspan="2" class="align-middle">በግለሰብ</th>
                <th rowspan="2" class="align-middle bg-light">ድምር</th>
                <th colspan="3" class="align-middle">ቋሚ</th>
                <th colspan="3" class="align-middle">ጊዜያዊ</th>
                <th colspan="3" class="align-middle bg-light">ቋሚና ጊዜያዊ</th>
            </tr>
            <tr>
                <th>ወ</th>
                <th>ሴ</th>
                <th class="bg-light">ድ</th>
                <th>ወ</th>
                <th>ሴ</th>
                <th class="bg-light">ድ</th>
                <th>ወ</th>
                <th>ሴ</th>
                <th class="bg-light">ድ</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // የዘርፎችና ንዑስ ዘርፎች መዋቅር
            $structure = [
                'ግብርና' => [
                    'ሰብል በመስኖ ማምረት', 'የአትክልትና ፍራፍሬ ችግኝ', 'የወተት ላም እርባታ', 'የዳልጋ ከብት ማድለብ', 
                    'በግና ፍየል እርባታ', 'በግና ፍየል ማድለብ', 'ደሮ እርባታ', 'መኖ ልማት', 'አሳ እርባታ', 
                    'አረንጓዴ ልማት የደን ችግኝ', 'ንብ ሀብት ልማት', 'ሀር ምርት', 'ግብርና ሌሎች'
                ],
                'ኢንዱስትሪ' => [
                    'አግሮፕሮሰሲንግ', 'ቆዳና የቆዳ ውጤቶች', 'ጨርቃጨርቅና አልባሳት', 'የእንጨት ስራዎች', 'በቅርቀሀ ስራዎች', 
                    'የብረታ ብረትና ኢንጅነሪንግ ምርቶች', 'ምግብና መጠጥ ዝግጅት', 'ባህላዊ እደጥበብና ጌጣጌጥ ስራዎች', 
                    'የኬሚካል ውጤቶች', 'ታዳሽ ሀይል', 'ብሎኬት፣ቴራዞንና እምነበረድ ምርት', 'በመንገድ ግንባታና ዲዛይን ስራዎች', 
                    'በመስኖ ግንባታ ስራዎች', 'በመጠጥ ውሀ ግንባታ ስራዎች', 'ጠቅላላ ስራ ተቋራጭነት', 'ኮብል ስቶን ስራዎች', 
                    'የከበረ ደንጋይ ማውጣት', 'የድንጋይ ወፍጮጠጠር ማምረት', 'አሸዋ ማምረት', 'ወርቅ ማምረት', 
                    'ኦ ፖል ማምረት', 'አምበር ማምረት', 'የወንዝ ጠጠር', 'ሸክላ አፈር፣ሰሌክትጋራጋንቲ', 'ኢንዱስትሪ ሌሎች'
                ],
                'አገልግሎት' => [
                    'ቱሪዝም', 'የስነጥበብ', 'በማዘጋጃ ቤት ስራዎች', 'ኢንፎርሜሽን ቴክኖሎጂ', 'ሎጂስቲክ፣ ትራንስፖርትና መገናኛ', 
                    'የመብራት፣ ውሀና ሌሎች መገልገያዎች ስራዎች', 'ማህበራዊ አገልግሎቶች', 'የዉበት ሳሎን ስራዎች', 
                    'ምግብና መጠጥ እና ካፍቴሪያ አገልግሎት', 'መንግስታዊ አገልግሎቶች', 'የሀገር ዉስጥ ምርቶች ጅምላ', 
                    'የሀገር ዉስጥ ምርቶች ችርቻሮ', 'የጥሬ እቃ አቅርቦት', 'በመንግስት መቤቶች የተቀጠሩ', 
                    'አገልግሎት ሌሎች'
                ]
            ];

            // የጠቅላላ ድምር ተለዋዋጮች
            $g_biz_m = $g_biz_p = $g_perm_m = $g_perm_f = $g_temp_m = $g_temp_f = 0;

            foreach ($structure as $main_sector => $sub_sectors):
                // 1. አስቀድሞ የዋና ሴክተሩን ድምር ማስላት
                $s_biz_m = $s_biz_p = $s_perm_m = $s_perm_f = $s_temp_m = $s_temp_f = 0;

                foreach ($sub_sectors as $sub) {
                    $s_biz_m += getVal($data, $sub, 'biz_mahber');
                    $s_biz_p += getVal($data, $sub, 'biz_private');
                    $s_perm_m += getVal($data, $sub, 'perm_m');
                    $s_perm_f += getVal($data, $sub, 'perm_f');
                    $s_temp_m += getVal($data, $sub, 'temp_m');
                    $s_temp_f += getVal($data, $sub, 'temp_f');
                }

                // ለጠቅላላ ድምር መደመር
                $g_biz_m += $s_biz_m; $g_biz_p += $s_biz_p;
                $g_perm_m += $s_perm_m; $g_perm_f += $s_perm_f;
                $g_temp_m += $s_temp_m; $g_temp_f += $s_temp_f;
            ?>
                <!-- የዋና ሴክተሩ ስምና የንዑስ ሴክተሮቹ ድምር በአንድ ላይ የሚታይበት ረድፍ -->
                <tr class="fw-bold bg-light text-start">
                    <td class="text-left"><?= $main_sector ?></td>
                    <td class="text-center"><?= number_format($s_biz_m) ?></td>
                    <td class="text-center"><?= number_format($s_biz_p) ?></td>
                    <td class="text-center"><?= number_format($s_biz_m + $s_biz_p) ?></td>
                    <td class="text-center"><?= number_format($s_perm_m) ?></td>
                    <td class="text-center"><?= number_format($s_perm_f) ?></td>
                    <td class="text-center"><?= number_format($s_perm_m + $s_perm_f) ?></td>
                    <td class="text-center"><?= number_format($s_temp_m) ?></td>
                    <td class="text-center"><?= number_format($s_temp_f) ?></td>
                    <td class="text-center"><?= number_format($s_temp_m + $s_temp_f) ?></td>
                    <td class="text-center"><?= number_format($s_perm_m + $s_temp_m) ?></td>
                    <td class="text-center"><?= number_format($s_perm_f + $s_temp_f) ?></td>
                    <td class="text-center"><?= number_format($s_perm_m + $s_perm_f + $s_temp_m + $s_temp_f) ?></td>
                </tr>

                <!-- የንዑስ ሴክተሮች ዝርዝር (ከግራ ጀምረው ትንሽ ገብ ብለው ይቀመጣሉ) -->
                <?php foreach ($sub_sectors as $sub):
                    $bm = getVal($data, $sub, 'biz_mahber');
                    $bp = getVal($data, $sub, 'biz_private');
                    $pm = getVal($data, $sub, 'perm_m');
                    $pf = getVal($data, $sub, 'perm_f');
                    $tm = getVal($data, $sub, 'temp_m');
                    $tf = getVal($data, $sub, 'temp_f');
                ?>
                    <tr>
                        <td class="text-left ps-4"><?= $sub ?></td>
                        <td><?= number_format($bm) ?></td>
                        <td><?= number_format($bp) ?></td>
                        <td class="fw-bold"><?= number_format($bm + $bp) ?></td>
                        <td><?= number_format($pm) ?></td>
                        <td><?= number_format($pf) ?></td>
                        <td class="fw-bold"><?= number_format($pm + $pf) ?></td>
                        <td><?= number_format($tm) ?></td>
                        <td><?= number_format($tf) ?></td>
                        <td class="fw-bold"><?= number_format($tm + $tf) ?></td>
                        <td><?= number_format($pm + $tm) ?></td>
                        <td><?= number_format($pf + $tf) ?></td>
                        <td class="fw-bold"><?= number_format($pm + $pf + $tm + $tf) ?></td>
                    </tr>
                <?php endforeach; ?>

            <?php endforeach; ?>

            <!-- የሦስቱ ዋና ዋና ዘርፎች ድምር (Sub-Total) -->
            <tr class="fw-bold bg-secondary text-white">
                <td class="text-left">ድምር</td>
                <td><?= number_format($g_biz_m) ?></td>
                <td><?= number_format($g_biz_p) ?></td>
                <td><?= number_format($g_biz_m + $g_biz_p) ?></td>
                <td><?= number_format($g_perm_m) ?></td>
                <td><?= number_format($g_perm_f) ?></td>
                <td><?= number_format($g_perm_m + $g_perm_f) ?></td>
                <td><?= number_format($g_temp_m) ?></td>
                <td><?= number_format($g_temp_f) ?></td>
                <td><?= number_format($g_temp_m + $g_temp_f) ?></td>
                <td><?= number_format($g_perm_m + $g_temp_m) ?></td>
                <td><?= number_format($g_perm_f + $g_temp_f) ?></td>
                <td><?= number_format($g_perm_m + $g_perm_f + $g_temp_m + $g_temp_f) ?></td>
            </tr>

            <!-- በቤተሰብ ንግድ የተደራጁ ኢንተርፕራይዞች -->
            <?php 
            $family_sectors = [
                'በቤተሰብ ንግድ የተደራጁ ኢንተርፕራይዞች' => 'family_total',
                'በግብርና ዘርፍ' => 'family_agri',
                'በኢንዱስትሪ ዘርፍ' => 'family_ind',
                'በአገልግሎት ዘርፍ' => 'family_serv'
            ];

            foreach ($family_sectors as $label => $key):
                $f_bm = getVal($data, $key, 'biz_mahber');
                $f_bp = getVal($data, $key, 'biz_private');
                $f_pm = getVal($data, $key, 'perm_m');
                $f_pf = getVal($data, $key, 'perm_f');
                $f_tm = getVal($data, $key, 'temp_m');
                $f_tf = getVal($data, $key, 'temp_f');
            ?>
                <tr class="fw-bold table-warning">
                    <td class="text-left"><?= $label ?></td>
                    <td><?= number_format($f_bm) ?></td>
                    <td><?= number_format($f_bp) ?></td>
                    <td><?= number_format($f_bm + $f_bp) ?></td>
                    <td><?= number_format($f_pm) ?></td>
                    <td><?= number_format($f_pf) ?></td>
                    <td><?= number_format($f_pm + $f_pf) ?></td>
                    <td><?= number_format($f_tm) ?></td>
                    <td><?= number_format($f_tf) ?></td>
                    <td><?= number_format($f_tm + $f_tf) ?></td>
                    <td><?= number_format($f_pm + $f_tm) ?></td>
                    <td><?= number_format($f_pf + $f_tf) ?></td>
                    <td><?= number_format($f_pm + $f_pf + $f_tm + $f_tf) ?></td>
                </tr>
            <?php endforeach; ?>

            <!-- ጠቅላላ ድምር (Grand Total) -->
            <?php
            $tot_fam_bm = getVal($data, 'family_total', 'biz_mahber');
            $tot_fam_bp = getVal($data, 'family_total', 'biz_private');
            $tot_fam_pm = getVal($data, 'family_total', 'perm_m');
            $tot_fam_pf = getVal($data, 'family_total', 'perm_f');
            $tot_fam_tm = getVal($data, 'family_total', 'temp_m');
            $tot_fam_tf = getVal($data, 'family_total', 'temp_f');

            $grand_bm = $g_biz_m + $tot_fam_bm;
            $grand_bp = $g_biz_p + $tot_fam_bp;
            $grand_pm = $g_perm_m + $tot_fam_pm;
            $grand_pf = $g_perm_f + $tot_fam_pf;
            $grand_tm = $g_temp_m + $tot_fam_tm;
            $grand_tf = $g_temp_f + $tot_fam_tf;
            ?>
            <tr class="fw-bold bg-primary text-white border-top border-dark border-3">
                <td class="text-left fs-6">ጠቅላላ ድምር</td>
                <td><?= number_format($grand_bm) ?></td>
                <td><?= number_format($grand_bp) ?></td>
                <td><?= number_format($grand_bm + $grand_bp) ?></td>
                <td><?= number_format($grand_pm) ?></td>
                <td><?= number_format($grand_pf) ?></td>
                <td><?= number_format($grand_pm + $grand_pf) ?></td>
                <td><?= number_format($grand_tm) ?></td>
                <td><?= number_format($grand_tf) ?></td>
                <td><?= number_format($grand_tm + $grand_tf) ?></td>
                <td><?= number_format($grand_pm + $grand_tm) ?></td>
                <td><?= number_format($grand_pf + $grand_tf) ?></td>
                <td><?= number_format($grand_pm + $grand_pf + $grand_tm + $grand_tf) ?></td>
            </tr>
        </tbody>
    </table>
</div>