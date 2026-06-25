// assets/js/toast.js
$(function () {
  var Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 10000
  });

  if (window.__flash.success) {
    Toast.fire({ icon: 'success', title: window.__flash.success });
  }

  if (window.__flash.error) {
    Toast.fire({ icon: 'error', title: window.__flash.error });
  }
});
const logoutBtn = document.getElementById('logout-btn');
if (logoutBtn) {
    logoutBtn.addEventListener('click', function(e) {
        if (!confirm(this.dataset.confirm)) {
            e.preventDefault();
        }
    });
}