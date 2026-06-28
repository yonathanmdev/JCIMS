<?php
namespace App\Controllers;
use App\Models\Organization;
use App\Models\Branch;
use App\Models\User;
use App\Models\Director;
use App\Helpers\AuthHelper;
use Ramsey\Uuid\Uuid;

// 1. BaseControllerን እንዲወርስ እናደርጋለን
class UserController extends BaseController {
    
  public function showRegisterForm() {
    AuthHelper::checkRole(['system_admin', 'org_admin']);

    $myBranchId = $_SESSION['user']['branch_id'];
    $id         = $_SESSION['user']['id'] ?? null;
    $role       = $_SESSION['user']['role'];

    $userModel   = new User($this->db);
    $branchModel = new Branch($this->db);
    $branchName  = $branchModel->getBranchById($myBranchId);

    $users       = [];
    $organizations = [];
    $subBranches   = [];

    if ($role === 'system_admin') {
        $users         = $userModel->getAllOrgAdmins();
        $organizations = (new Organization($this->db))->getAll();

    } elseif ($role === 'org_admin') {
        $users       = $userModel->getUsersForMyBranchHierarchy($myBranchId, $id);
        $subBranches = $branchModel->getImmediateSubBranches($myBranchId);
    }

    $this->render('register-user', [
        'title'         => 'ተጠቃሚ መመዝገቢያ',
        'organizations' => $organizations,
        'users'         => $users,
        'branchName'    => $branchName,
        'subBranches'   => $subBranches,
    ]);
}

