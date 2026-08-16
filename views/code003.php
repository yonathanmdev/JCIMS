<!DOCTYPE html>
<html lang="am">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Code 003</title>
<style>
  :root {
    --border: #d7dce3;
    --header-bg: #eef1f5;
    --header-bg-alt: #e3e8ef;
    --header-text: #2b3648;
    --row-alt: #f7f9fb;
    --sticky-shadow: 2px 0 4px rgba(0,0,0,0.08);
  }

  * { box-sizing: border-box; }

  /* A4 Landscape Print Setup */
  @page {
    size: A4 landscape;
    margin: 8mm;
  }

  body {
    margin: 0;
    padding: 15px;
    background: #f0f2f5;
    font-family: "Noto Sans Ethiopic", "Nyala", "Segoe UI", Tahoma, sans-serif;
    color: #1f2937;
  }

  .table-shell {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    overflow: hidden;
    border: 1px solid var(--border);
    max-width: 100%;
  }

  .table-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 12px 16px;
    border-bottom: 1px solid var(--border);
    flex-wrap: wrap;
    background: #fff;
  }

  .table-toolbar h1 {
    font-size: 15px;
    font-weight: 700;
    margin: 0;
    color: var(--header-text);
  }

  .toolbar-actions {
    display: flex;
    gap: 10px;
    align-items: center;
  }

  .print-btn {
    background: #3f6fb5;
    color: white;
    border: none;
    padding: 6px 14px;
    font-size: 12px;
    font-weight: 600;
    border-radius: 4px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 6px;
  }

  .print-btn:hover {
    background: #325a96;
  }

  .toolbar-hint {
    font-size: 12px;
    color: #6b7280;
    display: flex;
    align-items: center;
    gap: 6px;
  }

  .table-scroll {
    overflow-x: auto;
    overflow-y: auto;
    max-height: 78vh;
    -webkit-overflow-scrolling: touch;
  }

  table#myTable {
    border-collapse: separate;
    border-spacing: 0;
    width: max-content;
    min-width: 100%;
    font-size: 11px;
    line-height: 1.35;
  }

  #myTable th,
  #myTable td {
    border-right: 1px solid var(--border);
    border-bottom: 1px solid var(--border);
    padding: 6px 8px;
    text-align: center;
    vertical-align: middle;
    white-space: normal;
    word-break: break-word;
  }

  #myTable thead th {
    background: var(--header-bg);
    color: var(--header-text);
    font-weight: 600;
    position: sticky;
    top: 0;
    z-index: 3;
  }

  #myTable thead tr:nth-child(1) th { top: 0; }
  #myTable thead tr:nth-child(2) th { background: var(--header-bg-alt); }
  #myTable thead tr:nth-child(3) th { background: var(--header-bg); }
  #myTable thead tr:nth-child(4) th { background: var(--header-bg-alt); }

  /* Sticky first two columns (row number + enterprise name) */
  #myTable th:first-child,
  #myTable td:first-child {
    position: sticky;
    left: 0;
    z-index: 2;
    background: #fff;
    box-shadow: var(--sticky-shadow);
    width: 45px;
  }

  #myTable th:nth-child(2),
  #myTable td:nth-child(2) {
    position: sticky;
    left: 45px;
    z-index: 2;
    background: #fff;
    box-shadow: var(--sticky-shadow);
    text-align: right;
    width: 180px;
  }

  #myTable thead th:first-child,
  #myTable thead th:nth-child(2) {
    z-index: 4;
    background: var(--header-bg);
  }

  #myTable tbody tr:nth-child(even) td:not(:first-child):not(:nth-child(2)) {
    background: var(--row-alt);
  }

  #myTable tbody tr:nth-child(even) td:first-child,
  #myTable tbody tr:nth-child(even) td:nth-child(2) {
    background: #fbfcfd;
  }

  #myTable tbody tr:hover td {
    background: #eef4ff !important;
  }

  #myTable tbody td {
    color: #374151;
    min-height: 32px;
  }

  .empty-state td {
    padding: 28px 12px;
    color: #9ca3af;
    font-size: 12px;
  }

  /* scrollbar styling */
  .table-scroll::-webkit-scrollbar { height: 8px; width: 8px; }
  .table-scroll::-webkit-scrollbar-track { background: #eef1f5; }
  .table-scroll::-webkit-scrollbar-thumb { background: #b9c2cf; border-radius: 4px; }

  /* Print Specific Rules */
  @media print {
    body {
      padding: 0;
      background: #fff;
    }
    .table-shell {
      border: none;
      box-shadow: none;
    }
    .table-toolbar {
      display: none !important;
    }
    .table-scroll {
      max-height: none;
      overflow: visible;
    }
    #myTable th:first-child,
    #myTable td:first-child,
    #myTable th:nth-child(2),
    #myTable td:nth-child(2) {
      position: static;
      box-shadow: none;
    }
  }
</style>
</head>
<body>

<div class="table-shell">
  <div class="table-toolbar">
    <h1><center>በ<b><?= htmlspecialchars($myBranchName ?? 'የለም') ?></b> ተመዝግበው የሚገኙ ኢ/ዞች ብዛት መስጫ ቅፅ አዲስ ኢንተርፕራይዞች ኮድ 003</center></h1>
    <div class="toolbar-actions">
<button id="exportExcelBtn" class="print-btn" style="background: #217346; color: white; border: none; padding: 6px 14px; font-size: 12px; font-weight: 600; border-radius: 4px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
        ወደ Excel አውርድ
      </button>
      <div class="toolbar-hint">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>
        ወደ ቀኝ/ግራ ይንሸራተቱ
      </div>
    </div>
  </div>

  <div class="table-scroll">
    <table border="1" id="myTable">
      <thead>
        <tr>
          <th rowspan="3">ተራ ቁጥር</th>
          <th rowspan="3" align="left">የኢንተርፕራይዙ ስም</th>
          <th colspan="6">አድራሻ</th>
          <th rowspan="3">የተመሰረተበት ዘመን (ዓ/ም)</th>
          <th rowspan="3">የተሰማራበት የስራ መስክ</th>
          <th rowspan="3">የተሰማራበት ዘርፍ (አገልግሎት፣ ግብርና፣ ኢንዱስትሪ)</th>
          <th rowspan="3">የኢ/ዙ አይነት በደረጃ(ጥቃቅን፣ አነስተኛ)</th>
          <th rowspan="3">የአደረጃጀት አይነት (በግል/በንግድ ማህበር/በህ/ስ/ማ)</th>
          <th rowspan="3">የግብር ከፋይነት መለያ ቁጥር</th>
          <th rowspan="3">የዕድገት ደረጃ (ጀማሪ/ታዳጊ/መብቃት)</th>
          <th colspan="2">መነሻ ጠቅላላ ሃብት መጠንና ምንጩ</th>
          <th rowspan="3">ወቅታዊ ጠቅላላ ሃብት መጠን</th>
          <th colspan="3">ሲቋቋም የነበረ የሰው ሃይል</th>
          <th colspan="13">ወቅታዊ የአባላት ብዛት</th>
          <th colspan="6">ከአባላት ውጭ የተፈጠረ የስራ እድል</th>
          <th colspan="2">የኢንተርፕራይዙ ምርትና አገልግሎት</th>
        </tr>
        <tr>
          <th rowspan="2">ዞን</th>
          <th rowspan="2">ወረዳ</th>
          <th rowspan="2">ከተማ</th>
          <th rowspan="2">ቀበሌ</th>
          <th rowspan="2">የቤት ቁጥር</th>
          <th rowspan="2">ስልክ ቁጥር</th>
          <th rowspan="2">መነሻ ጠቅላላ ሃብት መጠን</th>
          <th rowspan="2">ምንጭ (ከራስ ተቀማጭ፣ ከቤተሰብ ብድር)</th>
          <th rowspan="2">ወንድ</th>
          <th rowspan="2">ሴት</th>
          <th rowspan="2">ድምር</th>
          <th colspan="3">ፆታ</th>
          <th colspan="5">በዕድሜ</th>
          <th colspan="5">በትምህርት ደረጃ</th>
          <th colspan="3">ቋሚ</th>
          <th colspan="3">ጊዚያዊ</th>
          <th rowspan="2">የምርቱ ዓይነት</th>
          <th rowspan="2">የሚቀርብበት ገበያ /ለሃገር ወይስ ለውጭ</th>
        </tr>
        <tr>
          <th>ወ</th>
          <th>ሴ</th>
          <th>ድ</th>
          <th>15-29</th>
          <th>30-49</th>
          <th>50-65</th>
          <th>&gt;65</th>
          <th>ድምር</th>
          <th>ማንበብና መፃፍ የማይችሉ /መሰረተ ትምህርት</th>
          <th>አንደኛ ደረጃ (1-8)*</th>
          <th>ሁለተኛ ደረጃ (9-12)**</th>
          <th>ኮሌጅ (ዩኒቨርሲቲ) ያጠናቀቁ</th>
          <th>ድምር</th>
          <th>ወ</th>
          <th>ሴ</th>
          <th>ድ</th>
          <th>ወ</th>
          <th>ሴ</th>
          <th>ድ</th>
        </tr>

      </thead>
      <tbody>
        <?php if (!empty($reports)): ?>
          <?php foreach ($reports as $index => $row): ?>
            <tr>
              <td><?= $index + 1 ?></td>
              <td><?= htmlspecialchars($row->enterprisename ?? '') ?></td>
              <!-- አድራሻ (አስፈላጊውን ከቴብሉ ማስተካከል ይቻላል) -->
              <td>-</td>
              <td>-</td>
              <td>-</td>
              <td><?= htmlspecialchars($row->jskebele ?? '') ?></td>
              <td>-</td>
              <td><?= htmlspecialchars($row->manager_phone ?? '') ?></td>
              <td><?= htmlspecialchars($row->established_date ?? '') ?></td>
              <td><?= htmlspecialchars($row->sub_sector_name ?? '') ?></td>
              <td><?= htmlspecialchars($row->sector_name ?? '') ?></td>
<td>
  <?php 
    $val = trim($row->yeedget_dereja ?? '');
    $text = match($val) {
        '0' => 'ጥቃቅን ጀማሪ',
        '1' => 'ጥቃቅን ታዳጊ',
        '2' => 'ጥቃቅን የበቃ',
        '3' => 'አነስተኛ ጀማሪ',
        '4' => 'አነስተኛ ታዳጊ',
        '5' => 'አነስተኛ የበቃ',
        default => $val
    };
    echo htmlspecialchars($text);
  ?>
</td>
              <td><?= htmlspecialchars($row->project_type_or_aderejajet ?? '') ?></td>
              <td><?= htmlspecialchars($row->tine_number ?? '') ?></td>
<td>
  <?php 
    $val = trim($row->yeedget_dereja ?? '');
    $text = match($val) {
        '0' => 'ጥቃቅን ጀማሪ',
        '1' => 'ጥቃቅን ታዳጊ',
        '2' => 'ጥቃቅን የበቃ',
        '3' => 'አነስተኛ ጀማሪ',
        '4' => 'አነስተኛ ታዳጊ',
        '5' => 'አነስተኛ የበቃ',
        default => $val
    };
    echo htmlspecialchars($text);
  ?>
</td>
              <td><?= htmlspecialchars($row->initial_capital ?? '') ?></td>
              <td>
  <?php 
    $mnchVal = trim($row->yehabtu_mnch ?? '');
    $mnchText = match($mnchVal) {
        '0' => 'በራስ ተቀማጭ',
        '1' => 'ከቤተሰብ',
        '2' => 'ከመንግስት',
        '3' => 'ከብድር',
        default => $mnchVal
    };
    echo htmlspecialchars($mnchText);
  ?>
</td>
              <td><?= htmlspecialchars($row->wektawi_yehabt_meten ?? '') ?></td>
              <!-- ሲቋቋም የነበረ የሰው ሃይል (ወንድ፣ ሴት፣ ድምር) -->
<td><?= htmlspecialchars($row->initial_male ?? '0') ?></td>
<td><?= htmlspecialchars($row->initial_female ?? '0') ?></td>
<td><?= htmlspecialchars($row->initial_total ?? '0') ?></td>
              <!-- ወቅታዊ የአባላት ብዛት -->
<!-- ከሞዴል የተደመረው የወንድ አባላት ብዛት -->
<td><?= htmlspecialchars($row->initial_male ?? '0') ?></td>

<!-- ከሞዴል የተደመረው የሴት አባላት ብዛት -->
<td><?= htmlspecialchars($row->initial_female ?? '0') ?></td>

<!-- የሁለቱም ድምር -->
<td><?= htmlspecialchars($row->initial_total ?? '0') ?></td>
              <!-- በዕድሜ ክልል የተከፋፈለ የሰው ሃይል -->
<td><?= htmlspecialchars($row->age_15_29 ?? '0') ?></td>
<td><?= htmlspecialchars($row->age_30_49 ?? '0') ?></td>
<td><?= htmlspecialchars($row->age_50_65 ?? '0') ?></td>
<td><?= htmlspecialchars($row->age_above_65 ?? '0') ?></td>
<td><?= htmlspecialchars($row->total_members ?? '0') ?></td>
<!-- በትምህርት ደረጃ የተከፋፈለ የሰው ሃይል -->
<td><?= htmlspecialchars($row->edu_basic ?? '0') ?></td>
<td><?= htmlspecialchars($row->edu_1_8 ?? '0') ?></td>
<td><?= htmlspecialchars($row->edu_9_12 ?? '0') ?></td>
<td><?= htmlspecialchars($row->edu_degree ?? '0') ?></td>
<td><?= htmlspecialchars($row->total_edu_members ?? '0') ?></td>
              <!-- ቋሚ የሥራ ዕድል -->
<td><?= htmlspecialchars($row->permanent_male ?? '0') ?></td>
<td><?= htmlspecialchars($row->permanent_female ?? '0') ?></td>
<td><?= htmlspecialchars($row->permanent_total ?? '0') ?></td>

<!-- ጊዜያዊ የሥራ ዕድል -->
<td><?= htmlspecialchars($row->temporary_male ?? '0') ?></td>
<td><?= htmlspecialchars($row->temporary_female ?? '0') ?></td>
<td><?= htmlspecialchars($row->temporary_total ?? '0') ?></td>
              <td><?= htmlspecialchars($row->yemrt_ayinet ?? '') ?></td>
              <td><?= htmlspecialchars($row->yemikerb_hager_weys_lewuch ?? '') ?></td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr class="empty-state">
            <td colspan="39">መረጃ አልተገኘም</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<script nonce="<?php echo $GLOBALS['nonce'] ?? ''; ?>">
document.addEventListener('DOMContentLoaded', function() {
    var exportBtn = document.getElementById('exportExcelBtn');
    if (exportBtn) {
        exportBtn.addEventListener('click', function() {
            var tableID = 'myTable';
            var filename = 'Code_003_Report.xls';
            var dataType = 'application/vnd.ms-excel;charset=utf-8';
            var tableSelect = document.getElementById(tableID);
            
            if (!tableSelect) {
                alert('ሰንጠረዡ አልተገኘም!');
                return;
            }

            var tableHTML = tableSelect.outerHTML;
            var downloadLink = document.createElement("a");
            document.body.appendChild(downloadLink);
            
            var blob = new Blob(['\ufeff', tableHTML], {
                type: dataType
            });
            var url = URL.createObjectURL(blob);
            downloadLink.href = url;
            downloadLink.download = filename;
            downloadLink.click();
            URL.revokeObjectURL(url);
            document.body.removeChild(downloadLink);
        });
    }
});
</script>
</body>
</html>