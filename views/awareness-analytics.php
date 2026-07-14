<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h5 class="font-weight-bold" style="color: #1a365d;"><i class="fas fa-chart-bar mr-2"></i>የግንዛቤ ፈጠራ ስታቲስቲካዊ ትንታኔ (በመዋቅሩ መሠረት)</h5>
            </div>
            <div class="col-sm-6 text-right">
                <a href="dashboard" class="btn btn-sm btn-secondary style="border-radius: 8px;"><i class="fas fa-arrow-left mr-1"></i> ወደ ዳሽቦርድ ተመለስ</a>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        
        <!-- 1ኛ ረድፍ፦ ፆታ፣ መኖሪያ እና አካል ጉዳት -->
        <div class="row">
            <div class="col-md-4 mb-4">
                        <div class="card h-100 shadow-sm" style="border-radius: 12px; background: #fff; border: 1px solid #e2e8f0;">
                            <div class="card-body">
                                <h6 class="font-weight-bold text-muted mb-3">
                                    <i class="fas fa-user-tie mr-2 text-primary"></i> በፆታ (ወንድ እና ሴት)
                                </h6>
                                <div style="position: relative; height:240px; width:100%">
                                    <canvas id="genderChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm border-0 h-100" style="border-radius: 12px;">
                    <div class="card-body">
                        <h6 class="font-weight-bold text-muted mb-3">በመኖሪያ አካባቢ (ከተማ እና ገጠር)</h6>
                        <canvas id="residenceChart" style="max-height: 220px;"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm border-0 h-100" style="border-radius: 12px;">
                    <div class="card-body">
                        <h6 class="font-weight-bold text-muted mb-3">የአካል ጉዳት ሁኔታ</h6>
                        <canvas id="physicalChart" style="max-height: 220px;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2ኛ ረድፍ፦ የትምህርት ደረጃ እና የስራ ፈላጊ ሁኔታ -->
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm border-0 h-100" style="border-radius: 12px;">
                    <div class="card-body">
                        <h6 class="font-weight-bold text-muted mb-3">በትምህርት ደረጃ ካታጎሪ</h6>
                        <canvas id="educationChart" style="max-height: 260px;"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm border-0 h-100" style="border-radius: 12px;">
                    <div class="card-body">
                        <h6 class="font-weight-bold text-muted mb-3">በስራ ፈላጊዎች ሁኔታ</h6>
                        <canvas id="statusChart" style="max-height: 260px;"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

<!-- Chart.js ላይብረሪ መሳቢያ -->
<!-- የ Chart.js ላይብረሪ መጫኑን እናረጋግጣለን (ይህንን መስመር መኖሩን ቼክ ያድርጉ) -->
<script src="plugins/chart.js/Chart.min.js" nonce="<?php echo htmlspecialchars($GLOBALS['nonce'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></script>

<script nonce="<?php echo htmlspecialchars($GLOBALS['nonce'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
document.addEventListener("DOMContentLoaded", function() {
    if (typeof Chart === 'undefined') return;

    const rawData = <?= json_encode($chartsData ?? null); ?>;
    const chartsData = rawData || {
        gender: { 'ወንድ': 0, 'ሴት': 0 },
        residence: { 'ከተማ': 0, 'ገጠር': 0 },
        physical: { '0': 0, '1': 0 },
        education: { 'ሌሎች': 0 },
        status: { 'ሌሎች': 0 }
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
                    // ቁጥሩ ከዜሮ በላይ ከሆነ ብቻ እንዲጽፍ
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
        animation: valueOnTopPlugin, // ማሳያውን እዚህ እንጭነዋለን
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

    // 1. ፆታ ቻርት
    new Chart(document.getElementById('genderChart'), {
        type: 'bar',
        data: {
            labels: Object.keys(chartsData.gender),
            datasets: [{ data: Object.values(chartsData.gender), backgroundColor: ['#1e3a8a', '#ec4899'], barPercentage: 0.5 }]
        },
        options: commonOptions
    });

    // 2. መኖሪያ ቻርት
    new Chart(document.getElementById('residenceChart'), {
        type: 'bar',
        data: {
            labels: Object.keys(chartsData.residence),
            datasets: [{ data: Object.values(chartsData.residence), backgroundColor: ['#10b981', '#f59e0b'], barPercentage: 0.5 }]
        },
        options: commonOptions
    });

    // 3. የአካል ጉዳት ሁኔታ ቻርት (0 = ጉዳት የሌለበት፣ 1 = ጉዳት ያለበት)
    const physicalRaw = chartsData.physical || { '0': 0, '1': 0 };

    // የዳታቤዙን 0 እና 1 ቁልፎች ወደ ፈለግከው ትክክለኛ የአማርኛ ስም መለወጫ
    const physicalMappedData = {
        'ጉዳት የሌለበት': physicalRaw['0'] || 0,
        'ጉዳት ያለበት': physicalRaw['1'] || 0
    };

    new Chart(document.getElementById('physicalChart'), {
        type: 'bar',
        data: {
            labels: Object.keys(physicalMappedData), // 'ጉዳት የሌለበት' እና 'ጉዳት ያለበት' የሚሉትን ስሞች ያወጣል
            datasets: [{ 
                data: Object.values(physicalMappedData), // የ 0 እና የ 1 ን ዋጋዎች ይወስዳል
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
        // ቁጥሮቹን (1 እና 2) ወደ ፈለጉት የአማርኛ ጽሑፍ ይቀይራል
        labels: Object.keys(chartsData.education).map(key => {
            if (key === '1') return 'ቴ/ሙ/ተመራቂዎች';
            if (key === '2') return 'ዩኒቨርሲቲ ተመራቂዎች';
            return key; // ሌላ ቁጥር ካለ እራሱን ይመልሳል
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
});
</script>