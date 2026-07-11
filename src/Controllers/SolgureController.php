<?php
namespace App\Controllers;
use App\Helpers\AuthHelper;
use App\Helpers\AmharicNormalizer;
use App\Models\SolgureModel;
 
use Ramsey\Uuid\Uuid;
use Exception;
class SolgureController extends BaseController {
   
    public function sulgureshowRegisterForm() {
AuthHelper::checkRole(['team_leader', 'officer']);
    $myBranchId = $_SESSION['user']['branch_id'];

   
    $solgureModel = new SolgureModel($this->db);
    $limit = 50;
    $currentPage = max(1, (int)($_GET['page'] ?? 1));
    $offset = ($currentPage - 1) * $limit;

    $jobSeekers = $solgureModel->getJobSeekersByHierarchy($myBranchId, $limit, $offset);
    $totalCount = $solgureModel->countJobSeekersByHierarchy($myBranchId);
    $totalPages = (int)ceil($totalCount / $limit);

    $data = [
        'title'       => 'JCIMS - የሰራተኛ መመዝገቢያ',
        'jobSeekers'  => $jobSeekers,
        
        'offset'      => $offset,        // ADD THIS
        'currentPage' => $currentPage,   // ADD THIS
        'totalPages'  => $totalPages,    // ADD THIS
        'totalCount' => $totalCount
    ];

 

        $this->render('solgure-regstration', $data);
    }
   public function processRegistration() {
    AuthHelper::checkRole(['team_leader', 'officer']);
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   

        // ግብዓቶችን ማጽዳት (Sanitize & Trim)
        $fullname          = trim($_POST['fullname'] ?? '');
        $nationalId        = trim($_POST['national_id'] ?? '');
        $sex               = trim($_POST['sex'] ?? '');
        $age               = trim($_POST['age'] ?? '');
        $phone             = trim($_POST['phone'] ?? '');
        $kebele            = trim($_POST['kebele'] ?? '');
        $sector            = trim($_POST['sector'] ?? '');
        $additional_skill  = trim($_POST['additional_skill'] ?? ''); // 's' ተወግዷል
        $educated_study    = trim($_POST['educated_study'] ?? '');
        $education_level   = trim($_POST['education_level'] ?? '');
        
        // branch_id እና መዝጋቢውን ከ Session መውሰድ
        $branchId          = trim($_SESSION['user']['branch_id'] ?? ''); 
        $userid            = trim($_SESSION['user']['id'] ?? '');

        // ለሞዴል የሚላከው ዳታ
        $data = [
            'fullname'          => $fullname,
            'national_id'       => $nationalId,
            'sex'               => $sex,
            'age'               => $age,
            'phone'             => $phone,
            'branch_id'         => $branchId,
            'kebele'            => $kebele,
            'sector'            => $sector,
            'additional_skill'  => $additional_skill,
            'educated_study'    => $educated_study,
            'education_level'   => $education_level,
            'registered_by'     => $userid, // ዳታቤዝህ ላይ ካለው ስም ጋር አዛምደው
            
        ];

        $solgureModel = new SolgureModel($this->db);
        $success = $solgureModel->saveCandidate($data);
        
        if ($success) {
            $_SESSION['success'] = 'የመከላከያ ሰራዊት ምልመላዉ በትክክል ተመዝግቧል።';
        } else {
            $_SESSION['error'] = 'የመከላከያ ሰራዊት ምልመላዉ መመዝገቢያ አልተሳካም። እባክዎ በድጋሚ ይሞክሩ።';
        }
        
        header('Location: ' . rtrim($_ENV['BASE_URL'], '/') . '/solgure-registration');
        exit();
    } else {
        header('Location: ' . rtrim($_ENV['BASE_URL'], '/') . '/solgure-registration');
        exit();
    }
}
public function getDetails() {
    // 1. የደህንነት/የፈቃድ ማረጋገጫ (Auth Check) እዚህ ጋር መያዝ ትችላለህ
    
    header('Content-Type: application/json; charset=utf-8');

    // 2. ID ማጣራት
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ትክክለኛ ያልሆነ መለያ (ID)።']);
        exit;
    }

    try {
        // 3. ሞዴሉን ጠርቶ ዳታውን መቀበል (የሞዴልህን ኢንስታንስ እንደ አሰራርህ ጥራው)
        $model = new SolgureModel($this->db); 
        $record = $model->getDefenseRecordById($id);

        if ($record) {
            echo json_encode(['success' => true, 'record' => $record]);
        } else {
            echo json_encode(['success' => false, 'message' => 'መዝገቡ አልተገኘም።']);
        }
    } catch (Exception $e) {
        // ለደህንነት ሲባል የውስጥ ስህተቶችን ደብቆ አጠቃላይ መልዕክት መስጠት
        echo json_encode(['success' => false, 'message' => 'የሲስተም ስህተት አጋጥሟል።']);
    }
    exit; // የ MVC View እንዳይቀጥል እዚህ ላይ ያበቃል
}
}