<?php
use App\Helpers\EthiopianDateHelper; 
use App\Helpers\AuthHelper;
$fiscal_year = AuthHelper::checkFiscalYear();
 
$totalCount = 0;
$offset = 0;
$currentPage = 1;
$totalPages = 1;

if (!isset($title) || $title === '') {
    $title = 'Solgure List by Branch';
}

if (!isset($report) || !is_array($report)) {
    $report = [];
}

if (!isset($sectors) || !is_array($sectors)) {
    $sectors = [];
}

// 1. ደረጃን (Rank) በረድፍ አጠቃላይ ድምር ላይ ተመስርቶ ቀድሞ ማስላት
$zoneTotals = [];
foreach ($report as $zone => $sectorData) {
    $zoneSum = 0;
    foreach ($sectors as $s) {
        $m = $sectorData[$s]['male'] ?? 0;
        $f = $sectorData[$s]['female'] ?? 0;
        $zoneSum += ($m + $f);
    }
    $zoneTotals[$zone] = $zoneSum;
}

// ከፍተኛ ድምር ያላቸውን ወደ ላይ ለመደርደር ማደራጀት
arsort($zoneTotals);

// ደረጃዎችን መመደብ
$ranks = [];
$currentRank = 1;
$counter = 1;
$prevValue = -1;

foreach ($zoneTotals as $zone => $totalVal) {
    if ($totalVal !== $prevValue) {
        $currentRank = $counter;
    }
    $ranks[$zone] = $currentRank;
    $counter++;
    $prevValue = $totalVal;
}
?>
 
<section class="content">
  <div class="container-fluid">
    <div class="card-header bg-white d-flex flex-column flex-md-row align-items-md-center card-primary card-outline"></div>
    <div class="card-body">
        <div class="card-body">
            <h3><?= htmlspecialchars($title) ?></h3>
            <table id="example1" class="table table-bordered table-hover small text-center align-middle">
                <thead>
                    <!-- የመጀመሪያው የራስጌ ረድፍ (Row 1) -->
                    <tr>
                        <th rowspan="2" class="align-middle">ተ.ቁ</th>
                        <th rowspan="2" class="align-middle">የዞን ስም</th>
                        <?php foreach ($sectors as $s): ?>
                            <th colspan="3"><?= htmlspecialchars($s) ?></th>
                        <?php endforeach; ?>
                        <th colspan="3">ድምር</th>
                        <th rowspan="2" class="align-middle">ደረጃ</th>
                    </tr>
                    <!-- ሁለተኛው የራስጌ ረድፍ (Row 2 ለጾታ ስብጥር) -->
                    <tr>
                        <?php foreach ($sectors as $s): ?>
                            <th>ወ</th>
                            <th>ሴ</th>
                            <th>ድ</th>
                        <?php endforeach; ?>
                        <th>ወ</th>
                        <th>ሴ</th>
                        <th>ድ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; foreach ($report as $zone => $sectorData): ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td class="text-start"><?= htmlspecialchars($zone) ?></td>
                            
                            <?php 
                            $rowMaleSum = 0;
                            $rowFemaleSum = 0;
                            
                            foreach ($sectors as $s): 
                                $m = $sectorData[$s]['male'] ?? 0;
                                $f = $sectorData[$s]['female'] ?? 0;
                                $sectTotal = $m + $f;
                                
                                $rowMaleSum += $m;
                                $rowFemaleSum += $f;
                            ?>
                                <td><?= $m ?></td>
                                <td><?= $f ?></td>
                                <td class="bg-light"><strong><?= $sectTotal ?></strong></td>
                            <?php endforeach; ?>
                            
                            <!-- የረድፍ ጥቅል ድምር (Grand Totals per Row) -->
                            <td class="table-primary"><?= $rowMaleSum ?></td>
                            <td class="table-primary"><?= $rowFemaleSum ?></td>
                            <td class="table-success"><strong><?= $rowMaleSum + $rowFemaleSum ?></strong></td>
                            
                            <!-- የደረጃ ማሳያ -->
                            <td class="table-warning"><strong><?= $ranks[$zone] ?? '-' ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
     </div>
  </div>
</section>
<?php include 'partials/register-defense-modal.php'; ?>
<?php include 'partials/edit-defense-modal.php'; ?>