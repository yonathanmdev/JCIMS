<!DOCTYPE html>
<html lang="am">
<head>
    <meta charset="UTF-8">
    <title><?php echo $title ?? 'የማጠቃለያ ሪፖርት'; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
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
</head>
<body>

<div class="container-fluid">
    <div class="report-header">
        <h4 class="font-weight-bold">የስራ ፈላጊዎች ምዝገባ እና ግንዛቤ ፈጠራ</h4>
        <div class="d-flex justify-content-center align-items-center mt-2">
            <span class="mr-3">የሪፖርት ቀን፦ <input type="text" value="<?php echo date('d/m/Y'); ?>" style="border: 1px solid #ccc; text-align: center; width: 120px; font-size: 12px;"></span>
            <span>ዓ.ም ፦ <strong class="ml-1"><?php echo htmlspecialchars($year ?? ''); ?></strong></span>
        </div>
    </div>

    <div class="text-right mb-2 no-print">
        <button class="btn btn-sm btn-primary" onclick="window.print()"><i class="fas fa-print"></i> አትም / Print</button>
    </div>

    <table class="table table-bordered text-center">
        <thead>
            <tr>
                <th rowspan="3" style="width: 10%;">ስትራቴጂያዊ ግቦች</th>
                <th rowspan="3" style="width: 22%;">አመልካቾች</th>
                <th rowspan="3" style="width: 4%;">መለኪያ</th>
                <th colspan="9">ዕቅድ (የዓመቱ)</th>
                <th colspan="9">ክንውን (አፈጻጸም)</th>
                <th rowspan="3" style="width: 5%;">አፈጻጸም<br>(%)</th>
            </tr>
            <tr>
                <th colspan="3">ከተማ</th>
                <th colspan="3">ገጠር</th>
                <th colspan="3">ከተማና ገጠር</th>
                
                <th colspan="3">ከተማ</th>
                <th colspan="3">ገጠር</th>
                <th colspan="3">ከተማና ገጠር</th>
            </tr>
            <tr>
                <th>ወንድ</th> <th>ሴት</th> <th>ድምር</th>
                <th>ወንድ</th> <th>ሴት</th> <th>ድምር</th>
                <th>ወንድ</th> <th>ሴት</th> <th>ድምር</th>
                
                <th>ወንድ</th> <th>ሴት</th> <th>ድምር</th>
                <th>ወንድ</th> <th>ሴት</th> <th>ድምር</th>
                <th>ወንድ</th> <th>ሴት</th> <th>ድምር</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $rows = [
                ['title' => 'አጠቃላይ የተመዘገቡ የስራ ፈላጊዎች ቁጥር', 'prefix' => 'reg'],
                ['title' => 'በዕድሜ (15-29 ዓመት)', 'prefix' => 'age15_29'],
                ['title' => 'በዕድሜ (30-64)', 'prefix' => 'age30_64'],
                ['title' => 'የተመዘገቡ የዩኒቨርሲቲ ተመራቂዎች ብዛት', 'prefix' => 'uni'],
                ['title' => 'የተመዘገቡ የቴክኒክና ሙያ ተመራቂዎች ብዛት', 'prefix' => 'tvet'],
                ['title' => 'የተመዘገቡ አካል ጉዳተኞች', 'prefix' => 'dis'],
                ['title' => 'የተመዘገቡ ከስደት ተመላሽ ዜጎች', 'prefix' => 'returnee'],
                ['title' => 'የተመዘገቡ የሀገር ውስጥ ተፈናቃዮች', 'prefix' => 'idp'],
                ['title' => 'የተመዘገቡ መኖሪያቸው ጎዳና የሆኑ ዜጎች', 'prefix' => 'homeless'],
                ['title' => 'የግንዛቤ ማስጨበጫ የተሰጣቸው ስራ ፈላጊዎች', 'prefix' => 'awr'],
                ['title' => 'የግንዛቤ ማስጨበጫ የተሰጣቸው ወጣቶች ስራ ፈላጊዎች', 'prefix' => 'awr_youth'],
                ['title' => 'የግንዛቤ ማስጨበጫ ለሌሎች ህብረተሰብ ክፍሎች', 'prefix' => 'others'],
                ['title' => 'የግንዛቤ ማስጨበጫ ስራ ፈላጊ ወላጆች', 'prefix' => 'parents'],
                ['title' => 'የግንዛቤ ማስጨበጫ የተሰጣቸው የዩኒቨርሲቲ ተመራቂዎች', 'prefix' => 'awr_uni'],
                ['title' => 'የግንዛቤ ማስጨበጫ የተሰጣቸው የቴክኒክና ሙያ ተመራቂዎች', 'prefix' => 'awr_tvet'],
                ['title' => 'የግንዛቤ ማስጨበጫ የተሰጣቸው አካል ጉዳተኞች', 'prefix' => 'awr_dis'],
                ['title' => 'የግንዛቤ ማስጨበጫ የተሰጣቸው ከስደት ተመላሾች', 'prefix' => 'awr_ret'],
                ['title' => 'የግንዛቤ ማስጨበጫ የተሰጣቸው የሀገር ውስጥ ተፈናቃዮች', 'prefix' => 'awr_idp'],
                ['title' => 'የግንዛቤ ማስጨበጫ የተሰጣቸው መኖሪያቸው ጎዳና የሆኑ ዜጎች', 'prefix' => 'awr_homeless'],
                ['title' => 'ለስራ ፈላጊዎች ተገቢውን የምክርና የመረጃ አገልግሎት ድጋፍ መስጠት', 'prefix' => 'advice']
            ];

            $data = $reportData ?? [];

            foreach ($rows as $index => $row):
                $p = $row['prefix'];
                
                // 1. የክንውን (አፈጻጸም) እሴቶችን ከዳታቤዝ ማንበብ
                $u_m = is_object($data) ? ($data->{"urban_m_$p"} ?? 0) : ($data["urban_m_$p"] ?? 0);
                $u_f = is_object($data) ? ($data->{"urban_f_$p"} ?? 0) : ($data["urban_f_$p"] ?? 0);
                $r_m = is_object($data) ? ($data->{"rural_m_$p"} ?? 0) : ($data["rural_m_$p"] ?? 0);
                $r_f = is_object($data) ? ($data->{"rural_f_$p"} ?? 0) : ($data["rural_f_$p"] ?? 0);

                // የክንውን ድምር ስሌቶች
                $u_t   = $u_m + $u_f; 
                $r_t   = $r_m + $r_f; 
                $tot_m = $u_m + $r_m; 
                $tot_f = $u_f + $r_f; 
                $tot_t = $u_t + $r_t; 

                // 2. የዕቅድ እሴቶች (ለጊዜው 0 ተደርገዋል)
                $p_u_m = 0; $p_u_f = 0; $p_u_t = $p_u_m + $p_u_f;
                $p_r_m = 0; $p_r_f = 0; $p_r_t = $p_r_m + $p_r_f;
                $p_t_m = $p_u_m + $p_r_m; $p_t_f = $p_u_f + $p_r_f; $p_t_all = $p_u_t + $p_r_t;

                // 3. የአፈጻጸም ፐርሰንት (%) ስሌት ማስተካከያ (እቅድ ከሌለ 0% እንዳያሳይ ባዶ እናደርገዋለን)
                $perf_pct = $p_t_all > 0 ? number_format(($tot_t / $p_t_all) * 100, 1) . '%' : '';
            ?>
                <tr>
                    <?php if ($index === 0): ?>
                        <td rowspan="20" class="font-weight-bold" style="background-color: #fff;">
                            ዘላቂ የሥራ ዕድል በመፍጠር የዜጎችን ተጠቃሚነት ማረጋገጥ
                        </td>
                    <?php endif; ?>
                    
                    <td class="text-left"><?php echo htmlspecialchars($row['title']); ?></td>
                    <td>በቁጥር</td>
                    
                    <td><?php echo $p_u_m ?: ''; ?></td> <td><?php echo $p_u_f ?: ''; ?></td> <td class="font-weight-bold"><?php echo $p_u_t ?: ''; ?></td>
                    <td><?php echo $p_r_m ?: ''; ?></td> <td><?php echo $p_r_f ?: ''; ?></td> <td class="font-weight-bold"><?php echo $p_r_t ?: ''; ?></td>
                    <td><?php echo $p_t_m ?: ''; ?></td> <td><?php echo $p_t_f ?: ''; ?></td> <td class="font-weight-bold"><?php echo $p_t_all ?: ''; ?></td>
                    
                    <td><?php echo $u_m ? number_format($u_m) : ''; ?></td>
                    <td><?php echo $u_f ? number_format($u_f) : ''; ?></td>
                    <td class="font-weight-bold" style="background-color: #f8f9fa;"><?php echo $u_t ? number_format($u_t) : ''; ?></td>
                    
                    <td><?php echo $r_m ? number_format($r_m) : ''; ?></td>
                    <td><?php echo $r_f ? number_format($r_f) : ''; ?></td>
                    <td class="font-weight-bold" style="background-color: #f8f9fa;"><?php echo $r_t ? number_format($r_t) : ''; ?></td>
                    
                    <td><?php echo $tot_m ? number_format($tot_m) : ''; ?></td>
                    <td><?php echo $tot_f ? number_format($tot_f) : ''; ?></td>
                    <td class="font-weight-bold text-success" style="background-color: #e9ecef;"><?php echo $tot_t ? number_format($tot_t) : ''; ?></td>
                    
                    <td class="font-weight-bold"><?php echo $perf_pct; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>