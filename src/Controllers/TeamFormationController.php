<?php
namespace App\Controllers;

use App\Helpers\AuthHelper;
use App\Helpers\AuditHelper;
use App\Models\TeamFormationModel;
use App\Models\JobSeekerModel;
use App\Models\SectorModel;
use App\Models\ProjectNgoModel;
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
         $allowedPlace = ['ከተማ', 'ገጠር'];
    if (!in_array($place, $allowedPlace, true)) {
        echo json_encode(['success' => false, 'message' => 'ልክ ያልሆነ አካባቢ'], JSON_UNESCAPED_UNICODE);
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
$branchId = $_SESSION['user']['branch_id'] ?? null;
    $teamModel = new TeamFormationModel($this->db);
    $result = $teamModel->addMember($branchId, $teamId, $jobSeekerId);

    echo json_encode($result);
    exit();
}

public function getTeamForEdit(array $params = []): void
{
    header('Content-Type: application/json; charset=utf-8');
    
    AuthHelper::checkRole(['team_leader', 'officer'], [3, 4]);
    $teamId = $params['uuid'] ?? $_GET['id'] ?? '';

    if ($teamId === '') {
        echo json_encode(
            ['status' => false, 'message' => 'የቡድን መታወቂያ አልተገኘም።'],
            JSON_UNESCAPED_UNICODE
        );
        return;
    }

    try {
        $teamModel = new TeamFormationModel($this->db);
        $team = $teamModel->getTeamForEdit($teamId);

        if (!$team) {
            echo json_encode(
                ['status' => false, 'message' => 'ቡድኑ አልተገኘም።'],
                JSON_UNESCAPED_UNICODE
            );
            return;
        }
  $projectModel = new ProjectNgoModel($this->db);
$projects  = $projectModel->getAllProjectNgos();
        $sectorModel = new SectorModel($this->db);
        $sectorData  = $sectorModel->getAllSectorsAndSubsectors();
        // ['sectors' => [{id: uuid, name}], 'subsectorsBySector' => { sectorUuid: [{id: uuid, name}] }]

        echo json_encode([
            'status' => true,
            'team'   => [
                'uuid'                  => $team['uuid'],
                'association_name'      => $team['association_name'],
                'yetederajubet_akababi' => $team['yetederajubet_akababi'],
                'manager_phone'         => $team['manager_phone'],
                'yesra_mesk'            => $team['yesra_mesk'],
                'project_type'          => $team['project_type'],
                'ngo_id'                => $team['ngo_id'],
                'ngo_name'              => $team['ngo_name'],
                // sector/subsector: use uuid to match subsectorsBySector keys
                'sector_id'             => $team['sector_uuid'],
                'subsector_id'          => $team['subsector_uuid'],
                'sector_uuid'           => $team['sector_uuid'],
                'subsector_uuid'        => $team['subsector_uuid'],
                'sector_int_id'         => $team['sector_int_id'],
                'subsector_int_id'      => $team['subsector_int_id'],

                // leader int ids + resolved display names
                'teamleader_id'         => $team['teamleader_id'],
                'teamleader_name'       => $team['teamleader_name'],
                'vice_teamleader_id'    => $team['vice_teamleader_id'],
                'vice_teamleader_name'  => $team['vice_teamleader_name'],
                'treasurer'             => $team['treasurer'],
                'treasurer_name'        => $team['treasurer_name'],
                'procurement'           => $team['procurement'],
                'procurement_name'      => $team['procurement_name'],
            ],
            'members'            => $team['members'], // [{id, job_seeker_id, first_name, father_name, last_name, gender, phone_number}, ...]
            'sectors'            => $sectorData['sectors'],
            'subsectorsBySector' => $sectorData['subsectorsBySector'],
            'ngos'           => $projects
        ], JSON_UNESCAPED_UNICODE);

    } catch (\Throwable $e) {
        error_log(__METHOD__ . ': ' . $e->getMessage());
        echo json_encode(
            ['status' => false, 'message' => 'ውስጣዊ ስህተት ተከስቷል።'],
            JSON_UNESCAPED_UNICODE
        );
    }
}
public function updateTeamFormation()
{
    // Prevent PHP warnings from leaking into JSON response
    ini_set('display_errors', '0');
    error_reporting(0);

    AuthHelper::checkRole(['team_leader', 'officer'], [3, 4]);

    header('Content-Type: application/json; charset=utf-8');

    // 1. Method check
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed'], JSON_UNESCAPED_UNICODE);
        return;
    }
    
    $branchId = $_SESSION['user']['branch_id'];
    if (!$branchId) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'ያልተፈቀደ ድርጊት።'
        ]);
        return;
    }

    // 2. Input sanitization — matches the edit-form JS payload keys
    $teamId        = trim($_POST['team_id'] ?? '');
    $assoName      = trim($_POST['association_name'] ?? '');
    $place         = trim($_POST['yetederajubet_akababi'] ?? '');
    $orgType       = trim($_POST['project_type'] ?? '');
    $ngoId       = trim($_POST['ngo'] ?? '');
    $phone         = trim($_POST['manager_phone'] ?? '');
    $sectorUuid    = trim($_POST['sector'] ?? '');
    $subsectorUuid = trim($_POST['subsector'] ?? '');
    $yesramesk     = trim($_POST['yesra_mesk'] ?? '');
    $leaderId      = trim($_POST['teamleader_id'] ?? '');      // job_seeker UUID
    $viceId        = trim($_POST['vice_teamleader_id'] ?? ''); // job_seeker UUID
    $financeId     = trim($_POST['treasurer'] ?? '');          // job_seeker UUID
    $procId        = trim($_POST['procurement'] ?? '');        // job_seeker UUID

   // 3. project_type / place allow-lists
    $allowedOrgTypes = ['NGO', 'የመንግስት', 'በራስ ፍላጎት', 'የቤተሰብ', 'በልዩ ሁኔታ'];
    if (!in_array($orgType, $allowedOrgTypes, true)) {
        echo json_encode(['success' => false, 'message' => 'ልክ ያልሆነ የአደረጃጀት አይነት'], JSON_UNESCAPED_UNICODE);
        return;
    }

    $allowedPlace = ['ከተማ', 'ገጠር'];
    if (!in_array($place, $allowedPlace, true)) {
        echo json_encode(['success' => false, 'message' => 'ልክ ያልሆነ አካባቢ'], JSON_UNESCAPED_UNICODE);
        return;
    }

    // 3b. NGO id required ONLY when project_type is NGO
    if ($orgType === 'NGO' && $ngoId === '') {
        echo json_encode(['success' => false, 'message' => 'እባክዎ NGO ይምረጡ'], JSON_UNESCAPED_UNICODE);
        return;
    }

    // If not NGO, ignore whatever was submitted for ngo (don't persist stale/irrelevant values)
    if ($orgType !== 'NGO') {
        $ngoId = null;
    }

    if ($teamId === '') {
        echo json_encode(['success' => false, 'message' => 'የቡድን መታወቂያ አልተገኘም።'], JSON_UNESCAPED_UNICODE);
        return;
    }

    // 4. Fetch team members + current project_type in a single query.
    //    Used both for the government-lock rule and for role-membership validation.
    $teamFormationModel = new TeamFormationModel($this->db);
    $teamData = $teamFormationModel->getTeamMembersWithProjectType($teamId);

    if (empty($teamData['member_ids'])) {
        echo json_encode(['success' => false, 'message' => 'የቡድኑ አባላት መረጃ አልተገኘም።'], JSON_UNESCAPED_UNICODE);
        return;
    }

    // 5. Required-field validation
    $requiredValues = compact(
        'teamId', 'assoName', 'place', 'phone',
        'sectorUuid', 'subsectorUuid', 'yesramesk',
        'leaderId', 'viceId', 'financeId', 'procId'
    );
    foreach ($requiredValues as $value) {
        if ($value === '') {
            echo json_encode(['success' => false, 'message' => 'እባክዎ ሁሉንም መስኮች ይሙሉ'], JSON_UNESCAPED_UNICODE);
            return;
        }
    }

    try {
        // 6. Convert submitted job_seeker UUIDs -> int job_seeker_id (same trust boundary as create flow)
        $jobSeekerModel = new JobSeekerModel($this->db);
        $roleUuids = array_filter(array_unique([$leaderId, $viceId, $financeId, $procId]));
        $idMap = $jobSeekerModel->getJobSeekerIds($roleUuids); // ['UUID' => INT_JOB_SEEKER_ID]

        $resolvedLeaderId  = $idMap[$leaderId]  ?? null;
        $resolvedViceId    = $idMap[$viceId]    ?? null;
        $resolvedFinanceId = $idMap[$financeId] ?? null;
        $resolvedProcId    = $idMap[$procId]    ?? null;

        foreach ([$resolvedLeaderId, $resolvedViceId, $resolvedFinanceId, $resolvedProcId] as $resolved) {
            if ($resolved === null) {
                throw new Exception('የተመረጠው ሰው መረጃ አልተገኘም።');
            }
        }

        // 7. Role ints must belong to THIS team's actual organized_jobseekers members
        $memberSet = array_flip(array_map('strval', $teamData['member_ids']));
        foreach ([$resolvedLeaderId, $resolvedViceId, $resolvedFinanceId, $resolvedProcId] as $roleId) {
            if (!isset($memberSet[(string) $roleId])) {
                throw new Exception('የተመረጠው ሚና ከቡድኑ አባላት ውጪ ነው');
            }
        }

        // 8. Resolve subsector UUID -> int id for storage in group_table.sub_sector
        $sectorModel = new SectorModel($this->db);
        $subSectorIntId = $sectorModel->getSubsectorBigIntIds($subsectorUuid);
        if ($subSectorIntId === null) {
            throw new Exception('የተመረጠው ዘርፍ/ንዑስ ዘርፍ መረጃ አልተገኘም።');
        }

        // 9. Build payload with resolved INT ids and update
        $payload = [
            'association_name'      => $assoName,
            'yetederajubet_akababi' => $place,
            'manager_phone'         => $phone,
            'sub_sector_int_id'     => $subSectorIntId['sub_sectorid'],
            'yesra_mesk'            => $yesramesk,
            'teamleader_id'         => $resolvedLeaderId,
            'vice_teamleader_id'    => $resolvedViceId,
            'treasurer'             => $resolvedFinanceId,
            'procurement'           => $resolvedProcId,
            'ngo_id'                => $teamData['ngo_id'],
            'branch_id'             => $_SESSION['user']['branch_id'],
        ];

        $result = $teamFormationModel->updateTeamFormation($teamId, $payload);

        if ($result['status'] !== 'success') {
            throw new Exception($result['message'] ?? 'ቡድኑን ማስተካከል አልተቻለም');
        }

        AuditHelper::log('team_formation_updated', 'group_table', $teamId, null, $payload, [
            'performed_by' => $_SESSION['user']['id'] ?? null,
        ]);

        echo json_encode(['success' => true, 'message' => 'ቡድኑ በተሳካ ሁኔታ ተስተካክሏል'], JSON_UNESCAPED_UNICODE);

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
    }
}

