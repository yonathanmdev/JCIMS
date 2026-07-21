    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
   
    <?php include_once 'footer.php'; ?>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- bs-custom-file-input -->
<script src="plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<!-- jQuery UI 1.11.4 -->
<script src="plugins/jquery-ui/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script nonce="<?php echo $GLOBALS['nonce']; ?>">
  $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->

<script src="plugins/jquery-knob/jquery.knob.min.js"></script>
<!-- daterangepicker -->
<script src="plugins/moment/moment.min.js"></script>
<script src="plugins/daterangepicker/daterangepicker.js"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<!-- Summernote -->
<script src="plugins/summernote/summernote-bs4.min.js"></script>
<!-- overlayScrollbars -->
<script src="plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<!-- AdminLTE App -->
<!-- AdminLTE for demo purposes -->
 <script src="dist/js/demo.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="plugins/sweetalert2/sweetalert2.min.js"></script>
<!-- DataTables  & Plugins -->
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="plugins/jszip/jszip.min.js"></script>
<script src="plugins/pdfmake/pdfmake.min.js"></script>
<script src="plugins/pdfmake/vfs_fonts.js"></script>
<script src="plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
<!-- AdminLTE App -->

<!-- Page specific script -->
 <?php if (isset($is_dashboard) && $is_dashboard === true): ?>
  <!-- ChartJS -->
<script src="plugins/chart.js/Chart.min.js"></script>
<!-- Sparkline -->
<script src="plugins/sparklines/sparkline.js"></script>
<!-- JQVMap -->
<script src="plugins/jqvmap/jquery.vmap.min.js"></script>
<script src="plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
<!-- jQuery Knob Chart 
   <script src="dist/js/pages/dashboard.js"></script>  if you want uncomment this  -->
<?php endif; ?>
<?php
function myAsset($path) {
    $fullPath = __DIR__ . '/' . $path;
    $version = file_exists($fullPath) ? filemtime($fullPath) : time();
    return $path . '?v=' . $version;
}
?>
<script src="<?= myAsset('js/confirm-delete.js') ?>"></script>

<?php if (isset($is_organization_page) && $is_organization_page === true): ?>
    
    <script src="<?= myAsset('js/organizations-logic.js') ?>"></script>
    <?php endif; ?>
   <?php if (isset($is_organization_deleted_page) && $is_organization_deleted_page === true): ?>
     <script src="<?= myAsset('js/deleted-organizations.js') ?>"></script>
    <?php endif; ?>
<?php if (isset($is_archived_organizations_page) && $is_archived_organizations_page === true): ?>
    <script src="<?= myAsset('js/archived-organization.js') ?>"></script>
<?php endif; ?>
<?php if (isset($is_branch_page) && $is_branch_page === true): ?>
    <script src="<?= myAsset('js/branches-logic.js') ?>"></script>
    <?php endif; ?>
 <?php if (isset($is_branch_deleted_page) && $is_branch_deleted_page === true): ?>
     <script src="<?= myAsset('js/deleted-branches.js') ?>"></script>
    <?php endif; ?>
    <?php if (isset($is_archived_branches_page) && $is_archived_branches_page === true): ?>
    <script src="<?= myAsset('js/archived-branches.js') ?>"></script>
<?php endif; ?>

   <?php if (isset($is_register_user_page) && $is_register_user_page === true): ?>
   
    <script src="<?= myAsset('js/edit-user.js') ?>"></script>
   <script nonce="<?php echo $GLOBALS['nonce']; ?>">
    const SESSION_ROLE    = <?= json_encode($_SESSION['user']['role'] ?? '') ?>;
    const BRANCH_NAME     = <?= json_encode($branchNameString) ?>;
    const ORGANIZATIONS   = <?= json_encode($organizations) ?>;
    const SUB_BRANCHES    = <?= json_encode($subBranches ?? []) ?>;
