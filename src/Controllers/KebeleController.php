<?php
namespace App\Controllers;
use App\Models\Organization;
use App\Models\KebeleModel;
use App\Models\User;
use App\Helpers\AuthHelper;
use Ramsey\Uuid\Uuid;
 
// 1. BaseControllerን እንዲወርስ እናደርጋለን
class KebeleController extends BaseController {

   public function showRegisterForm() {
     AuthHelper::checkRole(['system_admin', 'org_admin']);
    $kebeledata =[];
   
      $myBranchId = $_SESSION['user']['branch_id'];
    // 2. ሞዴሉን መጥራት
    $kebele = new KebeleModel($this->db);
    $kebeledata = $kebele->getImmediatekebele($myBranchId);

    $this->render('register-kebele', [
        'title' => 'ቀበሌ መመዝገቢያ',
        'kebeledata' => $kebeledata
    ]);
    }


public function handleRegistration() {
        AuthHelper::checkRole(['org_admin']);
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
        // 1. ዳታውን መቀበል
        $kebele_name = isset($_POST['kebele_name']) ? trim($_POST['kebele_name']) : '';
        $branch_id = isset($_SESSION['user']['branch_id']) ? trim($_SESSION['user']['branch_id']) : '';
        // Session ውስጥ ተጠቃሚው መኖሩን ማረጋገጥ (ደህንነት)
        $registeredBy = isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : null;

        // 2. Validation
        if (empty($kebele_name)) {
            $_SESSION['error'] = "እባክዎ የቀበሌዉን  በትክክል ይሙሉ!";
            header("Location: " . $_ENV['BASE_URL'] . "/register-kebele");
            exit();
        } 
        if (!$registeredBy) {
            $_SESSION['error'] = "ለዚህ ተግባር መጀመሪያ መግባት (Login) አለብዎት!";
            header("Location: " . $_ENV['BASE_URL'] . "/login");
            exit();
        }
        if($branch_id == null){
            $_SESSION['error'] = "የቀበሌዉን መመዝገቢያ ለመፈጸም እባክዎ ከመጀመሪያ ድርጅቱን ይመዝግቡ!";
            header("Location: " . $_ENV['BASE_URL'] . "/register-kebele");
            exit();
        }
// In your handleCreate/registration method
  
  
        $orgModel = new KebeleModel($this->db);

        try {
            // 4. ሞዴሉን መጥራት (ይህ ድርጅቱን እና Main Officeን በአንድ ላይ ይመዘግባል)
            $result = $orgModel->create($branch_id, $kebele_name, $registeredBy);

            if ($result) {
                // Log organization creation
                \App\Helpers\AuditHelper::log('kebele registered', 'organization', $branch_id, null, [
                    'name' => $kebele_name,
                   
                    'registered_by' => $registeredBy
                ]);

                $_SESSION['success'] = " በተሳካ ሁኔታ ተመዝግቧል!";
                header("Location: " . $_ENV['BASE_URL'] . "/register-kebele");
                exit();
            }
          
        } catch (\Exception $e) {
            // 5. ስህተቶችን መያዝ
            if ($e instanceof \PDOException && $e->getCode() == 23000) {
    error_log("Duplicate/constraint error detail: " . print_r($e->errorInfo, true));
    $_SESSION['error'] = "ይህ ድርጅት ቀደም ብሎ ተመዝግቧል!";
} else {
    error_log("Registration Error: " . $e->getMessage());
    $_SESSION['error'] = "የቴክኒክ ስህተት አጋጥሟል፤ እባክዎ ቆይተው ይሞክሩ።";
}
            header("Location: " . $_ENV['BASE_URL'] . "/register-kebele");
            exit();
        }
    }
}


 public function delete(): void
{
    AuthHelper::checkRole(['system_admin', 'org_admin']);
    header('Content-Type: application/json');

    $data   = json_decode(file_get_contents('php://input'), true);
    $id     = (string) ($data['id']   ?? '');
    $type   = (string) ($data['type'] ?? 'kebele'); 
    $adminId = $_SESSION['user']['id'] ?? '';

    $reason = trim($data['reason']      ?? '');
        $source = 'INDIVIDUAL';
        $password = $data['confirm_password'] ?? '';

        // Validate input
        if (!$id || !$reason || !$password || !$source) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'ሁሉም መስኮች አስፈላጊ ስለሆነ እባክዎ ሁሉንም ያስገቡ።'
            ]);
            return;
        }

        $user           = $_SESSION['user'] ?? [];
        $organizationId = $user['organization_id'] ?? null;
        $branchId = $user['branch_id'] ?? null;

        if (!$branchId) {
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
        if ($type === 'kebele' && $_SESSION['user']['role'] === 'org_admin') {
            
             $model = new KebeleModel($this->db);
            $action = 'kebele_deleted';
            $metaKey = 'affected_kebele'; // ለቅርንጫፍ ንዑስ ቅርንጫፎች ይባላሉ
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

}
?>