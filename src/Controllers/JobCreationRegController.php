<?php
namespace App\Controllers;
use App\Helpers\AuthHelper;
use App\Models\SectorModel;
use Ramsey\Uuid\Uuid;
use App\Models\ProjectNgoModel;
use App\Models\JobSeekerModel;
use App\Models\JobCreationModel;


class JobCreationRegController extends BaseController {

public function getJobSeekerData() {
        $JobSeekerModel = new JobSeekerModel($this->db);
        $term = $_GET['q'] ?? '';
        $branchId = $_SESSION['user']['branch_id']; // የUser session branch
        $fiscal_year =AuthHelper::checkFiscalYear();
        
        $results = $JobSeekerModel->searchJobSeekerjobcreation($term, $branchId,$fiscal_year);
        
        header('Content-Type: application/json');
        echo json_encode($results);
        exit;
    }
   public function showRegisterForm() {
    AuthHelper::checkRole(['team_leader', 'officer']);
    $sectorModel = new SectorModel($this->db);
    $sectors = $sectorModel->getAllSectors();
    $ngoModel = new ProjectNgoModel($this->db);
    $projectNgos = $ngoModel->getAllProjectNgos();
    
    $data = [
        'title' => 'JCIMS - ስራ እድል መመዝገቢያ',
        'sectors' => $sectors,
        'projectNgos' => $projectNgos
    ];
    $this->render('job-creation-reg', $data);
}

// ለAJAX ጥያቄ ብቻ
public function getSubSectors() {
    $sectorId = $_GET['sector_id'] ?? null;
    $sectorModel = new SectorModel($this->db);
    $subSectors = $sectorModel->getSubSectorsBySector($sectorId);
    
    header('Content-Type: application/json');
    echo json_encode($subSectors);
    exit; // ይህ በጣም አስፈላጊ ነው!
}
public function processRegistration() {
        // HTTP Request method ቼክ ማድረግ
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/job-creation-reg");
            exit();
        }

        // የሥራ እድል ፈጠራው ምክንያት አዲስ ኢንተርፕራይዝ ማቋቋም መሆኑን ማረጋገጥ
        $jobCategory = $_POST['job_category'] ?? null;
        $isNewEnterprise = ($jobCategory === "አዳዲስ ኢንተርፕራይዞች በማቋቋም የተፈጠረ ሥራ");

        if ($isNewEnterprise) {
            // አዳዲስ ኢንተርፕራይዞች ሲሆኑ sector እና subsector በ ሞዴሉ በኩል በራስ-ሰር ይፈለጋሉ
            $data = [
                'branchid'             => $_SESSION['user']['branch_id'] ?? null,
                'code003_id'           => $_POST['enid'] ?? null,
                'jobseeker_id'         => $_POST['jid'] ?? null,
                'sector'               => null, // በ Model ውስጥ ከትክክለኛው ኢንተርፕራይዝ ይሞላል
                'subsector'            => null, // በ Model ውስጥ ከትክክለኛው ኢንተርፕራይዝ ይሞላል
                'job_creation_reason'  => $jobCategory, 
                'employment_type'      => $_POST['job_type'] ?? null,
                'employed_institution' => $_POST['enid'] ?? null,
                'suportedby'           => $_POST['pid'] ?? null,
                'fiscal_year'          => AuthHelper::checkFiscalYear(),
                'job_field'            => $_POST['job_field'] ?? null,
                'registered_by'        => $_SESSION['user']['id'] ?? null
            ];

            // ለአዳዲስ ኢንተርፕራይዝ አስፈላጊ የሆኑ ፊልዶች
            $requiredFields = ['branchid', 'code003_id', 'jobseeker_id', 'employment_type', 'job_creation_reason'];

        } else {
            // ለሌሎች የሥራ እድል ፈጠራ ዓይነቶች መረጃው ከፎርሙ ይወሰዳል
            $data = [
                'branchid'             => $_SESSION['user']['branch_id'] ?? null,
                'code003_id'           => null,
                'jobseeker_id'         => $_POST['jid'] ?? null,
                'sector'               => $_POST['sector'] ?? null,
                'subsector'            => $_POST['sub_sector'] ?? null,
                'job_creation_reason'  => $jobCategory, 
                'employment_type'      => $_POST['job_type'] ?? null,
                'employed_institution' => $_POST['enid'] ?? null,
                'suportedby'           => $_POST['pid'] ?? null,
                'fiscal_year'          => AuthHelper::checkFiscalYear(),
                'job_field'            => $_POST['job_field'] ?? null,
                'registered_by'        => $_SESSION['user']['id'] ?? null
            ];

            // ለሌሎች ዓይነቶች አስፈላጊ የሆኑ ፊልዶች
            $requiredFields = ['branchid', 'jobseeker_id', 'sector', 'subsector', 'employment_type', 'job_creation_reason'];
        }

