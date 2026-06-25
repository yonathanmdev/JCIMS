// ── Helper: get today's date at midnight for comparison ──────────────────────
function getTodayDate() {
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    return today;
}

// ── Helper: parse date string as local midnight (avoids UTC offset issues) ───
function toLocalMidnight(dateStr) {
    const [y, m, d] = dateStr.split('-').map(Number);
    return new Date(y, m - 1, d);
}

// ── Add Form Validation ──────────────────────────────────────────────────────
$('#addExperienceForm').on('submit', function(e) {
    const startVal        = $('#add_start_date').val();
    const endVal          = $('#add_end_date').val();
    const $ethEndInput    = $('#add_eth_end_date');
    const $ethStartInput  = $('#add_eth_start_date');
    const $errorSpan      = $('#date_error_msg');
    const $startErrorSpan = $('#start_date_error_msg');

    const today = getTodayDate();
    let isDateValid = true;

    // Start date cannot be in the future
    if (startVal && toLocalMidnight(startVal) > today) {
        isDateValid = false;
        $ethStartInput.addClass('is-invalid');
        $startErrorSpan.text('ስህተት፡ የጀመሩበት ቀን ከዛሬ በላይ መሆን የለበትም።').show();
    } else {
        $ethStartInput.removeClass('is-invalid');
        $startErrorSpan.hide();
    }

    // End date cannot be in the future
    if (endVal && toLocalMidnight(endVal) > today) {
        isDateValid = false;
        $ethEndInput.addClass('is-invalid');
        $errorSpan.text('ስህተት፡ ስራ የጨረሱበት ቀን ከዛሬ በላይ መሆን የለበትም።').show();
    }
    // End date cannot be before or equal to start date
    else if (startVal && endVal && toLocalMidnight(startVal) >= toLocalMidnight(endVal)) {
        isDateValid = false;
        $ethEndInput.addClass('is-invalid');
        $errorSpan.text('ስህተት፡ ስራ የጨረሱበት ቀን ከጀመሩበት ቀን ማነስ የለበትም።').show();
    } else {
        $ethEndInput.removeClass('is-invalid');
        $errorSpan.hide();
        $ethEndInput[0].setCustomValidity("");
    }

    if (!isDateValid) {
        e.preventDefault();
        e.stopPropagation();
    }

    if (!this.checkValidity()) {
        e.preventDefault();
        e.stopPropagation();
    }

    $(this).addClass('was-validated');
});

// Clear add form errors on date change
$(document).on('change', '#add_eth_start_date', function() {
    $('#add_eth_start_date').removeClass('is-invalid');
    $('#start_date_error_msg').hide();
});

$(document).on('change', '#add_eth_end_date', function() {
    $('#add_eth_end_date').removeClass('is-invalid');
    $('#date_error_msg').hide();
});

// ── Edit Button Click ────────────────────────────────────────────────────────
$(document).on('click', '.edit-exp-btn', function () {
    const experienceId = $(this).data('uuid');
    const employeeUuid = $(this).data('employee');

    // Clear previous data and errors
    $('#editExperienceForm')[0].reset();
    $('#edit_date_error_msg').hide();
    $('#edit_start_date_error_msg').hide();
    $('#edit_eth_start_date').val('');
    $('#edit_eth_end_date').val('');

    // Set hidden fields
    $('#edit_experience_id').val(experienceId);
    $('#edit_employee_uuid').val(employeeUuid);

    $.ajax({
        url: `${BASE_URL}/employee-experience-show?uuid=${experienceId}`,
        method: 'GET',
        dataType: 'json',
        success: function (response) {
            if (response.status !== 'success') {
                alert(response.message ?? 'መረጃ አልተገኘም።');
                return;
            }

            const exp = response.data;

            // Fill text fields
            $('#edit_company_name').val(exp.company_name);
            $('#edit_job_title').val(exp.job_title);
            $('#edit_employment_type').val(exp.employment_type);

            // Fill hidden Gregorian inputs (submitted with form)
            $('#edit_start_date').val(exp.start_date);
            $('#edit_end_date').val(exp.end_date);

            // Fill Ethiopian display fields (converted by server)
            $('#edit_eth_start_date').val(exp.eth_start_date);
            $('#edit_eth_end_date').val(exp.eth_end_date);
        },
        error: function (xhr) {
            console.log('HTTP Error:', xhr.status);
            console.log('Response:', xhr.responseText);
            alert('ከሰርቨር ጋር መገናኘት አልተቻለም።');
        }
    });
});

