<?php
// src/Routes/Teddyroutes.php

return [
    'register-employee'          => ['TeddyController', 'showemployee', true],
    'register-employee-process'  => ['TeddybackendContoller', 'handlePositionRegistration', true],
    'Registertest'               => ['TeddyController', 'showtest', true],
    'register-developer'         => ['TeddyController', 'showdeveloper', true],
    'register-dev-process'       => ['TeddybackendContoller', 'handleDeveloperRegistration', true],
    
    //

    'awareness-registration'             => ['AwarenessController', 'showRegisterForm', true],
    'awareness-registration-other-process' =>['AwarenessController', 'awarenessRegistration', true],
    'awareness-list'                     => ['AwarenessController', 'showAwarenessList', true],
    'awareness-update-other-process' => ['AwarenessController', 'updateAwareness', true],
   // 🔗 ለስራ ፈላጊዎች ፖপ-አፕ ፍለጋ የተለየ አድራሻ መፍቀድ
    'search-job-seekers-ajax' => ['AwarenessController', 'searchJobSeekersAjax', true],
    'update-jobseeker-awareness' => ['AwarenessController', 'updateJobSeekerAwareness', true],
    'createupdate-jobseeker-awareness' => ['AwarenessController', 'jobseekerawareness', true],
    'awareness-list-jobseekers' => ['AwarenessController', 'showJobSeekerAwarenessList', true],
    'remove-job-seeker-awareness-ajax' => ['AwarenessController', 'removeJobSeekerAwarenessAjax', true],
    'jobseeker-transfer' => ['JobseekerTransferController', 'listofJobseekersfortransfer', true],
    'get-branches-by-hierarchy-ajax' => ['JobseekerTransferController', 'getBranchesByHierarchyAjax', true],
    'process-jobseeker-transfer' => ['JobseekerTransferController', 'processJobSeekerTransfer', true],
    'jobseeker-transfers-list' => ['JobseekerTransferController', 'showTransferTracking', true],
    'jobseeker-transfer-decision' => ['JobseekerTransferController', 'submitTransferDecision', true],
    'solgure-registration' => ['SolgureController', 'sulgureshowRegisterForm', true],
    'defense-direct-registration-process' => ['SolgureController', 'processRegistration', true],
    'defense-get-details' => ['SolgureController', 'getDetails', true],
    'defense-print-profile' => ['SolgureController', 'printProfile', true],
    'processEdit' => ['SolgureController', 'processEdit', true],
    'job-creation-reg' => ['JobCreationRegController', 'showRegisterForm', true],
    'get-sub-sectors' => ['JobCreationRegController', 'getSubSectors', true],
    'get-job-seeker' => ['JobCreationRegController', 'showRegisterForm', true],
    'get-job-seeker-route' => ['JobCreationRegController', 'getJobSeekerData', true],
    'jobcreation-registration-process' =>['JobCreationRegController', 'processRegistration', true],
    'jobcreation-list' =>['JobCreationRegController', 'jobcreationcreatedview', true],
    'job-creation-delete' =>['JobCreationRegController', 'deletejobcretion', true],
];
