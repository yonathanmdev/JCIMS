<?php
// src/Helpers/AuthHelper.php
namespace App\Helpers;

class AuthHelper {

    /**
     * Check if current user has one of the allowed roles
     *
     * @param array $allowedRoles Example: ['system_admin','org_admin']
     */
    public static function checkRole(array $allowedRoles) {
        $userRole = $_SESSION['user']['role'] ?? null;

        if (!in_array($userRole, $allowedRoles)) {
            // Check if this is an AJAX request
            $isAjax = isset($_SERVER['HTTP_ACCEPT']) &&
                     strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false;

            if ($isAjax) {
                header('Content-Type: application/json');
                http_response_code(403); // Forbidden
                echo json_encode([
                    'status' => 'error',
                    'message' => 'access_denied',
                    'redirect' => '/JCIMS/login'
                ]);
                exit();
            } else {
                $_SESSION['error'] = "Access Denied: You do not have permission to perform this action.";
                header("Location:  " . $_ENV['BASE_URL'] . "/login");
                exit();
            }
        }
    }
}