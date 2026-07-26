$(document).on('click', '.enterprise-type-choice', function () {
    const type = String($(this).data('type')); // force string — jQuery auto-converts "0"/"1" to numbers
    const targetModal = type === '0' ? '#individualEnterpriseModal' : '#associationEnterpriseModal';

    $('#enterpriseTypeChooserModal').one('hidden.bs.modal', function () {
        $(targetModal).modal('show');
    });

    $('#enterpriseTypeChooserModal').modal('hide');
});

$(function () {
    let searchTimer = null;

    // Search input typing handler
    $(document).on('input', '.linked-entity-search', function () {
        const $input = $(this);
        const $group = $input.closest('.linked-entity-group');
        const $modal = $input.closest('.modal');
        const type = $modal.data('enterprise-type');
        const query = $input.val().trim();

        $group.find('.linked-entity-id').val('');
        $group.find('.linked-entity-project-type').val('');
        clearTimeout(searchTimer);

        if (query.length < 2) {
            $group.find('.linked-entity-results').hide().empty();
            $group.find('.linked-entity-spinner').hide();
            return;
        }

        $group.find('.linked-entity-spinner').show();

        searchTimer = setTimeout(function () {
            $.ajax({
                url: window.BASE_URL + '/enterprise-search-linked-entity',
                method: 'GET',
                data: { q: query, enterprise_type: type },
                success: function (response) {
                    const results = response.data || [];
                    const $list = $group.find('.linked-entity-results').empty();

                    if (results.length === 0) {
                        $list.append('<div class="list-group-item text-muted small">ምንም አልተገኘም</div>');
                    } else {
                        results.forEach(function (item) {
                            $('<a href="#" class="list-group-item list-group-item-action small"></a>')
                                .text(item.label)
                                .data('id', item.id)
                                .data('label', item.label)
                                .data('project-type', item.project_type || '')
                                .appendTo($list);
                        });
                    }
                    $list.show();
                },
                complete: function () {
                    $group.find('.linked-entity-spinner').hide();
                }
            });
        }, 300);
    });

    // Unified click handler for selecting a search result
    $(document).on('click', '.linked-entity-results a', function (e) {
        e.preventDefault();
        const $result = $(this);
        const $group = $result.closest('.linked-entity-group');
        const $modal = $result.closest('.modal'); 

        const label = $result.data('label');
        const projectType = $result.data('project-type') || '';

        // Fill common fields
        $group.find('.linked-entity-search').val(label);
        $group.find('.linked-entity-id').val($result.data('id'));
        $group.find('.linked-entity-project-type').val(projectType);
        $modal.find('[name="enterprise_name"]').val(label);

        // If this is the Association Modal, run the custom dependency logic
        if ($modal.attr('id') === 'associationEnterpriseModal') {
            $modal.data('current-project-type', projectType);
            updateAssociationSupportFields($modal, projectType);
        }

        $group.find('.linked-entity-results').hide().empty();
    });

    // Close search results when clicking outside
    $(document).on('click', function (e) {
        if (!$(e.target).closest('.linked-entity-search, .linked-entity-results').length) {
            $('.linked-entity-results').hide();
        }
    });

    // Function to handle dynamic visibility and fields requirement for Association Modal
    function updateAssociationSupportFields($modal, projectType) {
        const supportGiven = $modal.find('#yetederege_dgaf_assoc').val();
        const orgSupportType = $modal.find('#org-selectsuport_assoc').val();

        const $ngoGroup = $modal.find('#ngo_id_assoc').closest('.col-12');
        const $orgTypeGroup = $modal.find('#org-selectsuport_assoc').closest('.col-12');
        const $supportedByGroup = $modal.find('#supported_by_assoc').closest('.col-12');
        const $supportedItemsGroup = $modal.find('#supported_items_assoc').closest('.col-12');

        // Reset fields requirement and hide elements by default
        $modal.find('#ngo_id_assoc').prop('required', false);
        $modal.find('#supported_by_assoc').prop('required', false);
        $modal.find('#supported_items_assoc').prop('required', false);

        if (supportGiven === '0') {
            // If "የለም" (0), hide all support-related fields
            $ngoGroup.hide();
            $orgTypeGroup.hide();
            $supportedByGroup.hide();
            $supportedItemsGroup.hide();
        } else if (supportGiven === '1') {
            if (projectType === 'NGO') {
                // If project type is NGO and support is 1
                $ngoGroup.hide();
                $orgTypeGroup.hide();
                $supportedByGroup.hide();
                $supportedItemsGroup.show().find('input').prop('required', true);
            } else {
                // If project type is NOT NGO and support is 1
                $orgTypeGroup.show();

                if (orgSupportType === 'beproject') {
                    $ngoGroup.show().find('select').prop('required', true);
                    $supportedByGroup.hide();
                    $supportedItemsGroup.show().find('input').prop('required', true);
                } else {
                    $ngoGroup.hide();
                    $supportedByGroup.show().find('input').prop('required', true);
                    $supportedItemsGroup.show().find('input').prop('required', true);
                }
            }
        }
    }

    // When "yetederege_dgaf_assoc" changes
    $(document).on('change', '#yetederege_dgaf_assoc', function () {
        const $modal = $(this).closest('.modal');
        const projectType = $modal.data('current-project-type') || '';
        updateAssociationSupportFields($modal, projectType);
    });

    // When "org-selectsuport_assoc" changes
    $(document).on('change', '#org-selectsuport_assoc', function () {
        const $modal = $(this).closest('.modal');
        const projectType = $modal.data('current-project-type') || '';
        updateAssociationSupportFields($modal, projectType);
    });
});