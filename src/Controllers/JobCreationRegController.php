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
        'code003_id'           =>  null,
        'jobseeker_id'         => $_POST['jid'] ?? null,
        'sector'               => $_POST['sector'] ?? null,
        'subsector'            => $_POST['sub_sector'] ?? null,
        'job_creation_reason'  => $_POST['job_category'] ?? null, 
        'employment_type'      => $_POST['job_type'] ?? null,
        'employed_institution' => $_POST['enid'] ?? null,
        'suportedby'           => $_POST['pid'] ?? null, // አሁን ስሙ ትክክል ነው
        'fiscal_year'          => AuthHelper::checkFiscalYear(),
        'registered_by'        => $_SESSION['user']['id'] ?? null
    ];
    //error_log("DATA ARRAY TO INSERT: " . print_r($data, true));
 
    // ይህንን መረጃ በ Browser ላይ ለማየት (ለሙከራ ብቻ)
     //echo "<pre>"; print_r($data); echo "</pre>"; exit;
        $model = new JobCreationModel($this->db);
        try {
            if ($model->registerJobCreation($data)) {
               // echo "መረጃው በተሳካ ሁኔታ ተመዝግቧል!";
                 $_SESSION['success'] = 'መረጃው በተሳካ ሁኔታ ተመዝግቧል!';
                   header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/job-creation-reg");
            }
        } catch (\PDOException $e) {
            // Foreign Key ስህተትን ለመለየት
            error_log("DB Error: " . $e->getMessage());
             $_SESSION['error'] = 'ስራ ፈላጊ መመዝገቢያ ሂደት አልተሳካም።'.error_log("DB Error: " . $e->getMessage());
           // echo "ስህተት፡ የተመረጠው መረጃ (ID) በስርዓቱ ውስጥ የለም።";
             header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/job-creation-reg");
    exit();
        }
    }
}
    ?>