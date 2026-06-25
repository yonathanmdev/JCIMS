// ============================================================
// POSITION TOGGLE MANAGER
// ============================================================
class PositionToggleManager {
    constructor(prefix) {
        this.prefix = prefix;
    }

    get(id) {
        return document.getElementById(this.prefix + '_' + id);
    }

    handleNesa(isOn) {
        const hidden = this.get('nesa_hkmna');
        const badge  = this.get('nesa_badge');
        const card   = this.get('card_nesa');

        if (hidden) hidden.value = isOn ? 'yes' : 'no';
        if (card)   card.classList.toggle('active', isOn);
        if (badge) {
            badge.textContent = isOn ? 'ተጠቃሚ' : 'አይደለም';
            badge.classList.toggle('on', isOn);
        }
    }

    handleCloth(isOn) {
        const select = this.get('cloth_duration');
        const badge  = this.get('cloth_badge');
        const card   = this.get('card_cloth');
        const hidden = this.get('cloth_value');

        if (hidden) hidden.value = isOn ? 'yes' : 'no';
        if (select) {
            select.disabled = !isOn;
            if (!isOn) select.value = '';
        }
        if (card)  card.classList.toggle('active', isOn);
        if (badge) {
            badge.textContent = isOn ? 'ተጠቃሚ' : 'አይደለም';
            badge.classList.toggle('on', isOn);
        }
    }

    handleMultiple(isOn) {
        const hidden       = this.get('allow_multiple');
        const badge        = this.get('multiple_badge');
        const card         = this.get('card_multiple');
        const vacancyInput = this.get('vacancy_count');

        if (hidden)       hidden.value        = isOn ? '1' : '0';
        if (vacancyInput) {
            vacancyInput.disabled = !isOn;
            if (!isOn) vacancyInput.value = '';
        }
        if (card)  card.classList.toggle('active', isOn);
        if (badge) {
            badge.textContent = isOn ? 'ይፈቀዳል' : 'አይፈቀድም';
            badge.classList.toggle('on', isOn);
        }
    }

    populate(position) {
        // Nesa
        const nesaOn    = position.hkmna === 'yes';
        const nesaCheck = this.get('nesa_check');
        if (nesaCheck) {
            nesaCheck.checked = nesaOn;
            this.handleNesa(nesaOn);
        }

        // Cloth
        const clothOn    = position.cloth_due !== null
                        && position.cloth_due !== ''
                        && position.cloth_due !== 'no';
        const clothCheck = this.get('cloth_check');
        if (clothCheck) {
            clothCheck.checked = clothOn;
            this.handleCloth(clothOn);
            if (clothOn) {
                const select = this.get('cloth_duration');
                if (select) select.value = position.cloth_due;
            }
        }

        // Allow multiple
        const multipleOn    = parseInt(position.allow_multiple) === 1;
        const multipleCheck = this.get('multiple_check');
        if (multipleCheck) {
            multipleCheck.checked = multipleOn;
            this.handleMultiple(multipleOn);
            if (multipleOn && position.vacancy_count) {
                const vacancyInput = this.get('vacancy_count');
                if (vacancyInput) vacancyInput.value = position.vacancy_count;
            }
        }
    }

    reset() {
        const nesaCheck = this.get('nesa_check');
        if (nesaCheck) { nesaCheck.checked = false; this.handleNesa(false); }

        const clothCheck = this.get('cloth_check');
        if (clothCheck) { clothCheck.checked = false; this.handleCloth(false); }

        const multipleCheck = this.get('multiple_check');
        if (multipleCheck) { multipleCheck.checked = false; this.handleMultiple(false); }
    }

    bindEvents() {
        const nesaCheck     = this.get('nesa_check');
        const clothCheck    = this.get('cloth_check');
        const multipleCheck = this.get('multiple_check');

        if (nesaCheck)     nesaCheck.addEventListener('change',     (e) => this.handleNesa(e.target.checked));
        if (clothCheck)    clothCheck.addEventListener('change',    (e) => this.handleCloth(e.target.checked));
        if (multipleCheck) multipleCheck.addEventListener('change', (e) => this.handleMultiple(e.target.checked));
    }
}

