<?php
namespace App\Controllers;
use App\Models\EmployeeRegistration;
use App\Models\User;
use App\Models\Branch;
use App\Models\ReportgenerationModel; // ለስራ ፈላጊ መቆጠሪያ የመጣ
use App\Helpers\AuthHelper;

class DashboardController extends BaseController {
    
    public function index() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        AuthHelper::checkRole(['system_admin','org_admin', 'team_leader', 'officer']);
        $branch_id = $_SESSION['user']['branch_id'] ?? null;
        $role = $_SESSION['user']['role'] ?? ''; 

        // ነባሪ እሴቶች
        $total_employees      = 0;
        $onleave_employees    = 0;
        $studyleave_employees = 0;
        $total_users          = 0;
        $active_users         = 0;
        $total_branches       = 0;
        $total_job_seekers    = 0; // የስራ ፈላጊዎች መቆጠሪያ ኮንቴይነር
        $total_awareness      = 0;

        // የሪፖርት ሞዴሉን እዚህ ጋር አንድ ጊዜ እናዘጋጀዋለን (ለሁሉም ሚናዎች እንዲያገለግል)
        $reportModel = new ReportgenerationModel($this->db);

        // 1. ለአስተዳዳሪዎች (Admin Roles)
        if ($role === 'system_admin' || $role === 'org_admin') {
            $userModel      = new User($this->db);
            $branchModel    = new Branch($this->db);
            $total_users    = $userModel->getTotalUsersCount($branch_id);
            $active_users   = $userModel->getActiveUsersCount($branch_id);
            $total_branches = $branchModel->getTotalBranchesCount($branch_id);
            
            // ለአስተዳዳሪውም የስራ ፈላጊውን ቁጥር ይቆጥራል
            $total_job_seekers = $reportModel->getTotalJobSeekersCountByHierarchy($branch_id);
        }
        
        // 2. ለባለሙያዎች (HR / Operation Roles)
        if ($role === 'team_leader' || $role === 'officer') {
            $employeeModel        = new EmployeeRegistration($this->db);
            $total_employees      = $employeeModel->getAllEmployeesCount($branch_id);
            $onleave_employees    = $employeeModel->getOnleaveEmployeesCount($branch_id);
            $studyleave_employees = $employeeModel->getStudyleaveEmployeesCount($branch_id);
            
            // ለባለሙያውም የስራ ፈላጊውን ቁጥር ይቆጥራል
            $total_job_seekers    = $reportModel->getTotalJobSeekersCountByHierarchy($branch_id);
            $total_awareness = $reportModel->getTotalAwarenessCountByHierarchy($branch_id);

        }

        // ዳታውን ወደ ቪው ማስተላለፍ
        $data = [
            'title'                => 'JCIMS - ዳሽቦርድ',
            'user'                 => $_SESSION['user'] ?? null,
            'role'                 => $role,
            'total_employees'      => $total_employees,
            'onleave_employees'    => $onleave_employees,
            'studyleave_employees' => $studyleave_employees,
            'total_branches'       => $total_branches,
            'total_users'          => $total_users,
            'active_users'         => $active_users,
            'total_job_seekers'    => $total_job_seekers, // አዲሱ የቁጥር ዳታ
            'total_awareness' => $total_awareness // አዲሱ የግንዛቤ ፈጠራ ቁጥር ዳታ
        ];

        $this->render('dashboard', $data);
    }
}