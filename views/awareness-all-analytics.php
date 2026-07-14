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
                            <canvas id="genderChart"></canvas>
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
                        <canvas id="residenceChart" style="max-height: 220px;"></canvas>
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
                        <h6 class="font-weight-bold text-muted mb-3">ግንዛቤ ፈጠራ ከስራ ፈላጊዎች ውጭ በጾታ</h6>
                        <canvas id="statusChart" style="max-height: 260px;"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

<!-- Chart.js ላይብረሪ መሳቢያ -->
<script src="plugins/chart.js/Chart.min.js" nonce="<?php echo htmlspecialchars($GLOBALS['nonce'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></script>

<script nonce="<?php echo htmlspecialchars($GLOBALS['nonce'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
document.addEventListener("DOMContentLoaded", function() {
    if (typeof Chart === 'undefined') return;

    const rawData = <?= json_encode($chartsData ?? null); ?>;
    const chartsData = rawData || {
        gender: { 'በስራ ፈላጊዎች': 0, 'በስራ ፈላጊ ወላጆች': 0, 'በሌሎች የህብረተሰብ ክፍሎች': 0 },
        jobSeekersGender: { 'ወንድ': 0, 'ሴት': 0, 'ድምር': 0 }, // 👈 ይህንን አዲስ መስመር ጨምር
        residence: { 'ወንድ': 0, 'ሴት': 0 },
        physical: { '0': 0, '1': 0 },
        education: { 'ሌሎች': 0 },
        status: { 'ሌሎች': 0 }
    };
    };

    // ከባሩ በላይ ቁጥሩን በቀጥታ ለመጻፍ የሚያስችል የ Chart.js ፕለጊን ሎጂክ
    const valueOnTopPlugin = {
        onComplete: function () {
            var chartInstance = this.chart,
                ctx = chartInstance.ctx;

            ctx.font = Chart.helpers.fontString(Chart.defaults.global.defaultFontSize, 'bold', Chart.defaults.global.defaultFontFamily);
            ctx.textAlign = 'center';
            ctx.textBaseline = 'bottom';
            ctx.fillStyle = '#1a202c'; // የቁጥሩ ከለር (ጥቁር)

            this.data.datasets.forEach(function (dataset, i) {
                var meta = chartInstance.controller.getDatasetMeta(i);
                meta.data.forEach(function (bar, index) {
                    var data = dataset.data[index];
                    if (data >= 0) {
                        ctx.fillText(data, bar._model.x, bar._model.y - 5);
                    }
                });
            });
        }
    };

    const commonOptions = {
        responsive: true,
        maintainAspectRatio: false,
        legend: { display: false },
        events: [], // ሆቨር ሲደረግ ቁጥሩ እንዳይጠፋ ይከላከላል
        animation: valueOnTopPlugin, 
        hover: { animationDuration: 0 },
        layout: {
            padding: {
                top: 25 // ቁጥሮቹ ከላይ እንዳይሸፈኑ ቦታ መተው
            }
        },
        scales: {
            yAxes: [{ 
                gridLines: { display: true, color: '#f3f4f6' }, 
                ticks: { beginAtZero: true, precision: 0, padding: 10 } 
            }],
            xAxes: [{ 
                gridLines: { display: false },
                ticks: { padding: 5 }
            }]
        }
    };

    // 1. ዋናው የተዋሃደ የግንዛቤ ፈጠራ ቻርት (ባለ 3 ምድብ ባር)
    new Chart(document.getElementById('genderChart'), {
        type: 'bar',
        data: {
            labels: Object.keys(chartsData.gender), // ['በስራ ፈላጊዎች', 'በስራ ፈላጊ ወላጆች', 'በሌሎች']
            datasets: [{ 
                data: Object.values(chartsData.gender), 
                backgroundColor: ['#1e3a8a', '#10b981', '#f59e0b'], // ሰማያዊ፣ አረንጓዴ፣ ቢጫ
                barPercentage: 0.3 // ባሮቹ መካከለኛ መጠን እንዲኖራቸው
            }]
        },
        options: commonOptions
    });

    // 2. በስራ ፈላጊዎች ፆታ ቻርት (በቀድሞው residenceChart መታወቂያ ላይ የሚሳለው)
    new Chart(document.getElementById('residenceChart'), {
        type: 'bar',
        data: {
            labels: Object.keys(chartsData.residence),
            datasets: [{ data: Object.values(chartsData.residence), backgroundColor: ['#3b82f6', '#ec4899'], barPercentage: 0.5 }]
        },
        options: commonOptions
    });

    // 3. በአካል ጉዳት ወይም በወላጆች ቻርት ላይ የሚሳለው
    const physicalRaw = chartsData.physical || { '0': 0, '1': 0 };
    const physicalMappedData = {
        'ጉዳት የሌለበት': physicalRaw['0'] || 0,
        'ጉዳት ያለበት': physicalRaw['1'] || 0
    };

    new Chart(document.getElementById('physicalChart'), {
        type: 'bar',
        data: {
            labels: Object.keys(physicalMappedData),
            datasets: [{ 
                data: Object.values(physicalMappedData), 
                backgroundColor: ['#6366f1', '#ef4444'], 
                barPercentage: 0.5 
            }]
        },
        options: commonOptions
    });

    // 4. ትምህርት ቻርት
    new Chart(document.getElementById('educationChart'), {
        type: 'bar',
        data: {
            labels: Object.keys(chartsData.education).map(key => {
                if (key === '1') return 'ቴ/ሙ/ተመራቂዎች';
                if (key === '2') return 'ዩኒቨርሲቲ ተመራቂዎች';
                return key;
            }),
            datasets: [{ 
                data: Object.values(chartsData.education), 
                backgroundColor: '#3b82f6', 
                barPercentage: 0.6 
            }]
        },
        options: commonOptions
    });

    // 5. ሁኔታ ቻርት
    new Chart(document.getElementById('statusChart'), {
        type: 'bar',
        data: {
            labels: Object.keys(chartsData.status),
            datasets: [{ data: Object.values(chartsData.status), backgroundColor: '#8b5cf6', barPercentage: 0.6 }]
        },
        options: commonOptions
    });
</script>