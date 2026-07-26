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
    --sticky-shadow: 2px 0 4px rgba(0,0,0,0.08);
    --accent: #3f6fb5;
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
    box-shadow: 0 1px 3px rgba(0,0,0,0.08), 0 1px 2px rgba(0,0,0,0.04);
    overflow: hidden;
    border: 1px solid var(--border);
  }

  .table-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 12px 16px;
    border-bottom: 1px solid var(--border);
    flex-wrap: wrap;
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
    display: flex;
    align-items: center;
    gap: 6px;
  }

  .toolbar-hint svg {
    width: 14px;
    height: 14px;
    flex-shrink: 0;
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

  /* stagger z-index/top offsets isn't trivial with rowspans in pure CSS sticky,
     so we keep the whole thead pinned as a block using top:0 per row via JS-free
     approach: each row's th gets its own top offset computed from row heights */
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
  }

  #myTable th:nth-child(2),
  #myTable td:nth-child(2) {
    position: sticky;
    left: 34px;
    z-index: 2;
    background: #fff;
    box-shadow: var(--sticky-shadow);
    text-align: right;
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
    font-style: normal;
    font-size: 12px;
  }

  /* scrollbar styling */
  .table-scroll::-webkit-scrollbar { height: 10px; width: 10px; }
  .table-scroll::-webkit-scrollbar-track { background: #eef1f5; }
  .table-scroll::-webkit-scrollbar-thumb { background: #b9c2cf; border-radius: 6px; }
  .table-scroll::-webkit-scrollbar-thumb:hover { background: #9aa6b8; }

  @media (max-width: 640px) {
    body { padding: 8px; }
    .table-toolbar { padding: 10px 12px; }
    .table-toolbar h1 { font-size: 13px; }
    #myTable { font-size: 10px; }
    #myTable th, #myTable td { padding: 5px 6px; }
  }
</style>
</head>
<body>

<div class="table-shell">
  <div class="table-toolbar">
    <h1>የኢንተርፕራይዝ ሪፖርት ሰንጠረዥ</h1>
    <div class="toolbar-hint">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>
      ወደ ቀኝ/ግራ ይንሸራተቱ
    </div>
  </div>

  <div class="table-scroll">
    <table border="1" id="myTable">
      <thead>
        <tr>
          <th rowspan="4" style="width:3%">ተራ ቁጥር</th>
          <th rowspan="4" style="width:30%">የኢንተርፕራይዙ ስም</th>
        </tr>
        <tr>
          <th colspan="6">አድራሻ</th>
          <th rowspan="3" style="width:20%">የተመሰረተበት ዘመን (ዓ/ም)</th>
          <th rowspan="3" style="width:20%">የተሰማራበት የስራ መስክ</th>
          <th rowspan="3" style="width:20%">የተሰማራበት ዘርፍ (ማኑፋክቸሪንግ፣ ኮንስትራክሽን፣ አገልግሎት፣ ከተማ ግብረና፣ ንግድ)</th>
          <th rowspan="3" style="width:20%">የኢ/ዙ አይነት በትርጓሜ (ጥቃቅን፣ አነስተኛ)</th>
          <th rowspan="3" style="width:20%">የአደረጃጀት አይነት (በግል/በንግድ ማህበር/በህ/ስ/ማ)</th>
          <th rowspan="3" style="width:20%">የግብር ከፋይነት መለያ ቁጥር</th>
          <th rowspan="3" style="width:20%">የዕድገት ደረጃ (ጀማሪ/ታዳጊ/መብቃት)</th>
          <th colspan="2" style="width:20%">መነሻ ጠቅላላ ሃብት መጠንና ምንጩ</th>
          <th rowspan="3" style="width:20%">ወቅታዊ ጠቅላላ ሃብት መጠን</th>
          <th colspan="3" style="width:20%">ሲቋቋም የነበረ የሰው ሃይል</th>
          <th colspan="13" style="width:20%">ወቅታዊ የአባላት ብዛት</th>
          <th colspan="6" style="width:20%">ከአባላት ውጭ የተፈጠረ የስራ እድል</th>
          <th colspan="2" style="width:20%">የኢንተርፕራይዙ ምርትና አገልግሎት</th>
        </tr>
        <tr>
          <th rowspan="2" style="width:4%">ዞን</th>
          <th rowspan="2" style="width:4%">ወረዳ</th>
          <th rowspan="2" style="width:4%">ከተማ</th>
          <th rowspan="2" style="width:4%">ቀበሌ</th>
          <th rowspan="2" style="width:4%">የቤት ቁጥር</th>
          <th rowspan="2" style="width:4%">ስልክ ቁጥር</th>
          <th rowspan="2" style="width:20%">መነሻ ጠቅላላ ሃብት መጠን</th>
          <th rowspan="2" style="width:20%">ምንጭ (ከራስ ተቀማጭ፣ ከቤተሰብ ብድር)</th>
          <th rowspan="2" style="width:20%">ወንድ</th>
          <th rowspan="2" style="width:20%">ሴት</th>
          <th rowspan="2" style="width:20%">ድምር</th>
          <th colspan="3" style="width:20%">ፆታ</th>
          <th colspan="5" style="width:20%">በዕድሜ</th>
          <th colspan="5" style="width:20%">በትምህርት ደረጃ</th>
          <th colspan="3" style="width:4%">ቋሚ</th>
          <th colspan="3" style="width:30px">ጊዚያዊ</th>
          <th rowspan="2">የምርቱ ዓይነት</th>
          <th rowspan="2">የሚቀርብበት ገበያ /ለሃገር ወይስ ለውጭ</th>
        </tr>
        <tr>
          <th style="width:20%">ወ</th>
          <th style="width:20%">ሴ</th>
          <th style="width:20%">ድ</th>
          <th style="width:20%">15-29</th>
          <th style="width:20%">30-49</th>
          <th style="width:20%">50-65</th>
          <th style="width:20%">&gt;65</th>
          <th style="width:20%">ድምር</th>
          <th style="width:20%">ማንበብና መፃፍ የማይችሉ /መሰረተ ትምህርት</th>
          <th style="width:20%">አንደኛ ደረጃ (1-8)*</th>
          <th style="width:20%">ሁለተኛ ደረጃ (9-12)**</th>
          <th style="width:20%">ኮሌጅ (ዩኒቨርሲቲ) ያጠናቀቁ</th>
          <th style="width:20%">ድምር</th>
          <th style="width:20%">ወ</th>
          <th style="width:20%">ሴ</th>
          <th style="width:20%">ድ</th>
          <th style="width:20%">ወ</th>
          <th style="width:20%">ሴ</th>
          <th style="width:20%">ድ</th>
        </tr>
      </thead>
      <tbody>
        <tr class="empty-state">
          <td colspan="39">መረጃ አልተገኘም</td>
        </tr>
      </tbody>
    </table>
  </div>
</div>

</body>
</html>