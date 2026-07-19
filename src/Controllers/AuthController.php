<?php
namespace App\Controllers;
use App\Helpers\BranchNameHelper;
use App\Models\User;
use App\Models\Branch;

// 1. BaseControllerን እንዲወርስ (Extends) እናደርጋለን
class AuthController extends BaseController {

    public function showLoginForm() {
        // BaseController ውስጥ ያለውን render በመጠቀም ቪው መጥራት
        $this->render('auth/login', [
            'title' => "JCIMS - Login"
        ]);
    }

    public function handleLogin() {
        // ሴሽን አስቀድሞ በ index.php ተጀምሯል (session_start())
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // 1. ዳታውን መቀበል እና trim ማድረግ
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $password = $_POST['password'] ?? '';
// 2. Server-Side Validation: ዳታው ባዶ አለመሆኑን ማረጋገጥ
        if (empty($email) || empty($password)) {
            $_SESSION['error'] = "እባክዎ ኢሜይል እና ፓስወርድ በትክክል ያስገቡ!";
            header("Location: " . $_ENV['BASE_URL'] . "/login");
            exit();
        }
            // 2. የ User ሞዴልን መጥራት (ከ BaseController የመጣውን $this->db በመስጠት)
            $userModel = new User($this->db);
            $user = $userModel->findByEmail($email);

            // 3. ተጠቃሚው መኖሩን እና ፓስወርዱን ማረጋገጥ
            if ($user && password_verify($password, $user['password'])) {
                
               
            // ← ADD THE STATUS CHECK HERE, before session_regenerate_id / building $_SESSION['user']

  if (is_null($user['status'])) {
    // default password still active — force password change first
    unset($_SESSION['pending_user_id']);
  
    session_regenerate_id(true);
    $_SESSION['pending_user_id'] = $user['id'];// confirm this matches your actual PK column
   
    \App\Helpers\AuditHelper::logAs($user['user_id'], 'forced_password_change_redirect', 'auth', $user['id']);

    header("Location: " . $_ENV['BASE_URL'] . "/password-change?forced=1");
    exit();

} elseif ($user['status'] !== 'active') {
    // any other non-active status (suspended, disabled, etc.)
    \App\Helpers\AuditHelper::logAs($user['user_id'], 'login_blocked_inactive', 'auth', $user['id']);
    $_SESSION['error'] = "የመለያዎ ሁኔታ ንቁ አይደለም። እባክዎ አስተዳዳሪውን ያግኙ!";
    header("Location: " . $_ENV['BASE_URL'] . "/login");
    exit();
}

// status === 'active' — proceed to normal login (session build, etc.)
            // ለደህንነት ሴሽን ማደስ
                session_regenerate_id(true);

                $_SESSION['user'] = [
                    'id'                 => $user['user_id'],
                    'first_name'         => $user['first_name'],
                    'father_name'        => $user['father_name'],
                    'grand_father_name'  => $user['grand_father_name'],
                    'branch_id'          => $user['branch_id'] ?? null,
                    'organization_id'    => $user['organization_id'] ?? null,
                    'email'              => $user['email'],
                    'role'               => $user['role'],
                    'user_uuid'      => $user['id'],
                ];
// 2. Safely resolve Branch details if a branch assignment exists
   if (!empty($user['branch_id'])) {
    $branchModel = new Branch($this->db);
    $branchData  = $branchModel->getBranchById($user['branch_id']);

    $_SESSION['user']['branch_name']  = !empty($branchData['name']) ? $branchData['name'] : 'Unknown Branch';
    $_SESSION['user']['alt_name']     = !empty($branchData['alt_name']) ? $branchData['alt_name'] : null;
    $_SESSION['user']['phone_number'] = !empty($branchData['phone_number']) ? $branchData['phone_number'] : null;
    $_SESSION['user']['postal_code']  = !empty($branchData['postal_code']) ? $branchData['postal_code'] : null;
    $_SESSION['user']['logo_url']     = !empty($branchData['logo_url']) ? $branchData['logo_url'] : null;
    $_SESSION['user']['level']        = $branchData['level'] ?? null;

    // Resolve full ancestry chain + inherited ketema_astedader flag
    $ancestry = $branchModel->getAncestryChain($user['branch_id']);

    $_SESSION['user']['ketema_astedader'] = $ancestry['ketema_astedader'] ? 'on' : null;

    $_SESSION['user']['full_branch_name'] = BranchNameHelper::getFullBranchName(
        $ancestry['names'],
        $ancestry['ketema_astedader']
    );

} else {
    // Fallback defaults for system-wide/global administrators
    $_SESSION['user']['branch_name']      = 'ዋናው መስሪያ ቤት (Headquarters)';
    $_SESSION['user']['alt_name']         = null;
    $_SESSION['user']['phone_number']     = null;
    $_SESSION['user']['postal_code']      = null;
    $_SESSION['user']['logo_url']         = null;
    $_SESSION['user']['level']            = 0;
    $_SESSION['user']['ketema_astedader'] = null;
    $_SESSION['user']['full_branch_name'] = 'ዋናው መስሪያ ቤት (Headquarters)';
}
// Log successful login
                \App\Helpers\AuditHelper::logAs($user['id'], 'login_success', 'auth', $user['id']);

                header("Location:" . $_ENV['BASE_URL'] . "/dashboard");
                exit();
            } else {
                // Log failed login attempt
                \App\Helpers\AuditHelper::logAs(null, 'login_failed', 'auth', null);

                $_SESSION['error'] = "ኢሜይል ወይም ፓስወርድ አልተዛመደም። እባክዎ እንደገና ይሞክሩ!";
                header("Location: " . $_ENV['BASE_URL'] . "/login");
                exit();
            }
        }
    }
 /**
     * GET /password-change — ፎርሙን ማሳየት
     */
    public function showPasswordChange() {
        // pending_user_id ካልተገኘ ወደ login መልስ (ገጹ በቀጥታ URL ተደርሶበት ከሆነ)
        if (empty($_SESSION['pending_user_id'])) {
          $this->render('auth/login', [
            'title' => "JCIMS - Login"
        ]);
        }

        $this->render('auth/password-change', [
            'title' => "JCIMS - Login"
        ]);
    }
    /**
     * ተጠቃሚው መግባቱን ማረጋገጫ (Middleware)
     */

    public function handlePasswordChange() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: " . $_ENV['BASE_URL'] . "/login");
        exit();
    }

    if (empty($_SESSION['pending_user_id'])) {
        header("Location: " . $_ENV['BASE_URL'] . "/login");
        exit();
    }

    $userUuid        = $_SESSION['pending_user_id'];
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword     = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
$userModel = new User($this->db);
$userRow = $userModel->findById($userUuid);