// ============================================================
// INIT
// ============================================================
document.addEventListener('DOMContentLoaded', function () {

    

    // Create instances
    const addToggle  = new PositionToggleManager('add');
    const editToggle = new PositionToggleManager('edit');

    // Bind checkbox events
    addToggle.bindEvents();
    editToggle.bindEvents();

    // Reset add modal on close
    const addModal = document.getElementById('positionModal');
    if (addModal) {
        addModal.addEventListener('hidden.bs.modal', () => addToggle.reset());
    }

    // Reset edit modal on close
    const editModal = document.getElementById('editPositionModal');
    if (editModal) {
        editModal.addEventListener('hidden.bs.modal', () => editToggle.reset());
    }

    // ============================================================
    // EDIT — open modal and populate
    // ============================================================
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.edit-position');
        if (!btn) return;

        const id = btn.getAttribute('data-id');

        fetch(BASE_URL + '/?action=get-position&id=' + id, {
            method:  'GET',
            headers: { 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => {
            if (data.status !== 'success') {
                Swal.fire('ስህተት', data.message, 'error');
                return;
            }

            const position = data.position;

            // Populate text fields
            document.getElementById('edit_position_id').value     = position.id                 || '';
            document.getElementById('edit_director_name').value   = position.director_id        || '';
            document.getElementById('edit_position_name').value   = position.job_name           || '';
            document.getElementById('edit_position_code').value   = position.job_identifier_no  || '';
            document.getElementById('edit_sera_dereja').value     = position.dereja             || '';
            document.getElementById('edit_sera_rken').value       = position.scale              || '';
            document.getElementById('edit_salary').value          = position.salary             || '';
            document.getElementById('edit_yeteyash_huneta').value = position.wastna             || '';
            document.getElementById('edit_description').value     = position.description        || '';

            // Populate toggles
            editToggle.populate(position);

            $('#editPositionModal').modal('show');
        })
        .catch(err => {
            console.error('Error:', err);
            Swal.fire('ስህተት', 'መረጃ ለማግኘት አልተሳካም።', 'error');
        });
    });

    // ============================================================
    // EDIT — submit form
    // ============================================================
    const editForm = document.getElementById('editPositionForm');
    if (editForm) {
        editForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const formData  = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');

            // Fix cloth_duration — send null if disabled
            const clothEnabled = document.getElementById('edit_cloth_value')?.value === 'yes';
            const clothSelect  = document.getElementById('edit_cloth_duration');
            formData.set(
                'cloth_duration',
                clothEnabled && clothSelect?.value ? clothSelect.value : ''
            );

            submitBtn.disabled    = true;
            submitBtn.innerHTML   = '<i class="fas fa-spinner fa-spin"></i> በማደስ ላይ...';

            fetch(BASE_URL + '/?action=update-position-process', {
                method:      'POST',
                body:        formData,
                credentials: 'same-origin',
                headers:     { 'Accept': 'application/json' }
            })
            .then(res => {
                if (!res.ok) throw new Error('HTTP ' + res.status);
                return res.text();
            })
            .then(text => {
                try { return JSON.parse(text); }
                catch { throw new Error('ሰርቨሩ የላከው መረጃ ትክክል አይደለም: ' + text); }
            })
            .then(data => {
                if (data.status === 'success') {
                    $('#editPositionModal').modal('hide');
                    Swal.fire({
                        icon:  'success',
                        title: 'ተሳክቷል!',
                        text:  data.message,
                        timer: 2000
                    }).then(() => location.reload());
                } else {
                    Swal.fire('ስህተት', data.message, 'error');
                }
            })
            .catch(err => {
                console.error('Error:', err);
                Swal.fire('ስህተት', 'የላከው መረጃ ተስተካክሎ እንደገና ይሞክሩ።', 'error');
            })
            .finally(() => {
                submitBtn.disabled  = false;
                submitBtn.innerHTML = 'አስተካክል';
            });
        });
    }

 document.addEventListener('click', function (e) {

    // ── Delete position
        const deletePosition = e.target.closest('.delete-position');
        if (deletePosition) {
            confirmDelete({
                endpoint:    'delete-position-process',
                id:          deletePosition.dataset.id,
                name:        deletePosition.dataset.name,
                task:        'delete',
                title:       `"${deletePosition.dataset.name}" ይሰረዝ?`,
                warning:     `<strong>"${deletePosition.dataset.name}ን"</strong> ከስራ መደብ ዝርዝር ለማስወገድ ነው።`,
                confirmText: '<i class="fas fa-user-times"></i> አዎ፣ ሰርዝ!',
                successText: 'መደቡ ተሰርዟል።',
                requireReason:   true,
                requirePassword: true,
                onSuccess: () => document.getElementById(`row-${deletePosition.dataset.id}`)?.remove()
            });
            return;
        }
  });




  document.addEventListener('change', function (e) {

    // ── Auto-fill employee_id from job_property_id
    const jobSelect = e.target.closest('#job_property_id');
    if (jobSelect) {
        const id = jobSelect.value;
        const employeeInput = document.getElementById('employee_id');

        if (!id) {
            employeeInput.value = '';
            return;
        }

        fetch(BASE_URL + '/?action=getPositionById&id=' + id , {
            method:      'GET',
            credentials: 'same-origin',
            headers:     { 'Accept': 'application/json' }
        })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    employeeInput.value = data.position.job_identifier_no ?? '';
                } else {
                    employeeInput.value = '';
                    console.warn(data.message);
                }
            })
            .catch(err => {
                employeeInput.value = '';
                console.error('Fetch error:', err);
            });

        return;
    }

});

}); // end DOMContentLoaded