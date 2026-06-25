document.addEventListener('DOMContentLoaded', function () {

  // ገጹ ላይ የሚደረጉ የክሊክ ኩነቶችን መከታተል
  document.addEventListener('click', function (e) {
    
    const card = e.target.closest('.report-type-card');
    
    if (card) {
      const cards = document.querySelectorAll('.report-type-card');

      // Highlight selected
      cards.forEach(function (c) {
        c.style.boxShadow = '';
        c.style.transform = '';
      });
      card.style.boxShadow = '0 0 0 3px rgba(0,123,255,0.5)';
      card.style.transform = 'scale(1.02)';

      const reportType = card.getAttribute('data-report');
      const branchId   = card.getAttribute('data-branch');

      if (!reportType || !branchId) {
        alert('Branch ID ወይም Report Type አልተገኘም።');
        return;
      }

      // ─── 🆕 ንጹህ URL (Clean URL) ግንባታ ───
const cleanBaseUrl = BASE_URL.replace(/\/+$/, '');

// አደራደር፡ /report/{type}/{branch_id}
const reportUrl = cleanBaseUrl + '/report/' + reportType + '/' + branchId;

// በአዲስ ታብ ላይ ገጹን መክፈት
window.open(reportUrl, '_blank');
    }
  });

});