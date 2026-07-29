<?php
namespace App\Controllers;
use App\Helpers\AuthHelper;
use App\Models\EnterpriseModel;
use App\Models\SectorModel;
use App\Models\ProjectNgoModel;
use Ramsey\Uuid\Uuid;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
// 1. BaseControllerን እንዲወርስ እናደርጋለን
class EnterpriseController extends BaseController {
    
public function showRegisterForm() {
    AuthHelper::checkRole(['team_leader', 'officer'], [3, 4]);

    $branchId = $_SESSION['user']['branch_id'];
    $id       = $_SESSION['user']['id'] ?? null;
if(!$branchId || !$id){
 header('Location: ' . rtrim($_ENV['BASE_URL'], '/') . '/logout');
        exit;
}$enterpriseModel = new EnterpriseModel($this->db);

   $recentResult      = $enterpriseModel->getEnterprisesRegisteredLast24Hours($branchId);
$recentEnterprises = $recentResult['data'];
$recentCount       = $recentResult['count'];
    $sectorModel = new SectorModel($this->db);
    $sectorData  = $sectorModel->getAllSectorsAndSubsectors();
        $projectModel = new ProjectNgoModel($this->db);
$projects  = $projectModel->getAllProjectNgos();
    $this->render('enterprise-registration', [
        'title'      => 'ኢንተርፕራይዝ መመዝገቢያ',
        'sectorData' => $sectorData,
        'projects' => $projects,
        'recentEnterprises' => $recentEnterprises,
        'recentCount'        => $recentCount,
    ]);
}
public function searchLinkedEntity(): void
{
        $branchId = $_SESSION['user']['branch_id'];
    $id       = $_SESSION['user']['id'] ?? null;
if(!$branchId || !$id){
 header('Location: ' . rtrim($_ENV['BASE_URL'], '/') . '/logout');
        exit;
}
    session_write_close();
    header('Content-Type: application/json');
AuthHelper::checkRole(['team_leader', 'officer'], [3, 4]);
    $query = trim($_GET['q'] ?? '');
    $enterpriseType = $_GET['enterprise_type'] ?? '';
    $branchId = $_SESSION['user']['branch_id']; // adjust to your actual session/branch source

    if ($query === '' || !in_array($enterpriseType, ['0', '1'], true)) {
        echo json_encode(['data' => []]);
        return;
    }
   $groupModel = new EnterpriseModel($this->db);
    if ($enterpriseType === '1') {
       $rows = $groupModel->searchGroupsForAssociationEnterprise($branchId, $query);
$data = array_map(fn($r) => [
    'id'         => $r['id'],
    'label'      => $r['label'],
    'project_type' => $r['project_type'],
], $rows);    } else {
        $rows = $groupModel->searchJobSeekersForIndividualEnterprise($branchId, $query);
        $data = array_map(fn($r) => ['id' => $r['id'], 'label' => $r['label']], $rows);
    }

    echo json_encode(['data' => $data]);
}

public function registerEnterprise(): void
{
    AuthHelper::checkRole(['team_leader', 'officer'], [3, 4]); // adjust roles to your actual convention

    $branchId = $_SESSION['user']['branch_id'] ?? null;
    $userId = $_SESSION['user']['id'] ?? null;
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
         $_SESSION['success'] = 'Invalid Request. Please try again';
        header('Location: ' . rtrim($_ENV['BASE_URL'], '/') . '/logout');
        exit;
    }
    if (!$branchId || !$userId) {
        $_SESSION['error'] = 'branch or user id invalid';
        header('Location: ' . rtrim($_ENV['BASE_URL'], '/') . '/logout');
        exit;
    }

    // 1. Retrieve and sanitize input fields
    $linkedEntityId          = trim($_POST['linked_entity_id'] ?? '');
    $enterpriseName          = trim($_POST['enterprise_name'] ?? '');
    $tinNumber               = trim($_POST['tin_number'] ?? '');
    $yeedgetDereja           = trim($_POST['yeedget_dereja'] ?? '');
    $initialCapital          = trim($_POST['initial_capital'] ?? '');
    $yehabtuMnch             = trim($_POST['yehabtu_mnch'] ?? '');
    $wektawiYehabtMeten      = trim($_POST['wektawi_yehabt_meten'] ?? '');
    $yemrtAyinet             = trim($_POST['yemrt_ayinet'] ?? '');
    $yemikerbHagerWeysLewuch = trim($_POST['yemikerb_hager_weys_lewuch'] ?? '');
    $startingCapitalInKind   = trim($_POST['starting_capital_in_kind'] ?? '');
    $yetederegeDgaf          = trim($_POST['yetederege_dgaf'] ?? '');
    $orgTypeSuport           = trim($_POST['org_type_suport'] ?? '');
    $supportedBy             = trim($_POST['supported_by'] ?? '');
    $supportedItems          = trim($_POST['supported_items'] ?? '');
    $establishedDateRaw      = trim($_POST['established_date'] ?? '');
    $ngoId                   = trim($_POST['ngo_id'] ?? '');
    $linkedEntityProjectType = trim($_POST['linked_entity_project_type'] ?? '');

