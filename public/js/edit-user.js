$(document).ready(function () {

    $(document).on("click", ".edit-user", function () {

        const id = $(this).data("id");


       fetch(`${BASE_URL}/edit-user?id=${id}`)
            .then(res => res.json())
            .then(data => {

                if (data.status === 'success') {

                    const user = data.data;

                    // Fill form fields
                    $("#edit_user_id").val(user.id);
                    $("#edit_firstname").val(user.first_name);
                    $("#edit_fathername").val(user.father_name);
                    $("#edit_grandfathername").val(user.grand_father_name);
                    $("#edit_phone").val(user.phone);
                    $("#edit_email").val(user.email);
                    // Show modal
                    $("#editUserModal").modal("show");

                } else {
                    alert(data.message || "Failed to load user");
                }
            })
            .catch(err => {
                console.error(err);
                alert("Error fetching data");
            });

    });

   document.addEventListener('click', function (e) {

    // ── Delete User
        const deleteButton = e.target.closest('.delete-user');
        if (deleteButton) {
            confirmDelete({
                endpoint:    'delete-user-process',
                id:          deleteButton.dataset.id,
                name:        deleteButton.dataset.name,
                task:        'delete',
                title:       `"${deleteButton.dataset.name}" ይሰረዝ?`,
                warning:     `<strong>"${deleteButton.dataset.name}ን"</strong> ከተቆጣጣሪ ዝርዝር ለማስወገድ ነው።`,
                confirmText: '<i class="fas fa-user-times"></i> አዎ፣ ሰርዝ!',
                successText: 'ተቆጣጣሪ ተሰርዟል።',
                requireReason:   true,
                requirePassword: true,
                onSuccess: () => document.getElementById(`row-${deleteButton.dataset.id}`)?.remove()
            });
            return;
        }
  });
   
});