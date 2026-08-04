function buildViewRow(label, value, colClass = 'col-12 col-sm-6 col-md-3') {
    if (value === null || value === undefined || value === '') return '';
    return `
        <div class="${colClass} mb-3">
            <small class="text-muted d-block">${label}</small>
            <div class="font-weight-bold">${value}</div>
        </div>
    `;
}
function buildFaydaVerifiedBadge(verified) {
    return verified == 1
        ? '<span class="text-success"><i class="fas fa-check-circle"></i> Fayda Verfication</span>'
        : '<span class="text-danger"><i class="fas fa-times-circle"></i> Fayda Verfication</span>';
}
function renderJobseekerView(js) {
    const genderLabel = js.gender;
    const physicalConditionLabel = js.physical_condition === '1' ? 'ያለበት' : 'የሌለበት';
    const educationFinishYear = js.education_trmnet_finsh_year || '';
    const housewifeLabel = js.housewife === '1' ? 'ነች' : 'አይደለችም';
    const haveExpLabel = js.haveexp === '1' ? 'አለ' : 'የለም';
    const employmentStatusMap = {
    '0': 'ስራ እድል ያልተፈጠረለት',
    '1': 'ቋሚ ስራ እድል የተፈጠረለት',
    '2': 'ጊዚያዊ ስራ እድል የተፈጠረለት',
    '3': 'እንዳይደራጅ reject የተደረገ',
    '4': 'በፕሮጀችት የተደራጀ'
};
 const awarenessStatusMap = {
    '0': 'ግንዛቤ ያልተፈጠረለት/ላት',
    '1': 'ግንዛቤ የተፈጠረለት/ላት',
};


const employmentStatusLabel = employmentStatusMap[js.employment_status] ?? '';
    const awarenessStatusLabel = awarenessStatusMap[js.awareness] ?? '';
 let html = '<div class="row">';

html += `
    <div class="col-12 mb-3">
        ${js.branch_display_path ? `<small class="text-muted d-block">የተመዘገበብት አድራሻ</small>` : ''}
        <div class="font-weight-bold d-flex justify-content-between align-items-center">
            <span>${js.branch_display_path || ''}</span>
            ${buildFaydaVerifiedBadge(js.verfied_with_fayda)}
        </div>
    </div>
`;

html += '</div><hr>';
    html += '<div class="row">';
    html += buildViewRow('መ/ቁ', js.job_seeker_id);
    html += buildViewRow('ሙሉ ስም', `${js.first_name || ''} ${js.father_name || ''} ${js.last_name || ''}`.trim());
    html += buildViewRow('ጾታ', genderLabel);
    if (js.gender==='ሴት') {
        html += buildViewRow('የቤት እመቤት', housewifeLabel);
    }
    html += buildViewRow('እድሜ', js.age);
    html += buildViewRow('የስራ ፈላጊ ሁኔታ', js.srafelagi_huneta);
    html += buildViewRow('Labor ID', js.Labor_ID);
    html += buildViewRow('የጋብቻ ሁኔታ', js.maritalstatus);
    html += buildViewRow('FAN', js.FAN);
    html += buildViewRow('መንደር', js.kebele);
    html += buildViewRow('መንደር', js.mender);
    html += buildViewRow('የቀበሌ መታወቂያ ቁጥር', js.kebele_id_no);
    html += buildViewRow('የሚኖርበት አካባቢ', js.residence_status);

    html += buildViewRow('የትምህርት ደረጃ', js.educational_level);
    html += buildViewRow('የት/ቤቱ ዓይነት', js.school_type);
    html += buildViewRow('የተመረቀበት ዲፓርትመንት', js.educated_dpt);
    html += buildViewRow('ትምህርት ያጠናቀቀበት ዓመት', educationFinishYear);
    html += buildViewRow('የ8ኛ ክፍል መለያ ቁጥር', js.g8id);
    html += buildViewRow('ያጠናቀቁበት ሙያ ምድብ', js.graguation_catagory);
    html += buildViewRow('CGPA', js.CGPA);
    html += buildViewRow('ስልክ ቁጥር', js.phone_number);
    html += buildViewRow('የመኖሪያ ቤት ሁኔታ', js.meteleya_huneta);
    html += buildViewRow('የአካል ጉዳት', physicalConditionLabel);
    html += buildViewRow('የአካል ጉዳቱ አይነት', js.physical_condition_desc);
    html += buildViewRow('የስራ ልምድ', haveExpLabel);
    html += buildViewRow('የስራ ልምድ በወር', js.experience);
    html += buildViewRow('የሰሩበት ሀገር', js.workplace);
    html += buildViewRow('የሰሩበት የሙያ መደብ', js.profession);
    html += buildViewRow('የሀገሩ ስም', js.nameofcountry);
    html += buildViewRow('ተጨማሪ የሚችሉት ቋንቋ', js.language);
    html += buildViewRow('አሁን መስራት የሚፈልጉት', js.wageorself);
    html += buildViewRow('ግንዛቤ', awarenessStatusLabel);
    html += buildViewRow('የስራ እድል ሁኔታ', employmentStatusLabel);
    html += buildViewRow('የእናት ሙሉ ስም', js.mothername);
    html += '</div><hr>';

    html += '<div class="row">';
    html += buildViewRow('ዘርፍ 1ኛ ምርጫ', js.choice_sector1_name);
    html += buildViewRow('ንዑስ ዘርፍ 1ኛ ምርጫ', js.sub_choose1_name);
    html += buildViewRow('ዘርፍ 2ኛ ምርጫ', js.choice_sector2_name);
    html += buildViewRow('ንዑስ ዘርፍ 2ኛ ምርጫ', js.sub_choose2_name);
    html += buildViewRow('ዘርፍ 3ኛ ምርጫ', js.choice_sector3_name);
    html += buildViewRow('ንዑስ ዘርፍ 3ኛ ምርጫ', js.sub_choose3_name);
    html += buildViewRow('በግብርና ዘርፍ ልምድ', js.agri_business_experience_status === '1' ? 'አለ' : 'የለም');
    html += buildViewRow('የግብርና ልምድ (ዓመት)', js.agri_business_experience);
    html += buildViewRow('በስር የሚተዳደር ቤተሰብ', js.has_dependents === '1' ? 'አለ' : '');
    html += buildViewRow('የሚተዳደረው ቤተሰብ ብዛት', js.number_of_dependents);
    html += buildViewRow('ከ5 ዓመት በታች ህፃናት ብዛት', js.children_under_five);
    html += buildViewRow('የተመዘገበበት ቀን', js.created_at);
    html += buildViewRow('የመዘገበው ባለሙያ መለያ', js.registered_by_name);
    html += '</div>';

    document.getElementById('jobseekerViewBody').innerHTML = html;
}

