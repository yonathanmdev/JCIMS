<?php
namespace App\Controllers;

use App\Helpers\AuthHelper;
use App\Models\ReportgenerationModel;

class ReportgenerationController extends BaseController
{
    protected $db;
    protected $reportModel;

    public function __construct($db)
    {
        $this->db = $db;
        $this->reportModel = new ReportgenerationModel($this->db);
    }

    /**
     * የሪፖርት ፎርሙንና የሪፖርት ማሳያ ገጽ
     */
   public function showReportForm()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $reportType = $_POST['report_type'] ?? null;
        $year       = $_POST['year'] ?? null;

        // 1. ከ Session ላይ የባለቤቱን branch_id እንወስዳለን
        // ማሳሰቢያ፦ ሲስተምህ ላይ በሎግኢን ጊዜ Session ውስጥ የተቀመጠበትን ስም (ለምሳሌ 'user_branch' ወይም 'branch_id') አረጋግጥ
        $branchId = $_SESSION['branch_id'] ?? null; 

        if ($reportType === 'ሠ1') {
            // 2. የ branch_id እሴትን ለሞዴሉ እናሳልፋለን
            $reportData = $this->reportModel->getSe1ReportData($year, $branchId);

            $title = 'የስራ ፈላጊዎች ምዝገባና ግንዛቤ ፈጠራ ሪፖርት (ሠ1)';
            
            $viewPath = __DIR__ . '/../views/report1.php'; 
            if (file_exists($viewPath)) {
                include $viewPath;
            } else {
                include __DIR__ . '/../../views/report1.php';
            }
            exit();
        }
    }

    $data = [
        'title' => 'JCIMS - የሪፖርት ማስሊያ ቦታ',
    ];
    $this->render('report-registration', $data);
}
}