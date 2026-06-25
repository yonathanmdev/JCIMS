document.addEventListener('DOMContentLoaded', function () {

    document.addEventListener('click', function (e) {

        // ── Remove / Delete attached document
      const archiveBtn = e.target.closest('.archive-delete-btn');
if (archiveBtn) {
    const isDiscipline = archiveBtn.dataset.discipline === '1';
    const label        = isDiscipline ? 'ማንሳት' : 'ሰርዝ';
    const docType      = archiveBtn.dataset.type;
    const empName      = archiveBtn.dataset.name;
    const employeeId   = archiveBtn.dataset.employeeId;

    confirmDelete({
        endpoint:    'archive-remove',
        id:          archiveBtn.dataset.id,
        name:        docType,
        type:        'document',
        task:        'delete',
        title:       `"${docType}" ${label}?`,
        warning:     `<strong>${empName}</strong> የተባሉ ሰራተኛ <strong>"${docType}"</strong> ሰነድ ${isDiscipline ? 'ይነሳል' : 'ይሰረዛል'}።`,
        confirmText: `<i class="fas fa-trash-alt"></i> አዎ፣ ${label}!`,
        successText: `${empName} - ሰነዱ ${isDiscipline ? 'ተነስቷል' : 'ተሰርዟል'}።`,
        requireReason:   true,
        requirePassword: true,
        employeeId:  employeeId,
        isDiscipline: isDiscipline,
        onSuccess: () => archiveBtn.closest('tr')?.remove()
    });
    return;
}
    });

});