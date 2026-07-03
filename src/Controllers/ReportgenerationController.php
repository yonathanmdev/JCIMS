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
        $branchId = $_SESSION['user']['branch_id'] ?? null; 

        if ($reportType === 'ሠ1') {
            // 2. የ branch_id እሴትን ለሞዴሉ እናሳልፋለን
           // 1. ያንተ የቆየውና ሌሎቹን ቁጥሮች በትክክል የሚያመጣው የሞዴል ጥሪ
$mainData = $this->reportModel->getSe1ReportData($year, $branchId);

// 2. አዲሱ ለቁጥር 12 እና 13 ብቻ የፈጠርነው የሞዴል ጥሪ
$otherData = $this->reportModel->getSe1OtherData($year, $branchId);

// 3. ሁለቱን ዳታዎች ማዋሃድ (ይህ ወሳኝ ነው!)
$reportData = array_merge($mainData ?: [], $otherData ?: []);

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



public function reportIndexShow()
{
    // 1. ከተጠቃሚው ሴሽን የራሱን ቅርንጫፍ መለያ መውሰድ
    $myBranchId = $_SESSION['user']['branch_id'] ?? null;

    // 2. ሞዴሉን መጥራት
    $awarenessModel = new ReportgenerationModel($this->db);
    
    // 3. ዳታውን ከሞዴል ማምጣት
    $branches = $awarenessModel->getAllowedBranches($myBranchId);

    // 4. 🔥 ዋናው ነጥብ፦ ዳታውን በ Array Key 'branches' አድርጎ ወደ ቪው መላክ
    // የእርስዎ ፍሬምወርቅ $this->render() የሚጠቀም ከሆነ፡
    $this->render('report-registration', [
        'branches' => $branches
    ]);

    /* 
    💡 ማሳሰቢያ፦ ሲስተምህ custom custom ቢሆንና render() ባይጠቀም፣ 
    ከላይ የተገኘው $branches ተለዋዋጭ ሳይጠፋ (ሳይሰረዝ) ቪውውን በ include መጥራት አለብህ፡
    
    include __DIR__ . '/../views/report-registration.php';
    */
}


public function report1Show()
{
    $myBranchId = $_SESSION['user']['branch_id'] ?? null; // ወይም የምታገኝበት መንገድ

    $awarenessModel = new ReportgenerationModel($this->db);
    
    // 1. ከመጀመሪያው ቴብል ዳታውን ያመጣል
    $awarenessReport = $awarenessModel->getReport1ByHierarchy($myBranchId);

    // 2. ከሁለተኛው (ከአዲሱ) ቴብል የምክርና መረጃ ዳታውን ያመጣል
    $adviceReport = $awarenessModel->getJobSeekersAdviceByHierarchy($myBranchId);

    // 3. ሁለቱንም የሪፖርት ውጤቶች በአንድ አሬይ (Array) ላይ ያዋህዳል
    $finalReport = array_merge($awarenessReport, $adviceReport);

    // 4. የተዋሃደውን ሙሉ ዳታ ለቪው (report-1) ያስተላልፋል
    $this->render('report-1', [
        'report1' => $finalReport
    ]);
}
}