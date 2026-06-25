<?php
namespace App\Controllers;
use App\Models\Organization;
use App\Models\Branch;
use App\Models\User;
use App\Helpers\AuthHelper;
use Ramsey\Uuid\Uuid;
use \App\Traits\FileUploadTrait;
// 1. BaseControllerን እንዲወርስ እናደርጋለን
class OrgController extends BaseController {
     use FileUploadTrait;
   public function showRegisterForm() {
     AuthHelper::checkRole(['system_admin', 'org_admin']);
    $organizations =[];
    if ($_SESSION['user']['role'] === 'system_admin') {
        $organizations = (new Organization($this->db))->getAll();
        $this->render('register-organization', [
        'title' => 'ድርጅት መመዝገቢያ',
        'organizations' => $organizations
    ]);
    }
    else {
      $myBranchId = $_SESSION['user']['branch_id'];
    // 2. ሞዴሉን መጥራት
    $branchModel = new Branch($this->db);
    $organizations = $branchModel->getImmediateSubBranches($myBranchId);

    $this->render('register-branch', [
        'title' => 'ቅርንጫፍ መመዝገቢያ',
        'organizations' => $organizations
    ]);
    }
   }

    public function handleRegistration() {
        AuthHelper::checkRole(['system_admin']);
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
        // 1. ዳታውን መቀበል
        $orgName = isset($_POST['org_name']) ? trim($_POST['org_name']) : '';
        $orgAlternateName = isset($_POST['org_alternate_name']) ? trim($_POST['org_alternate_name']) : '';
        $orgDescription = isset($_POST['org_description']) ? trim($_POST['org_description']) : '';
        $postal_code = isset($_POST['postal_code']) ? trim($_POST['postal_code']) : '';
        $phone_number = isset($_POST['phone_number']) ? trim($_POST['phone_number']) : '';
        // Session ውስጥ ተጠቃሚው መኖሩን ማረጋገጥ (ደህንነት)
        $registeredBy = isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : null;

        // 2. Validation
        if (empty($orgName) || empty($orgDescription) || empty($postal_code) || empty($phone_number)) {
            $_SESSION['error'] = "እባክዎ ሁሉንም መስኮች በትክክል ይሙሉ!";
            header("Location: " . $_ENV['BASE_URL'] . "/register-organization");
            exit();
        }
if (!preg_match('/^\d{10}$/', $phone_number)) {
    $_SESSION['error'] = "ስልክ ቁጥር በትክክል 10 አሃዝ መሆን አለበት!";
    header("Location: " . $_ENV['BASE_URL'] . "/register-organization");
    exit();
}

if (!preg_match('/^\d{1,6}$/', $postal_code)) {
    $_SESSION['error'] = "የፖስታ ኮድ ከ6 አሃዝ መብለጥ የለበትም!";
    header("Location: " . $_ENV['BASE_URL'] . "/register-organization");
    exit();
}
        if (!$registeredBy) {
            $_SESSION['error'] = "ለዚህ ተግባር መጀመሪያ መግባት (Login) አለብዎት!";
            header("Location: " . $_ENV['BASE_URL'] . "/login");
            exit();
        }
// In your handleCreate/registration method
if (empty($_FILES['logo']['name']) || $_FILES['logo']['error'] === UPLOAD_ERR_NO_FILE) {
    $_SESSION['error'] = 'እባክዎ የድርጅት ሎጎ ይምረጡ።';
    header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/register-organization");
    exit();
}
       $imageName   = $this->uploadFile('logo', 'images');

if (!$imageName) {
    $messages = [];

    if (!$imageName) {
        $imageError  = $_FILES['logo']['error'] ?? UPLOAD_ERR_NO_FILE;
        $messages[] = 'Photo: ' . $this->getUploadErrorMessage($imageError);
    }

    $_SESSION['error'] = !empty($messages)
        ? 'ፋይል እንዲወርድ አልቻለም። ' . implode(' | ', $messages)
        : 'ፋይል እንዲወርድ አልቻለም።';

    header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/register-organization");
    exit();
}
        // 3. UUID ማመንጨት (ለ Organization)
        $id = Uuid::uuid7()->toString();

        $orgModel = new Organization($this->db);

        try {
            // 4. ሞዴሉን መጥራት (ይህ ድርጅቱን እና Main Officeን በአንድ ላይ ይመዘግባል)
            $result = $orgModel->create($id, $orgName, $orgDescription, $registeredBy, $orgAlternateName, $imageName, $postal_code, $phone_number);

            if ($result) {
                // Log organization creation
                \App\Helpers\AuditHelper::log('organization_created', 'organization', $id, null, [
                    'name' => $orgName,
                    'description' => $orgDescription,
                    'postal_code' => $postal_code,
                    'phone_number' => $phone_number,
                    'registered_by' => $registeredBy
                ]);

                $_SESSION['success'] = "ድርጅቱ እና ዋና መሥሪያ ቤቱ በተሳካ ሁኔታ ተመዝግቧል!";
                header("Location: " . $_ENV['BASE_URL'] . "/register-organization");
                exit();
            }
            if ($imageName) {
                $imagePath = dirname(__DIR__, 2) . '/storage/uploads/images/' . $imageName;
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
        } catch (\Exception $e) {
            // 5. ስህተቶችን መያዝ
            if ($e instanceof \PDOException && $e->getCode() == 23000) {
                $_SESSION['error'] = "ይህ ድርጅት ቀደም ብሎ ተመዝግቧል!";
            } else {
                error_log("Registration Error: " . $e->getMessage());
                $_SESSION['error'] = "የቴክኒክ ስህተት አጋጥሟል፤ እባክዎ ቆይተው ይሞክሩ።";
            }
            header("Location: " . $_ENV['BASE_URL'] . "/register-organization");
            exit();
        }
    }
}
    
    
public function handleEditOrganization() {
    AuthHelper::checkRole(['system_admin', 'org_admin']);
    header('Content-Type: application/json');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $orgId            = isset($_POST['id'])                 ? trim($_POST['id'])                 : '';
        $branchId         = isset($_POST['branch_id'])          ? trim($_POST['branch_id'])          : '';
        $orgName          = isset($_POST['org_name'])           ? trim($_POST['org_name'])           : '';
        $orgAlternateName = isset($_POST['org_alternate_name']) ? trim($_POST['org_alternate_name']) : '';
        $orgDescription   = isset($_POST['org_description'])    ? trim($_POST['org_description'])    : '';
        $postal_code      = isset($_POST['postal_code'])        ? trim($_POST['postal_code'])        : '';
        $phone_number     = isset($_POST['phone_number'])       ? trim($_POST['phone_number'])       : '';

        if (empty($orgId) || empty($orgName) || empty($orgDescription) || empty($postal_code) || empty($phone_number)) {
            echo json_encode(['status' => 'error', 'message' => 'እባክዎ የተቋሙን መለያ እና ስም በትክክል ያስገቡ!']);
            exit();
        }
// ── Validation ────────────────────────────────────────────────────────────
if (!preg_match('/^\d{10}$/', $phone_number)) {
    echo json_encode(['status' => 'error', 'message' => 'ስልክ ቁጥር በትክክል 10 አሃዝ መሆን አለበት!']);
    exit();
}

if (!preg_match('/^\d{1,6}$/', $postal_code)) {
    echo json_encode(['status' => 'error', 'message' => 'የፖስታ ኮድ ከ6 አሃዝ መብለጥ የለበትም!']);
    exit();
}
// ─────────────────────────────────────────────────────────────────────────
        // ── Logo handling ─────────────────────────────────────────────────────
        // A file was actually chosen by the user
        $fileSubmitted = isset($_FILES['logo']) && $_FILES['logo']['error'] !== UPLOAD_ERR_NO_FILE;

        if ($fileSubmitted) {
            // Attempt upload; bail out on failure
            $imageName = $this->uploadFile('logo', 'images');

            if (!$imageName) {
                $imageError = $_FILES['logo']['error'] ?? UPLOAD_ERR_NO_FILE;
                $_SESSION['error'] = 'ፋይል እንዲወርድ አልቻለም። Photo: ' . $this->getUploadErrorMessage($imageError);
                header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/register-organization");
                exit();
            }
        } else {
            // No new logo submitted — keep the existing one from the DB
            $imageName = null;
        }
        // ─────────────────────────────────────────────────────────────────────

        $orgModel = new Organization($this->db);

        try {
            $oldData = $orgModel->findById($orgId);

            // Pass null for $imageName so updateOrganization keeps the current logo
            $result = $orgModel->updateOrganization(
                $orgId, $branchId, $orgName, $orgDescription,
                $orgAlternateName, $imageName, $phone_number, $postal_code
            );

            if ($result) {
                \App\Helpers\AuditHelper::log(
                    'organization_updated', 'organization', $orgId,
                    $oldData,
                    ['name' => $orgName, 'organization_type' => $orgDescription]
                );
                echo json_encode(['status' => 'success', 'message' => 'ድርጅቱ በተሳካ ሁኔታ ተሻሽሏል!']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'ማስተካከያው አልተሳካም፤ ምንም የተቀየረ መረጃ የለም።']);
            }
            exit();

        } catch (\PDOException $e) {
            if ($e->getCode() == 23000) {
                echo json_encode(['status' => 'error', 'message' => 'ይህ ድርጅት ቀደም ብሎ ተመዝግቧል!']);
            } else {
                error_log("Org Update Error: " . $e->getMessage());
                echo json_encode(['status' => 'error', 'message' => 'የዳታቤዝ ስህተት አጋጥሟል!']);
            }
            exit();
        }
    }
}
public function handleBranchRegistration() {
     AuthHelper::checkRole(['system_admin', 'org_admin']);
    // የቅርንጫፍ መመዝገቢያ ሂደት እዚህ ይገባል
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // 1. ዳታውን መቀበል እና trim() ማድረግ   
        $branchName = isset($_POST['branch_name']) ? trim($_POST['branch_name']) : '';
        $registeredBy = isset($_SESSION['user']) ? $_SESSION['user']['id'] : null;  
        $orgAlternateName = isset($_POST['branch_alternate_name']) ? trim($_POST['branch_alternate_name']) : '';
        $phone_number = isset($_POST['branch_phone_number']) ? trim($_POST['branch_phone_number']) : '';
        $postal_code = isset($_POST['branch_postal_code']) ? trim($_POST['branch_postal_code']) : '';
        $branchModel =  new Branch($this->db);
        $branchLevel = $branchModel->getBranchById($_SESSION['user']['branch_id']);
        if (!$branchLevel || !isset($branchLevel['level'])) {
            $_SESSION['error'] = "የቅርንጫፍ መረጃ አልተገኘም!";
            header("Location: " . $_ENV['BASE_URL'] . "/register-branch");
            exit();
        }
        $level = $branchLevel['level'] + 1; // የተጠቃሚው ቅርንጫፍ የሚገኘው በእርሱ በላይ ነው
        $parentId =$_SESSION['user']['branch_id'] ?? null; // የተጠቃሚው ቅርንጫፍ ID
        $orgId = $_SESSION['user']['organization_id'] ?? null;
        // 2. Validation
        if (empty($branchName)) {
            $_SESSION['error'] = "እባክዎ የቅርንጫፉን ስም በትክክል ያስገቡ!";
            header("Location: " . $_ENV['BASE_URL'] . "/register-branch");
            exit();        
 }
if (!preg_match('/^\d{10}$/', $phone_number)) {
    $_SESSION['error'] = "ስልክ ቁጥር በትክክል 10 አሃዝ መሆን አለበት!";
    header("Location: " . $_ENV['BASE_URL'] . "/register-organization");
    exit();
}

if (!preg_match('/^\d{1,6}$/', $postal_code)) {
    $_SESSION['error'] = "የፖስታ ኮድ ከ6 አሃዝ መብለጥ የለበትም!";
    header("Location: " . $_ENV['BASE_URL'] . "/register-organization");
    exit();
}
 // In your handleCreate/registration method
if (empty($_FILES['logo']['name']) || $_FILES['logo']['error'] === UPLOAD_ERR_NO_FILE) {
    $_SESSION['error'] = 'እባክዎ የድርጅት ሎጎ ይምረጡ።';
    header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/register-organization");
    exit();
}
       $imageName   = $this->uploadFile('logo', 'images');

if (!$imageName) {
    $messages = [];

    if (!$imageName) {
        $imageError  = $_FILES['logo']['error'] ?? UPLOAD_ERR_NO_FILE;
        $messages[] = 'Photo: ' . $this->getUploadErrorMessage($imageError);
    }

    $_SESSION['error'] = !empty($messages)
        ? 'ፋይል እንዲወርድ አልቻለም። ' . implode(' | ', $messages)
        : 'ፋይል እንዲወርድ አልቻለም።';

    header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/register-organization");
    exit();
}
        // 3. UUID ማመንጨት (ለ Branch)
        $id = Uuid::uuid7()->toString();

        try {
            // 4. ሞዴሉን መጥራት
            $result = $branchModel->insertBranch([
                'id' => $id,
                'org_id' => $orgId,
                'parent_id' => $parentId,
                'name' => $branchName,
                'alt_name' => $orgAlternateName,
                'phone_number' => $phone_number,
                'postal_code' => $postal_code,
                'level' => $level,
                'logo_url' => $imageName,
                'registered_by' => $registeredBy
            ]);

            if ($result) {
                // Log branch creation
                \App\Helpers\AuditHelper::log('branch_created', 'branch', $id, null, [
                    'org_id' => $orgId,
                    'parent_id' => $parentId,
                    'name' => $branchName,
                    'alt_name' => $orgAlternateName,
                    'level' => $level,
                    'logo_url' => $imageName,
                    'registered_by' => $registeredBy
                ]);

                $_SESSION['success'] = "ቅርንጫፉ በተሳካ ሁኔታ ተመዝግቧል!";
                header("Location: " . $_ENV['BASE_URL'] . "/register-branch");
                exit();
            }
        } catch (\PDOException $e) {
            if ($e->getCode() == 23000) {
                $_SESSION['error'] = "ይህ ቅርንጫፍ ቀደም ብሎ ተመዝግቧል!";
            } else {
                error_log("Branch Registration Error: " . $e->getMessage());
                $_SESSION['error'] = "የዳታቤዝ ስህተት አጋጥሟል፤ እባክዎ ቆይተው ይሞክሩ።";
            }
            header("Location: " . $_ENV['BASE_URL'] . "/register-branch");
            exit();
        }
    }
}
public function handleEditBranch() {
    // Set headers immediately for JavaScript compatibility
    AuthHelper::checkRole(['org_admin']);
    header('Content-Type: application/json');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {        
        $orgId = isset($_POST['id']) ? trim($_POST['id']) : '';
        $orgName = isset($_POST['branch_name']) ? trim($_POST['branch_name']) : '';
        $orgAlternateName = isset($_POST['branch_alternate_name']) ? trim($_POST['branch_alternate_name']) : '';
        $phone_number = isset($_POST['branch_phone_number']) ? trim($_POST['branch_phone_number']) : '';
        $postal_code = isset($_POST['branch_postal_code']) ? trim($_POST['branch_postal_code']) : '';
        $existingLogoUrl = isset($_POST['existing_logo_url']) ? trim($_POST['existing_logo_url']) : '';

        if (empty($orgId) || empty($orgName)) {
            echo json_encode(['status' => 'error', 'message' => 'እባክዎ የተቋሙን መለያ እና ስም በትክክል ያስገቡ!']);
            exit();
        }
// ── Validation ────────────────────────────────────────────────────────────
if (!preg_match('/^\d{10}$/', $phone_number)) {
    echo json_encode(['status' => 'error', 'message' => 'ስልክ ቁጥር በትክክል 10 አሃዝ መሆን አለበት!']);
    exit();
}

if (!preg_match('/^\d{1,6}$/', $postal_code)) {
    echo json_encode(['status' => 'error', 'message' => 'የፖስታ ኮድ ከ6 አሃዝ መብለጥ የለበትም!']);
    exit();
}
// ─────────────────────────────────────────────────────────────────────────
        // Check if a brand new file was actually selected for upload
        $hasNewFile = isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK;
        $imageName = null;

        if ($hasNewFile) {
            $imageName = $this->uploadFile('logo', 'images');

            // If the upload routine failed on a real file transfer attempt
            if (!$imageName) {
                $imageError = $_FILES['logo']['error'] ?? UPLOAD_ERR_NO_FILE;
                $errorMessage = 'ፋይል እንዲወርድ አልቻለም። ' . $this->getUploadErrorMessage($imageError);
                
                echo json_encode(['status' => 'error', 'message' => $errorMessage]);
                exit();
            }
        } else {
            // No new file uploaded, keep the current database image path intact
            $imageName = $existingLogoUrl;
        }

        $orgModel = new Branch($this->db);

        try {
            // Get old data for logging
            $oldData = $orgModel->getBranchById($orgId);
            
            // Pass the resolved image string to the model layer
            $result = $orgModel->updateBranch($orgId, $orgName, $orgAlternateName, $imageName, $phone_number, $postal_code);

            if ($result) {
                // Log branch update
                \App\Helpers\AuditHelper::log('branch_updated', 'branch', $orgId, $oldData, ['name' => $orgName]);

                echo json_encode(['status' => 'success', 'message' => 'ቅርንጫፉ በተሳካ ሁኔታ ተሻሽሏል!']);
                exit();
            } else {
                echo json_encode(['status' => 'error', 'message' => 'ማስተካከያው አልተሳካም፤ ምንም የተቀየረ መረጃ የለም።']);
                exit();
            }
        } catch (\PDOException $e) {
            if ($e->getCode() == 23000) {
                echo json_encode(['status' => 'error', 'message' => 'ይህ ቅርንጫፍ ቀደም ብሎ ተመዝግቧል!']);
            } else {
                error_log("Branch Update Error: " . $e->getMessage());
                echo json_encode(['status' => 'error', 'message' => 'የዳታቤዝ ስህተት አጋጥሟል!']);
            }
            exit();
        }
    }
}
 // Handle delete — returns JSON