    // 2. Server-Side Validation checks matching frontend rules & visibility logic
    $errors = [];

    if (empty($linkedEntityId)) {
        $errors[] = 'እባክዎ ከዝርዝር ውስጥ አባል/ቡድን ይምረጡ።';
    }

    if (!preg_match('/^\d+$/', $tinNumber)) {
        $errors[] = 'የግብር መክፈያ መለያ ቁጥር ቁጥሮች ብቻ መሆን አለበት።';
    }

    if (!in_array($yeedgetDereja, ['0','1','2','3','4','5','6'], true)) {
        $errors[] = 'እባክዎ ትክክለኛ የእድገት ደረጃ ይምረጡ።';
    }

    if (!is_numeric($initialCapital) || floatval($initialCapital) < 0) {
        $errors[] = 'መነሻ ካፒታል ቁጥር ብቻ መሆን አለበት (ክፍልፋይ መቀበል ይችላል)።';
    }

    if (!in_array($yehabtuMnch, ['0','1','2','3'], true)) {
        $errors[] = 'እባክዎ ትክክለኛ የሃብት ምንጭ ይምረጡ።';
    }

    if (!is_numeric($wektawiYehabtMeten) || floatval($wektawiYehabtMeten) < 0) {
        $errors[] = 'ወቅታዊ የሃብት መጠን ቁጥር ብቻ መሆን አለበት።';
    }

    if (!in_array($yemikerbHagerWeysLewuch, ['ለሃገር ውስጥ', 'ለውጭ ሃገር'], true)) {
        $errors[] = 'እባክዎ ምርቱ የሚቀርበውን አቅጣጫ ትክክለኛ ምርጫ ይምረጡ።';
    }

    if (!in_array($yetederegeDgaf, ['0', '1'], true)) {
        $errors[] = 'እባክዎ የተደረገ ድጋፍ መኖሩን ይምረጡ።';
    }

    // Conditional Validation based on visibility rules
    if ($yetederegeDgaf === '1') {
        if ($linkedEntityProjectType === 'NGO') {
            if (empty($supportedItems)) {
                $errors[] = 'እባክዎ የድጋፉ አይነት ያስገቡ።';
            }
            $orgTypeSuport = null;
            $supportedBy = null;
            $ngoId = null;
        } else {
            if (!in_array($orgTypeSuport, ['bemengst', 'bgelu', 'benterprise', 'beproject', 'belela'], true)) {
                $errors[] = 'እባክዎ ድጋፍ የተደረገበትን አካል ትክክለኛ ምርጫ ይምረጡ።';
            }

            if ($orgTypeSuport === 'beproject') {
                if (empty($ngoId)) {
                    $errors[] = 'እባክዎ NGO ይምረጡ።';
                }
                if (empty($supportedItems)) {
                    $errors[] = 'እባክዎ የድጋፉ አይነት ያስገቡ።';
                }
                $supportedBy = null;
            } else {
                if (empty($supportedBy)) {
                    $errors[] = 'እባክዎ ድጋፍ ያደረገው አካል ያስገቡ።';
                }
                if (empty($supportedItems)) {
                    $errors[] = 'እባክዎ የድጋፉ አይነት ያስገቡ።';
                }
                $ngoId = null;
            }
        }
    } else {
        $orgTypeSuport = null;
        $supportedBy = null;
        $supportedItems = null;
        $ngoId = null;
    }

    $establishedDate = null;

