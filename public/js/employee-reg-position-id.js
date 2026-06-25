
  document.addEventListener("DOMContentLoaded", function () {
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

});