<?php
use App\Helpers\EthiopianDateHelper; 
 $report = $report1 ?? [
    'urban_m_parents' => 0,
    'urban_f_parents' => 0,
    'rural_m_parents' => 0,
    'rural_f_parents' => 0,

    'urban_m_others' => 0,
    'urban_f_others' => 0,
    'rural_m_others' => 0,
    'rural_f_others' => 0,

    'urban_m_advice' => 0,
    'urban_f_advice' => 0,
    'rural_m_advice' => 0,
    'rural_f_advice' => 0,

    'urban_m_age15_29' => 0,
    'urban_f_age15_29' => 0,
    'rural_m_age15_29' => 0,
    'rural_f_age15_29' => 0,

    'urban_m_age30_64' => 0,
    'urban_f_age30_64' => 0,
    'rural_m_age30_64' => 0,
    'rural_f_age30_64' => 0,
    
    'urban_m_uni' => 0,
    'urban_f_uni' => 0,
    'rural_m_uni' => 0,
    'rural_f_uni' => 0,

    'urban_m_tvt' => 0,
    'urban_f_tvt' => 0,
    'rural_m_tvt' => 0,
    'rural_f_tvt' => 0,

    'urban_m_phy' => 0,
    'urban_f_phy' => 0,
    'rural_m_phy' => 0,
    'rural_f_phy' => 0,
    
    'urban_m_immg' => 0,
    'urban_f_immg' => 0,
    'rural_m_immg' => 0,
    'rural_f_immg' => 0,

    'urban_m_teff' => 0,
    'urban_f_teff' => 0,
    'rural_m_teff' => 0,
    'rural_f_teff' => 0,
    
    'urban_m_noh' => 0,
    'urban_f_noh' => 0,
    'rural_m_noh' => 0,
    'rural_f_noh' => 0,

    'urban_m_ajs' => 0,
    'urban_f_ajs' => 0,
    'rural_m_ajs' => 0,
    'rural_f_ajs' => 0,

    'urban_m_ajs15_29' => 0,
    'urban_f_ajs15_29' => 0,
    'rural_m_ajs15_29' => 0,
    'rural_f_ajs15_29' => 0,

    'urban_m_ajsuni' => 0,
    'urban_f_ajsuni' => 0,
    'rural_m_ajsuni' => 0,
    'rural_f_ajsuni' => 0,

    'urban_m_ajstvt' => 0,
    'urban_f_ajstvt' => 0,
    'rural_m_ajstvt' => 0,
    'rural_f_ajstvt' => 0,

    'urban_m_ajsdis' => 0,
    'urban_f_ajsdis' => 0,
    'rural_m_ajsdis' => 0,
    'rural_f_ajsdis' => 0,

    'urban_m_ajsimmg' => 0,
    'urban_f_ajsimmg' => 0,
    'rural_m_ajsimmg' => 0,
    'rural_f_ajsimmg' => 0,

    'urban_m_ajsnoh' => 0,
    'urban_f_ajsnoh' => 0,
    'rural_m_ajsnoh' => 0,
    'rural_f_ajsnoh' => 0,
    ];
$selectedBranchName = $branchData['name'] ?? $_SESSION['user']['branch_name'];
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
$ethendDate = EthiopianDateHelper::toEthCalendar($enddateParts[2], $enddateParts[1], $enddateParts[0]);?>
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

<center class="mb-4">
    <!-- የሪፖርት ርዕስ -->
    <h4 class="font-weight-bold">የስራ ፈላጊዎች ምዝገባ እና ግንዛቤ ፈጠራ (ሠ-1)</h4>
    
    <!-- 🟢 አዲስ የተጨመረ፦ የትኛው መዋቅር ሪፖርት እንደሆነ በግልጽ ያሳያል -->
    <h5 class="text-primary mt-2">የመዋቅር ደረጃ፦ <strong><?= htmlspecialchars($selectedBranchName); ?></strong></h5>
    
 <h6>
    የሪፖርት ቀን፦
    <?= EthiopianDateHelper::getMonthName($ethstartDate['month']) ?>
    <?= $ethstartDate['day'] ?>
    <?= $ethstartDate['year'] ?>

    <?php if ($startdate != $enddate): ?>
        -
        <?= EthiopianDateHelper::getMonthName($ethendDate['month']) ?>
        <?= $ethendDate['day'] ?>
        <?= $ethendDate['year'] ?>
    <?php endif; ?>
