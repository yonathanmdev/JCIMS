<div class="container-fluid py-4">
    <!-- Header Zone -->
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h3 class="font-weight-bold text-dark mb-1">
                    <i class="fa fa-line-chart text-primary"></i> የሥራ እድል ፈጠራ ስታቲስቲክስ ትንታኔ
                </h3>
                <p class="text-muted small mb-0">በተመረጠው ቅርንጫፍ እና ንዑስ ቅርንጫፎች ስር የተመዘገቡ የሥራ እድል ፈጠራ መረጃዎች አጠቃላይ ስታቲስቲክስ</p>
            </div>
            <div class="no-print">
                <button onclick="window.print()" class="btn btn-outline-secondary">
                    <a href="dashboard" class="btn btn-sm btn-secondary" style="border-radius: 8px;"><i class="fas fa-arrow-left mr-1"></i> ወደ ዳሽቦርድ ተመለስ</a>
                </button>
            </div>
        </div>
    </div>

    <!-- Top Row: 2 Expanded Charts (Employment Status & Sector) -->
    <div class="row g-4 mb-4">
        <!-- 1. የቅጥር ሁኔታ (Employment Status) -->
        <div class="col-lg-6 col-md-12">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom-0 pt-3 font-weight-bold text-secondary fs-5">
                    <i class="fa fa-clock-o text-primary me-2"></i> የቅጥር ሁኔታ (Employment Status)
                </div>
                <div class="card-body p-3">
                    <div style="position: relative; height: 300px;">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. በዋና ዋና ዘርፎች (Sector) -->
        <div class="col-lg-6 col-md-12">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom-0 pt-3 font-weight-bold text-secondary fs-5">
                    <i class="fa fa-cubes text-success me-2"></i> በዋና ዋና ዘርፎች (By Sector)
                </div>
                <div class="card-body p-3">
                    <div style="position: relative; height: 300px;">
                        <canvas id="sectorChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Row: Full Width Detailed Chart (Job Creation Reason) -->
    <div class="row g-4 mb-4">
        <!-- 3. የሥራ እድል መፍጠሪያ አማራጮች (Job Creation Reason) -->
        <div class="col-12">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom-0 pt-3 font-weight-bold text-secondary fs-5">
                    <i class="fa fa-briefcase text-warning me-2"></i> የሥራ እድል መፍጠሪያ አማራጮች (Job Creation Reasons)
                </div>
                <div class="card-body p-3">
                    <div style="position: relative; height: 400px;">
                        <canvas id="reasonChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js ላይብረሪ መሳቢያ (ያለህበት ኦሪጅናል ላይብረሪ ብቻ) -->
<script src="plugins/chart.js/Chart.min.js" nonce="<?php echo htmlspecialchars($GLOBALS['nonce'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></script>

<script nonce="<?php echo htmlspecialchars($GLOBALS['nonce'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
document.addEventListener("DOMContentLoaded", function() {
    const rawData = <?= json_encode($chartsData, JSON_UNESCAPED_UNICODE); ?> || {};

    const commonOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: { font: { family: 'sans-serif', size: 13 } }
            }
        }
    };

    // 1. የቅጥር ሁኔታ (Employment Status) - Doughnut
    const statusElem = document.getElementById('statusChart');
    if (statusElem) {
        new Chart(statusElem, {
            type: 'doughnut',
            data: {
                labels: Object.keys(rawData.employmentstatus || {}),
                datasets: [{
                    data: Object.values(rawData.employmentstatus || {}),
                    backgroundColor: ['#0d6efd', '#ffc107'],
                    borderWidth: 2
                }]
            },
            options: {
                ...commonOptions,
                animation: {
                    onComplete: function() {
                        const chart = this;
                        const ctx = chart.ctx;
                        ctx.font = 'bold 13px sans-serif';
                        ctx.fillStyle = '#ffffff';
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'middle';

                        chart.data.datasets.forEach((dataset, i) => {
                            const meta = chart.getDatasetMeta(i);
                            meta.data.forEach((element, index) => {
                                const dataVal = dataset.data[index];
                                if (dataVal > 0) {
                                    const position = element.tooltipPosition();
                                    ctx.fillText(dataVal.toLocaleString(), position.x, position.y);
                                }
                            });
                        });
                    }
                }
            }
        });
    }

    // 2. በዋና ዋና ዘርፎች (Per Sector) - Vertical Bar
    const sectorElem = document.getElementById('sectorChart');
    if (sectorElem) {
        new Chart(sectorElem, {
            type: 'bar',
            data: {
                labels: Object.keys(rawData.persector || {}),
                datasets: [{
                    label: 'የተፈጠረ የሥራ እድል',
                    data: Object.values(rawData.persector || {}),
                    backgroundColor: ['#198754', '#0dcaf0', '#fd7e14'],
                    borderRadius: 5
                }]
            },
            options: {
                ...commonOptions,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { 
                        beginAtZero: true,
                        grace: '12%'
                    }
                },
                animation: {
                    onComplete: function() {
                        const chart = this;
                        const ctx = chart.ctx;
                        ctx.font = 'bold 12px sans-serif';
                        ctx.fillStyle = '#212529';
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'bottom';

                        chart.data.datasets.forEach((dataset, i) => {
                            const meta = chart.getDatasetMeta(i);
                            meta.data.forEach((bar, index) => {
                                const dataVal = dataset.data[index];
                                if (dataVal > 0) {
                                    ctx.fillText(dataVal.toLocaleString(), bar.x, bar.y - 5);
                                }
                            });
                        });
                    }
                }
            }
        });
    }

    // 3. የሥራ እድል መፍጠሪያ አማራጮች (Job Creation Reason) - Horizontal Bar
    const reasonElem = document.getElementById('reasonChart');
    if (reasonElem) {
        new Chart(reasonElem, {
            type: 'bar',
            data: {
                labels: Object.keys(rawData.jobcreationreason || {}),
                datasets: [{
                    label: 'የተፈጠረ የሥራ እድል ብዛት',
                    data: Object.values(rawData.jobcreationreason || {}),
                    backgroundColor: '#3b82f6',
                    borderRadius: 4
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: { 
                        beginAtZero: true,
                        grace: '15%'
                    }
                },
                animation: {
                    onComplete: function() {
                        const chart = this;
                        const ctx = chart.ctx;
                        ctx.font = 'bold 12px sans-serif';
                        ctx.fillStyle = '#212529';
                        ctx.textAlign = 'left';
                        ctx.textBaseline = 'middle';

                        chart.data.datasets.forEach((dataset, i) => {
                            const meta = chart.getDatasetMeta(i);
                            meta.data.forEach((bar, index) => {
                                const dataVal = dataset.data[index];
                                if (dataVal > 0) {
                                    ctx.fillText(dataVal.toLocaleString(), bar.x + 8, bar.y);
                                }
                            });
                        });
                    }
                }
            }
        });
    }
});
</script>

<style>
/* Print Styling */
@media print {
    .no-print { display: none !important; }
    body { background-color: #ffffff !important; }
    .card { border: 1px solid #e5e7eb !important; box-shadow: none !important; }
}
</style>