</script>
<script src="<?= myAsset('js/register-user.js') ?>"></script>
    <?php endif; ?>

      <?php if (isset($is_user_deleted_page) && $is_user_deleted_page === true): ?>
        <script src="<?= myAsset('js/deleted-users.js') ?>"></script>
    <?php endif; ?>
<?php if (isset($is_sector_registration_page) && $is_sector_registration_page === true): ?>
    
    <script src="<?= myAsset('js/sector-validation.js') ?>"></script>
    <script src="<?= myAsset('js/ethiopian-calendar.js') ?>"></script>
    <?php endif; ?>
    <?php if (isset($is_sub_sector_registration_page) && $is_sub_sector_registration_page === true): ?>
        <script src="<?= myAsset('js/sector-validation.js') ?>"></script>
          <script src="<?= myAsset('js/ethiopian-calendar.js') ?>"></script>
    <?php endif; ?>

    
   <?php if (isset($is_awaerness_registration_page) && $is_awaerness_registration_page === true): ?>
    <script src="plugins/jquery-validation/jquery.validate.min.js"></script>
    <script src="plugins/jquery-validation/additional-methods.min.js"></script>
    <script src="<?= myAsset('js/awaerness-validation.js') ?>"></script>
    
    <?php endif; ?>

     <?php if (isset($is_jobseeker_registration_page) && $is_jobseeker_registration_page === true): ?>
    <script src="plugins/jquery-validation/jquery.validate.min.js"></script>
    <script src="plugins/jquery-validation/additional-methods.min.js"></script>
    <script src="<?= myAsset('js/ethiopian-calendar.js') ?>"></script>
    <script src="<?= myAsset('js/sector-cascade.js') ?>"></script>
    <script src="<?= myAsset('js/jobseeker-form-validation.js') ?>"></script>
     <script src="<?= myAsset('js/jobseeker.views.js') ?>"></script>
      
    <?php endif; ?>

     <?php if (isset($is_jobseeker_list_page) && $is_jobseeker_list_page === true): ?>
    <script src="plugins/jquery-validation/jquery.validate.min.js"></script>
    <script src="plugins/jquery-validation/additional-methods.min.js"></script>
    <script src="<?= myAsset('js/ethiopian-calendar.js') ?>"></script>
    <script src="<?= myAsset('js/sector-cascade.js') ?>"></script>
     <script src="<?= myAsset('js/jobseeker-form-validation.js') ?>"></script>
     <script src="<?= myAsset('js/jobseeker.views.js') ?>"></script>
     <script src="<?= myAsset('js/jobseeker-search.js') ?>"></script>
     
    <?php endif; ?>
        <?php if (isset($is_jobseeker_renewal_page) && $is_jobseeker_renewal_page === true): ?>
    <script src="plugins/jquery-validation/jquery.validate.min.js"></script>
    <script src="plugins/jquery-validation/additional-methods.min.js"></script>
    <script src="<?= myAsset('js/ethiopian-calendar.js') ?>"></script>
    <script src="<?= myAsset('js/sector-cascade.js') ?>"></script>
     <script src="<?= myAsset('js/jobseeker-form-validation.js') ?>"></script>
  <script src="<?= myAsset('js/renewal-search.js') ?>"></script>
 
    <?php endif; ?>

