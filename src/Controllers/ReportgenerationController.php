<?php
namespace App\Controllers;

use App\Helpers\AuthHelper;
use App\Models\ReportgenerationModel;
use App\Models\Branch;

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

            $title = 'የስራ ፈላጊዎች ምዝገባና ግንዛቤ ፈጠራ ሪፖርት (ሠ1)'.$branchId;
            
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
    AuthHelper::checkRole(['team_leader', 'officer']);
    // 1. ከተጠቃሚው ሴሽን የራሱን ቅርንጫፍ መለያ መውሰድ
    $myBranchId = $_SESSION['user']['branch_id'] ?? null;
    $ketemaAstedader = $_SESSION['user']['ketema_astedader'] ?? false;

    // 2. ሞዴሉን መጥራት
    $branches = [];
    $branchModel = new Branch($this->db);
    
    // 3. ዳታውን ከሞዴል ማምጣት
    if($ketemaAstedader){
        $branches = $branchModel->getOneStopCenter($myBranchId);
    } else {
        $branches = $branchModel->getImmediateSubBranches($myBranchId);
    }

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
AuthHelper::checkRole(['team_leader', 'officer']);
 $level = $_SESSION['user']['level'];
 $myBranchId = $_POST['branch_id'] ?? null;
$branchData = [];
if (empty($myBranchId) && $level !== 4) {
    $myBranchId = $_SESSION['user']['branch_id'] ?? null;
}
else{
    $myBranchId = $_POST['branch_id'] ?? null;
    $branchModel = new Branch($this->db);
    $branchData  = $branchModel->getBranchById($myBranchId);
}
    $today = date('Y-m-d');
    $startdate = $_POST['start_date'] ?? date('Y-m-d');
    $enddate = $_POST['end_date'] ?? date('Y-m-d');
    if($startdate > $today){
         $_SESSION['error'] = 'የሪፖርት መጀመሪያ ቀን ከዛሬ ቀን በኋላ መሆን የለበትም';
        header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/report-registration");
        exit(); 
    }
    if($enddate > $today){
          $_SESSION['error'] = 'የሪፖርት መጨረሻ ቀን ከዛሬ ቀን በኋላ መሆን የለበትም';
        header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/report-registration");
        exit();
    }
    if ($startdate > $enddate) {
        $_SESSION['error'] = 'የሪፖርት መጨረሻ ቀን ከመጀምሪያ ቀን በኋላ መሆን አለበት።';
        header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/report-registration");
        exit();
    }
    $awarenessModel = new ReportgenerationModel($this->db);

    
    // 1. ከመጀመሪያው ቴብል ዳታውን ያመጣል
    $awarenessReport = $awarenessModel->getReport1ByHierarchy($myBranchId, $startdate, $enddate);

    // 2. ከሁለተኛው (ከአዲሱ) ቴብል የምክርና መረጃ ዳታውን ያመጣል
    $adviceReport = $awarenessModel->getJobSeekersAdviceByHierarchy($myBranchId, $startdate, $enddate);

    // 3. ሁለቱንም የሪፖርት ውጤቶች በአንድ አሬይ (Array) ላይ ያዋህዳል
    $finalReport = array_merge($awarenessReport, $adviceReport);

    // 4. የተዋሃደውን ሙሉ ዳታ ለቪው (report-1) ያስተላልፋል
    $this->renderPrintable('report-1', [
        'report1' => $finalReport,
        'branchData' => $branchData,
        'startdate' => $startdate,
        'enddate' => $enddate
    ]);
}
}