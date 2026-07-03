// assets/js/toast.js
$(function () {
  var Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 10000,
    timerProgressBar: true
  });

  if (window.__flash.success) {
    Toast.fire({
      icon: 'success',
      title: window.__flash.success
    });
  }

  if (window.__flash.error) {
    Toast.fire({
      icon: 'error',
      title: window.__flash.error,
      timer: undefined,        // disable auto-dismiss timer
      timerProgressBar: false,
      showConfirmButton: true, // add a close/OK button
      confirmButtonText: 'x'  // "OK" in Amharic
    });
  }
});