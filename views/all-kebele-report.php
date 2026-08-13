<!DOCTYPE html>
<html lang="am">
<head>
    <meta charset="UTF-8">
    <title>የቀበሌዎች የሥራ ስምሪት ዝርዝር ሪፖርት</title>
    <!-- Bootstrap 4 CSS ለዲዛይን ውበት -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        /* A4 Landscape ገጽ ማስተካከያ እና የኅብረ ቀለም ፖሊሲ */
        @page {
            size: A4 landscape;
            margin: 10mm;
        }
        body {
            font-family: "Nyala", "Segoe UI", Tahoma, sans-serif;
            background-color: #fff;
            color: #000;
            font-size: 13px;
            margin: 0;
            padding: 0;
        }
        .a4-container {
            width: 297mm;
            min-height: 210mm;
            padding: 10mm;
            margin: auto;
            background: white;
        }
        .report-header {
            text-align: center;
            font-weight: bold;
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
        }
        th, td {
            border: 1px solid #000 !important;
            padding: 6px 4px;
            vertical-align: middle !important;
            font-size: 13px;
            word-wrap: break-word;
        }
        th {
            background-color: #f2f2f2 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .text-left-custom {
            text-align: left;
            padding-left: 8px;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                padding: 0;
            }
            .a4-container {
                width: 100%;
                padding: 0;
            }
        }
    </style>
</head>
<body>

    <div class="a4-container">
        <!-- ማተሚያ አዝራር -->
        <div class="row mb-3 no-print">
            <div class="col-md-12 text-right">
                <!--<button onclick="window.print()" class="btn btn-dark btn-sm">
                    <i class="fas fa-print"></i> ሪፖርቱን አትም (Print Landscape)
                </button>-->
            </div>
        </div>

        <!-- ሪፖርት ርዕስ -->
        <div class="report-header">
            <p class="mb-1" style="font-size: 16px;">በ <strong><?= htmlspecialchars($defaultBranchName ?? '') ?></strong> ወረዳ ሥር ያሉ ቀበሌዎች ዝርዝር ሪፖርት</p>
            <p style="font-size: 13px; font-weight: normal;">ማዕከላትንና ባለሙያ የተመደቡላቸው የገጠር ቀበሌዎች ሳይጨምር</p>
        </div>

        <!-- ዋናው ሰንጠረዥ -->
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th rowspan="2" style="width: 45px;">ተ.ቁ</th>
                        <th rowspan="2">የቀበሌው ስም</th>
                        <th rowspan="2" style="width: 100px;">የመዝገበው ሥራ ፈላጊ</th>
                        <th rowspan="2" style="width: 100px;">ግንዛቤ የተፈጠረለት</th>
                        <th colspan="3">የተፈጠረው ሥራ ዕድል</th>
                        <th colspan="4">የተመሠረተው ኢንተርፕራይዝ</th>
                    </tr>
                    <tr>
                        <th style="width: 70px;">ቋሚ</th>
                        <th style="width: 70px;">ጊዜያዊ</th>
                        <th style="width: 80px;">ድምር</th>
                        <th style="width: 80px;">ግብርና</th>
                        <th style="width: 80px;">ኢንዱስትሪ</th>
                        <th style="width: 80px;">አገልግሎት</th>
                        <th style="width: 80px;">ድምር</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($reports)): ?>
                        <?php foreach ($reports as $index => $row): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td class="text-left-custom"><?= htmlspecialchars($row->kebele_name) ?></td>
                                <td><?= $row->bookkeeping_seekers ?></td>
                                <td><?= $row->business_plan_seekers ?></td>
                                <td><?= $row->job_created_permanent ?></td>
                                <td><?= $row->job_created_temporary ?></td>
                                <td><?= $row->job_created_permanent + $row->job_created_temporary ?></td>
                                <td><?= $row->trained_agriculture ?></td>
                                <td><?= $row->trained_industry ?></td>
                                <td><?= $row->trained_service ?></td>
                                <td><?= $row->trained_agriculture + $row->trained_industry + $row->trained_service ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="11" class="text-center text-muted">ምንም መረጃ አልተገኘም</td>
                        </tr>
                    <?php endif; ?>

                    <!-- የድምር (Total) መስመር -->
                    <tr style="font-weight: bold; background-color: #f9f9f9 !important;">
                        <td colspan="2" class="text-center">ጠቅላላ (<?= $totals['kebele_count'] ?? 0 ?> ቀበሌዎች)</td>
                        <td><?= $totals['bookkeeping_seekers'] ?></td>
                        <td><?= $totals['business_plan_seekers'] ?></td>
                        <td><?= $totals['job_permanent_sum'] ?></td>
                        <td><?= $totals['job_temporary_sum'] ?></td>
                        <td><?= $totals['job_total_sum'] ?></td>
                        <td><?= $totals['trained_agri_sum'] ?></td>
                        <td><?= $totals['trained_ind_sum'] ?></td>
                        <td><?= $totals['trained_serv_sum'] ?></td>
                        <td><?= $totals['trained_total_sum'] ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>