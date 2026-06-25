<?php
// src/Routes/Teddyroutes.php

return [
    'register-employee'          => ['TeddyController', 'showemployee', true],
    'register-employee-process'  => ['TeddybackendContoller', 'handlePositionRegistration', true],
    'Registertest'               => ['TeddyController', 'showtest', true],
    'register-developer'         => ['TeddyController', 'showdeveloper', true],
    'register-dev-process'       => ['TeddybackendContoller', 'handleDeveloperRegistration', true],
    'employee-leave'             => ['EmployeeOnleaveController', 'showOnLeavePage', true],
    'employee-rest-store' => ['EmployeeOnleaveController', 'anualRestRegstration', true],
];