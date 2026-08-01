<?php
// src/Routes/Yoniroutes.php

return [
    // User Management
    'register-user'                 => ['UserController', 'showRegisterForm', true],
    'register-process'              => ['UserController', 'handleRegistration', true],
    'edit-user'                     => ['UserController', 'getUserById', true],
    'edit-user-process'             => ['UserController', 'handleUpdateUser', true],
    'reset-password'                => ['UserController', 'resetPassword', true],
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
    //sector Management
    'sector-registration'               => ['SectorController', 'showRegisterForm', true],
    'sector-registration-process'       => ['SectorController', 'handleSectorRegistration', true],
    
    'sub-sector-registration'               => ['SectorController', 'showSubRegisterForm', true],    
    'sub-sector-registration-process'       => ['SectorController', 'handleSubsectorRegistration', true],
    'subsectors-by-sector' => ['SectorController', 'subsectorsBySector', true],
    'all-sectors-subsectors' => ['SectorController', 'getAllSectorsWithSubsectors', true],
    // job seeker Management
    'jobseeker-registration'                 => ['JobseekerController', 'showRegisterForm', true],
    'jobseeker-registration-process'         => ['JobseekerController', 'handleRegistration', true],
    'jobseekers-list'                        => ['JobseekerController', 'listofJobseekers', true],
    'retrieve-jobseeker'                     => ['JobseekerController', 'getJobseekerById', true],
    'job-seekers-search'             => ['JobseekerController', 'liveSearch', true],
    'jobseekers-list-export-excel'            => ['JobseekerController', 'exportJobSeekersExcel', true],
   'jobseekers-renewal'   => ['JobseekerController', 'renewalPage', true],
    'job-seekers-renewal-search' => ['JobseekerController', 'renewalSearch', true],
    'jobseeker-renewal-data' => ['JobseekerController', 'renewalData', true],
    'setting-up-team'   => ['JobseekerController', 'settingUpTeam', true],
    'jobseekers-for-government-project'   => ['JobseekerController', 'getJobSeekersForGovernmentProject', true],
    'jobseekers-record-removal'   => ['JobseekerController', 'recordJobSeekerRemoval', true],
    'jobseekers-search-for-organizing' => ['JobseekerController', 'searchJobSeekersForOrganizing', true],
    'team-formation'   => ['TeamFormationController', 'teamFormation', true],
    'team-lists'       => ['TeamFormationController', 'listGroups', true],
    'team-members-view'    => ['TeamFormationController', 'retrieveTeamMembers', true],
    'team-data-edit' => ['TeamFormationController', 'getTeamForEdit', true],
    'add-team-member' => ['TeamFormationController', 'addMember', true],
    'team-update' => ['TeamFormationController', 'updateTeamFormation', true],
    'team-purge' => ['TeamFormationController', 'purge', true],
    'member-purge' => ['TeamFormationController', 'purgeMember', true],
    
    //enterprise
   'enterprise-registration'                 => ['EnterpriseController', 'showRegisterForm', true],
   'enterprise-search-linked-entity' => ['EnterpriseController', 'searchLinkedEntity', true],
   'enterprise-registration-process'         => ['EnterpriseController', 'registerEnterprise', true],
   'individual-enterprise-registration-process'         => ['EnterpriseController', 'createIndividualEnterprise', true],
   'enterprise-lists'                        => ['EnterpriseController', 'listofEnterprises', true],
   'enterprises-details'                     => ['EnterpriseController', 'details', true],

   
   'enterprise-purge'                        => ['EnterpriseController', 'purge', true],
   'code003' => ['EnterpriseController', 'displayCode003', true],
    'serve-file' => ['FileController', 'serveFile', true], // true = auth required

    
    
    //fayda routes
    'fayda-start'    => ['FaydaController', 'start',    true], // shows the "enter ID" form
    'fayda-redirect' => ['FaydaController', 'redirect', true], // sends them to the bridge
    'fayda-verify'   => ['FaydaController', 'verify',   true],
    'fayda-confirm'  => ['FaydaController', 'confirm',  true],
    'fayda-register' => ['FaydaController', 'register', true],
    'fayda-error'    => ['FaydaController', 'showError', false],
    // Audit Logs
    'audit-logs'         => ['AuditController', 'index', true],
    'audit-logs-data'    => ['AuditController', 'data', true],
    'audit-logs-stats'   => ['AuditController', 'stats', true],
    'audit-logs-show'    => ['AuditController', 'show', true],
    'audit-logs-export'  => ['AuditController', 'export', true],
   ];