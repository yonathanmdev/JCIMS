$(document).on('click', '.editTeamBtn', function () {
    const teamId = $(this).data('id');
    $('#editTeamError').addClass('d-none').text('');

    $.get(`${BASE_URL}/team-data-edit/${teamId}`)
        .done(function (res) {
            if (!res.status) {
                $('#editTeamError').removeClass('d-none').text(res.message || 'መረጃ ማምጣት አልተቻለም።');
                return;
            }
            console.log(res);
           populateEditTeamModal(res.team, res.sectors, res.subsectorsBySector, res.members, res.ngos);
        })
        .fail(function () {
            $('#editTeamError').removeClass('d-none').text('የኔትወርክ ስህተት ተከስቷል።');
        });
});
function populateEditTeamModal(team, sectors, subsectorsBySector, members, ngos) {

    $('#edit_team_id').val(team.uuid);
    $('#edit_association_name').val(team.association_name);
   setSelectValueTrimmed('#edit_yetederajubet_akababi', team.yetederajubet_akababi);
    $('#edit_manager_phone').val(team.manager_phone);
    $('#edit_yesra_mesk').val(team.yesra_mesk);
   setSelectValueTrimmed('#edit_project_type', team.project_type);
   $('#edit_project_type').prop('disabled', true); // project_type is fixed at creation, cannot be edited
$('#edit_project_type')
    .prop('disabled', true)
    .attr('title', 'የአደረጃጀት አይነት መቀየር አይቻልም');
    // Sector options — value = sector UUID (s.id)
    const $sector = $('#edit_sector');
    $sector.empty().append('<option value="">-- ይምረጡ --</option>');
    sectors.forEach(s => {
        $sector.append(
            `<option value="${s.id}" ${String(s.id) === String(team.sector_id) ? 'selected' : ''}>
                ${s.name}
            </option>`
        );
    });


    const normalizedMap = {};
    Object.keys(subsectorsBySector).forEach(key => {
        normalizedMap[String(key)] = subsectorsBySector[key];
    });
    $('#editTeamModal').data('subsectorMap', normalizedMap);
    fillSubsectorsFor(team.sector_id, team.subsector_id);

const $ngo = $('#ngo');
    $ngo.empty().append('<option value="">-- NGO ይምረጡ --</option>');
    (ngos || []).forEach(n => {
        $ngo.append(
            `<option value="${n.pid}" ${String(n.pid) === String(team.ngo_id) ? 'selected' : ''}>
                ${n.pname}
            </option>`
        );
    });

    toggleNgoField(team.project_type);

    // Leader dropdowns — value = job_seeker_id, text = full name
    populateMemberSelect('#edit_teamleader_id', members, team.teamleader_id);
    populateMemberSelect('#edit_vice_teamleader_id', members, team.vice_teamleader_id);
    populateMemberSelect('#edit_treasurer_id', members, team.treasurer);
    populateMemberSelect('#edit_procurement_id', members, team.procurement);
}
function toggleNgoField(projectType) {
    const $ngoGroup = $('#ngo').closest('.form-group, .col-md-4'); // wraps the NGO field
    const $ngo = $('#ngo');

    if (projectType === 'NGO') {
        $ngoGroup.removeClass('d-none');
        $ngo.prop('disabled', false);
    } else {
        $ngoGroup.addClass('d-none');
        $ngo.prop('disabled', true).val(''); // clear selection so it isn't submitted
    }
}

function populateMemberSelect(selector, members, selectedId) {
    const $select = $(selector);
    $select.empty().append('<option value="">-- ይምረጡ --</option>');

    (members || []).forEach(m => {
        const fullName = [m.first_name, m.father_name, m.last_name].filter(Boolean).join(' ');
        $select.append(
            `<option value="${m.id}" ${String(m.job_seeker_id) === String(selectedId) ? 'selected' : ''}>
                ${fullName}
            </option>`
        );
    });
}