   public function handleRegistration() {
    AuthHelper::checkRole(['system_admin', 'org_admin']);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // 1. Receive inputs
        $firstName       = isset($_POST['firstname'])       ? trim($_POST['firstname'])       : '';
        $fatherName      = isset($_POST['fathername'])      ? trim($_POST['fathername'])      : '';
        $gFatherName     = isset($_POST['grandfathername']) ? trim($_POST['grandfathername']) : '';
        $email           = isset($_POST['email'])           ? trim($_POST['email'])           : '';
        $password        = $_POST['password'] ?? '';
        $txtpassword        = $_POST['password'] ?? '';
        $phone           = isset($_POST['phone'])           ? trim($_POST['phone'])           : '';
        $postedBranchId  = isset($_POST['branch_id'])       ? trim($_POST['branch_id'])       : '';

        $branchModel     = new Branch($this->db);
        $sessionRole     = $_SESSION['user']['role'];

        if ($sessionRole === 'system_admin') {
            $role = 'org_admin';

            // system_admin: organization comes from POST (selected org)
            
            $orgId = isset($_POST['organization_id']) ? trim($_POST['organization_id']) : '';

            if (empty($orgId)) {
                $_SESSION['error'] = "እባክዎ ድርጅት ይምረጡ!";
                header("Location: " . $_ENV['BASE_URL'] . "/register-user");
                exit();
            }

            // branch is the main office of the selected org
             $organization = (new Organization($this->db))->findById($orgId);
             $organization_id = $organization['org_id'];
            $mainBranchId = $branchModel->getMainOfficeId($organization_id);
            if (!$mainBranchId) {
                $_SESSION['error'] = "የዚህ ድርጅት ዋና መሥሪያ ቤት አልተገኘም!";
                header("Location: " . $_ENV['BASE_URL'] . "/register-user");
                exit();
            }

        } else {
            // org_admin: organization is ALWAYS from session
            $branchModel =  new Branch($this->db);
        $branchLevel = $branchModel->getBranchById($_SESSION['user']['branch_id']);
        if (!$branchLevel ||  !isset($branchLevel['organization_id'])) {
            $_SESSION['error'] = "የቅርንጫፍ መረጃ አልተገኘም!";
            header("Location: " . $_ENV['BASE_URL'] . "/register-branch");
            exit();
        }
            $role            = $_POST['role'] ?? '';
            $organization_id = $branchLevel['organization_id'];

            $allowedRoles = ['org_admin', 'team_leader', 'officer'];
            if (!in_array($role, $allowedRoles)) {
                $_SESSION['error'] = "የተፈቀደ Role አልተመረጠም!";
                header("Location: " . $_ENV['BASE_URL'] . "/register-user");
                exit();
            }

            if ($role === 'org_admin') {
                // branch comes from POST (selected sub-branch)
                if (empty($postedBranchId)) {
                    $_SESSION['error'] = "እባክዎ ቅርንጫፍ ይምረጡ!";
                    header("Location: " . $_ENV['BASE_URL'] . "/register-user");
                    exit();
                }

              $subBranch = $branchModel->isSubBranchOf(
    $postedBranchId,
    $_SESSION['user']['branch_id']
);
                if ($subBranch === false) {
                    $_SESSION['error'] = "የተመረጠው ቅርንጫፍ የእርስዎ ንዑስ ቅርንጫፍ አይደለም!";
                    header("Location: " . $_ENV['BASE_URL'] . "/register-user");
                    exit();
                }
                $mainBranchId = $subBranch['internal_id'];

            } else {
                // team_leader / officer: branch is always session branch — ignore POST
                $mainBranchId = $_SESSION['user']['branch_id'];
            }
        }

        $registeredBy = $_SESSION['user']['id'] ?? null;

        // 2. Basic validation
        if (empty($firstName) || empty($password) || empty($role)
            || empty($fatherName) || empty($gFatherName) || empty($phone)) {
            $_SESSION['error'] = "እባክዎ ሁሉንም አስፈላጊ መረጃዎች በትክክል ያስገቡ!";
            header("Location: " . $_ENV['BASE_URL'] . "/register-user");
            exit();
        }
      if (!empty($email)) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "እባክዎ ትክክለኛ ኢሜይል ያስገቡ!";
        header("Location: " . $_ENV['BASE_URL'] . "/register-user");
        exit();
    }
}
        // 3. UUID and password hash
        $uuid           = Uuid::uuid7()->toString();
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $userModel = new User($this->db);

        try {
            $result = $userModel->create(
                $uuid,
                $mainBranchId,
                $firstName,
                $fatherName,
                $gFatherName,
                $phone,
                $email,
                $hashedPassword,
                $txtpassword,
                $role,
                $registeredBy
            );

            if ($result) {
                \App\Helpers\AuditHelper::log('user_created', 'user', $uuid, null, [
                    'branch_id'       => $mainBranchId,
                    'first_name'      => $firstName,
                    'father_name'     => $fatherName,
                    'grand_father_name' => $gFatherName,
                    'phone'           => $phone,
                    'email'           => $email,
                    'role'            => $role,
                    'registered_by'   => $registeredBy
                ]);

                $_SESSION['success'] = "ተጠቃሚው በተሳካ ሁኔታ ተመዝግቧል!";
                header("Location: " . $_ENV['BASE_URL'] . "/register-user");
                exit();
            } else {
                $_SESSION['error'] = "ምዝገባው አልተሳካም፤ እባክዎ እንደገና ይሞክሩ።";
                header("Location: " . $_ENV['BASE_URL'] . "/register-user");
                exit();
            }

        } catch (\PDOException $e) {
            if ($e->getCode() == 23000) {
                $_SESSION['error'] = "ይህ ኢሜይል ቀደም ብሎ ተመዝግቧል!";
            } else {
                error_log("Registration Error: " . $e->getMessage());
                $_SESSION['error'] = "የቴክኒክ ስህተት አጋጥሟል፤ እባክዎ ቆይተው ይሞክሩ።";
            }
            header("Location: " . $_ENV['BASE_URL'] . "/register-user");
            exit();
        }
    }
}