    if ($establishedDateRaw === '') {
        $errors[] = 'የተመሰረትበት ቀን ባዶ መሆን የለበትም።';
    } else {
        $dateObj = \DateTime::createFromFormat('Y-m-d', $establishedDateRaw);

        if ($dateObj === false || $dateObj->format('Y-m-d') !== $establishedDateRaw) {
            $errors[] = 'የተመሰረትበት ቀን ትክክለኛ ቀን አይደለም።';
        } else {
            $dateObj->setTime(0, 0, 0);

            $today = new \DateTime('today');
            $currentYear = (int) $today->format('Y');

            $fiscalStart = new \DateTime("{$currentYear}-07-08");
            if ($today < $fiscalStart) {
                $fiscalStart = new \DateTime(($currentYear - 1) . '-07-08');
            }

            if ($dateObj > $today) {
                $errors[] = 'የተመሰረትበት ቀን ወደፊት ሊሆን አይችልም።';
            } elseif ($dateObj < $fiscalStart) {
                $errors[] = 'የተመሰረትበት ቀን ከ' . $fiscalStart->format('Y-m-d') . ' በኋላ መሆን አለበት።';
            } else {
                $establishedDate = $dateObj->format('Y-m-d');
            }
        }
    }

    if (!empty($errors)) {
        $_SESSION['error'] = implode('፣ ', $errors);
        header('Location: ' . rtrim($_ENV['BASE_URL'], '/') . '/enterprise-registration');
        exit;
    }

    $data = [
        'branch_id'                  => $branchId,
        'user_id'                    => $userId,
        'enterpriseId'               => Uuid::uuid7()->toString(),
        'linked_entity_id'           => $linkedEntityId,
        'enterprise_name'            => $enterpriseName,
        'tin_number'                 => $tinNumber,
        'yeedget_dereja'             => $yeedgetDereja,
        'initial_capital'            => $initialCapital,
        'yehabtu_mnch'               => $yehabtuMnch,
        'wektawi_yehabt_meten'       => $wektawiYehabtMeten,
        'yemrt_ayinet'               => $yemrtAyinet,
        'yemikerb_hager_weys_lewuch' => $yemikerbHagerWeysLewuch,
        'yetederege_dgaf'            => $yetederegeDgaf,
        'org_type_suport'            => $orgTypeSuport,
        'supported_by'               => $supportedBy,
        'supported_items'            => $supportedItems,
        'ngo_id'                     => $ngoId,
        'starting_capital_in_kind'   => $startingCapitalInKind,
        'fiscal_year'                => AuthHelper::checkFiscalYear(),
        'established_date'           => $establishedDate,
    ];

    try {
        $model  = new EnterpriseModel($this->db);
        $result = $model->createAssocationEnterprise($data);

        if ($result['status'] === 'success') {
            \App\Helpers\AuditHelper::log(
                action:     'enterprise_created',
                entityType: 'enterprise',
                entityId:   $result['id'],
                oldValues:  null,
                newValues:  $_POST,
                metadata:   ['branch_id' => $branchId, 'performed_by' => $userId]
            );

            $_SESSION['success'] = 'ኢንተርፕራይዙ በተሳካ ሁኔታ ተመዝግቧል።';
            header('Location: ' . rtrim($_ENV['BASE_URL'], '/') . '/enterprise-registration');
            exit;
        }

        $_SESSION['error'] = $result['message'] ?? 'ኢንተርፕራይዙን መመዝገብ አልተቻለም። እባክዎ ደግመው ይሞክሩ።';
        header('Location: ' . rtrim($_ENV['BASE_URL'], '/') . '/enterprise-registration');
        exit;

    } catch (\Exception $e) {
        error_log("Enterprise Registration Error: " . $e->getMessage());
        $_SESSION['error'] = 'ስህተት ተፈጥሯል፤ እባክዎ በድጋሚ ይሞክሩ።';
        header('Location: ' . rtrim($_ENV['BASE_URL'], '/') . '/enterprise-registration');
        exit;
    }
}