public function purge(): void
{
    AuthHelper::checkRole(['team_leader', 'officer'], [3, 4]);
    header('Content-Type: application/json');

    $data     = json_decode(file_get_contents('php://input'), true);
    $teamid   = (string) ($data['id'] ?? '');
    $userId   = $_SESSION['user']['id'] ?? '';
    $type     = 'team_deletion'; // ለ Audit Log
    $reason   = trim($data['reason'] ?? '');
    $password = $data['confirm_password'] ?? '';

    // Validate input
    if (!$userId || !$teamid || !$reason || !$password) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'ሁሉም መስኮች አስፈላጊ ናቸው።'
        ]);
        return;
    }

    $user     = $_SESSION['user'] ?? [];
    $branchId = $user['branch_id'] ?? null;
    if (!$branchId) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'ያልተፈቀደ ድርጊት።'
        ]);
        return;
    }

    if (empty($teamid)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid ID']);
        return;
    }

    if (empty($userId)) {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
        return;
    }

    // Verify password
    $userModel = new \App\Models\User($this->db);
    if (!$userModel->verifyPassword($user['id'], $password)) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'ፓስዋርዱ ትክክል አይደለም።'
        ]);
        return;
    }

    try {
        $model = new TeamFormationModel($this->db);
        $action = 'deleted team';
        $metaKey = 'affected_records'; 
        
        $result = $model->purge($branchId, $userId, $teamid, $reason);

        if ($result['status'] === 'success') {
            $metadata = [
                $metaKey ,              
                'affected_job_seekers'  => $result['jobSeekerCount'] ?? 0,
            ];

            \App\Helpers\AuditHelper::log(
                action:     $action,
                entityType: $type,
                entityId:   $teamid,
                newValues:  ['status' => 'archived'],
                metadata:   $metadata
            );
        }

        echo json_encode($result);

    } catch (\Exception $e) {
        error_log("Delete Error ({$type}): " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'ስህተት ተፈጥሯል፤ እባክዎ በድጋሚ ይሞክሩ።']);
    }
}

