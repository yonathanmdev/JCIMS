<?php
namespace App\Controllers;

use App\Helpers\AuditHelper;

class BaseController {
    protected $db;

    // ሁሉም ኮንትሮለሮች የዳታቤዝ ኮኔክሽን እንዲኖራቸው
    public function __construct($db) {
        $this->db = $db;
        // Initialize audit logging
        AuditHelper::init($db);
    }

    /**
     * ቪው ፋይሎችን በቀላሉ ለመጥራት (Render Views)
     * @param string $viewName የቪው ፋይሉ ስም (ለምሳሌ 'auth/login')
     * @param array $data ወደ ቪው የሚላክ ዳታ
     */
 protected function render($viewName, $data = []) {
    extract($data);
    
    $basePath = __DIR__ . "/../../views/";
    $viewPath = $basePath . $viewName . ".php";

    // 1. የሎጊን ገጽ መሆኑን መለየት (ለምሳሌ 'auth/login' ከሆነ)
    $isLogin = ($viewName === 'auth/login' || $viewName === 'login');

    if ($isLogin) {
        // ለሎጊን ገጽ ከሆነ ቪው ፋይሉን ብቻ አሳይ
        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            die("ስህተት: የሎጊን ቪው ፋይል አልተገኘም: " . $viewPath);
        }
    } else {
        // ለሌሎች ገጾች (Dashboard, ወዘተ) Header እና Footer ጨምር
        $headerPath = $basePath . "layout/header.php";
        $footerPath = $basePath . "layout/allfooter.php";

        if (!file_exists($headerPath)) die("Header አልተገኘም: " . $headerPath);
        if (!file_exists($viewPath))   die("View አልተገኘም: " . $viewPath);
        if (!file_exists($footerPath)) die("Footer አልተገኘም: " . $footerPath);

        require_once $headerPath;
        require_once $viewPath;
        require_once $footerPath;
    }
}

/**
 * ቪው ፋይሎችን ያለ ሄደር እና ፉተር ለህትመት ወይም ለፖፕ-አፕ ለማሳየት
 */
protected function renderPrintable($viewName, $data = []) {
    extract($data);
    
    $viewPath = __DIR__ . "/../../views/" . $viewName . ".php";

    if (file_exists($viewPath)) {
        require_once $viewPath;
    } else {
        die("ስህተት: የህትመት ቪው ፋይል አልተገኘም: " . $viewPath);
    }
}
}