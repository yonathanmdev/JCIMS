<?php
$report = $report1 ?? [
    'urban_m_parents' => 0,
    'urban_f_parents' => 0,
    'rural_m_parents' => 0,
    'rural_f_parents' => 0,
    'urban_m_others' => 0,
    'urban_f_others' => 0,
    'rural_m_others' => 0,
    'rural_f_others' => 0,
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
?>


<center><h5>የስራ ፈላጊዎች ምዝገባ እና ግንዛቤ ፈጠራ (ሠ-1)</h5>
<h6>የሪፖርት ቀን፦</h6></center>
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
        </tbody>
    </table>
</div>