function buildViewRow(label, value) {
    if (value === null || value === undefined || value === '') return '';
    return `
        <div class="col-12 col-sm-6 col-md-4 mb-3">
            <small class="text-muted d-block">${label}</small>
            <div class="font-weight-bold">${value}</div>
        </div>
    `;
}

function renderJobseekerView(js) {
    const genderLabel = js.gender;
    const physicalConditionLabel = js.physical_condition === '1' ? 'ያለበት' : 'የሌለበት';
    const haveExpLabel = js.haveexp === '1' ? 'አለ' : 'የለም';

    let html = '<div class="row">';
    html += buildViewRow('ስም', js.first_name);
    html += buildViewRow('የአባት ስም', js.father_name);
    html += buildViewRow('የአያት ስም', js.last_name);
    html += buildViewRow('ጾታ', genderLabel);
    html += buildViewRow('እድሜ', js.age);
    html += buildViewRow('የስራ ፈላጊ ሁኔታ', js.srafelagi_huneta);
    html += buildViewRow('Labor ID', js.Labor_ID);
    html += buildViewRow('የጋብቻ ሁኔታ', js.maritalstatus);
    html += buildViewRow('FAN', js.FAN);
    html += buildViewRow('መንደር', js.mender);
    html += buildViewRow('የቀበሌ መታወቂያ ቁጥር', js.kebele_id_no);
    html += buildViewRow('የሚኖርበት አካባቢ', js.residence_status);
    html += '</div><hr>';

    html += '<div class="row">';
    html += buildViewRow('የትምህርት ደረጃ', js.educational_level);
    html += buildViewRow('የት/ቤቱ ዓይነት', js.school_type);
    html += buildViewRow('የተመረቀበት ዲፓርትመንት', js.educated_dpt);
    html += buildViewRow('ትምህርት ያጠናቀቀበት ዓመት', js.education_trmnet_finsh_year);
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
    html += buildViewRow('የሚችሉት ቋንቋ', js.language);
    html += buildViewRow('አሁን መስራት የሚፈልጉት', js.wageorself);
    html += buildViewRow('የእናት ሙሉ ስም', js.mothername);
    html += '</div><hr>';

    html += '<div class="row">';
    html += buildViewRow('የዘርፍ ምርጫ 1', js.choice_sector1_name);
    html += buildViewRow('የሙያ ምርጫ 1', js.sub_choose1_name);
    html += buildViewRow('የዘርፍ ምርጫ 2', js.choice_sector2_name);
    html += buildViewRow('የሙያ ምርጫ 2', js.sub_choose2_name);
    html += buildViewRow('የዘርፍ ምርጫ 3', js.choice_sector3_name);
    html += buildViewRow('የሙያ ምርጫ 3', js.sub_choose3_name);
    html += buildViewRow('በግብርና ዘርፍ ልምድ', js.agri_business_experience_status === '1' ? 'አለ' : 'የለም');
    html += buildViewRow('የግብርና ልምድ (ዓመት)', js.agri_business_experience);
    html += buildViewRow('በስር የሚተዳደር ቤተሰብ', js.has_dependents === '1' ? 'አለ' : '');
    html += buildViewRow('የሚተዳደረው ቤተሰብ ብዛት', js.number_of_dependents);
    html += buildViewRow('ከ5 ዓመት በታች ህፃናት ብዛት', js.children_under_five);
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