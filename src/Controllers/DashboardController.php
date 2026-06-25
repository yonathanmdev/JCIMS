<?php
namespace App\Controllers;
use App\Models\EmployeeRegistration;
use App\Models\User;
use App\Models\Branch;
use App\Helpers\AuthHelper;

class DashboardController extends BaseController {
    
    public function index() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
 AuthHelper::checkRole(['system_admin','org_admin', 'team_leader', 'officer']);
        $branch_id = $_SESSION['user']['branch_id'] ?? null;
        
        $role = $_SESSION['user']['role'] ?? ''; 

        // Initialize variables to safe defaults
        $total_employees     = 0;
        $onleave_employees   = 0;
        $studyleave_employees = 0;
        $total_users         = 0;
        $active_users        = 0;
        $total_branches       = 0; // New dynamic counter container

        // 1. Check for Admin Roles (System Admin or Organization Admin)
        if ($role === 'system_admin' || $role === 'org_admin') {
            $userModel    = new User($this->db);
            $branchModel  = new Branch($this->db);
            $total_users   = $userModel->getTotalUsersCount($branch_id);
            $active_users = $userModel->getActiveUsersCount($branch_id);
            $total_branches = $branchModel->getTotalBranchesCount($branch_id);

        }
        
        // 2. Check for HR Roles (HR Director or HR Officer)
        if ($role === 'team_leader' || $role === 'officer') {
            $employeeModel        = new EmployeeRegistration($this->db);
            $total_employees      = $employeeModel->getAllEmployeesCount($branch_id);
            $onleave_employees    = $employeeModel->getOnleaveEmployeesCount($branch_id);
            $studyleave_employees = $employeeModel->getStudyleaveEmployeesCount($branch_id);
        }

        // Pass everything to the view package template
        $data = [
            'title'                => 'JCIMS - ዳሽቦርድ',
            'user'                 => $_SESSION['user'] ?? null,
            'role'                 => $role,
            'total_employees'      => $total_employees,
            'onleave_employees'    => $onleave_employees,
            'studyleave_employees' => $studyleave_employees,
            'total_branches'       => $total_branches,
            'total_users'         => $total_users,
            'active_users'         => $active_users
        ];

        $this->render('dashboard', $data);
    }
}