private function getFiscalMinMax(): array
    {
        $today = new \DateTime();
        $today->setTime(0, 0, 0);

        $year = (int)$today->format('Y');
        $fiscalStart = new \DateTime("{$year}-07-08");
        $fiscalStart->setTime(0, 0, 0);

        if ($today < $fiscalStart) {
            $fiscalStart = new \DateTime(($year - 1) . "-07-08");
            $fiscalStart->setTime(0, 0, 0);
        }

        return ['min' => $fiscalStart, 'max' => $today];
    }

    public function createIndividualEnterprise()
    {
        AuthHelper::checkRole(['team_leader', 'officer'], [3, 4]);
        $branchId = $_SESSION['user']['branch_id'];
        $userId = $_SESSION['user']['id'];
          if (!$branchId || !$userId) {
        header('Location: ' . rtrim($_ENV['BASE_URL'], '/') . '/logout');
        exit;
    }
        // Guard check: Ensure it is strictly a POST request
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . rtrim($_ENV['BASE_URL'], '/') . '/logout');
            exit;
        }

        $data = $_POST;
        $errors = [];

        // 1. Linked Entity ID validation
        $linkedEntityId = trim($data['linked_entity_id'] ?? '');
        if (empty($linkedEntityId)) {
            $errors['linked_entity_search'] = 'እባክዎ የስራ ፈላጊ ስም ወይም መታወቂያ ይምረጡ!';
        }

        // 2. Enterprise Name validation
        $enterpriseName = trim($data['enterprise_name'] ?? '');
        if (empty($enterpriseName)) {
            $errors['enterprise_name'] = 'ኢንተርፕራይዝ ስም ባዶ መሆን አይችልም!';
        }

        // 3. TIN Number validation
        $tinNumber = trim($data['tin_number'] ?? '');
        if (!preg_match('/^\d+$/', $tinNumber)) {
            $errors['tin_number'] = 'የግብር መክፈያ መለያ ቁጥር (TIN) ቁጥር ብቻ መሆን አለበት!';
        }

        // 4. Growth Stage validation (0 to 6)
        $yeedgetDereja = $data['yeedget_dereja'] ?? '';
        if ($yeedgetDereja === '' || !is_numeric($yeedgetDereja) || (int)$yeedgetDereja < 0 || (int)$yeedgetDereja > 6) {
            $errors['yeedget_dereja'] = 'እባክዎ ትክክለኛ የእድገት ደረጃ ይምረጡ (ከ 0 እስከ 6)።';
        }

        // 5. Initial Capital validation
        $initialCapital = trim($data['initial_capital'] ?? '');
        if ($initialCapital === '' || !is_numeric($initialCapital)) {
            $errors['initial_capital'] = 'መነሻ ካፒታል ቁጥር መሆን አለበት!';
        }

        // 6. Source of Wealth validation (0 to 3)
        $yehabtuMnch = $data['yehabtu_mnch'] ?? '';
        if ($yehabtuMnch === '' || !is_numeric($yehabtuMnch) || (int)$yehabtuMnch < 0 || (int)$yehabtuMnch > 3) {
            $errors['yehabtu_mnch'] = 'እባክዎ ትክክለኛ የሃብቱ ምንጭ ይምረጡ (ከ 0 እስከ 3)።';
        }

        // 7. Current Asset Amount validation
        $wektawiYehabtMeten = trim($data['wektawi_yehabt_meten'] ?? '');
        if ($wektawiYehabtMeten === '' || !is_numeric($wektawiYehabtMeten)) {
            $errors['wektawi_yehabt_meten'] = 'ወቅታዊ የሃብት መጠን ቁጥር መሆን አለበት!';
        }

        // 8. Product Destination validation
        $yemikerb = $data['yemikerb_hager_weys_lewuch'] ?? '';
        if ($yemikerb !== 'ለሃገር ውስጥ' && $yemikerb !== 'ለውጭ ሃገር') {
            $errors['yemikerb_hager_weys_lewuch'] = 'እባክዎ ምርቱ የሚቀርበውን ትክክለኛ አማራጭ ይምረጡ።';
        }

        // 9. Established Date Validation (Fiscal Year Range Validation)
        $establishedDateRaw = trim($data['established_date'] ?? '');
        if (empty($establishedDateRaw)) {
            $errors['eth_start_date'] = 'እባክዎ የተመሰረትበት ቀን ይምረጡ።';
        } else {
            $pickedDate = \DateTime::createFromFormat('Y-m-d', $establishedDateRaw) ?: new \DateTime($establishedDateRaw);
            
            if (!$pickedDate) {
                $errors['eth_start_date'] = 'ልክ ያልሆነ የቀን ቅርጸት።';
            } else {
                $pickedDate->setTime(0, 0, 0);
                ['min' => $minFiscal, 'max' => $maxFiscal] = $this->getFiscalMinMax();

                if ($pickedDate > $maxFiscal) {
                    $errors['eth_start_date'] = 'የተመሰረትበት ቀን ወደፊት ሊሆን አይችልም።';
                } elseif ($pickedDate < $minFiscal) {
                    $minStr = $minFiscal->format('d/m/Y');
                    $errors['eth_start_date'] = 'የተመሰረትበት ቀን ከ' . $minStr . ' በኋላ መሆን አለበት።';
                }
            }
        }

        // If validation fails, handle normal post error redirect (or save to session and redirect back)
        if (!empty($errors)) {
            // If you want standard PHP native session handling for errors:
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $data;
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }

       // Resolve support variables properly based on your form logic
        $projectType      = $data['yeaderejajet_ayinet'] ?? null;
        $yetederegeDgaf   = $data['yetederege_dgaf'] ?? '0';
        $incomingOrgSupport = $data['org_type_suport'] ?? '';

        $orgTypeSupport = null;
        $project_ID     = null;
        $supporter      = null;

        if ($yetederegeDgaf === '1') {
            if ($projectType === 'NGO') {
                $project_ID     = $data['ngo_id'] ?? null;
                $orgTypeSupport = 'beproject';
            } else if ($incomingOrgSupport === 'beproject') {
                $project_ID     = $data['supported_by'] ?? null;
                $orgTypeSupport = 'beproject';
            } else {
                $supporter      = $data['supported_by'] ?? null;
                $orgTypeSupport = $incomingOrgSupport;
            }
        }

        $modelData = [
            'branch_id'                => $branchId,
            'user_id'                  => $userId,
            'linked_entity_id'         => trim($data['linked_entity_id'] ?? ''),
            'enterprise_name'          => trim($data['enterprise_name'] ?? ''),
            'tin_number'               => trim($data['tin_number'] ?? ''),
            'yeedget_dereja'           => $data['yeedget_dereja'] ?? '',
            'initial_capital'          => trim($data['initial_capital'] ?? ''),
            'yehabtu_mnch'             => $data['yehabtu_mnch'] ?? '',
            'wektawi_yehabt_meten'     => trim($data['wektawi_yehabt_meten'] ?? ''),
            'yemrt_ayinet'             => trim($data['yemrt_ayinet'] ?? ''),
            'yemikerb_hager_weys_lewuch' => trim($data['yemikerb_hager_weys_lewuch'] ?? ''),
            'starting_capital_in_kind' => trim($data['starting_capital_in_kind'] ?? ''),
            'entherprise_ayinet'       => $data['entherprise_ayinet'] ?? '0',
            'yeaderejajet_ayinet'      => $projectType,
            'yeminorubet_acababi'      => $data['yeminorubet_acababi'] ?? '',
            'sub_sector'               => $data['subsector_id'] ?? '',
            'yesra_mesk'               => trim($data['yesra_mesk'] ?? ''),
            'yetederege_dgaf'          => $yetederegeDgaf,
            'supporter'          => $supporter,
            'ngo_id'                   => $project_ID,
            'supported_by'             => $orgTypeSupport,
            'supported_items'          => trim($data['supported_items'] ?? ''),
            'established_date'         => trim($data['established_date'] ?? ''),
            'fiscal_year'              => date('Y'),
            'enterpriseId'             => \Ramsey\Uuid\Uuid::uuid4()->toString(),
        ];

        try {
            $model  = new EnterpriseModel($this->db);
            $result = $model->createIndividualEnterprise($modelData); // or your appropriate model method

            if ($result['status'] === 'success') {
                \App\Helpers\AuditHelper::log(
                    action:     'enterprise_created',
                    entityType: 'enterprise',
                    entityId:   $result['id'],
                    oldValues:  null,
                    newValues:  $_POST,
                    metadata:   ['branch_id' => $branchId, 'performed_by' => $userId]
                );

                $_SESSION['success'] = 'ኢንተርፕራይዙ በተሳካ ሁኔታ ተመዝግቧል።';
                header('Location: ' . rtrim($_ENV['BASE_URL'], '/') . '/enterprise-registration');
                exit;
            }

            $_SESSION['error'] = $result['message'] ?? 'ኢንተርፕራይዙን መመዝገብ አልተቻለም። እባክዎ ደግመው ይሞክሩ።';
            header('Location: ' . rtrim($_ENV['BASE_URL'], '/') . '/enterprise-registration');
            exit;

        } catch (\Exception $e) {
            error_log("Enterprise Registration Error: " . $e->getMessage());
            $_SESSION['error'] = 'ስህተት ተፈጥሯል፤ እባክዎ በድጋሚ ይሞክሩ።';
            header('Location: ' . rtrim($_ENV['BASE_URL'], '/') . '/enterprise-registration');
            exit;
        }
    }