public function getUserById()
{
     AuthHelper::checkRole(['system_admin', 'org_admin']);
    header('Content-Type: application/json');

    $id = $_GET['id'] ?? null;

    if (!$id) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid ID'
        ]);
        return;
    }

    $userModel = new User($this->db);
    $user = $userModel->findById($id);

    if ($user) {
        echo json_encode([
            'status' => 'success',
            'data' => $user
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'User not found'
        ]);
    }
}
public function handleUpdateUser()
{
    AuthHelper::checkRole(['system_admin', 'org_admin']);

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: " . $_ENV['BASE_URL'] . "/register-user");
        exit();
    }

    try {
        // 1. መረጃዎችን መቀበል
        $id = $_POST['id'] ?? null; // በ Form ውስጥ <input type="hidden" name="id"> መኖሩን አረጋግጥ
        
        $data = [
            'first_name'        => trim($_POST['edit_firstname']),
            'father_name'       => trim($_POST['edit_fathername']),
            'grand_father_name' => trim($_POST['edit_grandfathername']),
            'phone'             => trim($_POST['edit_phone']),
            'email'             => trim($_POST['edit_email'])
        ];

        // 2. Validation (መሰረታዊ ማረጋገጫ)
        if (empty($id) || in_array("", $data)) {
            $_SESSION['error'] = "እባክዎ ሁሉንም አስፈላጊ መረጃዎች በትክክል ያስገቡ!";
            header("Location: " . $_ENV['BASE_URL'] . "/register-user");
            exit();
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = "ትክክለኛ ኢሜይል ያስገቡ!";
            header("Location: " . $_ENV['BASE_URL'] . "/register-user");
            exit();
        }

        // 3. Update ለማድረግ መሞከር
        $userModel = new User($this->db);
        
        // Get old data for logging
        $oldData = $userModel->findById($id);
        
        $isUpdated = $userModel->updateUser($id, $data);

        if ($isUpdated) {
            // Log the user update
            \App\Helpers\AuditHelper::log('user_updated', 'user', $id, $oldData, $data);
            
            $_SESSION['success'] = "መረጃው በተሳካ ሁኔታ ተቀይሯል!";
        } else {
            // እዚህ ጋር ዳታቤዙ ላይ ምንም ለውጥ ካልተደረገ (ለምሳሌ መረጃው ያው ከሆነ)
            $_SESSION['info'] = "ምንም የተቀየረ አዲስ መረጃ የለም።";
        }

    } catch (\Exception $e) {
        error_log("Update Error: " . $e->getMessage());
        $_SESSION['error'] = "የቴክኒክ ስህተት ተፈጥሯል።";
    }

    header("Location: " . $_ENV['BASE_URL'] . "/register-user");
    exit();
}

public function delete(): void
{
    AuthHelper::checkRole(['system_admin', 'org_admin']);
    header('Content-Type: application/json');

    $data   = json_decode(file_get_contents('php://input'), true);
    $id     = (string) ($data['id']   ?? '');
    $adminId = $_SESSION['user']['id'] ?? '';
    $type = 'user'; // ለ Audit Log
   
    $reason = trim($data['reason']      ?? '');
        $source = 'INDIVIDUAL';
        $password = $data['confirm_password'] ?? '';

        // Validate input
        if (!$id || !$reason || !$password || !$source) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'ሁሉም መስኮች አስፈላጊ ናቸው።'
            ]);
            return;
        }

        $user           = $_SESSION['user'] ?? [];
        $organizationId = $user['organization_id'] ?? null;
        $branchId = $user['branch_id'] ?? null;
