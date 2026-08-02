<div class="container-fluid py-4">
    <!-- የራስጌ ክፍል (Header) -->
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h3 class="font-weight-bold text-dark mb-1">
                    <i class="fa fa-pie-chart text-primary"></i> <?php echo htmlspecialchars('ገና ኢንተርፕራይዝ ያልመሰረቱ ግን የተደራጁ ስታቲስቲክስ ትንታኔ', ENT_QUOTES, 'UTF-8'); ?>
                </h3>
            </div>
            <div class="no-print">
                <button onclick="window.print()" class="btn btn-outline-secondary">
                    <a href="dashboard" class="btn btn-sm btn-secondary" style="border-radius: 8px;"><i class="fas fa-arrow-left mr-1"></i> ወደ ዳሽቦርድ ተመለስ</a>
                </button>
            </div>
        </div>
    </div>

    <!-- የቻርት ረድፍ (Charts Row) -->
    <div class="row g-4 mb-4">
        <!-- 1. የተደራጁበት አካባቢ (Doughnut Chart) -->
        <div class="col-lg-6 col-md-12">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom-0 pt-3 font-weight-bold text-secondary fs-5">
                    <i class="fa fa-map-marker text-danger me-2"></i> የተደራጁበት አካባቢ (ከተማ / ገጠር)
                </div>
                <div class="card-body p-3">
                    <div style="position: relative; height: 320px;">
                        <canvas id="akababiChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. የአደረጃጀቱ አይነት / Project Type (Bar Chart) -->
        <div class="col-lg-6 col-md-12">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom-0 pt-3 font-weight-bold text-secondary fs-5">
                    <i class="fa fa-list-alt text-primary me-2"></i> የአደረጃጀቱ አይነት (Project Type)
                </div>
                <div class="card-body p-3">
                    <div style="position: relative; height: 320px;">
                        <canvas id="projectTypeChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js ላይብረሪ መሳቢያ -->
<!-- Chart.js ላይብረሪ መሳቢያ (ያለህበት ኦሪጅናል ላይብረሪ ብቻ) -->
<script src="plugins/chart.js/Chart.min.js" nonce="<?php echo htmlspecialchars($GLOBALS['nonce'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></script>

<script nonce="<?php echo htmlspecialchars($GLOBALS['nonce'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
document.addEventListener("DOMContentLoaded", function() {
    // ከኮንትሮለር የተላከውን $chartsData ወደ JS Object መለወጥ
    const rawData = <?= json_encode($chartsData ?? [], JSON_UNESCAPED_UNICODE); ?>;

    // የጋራ የቻርት አማራጮች
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

    // 1. የተደራጁበት አካባቢ (Doughnut Chart)
    const akababiElem = document.getElementById('akababiChart');
    if (akababiElem) {
        new Chart(akababiElem, {
            type: 'doughnut',
            data: {
                labels: Object.keys(rawData.yetederajubet_akababi || {}),
                datasets: [{
                    data: Object.values(rawData.yetederajubet_akababi || {}),
                    backgroundColor: ['#0d6efd', '#198754'], // ከተማ = ሰማያዊ, ገጠር = አረንጓዴ
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

    // 2. የአደረጃጀቱ አይነት / Project Type (Vertical Bar Chart)
    const projectElem = document.getElementById('projectTypeChart');
    if (projectElem) {
        new Chart(projectElem, {
            type: 'bar',
            data: {
                labels: Object.keys(rawData.project_type || {}),
                datasets: [{
                    label: 'የተደራጁ አደረጃጀቶች ብዛት',
                    data: Object.values(rawData.project_type || {}),
                    backgroundColor: '#8b5cf6',
                    borderRadius: 5,
                    barPercentage: 0.6
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