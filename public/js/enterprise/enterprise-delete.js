document.addEventListener('DOMContentLoaded', function () {

    document.addEventListener('click', function (e) {

        // ── Remove / Delete enterprise
        const archiveBtn = e.target.closest('.delete-enterprise-btn');
        if (archiveBtn) {
            const label    = 'ሰርዝ';
            const teamName = archiveBtn.dataset.name;
            const teamId   = archiveBtn.dataset.id;

            confirmDelete({
                endpoint:    'enterprise-purge',
                id:          teamId,
                type:        'team',
                task:        'delete',
                title:       `"${teamName}" ${label}?`,
                warning:     `<strong>${teamName}</strong> የተባለ ኢንተርፕራይዝ ይሰረዛል! ከሰረዙት በኋላ መመለስ አይችሉም። History ላይ ግን በስመዎት ይቀመጣል`,
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