if($user['role'] === 'org_admin') {
    

        if (!$organizationId || !$branchId) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'ያልተፈቀደ ድርጊት።'
            ]);
            return;
        }
}
       

    if (empty($id)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid ID']);
        return;
    }

    if (empty($adminId)) {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
        return;
    }
 // Verify password
        $userModel = new User($this->db);
        if (!$userModel->verifyPassword($user['id'], $password)) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'ፓስዋርዱ ትክክል አይደለም።'
            ]);
            return;
        }

    try {
            $model = new User($this->db);
            $action = 'user_deleted';
            $metaKey = 'affected_records'; // ለድርጅት ቅርንጫፎች ይባላሉ
        $result = $model->softDelete($id, $adminId, $reason, $source);

        if ($result['status'] === 'success') {
   

    $metadata = [
        $metaKey          =>$result['branchCount'] ?? 0,
        'affected_users'  => $result['userCount'] ?? 0,
        'afected_directors' => $result['directorCount'] ?? 0,
        'affected_job_properties' => $result['jobPropertyCount'] ?? 0,
        'affected_employees' => $result['employeeCount'] ?? 0,
        'affected_scholarships' => $result['scholarshipCount'] ?? 0,
        'affected_debt_suspensions' => $result['debtSuspensionCount'] ?? 0,
        'deletion_source' => $source
    ];

    \App\Helpers\AuditHelper::log(
        action:     $action,
        entityType: $type,
        entityId:   $id,
        newValues:  ['status' => 'inactive'],
        metadata:   $metadata
    );

    unset($result['branchCount'], $result['userCount']);
}

        echo json_encode($result);

    } catch (\Exception $e) {
        error_log("Delete Error ({$type}): " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'ስህተት ተፈጥሯል፤ እባክዎ በድጋሚ ይሞክሩ።']);
    }
}
public function showDeletedLists() {
     AuthHelper::checkRole(['system_admin', 'org_admin']);

        $myBranchId = $_SESSION['user']['branch_id'] ?? null;
        $userModel = (new User($this->db));
            $users = $userModel->findAllDeleted($myBranchId);
        $this->render('deleted-users', [
        'title' => 'የተሰረዙ ተቆጣጣሪዎች',
        'users' => $users
    ]);
   
}
 public function restore(): void
{
    AuthHelper::checkRole(['system_admin', 'org_admin']);
    header('Content-Type: application/json');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
        return;
    }

    $data   = json_decode(file_get_contents('php://input'), true);
    $id     = (string) ($data['id']   ?? '');
    $userId = (string) ($_SESSION['user']['id'] ?? '');

    if (empty($id)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid ID']);
        return;
    }

    try {
   
            // org_admin ወይም system_admin ቅርንጫፍ ሲመልሱ
            $model = new User($this->db);
            $action = 'user_restored';
            $metaKey = 'restored_user';
        

        $result = $model->restore($id, $userId);

        if ($result['status'] === 'success') {
            // 2. ኦዲት ሎግ መመዝገብ
            \App\Helpers\AuditHelper::log(
                action:     $action,
                entityType: 'user',
                entityId:   $id,
                newValues:  ['status' => 'active'],
                metadata:   [
                    $metaKey => 1, 'restored_users'  => 1,
                    'restore_type'    => 'individual_restore'
                ]
            );

           
            }

        echo json_encode($result);

    } catch (\Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'ስህተት፡ ' . $e->getMessage()]);
    }
}

    // ============================================================
    // PURGE (permanent delete)
    // ============================================================
  public function purge(): void
{
    AuthHelper::checkRole(['system_admin', 'org_admin']);
    header('Content-Type: application/json');

    $data     = json_decode(file_get_contents('php://input'), true);
    $id       = (string) ($data['id']               ?? '');
    $adminId  = (string) ($_SESSION['user']['id']   ?? '');
    $password = (string) ($data['confirm_password'] ?? '');

    if (empty($id) || empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'መለያ ወይም ሚስጥራዊ ቁጥር አልገባም']);
        return;
    }

    // ============================================================
    // 1. Verify admin password
    // ============================================================
    $userModel = new User($this->db);
    if (!$userModel->verifyPassword($adminId, $password)) {
        echo json_encode(['status' => 'error', 'message' => 'የእርስዎ ሚስጥራዊ ቁጥር (Password) ትክክል አይደለም።']);
        return;
    }

    try {
        // ============================================================
        // 2. Pick model based on role
        // ============================================================
        $oldRecord = null;
        $archiveId = Uuid::uuid7()->toString();
       
            $model      = new User($this->db);
            $action     = 'user_purged';
            $entityType = 'user';
            $metaKey    = 'purged_users';
            $oldRecord  = $model->findById($id);

        if (!$oldRecord) {
            echo json_encode(['status' => 'error', 'message' => 'መረጃው አልተገኘም።']);
            return;
        }

        // ============================================================
        // 3. Purge — model handles archive + hard delete internally
        // ============================================================
        $result = $model->purge($id, $archiveId);

        // ============================================================
        // 4. Audit log — includes archiveId so you can trace back
        // ============================================================
        if ($result['status'] === 'success') {
            \App\Helpers\AuditHelper::log(
                action:     $action,
                entityType: $entityType,
                entityId:   $id,
                oldValues:  $oldRecord,
                newValues:  null,
                metadata:   [
                    $metaKey          => 1,
                    'purged_users'    => 1,
                    'archive_id'      => $result['archiveId']   ?? null, // ← trace to archive
                    'deletion_type'   => 'permanent_purge',
                    'confirmed_by'    => $adminId
                ]
            );

            unset(
                $result['oldRecord'],
                $result['archiveId'],   // ← don't expose to frontend
                $result['userCount']
            );
        }

        echo json_encode($result);

    } catch (\Exception $e) {
        error_log("Purge Error: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'መሰረዝ አልተቻለም።']);
    }
}
}