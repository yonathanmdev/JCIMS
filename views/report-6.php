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
    header("Content-Disposition: attachment; filename=Inclusive_Report_06.xls");
    header("Pragma: no-cache");
    header("Expires: 0");
    echo "\xEF\xBB\xBF"; 
}
?>

<style>
    body { padding: 10px; background-color: #fff; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #000; }
    .table-container { width: 100%; overflow-x: auto; background: #fff; }
    .table { width: 100%; border-collapse: collapse; margin-top: 10px; text-align: center; font-size: 11px; }
    .table th { background-color: #ffffff; text-align: center; vertical-align: middle !important; font-weight: bold; border: 1px solid #94a3b8 !important; padding: 4px; color: #0f172a; }
    .table td { vertical-align: middle !important; border: 1px solid #cbd5e1 !important; padding: 3px; color: #1e293b; }
    
    .sector-header-row { background-color: #f8fafc !important; font-weight: bold !important; }
    .sector-header-row td { font-weight: bold !important; color: #000000 !important; font-size: 12px; border-bottom: 2px solid #000 !important; }
    .grand-total-row { background-color: #e2e8f0 !important; font-weight: bold !important; border-top: 2px solid #000 !important; border-bottom: 2px solid #000 !important; }
    .grand-total-row td { font-weight: bold !important; color: #000000 !important; font-size: 12px; }
    
    .text-left { text-align: left !important; padding-left: 8px !important; }
    
    @media print {
        body { padding: 0; }
        .no-print { display: none; }
        .table-container { overflow: visible; }
    }
</style>

<div class="table-container">
    <center class="mb-3">
        <h3 class="font-weight-bold" style="margin-bottom: 5px;">በ<?= htmlspecialchars($residenceStatus ?? 'የለም'); ?> የሥራ እድል የተፈጠረባቸው የሥራ ዓይነቶች በአንቀሳቃሾች ቁጥር/Forms of Employment <?php  if($residenceStatus=="ከተማ"){ echo "6"; } else{  echo "7"; } ?> </h3>
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
                <th rowspan="3" style="min-width: 180px;" class="align-middle">ዘርፍ / ንዑስ ዘርፍ</th>
                <th colspan="4">አዳዲስ ኢንተርፕራይዞች በማቋቋም የተፈጠረ</th>
                <th colspan="4">ነባር ኢንተርፕራይዞችን በማስፋፋት የተቀጠሩ</th>
                <th colspan="4">የግል ዘርፍ ኢንቭስትመንት/ድርጅቶች የተቀጠሩ</th>
                <th colspan="4">በመንግስት ኢንተርፕራይዞች/ግዙፍ ፕሮጀክቶች የተቀጠሩ</th>
                <th colspan="4">በህ/ስ/ማህበራት የተቀጠሩ</th>
                <th colspan="4">መንግስታዊ ያልሆኑ ድርጅቶች ቅጥር</th>
                <th colspan="4">በመንግስት መ/ቤቶች የተቀጠሩ</th>
                <th colspan="4">የውጭ አገር ሥራ ስምሪት</th>
            </tr>
            <tr>
                <th colspan="2">ቋሚ</th><th colspan="2">ጊዜያዊ</th>
                <th colspan="2">ቋሚ</th><th colspan="2">ጊዜያዊ</th>
                <th colspan="2">ቋሚ</th><th colspan="2">ጊዜያዊ</th>
                <th colspan="2">ቋሚ</th><th colspan="2">ጊዜያዊ</th>
                <th colspan="2">ቋሚ</th><th colspan="2">ጊዜያዊ</th>
                <th colspan="2">ቋሚ</th><th colspan="2">ጊዜያዊ</th>
                <th colspan="2">ቋሚ</th><th colspan="2">ጊዜያዊ</th>
                <th colspan="2">ቋሚ</th><th colspan="2">ጊዜያዊ</th>
            </tr>
            <tr>
                <?php for($i = 0; $i < 16; $i++): ?>
                    <th>ወ</th><th>ሴ</th>
                <?php endfor; ?>
            </tr>
        </thead>
        <tbody>
            <?php 
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

            $grandTotals = array_fill(0, 32, 0);

            foreach ($fixedSectorsStructure as $sectorName => $subSectorList):
                
                $sectorTotals = array_fill(0, 32, 0);

                $incomingData = $reportData[$sectorName] ?? [];
                $indexedRows = [];
                foreach ($incomingData as $row) {
                    if (!empty($row['sub_sector_name'])) {
                        $cleanKey = preg_replace('/\s+/', ' ', trim($row['sub_sector_name']));
                        $indexedRows[$cleanKey] = $row;
                    }
                }

                foreach ($subSectorList as $subName) {
                    $cleanSubName = preg_replace('/\s+/', ' ', trim($subName));
                    $r = $indexedRows[$cleanSubName] ?? [];
                    for ($c = 1; $c <= 32; $c++) {
                        $colKey = "c" . $c;
                        $val = $r[$colKey] ?? 0;
                        $sectorTotals[$c - 1] += $val;
                    }
                }

                for ($c = 0; $c < 32; $c++) {
                    $grandTotals[$c] += $sectorTotals[$c];
                }
            ?>
                <!-- Sector Header Row -->
                <tr class="sector-header-row">
                    <td class="text-left"><?= htmlspecialchars($sectorName); ?></td>
                    <?php for ($c = 0; $c < 32; $c++): ?>
                        <td><?= number_format($sectorTotals[$c]); ?></td>
                    <?php endfor; ?>
                </tr>

                <!-- Sub-sector Rows -->
                <?php foreach ($subSectorList as $subName): 
                    $cleanSubName = preg_replace('/\s+/', ' ', trim($subName));
                    $r = $indexedRows[$cleanSubName] ?? [];
                ?>
                    <tr>
                        <td class="text-left" style="padding-left: 15px;"><?= htmlspecialchars($subName); ?></td>
                        <?php for ($c = 1; $c <= 32; $c++): 
                            $colKey = "c" . $c;
                            $val = $r[$colKey] ?? 0;
                        ?>
                            <td><?= number_format($val); ?></td>
                        <?php endfor; ?>
                    </tr>
                <?php endforeach; ?>

            <?php endforeach; ?>

            <!-- Grand Total Row -->
            <tr class="grand-total-row">
                <td class="text-left">ድምር</td>
                <?php for ($c = 0; $c < 32; $c++): ?>
                    <td><?= number_format($grandTotals[$c]); ?></td>
                <?php endfor; ?>
            </tr>
        </tbody>
    </table>
</div>