$(document).on('click', '.view-jobseeker-btn', function () {
    const jobseekerId = this.dataset.id;

    document.getElementById('jobseekerViewBody').innerHTML =
        '<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x"></i></div>';

    $('#jobseekerViewModal').modal('show');

    fetch(`${window.BASE_URL}/retrieve-jobseeker?jobseeker_id=${jobseekerId}`)
        .then(res => res.json())
        .then(data => {
            if (!data.success) {
                document.getElementById('jobseekerViewBody').innerHTML =
                    `<p class="text-danger text-center">${data.message}</p>`;
                return;
            }
            renderJobseekerView(data.jobseeker);
        })
        .catch(() => {
            document.getElementById('jobseekerViewBody').innerHTML =
                '<p class="text-danger text-center">መረጃ መጫን አልተሳካም</p>';
        });
});
 document.addEventListener('DOMContentLoaded', function () {
    const printButtons = document.querySelectorAll('.print-id-btn');
    
    printButtons.forEach(button => {
        button.addEventListener('click', function () {
            // መረጃዎችን ከ በተኑ attribute መውሰድ
            const data = JSON.parse(this.getAttribute('data-info'));
            const logoUrl = this.getAttribute('data-logo');
            const branchName = this.getAttribute('data-branch');
            
            const printWindow = window.open('', '_blank', 'width=450,height=600');
            
            printWindow.document.write(`
                <html>
                <head>
                    <title>መታወቂያ ካርድ</title>
                    <style>
                        body { font-family: 'Nyala', 'Arial', sans-serif; display: flex; justify-content: center; padding: 20px; }
                        .id-card { width: 380px; padding: 15px; border: 1px solid #000; }
                        .header { text-align: center; margin-bottom: 10px; }
                        .logo { width: 60px; }
                        .meta-info { font-size: 12px; margin-bottom: 10px; text-align: right; }
                        .section-title { background: #eee; font-weight: bold; padding: 2px 5px; margin-top: 10px; display: flex; justify-content: space-between; }
                        .row-data { display: flex; justify-content: space-between; padding: 2px 5px; border-bottom: 1px solid #f0f0f0; }
                        .footer { margin-top: 20px; font-size: 12px; }
                    </style>
                </head>
                <body>
                    <div class="id-card">
                        <div class="header">
                            <img src="${logoUrl}" class="logo">
                            <div>የአብክመ ስራና ክህሎት ቢሮ</div>
                            <div>የስራ ፈላጊ መታወቂያ ካርድ</div>
                        </div>
                        <div class="meta-info">
                            <div>መታወቂያ ቁጥር: ${data.job_seeker_id}</div>
                            <div>የተሰጠበት ቀን: ${data.created_at}</div>
                        </div>
                        <div style="width: 80px; height: 100px; border: 1px solid #ccc; margin-bottom: 10px;"></div>
                        
                        <div class="section-title"><span>Personal Information</span><span>Description</span></div>
                        <div class="row-data"><span>ሙሉ ስም</span><span>${data.first_name} ${data.father_name}</span></div>
                        <div class="row-data"><span>ጾታ</span><span>${data.gender}</span></div>
                        <div class="row-data"><span>እድሜ</span><span>${data.age}</span></div>
                        <div class="row-data"><span>የትምህርት ደረጃ</span><span>${data.educational_level}</span></div>
                        
                        <div class="section-title"><span>Adress</span><span>Description</span></div>
                        <div class="row-data"><span>የተመዘገበበት ወረዳ/ማዕከል </span><span>${branchName}</span></div>
                        
                        <div class="footer">
                            <p>የሰጠው ባለሙያ ስም: ....................</p>
                            <p>ፊርማ: ....................</p>
                        </div>
                    </div>
                    <script>
                        window.print();
                        window.onafterprint = function() { window.close(); };
                    </script>
                </body>
                </html>
            `);
            printWindow.document.close();
        });
    });
});