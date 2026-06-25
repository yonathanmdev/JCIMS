$(document).ready(function() {
    let searchTimeout = null;

    // 1. Capture the source attribute cleanly when the modal is opened
    $('#employeeSearchModal').on('show.bs.modal', function (event) {
        // 'relatedTarget' is the native Bootstrap way to find the element that opened the modal
        const triggeringLink = $(event.relatedTarget); 
        const sourceValue = triggeringLink.data('source') || ''; 
        
        // Security Sanitization: Ensure the value is strictly alphanumeric 
        // to prevent any local DOM manipulation or XSS edge cases.
        const sanitizedSource = sourceValue.replace(/[^a-zA-Z0-9_-]/g, '');

        // Securely bind the sanitized source to the modal element's current state
        $(this).data('current-source', sanitizedSource);
    });

    $(document).on('input', '#emp_search_input', function() {
        const q = $(this).val().trim();
        const $list = $('#emp_search_suggestions');
        const $error = $('#search_error_msg');
        
        // 2. Fetch the source safely from the modal wrapper state context
        const sanitizedSource = $('#employeeSearchModal').data('current-source') || '';
        
        clearTimeout(searchTimeout);

        if (q.length < 2) {
            $list.hide().empty();
            return;
        }

        searchTimeout = setTimeout(() => {
            // ተመሳሳዩን የ Fetch አማራጮች (Options) እንጠቀም
            // We append the source to the query params so the backend API knows the intent if needed
            fetch(`${BASE_URL}/employee-search-api?query=${encodeURIComponent(q)}&source=${encodeURIComponent(sanitizedSource)}`, {
                method: 'GET',
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json' }
            })
            .then(res => {
                if (!res.ok) throw new Error('Network response was not ok');
                return res.json();
            })
            .then(employees => {
                $list.empty().show();

                if (employees.length > 0) {
                    employees.forEach(emp => {
                        // ስሙን በ loop ውስጥ ነው መገንባት ያለብን
                        const full_name = `${emp.first_name} ${emp.father_name} ${emp.g_father_name}`;
                    
                        const $btn = $(`
                            <button type="button" class="list-group-item list-group-item-action py-2 d-flex justify-content-between align-items-center">
                                <span>${full_name}</span>
                                <span class="badge badge-light border text-muted">${emp.employee_id || ''}</span>
                            </button>
                        `);

                        $btn.on('click', function() {
                            $(this).addClass('active');
                            
                            // Adjusting the redirect location dynamically if the source matches 'onleave'
                            let destinationUrl = `${BASE_URL}/employee-experience/${emp.uuid}`;
                            if (sanitizedSource === 'onleave') {
                                destinationUrl = `${BASE_URL}/employee-leave/${emp.uuid}`;
                            }
                              if (sanitizedSource === 'promotion') {
                                destinationUrl = `${BASE_URL}/employee-promotion/${emp.uuid}`;
                            }
                            
                            window.location.href = destinationUrl;
                        });

                        $list.append($btn);
                    });
                } else {
                    $list.append('<li class="list-group-item small text-danger text-center">ምንም ሰራተኛ አልተገኘም</li>');
                }
            })
            .catch(err => {
                console.error("Search Error:", err);
                $error.text('መረጃ በመፈለግ ላይ ስህተት ተፈጥሯል።').show();
            });
        }, 300);
    });

    // 3. Clear inputs, suggestions, and clean up state memory upon modal close
    $('#employeeSearchModal').on('hidden.bs.modal', function () {
        $('#emp_search_input').val('');
        $('#emp_search_suggestions').hide().empty();
        $(this).removeData('current-source'); // Destroys the state to prevent bleedover on the next link click
    });
});