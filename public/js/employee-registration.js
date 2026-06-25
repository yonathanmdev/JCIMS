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
$(document).ready(function() {

    // Initialize form validation
    $('#employeeRegistrationModal form').validate({
        ignore: ':hidden:not([required])',
        rules: {
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
            g_father_name: {
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
                noFutureDate: true,
                notBeforeBirthDate: true,  // ← add this
                minAgeAtEmployment: true  
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
                required: true
            },
            employee_image: {
                required: true,
                extension: "jpg|jpeg|png|gif",
                filesize: 5242880
            },
            employee_file201: {
                required: true,
                extension: "pdf|doc|docx|jpg|jpeg|png",
                filesize: 10485760
            },
            remark: {
                maxlength: 500
            }
        },
        messages: {
            first_name: {
                required: "ስም አስፈላጊ ነው።",
                minlength: "ስም ቢያንስ 2 ፊደል መሆን አለበት።",
                maxlength: "ስም ከ50 ፊደል መብለጥ የለበትም።"
            },
            father_name: {
                required: "የአባት ስም አስፈላጊ ነው።",
                minlength: "የአባት ስም ቢያንስ 2 ፊደል መሆን አለበት።",
                maxlength: "የአባት ስም ከ50 ፊደል መብለጥ የለበትም።"
            },
            g_father_name: {
                required: "የአያት ስም አስፈላጊ ነው።",
                minlength: "የአያት ስም ቢያንስ 2 ፊደል መሆን አለበት።",
                maxlength: "የአያት ስም ከ50 ፊደል መብለጥ የለበትም።"
            },
            mother_name: {
                required: "የእናት ሙሉ ስም አስፈላጊ ነው።",
                minlength: "የእናት ስም ቢያንስ 2 ፊደል መሆን አለበት።",
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
                minlength: "የጋብቻ ሁኔታ ቢያንስ 2 ፊደል መሆን አለበት።",
                maxlength: "የጋብቻ ሁኔታ ከ50 ፊደል መብለጥ የለበትም።"
            },
            job_property_id: {
                required: "የስራ መደብ መምረጥ አስፈላጊ ነው።"
            },
            level_of_education: {
                required: "የትምህርት ደረጃ አስፈላጊ ነው።",
                minlength: "የትምህርት ደረጃ ቢያንስ 2 ፊደል መሆን አለበት።",
                maxlength: "የትምህርት ደረጃ ከ100 ፊደል መብለጥ የለበትም"
            },
            date_of_employed: {
                required: "የቅጥር ቀን አስፈላጊ ነው።",
                date: "ትክክለኛ ቀን ያስገቡ።",
                noFutureDate: "የቅጥር ቀን ወደፊት ሊሆን አይችልም።",
                notBeforeBirthDate: "የቅጥር ቀን ከልደት ቀን በፊት ሊሆን አይችልም።",  // ← add this
                minAgeAtEmployment: "ሰራተኛው ሲቀጠር ቢያንስ 18 ዓመት መሆን አለበት።"
            },
            employment_situation: {
                required: "የቅጥር ሁኔታ አስፈላጊ ነው።",
                minlength: "የቅጥር ሁኔታ ቢያንስ 2 ፊደል መሆን አለበት።",
                maxlength: "የቅጥር ሁኔታ ከ100 ፊደል መብለጥ የለበትም"
            },
            displin_situation: {
                required: "የዲሲፕሊን ሁኔታ አስፈላጊ ነው።",
                minlength: "የዲሲፕሊን ሁኔታ ቢያንስ 2 ፊደል መሆን አለበት።",
                maxlength: "የዲሲፕሊን ሁኔታ ከ100 ፊደል መብለጥ የለበትም"
            },
            competency_situation: {
                required: "የብቃት ሁኔታ አስፈላጊ ነው።"
            },
            employee_image: {
                required: "ፎቶ አስፈላጊ ነው።",
                extension: "ፎቶ ፋይል መሆን አለበት (jpg, jpeg, png, gif)።",
                filesize: "ፎቶ ፋይል 5MB ከመብለጫ ቀር መሆን አለበት።"
            },
            employee_file201: {
                required: "የት/ት ማስረጃ እና ሌሎች ፋይል አስፈላጊ ነው።",
                extension: "ትክክለኛ ፋይል አይነት ያስገቡ (pdf, doc, docx, jpg, jpeg, png)።",
                filesize: "ፋይል 10MB ከመብለጫ ቀር መሆን አለበት።"
            }
        },
        errorElement: 'span',
        errorPlacement: function (error, element) {
            error.addClass('invalid-feedback');
            const isHidden = $(element).attr('type') === 'hidden'
                          || $(element).hasClass('d-none');
            if (isHidden) {
                const ethField = $('[data-gregorian="#' + $(element).attr('id') + '"]');
                if (ethField.length) {
                    ethField.closest('.form-group').append(error);
                    return;
                }
            }
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
                    return;
                }
            }
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
                    return;
                }
            }
            $el.removeClass('is-invalid');
        },
        submitHandler: function(form) {
            const submitBtn = $(form).find('button[type="submit"]');
            submitBtn.prop('disabled', true)
                     .html('<i class="fas fa-spinner fa-spin"></i> በመመዝገብ ላይ...');
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

   // Make modal draggable — deferred to avoid forced reflow
$('#employeeRegistrationModal').on('shown.bs.modal', function() {
    const $dialog = $(this).find('.modal-dialog');
    const $header = $(this).find('.modal-header');
    requestAnimationFrame(function() {
        $dialog.draggable({
            handle: '.modal-header',
            containment: 'window',
            scroll: false
        });
        $header.css('cursor', 'move');
    });
});

// Reset form when modal is closed
$('#employeeRegistrationModal').on('hidden.bs.modal', function() {
    const $form = $('#employeeRegistrationModal form');

    // Reset native form values
    $form[0].reset();

    // Reset jQuery Validate state (removes error classes and messages)
    $form.validate().resetForm();

    // Remove any leftover is-invalid classes and error spans
    $form.find('.is-invalid').removeClass('is-invalid');
    $form.find('.invalid-feedback').remove();

    // Reset Ethiopian date display fields explicitly
    // (form.reset() won't reset readonly fields in some browsers)
    $form.find('.ethiopian-date').val('');

    // Reset hidden Gregorian date fields explicitly
    $form.find('#birth_date, #date_of_employed').val('');

    // Reset efficiency level field
    $form.find('#level_of_effeciency').val('');

    // Reset file input previews if any
    $form.find('input[type="file"]').val('');

    // Reset modal position (in case user dragged it)
    $(this).find('.modal-dialog').removeAttr('style');
});

});
document.addEventListener('DOMContentLoaded', function () {
  const jobSelect        = document.getElementById('job_property_id');
  const guarantorSection = document.getElementById('guarantor-section');

  const guarantorName   = document.getElementById('guarantor_name');
  const guarantorPhone  = document.getElementById('guarantor_phone');
  const guarantorLetter = document.getElementById('guarantor_letter');

  function validatePhone() {
    const digits = guarantorPhone.value.replace(/\D/g, '');
    if (guarantorPhone.required && digits.length !== 10) {
      guarantorPhone.setCustomValidity('ስልክ ቁጥሩ ትክክለኛ 10 አሃዝ መሆን አለበት።');
    } else {
      guarantorPhone.setCustomValidity('');
    }
  }

  function toggleGuarantor() {
    const selected   = jobSelect.options[jobSelect.selectedIndex];
    const wastna     = selected?.dataset?.wastna?.trim();
    const isRequired = wastna === 'ተያዥ የሚያስፈልገዉ';

    guarantorSection.style.display = isRequired ? 'block' : 'none';

    guarantorName.required   = isRequired;
    guarantorPhone.required  = isRequired;
    guarantorLetter.required = isRequired;

    if (!isRequired) {
      guarantorName.value   = '';
      guarantorPhone.value  = '';
      guarantorLetter.value = '';
      guarantorPhone.setCustomValidity(''); // clear any lingering error
    }

    validatePhone(); // re-evaluate when job changes
  }

  guarantorPhone.addEventListener('input', validatePhone);

  jobSelect.addEventListener('change', toggleGuarantor);
  toggleGuarantor();
});