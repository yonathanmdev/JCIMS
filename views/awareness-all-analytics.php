<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h5 class="font-weight-bold" style="color: #1a365d;"><i class="fas fa-chart-bar mr-2"></i>የግንዛቤ ፈጠራ ስታቲስቲካዊ ትንታኔ (በመዋቅሩ መሠረት)</h5>
            </div>
            <div class="col-sm-6 text-right">
                <a href="dashboard" class="btn btn-sm btn-secondary" style="border-radius: 8px;"><i class="fas fa-arrow-left mr-1"></i> ወደ ዳሽቦርድ ተመለስ</a>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        
        <!-- 1ኛ ረድፍ፦ የዋናው የግንዛቤ ፈጠራ ጥምር ቻርት (ሙሉ ስፋት ያለው) -->
        <div class="row">
            <div class="col-md-12 mb-4">
                <div class="card h-100 shadow-sm" style="border-radius: 12px; background: #fff; border: 1px solid #e2e8f0;">
                    <div class="card-body">
                        <h6 class="font-weight-bold text-muted mb-3">
                            <i class="fas fa-bullhorn mr-2 text-primary"></i> ጠቅላላ የግንዛቤ ፈጠራ ሁኔታ (በምድብ)
                        </h6>
                        <div style="position: relative; height:300px; width:100%">
                            <canvas id="genderChart1"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2ኛ ረድፍ፦ የስራ ፈላጊዎች እና ወላጆች ሁኔታ -->
        <div class="row">
            <div class="col-md-6 mb-4">
                <a href="awareness-analytics" class="text-decoration-none d-block h-100 text-dark">
                <div class="card shadow-sm border-0 h-100" style="border-radius: 12px;">
                    <div class="card-body">
                        <h6 class="font-weight-bold text-muted mb-3">ግንዛቤ ፈጠራ በስራ ፈላጊዎች (በፆታ)</h6>
                        <canvas id="genderChart" style="max-height: 220px;"></canvas>
                    </div>
                </div>
                </a>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm border-0 h-100" style="border-radius: 12px;">
                    <div class="card-body">
                        <h6 class="font-weight-bold text-muted mb-3">ግንዛቤ ፈጠራ በስራ ፈላጊ ወላጆች (በፆታ)</h6>
                        <canvas id="physicalChart" style="max-height: 220px;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3ኛ ረድፍ፦ የትምህርት ደረጃ እና የስራ ፈላጊ ሁኔታ -->
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm border-0 h-100" style="border-radius: 12px;">
                    <div class="card-body">
                        <h6 class="font-weight-bold text-muted mb-3">ግንዛቤ ፈጠራ በሌሎች የማህበረሰብ ክፍሎች</h6>
                        <canvas id="educationChart" style="max-height: 260px;"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm border-0 h-100" style="border-radius: 12px;">
                    <div class="card-body">
                        <h6 class="font-weight-bold text-muted mb-3">ግንዛቤ ፈጠራ ያላገኙ በጾታ</h6>
                        <canvas id="statusChart" style="max-height: 260px;"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

<!-- Chart.js ላይብረሪ መሳቢያ -->
<script src="plugins/chart.js/Chart.min.js" nonce="<?php echo htmlspecialchars($GLOBALS['nonce'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@0.7.0" nonce="<?php echo htmlspecialchars($GLOBALS['nonce'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></script>

