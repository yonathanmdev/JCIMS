<?php
use App\Helpers\EthiopianDateHelper; 

// የቀን ማስተካከያ
$startdate = !empty($startdate) ? $startdate : date('Y-m-d');
$startdateParts = explode('-', $startdate);
$ethstartDate = EthiopianDateHelper::toEthCalendar($startdateParts[2], $startdateParts[1], $startdateParts[0]);

$enddate = !empty($enddate) ? $enddate : date('Y-m-d');
$enddateParts = explode('-', $enddate);
$ethendDate = EthiopianDateHelper::toEthCalendar($enddateParts[2], $enddateParts[1], $enddateParts[0]);

// 🟢 EXPORT LOGIC: ወደ Excel ለመቀየር ካስፈለገ
if (isset($_GET['export']) && $_GET['export'] == 'excel') {
    header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
    header("Content-Disposition: attachment; filename=Job_Seekers_Report_W4.xls");
    header("Pragma: no-cache");
    header("Expires: 0");
    echo "\xEF\xBB\xBF"; 
}
?>

<style>
    body { padding: 20px; background-color: #fff; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #000; }
    .table-container { width: 100%; margin: 0 auto; background: #fff; }
    .table { width: 100%; border-collapse: collapse; margin-top: 10px; text-align: center; }
    .table th { background-color: #ffffff; text-align: center; vertical-align: middle !important; font-size: 13px; font-weight: bold; border: 1px solid #cbd5e1 !important; padding: 6px; color: #0f172a; }
    .table td { vertical-align: middle !important; font-size: 13px; border: 1px solid #cbd5e1 !important; padding: 5px; color: #334155; }
    
    /* በምስሉ ላይ ያለውን ዲዛይን ለመጠበቅ */
    .sector-header-row { background-color: #ffffff !important; font-weight: bold !important; }
    .sector-header-row td { font-weight: bold !important; color: #000000 !important; font-size: 14px; border-bottom: 2px solid #000 !important; }
    .grand-total-row { background-color: #ffffff !important; font-weight: bold !important; border-top: 2px solid #000 !important; border-bottom: 2px solid #000 !important; }
    .grand-total-row td { font-weight: bold !important; color: #000000 !important; font-size: 14px; }
    
    .text-left { text-align: left !important; padding-left: 10px !important; }
    .font-weight-bold { font-weight: bold !important; }
    
    @media print {
        body { padding: 0; }
        .no-print { display: none; }
    }
</style>

<div class="table-container">
    <center class="mb-4">
        <h3 class="font-weight-bold" style="margin-bottom: 5px;">አጠቃላይ <?= htmlspecialchars($residenceStatus ?? 'የለም'); ?> የተፈጠረ የሥራ ዕድል ሪፖርት <?php  if($residenceStatus=="ከተማ"){ echo "4"; } else{  echo "5"; } ?> </h3>
        <h5 style="margin-top: 0; color: #475569;">የመዋቅር ደረጃ፦ <strong><?= htmlspecialchars($selectedBranchName ?? 'የለም'); ?></strong></h5>
        <h6 style="color: #64748b;">
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
                <th rowspan="2" style="width: 30%;" class="align-middle">ዘርፍ/ንዑስ ዘርፍ</th>
                <th colspan="3">ቋሚ</th>
                <th colspan="3">ጊዜያዊ</th>
                <th colspan="3">ቋሚና ጊዜያዊ</th>
            </tr>
            <tr>
                <th>ወ</th>
                <th>ሴ</th>
                <th>ድ</th>
                <th>ወ</th>
                <th>ሴ</th>
                <th>ድ</th>
                <th>ወ</th>
                <th>ሴ</th>
                <th>ድ</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            // የታላቅ ድምር (Grand Total) ማጠራቀሚያዎች
            $grand_m_perm = 0; $grand_f_perm = 0; $grand_t_perm = 0;
            $grand_m_temp = 0; $grand_f_temp = 0; $grand_t_temp = 0;
            $grand_m_total = 0; $grand_f_total = 0; $grand_t_total = 0;

            // 💡 በምስሉ ላይ ያሉት 3ቱ ዋና ዘርፎች እና ትክክለኛ ንዑስ ዘርፎቻቸው ዝርዝር
            $fixedSectorsStructure = [
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

            foreach ($fixedSectorsStructure as $sectorName => $subSectorList):
                // ከኮንትሮለር ለመጣው ዳታ ኢንዴክሱን በንዑስ ዘርፍ ስም በመስራት እንዲቀልል ማደራጀት
                $incomingData = $reportData[$sectorName] ?? [];
                $indexedRows = [];
                foreach ($incomingData as $row) {
                    if (!empty($row['sub_sector_name'])) {
                        $indexedRows[trim($row['sub_sector_name'])] = $row;
                    }
                }

                // 1. መጀመሪያ የዚህን ዘርፍ ድምር (Subtotal) እናሰላለማለን (ምክንያቱም ሄደሩ ላይ መውጣት ስላለበት)
                $sec_m_perm = 0; $sec_f_perm = 0; $sec_t_perm = 0;
                $sec_m_temp = 0; $sec_f_temp = 0; $sec_t_temp = 0;
                $sec_m_total = 0; $sec_f_total = 0; $sec_t_total = 0;

                foreach ($subSectorList as $subName) {
                    $r = $indexedRows[$subName] ?? null;
                    $mp = $r['m_perm'] ?? 0; $fp = $r['f_perm'] ?? 0;
                    $mt = $r['m_temp'] ?? 0; $ft = $r['f_temp'] ?? 0;

                    $sec_m_perm += $mp; $sec_f_perm += $fp; $sec_t_perm += ($mp + $fp);
                    $sec_m_temp += $mt; $sec_f_temp += $ft; $sec_t_temp += ($mt + $ft);
                    $sec_m_total += ($mp + $mt); $sec_f_total += ($fp + $ft); $sec_t_total += ($mp + $fp + $mt + $ft);
                }

                // ወደ Grand Total መደመር
                $grand_m_perm += $sec_m_perm; $grand_f_perm += $sec_f_perm; $grand_t_perm += $sec_t_perm;
                $grand_m_temp += $sec_m_temp; $grand_f_temp += $sec_f_temp; $grand_t_temp += $sec_t_temp;
                $grand_m_total += $sec_m_total; $grand_f_total += $sec_f_total; $grand_t_total += $sec_t_total;
            ?>
                <!-- 💡 የዘርፉ ራስጌ ረድፍ (በምስሉ መሠረት ስሙና ድምሩ መጀመሪያ ላይ ይቀመጣል) -->
                <tr class="sector-header-row">
                    <td class="text-left"><?= htmlspecialchars($sectorName); ?></td>
                    <td><?= number_format($sec_m_perm); ?></td>
                    <td><?= number_format($sec_f_perm); ?></td>
                    <td><?= number_format($sec_t_perm); ?></td>
                    <td><?= number_format($sec_m_temp); ?></td>
                    <td><?= number_format($sec_f_temp); ?></td>
                    <td><?= number_format($sec_t_temp); ?></td>
                    <td><?= number_format($sec_m_total); ?></td>
                    <td><?= number_format($sec_f_total); ?></td>
                    <td><?= number_format($sec_t_total); ?></td>
                </tr>

                <?php 
                // 2. ከዘርፉ በታች ንዑስ ዘርፎችን አንድ በአንድ በቅደም ተከተል መዘርዘር
                foreach ($subSectorList as $subName):
                    $r = $indexedRows[$subName] ?? null;
                    $mp = $r['m_perm'] ?? 0; $fp = $r['f_perm'] ?? 0;
                    $mt = $r['m_temp'] ?? 0; $ft = $r['f_temp'] ?? 0;

                    $row_t_perm = $mp + $fp;
                    $row_t_temp = $mt + $ft;
                    $row_m_total = $mp + $mt;
                    $row_f_total = $fp + $ft;
                    $row_t_total = $row_m_total + $row_f_total;
                ?>
                    <tr>
                        <td class="text-left" style="padding-left: 20px; color: #475569;"><?= htmlspecialchars($subName); ?></td>
                        <td><?= number_format($mp); ?></td>
                        <td><?= number_format($fp); ?></td>
                        <td class="font-weight-bold" style="color:#64748b;"><?= number_format($row_t_perm); ?></td>
                        <td><?= number_format($mt); ?></td>
                        <td><?= number_format($ft); ?></td>
                        <td class="font-weight-bold" style="color:#64748b;"><?= number_format($row_t_temp); ?></td>
                        <td class="font-weight-bold"><?= number_format($row_m_total); ?></td>
                        <td class="font-weight-bold"><?= number_format($row_f_total); ?></td>
                        <td class="font-weight-bold" style="background-color: #f8fafc;"><?= number_format($row_t_total); ?></td>
                    </tr>
                <?php endforeach; ?>

            <?php endforeach; ?>

            <!-- 💡 የታላቅ ድምር ረድፍ (Grand Total Row) በመጨረሻ ላይ -->
            <tr class="grand-total-row">
                <td class="text-left">ድምር</td>
                <td><?= number_format($grand_m_perm); ?></td>
                <td><?= number_format($grand_f_perm); ?></td>
                <td><?= number_format($grand_t_perm); ?></td>
                <td><?= number_format($grand_m_temp); ?></td>
                <td><?= number_format($grand_f_temp); ?></td>
                <td><?= number_format($grand_t_temp); ?></td>
                <td><?= number_format($grand_m_total); ?></td>
                <td><?= number_format($grand_f_total); ?></td>
                <td style="background-color: #f1f5f9;"><?= number_format($grand_t_total); ?></td>
            </tr>
        </tbody>
    </table>
</div>