public function purgeMember(): void
{
    AuthHelper::checkRole(['team_leader', 'officer'], [3, 4]);
    header('Content-Type: application/json');

    $data     = json_decode(file_get_contents('php://input'), true);
    $jobseekerId   = (string) ($data['id'] ?? '');
    $userId   = $_SESSION['user']['id'] ?? '';
    $type     = 'member_deletion'; // ለ Audit Log
    $reason   = trim($data['reason'] ?? '');
    $password = $data['confirm_password'] ?? '';

    // Validate input
    if (!$userId || !$jobseekerId || !$reason || !$password) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'ሁሉም መስኮች አስፈላጊ ናቸው።'
        ]);
        return;
    }

    $user     = $_SESSION['user'] ?? [];
    $branchId = $user['branch_id'] ?? null;
    if (!$branchId) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'ያልተፈቀደ ድርጊት።'
        ]);
        return;
    }

    if (empty($jobseekerId)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid ID']);
        return;
    }

    if (empty($userId)) {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
        return;
    }

    // Verify password
    $userModel = new \App\Models\User($this->db);
    if (!$userModel->verifyPassword($user['id'], $password)) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'ፓስዋርዱ ትክክል አይደለም።'
        ]);
        return;
    }

    try {
        $model = new TeamFormationModel($this->db);
        $action = 'deleted member';
        $metaKey = 'affected_records'; 
        
        $result = $model->purgeMember($branchId, $userId, $jobseekerId, $reason);

        if ($result['status'] === 'success') {
            $metadata = [
                $metaKey ,              
                'affected_job_seekers'  => $result['jobSeekerCount'] ?? 0,
            ];

            \App\Helpers\AuditHelper::log(
                action:     $action,
                entityType: $type,
                entityId:   $jobseekerId,
                newValues:  ['status' => 'archived'],
                metadata:   $metadata
            );
        }

        echo json_encode($result);

    } catch (\Exception $e) {
        error_log("Delete Error ({$type}): " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'ስህተት ተፈጥሯል፤ እባክዎ በድጋሚ ይሞክሩ።']);
    }
}
}