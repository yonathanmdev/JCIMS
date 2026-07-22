<?php
// src/Routes/YibeRoutes.php

return [
    // የሪፖርት ፎርሙን ማሳያ ገጽ ራውት
    'report-registration' => ['ReportgenerationController', 'reportIndexShow', true],
    
    // በ AJAX የሪፖርት ሰንጠረዦችን (እንደ ሠ1) ዳታ መሳቢያ ራውት
    'report1'    => ['ReportgenerationController', 'report1', true],
    'report-1'   => ['ReportgenerationController', 'report1Show', true],
    'report-10'  => ['ReportgenerationController', 'report10Show', true],
    'report-4'   => ['ReportgenerationController', 'report4Show', true],
    'report-5'   => ['ReportgenerationController', 'report4Show', true],
    'report-6'   => ['ReportgenerationController', 'report6Show', true],
    'report-7'   => ['ReportgenerationController', 'report6Show', true],
    'report-8'   => ['ReportgenerationController', 'report8Show', true],
    'report-9'   => ['ReportgenerationController', 'report8Show', true],

    // የስራ ፈላጊዎች ሁኔታ ሲነካ የሚከፈተው የቻርት ገጽ ራውት
    'seeker-analytics' => ['ReportgenerationController', 'seekerAnalyticsShow', true],
    // የግንዛቤ ፈጠራ ሲነካ የሚከፈተው የቻርት ገጽ ራውት
    'awareness-all-analytics' => ['ReportgenerationController', 'awarenessallanalyticsShow', true],
    'awareness-analytics' => ['ReportgenerationController', 'awarnessAnalyticsShow', true],

];