        // 1. Validation: አስፈላጊ የሆኑ መረጃዎች መሞላታቸውን ማረጋገጥ
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                $_SESSION['error'] = "እባክዎ ሁሉንም አስፈላጊ መረጃዎች በትክክል ይሙሉ! (" . htmlspecialchars($field, ENT_QUOTES, 'UTF-8') . " ክፍት ነው)";
                header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/job-creation-reg");
                exit();
            }
        }

        // 2. በ Model በኩል መረጃውን መዝግቦ መያዝ
        $model = new JobCreationModel($this->db);

        try {
            if ($model->registerJobCreation($data)) {
                $_SESSION['success'] = 'የሥራ እድል የተፈጠረለት መረጃ በጥሩ ሁኔታ ተመዝግቧል!';
                header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/job-creation-reg");
                exit();
            }
        } catch (\Exception $e) {
            // የስህተት መረጃዎችን ወደ log መጻፍ
            error_log("Job Creation Registration DB Error: " . $e->getMessage());

            // ከ Model የመጣውን የ exception መልእክት ለተጠቃሚው ማሳየት (ለምሳሌ የቋሚ ቅጥር ድግግሞሽ ወይም የዘርፍ ማጣራት ችግር)
            $_SESSION['error'] = $e->getMessage();
            header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/job-creation-reg");
            exit();
        }
    }


    
    public function jobcreationcreatedview() {
    $branchid = $_SESSION['user']['branch_id'];
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = 30;
    $offset = ($page - 1) * $limit;

    $model = new JobCreationModel($this->db);
    $jobCreations = $model->getJobCreationsWithDetails($branchid, $offset, $limit);
    $totalRecords = $model->getTotalCount($branchid);
    $totalPages = ceil($totalRecords / $limit);

    $this->render('job-creation-list', [
        'jobCreations' => $jobCreations,
        'currentPage' => $page,
        'totalPages' => $totalPages
    ]);
}

public function deletejobcretion() {
    $uuid = $_GET['uuid'] ?? null;
    $js_id = $_GET['jobseeker_id'] ?? null;
    $reason = $_POST['reason'] ?? 'የተሳሳተ መረጃ';
    $userId = $_SESSION['user']['id'];
    $branchid = $_SESSION['user']['branch_id'];
    // ከURL የመጣውን branchid ከመጠቀም ይልቅ፣ የሴሽኑን ብቻ ተጠቀም (ለደህንነት)
    //$branchid = $_SESSION['user']['branch_id'] ?? null;

    // ማረጋገጫ (Validation)
    if (!$uuid || !$branchid || !$js_id) {
        $_SESSION['error'] = "የተሟላ መረጃ የለም ወይም የመዳረሻ ፍቃድ የለዎትም።";
        header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/jobcreation-list");
        exit();
    }

    $model = new JobCreationModel($this->db);
    



if ($model->deletearchiveJobCreation($uuid, $branchid, $js_id, $reason, $userId)) {
    $_SESSION['success'] = 'መረጃው በተሳካ ሁኔታ ተመዝግቦ ወደ አርካይቭ ተዛውሯል!';
}else {
        $_SESSION['error'] = "ስህተት ተፈጥሯል፤ መረጃው አልተሰረዘም።";
    }
    
  // የትኛውንም አይነት የRedirect ሉፕ ለማስቀረት
header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/jobcreation-list");
exit(); // exit() መጠቀም በጣም አስፈላጊ ነው
}
public function getEnterpriseList(): void {
        // የ JSON Header ማስተካከያ
        header('Content-Type: application/json; charset=utf-8');

        // 1. Session መጀመሩን እና Branch ID መኖሩን ማረጋገጥ
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // በሲስተምህ Session አሰያየም መሰረት ($ _SESSION['branchid'] ወይም $_SESSION['branch_id'])
       // $branchId = $_SESSION['branchid'] ?? $_SESSION['branch_id'] ?? null;
        $branchId = $_SESSION['user']['branch_id'];

        if (!$branchId) {
            http_response_code(401); // Unauthorized
            echo json_encode(['error' => 'ያልተፈቀደ አክሰስ ወይም Branch ID አልተገኘም'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // 2. ከ Frontend (AJAX) የመጣውን የፍለጋ ቃል መቀበል እና ማጽዳት
        $search = isset($_GET['q']) ? trim($_GET['q']) : '';

        if (mb_strlen($search) < 2) {
            echo json_encode([]);
            exit;
        }

        try {
              $JobCreationModel = new JobCreationModel($this->db);
            // 3. ከ Model ላይ የመፈለጊያ ተግባሩን መጥራት
            $enterprises = $JobCreationModel->searchEnterprisesByBranch($search, $branchId);
            
            // 4. ህጋዊ JSON Response መመለስ
            echo json_encode($enterprises, JSON_UNESCAPED_UNICODE);
            exit;

        } catch (\PDOException $e) {
            // ለደህንነት ሲባል የዳታቤዝ ዝርዝር ስህተትን በ Log መያዝ እንጂ ለተጠቃሚው አለማሳየት
            error_log("Database Error in EnterpriseController: " . $e->getMessage());
            
            http_response_code(500);
            echo json_encode(['error' => 'የዳታቤዝ ስህተት ተፈጥሯል'], JSON_UNESCAPED_UNICODE);
            exit;
        }
    }       
 
}
     