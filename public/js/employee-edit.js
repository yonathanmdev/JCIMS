$(document).ready(function() {
    // Initialize form validation
    $('#employee-edit-form').validate({
       ignore: ':hidden:not(.d-none)', // ← validate d-none fields but still ignore truly hidden ones
        rules: {
            pension_number: {
                maxlength: 50
            },
            first_name: {
                required: true,
                minlength: 2,
                maxlength: 50
            },
            father_name: {
                required: true,
                minlength: 2,
                maxlength: 50
            },
            last_name: {
                required: true,
                minlength: 2,
                maxlength: 50
            },
            mother_name: {
                required: true,
                minlength: 2,
                maxlength: 100
            },
            sex: {
                required: true
            },
            birth_date: {
                required: true,
                date: true,
                ageRange: [18, 65]
            },
            eth_birth_date: {
               
            },
            phone_number: {
                required: true,
                pattern: /^[0-9]{10}$/
            },
            yegabcha_huneta: {
                required: true
            },
            job_property_id: {
                required: true
            },
            date_of_employed: {
                required: true,  
                date: true,
                noFutureDate: true, // Add this custom rule
                notBeforeBirthDate: true,  // ← add this
                minAgeAtEmployment: true    // ← check 2
            },
            level_of_education: {
                required: true
            },
            department: {
                maxlength: 100
            },
            employment_situation: {
                required: true
            },
            immidate_boss: {
                maxlength: 100
            },
            annual_rest: {
                number: true,
                min: 0,
                max: 365
            },
            effeciency: {
                number: true,
                min: 0,
                max: 100
            },
            no_of_files_in_folder: {
                number: true,
                min: 0
            },
            displin_situation: {
                required: true
            },
            competency_situation: {
                maxlength: 200
            },
            // ── Guarantor (toggled dynamically) ──────────────────────────────
guarantor_name: {
    required: false,
    minlength: 2,
    maxlength: 255
},
guarantor_phone: {
    required: false,
    pattern: /^[0-9]{10}$/
},
            remark: {
                maxlength: 500
            }
        },
        messages: {
           
            first_name: {
                required: "ስም አስፈላጊ ነው።",
                minlength: "ስም ቢያንስ 2  ፊደል መሆን አለበት።",
                maxlength: "ስም  ከ50 ፊደል መብለጥ የለበትም።"
            },
            father_name: {
                required: "የአባት ስም አስፈላጊ ነው።",
                minlength: "የአባት ስም ቢያንስ 2  ፊደል መሆን አለበት።",
                maxlength: "የአባት ስም  ከ50 ፊደል መብለጥ የለበትም።"
            },
            last_name: {
                required: "የአያት ስም አስፈላጊ ነው።",
                minlength: "የአያት ስም ቢያንስ 2  ፊደል መሆን አለበት።",
                maxlength: "የአያት ስም  ከ50 ፊደል መብለጥ የለበትም።"
            },
            mother_name: {
                required: "የእናት ሙሉ ስም አስፈላጊ ነው።",
                minlength: "የእናት ስም ቢያንስ 2  ፊደል መሆን አለበት።",
                maxlength: "የእናት ስም ከ100 ፊደል መብለጥ የለበትም"
            },
            sex: {
                required: "ጾታ መምረጥ አስፈላጊ ነው።"
            },
            birth_date: {
                required: "የትውልድ ቀን አስፈላጊ ነው።",
                date: "ትክክለኛ ቀን ያስገቡ።",
                ageRange: "ዕድሜ ከ 18 እስከ 65 አመት መሆን አለበት።"
            },
            phone_number: {
                required: "ስልክ ቁጥር አስፈላጊ ነው።",
                pattern: "ስልክ ቁጥር ትክክለኛ 10 አሃዝ መሆን አለበት።"
            },
            yegabcha_huneta: {
                required: "የጋብቻ ሁኔታ አስፈላጊ ነው።",
                minlength: "የጋብቻ ሁኔታ ቢያንስ 2  ፊደል መሆን አለበት።",
                maxlength: "የጋብቻ ሁኔታ  ከ50 ፊደል መብለጥ የለበትም።"
            },
            job_property_id: {
                required: "የስራ መደብ መምረጥ አስፈላጊ ነው።"
            },
            level_of_education: {
                required: "የትምህርት ደረጃ አስፈላጊ ነው።",
                minlength: "የትምህርት ደረጃ ቢያንስ 2  ፊደል መሆን አለበት።",
                maxlength: "የትምህርት ደረጃ ከ100 ፊደል መብለጥ የለበትም"
            },
            employment_situation: {
                required: "Employment Situation አስፈላጊ ነው።",
                minlength: "Employment Situation ቢያንስ 2  ፊደል መሆን አለበት።",
                maxlength: "Employment Situation ከ100 ፊደል መብለጥ የለበትም"
            },
            date_of_employed: {
                required: "የቅጥር ቀን አስፈላጊ ነው።",
                date:'ትክክለኛ ቀን ያስገቡ።',
                noFutureDate: "የቅጥር ቀን ወደፊት ሊሆን አይችልም።",
                notBeforeBirthDate: "የቅጥር ቀን ከልደት ቀን በፊት ሊሆን አይችልም።",  // ← add this
                minAgeAtEmployment: "ሰራተኛው ሲቀጠር ቢያንስ 18 ዓመት መሆን አለበት።"

            },
            displin_situation: {
                required: "የዲሲፕሊን ሁኔታ አስፈላጊ ነው።",
                minlength: "የዲሲፕሊን ሁኔታ ቢያንስ 2  ፊደል መሆን አለበት።",
                maxlength: "የዲሲፕሊን ሁኔታ ከ100 ፊደል መብለጥ የለበትም"
            },
            // ── Guarantor messages ────────────────────────────────────────────
guarantor_name: {
    required: "የተያዥ ሙሉ ስም አስፈላጊ ነው።",
    minlength: "ስም ቢያንስ 2 ፊደል መሆን አለበት።"
},
guarantor_phone: {
    required: "የተያዥ ስልክ ቁጥር አስፈላጊ ነው።",
    pattern:  "ስልክ ቁጥሩ ትክክለኛ 10 አሃዝ መሆን አለበት።"
}
        },
    errorElement: 'span',
errorPlacement: function (error, element) {
    error.addClass('invalid-feedback');

    // If hidden Gregorian field, show error on paired Ethiopian field
    const isHidden = $(element).attr('type') === 'hidden' 
                  || $(element).hasClass('d-none');
    
    if (isHidden) {
        const ethField = $('[data-gregorian="#' + $(element).attr('id') + '"]');
        if (ethField.length) {
            ethField.closest('.form-group').append(error);
            return; // stop here
        }
    }

    // Default behavior for all other fields
    $(element).closest('.form-group').append(error);
},

highlight: function (element, errorClass, validClass) {
    const $el = $(element);
    const isHidden = $el.attr('type') === 'hidden' 
                  || $el.hasClass('d-none');

    if (isHidden) {
        const ethField = $('[data-gregorian="#' + $el.attr('id') + '"]');
        if (ethField.length) {
            ethField.addClass('is-invalid');
            return; // stop here
        }
    }

    // Default behavior for all other fields
    $el.addClass('is-invalid');
},

unhighlight: function (element, errorClass, validClass) {
    const $el = $(element);
    const isHidden = $el.attr('type') === 'hidden' 
                  || $el.hasClass('d-none');

    if (isHidden) {
        const ethField = $('[data-gregorian="#' + $el.attr('id') + '"]');
        if (ethField.length) {
            ethField.removeClass('is-invalid');
            return; // stop here
        }
    }

    // Default behavior for all other fields
    $el.removeClass('is-invalid');
},

submitHandler: function(form) {
    const submitBtn = $(form).find('button[type="submit"]');
    const originalText = submitBtn.html();
    submitBtn.prop('disabled', true)
             .html('<i class="fas fa-spinner fa-spin"></i> በማስተካከል ላይ...');
    form.submit();
}
    });

    // Calculate efficiency level based on efficiency percentage
    $('#effeciency').on('input', function() {
        const value = parseFloat($(this).val());
        let level = '';
       if (value >= 95 && value <= 100) {
            level = 'በጣም ከፍተኛ';
        }
        else if (value >= 80) {
            level = 'ከፍተኛ';
        } else if (value >= 65) {
            level = 'መካከለኛ';
        } else if (value >= 50) {
            level = 'አጥጋቢ';
        } else {
            level = 'ዝቅተኛ';
        }
        $('#level_of_effeciency').val(level);
    });

   $.validator.addMethod('ageRange', function(value, element, param) {
    if (this.optional(element)) return true;
    var birthDate = new Date(value);
    if (isNaN(birthDate.getTime())) return false;
    var today = new Date();
    var age = today.getFullYear() - birthDate.getFullYear();
    var monthDiff = today.getMonth() - birthDate.getMonth();
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }
    return age >= param[0] && age <= param[1];
}, 'ዕድሜ ከ 18 እስከ 65 አመት መሆን አለበት።');

$.validator.addMethod('notBeforeBirthDate', function(value, element) {
    if (this.optional(element)) return true;
    var employedDate = new Date(value);
    if (isNaN(employedDate.getTime())) return false;
    var birthDateVal = $('#birth_date').val();
    if (!birthDateVal || birthDateVal === '') return true; // skip if not set
    var birthDate = new Date(birthDateVal);
    if (isNaN(birthDate.getTime())) return true; // skip if invalid
    return employedDate > birthDate;
}, 'የቅጥር ቀን ከልደት ቀን በፊት ሊሆን አይችልም።');

$.validator.addMethod('minAgeAtEmployment', function(value, element) {
    if (this.optional(element)) return true;
    var employedDate = new Date(value);
    if (isNaN(employedDate.getTime())) return false;
    var birthDateVal = $('#birth_date').val();
    if (!birthDateVal || birthDateVal === '') return true; // skip if not set
    var birthDate = new Date(birthDateVal);
    if (isNaN(birthDate.getTime())) return true; // skip if invalid
    var minDate = new Date(birthDate);
    minDate.setFullYear(minDate.getFullYear() + 18);
    return employedDate >= minDate;
}, 'ሰራተኛው ሲቀጠር ቢያንስ 18 ዓመት መሆን አለበት።');

$.validator.addMethod('noFutureDate', function(value, element) {
    if (this.optional(element)) return true;
    var parts = value.split('-');
    if (parts.length !== 3) return false;
    var inputDate = new Date(parts[0], parts[1] - 1, parts[2]); // ← local time, no UTC shift
    if (isNaN(inputDate.getTime())) return false;
    var today = new Date();
    today.setHours(0, 0, 0, 0);
    return inputDate <= today;
}, 'የቅጥር ቀን ወደፊት ሊሆን አይችልም።');
// Custom validation methods — outside document.ready
$.validator.addMethod('filesize', function(value, element, param) {
    return this.optional(element) || (element.files[0].size <= param);
}, 'ፋይል ከፍተኛ ነው።');

  // Job change confirmation
$('#job_property_id').on('change', function() {
    const $select = $(this);
    const currentJobId = $select.data('current-job');
    const newJobId = $select.val();

    if (currentJobId && newJobId && currentJobId !== newJobId) {
        const currentJobName = $select.find('option[value="' + currentJobId + '"]').text().replace(' (የተያዘ)', '');
        const newJobName = $select.find('option:selected').text().replace(' (የተያዘ)', '');

        $('#job-confirm-from').text(currentJobName);
        $('#job-confirm-to').text(newJobName);

        let confirmed = false;
        const $modal = $('#jobChangeConfirmModal');
        $modal.modal('show');

        $('#jobConfirmYes').off('click').on('click', function() {
            confirmed = true;
            $modal.modal('hide');
            $select.data('current-job', newJobId);
        });

        $modal.off('hidden.bs.modal').on('hidden.bs.modal', function() {
            if (!confirmed) $select.val(currentJobId);
        });
    }
});

// Set current job on page load
$('#job_property_id').data('current-job', $('#job_property_id').val());
});

