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
    // ሰሽን ቀድሞ ካልተጀመረ ብቻ እንዲጀምር ማድረግ (ፍሬምወርኩ ራሱ የሚጀምረው ከሆነ ይህንን ማጥፋት ትችላለህ)
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // የቅርንጫፍ መታወቂያውን መውሰድ
    $branchId = $_SESSION['user']['branch_id'] ?? null;
    
    // ከሞዴል ዳታውን መሳብ
    $chartsData = $this->reportModel->getDashboardChartsDataacall($branchId);

    // ሞዴሉ የሰጠው ምላሽ ባዶ ከሆነ ወይም የተሳሳተ ፎርማት ከሆነ መከላከል (የቁልፍ ስሞችን ከሞዴሉ ጋር ማጣጣም)
    if (empty($chartsData) || !isset($chartsData['gender'])) {
        $chartsData = [
            'gender' => ['ወንድ' => 0, 'ሴት' => 0]
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
    $startdate = (!empty(trim($rawStartDate))) ? trim($rawStartDate) : '2026-07-08';
    $firstchoice='2026-07-08';
    
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
    
    $sessionBranchId = $_SESSION['user']['branch_id'] ?? null;
    $postedBranchId  = $_POST['branch_id'] ?? ($_GET['branch_id'] ?? null);
    
    $branchData = [];
    $branchModel = new Branch($this->db);

    if (!empty($postedBranchId)) {
        $myBranchId = $postedBranchId;
    } else {
        $myBranchId = $sessionBranchId;
    }

    $myBranchId = (string)$myBranchId;

    if (!empty($myBranchId)) {
        $branchData = $branchModel->getBranchById($myBranchId);
    }

    $today = date('Y-m-d');

    $rawStartDate = $_POST['start_date'] ?? ($_GET['start_date'] ?? '');
    $rawEndDate = $_POST['end_date'] ?? ($_GET['end_date'] ?? '');

    $startdate = (!empty(trim($rawStartDate))) ? trim($rawStartDate) : '2026-07-08';
    $firstchoice = '2026-07-08';
    $enddate = (!empty(trim($rawEndDate))) ? trim($rawEndDate) : $today;

    // የቅርንጫፍ ስም ማስተካከያ
    $selectedBranchName = $branchData['name'] ?? ($branchData['branch_name'] ?? 'ያልታወቀ መዋቅር');

    // የኢትዮጵያ ቀናትን እዚህ ኮንትሮለሩ ላይ እናስላለን
    $ethstartDate = null;
    $ethendDate = null;

    if (class_exists('EthiopianDateHelper')) {
        // የጀማሪ ቀን ቅያሬ
        $startDateParts = explode('-', $startdate);
        if (count($startDateParts) === 3) {
            $ethstartDate = EthiopianDateHelper::toEthiopian((int)$startDateParts[0], (int)$startDateParts[1], (int)$startDateParts[2]);
        }

        // የማጠናቀቂያ ቀን ቅያሬ
        $endDateParts = explode('-', $enddate);
        if (count($endDateParts) === 3) {
            $ethendDate = EthiopianDateHelper::toEthiopian((int)$endDateParts[0], (int)$endDateParts[1], (int)$endDateParts[2]);
        }
    }

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
    $this->renderPrintable('report-10', [
        'report1'    => $finalReport,
        'branchData' => $branchData,
        'startdate'  => $startdate,
        'enddate'    => $enddate,
        'myBranchId' => $myBranchId // 💡 ይህ ለቪው ሊንክ መስሪያ እንዲያገለግል ወደ ቪው ተልኳል
    ]);
}

public function report4Show()
{
    AuthHelper::checkRole(['team_leader', 'officer']);
    
    $sessionBranchId = $_SESSION['user']['branch_id'] ?? null;
    
    // 💡 ማስተካከያ 1፦ ዳታውን ከ POST ካጣው ከ GET (ከሊንኩ ላይ) እንዲፈልግ ተደርጓል
    $postedBranchId  = $_POST['branch_id'] ?? ($_GET['branch_id'] ?? null);
     $report_type  = $_POST['report_type'] ?? ($_GET['report_type'] ?? null);

    $ketemaAstedader = $_SESSION['user']['ketema_astedader'] ?? false;
     $residenceStatus=null;
     $selectedreport_type=null;
    // 💡 አዲስ ማሻሻያ፦ የነዋሪነት ሁኔታ ማጣሪያን (residence_status) ከ POST ወይም ከ GET መቀበል
    if( $report_type=="ሠ4"){
 $residenceStatus = 'ከተማ';
 //$selectedreport_type="report-4";
    }elseif($report_type=="ሠ5"){
 $residenceStatus = 'ገጠር';
   //$selectedreport_type="report-4";
    }
    else{

    }
  
    
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
    // 🛠️ ማስተካከያ፦ 'empy()' የነበረው የፊደል ስህተት ወደ 'empty()' ተስተካክሏል
    $startdate = (!empty(trim($rawStartDate))) ? trim($rawStartDate) : '2026-07-08';
    $firstchoice = '2026-07-08';
    
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
    $startDateTime = $startdate . ' 00:00:00';
    $endDateTime = $enddate . ' 23:59:59';

    // 💡 ከላይ ካለው ቪው መዋቅር ጋር ለማገናኘት የሪፖርት ሞዴሉን ጠርተን ዳታውን እናመጣለን
    $reportModel = new ReportgenerationModel($this->db);
    
    // ከ $branchData ላይ የቅርንጫፉን ስም መውሰጃ
    $branchName = $branchData['name'] ?? 'የተመረጠው ቅርንጫፍ';

    // ለቪው እንዲመች ዳታውን በየዘርፉ (ግብርና፣ ኢንዱስትሪ፣ አገልግሎት) የመደብንበት $reportData
    // 💡 ማሻሻያ፦ ሴክተርን ሳናሳልፍ አዲሱን $residenceStatus በፓራሜትር ወደ ሞዴሉ አሳልፈናል
    $reportData = [
        'ግብርና' => $reportModel->getJobSeekers04ByHierarchy($myBranchId, $startDateTime, $endDateTime, $residenceStatus, 'ግብርና'),
        'ኢንዱስትሪ' => $reportModel->getJobSeekers04ByHierarchy($myBranchId, $startDateTime, $endDateTime, $residenceStatus, 'ኢንዱስትሪ'),
        'አገልግሎት' => $reportModel->getJobSeekers04ByHierarchy($myBranchId, $startDateTime, $endDateTime, $residenceStatus, 'አገልግሎት'),
    ];

    // 🛠️ ማስተካከያ፦ የነበረው ስህተት ተወግዶ በ $this->renderPrintable ቪው እንዲጠራ ተደርጓል
    // 💡 ማሻሻያ፦ $residenceStatus ለቪው ፎርም/ሄደር አገልግሎት እንዲውል አብሮ ተላልፏል
        
  return $this->renderPrintable('report-4', [
        'reportData'         => $reportData,
        'selectedBranchName' => $branchName,
        'startdate'          => $startdate,
        'enddate'            => $enddate,
        'residenceStatus'    => $residenceStatus
    ]);
     
    
   
}

public function report6Show()
{
    AuthHelper::checkRole(['team_leader', 'officer']);
    
    $sessionBranchId = $_SESSION['user']['branch_id'] ?? null;
    
    $postedBranchId  = $_POST['branch_id'] ?? ($_GET['branch_id'] ?? null);
    $report_type     = $_POST['report_type'] ?? ($_GET['report_type'] ?? null);

    $ketemaAstedader = $_SESSION['user']['ketema_astedader'] ?? false;
    $residenceStatus = null;
    $selectedreport_type = null;

    if ($report_type == "ሠ6") {
        $residenceStatus = 'ከተማ';
        $selectedreport_type = "report-6";
    } elseif ($report_type == "ሠ7") {
        $residenceStatus = 'ገጠር';
        $selectedreport_type = "report-6";
    } else {
        $residenceStatus = 'ከተማ';
        $selectedreport_type = "report-6";
    }

    $branchData = [];
    $branchModel = new Branch($this->db);

    if (!empty($postedBranchId)) {
        $myBranchId = $postedBranchId;
    } else {
        $myBranchId = $sessionBranchId;
    }

    $myBranchId = (string)$myBranchId;

    if (!empty($myBranchId)) {
        $branchData = $branchModel->getBranchById($myBranchId);
    }

    $today = date('Y-m-d');

    $rawStartDate = $_POST['start_date'] ?? ($_GET['start_date'] ?? '');
    $rawEndDate = $_POST['end_date'] ?? ($_GET['end_date'] ?? '');

    $startdate = (!empty(trim($rawStartDate))) ? trim($rawStartDate) : '2026-07-08';
    $firstchoice = '2026-07-08';
    
    $enddate = (!empty(trim($rawEndDate))) ? trim($rawEndDate) : $today;

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

    $startDateTime = $startdate . ' 00:00:00';
    $endDateTime = $enddate . ' 23:59:59';

    $reportModel = new ReportgenerationModel($this->db);
    
    $branchName = $branchData['name'] ?? 'የተመረጠው ቅርንጫፍ';

    $reportData = [
        'ግብርና' => $reportModel->getJobSeekers06ByHierarchy($myBranchId, $startDateTime, $endDateTime, $residenceStatus, 'ግብርና'),
        'ኢንዱስትሪ' => $reportModel->getJobSeekers06ByHierarchy($myBranchId, $startDateTime, $endDateTime, $residenceStatus, 'ኢንዱስትሪ'),
        'አገልግሎት' => $reportModel->getJobSeekers06ByHierarchy($myBranchId, $startDateTime, $endDateTime, $residenceStatus, 'አገልግሎት'),
    ];

    return $this->renderPrintable('report-6', [
        'reportData'         => $reportData,
        'selectedBranchName' => $branchName,
        'startdate'          => $startdate,
        'enddate'            => $enddate,
        'residenceStatus'    => $residenceStatus
    ]);  
}


public function report8Show()
{
    AuthHelper::checkRole(['team_leader', 'officer']);
    
    $sessionBranchId = $_SESSION['user']['branch_id'] ?? null;
    
    $postedBranchId  = $_POST['branch_id'] ?? ($_GET['branch_id'] ?? null);
    $report_type     = $_POST['report_type'] ?? ($_GET['report_type'] ?? null);

    $residenceStatus = null;

    if ($report_type == "ሠ8") {
        $residenceStatus = 'ከተማ';
    } elseif ($report_type == "ሠ9") {
        $residenceStatus = 'ገጠር';
    } else {
        $residenceStatus = 'ከተማ';
    }

    $branchData = [];
    $branchModel = new Branch($this->db);

    $myBranchId = !empty($postedBranchId) ? $postedBranchId : $sessionBranchId;
    $myBranchId = (string)$myBranchId;

    if (!empty($myBranchId)) {
        $branchData = $branchModel->getBranchById($myBranchId);
    }

    $today = date('Y-m-d');

    $rawStartDate = $_POST['start_date'] ?? ($_GET['start_date'] ?? '');
    $rawEndDate = $_POST['end_date'] ?? ($_GET['end_date'] ?? '');

    $startdate = (!empty(trim($rawStartDate))) ? trim($rawStartDate) : '2026-07-08';
    $firstchoice = '2026-07-08';
    $enddate = (!empty(trim($rawEndDate))) ? trim($rawEndDate) : $today;

    if ($startdate < $firstchoice || $startdate > $today || $enddate > $today || $startdate > $enddate) {
        $_SESSION['error'] = 'የተሳሳተ የሪፖርት ቀን መርጠዋል።';
        header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/report-registration");
        exit();
    }

    $startDateTime = $startdate . ' 00:00:00';
    $endDateTime = $enddate . ' 23:59:59';

    $reportModel = new ReportgenerationModel($this->db);
    $branchName = $branchData['name'] ?? 'የተመረጠው ቅርንጫፍ';

    $reportData = $reportModel->getJobSeekers08ByHierarchy($myBranchId, $startDateTime, $endDateTime, $residenceStatus);

    return $this->renderPrintable('report-8', [
        'reportData'         => $reportData,
        'selectedBranchName' => $branchName,
        'startdate'          => $startdate,
        'enddate'            => $enddate,
        'residenceStatus'    => $residenceStatus
    ]);  
}
}