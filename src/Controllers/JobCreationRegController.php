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
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
    //error_log("DEBUG DATA: " . print_r($_POST, true));

    // የፎርም መረጃዎች
    $data = [
        'branchid'             => $_SESSION['user']['branch_id'] ?? null,
        'code003_id'           => null,
        'jobseeker_id'         => $_POST['jid'] ?? null,
        'sector'               => $_POST['sector'] ?? null,
        'subsector'            => $_POST['sub_sector'] ?? null,
        'job_creation_reason'  => $_POST['job_category'] ?? null, 
        'employment_type'      => $_POST['job_type'] ?? null,
        'employed_institution' => $_POST['enid'] ?? null,
        'suportedby'           => $_POST['pid'] ?? null, // አሁን ስሙ ትክክል ነው
        'fiscal_year'          => AuthHelper::checkFiscalYear(),
        'job_field'         => $_POST['job_field'] ?? null, // አሁን ስሙ ትክክል ነው
        'registered_by'        => $_SESSION['user']['id'] ?? null
    ];

    // 2. አስፈላጊ የሆኑትን አምዶች ዝርዝር (እነዚህ ባዶ መሆን የለባቸውም)
    $requiredFields = ['branchid', 'jobseeker_id', 'sector', 'subsector', 'employment_type','job_creation_reason'];

    // 3. Validation
    foreach ($requiredFields as $field) {
        if (empty($data[$field])) {
            $_SESSION['error'] = "እባክዎ ሁሉንም አስፈላጊ መረጃዎች በትክክል ይሙሉ! (" . $field . " ክፍት ነው)";
            header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/job-creation-reg"); // ወይም ወደነበረበት ገጽ
            exit();
        }
    }
    //error_log("DATA ARRAY TO INSERT: " . print_r($data, true));

    // ይህንን መረጃ በ Browser ላይ ለማየት (ለሙከራ ብቻ)
    //echo "<pre>"; print_r($data); echo "</pre>"; exit;
    
    $model = new JobCreationModel($this->db);
    try {
        // አዲሱን Transaction እና Update የያዘውን registerJobCreation እንጠራለን
        if ($model->registerJobCreation($data)) {
            $_SESSION['success'] = 'መረጃው በተሳካ ሁኔታ ተመዝግቧል!';
            header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/job-creation-reg");
            exit();
        }
// ኮንትሮለርህ ላይ ያለው catch ብሎክ
} catch (\Exception $e) {
    error_log("DB Error: " . $e->getMessage());
    
    // የሞዴሉን የራሱ መልእክት (Exception message) ለተጠቃሚው አሳይ
    //$_SESSION['error'] = $e->getMessage(); 
    $_SESSION['error'] ="ስራ እድል ፈጠራ ምዝገባዉ አልተሳካም እባከወ መረጃወን በትክክል ያስገቡ በተለይ የስራ ፈላጊ መለያ ቁጥርን በትክክል ያስገቡ ";
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
       
 
}
     