</h6>
</center>
<div class="table-responsive">
    <table class="table table-bordered table-striped text-center">
        <thead class="table-primary">
            <tr>
                <th rowspan="3" style="width: 10%;">ስትራቴጂያዊ ግቦች</th>
                <th rowspan="2">አመልካቾች</th>
                <th rowspan="3" style="width: 4%;">መለኪያ</th>
                <th colspan="3">ከተማ</th>
                <th colspan="3">ገጠር</th>
                <th colspan="3">ከተማና ገጠር</th>
            </tr>
            <tr>
                <th>ወንድ</th>
                <th>ሴት</th>
                <th>ድምር</th>

                <th>ወንድ</th>
                <th>ሴት</th>
                <th>ድምር</th>

                <th>ወንድ</th>
                <th>ሴት</th>
                <th>ድምር</th>
            </tr>
        </thead>

        <tbody>
            <tr>
                <td rowspan="20" class="font-weight-bold" style="background-color: #fff;">
                ዘላቂ የሥራ ዕድል በመፍጠር የዜጎችን ተጠቃሚነት ማረጋገጥ
                        </td>
                <td class="text-left">
                    አጠቃላይ የተመዘገቡ ስራ ፈላጊዎች
                </td>
                <td>በቁጥር</td>

                <td><?= $report['urban_m_advice']; ?></td>
                <td><?= $report['urban_f_advice']; ?></td>
                <td><?= $totalUrbanadvice; ?></td>

                <td><?= $report['rural_m_advice']; ?></td>
                <td><?= $report['rural_f_advice']; ?></td>
                <td><?= $totalRuraladvice; ?></td>

                <td><?= $totalMaleadvice; ?></td>
                <td><?= $totalFemaleadvice; ?></td>
                <td><strong><?= $grandTotaladvice; ?></strong></td>
            </tr>
            <tr>
                <td class="text-left">
                    በዕድሜ (15-29 ዓመት)
                </td>
                <td>በቁጥር</td>

                <td><?= $report['urban_m_age15_29']; ?></td>
                <td><?= $report['urban_f_age15_29']; ?></td>
                <td><?= $totalUrbanage15_29; ?></td>

                <td><?= $report['rural_m_age15_29']; ?></td>
                <td><?= $report['rural_f_age15_29']; ?></td>
                <td><?= $totalRuralage15_29; ?></td>

                <td><?= $totalMaleage15_29; ?></td>
                <td><?= $totalFemaleage15_29; ?></td>
                <td><strong><?= $grandTotalage15_29; ?></strong></td>
            </tr>
            <tr>
                <td class="text-left">
                    በዕድሜ (30-64 ዓመት)
                </td>
                <td>በቁጥር</td>

                <td><?= $report['urban_m_age30_64']; ?></td>
                <td><?= $report['urban_f_age30_64']; ?></td>
                <td><?= $totalUrbanage30_64; ?></td>

                <td><?= $report['rural_m_age30_64']; ?></td>
                <td><?= $report['rural_f_age30_64']; ?></td>
                <td><?= $totalRuralage30_64; ?></td>

                <td><?= $totalMaleage30_64; ?></td>
                <td><?= $totalFemaleage30_64; ?></td>
                <td><strong><?= $grandTotalage30_64; ?></strong></td>
            </tr>
            <tr>
                <td class="text-left">
                    የተመዘገቡ የዩኒቨርሲቲ ተመራቂዎች
                </td>
                <td>በቁጥር</td>

                <td><?= $report['urban_m_uni']; ?></td>
                <td><?= $report['urban_f_uni']; ?></td>
                <td><?= $totalUrbanuni; ?></td>

                <td><?= $report['rural_m_uni']; ?></td>
                <td><?= $report['rural_f_uni']; ?></td>
                <td><?= $totalRuraluni; ?></td>

                <td><?= $totalMaleuni; ?></td>
                <td><?= $totalFemaleuni; ?></td>
                <td><strong><?= $grandTotaluni; ?></strong></td>
            </tr>

            <tr>
                <td class="text-left">
                    የተመዘገቡ የቴክኒክና ሙያ ተመራቂዎች
                </td>
                <td>በቁጥር</td>

                <td><?= $report['urban_m_tvt']; ?></td>
                <td><?= $report['urban_f_tvt']; ?></td>
                <td><?= $totalUrbantvt; ?></td>

                <td><?= $report['rural_m_tvt']; ?></td>
                <td><?= $report['rural_f_tvt']; ?></td>
                <td><?= $totalRuraltvt; ?></td>

                <td><?= $totalMaletvt; ?></td>
                <td><?= $totalFemaletvt; ?></td>
                <td><strong><?= $grandTotaltvt; ?></strong></td>
            </tr>

            <tr>
                <td class="text-left">
                    የተመዘገቡ አካል ጉዳተኞች
                </td>
                <td>በቁጥር</td>

                <td><?= $report['urban_m_phy']; ?></td>
                <td><?= $report['urban_f_phy']; ?></td>
                <td><?= $totalUrbanphy; ?></td>

                <td><?= $report['rural_m_phy']; ?></td>
                <td><?= $report['rural_f_phy']; ?></td>
                <td><?= $totalRuralphy; ?></td>

                <td><?= $totalMalephy; ?></td>
                <td><?= $totalFemalephy; ?></td>
                <td><strong><?= $grandTotalphy; ?></strong></td>
            </tr>
            <tr>
                <td class="text-left">
                    የተመዘገቡ ከስደት ተመላሾች
                </td>
                <td>በቁጥር</td>

                <td><?= $report['urban_m_immg']; ?></td>
                <td><?= $report['urban_f_immg']; ?></td>
                <td><?= $totalUrbanimmg; ?></td>

                <td><?= $report['rural_m_immg']; ?></td>
                <td><?= $report['rural_f_immg']; ?></td>
                <td><?= $totalRuralimmg; ?></td>

                <td><?= $totalMaleimmg; ?></td>
                <td><?= $totalFemaleimmg; ?></td>
                <td><strong><?= $grandTotalimmg; ?></strong></td>
            </tr>

             <tr>
                <td class="text-left">
                    የተመዘገቡ የሀገር ውስጥ ተፈናቃዮች
                </td>
                <td>በቁጥር</td>

                <td><?= $report['urban_m_teff']; ?></td>
                <td><?= $report['urban_f_teff']; ?></td>
                <td><?= $totalUrbanteff; ?></td>

                <td><?= $report['rural_m_teff']; ?></td>
                <td><?= $report['rural_f_teff']; ?></td>
                <td><?= $totalRuralteff; ?></td>

                <td><?= $totalMaleteff; ?></td>
                <td><?= $totalFemaleteff; ?></td>
                <td><strong><?= $grandTotalteff; ?></strong></td>
            </tr>
            <tr>
                <td class="text-left">
                    የተመዘገቡ መኖሪያቸው ጎዳና የሆኑ
                </td>
                <td>በቁጥር</td>

                <td><?= $report['urban_m_noh']; ?></td>
                <td><?= $report['urban_f_noh']; ?></td>
                <td><?= $totalUrbannoh; ?></td>

                <td><?= $report['rural_m_noh']; ?></td>
                <td><?= $report['rural_f_noh']; ?></td>
                <td><?= $totalRuralnoh; ?></td>

                <td><?= $totalMalenoh; ?></td>
                <td><?= $totalFemalenoh; ?></td>
                <td><strong><?= $grandTotalnoh; ?></strong></td>
            </tr>
                        <tr>
                <td class="text-left">
                    ግንዛቤ ማስጨባጫ የወሰዱ ስራ ፈላጊዎች
                </td>
                <td>በቁጥር</td>

                <td><?= $report['urban_m_ajs']; ?></td>
                <td><?= $report['urban_f_ajs']; ?></td>
                <td><?= $totalUrbanajs; ?></td>

                <td><?= $report['rural_m_ajs']; ?></td>
                <td><?= $report['rural_f_ajs']; ?></td>
                <td><?= $totalRuralajs; ?></td>

                <td><?= $totalMaleajs; ?></td>
                <td><?= $totalFemaleajs; ?></td>
                <td><strong><?= $grandTotalajs; ?></strong></td>
            </tr>
                                   <tr>
                <td class="text-left">
                    ግንዛቤ ማስጨባጫ የወሰዱ ወጣቶች
                </td>
                <td>በቁጥር</td>

                <td><?= $report['urban_m_ajs15_29']; ?></td>
                <td><?= $report['urban_f_ajs15_29']; ?></td>
                <td><?= $totalUrbanajs15_29; ?></td>

                <td><?= $report['rural_m_ajs15_29']; ?></td>
                <td><?= $report['rural_f_ajs15_29']; ?></td>
                <td><?= $totalRuralajs15_29; ?></td>

                <td><?= $totalMaleajs15_29; ?></td>
                <td><?= $totalFemaleajs15_29; ?></td>
                <td><strong><?= $grandTotalajs15_29; ?></strong></td>
            </tr>

            <tr>
                <td class="text-left">
                    የግንዛቤ ማስጨበጫ ስራ ፈላጊ ወላጆች
                </td>
                <td>በቁጥር</td>

                <td><?= $report['urban_m_parents']; ?></td>
                <td><?= $report['urban_f_parents']; ?></td>
                <td><?= $totalUrbanparents; ?></td>

                <td><?= $report['rural_m_parents']; ?></td>
                <td><?= $report['rural_f_parents']; ?></td>
                <td><?= $totalRuralparents; ?></td>

                <td><?= $totalMaleparents; ?></td>
                <td><?= $totalFemaleparents; ?></td>
                <td><strong><?= $grandTotalparents; ?></strong></td>
            </tr>
             <tr>
                <td class="text-left">
                    የግንዛቤ ማስጨበጫ ሌሎች ህብረተሰብ ክፍሎች
                </td>
                <td>በቁጥር</td>

                <td><?= $report['urban_m_others']; ?></td>
                <td><?= $report['urban_f_others']; ?></td>
                <td><?= $totalUrbanothers; ?></td>

                <td><?= $report['rural_m_others']; ?></td>
                <td><?= $report['rural_f_others']; ?></td>
                <td><?= $totalRuralothers; ?></td>

                <td><?= $totalMaleothers; ?></td>
                <td><?= $totalFemaleothers; ?></td>
                <td><strong><?= $grandTotalothers; ?></strong></td>
            </tr>
            <tr>
                <td class="text-left">
                    የግንዛቤ ማስጨበጫ የወሰዱ የዩኒቨርሲቲ ተመራቂዎች
                </td>
                <td>በቁጥር</td>

                <td><?= $report['urban_m_ajsuni']; ?></td>
                <td><?= $report['urban_f_ajsuni']; ?></td>
                <td><?= $totalUrbanajsuni; ?></td>

                <td><?= $report['rural_m_ajsuni']; ?></td>
                <td><?= $report['rural_f_ajsuni']; ?></td>
                <td><?= $totalRuralajsuni; ?></td>

                <td><?= $totalMaleajsuni; ?></td>
                <td><?= $totalFemaleajsuni; ?></td>
                <td><strong><?= $grandTotalajsuni; ?></strong></td>
            </tr>
            <tr>
                <td class="text-left">
                    የግንዛቤ ማስጨበጫ የወሰዱ የቴክኒክና ሙያ ኮሌጅ ተመራቂዎች
                </td>
                <td>በቁጥር</td>

                <td><?= $report['urban_m_ajstvt']; ?></td>
                <td><?= $report['urban_f_ajstvt']; ?></td>
                <td><?= $totalUrbanajstvt; ?></td>

                <td><?= $report['rural_m_ajstvt']; ?></td>
                <td><?= $report['rural_f_ajstvt']; ?></td>
                <td><?= $totalRuralajstvt; ?></td>

                <td><?= $totalMaleajstvt; ?></td>
                <td><?= $totalFemaleajstvt; ?></td>
                <td><strong><?= $grandTotalajstvt; ?></strong></td>
            </tr>
                <tr>
                <td class="text-left">
                    የግንዛቤ ማስጨበጫ የወሰዱ አካል ጉዳተኞች
                </td>
                <td>በቁጥር</td>

                <td><?= $report['urban_m_ajsdis']; ?></td>
                <td><?= $report['urban_f_ajsdis']; ?></td>
                <td><?= $totalUrbanajsdis; ?></td>

                <td><?= $report['rural_m_ajsdis']; ?></td>
                <td><?= $report['rural_f_ajsdis']; ?></td>
                <td><?= $totalRuralajsdis; ?></td>

                <td><?= $totalMaleajsdis; ?></td>
                <td><?= $totalFemaleajsdis; ?></td>
                <td><strong><?= $grandTotalajsdis; ?></strong></td>
            </tr>
            <tr>
                <td class="text-left">
                    የግንዛቤ ማስጨበጫ የወሰዱ ከስደት ተመላሾች
                </td>
                <td>በቁጥር</td>

                <td><?= $report['urban_m_ajsimmg']; ?></td>
                <td><?= $report['urban_f_ajsimmg']; ?></td>
                <td><?= $totalUrbanajsimmg; ?></td>

                <td><?= $report['rural_m_ajsimmg']; ?></td>
                <td><?= $report['rural_f_ajsimmg']; ?></td>
                <td><?= $totalRuralajsimmg; ?></td>

                <td><?= $totalMaleajsimmg; ?></td>
                <td><?= $totalFemaleajsimmg; ?></td>
                <td><strong><?= $grandTotalajsimmg; ?></strong></td>
            </tr>

                <tr>
                <td class="text-left">
                    የግንዛቤ ማስጨበጫ የወሰዱ መኖሪያቸው ጎዳና የሆኑ ዜጎች
                </td>
                <td>በቁጥር</td>

                <td><?= $report['urban_m_ajsnoh']; ?></td>
                <td><?= $report['urban_f_ajsnoh']; ?></td>
                <td><?= $totalUrbanajsnoh; ?></td>

                <td><?= $report['rural_m_ajsnoh']; ?></td>
                <td><?= $report['rural_f_ajsnoh']; ?></td>
                <td><?= $totalRuralajsnoh; ?></td>

                <td><?= $totalMaleajsnoh; ?></td>
                <td><?= $totalFemaleajsnoh; ?></td>
                <td><strong><?= $grandTotalajsnoh; ?></strong></td>
            </tr>
            <tr>
                <td class="text-left">
                    ለስራ ፈላጊዎች ተገቢውን የምክርና የመረጃ አገልግሎት መስጠት
                </td>
                <td>በቁጥር</td>

                <td><?= $report['urban_m_advice']; ?></td>
                <td><?= $report['urban_f_advice']; ?></td>
                <td><?= $totalUrbanadvice; ?></td>

                <td><?= $report['rural_m_advice']; ?></td>
                <td><?= $report['rural_f_advice']; ?></td>
                <td><?= $totalRuraladvice; ?></td>

                <td><?= $totalMaleadvice; ?></td>
                <td><?= $totalFemaleadvice; ?></td>
                <td><strong><?= $grandTotaladvice; ?></strong></td>
            </tr>
        </tbody>
    </table>
</div>
