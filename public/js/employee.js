
// employee.js
document.addEventListener('DOMContentLoaded', function () {

    document.addEventListener('click', function (e) {

        // ── Delete employee
        const deleteEmployee = e.target.closest('.myapp-delete-btn');
        if (deleteEmployee) {
            confirmDelete({
                endpoint:    'request-employee-deletion',
                id:          deleteEmployee.dataset.id,
                name:        deleteEmployee.dataset.name,
                task:        'delete',
                title:       `"${deleteEmployee.dataset.name}" ይሰረዝ?`,
                warning:     `<strong>"${deleteEmployee.dataset.name}"</strong> ን ከሰራተኞች ዝርዝር ለማስወገድ ነው።`,
                confirmText: '<i class="fas fa-user-times"></i> አዎ፣ ሰርዝ!',
                successText: 'ሰራተኛው ተሰርዟል።',
                requireReason:   true,
                requirePassword: true,
                onSuccess: () => document.getElementById(`row-${deleteEmployee.dataset.id}`)?.remove()
            });
            return;
        }

       // ── Delete employee director
        const deleteEmployeeDirector = e.target.closest('.director-approval-delete-btn');
        if (deleteEmployeeDirector) {
            confirmDelete({
                endpoint:    'approve-employee-deletion',
                id:          deleteEmployeeDirector.dataset.id,
                name:        deleteEmployeeDirector.dataset.name,
                task:        'delete',
                title:       `"${deleteEmployeeDirector.dataset.name}" ይሰረዝ?`,
                warning:     `<strong>"${deleteEmployeeDirector.dataset.name}"</strong> ን ከሰራተኞች ዝርዝር ለማስወገድ ነው።`,
                confirmText: '<i class="fas fa-user-times"></i> አዎ፣ ሰርዝ!',
                successText: 'ሰራተኛው ተሰርዟል።',
                requireReason:   false,
                requirePassword: true,
                onSuccess: () => document.getElementById(`row-${deleteEmployeeDirector.dataset.id}`)?.remove()
            });
            return;
        }

 // ── reject delete employee director
        const rejectEmployeeDirector = e.target.closest('.director-reject-delete-btn');
        if (rejectEmployeeDirector) {
            confirmDelete({
                endpoint:    'reject-employee-deletion',
                id:          rejectEmployeeDirector.dataset.id,
                name:        rejectEmployeeDirector.dataset.name,
                task:        'reject',
                title:       `"${rejectEmployeeDirector.dataset.name}" ይመለስ?`,
                warning:     `<strong>"${rejectEmployeeDirector.dataset.name}"</strong> ን ወደ Active ሰራተኞች ዝርዝር ለመመለስ ነው።`,
                confirmText: '<i class="fas fa-user-times"></i> አዎ፣ መልስ!',
                successText: 'ሰራተኛው ተመልሷል',
                requireReason:   true,
                requirePassword: true,
                onSuccess: () => document.getElementById(`row-${rejectEmployeeDirector.dataset.id}`)?.remove()
            });
            return;
        }


    });

});