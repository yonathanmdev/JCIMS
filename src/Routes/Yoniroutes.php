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
    
    'serve-file' => ['FileController', 'serveFile', true], // true = auth required

    // Audit Logs
    'audit-logs'         => ['AuditController', 'index', true],
    'audit-logs-data'    => ['AuditController', 'data', true],
    'audit-logs-stats'   => ['AuditController', 'stats', true],
    'audit-logs-show'    => ['AuditController', 'show', true],
    'audit-logs-export'  => ['AuditController', 'export', true],
   ];