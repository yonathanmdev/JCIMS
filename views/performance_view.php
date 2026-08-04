<!DOCTYPE html>
<html lang="am" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-color-scheme=light">
    <title>የቡድን መሪዎች እቅድና አፈጻጸም መከታተያ</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            font-family: 'Nyala', 'PowerGeesz', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f9;
            padding: 20px;
        }

        .report-card {
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            padding: 25px;
        }

        .table-report {
            border: 2px solid #343a40;
            vertical-align: middle;
            text-align: center;
            font-size: 0.90rem;
        }

        .table-report th {
            background-color: #f8f9fa;
            color: #111;
            font-weight: 700;
            border: 1px solid #444;
        }

        .table-report td {
            border: 1px solid #777;
            padding: 5px 8px;
        }

        .section-header {
            background-color: #e9ecef !important;
            font-size: 1rem;
            font-weight: bold;
        }

        .total-row {
            background-color: #dbe2ef !important;
            font-weight: bold;
            border-top: 2.5px solid #000 !important;
        }

        .rank-cell {
            font-weight: bold;
        }
        
        .zone-name {
            text-align: left !important;
            font-weight: 600;
            padding-left: 12px !important;
        }
    </style>
</head>
<body>

<div class="container-fluid report-card">
    
    <!-- ርዕሱ(Header Container) -->
    <div class="position-relative mb-4">
        <div class="text-center">
            <h5 class="fw-bold text-secondary mt-2">
                የ<?= htmlspecialchars($defaultBranchName ?? 'ቅርንጫፍ'); ?> የስራ ፈላጊዎች ምዝገባ እና የግንዛቤ ፈጠራ አፈጻጸም ሁኔታ
            </h5>
        </div>
    </div>

