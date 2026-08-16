<!DOCTYPE html>
<html lang="am">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>የኢንተርፕራይዝ ሪፖርት ሰንጠረዥ</title>
<style>
  :root {
    --border: #d7dce3;
    --header-bg: #eef1f5;
    --header-bg-alt: #e3e8ef;
    --header-text: #2b3648;
    --row-alt: #f7f9fb;
    --sticky-shadow: 2px 0 5px rgba(0,0,0,0.12);
  }

  * { box-sizing: border-box; }

  body {
    margin: 0;
    padding: 20px;
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
  }

  .table-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 16px;
    border-bottom: 1px solid var(--border);
  }

  .table-toolbar h1 {
    font-size: 15px;
    font-weight: 700;
    margin: 0;
    color: var(--header-text);
  }

  .toolbar-hint {
    font-size: 12px;
    color: #6b7280;
  }

  .table-scroll {
    overflow-x: auto;
    overflow-y: auto;
    max-height: 78vh;
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
    padding: 8px 6px;
    text-align: center;
    vertical-align: middle;
  }

  #myTable thead th {
    background: var(--header-bg);
    color: var(--header-text);
    font-weight: 600;
    position: sticky;
    top: 0;
    z-index: 3;
  }

  /* ቁልቁል የሚነበቡ ረጅም አርእስቶች */
  .vertical-text {
    writing-mode: vertical-rl;
    transform: rotate(180deg);
    white-space: nowrap;
    padding: 8px 2px !important;
    max-height: 170px;
    margin: 0 auto;
  }

  /* ========================================================= */
  /*  STUCK / FIXED COLUMNS (ተራ ቁጥር እና የኢንተርፕራይዙ ስም)            */
  /* ========================================================= */

  /* 1ኛ ዓምድ: ተራ ቁጥር (ስፋት: 50px, left: 0px) */
  #myTable th:nth-child(1),
  #myTable td:nth-child(1) {
    position: sticky;
    left: 0 !important;
    width: 50px !important;
    min-width: 50px !important;
    max-width: 50px !important;
    z-index: 5;
    background: #fff;
  }

  /* 2ኛ ዓምድ: የኢንተርፕራይዙ ስም (ስፋት: 180px, left: 50px) */
  #myTable th:nth-child(2),
  #myTable td:nth-child(2) {
    position: sticky;
    left: 50px !important;
    width: 180px !important;
    min-width: 180px !important;
    max-width: 180px !important;
    z-index: 5;
    background: #fff;
    text-align: right;
    box-shadow: var(--sticky-shadow);
  }

  #myTable thead th:nth-child(1),
  #myTable thead th:nth-child(2) {
    z-index: 10;
    background: var(--header-bg);
  }

  #myTable tbody tr:nth-child(even) td:nth-child(1),
  #myTable tbody tr:nth-child(even) td:nth-child(2) {
    background: #f7f9fb;
  }

  #myTable tbody tr:hover td {
    background: #eef4ff !important;
  }

  .col-narrow { min-width: 38px; }
  .col-medium { min-width: 85px; }
</style>
</head>
<body>