public function delete(): void
{
    AuthHelper::checkRole(['system_admin', 'org_admin']);
    header('Content-Type: application/json');

    $data   = json_decode(file_get_contents('php://input'), true);
    $id     = (string) ($data['id']   ?? '');
    $type   = (string) ($data['type'] ?? 'org'); 
    $adminId = $_SESSION['user']['id'] ?? '';

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

        if (!$organizationId || !$branchId) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'ያልተፈቀደ ድርጊት።'
            ]);
            return;
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
        $model = null;
        if ($type === 'branch' && $_SESSION['user']['role'] === 'org_admin') {
            
             $model = new Branch($this->db);
            $action = 'branch_deleted';
            $metaKey = 'affected_sub_branches'; // ለቅርንጫፍ ንዑስ ቅርንጫፎች ይባላሉ
        } else if($_SESSION['user']['role'] === 'system_admin') { 
           $model = new Organization($this->db);
            $action = 'organization_deleted';
            $metaKey = 'affected_branches'; // ለድርጅት ቅርንጫፎች ይባላሉ
        }

        $result = $model->softDelete($id, $adminId, $reason, $source);

        if ($result['status'] === 'success') {
    // መጀመሪያ መረጃዎቹን ከሪሰልት እናውጣ
    $branchCount = $result['branchCount'] ?? 0;
    $userCount   = $result['userCount'] ?? 0;


    $metadata = [
        $metaKey          => $branchCount,
        'affected_users'  => $userCount,
        'deletion_source' => $source
    ];

    \App\Helpers\AuditHelper::log(
        action:     $action,
        entityType: $type,
        entityId:   $id,
        oldValues:  $result['oldRecord'],
        newValues:  ['status' => 'inactive'],
        metadata:   $metadata
    );

    unset($result['oldRecord'], $result['branchCount'], $result['userCount']);
}

        echo json_encode($result);

    } catch (\Exception $e) {
        error_log("Delete Error ({$type}): " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'ስህተት ተፈጥሯል፤ እባክዎ በድጋሚ ይሞክሩ።']);
    }
}
public function showDeletedLists() {
     AuthHelper::checkRole(['system_admin', 'org_admin']);
    $deletedOrgs =[];
    if ($_SESSION['user']['role'] === 'system_admin') {
        $deletedOrgs = (new Organization($this->db))->findAllDeleted();
        $this->render('organization-deleted-lists', [
        'title' => 'የተሰረዙ ድርጅቶች',
        'deletedOrgs' => $deletedOrgs
    ]);
    }
   if ($_SESSION['user']['role'] === 'org_admin') {
        $myBranchId = $_SESSION['user']['branch_id'] ?? null;
        $deletedOrgs = (new Branch($this->db))->findAllDeleted($myBranchId);
        $this->render('deleted-branches', [
        'title' => 'የተሰረዙ ቅርንጫፎች',
        'deletedOrgs' => $deletedOrgs
    ]);
    } 
}

 // ============================================================
    // RESTORE
    // ============================================================
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
    $type   = (string) ($data['type'] ?? 'branch'); // 'org' ወይም 'branch' መሆኑን ከ JS እንቀበላለን
    $userId = (string) ($_SESSION['user']['id'] ?? '');

    if (empty($id)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid ID']);
        return;
    }

    try {
        // 1. በ 'type' ላይ ተመስርቶ ሞዴሉን መምረጥ
        if ($_SESSION['user']['role'] === 'system_admin') {
            $model = new \App\Models\Organization($this->db);
            $action = 'organization_restored';
            $metaBranchesKey = 'restored_branches';
        } else {
            // org_admin ወይም system_admin ቅርንጫፍ ሲመልሱ
            $model = new \App\Models\Branch($this->db);
            $action = 'branch_restored';
            $metaBranchesKey = 'restored_subbranches';
        }

        $result = $model->restore($id, $userId);

        if ($result['status'] === 'success') {
            // 2. ኦዲት ሎግ መመዝገብ
            \App\Helpers\AuditHelper::log(
                action:     $action,
                entityType: $type,
                entityId:   $id,
                oldValues:  $result['oldRecord'],
                newValues:  ['status' => 'active'],
                metadata:   [
                    $metaBranchesKey => $result['restoredBranches'] ?? $result['restoredSubBranches'] ?? 0,
                    'restored_users'  => $result['restoredUsers'] ?? 0,
                    'restore_type'    => 'cascade_restore'
                ]
            );

            // ለተጠቃሚው የማይፈለጉ መረጃዎችን እናጥፋ
            unset($result['oldRecord'], $result['restoredBranches'], $result['restoredSubBranches'], $result['restoredUsers']);
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
    $type     = (string) ($data['type']             ?? 'branch');
    $adminId  = (string) ($_SESSION['user']['id']   ?? '');
    $password = (string) ($data['confirm_password'] ?? '');

    if (empty($id) || empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'መለያ ወይም ሚስጥራዊ ቁጥር አልገባም']);
        return;
    }

    // ============================================================
    // 1. Verify admin password
    // ============================================================
    $userModel = new \App\Models\User($this->db);
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
        if ($_SESSION['user']['role'] === 'system_admin') {
            $model      = new Organization($this->db);
            $action     = 'organization_purged';
            $entityType = 'organization';
            $metaKey    = 'purged_branches';
            $oldRecord  = $model->findById($id);
        } else {
            $model      = new Branch($this->db);
            $action     = 'branch_purged';
            $entityType = 'branch';
            $metaKey    = 'purged_subbranches';
            $oldRecord  = $model->getBranchById($id);
        }

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
                    $metaKey          => $result['branchCount'] ?? 0,
                    'purged_users'    => $result['userCount']   ?? 0,
                    'archive_id'      => $result['archiveId']   ?? null, // ← trace to archive
                    'deletion_type'   => 'permanent_purge',
                    'confirmed_by'    => $adminId
                ]
            );

            unset(
                $result['oldRecord'],
                $result['archiveId'],   // ← don't expose to frontend
                $result['branchCount'],
                $result['userCount']
            );
        }

        echo json_encode($result);

    } catch (\Exception $e) {
        error_log("Purge Error: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'መሰረዝ አልተቻለም።']);
    }
}
public function archiveList(): void
{
    AuthHelper::checkRole(['system_admin']);
    $model      = new Organization($this->db);
    $archivedOrgs = $model->findAllArchived();
    $this->render('archived-organizations', [
        'title' => 'Archived Organizations',
        'archivedOrgs' => $archivedOrgs
    ]);
    
}
public function branchArchiveList(): void
{
    AuthHelper::checkRole(['system_admin']);
    $model      = new Branch($this->db);
    $archivedOrgs = $model->findAllArchived();
    $this->render('archived-branches', [
        'title' => 'Archived Branches',
        'archivedOrgs' => $archivedOrgs
    ]);
    
}
// ============================================================
// RESTORE FROM ARCHIVE — system_admin only
// ============================================================
public function restoreFromArchive(): void
{
    AuthHelper::checkRole(['system_admin']);
    header('Content-Type: application/json');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
        return;
    }

    $data       = json_decode(file_get_contents('php://input'), true);
    $originalId = (string) ($data['original_id']      ?? '');
    $adminId    = (string) ($_SESSION['user']['id']    ?? '');
    $password   = (string) ($data['confirm_password']  ?? '');
    $type       = (string) ($data['type']              ?? 'org'); // 'org' or 'branch'

    if (empty($originalId) || empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'መለያ ወይም ሚስጥራዊ ቁጥር አልገባም']);
        return;
    }

    // ============================================================
    // 1. Verify admin password
    // ============================================================
    $userModel = new \App\Models\User($this->db);
    if (!$userModel->verifyPassword($adminId, $password)) {
        echo json_encode(['status' => 'error', 'message' => 'የእርስዎ ሚስጥራዊ ቁጥር ትክክል አይደለም።']);
        return;
    }

    // ============================================================
    // 2. Pick model + audit metadata based on type
    // ============================================================
    if ($type === 'org') {
        $model      = new Organization($this->db);
        $action     = 'organization_restored_from_archive';
        $entityType = 'organization';
        $nameKey    = 'orgName';      // ← key returned from model
    } else {
        $model      = new Branch($this->db);
        $action     = 'branch_restored_from_archive';
        $entityType = 'branch';
        $nameKey    = 'branchName';   // ← key returned from branch model
    }

    // ============================================================
    // 3. Restore — adminId passed, model never touches session
    // ============================================================
    $result = $model->restoreFromArchive($originalId, $adminId);

    // ============================================================
    // 4. Audit log
    // ============================================================
    if ($result['status'] === 'success') {
        \App\Helpers\AuditHelper::log(
            action:     $action,
            entityType: $entityType,
            entityId:   $originalId,
            oldValues:  null,
            newValues:  null,
            metadata:   [
                'archive_id'        => $result['archiveId']        ?? null,
                'name'              => $result[$nameKey]           ?? null,
                'restored_branches' => $result['branchCount']      ?? 0,
                'restored_users'    => $result['userCount']        ?? 0,
                'restored_by'       => $adminId
            ]
        );

        unset(
            $result['archiveId'],
            $result[$nameKey],
            $result['branchCount'],
            $result['userCount']
        );
    }

    echo json_encode($result);
}
}
