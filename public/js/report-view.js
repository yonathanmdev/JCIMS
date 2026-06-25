// ─── CSV Export ──────────────────────────────────────────────
function exportCSV() {
  var table = document.getElementById('reportTable');
  if (!table) return;

  var rows = table.querySelectorAll('tr');
  var csv  = [];

  rows.forEach(function (row) {
    // Skip action column (last th/td)
    var cols = row.querySelectorAll('th, td');
    var rowData = [];

    cols.forEach(function (col, index) {
      // Skip last column (Action)
      if (index < cols.length - 1) {
        rowData.push('"' + col.innerText.trim().replace(/"/g, '""') + '"');
      }
    });

    csv.push(rowData.join(','));
  });

  // UTF-8 BOM for correct Amharic display in Excel
  var blob     = new Blob(['\uFEFF' + csv.join('\n')], { type: 'text/csv;charset=utf-8;' });
  var url      = URL.createObjectURL(blob);
  var a        = document.createElement('a');
  var filename = document.title.replace(/\s+/g, '_') + '_' + new Date().toISOString().slice(0, 10) + '.csv';

  a.href     = url;
  a.download = filename;
  document.body.appendChild(a);
  a.click();
  document.body.removeChild(a);
  URL.revokeObjectURL(url);
}
document.addEventListener('DOMContentLoaded', function() {
        const printBtn = document.getElementById('printGenderReportBtn');
        
        if (printBtn) {
            printBtn.addEventListener('click', function(event) {
                event.preventDefault(); // Prevents the "#" from jumping the page
                window.print();         // Triggers the browser print dialog
            });
        }
    });