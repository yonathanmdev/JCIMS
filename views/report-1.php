<?php
$report = $report1 ?? [
    'urban_m_parents' => 0,
    'urban_f_parents' => 0,
    'rural_m_parents' => 0,
    'rural_f_parents' => 0,
];

$totalUrban = $report['urban_m_parents'] + $report['urban_f_parents'];
$totalRural = $report['rural_m_parents'] + $report['rural_f_parents'];
$totalMale  = $report['urban_m_parents'] + $report['rural_m_parents'];
$totalFemale = $report['urban_f_parents'] + $report['rural_f_parents'];
$grandTotal = $totalUrban + $totalRural;
?>

<div class="table-responsive">
    <table class="table table-bordered table-striped text-center">
        <thead class="table-primary">
            <tr>
                <th rowspan="2">Category</th>
                <th colspan="3">Urban</th>
                <th colspan="3">Rural</th>
                <th colspan="3">Grand Total</th>
            </tr>
            <tr>
                <th>Male</th>
                <th>Female</th>
                <th>Total</th>

                <th>Male</th>
                <th>Female</th>
                <th>Total</th>

                <th>Male</th>
                <th>Female</th>
                <th>Total</th>
            </tr>
        </thead>

        <tbody>
            <tr>
                <td class="text-left">
                    ለስራ ፈላጊ ወላጆች
                </td>

                <td><?= $report['urban_m_parents']; ?></td>
                <td><?= $report['urban_f_parents']; ?></td>
                <td><?= $totalUrban; ?></td>

                <td><?= $report['rural_m_parents']; ?></td>
                <td><?= $report['rural_f_parents']; ?></td>
                <td><?= $totalRural; ?></td>

                <td><?= $totalMale; ?></td>
                <td><?= $totalFemale; ?></td>
                <td><strong><?= $grandTotal; ?></strong></td>
            </tr>
        </tbody>
    </table>
</div>