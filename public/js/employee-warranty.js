$(document).ready(function() {
    // ፕሮጀክትህ ያለበትን ትክክለኛ Path እዚህ ጋር አረጋግጥ
    
    let searchTimeout = null;

    /* -----------------------------------------------------------
        1. ሞዳሉ ሲከፈት መረጃዎችን ማጽዳት
    ----------------------------------------------------------- */
    $('warrantyModal').on('show.bs.modal', function () {
        $('#warrantyForm')[0].reset();
        $('#selected_employee_id').val(''); // Hidden input ማጽዳት
        $('#selectedEmployeePreview').hide();
        $('#searchResults').hide().empty();
        $('#submitBtn').prop('disabled', true);
        $('.custom-file-label').html('ፋይል ይምረጡ...');
    });

    /* -----------------------------------------------------------
        2. ሰራተኛ ፍለጋ (Debounce Logic)
    ----------------------------------------------------------- */
    $(document).on('input', '#empSearchInput', function() {
        const query = $(this).val().trim();
        const $resultsDiv = $('#searchResults');

        clearTimeout(searchTimeout);

        if (query.length < 2) {
            $resultsDiv.hide().empty();
            return;
        }

        searchTimeout = setTimeout(() => {
            fetch(`${BASE_URL}/employee-warranty-search?query=${encodeURIComponent(query)}`, {
                method: 'GET',
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json' }
            })
            .then(res => res.json())
            .then(employees => {
                $resultsDiv.empty().show();

                if (employees.length === 0) {
                    $resultsDiv.append('<div class="list-group-item text-danger">ምንም ሰራተኛ አልተገኘም</div>');
                    return;
                }

                employees.forEach(emp => {
                    const fullName = `${emp.first_name} ${emp.father_name} ${emp.g_father_name}`;
                    
                    // ምስል የማዘጋጀት ሎጂክ
                    let imgFullUrl = 'public/dist/img/avatar5.png';
                    if (emp.employee_image) {
                        imgFullUrl = `${BASE_URL}/serve-file?file=${encodeURIComponent(emp.employee_image)}&type=image`;
                    }

                    // የፍለጋ ውጤት በተን (Button)
                    const $btn = $(`
                        <button type="button" class="list-group-item list-group-item-action d-flex align-items-center">
                            <img src="${imgFullUrl}" width="35" height="35" class="rounded-circle mr-3 border" onerror="this.src='public/dist/img/avatar5.png'">
                            <div>
                                <div class="font-weight-bold text-sm">${fullName}</div>
                                <small class="text-muted">መታወቂያ: ${emp.employee_id}</small>
                            </div>
                        </button>
                    `);

                    // ሰራተኛው ሲመረጥ (Click Event)
                    $btn.on('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation(); // ክሊኩ ወደ ሌላ ቦታ እንዳይሰራጭ
                        
                        // ማሳሰቢያ፡ PHP ሞዴልህ 'uuid' ስለሚመልስ እዚህ ጋር emp.uuid እንጠቀማለን
                        selectEmployee(emp.uuid, fullName, emp.employee_id, imgFullUrl);
                    });

                    $resultsDiv.append($btn);
                });
            })
            .catch(err => {
                console.error("Search Error:", err);
            });
        }, 300);
    });

    /* -----------------------------------------------------------
        3. ሰራተኛ የመምረጥ ተግባር (Selection)
    ----------------------------------------------------------- */
    function selectEmployee(id, name, empId, imageUrl) {
        // 1. መታወቂያውን (UUID) ለሂደን ኢንፑቱ መስጠት (ለፎርም ሰብሚሽን)
        $('#selected_employee_id').val(id); 

        // 2. በፍለጋ ሳጥኑ ላይ ስሙን መጻፍ
        $('#empSearchInput').val(name);
        
        // 3. የተመረጠውን ሰራተኛ Preview ማሳየት
        $('#selectedEmployeePreview').fadeIn();
        $('#display_name').text(name);
        $('#display_id_text').text('መታወቂያ: ' + empId);
        $('#display_image').attr('src', imageUrl);

        // 4. Submit ቁልፍን ማብራት
        $('#submitBtn').prop('disabled', false);

        // 5. የፍለጋ ውጤቶችን ዝርዝር መደበቅ እና ማጽዳት
        $('#searchResults').hide().empty();
        
        // ለዴባጊንግ (አስፈላጊ ካልሆነ ማጥፋት ትችላለህ)
        console.log("Selected Employee ID (UUID):", id);
    }

    /* -----------------------------------------------------------
        4. የፋይል ስም መቀየር (Bootstrap Custom File Input)
    ----------------------------------------------------------- */
    $(document).on('change', '.custom-file-input', function() {
        let fileName = $(this).val().split('\\').pop();
        if (fileName) {
            $(this).next('.custom-file-label').html(fileName);
        }
    });

    // ተጨማሪ፡ ከፍለጋ ውጭ ክሊክ ሲደረግ ውጤቱ እንዲጠፋ
    $(document).on('mousedown', function(e) {
        if (!$(e.target).closest('#searchResults, #empSearchInput').length) {
            $('#searchResults').hide();
        }
    });

    

});