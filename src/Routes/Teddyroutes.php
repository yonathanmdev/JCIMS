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
];