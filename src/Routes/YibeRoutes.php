<?php
// src/Routes/YibeRoutes.php

return [
    // የሪፖርት ፎርሙን ማሳያ ገጽ ራውት
    'report-registration' => ['ReportgenerationController', 'reportIndexShow', true],
    
    // በ AJAX የሪፖርት ሰንጠረዦችን (እንደ ሠ1) ዳታ መሳቢያ ራውት
    'report1'     => ['ReportgenerationController', 'report1', true],
    'report-1'     => ['ReportgenerationController', 'report1Show', true],
];