<script nonce="<?php echo htmlspecialchars($GLOBALS['nonce'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
document.addEventListener("DOMContentLoaded", function() {
(function() {
    // ከ PHP የሚመጣውን ዳታ መውሰድ
    const rawDataJson = '<?php echo json_encode($chartsData ?? null); ?>';
    
    let chartsData;
    try {
        chartsData = JSON.parse(rawDataJson);
    } catch (e) {
        console.error("ዳታውን ማስተላለፍ አልተቻለም፡", e);
    }

    // ዳታው ከሌለ ነባሪ መተኪያ ማዘጋጀት
    if (!chartsData) {
        chartsData = {
            gender: { 'ወንድ': 0, 'ሴት': 0 },
            parents_gender: { 'ወንድ': 0, 'ሴት': 0 },
            others_gender: { 'ወንድ': 0, 'ሴት': 0 },
            no_awareness_gender: { 'ወንድ': 0, 'ሴት': 0 }
        };
    }

    // ==========================================
    // 1ኛ ቻርት (ስራ ፈላጊዎች - genderChart)
    // ==========================================
    const canvas1 = document.getElementById('genderChart');
    if (canvas1) {
        const ctx1 = canvas1.getContext('2d');
        const gData = chartsData.gender || { 'ወንድ': 0, 'ሴት': 0 };

        new Chart(ctx1, {
            type: 'doughnut',
            plugins: [ChartDataLabels],
            data: {
                labels: Object.keys(gData),
                datasets: [{
                    label: 'የተሳታፊዎች ብዛት',
                    data: Object.values(gData),
                    backgroundColor: ['#3b82f6', '#ec4899'],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' },
                    datalabels: {
                        color: '#ffffff',
                        backgroundColor: 'rgba(0, 0, 0, 0.4)',
                        borderRadius: 4,
                        padding: 4,
                        font: { weight: 'bold', size: 13 },
                        formatter: value => value
                    }
                }
            }
        });
    }

    // ==========================================
    // 2ኛ ቻርት (ወላጆች - physicalChart)
    // ==========================================
    const canvas2 = document.getElementById('physicalChart');
    if (canvas2) {
        const ctx2 = canvas2.getContext('2d');
        const parentsData = chartsData.parents_gender || { 'ወንድ': 0, 'ሴት': 0 };

        new Chart(ctx2, {
            type: 'doughnut',
            plugins: [ChartDataLabels],
            data: {
                labels: Object.keys(parentsData),
                datasets: [{
                    label: 'የወላጆች ብዛት',
                    data: Object.values(parentsData),
                    backgroundColor: ['#3b82f6', '#ec4899'],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' },
                    datalabels: {
                        color: '#ffffff',
                        backgroundColor: 'rgba(0, 0, 0, 0.4)',
                        borderRadius: 4,
                        padding: 4,
                        font: { weight: 'bold', size: 13 },
                        formatter: value => value
                    }
                }
            }
        });
    }

    // ==========================================
    // 3ኛ ቻርት (ሌሎች ህብረተሰብ ክፍሎች - educationChart)
    // ==========================================
    const canvas3 = document.getElementById('educationChart');
    if (canvas3) {
        const ctx3 = canvas3.getContext('2d');
        const othersData = chartsData.others_gender || { 'ወንድ': 0, 'ሴት': 0 };

        new Chart(ctx3, {
            type: 'doughnut',
            plugins: [ChartDataLabels],
            data: {
                labels: Object.keys(othersData),
                datasets: [{
                    label: 'የተሳታፊዎች ብዛት',
                    data: Object.values(othersData),
                    backgroundColor: ['#3b82f6', '#ec4899'],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' },
                    datalabels: {
                        color: '#ffffff',
                        backgroundColor: 'rgba(0, 0, 0, 0.4)',
                        borderRadius: 4,
                        padding: 4,
                        font: { weight: 'bold', size: 13 },
                        formatter: value => value
                    }
                }
            }
        });
    }

    // ==========================================
    // 4ኛ ቻርት (ግንዛቤ ያላገኙ - statusChart)
    // ==========================================
    const canvas4 = document.getElementById('statusChart');
    if (canvas4) {
        const ctx4 = canvas4.getContext('2d');
        // 💡 ከ PHP የመጣውን የዲበግ ዳታ 'no_awareness_gender' በትክክል ማንበብ
        const noAwarenessData = chartsData.no_awareness_gender || { 'ወንድ': 0, 'ሴት': 0 };

        new Chart(ctx4, {
            type: 'doughnut',
            plugins: [ChartDataLabels],
            data: {
                labels: Object.keys(noAwarenessData),
                datasets: [{
                    label: 'ያላገኙ ስራ ፈላጊዎች',
                    data: Object.values(noAwarenessData),
                    backgroundColor: ['#3b82f6', '#ec4899'],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' },
                    datalabels: {
                        color: '#ffffff',
                        backgroundColor: 'rgba(0, 0, 0, 0.4)',
                        borderRadius: 4,
                        padding: 4,
                        font: { weight: 'bold', size: 13 },
                        formatter: value => value
                    }
                }
            }
        });
    }

})();
});
</script>