function fillSubsectorsFor(sectorId, selectedSubsectorId = '') {
    const map  = $('#editTeamModal').data('subsectorMap') || {};
    const list = map[String(sectorId)] || [];
    const $sub = $('#edit_subsector');

    $sub.empty().append('<option value="">-- ይምረጡ --</option>');

    if (list.length) {
        list.forEach(ss => {
            $sub.append(
                `<option value="${ss.id}" ${String(ss.id) === String(selectedSubsectorId) ? 'selected' : ''}>
                    ${ss.name}
                </option>`
            );
        });
        $sub.prop('disabled', false);
    } else {
        $sub.empty().append('<option value="">-- ንዑስ ዘርፍ የለም --</option>');
        $sub.prop('disabled', true);
    }
}
function setSelectValueTrimmed(selector, rawValue) {
    const $el = $(selector);
    const value = (rawValue || '').trim();
    $el.val(value);

    // If nothing matched (value not found among options), fall back to blank
    if ($el.val() === null) {
        $el.val('');
    }
}

$('#edit_sector').on('change', function () {
    fillSubsectorsFor($(this).val(), '');
});

$('#editTeamForm').on('submit', function (e) {
    e.preventDefault();
    $('#editTeamError').addClass('d-none').text('');

    const payload = {
        team_id:                $('#edit_team_id').val(),
        association_name:       $('#edit_association_name').val(),
        yetederajubet_akababi:  $('#edit_yetederajubet_akababi').val(),
        project_type:           $('#edit_project_type').val(),
        manager_phone:          $('#edit_manager_phone').val(),
        sector:                 $('#edit_sector').val(),      // uuid
        subsector:              $('#edit_subsector').val(),   // uuid
        yesra_mesk:             $('#edit_yesra_mesk').val(),
        ngo:           $('#ngo').val(),
        
        teamleader_id:          $('#edit_teamleader_id').val(),
        vice_teamleader_id:     $('#edit_vice_teamleader_id').val(),
        treasurer:              $('#edit_treasurer_id').val(),
        procurement:            $('#edit_procurement_id').val(),
    };

    // --- Required field validation ---
    const requiredFields = [
        { key: 'team_id',               label: 'የቡድን መታወቂያ' },
        { key: 'association_name',      label: 'የማህበሩ ስም' },
        { key: 'yetederajubet_akababi', label: 'የተደራጁበት አካባቢ' },
        { key: 'project_type',          label: 'የአደረጃጀት ዓይነት' },
        { key: 'manager_phone',         label: 'የስራ አስኪያጅ ስልክ ቁጥር' },
        { key: 'sector',                label: 'ዘርፍ' },
        { key: 'subsector',             label: 'ንዑስ ዘርፍ' },
        { key: 'yesra_mesk',            label: 'የስራ መስክ' },
        { key: 'teamleader_id',         label: 'የቡድን መሪ' },
        { key: 'vice_teamleader_id',    label: 'ም/የቡድን መሪ' },
        { key: 'treasurer',             label: 'ገንዘብ ያዥ' },
        { key: 'procurement',           label: 'ግዥ' },
    ];

    for (const field of requiredFields) {
        if (!payload[field.key]) {
            Swal.fire({
                icon: 'warning',
                title: 'እባክዎ ያረጋግጡ',
                text: `እባክዎ "${field.label}" መስክን ይሙሉ`,
                confirmButtonText: 'እሺ'
            });
            return;
        }
    }

    // NGO required ONLY when project_type is NGO
    if (payload.project_type === 'NGO' && !payload.ngo) {
        Swal.fire({
            icon: 'warning',
            title: 'እባክዎ ያረጋግጡ',
            text: 'እባክዎ NGO ይምረጡ',
            confirmButtonText: 'እሺ'
        });
        return;
    }

    const $submitBtn = $('#editTeamSubmitBtn');
    $submitBtn.prop('disabled', true);

    $.post(`${BASE_URL}/team-update`, payload)
        .done(function (res) {
            if (!res.success) {
                Swal.fire({
                    icon: 'error',
                    title: 'ስህተት',
                    text: res.message || 'ማዘመን አልተቻለም።',
                    confirmButtonText: 'እሺ'
                });
                return;
            }

            Swal.fire({
                icon: 'success',
                title: 'ተሳክቷል',
                text: res.message || 'ቡድኑ በተሳካ ሁኔታ ተስተካክሏል',
                confirmButtonText: 'እሺ'
            }).then(() => {
                $('#editTeamModal').modal('hide');
                location.reload(); // or refresh the table via AJAX
            });
        })
        .fail(function () {
            Swal.fire({
                icon: 'error',
                title: 'ስህተት',
                text: 'የኔትወርክ ስህተት ተከስቷል።',
                confirmButtonText: 'እሺ'
            });
        })
        .always(function () {
            $submitBtn.prop('disabled', false);
        });
});