// employee-experience.js
document.addEventListener('DOMContentLoaded', function () {

    document.addEventListener('click', function (e) {

        // ── Delete employee
        const deleteExperience = e.target.closest('.delete-exp-btn');
        if (deleteExperience) {
            confirmDelete({
                endpoint:    'employee-experience-delete',
                id:          deleteExperience.dataset.id,
                name:        deleteExperience.dataset.name,
                task:        'delete',
                title:       `"የ${deleteExperience.dataset.name}" ይህ የስራ ልምድ ይሰረዝ?`,
                warning:     `<strong>"የ${deleteExperience.dataset.name}"</strong> ን ይህን የስራ ልምድ ለማጥፋት ነው።`,
                confirmText: '<i class="fas fa-user-times"></i> አዎ፣ ሰርዝ!',
                successText: 'የስራ ልምዱ ተሰርዟል።',
                requireReason:   true,
                requirePassword: true,
                onSuccess: () => document.getElementById(`row-${deleteExperience.dataset.id}`)?.remove()
            });
            return;
        }

      

    });

});