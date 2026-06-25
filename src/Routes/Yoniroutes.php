<?php
// src/Routes/Yoniroutes.php

return [
    // User Management
    'register-user'                 => ['UserController', 'showRegisterForm', true],
    'register-process'              => ['UserController', 'handleRegistration', true],
    'edit-user'                     => ['UserController', 'getUserById', true],
    'edit-user-process'             => ['UserController', 'handleUpdateUser', true],
    'delete-user-process'           => ['UserController', 'delete', true],
    'deleted-users'                 => ['UserController', 'showDeletedLists', true],
    'restore-user'           => ['UserController', 'restore', true],
    'purge-user'             => ['UserController', 'purge', true],

    // Organization Management
    'register-organization'         => ['OrgController', 'showRegisterForm', true],
    'register-organization-process' => ['OrgController', 'handleRegistration', true],
    'update-organization-process'   => ['OrgController', 'handleEditOrganization', true],
    'delete-organization-process'   => ['OrgController', 'delete', true],
    'organization-deleted-lists'         => ['OrgController', 'showDeletedLists', true],
    'restore-organization'   => ['OrgController', 'restore', true],
    'purge-organization'    => ['OrgController', 'purge', true],
    'archived-organizations' => ['OrgController', 'archiveList', true],
    'restore-from-archive' => ['OrgController', 'restoreFromArchive', true],
 // ── Settings dashboard (GET) ──────────────────────────────────────
    //   /evaluation-settings
    //   /evaluation-settings?season={uuid}   ← season selected via query string
    'evaluation-settings'
        => ['EvaluationSettingsController', 'index', true],
 
    // ── Create season (POST) ──────────────────────────────────────────
    //   Form action: POST /save-season
    'save-season'
        => ['EvaluationSettingsController', 'saveSeason', true],
 'update-season' => ['EvaluationSettingsController', 'updateSeason', true],
    // ── Save branch windows (POST) ────────────────────────────────────
    //   Form action: POST /save-branch-settings
    'save-branch-settings'
        => ['EvaluationSettingsController', 'saveBranchSettings', true],

 
    // ── Delete season (GET, optional) ─────────────────────────────────
    //   Link: GET /delete-season?id={uuid}
    'delete-season'
        => ['EvaluationSettingsController', 'deleteSeason', true],

    'bsc-plan-management' => ['HrBscPlanController', 'index', true],
    'bsc-plan-mark-confirmed' => ['HrBscPlanController', 'markConfirmed', true],
    'bsc-plan-upload' => ['HrBscPlanController', 'upload', true],
    'bsc-plan-remove' => ['HrBscPlanController', 'delete', true],
        // efficency management

    'efficiency-management' => ['HrBscPlanController', 'indexEfficency', true],
    'efficiency-file-upload' => ['HrBscPlanController', 'efficiencyRegistration', true],
    'efficiency-file-update' => ['HrBscPlanController', 'efficiencyUpdate', true],
    // Branch Management
    'register-branch'               => ['OrgController', 'showRegisterForm', true],
    'register-branch-process'       => ['OrgController', 'handleBranchRegistration', true],
    'update-branch-process'   => ['OrgController', 'handleEditBranch', true],
    'delete-branch-process'   => ['OrgController', 'delete', true],
    'deleted-branches'         => ['OrgController', 'showDeletedLists', true],
    'restore-branch'   => ['OrgController', 'restore', true],
    'purge-branch'    => ['OrgController', 'purge', true],
    'archived-branches' => ['OrgController', 'branchArchiveList', true],
    'restore-from-archive-branch' => ['OrgController', 'restoreFromArchive', true],

    'register-director'          => ['DirectorController', 'showDirector', true],
    'register-director-process'  => ['DirectorController', 'handleDirector', true],
    'update-director-process'   => ['DirectorController', 'handleEditDirector', true],
    'delete-director-process'   => ['DirectorController', 'delete', true],
    'deleted-directors'         => ['DirectorController', 'showDeletedLists', true],
    'restore-director'   => ['DirectorController', 'restore', true],
    'purge-director'    => ['DirectorController', 'purge', true],

    'register-position'          => ['DirectorController', 'showPosition', true],
    'register-position-process'  => ['DirectorController', 'handlePositionRegistration', true],
    'update-position-process'    => ['DirectorController', 'handleEditPosition', true],
    'get-position'               => ['DirectorController', 'getPositionById', true],
    'getPositionById'            => ['DirectorController', 'getPositionById', true],
    'delete-position-process'    => ['DirectorController', 'deletePosition', true],
    'deleted-positions'         => ['DirectorController', 'showDeletedPositions', true],
    'restore-position'   => ['DirectorController', 'restorePosition', true],
    'purge-position'    => ['DirectorController', 'purgePosition', true],
    //employee
    'employee-active'       => ['EmployeeRegistrationController', 'showForm', true],
    'employee-registration-save'  => ['EmployeeRegistrationController', 'handleRegistration', true],
    'employee-edit'               => ['EmployeeRegistrationController', 'showEditForm', true],
    'employee-edit-save'          => ['EmployeeRegistrationController', 'handleEdit', true],
    'onBoardingEmployees'          => ['EmployeeRegistrationController', 'onboardingEmployees', true],
    'employee-registration'          => ['EmployeeRegistrationController', 'listofOnboardingEmployees', true],
    'employee-onboarding-views'    => ['EmployeeRegistrationController', 'showOnBoardingForm', true],
    'employee-onboadring-approve'          => ['EmployeeRegistrationController', 'handleOnboardingApproval', true],
    'employee-views'               => ['EmployeeRegistrationController', 'employeeDetails', true],
    'request-employee-deletion'            => ['EmployeeRegistrationController', 'requestDeletion', true],
    'employee-deletions-request-count'      => ['EmployeeRegistrationController', 'countPendingDeletions', true],
    'employee-deletion-requests'          => ['EmployeeRegistrationController', 'showPendingDeletions', true],
    'approve-employee-deletion'            => ['EmployeeRegistrationController', 'approveDeletion', true],
    'reject-employee-deletion'             => ['EmployeeRegistrationController', 'rejectDeletion', true],
    
    //Experience
    'employee-search-api' => ['ExperiencesController', 'employeeSearch', true],
    'employee-experience' => ['ExperiencesController', 'showExperience', true],
    'employee-company-search' => ['ExperiencesController', 'liveSearch', true],
    'employee-job-search' => ['ExperiencesController', 'jobSearch', true],
    'employee-experience-store' => ['ExperiencesController', 'storeExperience', true],
    'employee-experience-show' => ['ExperiencesController', 'getExperienceById', true],
    'employee-experience-update' => ['ExperiencesController', 'updateExperience', true],
    'employee-experience-letter' => ['ExperiencesController', 'showExperienceLetter', true],
    'employee-experience-delete' => ['ExperiencesController', 'delete', true],
    
    // File Management
    'employee-archive' => ['ScholarshipController', 'getDocument', true],
    'upload-certificate' => ['ArchiveController', 'attachFile', true],
    'archive-remove' => ['ArchiveController', 'delete', true],
    //scholarship
    'employee-scholarship'         => ['ScholarshipController', 'showScholarshipForm', true],
    'employee-scholarship-search'  => ['ScholarshipController', 'liveSearch', true],
    'employee-scholarship-store'   => ['ScholarshipController', 'storeScholarship', true],
    'on-leave-scholarship-count'   => ['ScholarshipController', 'onLeaveScholarshipEmployees', true],
    'employee-scholarship-onleave' => ['ScholarshipController', 'showScholarshiponLeavePending', true],
    'employee-scholarship-onleave-views' => ['ScholarshipController', 'getScholarshipDetails', true],
    'employee-scholarship-onleave-approval' => ['ScholarshipController', 'handleOnLeaveApproval', true],
    'employee-scholarship-edit' => ['ScholarshipController', 'showScholarshipEdit', true],
    'employee-scholarship-update' => ['ScholarshipController', 'updateScholarship', true],
    'employee-scholarship-returnee' => ['ScholarshipController', 'showScholarshipReturnees', true],
    'get-scholarship-details' => ['ScholarshipController', 'showScholarshipJson', true],
    'update-scholarship-returnee' => ['ScholarshipController', 'updateScholarshipReturnee', true],
    'employee-scholarship-return-store' => ['ScholarshipController', 'storeReturnScholarship', true],
    'delete-scholarship-process' => ['ScholarshipController', 'delete', true],
    
    //promotion

    'employee-promotion'         => ['PromotionController', 'showPromotionForm', true],
    //warranty
    'employee-warranty'         => ['WarrantyController', 'showWarrantyForm', true],
    'employee-warranty-search'  => ['WarrantyController', 'liveSearch', true],
    'employee-warranty-store'   => ['WarrantyController', 'storeWarranty', true],
    'employee-warranty-pending' => ['WarrantyController', 'getPendingWarranty', true],
    'employee-warranty-file-attachment-process' => ['WarrantyController', 'handleFileAttachment', true],
    'employee-has-warranty' => ['WarrantyController', 'showActiveWarranties', true],
    'employee-warranty-views' => ['WarrantyController', 'getWarrantyDetails', true],
    'employee-warranty-release-process' => ['WarrantyController', 'handleWarrantyRelease', true],
    //debt suspension
    'employee-debt-suspension' => ['DebtSuspensionController', 'showDebtSuspensionForm', true],
    'employee-debt-search'  => ['DebtSuspensionController', 'liveSearch', true],
    'employee-debt-suspension-store'   => ['DebtSuspensionController', 'storeDebtSuspension', true],
    'debt-suspension-count'   => ['DebtSuspensionController', 'countPending', true],
    'employee-debt-suspension-pending' => ['DebtSuspensionController', 'showDebtSuspensionPending', true],
    'employee-debt-suspension-approval-view' => ['DebtSuspensionController', 'getDebtSuspensionDetails', true],
    'employee-debt-suspension-approval' => ['DebtSuspensionController', 'handleDebtSuspensionApproval', true],
    'employee-debt-suspension-clearing' => ['DebtSuspensionController', 'getDebtSuspensionClearing', true],
    'employee-debt-suspension-clearing-approval' => ['DebtSuspensionController', 'storeDebtSuspensionClearing', true],
    'employee-debt-suspension-edit' => ['DebtSuspensionController', 'showDebtSuspensionEdit', true],
    'employee-debt-suspension-update' => ['DebtSuspensionController', 'updateDebtSuspension', true],
    'delete-debt-suspension-process' => ['DebtSuspensionController', 'delete', true],
    // Stored files

    
    
    'serve-file' => ['FileController', 'serveFile', true], // true = auth required

    // Audit Logs
    'audit-logs'         => ['AuditController', 'index', true],
    'audit-logs-data'    => ['AuditController', 'data', true],
    'audit-logs-stats'   => ['AuditController', 'stats', true],
    'audit-logs-show'    => ['AuditController', 'show', true],
    'audit-logs-export'  => ['AuditController', 'export', true],
   ];