<?php
// src/Helpers/AuthHelper.php
namespace App\Helpers;
use App\Helpers\EthiopianDateHelper;

class AuthHelper {
    /**
     * Check if current user has one of the allowed roles
     *
     * @param array $allowedRoles Example: ['system_admin','org_admin']
     */
    public static function checkRole(array $allowedRoles, array $allowedLevels = []): void
    {
        $userRole  = $_SESSION['user']['role'] ?? null;
        $userLevel = $_SESSION['user']['level'] ?? null;

        $roleAllowed = in_array($userRole, $allowedRoles, true);

        // Cast to int before strict comparison — DB-sourced level values
        // may come back as strings depending on PDO fetch/emulation config,
        // which differs between environments (local vs server).
        $levelAllowed = empty($allowedLevels)
            ? true
            : in_array(
                (int) $userLevel,
                array_map('intval', $allowedLevels),
                true
            );

        if (!$roleAllowed || !$levelAllowed) {
            $isAjax = isset($_SERVER['HTTP_ACCEPT']) &&
                      strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false;

            if ($isAjax) {
                header('Content-Type: application/json');
                http_response_code(403);
                echo json_encode([
                    'status'   => 'error',
                    'message'  => 'access_denied',
                    'redirect' => rtrim($_ENV['BASE_URL'], '/') . '/login'
                ]);
                exit();
            }

            $_SESSION['error'] = 'Access Denied: You do not have permission to perform this action.';
            header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/login");
            exit();
        }
    }

    public static function checkFiscalYear(): int
    {
        $ethiopianDate = EthiopianDateHelper::toEthCalendar(
            (int) date('d'),
            (int) date('m'),
            (int) date('Y')
        );
        $ethMonth = $ethiopianDate['month'];
        $ethYear  = $ethiopianDate['year'];

        $fiscalYear = ($ethMonth >= 11)
            ? $ethYear + 1
            : $ethYear;

        return $fiscalYear;
    }

    public static function hasRole(array $allowedRoles, array $allowedLevels = []): bool
    {
        $userRole  = $_SESSION['user']['role'] ?? null;
        $userLevel = $_SESSION['user']['level'] ?? null;

        $roleAllowed = in_array($userRole, $allowedRoles, true);

        $levelAllowed = empty($allowedLevels)
            ? true
            : in_array(
                (int) $userLevel,
                array_map('intval', $allowedLevels),
                true
            );

        return $roleAllowed && $levelAllowed;
    }
}