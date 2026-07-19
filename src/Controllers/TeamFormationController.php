<?php
namespace App\Controllers;

use App\Helpers\AuthHelper;
use App\Helpers\AuditHelper;
use App\Models\TeamFormationModel;
use App\Models\JobSeekerModel;
use App\Models\SectorModel;
use Exception;
use Ramsey\Uuid\Uuid;

class TeamFormationController extends BaseController {
    public function teamFormation()
    {
        // Add this to prevent PHP warnings from being included in your JSON response
    ini_set('display_errors', '0'); 
    error_reporting(0);
        AuthHelper::checkRole(['team_leader', 'officer'], [3, 4]);
        header('Content-Type: application/json');

        // 1. Method and CSRF Security Check
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

       
        // 2. Input Sanitization
        $sector_id   = trim($_POST['sector_id'] ?? '');
        $sub_sector_name = trim($_POST['sector_name'] ?? '');
        $yesramesk   = trim($_POST['yesra_mesk'] ?? '');   
        $orgType     = trim($_POST['organization_type'] ?? '');
        $place       = trim($_POST['place'] ?? '');
        $assoName    = trim($_POST['asso_name'] ?? '');
        $leaderId    = trim($_POST['leader_id'] ?? '');
        $coId        = trim($_POST['co_id'] ?? '');
        $financeId   = trim($_POST['finance_id'] ?? '');
        $procId      = trim($_POST['procurement_id'] ?? '');
        $phone       = trim($_POST['manager_phone'] ?? '');
        $ngoId       = trim($_POST['ngo_id'] ?? '');
        $selected    = $_POST['selected_jobseekers'] ?? [];
 if ($orgType === 'family') {
            $orgType = 'የቤተሰብ';
        } elseif ($orgType === 'self_interest') {
            $orgType = 'በራስ ፍላጎት';
        } elseif ($orgType === 'special_case') {
            $orgType = 'በልዩ ሁኔታ';
        } elseif ($orgType === 'government_project') {
            $orgType = 'የመንግስት';
        } elseif ($orgType === 'ngo') {
            $orgType = 'NGO';
        }
 
        $allowedOrgTypes = ['NGO', 'የመንግስት', 'በራስ ፍላጎት', 'የቤተሰብ', 'በልዩ ሁኔታ'];
          if (!in_array($orgType, $allowedOrgTypes, true)) {
            echo json_encode(['success' => false, 'message' => 'ልክ ያልሆነ የአደረጃጀት አይነት']);
            return;
        }
        // 3. Validation
        $requiredValues = compact('sector_id', 'sub_sector_name', 'yesra_mesk', 'orgType','place', 'assoName', 'leaderId', 'coId', 'financeId', 'procId', 'phone');
        foreach ($requiredValues as $value) {
            if ($value === '') {
                echo json_encode(['success' => false, 'message' => 'እባክዎ ሁሉንም መስኮች ይሙሉ']);
                return;
            }
        }

        if (!is_array($selected) || count($selected) < 2) {
            echo json_encode(['success' => false, 'message' => 'እባክዎ ቢያንስ ሁለት ስራ ፈላጊዎች ይምረጡ']);
            return;
        }

        // Ensure roles belong to selected set
        $roleValues = [$leaderId, $coId, $financeId, $procId];
        $selectedSet = array_flip(array_map('strval', $selected));
        foreach ($roleValues as $roleId) {
            if (!isset($selectedSet[(string)$roleId])) {
                echo json_encode(['success' => false, 'message' => 'የተመረጠው ሚና ከተመረጡት ስራ ፈላጊዎች ውጪ ነው']);
                return;
            }
        }

        $registered_by = $_SESSION['user']['id'] ?? null;

        // 4. Processing logic inside try-catch
        try {
            $sectorModel = new SectorModel($this->db);
            $ids = $sectorModel->getSubsectorBigIntIds($sub_sector_name);
            if (!$ids) throw new Exception("የተመረጠው ሙያ መረጃ አልተገኘም።");

            $jobSeekerModel = new JobSeekerModel($this->db);
            $allUuids = array_filter(array_unique(array_merge($roleValues, $selected)));
            $idMap = $jobSeekerModel->getJobSeekerIds($allUuids);
            // Add this temporarily
            // Payload Construction
            $payload = [
                'team_id'              => Uuid::uuid7()->toString(),
                'org_type'        => $orgType,
                'yesra_mesk'       => $yesramesk,
                'ngo_id'          => $ngoId ?: null,
                'place'           => $place,
                'asso_name'       => $assoName,
                'manager_phone'   => $phone,
                'leader_id'       => $idMap[$leaderId] ?? null,
                'co_id'           => $idMap[$coId] ?? null,
                'finance_id'      => $idMap[$financeId] ?? null,
                'procurement_id'  => $idMap[$procId] ?? null,
                'member_ids'      => array_map(fn($u) => $idMap[$u], $selected),
                'sector'          => $ids['sectorid'],
                'sub_sector'      => $ids['sub_sectorid'],
                'registered_by'   => $registered_by,
                'branch_id'       => $_SESSION['user']['branch_id'],
                'level'           => $_SESSION['user']['level']
            ];

            $teamFormationModel = new TeamFormationModel($this->db);
            $result = $teamFormationModel->createTeamFormation($payload);

            if ($result['status'] !== 'success') {
                throw new Exception($result['message'] ?? 'ቡድኑን መፍጠር አልተቻለም');
            }

            // Corrected Audit Log
            AuditHelper::log('team_formation_created', 'team_formations', $result['team_formation_id'], null, $payload, [
                'team_number'  => $result['team_number'] ?? null,
                'performed_by' => $registered_by, 
            ]);

            echo json_encode(['success' => true, 'message' => 'ቡድኑ በተሳካ ሁኔታ ተመዝግቧል']);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }

        
    }
public function listGroups()
{
    // Authorization check
    AuthHelper::checkRole(['team_leader', 'officer'], [3, 4]);

    $branchId = $_SESSION['user']['branch_id'] ?? null;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    
    // Fetch data
    $teamFormationModel = new TeamFormationModel($this->db);
    $result = $teamFormationModel->getGroupsByBranch((int)$branchId, $page, 10);

    // Pass $result to your view
    $this->render('team-lists', [
        'teams' => $result['data'],
        'pagination' => [
            'total' => $result['total'],
            'current' => $result['page'],
            'last_page' => $result['last_page']
        ]
    ]);
}

public function retrieveTeamMembers(array $params = []): void
{
    $teamId = $params['uuid'] ?? $_GET['id'] ?? '';
 
    if ($teamId === '') {
        $_SESSION['error'] = 'የቡድን መታወቂያ አልተገኘም።';
        header('Location: ' . $_ENV['BASE_URL'] . '/team-lists');
        exit();
    }
 
    $teamModel = new TeamFormationModel($this->db);
    $team = $teamModel->getTeamWithMembers($teamId);
 
    if (!$team) {
        $_SESSION['error'] = 'የተጠየቀው ቡድን አልተገኘም።';
        header('Location: ' . $_ENV['BASE_URL'] . '/team-lists');
        exit();
    }
 
    $this->render('team-members-view', [
        'team' => $team,
    ]);
}
 
/**
 * Route (add to your $routes table, matching the pattern already used):
 *   'add-team-member' => ['TeamFormationController', 'addMember', true]
 *
 * POST JSON body: { "team_id": <int table_id>, "job_seeker_id": "<string>" }
 */
public function addMember(): void
{
    header('Content-Type: application/json; charset=utf-8');

    AuthHelper::checkRole(['team_leader', 'officer'], [3, 4]);

    $input = json_decode(file_get_contents('php://input'), true) ?? [];

    $teamId = isset($input['team_id']) ?  $input['team_id'] : null;
    $jobSeekerId = isset($input['job_seeker_id']) ? trim($input['job_seeker_id']) : '';

    if ($teamId <= 0 || $jobSeekerId === '') {
        echo json_encode(['status' => 'error', 'message' => 'የግቤት ውሂብ ትክክል አይደለም']);
        exit();
    }

    $teamModel = new TeamFormationModel($this->db);
    $result = $teamModel->addMember($teamId, $jobSeekerId);

    echo json_encode($result);
    exit();
}
}