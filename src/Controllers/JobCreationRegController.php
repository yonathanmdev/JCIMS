<?php
namespace App\Controllers;
use App\Helpers\AuthHelper;
use App\Models\SectorModel;
use Ramsey\Uuid\Uuid;
use App\Models\ProjectNgoModel;

class JobCreationRegController extends BaseController {

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
}
    ?>