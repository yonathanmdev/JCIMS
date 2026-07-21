document.addEventListener('DOMContentLoaded', function () {

    document.addEventListener('click', function (e) {

        // ── Remove / Delete team
        const archiveBtn = e.target.closest('.remove-team-member-btn');
        if (archiveBtn) {
            const label    = 'ሰርዝ';
            const teamName = archiveBtn.dataset.name;
            const Id   = archiveBtn.dataset.id;
            const teamId   = archiveBtn.dataset.teamId;

            confirmDelete({
                endpoint:    'member-purge',
                id:          Id,
                teamId:      teamId,
                type:        'member',
                task:        'delete',
                title:       `"${teamName}" ${label}?`,
                warning:     `<strong>${teamName}</strong> የተባለ አባል ይሰረዛል! ከሰረዙት በኋላ መመለስ አይችሉም። History ላይ ግን በስመዎት ይቀመጣል`,
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