public function listofEnterprises()
{
    AuthHelper::checkRole(['team_leader', 'officer']);
    $branchId = $_SESSION['user']['branch_id'];
    if (!$branchId) {
        header('Location: ' . rtrim($_ENV['BASE_URL'], '/') . '/logout');
        exit;
    }

    $currentPage = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
    $limit       = 20;
    $offset      = ($currentPage - 1) * $limit;

    $enterpriseModel = new EnterpriseModel($this->db);

    $enterprises = $enterpriseModel->getEnterprisesByHierarchy($branchId, $limit, $offset);
    $totalCount  = $enterpriseModel->getEnterprisesCountByHierarchy($branchId);
    $totalPages  = (int) ceil($totalCount / $limit);

    $this->render('enterprise-lists', [
        'enterprises' => $enterprises,
        'currentPage' => $currentPage,
        'totalPages'  => $totalPages,
        'totalCount'  => $totalCount,
        'offset'      => $offset,
    ]);
}

public function details(array $params = []): void
{
    AuthHelper::checkRole(['team_leader', 'officer'], [3, 4]);
    $branchId = $_SESSION['user']['branch_id'];
    $enterpriseId = $params['uuid'] ?? $_GET['id'] ?? '';

    if (!$enterpriseId) {
        // handle missing id — redirect back or show a not-found view
        header('Location: ' . rtrim($_ENV['BASE_URL'], '/') . '/enterprise-lists');
        return;
    }

    $enterpriseModel = new EnterpriseModel($this->db);
    $enterprise = $enterpriseModel->getEnterpriseDetails($branchId, $enterpriseId);

    if (!$enterprise) {
        // handle not found — redirect or render a "not found" view
        header('Location: ' . rtrim($_ENV['BASE_URL'], '/') . '/enterprise-lists');
        return;
    }

    // render your existing enterprsie-details.php view with $enterprise
    $this->render('enterprise-details', ['enterprise' => $enterprise]);
}

