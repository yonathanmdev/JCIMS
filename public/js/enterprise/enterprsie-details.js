document.querySelectorAll('.view-enterprise-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const id = btn.dataset.id;
        fetch(`${window.BASE_URL}/enterprises-details?id=${id}`)
            .then(res => res.json())
            .then(res => {
                if (res.status !== 'success') {
                    Swal.fire('ስህተት', res.message, 'error');
                    return;
                }
                renderEnterpriseModal(res.data); // builds header + conditional section + members table, then $('#enterpriseDetailModal').modal('show')
            });
    });
});
