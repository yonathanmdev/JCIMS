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
    public function reportIndexShow()
    {
        AuthHelper::checkRole(['team_leader', 'officer']);
        
        $myBranchId = $_SESSION['user']['branch_id'] ?? '';
        $myBranchId = (string)$myBranchId;
        
        // የቅርንጫፉን ስም ከሴሽን መውሰድ (ከሌለ ባዶ)
        $myBranchName = $_SESSION['user']['branch_name'] ?? ($_SESSION['user']['name'] ?? '');
        $ketemaAstedader = $_SESSION['user']['ketema_astedader'] ?? false;

        $branches = [];
        $branchModel = new Branch($this->db);
        
        if (!empty($myBranchId)) {
            if ($ketemaAstedader) {
                $branches = $branchModel->getOneStopCenter($myBranchId);
            } else {
                $branches = $branchModel->getImmediateSubBranches($myBranchId);
            }
        }

        // 💡 ማስተካከያ፡ defaultBranchId እና defaultBranchName ወደ ቪው ይላካሉ
        $this->render('report-registration', [
            'branches'          => $branches,
            'defaultBranchId'   => $myBranchId,
            'defaultBranchName' => $myBranchName
        ]);
    }

    public function report1Show()
{
    AuthHelper::checkRole(['team_leader', 'officer']);
    
    $sessionBranchId = $_SESSION['user']['branch_id'] ?? '';
    
    // 💡 ማስተካከያ 1፦ ዳታውን ከ POST ካጣው ከ GET (ከሊንኩ ላይ) እንዲፈልግ ተደርጓል
    $postedBranchId  = $_POST['branch_id'] ?? ($_GET['branch_id'] ?? null);
    $ketemaAstedader = $_SESSION['user']['ketema_astedader'] ?? false;
    
    $branchData = [];
    $branchModel = new Branch($this->db);

    // 1. መለያው (branch_id) በትክክል መመረጡን ማረጋገጥ
    if (!empty($postedBranchId)) {
        $myBranchId = $postedBranchId;
    } else {
        $myBranchId = $sessionBranchId;
    }

    $myBranchId = (string)$myBranchId;

    // የብራንቹን መረጃ ለሪፖርቱ ሄደር (Header) ማምጫ
    if (!empty($myBranchId)) {
        $branchData = $branchModel->getBranchById($myBranchId);
    }

    // 💡 ማስተካከያ 2፦ ቀናቶችንም ከ POST ከሌለ ከ GET (ከሊንኩ) እንዲወስድ ተደርጓል
    $today = date('Y-m-d');
    $startdate = $_POST['start_date'] ?? ($_GET['start_date'] ?? date('Y-m-d'));
    $enddate = $_POST['end_date'] ?? ($_GET['end_date'] ?? date('Y-m-d'));

    if ($startdate > $today) {
        $_SESSION['error'] = 'የሪፖርት መጀመሪያ ቀን ከዛሬ ቀን በኋላ መሆን የለበትም';
        header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/report-registration");
        exit(); 
    }
    if ($enddate > $today) {
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
        'report1'    => $finalReport,
        'branchData' => $branchData,
        'startdate'  => $startdate,
        'enddate'    => $enddate,
        'myBranchId' => $myBranchId // 💡 ይህ ለቪው ሊንክ መስሪያ እንዲያገለግል ወደ ቪው ተልኳል
    ]);
}
public function report10Show()
{
    AuthHelper::checkRole(['team_leader', 'officer']);
    
    $sessionBranchId = $_SESSION['user']['branch_id'] ?? '';
    $postedBranchId  = $_POST['branch_id'] ?? ($_GET['branch_id'] ?? null);
    
    $branchModel = new Branch($this->db);
    $myBranchId = !empty($postedBranchId) ? $postedBranchId : $sessionBranchId;
    $myBranchId = (string)$myBranchId;

    $branchData = [];
    if (!empty($myBranchId)) {
        $branchData = $branchModel->getBranchById($myBranchId);
    }

    $startdate = $_POST['start_date'] ?? ($_GET['start_date'] ?? date('Y-m-d'));
    $enddate = $_POST['end_date'] ?? ($_GET['end_date'] ?? date('Y-m-d'));

    // 💡 ማስታወሻ፦ እዚህ ጋር የ Model ኮድህን ጠርተህ ዳታቤዝ ውስጥ ያሉትን የከተማ/ገጠር ስብጥሮች ታመጣለህ
    $reportModel = new ReportgenerationModel($this->db);
    // $reportData = $reportModel->getReport10Data($myBranchId, $startdate, $enddate);

    $this->renderPrintable('report-10', [
        'branchData' => $branchData,
        'startdate'  => $startdate,
        'enddate'    => $enddate,
        'myBranchId' => $myBranchId
    ]);
}
}