public function purge(): void
{
    AuthHelper::checkRole(['team_leader', 'officer'], [3, 4]);
    header('Content-Type: application/json');

    $data         = json_decode(file_get_contents('php://input'), true);
    $enterpriseid = (string) ($data['id'] ?? '');
    $userId       = $_SESSION['user']['id'] ?? '';
    $type         = 'enterprise_deletion'; // ለ Audit Log
    $reason       = trim($data['reason'] ?? '');
    $password     = $data['confirm_password'] ?? '';

    // Validate input
    if (!$userId || !$enterpriseid || !$reason || !$password) {
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

    if (empty($enterpriseid)) {
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
        $model  = new EnterpriseModel($this->db);
        $action = 'deleted enterprise';

        $result = $model->purge($branchId, $userId, $enterpriseid, $reason);

        if ($result['status'] === 'success') {
            $metadata = [
                'affected_job_seekers' => $result['jobSeekerCount'] ?? 0,
                'branch_id'            => $branchId,
                'reason'               => $reason,
            ];

            \App\Helpers\AuditHelper::log(
                action:     $action,
                entityType: $type,
                entityId:   $enterpriseid,
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
  public function displayCode003()
{
    AuthHelper::checkRole(['team_leader', 'officer']);
    $this->renderPrintable('code003', [
       
    ]);
}


public function exportEnterpriseReportToExcel(array $rows): void
{
    session_write_close();

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('የኢንተርፕራይዝ ሪፖርት');

    // ── Header row 1 (A1:B4 tall labels) ──────────────────────────
    $sheet->setCellValue('A1', 'ተራ ቁጥር');
    $sheet->setCellValue('B1', 'የኢንተርፕራይዙ ስም');
    $sheet->mergeCells('A1:A4');
    $sheet->mergeCells('B1:B4');

    // ── Header row 2 ───────────────────────────────────────────────
    $sheet->setCellValue('C2', 'አድራሻ');
    $sheet->mergeCells('C2:H2');

    $sheet->setCellValue('I2', 'የተመሰረተበት ዘመን (ዓ/ም)');
    $sheet->mergeCells('I2:I4');
    $sheet->setCellValue('J2', 'የተሰማራበት የስራ መስክ');
    $sheet->mergeCells('J2:J4');
    $sheet->setCellValue('K2', 'የተሰማራበት ዘርፍ (ማኑፋክቸሪንግ፣ ኮንስትራክሽን፣ አገልግሎት፣ ከተማ ግብረና፣ ንግድ)');
    $sheet->mergeCells('K2:K4');
    $sheet->setCellValue('L2', 'የኢ/ዙ አይነት በትርጓሜ (ጥቃቅን፣ አነስተኛ)');
    $sheet->mergeCells('L2:L4');
    $sheet->setCellValue('M2', 'የአደረጃጀት አይነት (በግል/በንግድ ማህበር/በህ/ስ/ማ)');
    $sheet->mergeCells('M2:M4');
    $sheet->setCellValue('N2', 'የግብር ከፋይነት መለያ ቁጥር');
    $sheet->mergeCells('N2:N4');
    $sheet->setCellValue('O2', 'የዕድገት ደረጃ (ጀማሪ/ታዳጊ/መብቃት)');
    $sheet->mergeCells('O2:O4');

    $sheet->setCellValue('P2', 'መነሻ ጠቅላላ ሃብት መጠንና ምንጩ');
    $sheet->mergeCells('P2:Q2');

    $sheet->setCellValue('R2', 'ወቅታዊ ጠቅላላ ሃብት መጠን');
    $sheet->mergeCells('R2:R4');

    $sheet->setCellValue('S2', 'ሲቋቋም የነበረ የሰው ሃይል');
    $sheet->mergeCells('S2:U2');

    $sheet->setCellValue('V2', 'ወቅታዊ የአባላት ብዛት');
    $sheet->mergeCells('V2:AH2');

    $sheet->setCellValue('AI2', 'ከአባላት ውጭ የተፈጠረ የስራ እድል');
    $sheet->mergeCells('AI2:AN2');

    $sheet->setCellValue('AO2', 'የኢንተርፕራይዙ ምርትና አገልግሎት');
    $sheet->mergeCells('AO2:AP2');

    // ── Header row 3 ───────────────────────────────────────────────
    $addressCols = ['C' => 'ዞን', 'D' => 'ወረዳ', 'E' => 'ከተማ', 'F' => 'ቀበሌ', 'G' => 'የቤት ቁጥር', 'H' => 'ስልክ ቁጥር'];
    foreach ($addressCols as $col => $label) {
        $sheet->setCellValue("{$col}3", $label);
        $sheet->mergeCells("{$col}3:{$col}4");
    }

    $sheet->setCellValue('P3', 'መነሻ ጠቅላላ ሃብት መጠን');
    $sheet->setCellValue('Q3', 'ምንጭ (ከራስ ተቀማጭ፣ ከቤተሰብ ብድር)');

    $foundingCols = ['S' => 'ወንድ', 'T' => 'ሴት', 'U' => 'ድምር'];
    foreach ($foundingCols as $col => $label) {
        $sheet->setCellValue("{$col}3", $label);
        $sheet->mergeCells("{$col}3:{$col}4");
    }

    $sheet->setCellValue('V3', 'ፆታ');
    $sheet->mergeCells('V3:X3');
    $sheet->setCellValue('Y3', 'በዕድሜ');
    $sheet->mergeCells('Y3:AC3');
    $sheet->setCellValue('AD3', 'በትምህርት ደረጃ');
    $sheet->mergeCells('AD3:AH3');

    $sheet->setCellValue('AI3', 'ቋሚ');
    $sheet->mergeCells('AI3:AK3');
    $sheet->setCellValue('AL3', 'ጊዚያዊ');
    $sheet->mergeCells('AL3:AN3');

    $sheet->setCellValue('AO3', 'የምርቱ ዓይነት');
    $sheet->mergeCells('AO3:AO4');
    $sheet->setCellValue('AP3', 'የሚቀርብበት ገበያ /ለሃገር ወይስ ለውጭ');
    $sheet->mergeCells('AP3:AP4');

    // ── Header row 4 (leaf labels) ─────────────────────────────────
    $row4 = [
        'V' => 'ወ', 'W' => 'ሴ', 'X' => 'ድ',
        'Y' => '15-29', 'Z' => '30-49', 'AA' => '50-65', 'AB' => '>65', 'AC' => 'ድምር',
        'AD' => 'ማንበብና መፃፍ የማይችሉ /መሰረተ ትምህርት',
        'AE' => 'አንደኛ ደረጃ (1-8)*',
        'AF' => 'ሁለተኛ ደረጃ (9-12)**',
        'AG' => 'ኮሌጅ (ዩኒቨርሲቲ) ያጠናቀቁ',
        'AH' => 'ድምር',
        'AI' => 'ወ', 'AJ' => 'ሴ', 'AK' => 'ድ',
        'AL' => 'ወ', 'AM' => 'ሴ', 'AN' => 'ድ',
    ];
    foreach ($row4 as $col => $label) {
        $sheet->setCellValue("{$col}4", $label);
    }

    // ── Header styling ───────────────────────────────────────────────
    $headerRange = 'A1:AP4';
    $sheet->getStyle($headerRange)->applyFromArray([
        'font' => ['bold' => true, 'size' => 9],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER,
            'wrapText' => true,
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['rgb' => 'EEF1F5'],
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['rgb' => 'D7DCE3'],
            ],
        ],
    ]);

    // ── Data rows ────────────────────────────────────────────────────
    $rowNum = 5;
    $serial = 1;

    foreach ($rows as $row) {
        $sheet->setCellValue("A{$rowNum}", $serial++);
        $sheet->setCellValue("B{$rowNum}", $row['enterprise_name'] ?? '');
        $sheet->setCellValue("C{$rowNum}", $row['zone'] ?? '');
        $sheet->setCellValue("D{$rowNum}", $row['woreda'] ?? '');
        $sheet->setCellValue("E{$rowNum}", $row['city'] ?? '');
        $sheet->setCellValue("F{$rowNum}", $row['kebele'] ?? '');
        $sheet->setCellValue("G{$rowNum}", $row['house_number'] ?? '');
        $sheet->setCellValue("H{$rowNum}", $row['phone_number'] ?? '');
        $sheet->setCellValue("I{$rowNum}", $row['established_eth_year'] ?? '');
        $sheet->setCellValue("J{$rowNum}", $row['work_field'] ?? '');
        $sheet->setCellValue("K{$rowNum}", $row['sector'] ?? '');
        $sheet->setCellValue("L{$rowNum}", $row['type_definition'] ?? '');
        $sheet->setCellValue("M{$rowNum}", $row['organization_type'] ?? '');
        $sheet->setCellValue("N{$rowNum}", $row['tin_number'] ?? '');
        $sheet->setCellValue("O{$rowNum}", $row['growth_level'] ?? '');
        $sheet->setCellValue("P{$rowNum}", $row['initial_capital_amount'] ?? '');
        $sheet->setCellValue("Q{$rowNum}", $row['initial_capital_source'] ?? '');
        $sheet->setCellValue("R{$rowNum}", $row['current_capital_amount'] ?? '');
        $sheet->setCellValue("S{$rowNum}", $row['founding_male'] ?? '');
        $sheet->setCellValue("T{$rowNum}", $row['founding_female'] ?? '');
        $sheet->setCellValue("U{$rowNum}", $row['founding_total'] ?? '');
        $sheet->setCellValue("V{$rowNum}", $row['gender_male'] ?? '');
        $sheet->setCellValue("W{$rowNum}", $row['gender_female'] ?? '');
        $sheet->setCellValue("X{$rowNum}", $row['gender_total'] ?? '');
        $sheet->setCellValue("Y{$rowNum}", $row['age_15_29'] ?? '');
        $sheet->setCellValue("Z{$rowNum}", $row['age_30_49'] ?? '');
        $sheet->setCellValue("AA{$rowNum}", $row['age_50_65'] ?? '');
        $sheet->setCellValue("AB{$rowNum}", $row['age_above_65'] ?? '');
        $sheet->setCellValue("AC{$rowNum}", $row['age_total'] ?? '');
        $sheet->setCellValue("AD{$rowNum}", $row['edu_illiterate'] ?? '');
        $sheet->setCellValue("AE{$rowNum}", $row['edu_primary'] ?? '');
        $sheet->setCellValue("AF{$rowNum}", $row['edu_secondary'] ?? '');
        $sheet->setCellValue("AG{$rowNum}", $row['edu_college'] ?? '');
        $sheet->setCellValue("AH{$rowNum}", $row['edu_total'] ?? '');
        $sheet->setCellValue("AI{$rowNum}", $row['permanent_male'] ?? '');
        $sheet->setCellValue("AJ{$rowNum}", $row['permanent_female'] ?? '');
        $sheet->setCellValue("AK{$rowNum}", $row['permanent_total'] ?? '');
        $sheet->setCellValue("AL{$rowNum}", $row['temporary_male'] ?? '');
        $sheet->setCellValue("AM{$rowNum}", $row['temporary_female'] ?? '');
        $sheet->setCellValue("AN{$rowNum}", $row['temporary_total'] ?? '');
        $sheet->setCellValue("AO{$rowNum}", $row['product_type'] ?? '');
        $sheet->setCellValue("AP{$rowNum}", $row['market_destination'] ?? '');

        $rowNum++;
    }

    $sheet->getStyle("A5:AP" . ($rowNum - 1))->applyFromArray([
        'font' => ['size' => 9],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['rgb' => 'D7DCE3'],
            ],
        ],
    ]);

    // Column B (name) wider + right-aligned like the HTML version
    $sheet->getStyle("B5:B" . ($rowNum - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    $sheet->getColumnDimension('B')->setWidth(28);
    $sheet->getColumnDimension('A')->setWidth(6);

    foreach (range('C', 'AP') as $col) {
        $sheet->getColumnDimension($col)->setWidth(10);
    }

    // Freeze panes so header + first two columns stay visible while scrolling in Excel
    $sheet->freezePane('C5');

    $filename = 'enterprise-report-' . date('Y-m-d-His') . '.xlsx';

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}
}