<div class="table-responsive">
    <table class="table table-bordered table-hover table-report align-middle">
        <thead>
            <!-- Main Header Row -->
            <tr>
                <th rowspan="2" style="width: 40px;">ተ.ቁ</th>
                <th rowspan="2" style="width: 180px;" class="text-start ps-3">የዞን / ከተማ አስተዳደር ስም</th>
                <th colspan="6" class="section-header">የተመዘገቡ የስራ ፈላጊ</th>
                <th colspan="6" class="section-header">ግንዛቤ የተፈጠረላቸው</th>
            </tr>
            <!-- Sub Header Row -->
            <tr>
                <!-- Registered Job Seekers (ከነደረጃው) -->
                <th>እቅድ</th>
                <th>ወንድ</th>
                <th>ሴት</th>
                <th>ድምር</th>
                <th>አፈጻጸም %</th>
                <th style="width: 75px;">ደረጃ</th>

                <!-- Awareness Created (ደረጃ የተወሰደበት) -->
                <th>እቅድ</th>
                <th>ወንድ</th>
                <th>ሴት</th>
                <th>ድምር</th>
                <th>አፈጻጸም %</th>
                <th style="width: 75px;">ደረጃ</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // የጠቅላላ ድምር መያዣዎች
            $tot_p_plan = $tot_p_m = $tot_p_f = $tot_p_sum = 0;
            $tot_a_plan = $tot_a_m = $tot_a_f = $tot_a_sum = 0;

            // 🌟 ለምዝገባ አፈጻጸም ደረጃ ማሳያ Helper Function
            function renderRankBadge($rank) {
                if ($rank == 1) {
                    return '<span class="badge bg-warning text-dark p-2 shadow-sm" style="font-size: 0.85rem; border-radius: 6px;">🥇 1ኛ</span>';
                } elseif ($rank == 2) {
                    return '<span class="badge bg-secondary text-white p-2 shadow-sm" style="font-size: 0.85rem; border-radius: 6px;">🥈 2ኛ</span>';
                } elseif ($rank == 3) {
                    return '<span class="badge text-white p-2 shadow-sm" style="font-size: 0.85rem; border-radius: 6px; background-color: #cd7f32;">🥉 3ኛ</span>';
                } elseif ($rank > 3) {
                    return '<span class="badge bg-light text-dark border px-2 py-1" style="font-size: 0.85rem;">' . $rank . '</span>';
                }
                return '-';
            }

            // 🌟 ለግንዛቤ አፈጻጸም ደረጃ ማሳያ Helper Function (አዲስ የተጨመረ)
            function renderAwarenessRankBadge($rank) {
                if ($rank == 1) {
                    return '<span class="badge bg-warning text-dark p-2 shadow-sm" style="font-size: 0.85rem; border-radius: 6px;">🥇 1ኛ</span>';
                } elseif ($rank == 2) {
                    return '<span class="badge bg-secondary text-white p-2 shadow-sm" style="font-size: 0.85rem; border-radius: 6px;">🥈 2ኛ</span>';
                } elseif ($rank == 3) {
                    return '<span class="badge text-white p-2 shadow-sm" style="font-size: 0.85rem; border-radius: 6px; background-color: #cd7f32;">🥉 3ኛ</span>';
                } elseif ($rank > 3) {
                    return '<span class="badge bg-light text-dark border px-2 py-1" style="font-size: 0.85rem;">' . $rank . '</span>';
                }
                return '-';
            }

            if (!empty($branches) && is_array($branches)):
                $index = 1;
                foreach ($branches as $row):
                    // ጠቅላላ ድምሮችን ማደራጀት
                    $tot_p_plan += $row['p_plan'] ?? 0;
                    $tot_p_m    += $row['p_m'] ?? 0;
                    $tot_p_f    += $row['p_f'] ?? 0;
                    $tot_p_sum  += $row['p_sum'] ?? 0;

                    $tot_a_plan += $row['a_plan'] ?? 0;
                    $tot_a_m    += $row['a_m'] ?? 0;
                    $tot_a_f    += $row['a_f'] ?? 0;
                    $tot_a_sum  += $row['a_sum'] ?? 0;
            ?>
            <tr>
                <td><?= $index++; ?></td>
                <td class="zone-name text-start ps-3 fw-bold"><?= htmlspecialchars($row['name'] ?? ''); ?></td>
                
                <!-- የተመዘገቡ የስራ ፈላጊዎች እና ደረጃቸው -->
                <td><?= number_format($row['p_plan'] ?? 0); ?></td>
                <td><?= number_format($row['p_m'] ?? 0); ?></td>
                <td><?= number_format($row['p_f'] ?? 0); ?></td>
                <td class="fw-bold"><?= number_format($row['p_sum'] ?? 0); ?></td>
                <td><?= number_format($row['p_per'] ?? 0, 2); ?>%</td>
                <td class="text-center">
                    <?= renderRankBadge($row['p_rank'] ?? ($row['rank_no'] ?? 0)); ?>
                </td>

                <!-- ግንዛቤ የተፈጠረላቸው እና ደረጃቸው (አዲስ የተጨመረው a_rank) -->
                <td><?= number_format($row['a_plan'] ?? 0); ?></td>
                <td><?= number_format($row['a_m'] ?? 0); ?></td>
                <td><?= number_format($row['a_f'] ?? 0); ?></td>
                <td class="fw-bold"><?= number_format($row['a_sum'] ?? 0); ?></td>
                <td><?= number_format($row['a_per'] ?? 0, 2); ?>%</td>
                <td class="text-center">
                    <?= renderAwarenessRankBadge($row['a_rank'] ?? 0); ?>
                </td>
            </tr>
            <?php 
                endforeach;
            else: 
            ?>
            <tr>
                <td colspan="14" class="text-center text-muted py-4">
                    <i class="fa-solid fa-info-circle me-1"></i> ምንም አይነት የቅርንጫፍ መረጃ አልተገኘም።
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
        <?php if (!empty($branches)): ?>
        <tfoot>
            <!-- Total Summary Row -->
            <tr class="total-row fw-bold bg-light">
                <td colspan="2" class="text-center">ጠቅላላ በ<?= count($branches); ?> ቅርንጫፎች</td>
                
                <!-- Reg Total -->
                <td><?= number_format($tot_p_plan); ?></td>
                <td><?= number_format($tot_p_m); ?></td>
                <td><?= number_format($tot_p_f); ?></td>
                <td class="fw-bold"><?= number_format($tot_p_sum); ?></td>
                <td><?= $tot_p_plan > 0 ? number_format(($tot_p_sum / $tot_p_plan) * 100, 2) . '%' : '-'; ?></td>
                <td>-</td>

                <!-- Awareness Total -->
                <td><?= number_format($tot_a_plan); ?></td>
                <td><?= number_format($tot_a_m); ?></td>
                <td><?= number_format($tot_a_f); ?></td>
                <td class="fw-bold"><?= number_format($tot_a_sum); ?></td>
                <td><?= $tot_a_plan > 0 ? number_format(($tot_a_sum / $tot_a_plan) * 100, 2) . '%' : '-'; ?></td>
                <td>-</td>
            </tr>
        </tfoot>
        <?php endif; ?>
    </table>
</div>
</div>

</body>
</html>