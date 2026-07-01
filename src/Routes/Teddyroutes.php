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
   
];