if (!$userRow) {
    $_SESSION['error'] = "ተጠቃሚ አልተገኘም!"; // user not found
    header("Location: " . $_ENV['BASE_URL'] . "/password-change");
    exit();
}

$intUserId = $userRow['user_id'];

// --- Verify the current (default) password is actually correct ---
if (empty($currentPassword) || !$userModel->verifyPassword($intUserId, $currentPassword)) {
    $_SESSION['error'] = "የአሁኑ ፓስወርድ ትክክል አይደለም!";
    header("Location: " . $_ENV['BASE_URL'] . "/password-change");
    exit();
}

    // --- Validation ---
    if (empty($newPassword) || empty($confirmPassword)) {
        $_SESSION['error'] = "እባክዎ ሁሉንም መስኮች ይሙሉ!";
        header("Location: " . $_ENV['BASE_URL'] . "/password-change");
        exit();
    }

    if ($newPassword !== $confirmPassword) {
        $_SESSION['error'] = "ፓስወርዶቹ አይመሳሰሉም። እባክዎ እንደገና ይሞክሩ!";
        header("Location: " . $_ENV['BASE_URL'] . "/password-change");
        exit();
    }

    if (strlen($newPassword) < 8) {
    $_SESSION['error'] = "ፓስወርዱ ቢያንስ 8 ፊደላት/ቁጥሮች ሊኖረው ይገባል!";
    header("Location: " . $_ENV['BASE_URL'] . "/password-change");
    exit();
}

if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z\d]).+$/', $newPassword)) {
    $_SESSION['error'] = "ፓስወርዱ ትልቅ ፊደል፣ ትንሽ ፊደል፣ ቁጥር እና ልዩ ምልክት (ለምሳሌ !@#$%) ማካተት አለበት!";
    header("Location: " . $_ENV['BASE_URL'] . "/password-change");
    exit();
}

if ($newPassword === $currentPassword) {
    $_SESSION['error'] = "አዲሱ ፓስወርድ ከነባሩ የተለየ መሆን አለበት!";
    header("Location: " . $_ENV['BASE_URL'] . "/password-change");
    exit();
}

    // --- Update ---
     $hash = password_hash($newPassword, PASSWORD_DEFAULT);
    $result = $userModel->completeForcedPasswordChange($userUuid, $hash, $newPassword);

    if (!$result) {
        $_SESSION['error'] = "ፓስወርድ ማዘመን አልተሳካም። እባክዎ እንደገና ይሞክሩ!";
        header("Location: " . $_ENV['BASE_URL'] . "/password-change");
        exit();
    }

    unset($_SESSION['pending_user_id']);
    \App\Helpers\AuditHelper::logAs($userUuid, 'forced_password_change_success', 'auth', $userUuid);

    $_SESSION['success'] = "ፓስወርድዎ በተሳካ ሁኔታ ተቀይሯል። እባክዎ በአዲሱ ፓስወርድ ይግቡ!";
    header("Location: " . $_ENV['BASE_URL'] . "/login");
    exit();
}
 public static function checkAuth() {
    // ተጠቃሚው Login ካላደረገ
    if (!isset($_SESSION['user'])) {
        
        // ጥያቄው የመጣው በ Fetch/AJAX መሆኑን ማረጋገጥ
        // ይህ የሚታወቀው በ Header ውስጥ application/json ሲኖር ነው
        if (isset($_SERVER['HTTP_ACCEPT']) 
    && strpos($_SERVER['SERVER_PROTOCOL'], 'HTTP') !== false 
    && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {

    header('Content-Type: application/json');
    http_response_code(401); // Unauthorized status code
    echo json_encode([
        'status' => 'error', 
        'message' => 'session_expired',
        'redirect' => rtrim($_ENV['BASE_URL'], '/') . '/login'
    ]);
    exit();
}

        // ለተለመደ የገጽ ጥያቄ (Direct Page Access)
        header("Location: " . $_ENV['BASE_URL'] . "/login");
        exit();
    }
}
    /**
     * መውጫ (Logout)
     */
    public function logout() {
        $userId = $_SESSION['user']['id'] ?? null;
        
        // Log logout before destroying session
        if ($userId) {
            \App\Helpers\AuditHelper::logAs($userId, 'logout', 'auth', $userId);
        }
        
        session_destroy();
        header("Location:  " . $_ENV['BASE_URL'] . "/login");
        exit();
    }
}