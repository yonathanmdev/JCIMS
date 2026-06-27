$(document).ready(function() {

    // Initialize form validation
    $('#jobseekerRegistrationModal form').validate({
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
            sex: {
                required: true
            },
            
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
            sex: {
                required: "ጾታ መምረጥ አስፈላጊ ነው።"
            },
        },
        errorElement: 'span',
        errorPlacement: function (error, element) {
            error.addClass('invalid-feedback');
            $(element).closest('.form-group').append(error);
        },
        highlight: function (element, errorClass, validClass) {
            const $el = $(element);
            $el.addClass('is-invalid');
        },
        unhighlight: function (element, errorClass, validClass) {
            const $el = $(element);
            $el.removeClass('is-invalid');
        },
        submitHandler: function(form) {
            const submitBtn = $(form).find('button[type="submit"]');
            submitBtn.prop('disabled', true)
                     .html('<i class="fas fa-spinner fa-spin"></i> በመመዝገብ ላይ...');
            form.submit();
        }
    });

   // Make modal draggable — deferred to avoid forced reflow
$('#jobseekerRegistrationModal').on('shown.bs.modal', function() {
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
$('#jobseekerRegistrationModal').on('hidden.bs.modal', function() {
    const $form = $('#jobseekerRegistrationModal form');

    // Reset native form values
    $form[0].reset();

    // Reset jQuery Validate state (removes error classes and messages)
    $form.validate().resetForm();

    // Remove any leftover is-invalid classes and error spans
    $form.find('.is-invalid').removeClass('is-invalid');
    $form.find('.invalid-feedback').remove();


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