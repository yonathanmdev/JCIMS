<?php
$selectedBranchName = $branchName ?? ($_POST['branch_name'] ?? ($_SESSION['user']['branch_name'] ?? 'አጠቃላይ ሪፖርት'));
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
    
    
];

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
?>


<center class="mb-4">
    <!-- የሪፖርት ርዕስ -->
    <h4 class="font-weight-bold">የስራ ፈላጊዎች ምዝገባ እና ግንዛቤ ፈጠራ (ሠ-1)</h4>
    
    <!-- 🟢 አዲስ የተጨመረ፦ የትኛው መዋቅር ሪፖርት እንደሆነ በግልጽ ያሳያል -->
    <h5 class="text-primary mt-2">የመዋቅር ደረጃ፦ <strong><?= htmlspecialchars($selectedBranchName); ?></strong></h5>
    
    <!-- 🟢 አዲስ የተጨመረ፦ የዛሬውን ቀን በዲናሚክ ይተካል -->
    <h6>የሪፖርት ቀን፦ <strong><?= date('d/m/Y'); ?></strong></h6>
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