<div class="table-shell">
  <div class="table-toolbar">
    <h1>የኢንተርፕራይዝ ሪፖርት ሰንጠረዥ</h1>
    <div class="toolbar-hint">ወደ ቀኝ/ግራ ይንሸራተቱ</div>
  </div>

  <div class="table-scroll">
    <table id="myTable">
      <thead>
        <tr>
          <th rowspan="4">ተራ ቁጥር</th>
          <th rowspan="4">የኢንተርፕራይዙ ስም</th>
          <th colspan="6">አድራሻ</th>
          <th rowspan="3"><div class="vertical-text">የተመሰረተበት ዘመን (ዓ/ም)</div></th>
          <th rowspan="3"><div class="vertical-text">የተሰማራበት የስራ መስክ</div></th>
          <th rowspan="3"><div class="vertical-text">የተሰማራበት ዘርፍ</div></th>
          <th rowspan="3"><div class="vertical-text">የኢ/ዙ አይነት በትርጓሜ</div></th>
          <th rowspan="3"><div class="vertical-text">የአደረጃጀት አይነት</div></th>
          <th rowspan="3"><div class="vertical-text">የግብር ከፋይነት መለያ ቁጥር</div></th>
          <th rowspan="3"><div class="vertical-text">የዕድገት ደረጃ</div></th>
          <th colspan="2">መነሻ ጠቅላላ ሃብት መጠንና ምንጩ</th>
          <th rowspan="3"><div class="vertical-text">ወቅታዊ ጠቅላላ ሃብት መጠን</div></th>
          <th colspan="3">ሲቋቋም የነበረ የሰው ሃይል</th>
          <th colspan="13">ወቅታዊ የአባላት ብዛት</th>
          <th colspan="6">ከአባላት ውጭ የተፈጠረ የስራ እድል</th>
          <th colspan="2">የኢንተርፕራይዙ ምርትና አገልግሎት</th>
        </tr>
        <tr>
          <th rowspan="2" class="col-medium">ዞን</th>
          <th rowspan="2" class="col-medium">ወረዳ</th>
          <th rowspan="2" class="col-medium">ከተማ</th>
          <th rowspan="2" class="col-medium">ቀበሌ</th>
          <th rowspan="2" class="col-medium">የቤት ቁጥር</th>
          <th rowspan="2" class="col-medium">ስልክ ቁጥር</th>
          <th rowspan="2">መነሻ ጠቅላላ ሃብት መጠን</th>
          <th rowspan="2">ምንጭ</th>
          <th rowspan="2" class="col-narrow">ወንድ</th>
          <th rowspan="2" class="col-narrow">ሴት</th>
          <th rowspan="2" class="col-narrow">ድምር</th>
          <th colspan="3">ፆታ</th>
          <th colspan="5">በዕድሜ</th>
          <th colspan="5">በትምህርት ደረጃ</th>
          <th colspan="3">ቋሚ</th>
          <th colspan="3">ጊዚያዊ</th>
          <th rowspan="2">የምርቱ ዓይነት</th>
          <th rowspan="2">የሚቀርብበት ገበያ</th>
        </tr>
        <tr>
          <th class="col-narrow">ወ</th>
          <th class="col-narrow">ሴ</th>
          <th class="col-narrow">ድ</th>
          <th class="col-narrow">15-29</th>
          <th class="col-narrow">30-49</th>
          <th class="col-narrow">50-65</th>
          <th class="col-narrow">&gt;65</th>
          <th class="col-narrow">ድምር</th>
          <th><div class="vertical-text">መሰረተ ትምህርት</div></th>
          <th><div class="vertical-text">አንደኛ ደረጃ (1-8)</div></th>
          <th><div class="vertical-text">ሁለተኛ ደረጃ (9-12)</div></th>
          <th><div class="vertical-text">ኮሌጅ/ዩኒቨርሲቲ</div></th>
          <th class="col-narrow">ድምር</th>
          <th class="col-narrow">ወ</th>
          <th class="col-narrow">ሴ</th>
          <th class="col-narrow">ድ</th>
          <th class="col-narrow">ወ</th>
          <th class="col-narrow">ሴ</th>
          <th class="col-narrow">ድ</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($enterprises)): ?>
            <?php foreach ($enterprises as $index => $ent): ?>
                <tr>
                    <!-- 1. ተራ ቁጥር -->
                    <td><?= $index + 1 ?></td>
                    
                    <!-- 2. የኢንተርፕራይዙ ስም -->
                    <td style="text-align: right;"><?= htmlspecialchars($ent['enterprise_name'] ?? '') ?></td>
                    
                    <!-- አድራሻ -->
                    <td><?= htmlspecialchars($ent['zone'] ?? '') ?></td>
                    <td><?= htmlspecialchars($ent['woreda'] ?? '') ?></td>
                    <td><?= htmlspecialchars($ent['city'] ?? '') ?></td>
                    <td><?= htmlspecialchars($ent['kebele'] ?? '') ?></td>
                    <td><?= htmlspecialchars($ent['house_no'] ?? '') ?></td>
                    <td><?= htmlspecialchars($ent['phone'] ?? '') ?></td>
                    
                    <!-- የተመሰረተበት ዘመን -->
                    <td><?= htmlspecialchars($ent['established_year'] ?? '') ?></td>
                    
                    <!-- የተሰማራበት የስራ መስክ እና ዘርፍ -->
                    <td><?= htmlspecialchars($ent['business_field'] ?? '') ?></td>
                    <td><?= htmlspecialchars($ent['sector'] ?? '') ?></td>
                    
                    <!-- የኢ/ዙ አይነት እና አደረጃጀት -->
                    <td><?= htmlspecialchars($ent['enterprise_type'] ?? '') ?></td>
                    <td><?= htmlspecialchars($ent['organization_type'] ?? '') ?></td>
                    
                    <!-- የግብር ከፋይ ቁጥር እና የዕድገት ደረጃ -->
                    <td><?= htmlspecialchars($ent['tin_number'] ?? '') ?></td>
                    <td><?= htmlspecialchars($ent['growth_stage'] ?? '') ?></td>
                    
                    <!-- መነሻ ጠቅላላ ሃብት መጠንና ምንጩ -->
                    <td><?= htmlspecialchars($ent['initial_capital'] ?? '') ?></td>
                    <td><?= htmlspecialchars($ent['capital_source'] ?? '') ?></td>
                    
                    <!-- ወቅታዊ ጠቅላላ ሃብት መጠን -->
                    <td><?= htmlspecialchars($ent['current_capital'] ?? '') ?></td>
                    
                    <!-- ሲቋቋም የነበረ የሰው ሃይል -->
                    <td><?= htmlspecialchars($ent['init_male_emp'] ?? 0) ?></td>
                    <td><?= htmlspecialchars($ent['init_female_emp'] ?? 0) ?></td>
                    <td><?= htmlspecialchars(($ent['init_male_emp'] ?? 0) + ($ent['init_female_emp'] ?? 0)) ?></td>
                    
                    <!-- ወቅታዊ የአባላት ብዛት (ፆታ) -->
                    <td><?= htmlspecialchars($ent['curr_male_members'] ?? 0) ?></td>
                    <td><?= htmlspecialchars($ent['curr_female_members'] ?? 0) ?></td>
                    <td><?= htmlspecialchars(($ent['curr_male_members'] ?? 0) + ($ent['curr_female_members'] ?? 0)) ?></td>
                    
                    <!-- በዕድሜ ክልል -->
                    <td><?= htmlspecialchars($ent['age_15_29'] ?? 0) ?></td>
                    <td><?= htmlspecialchars($ent['age_30_49'] ?? 0) ?></td>
                    <td><?= htmlspecialchars($ent['age_50_65'] ?? 0) ?></td>
                    <td><?= htmlspecialchars($ent['age_above_65'] ?? 0) ?></td>
                    <td><?= htmlspecialchars(($ent['age_15_29'] ?? 0) + ($ent['age_30_49'] ?? 0) + ($ent['age_50_65'] ?? 0) + ($ent['age_above_65'] ?? 0)) ?></td>
                    
                    <!-- በትምህርት ደረጃ -->
                    <td><?= htmlspecialchars($ent['edu_basic'] ?? 0) ?></td>
                    <td><?= htmlspecialchars($ent['edu_primary'] ?? 0) ?></td>
                    <td><?= htmlspecialchars($ent['edu_secondary'] ?? 0) ?></td>
                    <td><?= htmlspecialchars($ent['edu_college_degree'] ?? 0) ?></td>
                    <td><?= htmlspecialchars(($ent['edu_basic'] ?? 0) + ($ent['edu_primary'] ?? 0) + ($ent['edu_secondary'] ?? 0) + ($ent['edu_college_degree'] ?? 0)) ?></td>
                    
                    <!-- ከአባላት ውጭ የተፈጠረ የስራ እድል (ቋሚ) -->
                    <td><?= htmlspecialchars($ent['perm_male_jobs'] ?? 0) ?></td>
                    <td><?= htmlspecialchars($ent['perm_female_jobs'] ?? 0) ?></td>
                    <td><?= htmlspecialchars(($ent['perm_male_jobs'] ?? 0) + ($ent['perm_female_jobs'] ?? 0)) ?></td>
                    
                    <!-- ከአባላት ውጭ የተፈጠረ የስራ እድል (ጊዜያዊ) -->
                    <td><?= htmlspecialchars($ent['temp_male_jobs'] ?? 0) ?></td>
                    <td><?= htmlspecialchars($ent['temp_female_jobs'] ?? 0) ?></td>
                    <td><?= htmlspecialchars(($ent['temp_male_jobs'] ?? 0) + ($ent['temp_female_jobs'] ?? 0)) ?></td>
                    
                    <!-- የኢንተርፕራይዙ ምርትና አገልግሎት -->
                    <td><?= htmlspecialchars($ent['product_type'] ?? '') ?></td>
                    <td><?= htmlspecialchars($ent['target_market'] ?? '') ?></td>
                </tr>
                <?php 
            endforeach; ?>
        <?php else: ?>
            <tr class="empty-state">
              <td colspan="39">መረጃ አልተገኘም</td>
            </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

</body>
</html>