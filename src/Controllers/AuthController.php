<?php
namespace App\Controllers;

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
        
      
        $_SESSION['user']['branch_name']     = !empty($branchData['name']) ? $branchData['name'] : 'Unknown Branch';
        $_SESSION['user']['alt_name'] = !empty($branchData['alt_name']) ? $branchData['alt_name'] : null;
        $_SESSION['user']['phone_number'] = !empty($branchData['phone_number']) ? $branchData['phone_number'] : null;
        $_SESSION['user']['postal_code'] = !empty($branchData['postal_code']) ? $branchData['postal_code'] : null;
        $_SESSION['user']['logo_url'] = !empty($branchData['logo_url']) ? $branchData['logo_url'] : null;
        $_SESSION['user']['level']        = $branchData['level'] ?? null;   // ← add this
        $_SESSION['user']['ketema_astedader']        = $branchData['ketema_astedader'] ?? null;   // ← add this
    
         } else {
        // Fallback defaults for system-wide/global administrators
        $_SESSION['user']['branch_name']     = 'ዋናው መስሪያ ቤት (Headquarters)';
        $_SESSION['user']['alt_name'] = null;
        $_SESSION['user']['phone_number'] = null;
        $_SESSION['user']['postal_code'] = null;
        $_SESSION['user']['logo_url'] = null; // Dashboard code falls back to icon cleanly
        $_SESSION['user']['level']        = 0;   // ← system_admin / no-branch users, treat as top-level
        $_SESSION['user']['ketema_astedader'] = null;   // ← add this
     
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
     * ተጠቃሚው መግባቱን ማረጋገጫ (Middleware)
     */
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