// ── 5. Guarantor toggle (vanilla JS) ─────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    const jobSelect        = document.getElementById('job_property_id');
    const guarantorSection = document.getElementById('guarantor-section');
    const guarantorName    = document.getElementById('edit_guarantor_name');
    const guarantorPhone   = document.getElementById('edit_guarantor_phone');
    const guarantorLetter  = document.getElementById('edit_guarantor_letter');

    const originalJobId        = jobSelect.value;
    const hasExistingGuarantor = guarantorSection.dataset.hasGuarantor === '1';

    window.toggleGuarantor = function () {
        const selected   = jobSelect.options[jobSelect.selectedIndex];
        const wastna     = selected?.dataset?.wastna?.trim();
        const isRequired = wastna === 'ተያዥ የሚያስፈልገዉ';

        const jobChanged         = jobSelect.value !== originalJobId;
        const existingStillValid = hasExistingGuarantor && !jobChanged;

        guarantorSection.style.display = isRequired ? 'block' : 'none';

        guarantorName.required   = isRequired;
        guarantorPhone.required  = isRequired;

        // File required only if no existing file or job changed
        guarantorLetter.required = isRequired && !existingStillValid;

        // Sync required state with jQuery validate rules
        const $validator = $('#employee-edit-form').data('validator');
        if ($validator) {
            $validator.settings.rules.guarantor_name  = { required: isRequired, minlength: 2, maxlength: 255 };
            $validator.settings.rules.guarantor_phone = { required: isRequired, pattern: /^[0-9]{10}$/ };
        }

        if (!isRequired) {
            guarantorName.value   = '';
            guarantorPhone.value  = '';
            guarantorLetter.value = '';
        }
    };

    jobSelect.addEventListener('change', window.toggleGuarantor);
    window.toggleGuarantor();
});