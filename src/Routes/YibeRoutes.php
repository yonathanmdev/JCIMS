<?php
// src/Routes/YibeRoutes.php

return [
//የሶስቱንም አፈጻጸም የሚያወጣው 
'efficiency_statusy' => ['ReportgenerationController', 'efficiencyStatusReport', true],
// የባለሙያን አፈጻጸም ሁኔታ ማሳያ
    //'expert_level_view' => ['ReportgenerationController', 'expertLevelReport', true],

    // የስራ ፈላጊና ግንዛቤ ፈጠራ አፈጻጸም ሁኔታ ማሳያ
    'performance_view' => ['ReportgenerationController', 'performanceIndexShow', true],
    // የስራ እድልና ኢንተርፕራይዝ ምስረታ አፈጻጸም ሁኔታ ማሳያ
    'performance_job_creation_view' => ['ReportgenerationController', 'performanceJobCreationShow', true],

    // የሪፖርት ፎርሙን ማሳያ ገጽ ራውት
    'report-registration' => ['ReportgenerationController', 'reportIndexShow', true],

    // የቀበሌ ሪፖርት ፎርሙን ማሳያ ገጽ ራውት
    'kreport-registration' => ['ReportgenerationControllerk', 'reportIndexShow', true],
    
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
    'report-2'   => ['ReportgenerationController', 'report2Show', true],
    'report-3'   => ['ReportgenerationController', 'report2Show', true],


    // የቀበሌ በ AJAX የሪፖርት ሰንጠረዦችን (እንደ ሠ1) ዳታ መሳቢያ ራውት
    'kreport1'    => ['ReportgenerationControllerk', 'report1', true],
    'kreport-1'   => ['ReportgenerationControllerk', 'report1Show', true],
    'kreport-10'  => ['ReportgenerationControllerk', 'report10Show', true],
    'kreport-4'   => ['ReportgenerationControllerk', 'report4Show', true],
    'kreport-5'   => ['ReportgenerationControllerk', 'report4Show', true],
    'kreport-6'   => ['ReportgenerationControllerk', 'report6Show', true],
    'kreport-7'   => ['ReportgenerationControllerk', 'report6Show', true],
    'kreport-8'   => ['ReportgenerationControllerk', 'report8Show', true],
    'kreport-9'   => ['ReportgenerationControllerk', 'report8Show', true],
    'kreport-2'   => ['ReportgenerationControllerk', 'report2Show', true],
    'kreport-3'   => ['ReportgenerationControllerk', 'report2Show', true],

    // የስራ ፈላጊዎች ሁኔታ ሲነካ የሚከፈተው የቻርት ገጽ ራውት
    'seeker-analytics' => ['ReportgenerationController', 'seekerAnalyticsShow', true],
    // የግንዛቤ ፈጠራ ሲነካ የሚከፈተው የቻርት ገጽ ራውት
    'awareness-all-analytics' => ['ReportgenerationController', 'awarenessallanalyticsShow', true],
    'awareness-analytics' => ['ReportgenerationController', 'awarnessAnalyticsShow', true],

    // የስራ እድል ሁኔታ ሲነካ የሚከፈተው የቻርት ገጽ ራውት
    'jcreation-analytics' => ['ReportgenerationController', 'jcreationAnalyticsShow', true],

    // የአደረጃጀት ሁኔታ ሲነካ የሚከፈተው የቻርት ገጽ ራውት
    'orgteam-analytics' => ['ReportgenerationController', 'orgteamAnalyticsShow', true],

    // የኢንተርፐራይዝ ሁኔታ ሲነካ የሚከፈተው የቻርት ገጽ ራውት
    'enterprise-analytics' => ['ReportgenerationController', 'enterpriseAnalyticsShow', true],

    // የሁሉም ቀበሌዎች ሪፖርት ማየት ገጽ ራውት
    'all-kebele-report' => ['KebeleReportcontroller', 'allKebeleReport', true],
];