<?php if (isset($is_jobseeker_team_page) && $is_jobseeker_team_page === true): ?>
    <script src="plugins/jquery-validation/jquery.validate.min.js"></script>
    <script src="plugins/jquery-validation/additional-methods.min.js"></script>
    <script src="<?= myAsset('js/ethiopian-calendar.js') ?>"></script>
    <script src="<?= myAsset('js/sector-cascade.js') ?>"></script>
    <script src="<?= myAsset('js/team-formation/core.js') ?>"></script>
    <script src="<?= myAsset('js/team-formation/government-project.js') ?>"></script>
    <script src="<?= myAsset('js/team-formation/search-select.js') ?>"></script>
    <script src="<?= myAsset('js/team-formation/submit.js') ?>"></script>

    <?php endif; ?>
 

     <?php if (isset($is_team_list_page) && $is_team_list_page === true): ?>
    <script src="plugins/jquery-validation/jquery.validate.min.js"></script>
    <script src="plugins/jquery-validation/additional-methods.min.js"></script>
    <script src="<?= myAsset('js/ethiopian-calendar.js') ?>"></script> 
     <script src="<?= myAsset('js/team-setup-delete.js') ?>"></script> 
    
    <?php endif; ?>

     <?php if (isset($is_team_member_add_page) && $is_team_member_add_page === true): ?>
    <script src="plugins/jquery-validation/jquery.validate.min.js"></script>
    <script src="plugins/jquery-validation/additional-methods.min.js"></script>
    <script src="<?= myAsset('js/ethiopian-calendar.js') ?>"></script>
    <script src="<?= myAsset('js/sector-cascade.js') ?>"></script>
    <script src="<?= myAsset('js/team-member-add.js') ?>"></script>
    <script src="<?= myAsset('js/team-set-up-edit.js') ?>"></script> 
    <script src="<?= myAsset('js/team-set-up-members-delete.js') ?>"></script>
   
    <?php endif; ?>


<?php if (isset($is_sra_edl_page) && $is_sra_edl_page === true): ?>
    <script src="plugins/jquery-validation/jquery.validate.min.js"></script>
    <script src="plugins/jquery-validation/additional-methods.min.js"></script>
    <script src="<?= myAsset('js/ethiopian-calendar.js') ?>"></script>
    <script src="<?= myAsset('js/sector-cascade.js') ?>"></script>
    
    <?php endif; ?>

   
    
    <?php if (isset($is_employee_scholarship_returnee) && $is_employee_scholarship_returnee === true): ?>
    <script src="plugins/jquery-validation/jquery.validate.min.js"></script>
    <script src="plugins/jquery-validation/additional-methods.min.js"></script>
    <script src="<?= myAsset('js/ethiopian-calendar.js') ?>"></script>
    
    <?php endif; ?>
    
    <?php if (isset($is_employee_warranty_page) && $is_employee_warranty_page === true): ?>
          <script src="<?= myAsset('js/employee-warranty.js') ?>"></script>
        <?php endif; ?>

    <?php if (isset($is_employee_debt_suspension_page) && $is_employee_debt_suspension_page === true): ?>
    <script src="plugins/jquery-validation/jquery.validate.min.js"></script>
    <script src="plugins/jquery-validation/additional-methods.min.js"></script>
    <script src="<?= myAsset('js/ethiopian-calendar.js') ?>"></script>
    <script src="<?= myAsset('js/employee-debt-suspension.js') ?>"></script>
    
    <?php endif; ?>
    <?php if (isset($is_employee_debt_suspension_clearing_page) && $is_employee_debt_suspension_clearing_page === true): ?>
    <script src="plugins/jquery-validation/jquery.validate.min.js"></script>
    <script src="plugins/jquery-validation/additional-methods.min.js"></script>
    <script src="<?= myAsset('js/ethiopian-calendar.js') ?>"></script>
    <script src="<?= myAsset('js/employee-debt-suspenssion.js') ?>"></script>
    
    <?php endif; ?>