// ── Edit Form Validation ─────────────────────────────────────────────────────
$('#editExperienceForm').on('submit', function(e) {
    const startVal        = $('#edit_start_date').val();
    const endVal          = $('#edit_end_date').val();
    const $ethEndInput    = $('#edit_eth_end_date');
    const $ethStartInput  = $('#edit_eth_start_date');
    const $errorSpan      = $('#edit_date_error_msg');
    const $startErrorSpan = $('#edit_start_date_error_msg');

    const today = getTodayDate();
    let isDateValid = true;

    // Start date cannot be in the future
    if (startVal && toLocalMidnight(startVal) > today) {
        isDateValid = false;
        $ethStartInput.addClass('is-invalid');
        $startErrorSpan.text('ስህተት፡ የጀመሩበት ቀን ከዛሬ በላይ መሆን የለበትም።').show();
    } else {
        $ethStartInput.removeClass('is-invalid');
        $startErrorSpan.hide();
    }

    // End date cannot be in the future
    if (endVal && toLocalMidnight(endVal) > today) {
        isDateValid = false;
        $ethEndInput.addClass('is-invalid');
        $errorSpan.text('ስህተት፡ ስራ የጨረሱበት ቀን ከዛሬ በላይ መሆን የለበትም።').show();
    }
    // End date cannot be before or equal to start date
    else if (startVal && endVal && toLocalMidnight(startVal) >= toLocalMidnight(endVal)) {
        isDateValid = false;
        $ethEndInput.addClass('is-invalid');
        $errorSpan.text('ስህተት፡ ስራ የጨረሱበት ቀን ከጀመሩበት ቀን ማነስ የለበትም።').show();
    } else {
        $ethEndInput.removeClass('is-invalid');
        $errorSpan.hide();
        $ethEndInput[0].setCustomValidity("");
    }

    if (!isDateValid) {
        e.preventDefault();
        e.stopPropagation();
    }

    if (!this.checkValidity()) {
        e.preventDefault();
        e.stopPropagation();
    }

    $(this).addClass('was-validated');
});

// Clear edit form errors on date change
$(document).on('change', '#edit_eth_start_date', function() {
    $('#edit_eth_start_date').removeClass('is-invalid');
    $('#edit_start_date_error_msg').hide();
});

$(document).on('change', '#edit_eth_end_date', function() {
    $('#edit_eth_end_date').removeClass('is-invalid');
    $('#edit_date_error_msg').hide();
});

/* -----------------------------------------------------------
    Global Variables & Config
----------------------------------------------------------- */
let debounceTimer;

function setupAutocomplete(inputId, listId, endpoint, dataKey, editInputId, editListId, editEndpoint, editDataKey) {

    function bindInput($input, $list, ep, key, focusNextId) {

        $input.on('focus', function () {
            $('.list-group').not($list).hide().empty();
        });

        $input.on('input', function () {
            const q = $(this).val().trim();
            clearTimeout(debounceTimer);

            if (q.length < 2) {
                $list.hide().empty();
                return;
            }

            debounceTimer = setTimeout(() => {
                fetch(`${BASE_URL}/${ep}?query=${encodeURIComponent(q)}`, {
                    method: 'GET',
                    credentials: 'same-origin',
                    headers: { 'Accept': 'application/json' }
                })
                .then(res => res.json())
                .then(data => {
                    $('.list-group').not($list).hide().empty();
                    $list.empty().show();

                    if (data.length > 0) {
                        data.forEach(item => {
                            const value = item[key];
                            const $btn = $(`
                                <button type="button" class="list-group-item list-group-item-action py-2 d-flex justify-content-between align-items-center">
                                    <span>${value}</span>
                                    <small class="text-muted">ያለ (Existing)</small>
                                </button>
                            `);

                            $btn.on('click', function (e) {
                                e.preventDefault();
                                e.stopPropagation();

                                $input.val(value);
                                $list.hide().empty();

                                if (focusNextId) {
                                    $(`#${focusNextId}`).focus();
                                }
                            });

                            $list.append($btn);
                        });
                    } else {
                        $list.hide();
                    }
                })
                .catch(err => console.error(`${ep} Error:`, err));
            }, 300);
        });
    }

    // ── Add form ──────────────────────────────────────────────────────────────
    bindInput(
        $(`#${inputId}`),
        $(`#${listId}`),
        endpoint,
        dataKey,
        inputId === 'add_company_name' ? 'add_job_title' : null
    );

    // ── Edit form ─────────────────────────────────────────────────────────────
    if (editInputId && editListId && editEndpoint && editDataKey) {
        bindInput(
            $(`#${editInputId}`),
            $(`#${editListId}`),
            editEndpoint,
            editDataKey,
            editInputId === 'edit_company_name' ? 'edit_job_title' : null
        );
    }
}

/* -----------------------------------------------------------
    Initialization
----------------------------------------------------------- */
$(document).ready(function () {

    // ለኩባንያ ስም
    setupAutocomplete(
        'add_company_name',  'company_suggestions',      'employee-company-search', 'company_name',
        'edit_company_name', 'edit_company_suggestions', 'employee-company-search', 'company_name'
    );

    // ለስራ መደብ (Job Title)
    setupAutocomplete(
        'add_job_title',  'job_suggestions',      'employee-job-search', 'job_title',
        'edit_job_title', 'edit_job_suggestions', 'employee-job-search', 'job_title'
    );

    // ከፊልዶቹ ውጭ ሲነካ ዝርዝሮች እንዲጠፉ
    $(document).on('click', function (e) {
        if (!$(e.target).closest('.form-group').length) {
            $('.list-group').hide().empty();
        }
    });
});