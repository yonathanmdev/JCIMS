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

public function awarenessallanalyticsShow()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // የቅርንጫፍ መታወቂያውን መውሰድ
    $branchId = $_SESSION['user']['branch_id'] ?? null;
    
    // ከሞዴል ዳታውን መሳብ
    $chartsData = $this->reportModel->getDashboardChartsDataacall($branchId);

    // ዳታው በሆነ ምክንያት NULL ወይም ባዶ ከሆነ እንዳይበላሽ በአዲሱ የሞዴል መዋቅር መከላከል
    if (!$chartsData) {
        $chartsData = [
            'gender'           => ['በስራ ፈላጊዎች' => 0, 'በስራ ፈላጊ ወላጆች' => 0, 'በሌሎች የህብረተሰብ ክፍሎች' => 0],
            'jobSeekersGender' => ['ወንድ' => 0, 'ሴት' => 0],
            'residence'        => ['ወንድ' => 0, 'ሴት' => 0], // ለቪው 'residence' የሚለው በስራ ፈላጊዎች ጾታ ላይ ይሳላል
            'physical'         => ['0' => 0, '1' => 0],
            'education'        => ['ሌሎች' => 0],
            'status'           => ['ሌሎች' => 0]
        ];
    }

    // ዳታውን ወደ ቪው መላክ
    $this->render('/awareness-all-analytics', [
        'title'      => 'የግንዛቤ ፈጠራ ትንታኔ',
        'chartsData' => $chartsData
    ]);
}


public function awarnessAnalyticsShow()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // የቅርንጫፍ መታወቂያውን መውሰድ
    $branchId = $_SESSION['user']['branch_id'] ?? null;
    
    // ከሞዴል ዳታውን መሳብ
    $chartsData = $this->reportModel->getDashboardChartsDataac($branchId);

    // ዳታው በሆነ ምክንያት NULL ከሆነ እንዳይበላሽ መከላከል
    if (!$chartsData) {
        $chartsData = [
            'gender'    => ['ወንድ' => 0, 'ሴት' => 0],
            'residence' => ['ከተማ' => 0, 'ገጠር' => 0],
            'physical'  => ['መደበኛ' => 0, 'አካል ጉዳተኛ' => 0],
            'education' => ['ያልተገለጸ' => 0],
            'status'    => ['ያልተገለጸ' => 0]
        ];
    }

    // ያለ ምንም nonce በቀጥታ ወደ ቪው መላክ
    $this->render('/awareness-analytics', [
        'title'      => 'የግንዛቤ ፈጠራ ትንታኔ',
        'chartsData' => $chartsData
    ]);
}

public function seekerAnalyticsShow()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // የቅርንጫፍ መታወቂያውን መውሰድ
    $branchId = $_SESSION['user']['branch_id'] ??  null;
    
    // ከሞዴል ዳታውን መሳብ
    $chartsData = $this->reportModel->getDashboardChartsDatajs($branchId);

    // ዳታው በሆነ ምክንያት NULL ከሆነ እንዳይበላሽ መከላከል
    if (!$chartsData) {
        $chartsData = [
            'gender'    => ['ወንድ' => 0, 'ሴት' => 0],
            'residence' => ['ከተማ' => 0, 'ገጠር' => 0],
            'physical'  => ['መደበኛ' => 0, 'አካል ጉዳተኛ' => 0],
            'education' => ['ያልተገለጸ' => 0],
            'status'    => ['ያልተገለጸ' => 0]
        ];
    }

    // ያለ ምንም nonce በቀጥታ ወደ ቪው መላክ
    $this->render('/seeker-analytics', [
        'title'      => 'የስራ ፈላጊዎች ስታቲስቲክስ ትንታኔ',
        'chartsData' => $chartsData
    ]);
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
    
    $sessionBranchId = $_SESSION['user']['branch_id'] ?? null;
    
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

    // 1. መጀመሪያ ከ POST ወይም ከ GET መምጣቱን ቼክ ማድረግ፤ ባዶ ከሆኑም default ቀኑን መስጠት
    $rawStartDate = $_POST['start_date'] ?? ($_GET['start_date'] ?? '');
    $rawEndDate = $_POST['end_date'] ?? ($_GET['end_date'] ?? '');

    // 2. ተጠቃሚው ካልመረጠው (ባዶ ከሆነ) default ቀናትን እዚህ ላይ እንሰጣለን
    // መጀመሪያ ቀን ካልተመረጠ Default '2026-07-07' ይሆናል
    $startdate = (!empty(trim($rawStartDate))) ? trim($rawStartDate) : '2026-07-07';
    $firstchoice='2026-07-07';
    
    // መጨረሻ ቀን ካልተመረጠ Default የዛሬ ቀን ($today) ይሆናል
    $enddate = (!empty(trim($rawEndDate))) ? trim($rawEndDate) : $today;
    // የቀናት ማረጋገጫ (Validation) በንፁህ ቀን (Y-m-d) ይሰራል።
    if ($startdate < $firstchoice) {
        $_SESSION['error'] = 'የሪፖርት መጀመሪያ ቀን በጀት ዓመት ከመጀመሩ በፊት መሆን የለበትም';
        header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/report-registration");
        exit(); 
    }
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

    // 💡 ማሻሻያ፦ DATETIME ን በትክክል ለማወዳደር የመነሻ እና የማጠናቀቂያ ሰዓት መጨመር
    // ይህ በመጨረሻው ቀን 23:59:59 ድረስ የተመዘገቡ ዳታዎች እንዳያመልጡ ያደርጋል
    $startDateTime = $startdate . ' 00:00:00';
    $endDateTime = $enddate . ' 23:59:59';

    $awarenessModel = new ReportgenerationModel($this->db);

    // 1. ከመጀመሪያው ቴብል ዳታውን ያመጣል (ሰዓት የተጨመረበትን ተለዋዋጭ በመጠቀም)
    $awarenessReport = $awarenessModel->getReport1ByHierarchy($myBranchId, $startDateTime, $endDateTime);

    // 2. ከሁለተኛው (ከአዲሱ) ቴብል የምክርና መረጃ ዳታውን ያመጣል (ሰዓት የተጨመረበትን ተለዋዋጭ በመጠቀም)
    $adviceReport = $awarenessModel->getJobSeekersAdviceByHierarchy($myBranchId, $startDateTime, $endDateTime);

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