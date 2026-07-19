document.addEventListener('DOMContentLoaded', function () {

    document.addEventListener('click', function (e) {

        // ── Remove / Delete team
        const archiveBtn = e.target.closest('.delete-team-btn');
        if (archiveBtn) {
            const label    = 'ሰርዝ';
            const teamName = archiveBtn.dataset.name;
            const teamId   = archiveBtn.dataset.id;

            confirmDelete({
                endpoint:    'team-delete',
                id:          teamId,
                type:        'team',
                task:        'delete',
                title:       `"${teamName}" ${label}?`,
                warning:     `<strong>${teamName}</strong> የተባለ አደረጃጀት ይሰረዛል።`,
                confirmText: `<i class="fas fa-trash-alt"></i> አዎ፣ ${label}!`,
                successText: `${teamName} ተሰርዟል።`,
                requireReason:   true,
                requirePassword: true,
                onSuccess: () => archiveBtn.closest('tr')?.remove()
            });
            return;
        }
    });

});