<?php if (isset($is_employee_debt_suspension_edit_page) && $is_employee_debt_suspension_edit_page === true): ?>
    <script src="plugins/jquery-validation/jquery.validate.min.js"></script>
    <script src="plugins/jquery-validation/additional-methods.min.js"></script>
    <script src="<?= myAsset('js/ethiopian-calendar.js') ?>"></script>
     <script src="<?= myAsset('js/employee-debt-suspenssion.js') ?>"></script>
    <?php endif; ?>

    <?php if (isset($is_report_page) && $is_report_page === true): ?>
    <script src="plugins/jquery-validation/jquery.validate.min.js"></script>
    <script src="plugins/jquery-validation/additional-methods.min.js"></script>
    <script src="<?= myAsset('js/ethiopian-calendar.js') ?>"></script>
    <?php endif; ?>

 <?php if (isset($is_report_view_page) && $is_report_view_page === true): ?>
    <script src="plugins/jquery-validation/jquery.validate.min.js"></script>
    <script src="plugins/jquery-validation/additional-methods.min.js"></script>
    <script src="<?= myAsset('js/ethiopian-calendar.js') ?>"></script>
     <script src="<?= myAsset('js/report-view.js') ?>"></script>
    <?php endif; ?>

    <?php if ($_SESSION['user']['role']==='team_leader'): ?>
     <script nonce="<?php echo $GLOBALS['nonce']; ?>">
   /* const NOTIFICATION_URLS = {
        onboarding: BASE_URL + '/onBoardingEmployees',
        scholarship: BASE_URL + '/on-leave-scholarship-count',
        debtsuspension: BASE_URL + '/debt-suspension-count',
        employee_deleteion_request: BASE_URL + '/employee-deletions-request-count'
    };
    */
</script>

<!-- <script src="<?= myAsset('js/all-userdefined-notifications.js') ?>"></script> -->
    <?php endif; ?>
<script nonce="<?php echo $GLOBALS['nonce']; ?>">
$(function () {
  bsCustomFileInput.init();
});
</script>
<script nonce="<?php echo $GLOBALS['nonce']; ?>">
  $(function () {

    function resetSubmitButtons($form) {
      $form.data('submitting', false);
      $form.removeData('submitButton');
      $form.removeData('prevent-submit');

      $form.find('button[type="submit"], input[type="submit"]')
        .prop('disabled', false);
    }

    $(document).on('click', 'form button[type="submit"], form input[type="submit"]', function () {
      var $button = $(this);
      var $form = $button.closest('form');

      if ($form.data('submitting')) {
        return;
      }

      $form.data('submitButton', $button);
    });

    $(document).on('submit', 'form', function (event) {
      var $form = $(this);

      // ❌ BLOCK global disabling when validation already failed
      if ($form.data('prevent-submit') === true) {
        event.preventDefault();
        return false;
      }

      if ($form.data('submitting')) {
        event.preventDefault();
        return false;
      }

      $form.data('submitting', true);

      $form.find('button[type="submit"], input[type="submit"]')
        .prop('disabled', true);
    });

    $(document).on('invalid-form.validate invalid', 'form', function () {
      resetSubmitButtons($(this));
    });

  });
</script>
</body>
</html>

<script nonce="<?php echo $GLOBALS['nonce']; ?>">
  $(function () {
    // Define the message using the PHP variable
    var dynamicMsg = $("#example1").data("empty-msg") || "ምንም መረጃ የለም።";
    $("#example1").DataTable({
      "responsive": true, "lengthChange": false, "autoWidth": false,
      "deferRender": true,
      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
      "paging": false,
      "language": {
            "emptyTable": dynamicMsg, // Dynamic Amharic message,
            "zeroRecords": "ምንም የሚዛመድ መረጃ አልተገኘም",
            "search": "ፈልግ:",
            "paginate": {
                "next": "ቀጣይ",
                "previous": "ቀዳሚ"
            }
        }
    }
  ).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    $('#example2').DataTable({
      "deferRender": true,
      "paging": true,
      "lengthChange": false,
      "searching": false,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
    });
  });
  
</script>
<!-- 1. First: flash data -->
<script nonce="<?php echo $GLOBALS['nonce']; ?>">
  window.__flash = {
    success: <?php echo json_encode($_SESSION['success'] ?? null); ?>,
    error:   <?php echo json_encode($_SESSION['error']   ?? null); ?>
  };
  <?php unset($_SESSION['success'], $_SESSION['error']); ?>
</script>
<script src="<?= myAsset('js/toast.js') ?>"></script>