<?php
namespace App\Controllers;
use App\Helpers\AuthHelper;
use App\Models\JobSeekerModel;
use Ramsey\Uuid\Uuid;
class JobseekerController extends BaseController {
   
    public function showRegisterForm() {
         AuthHelper::checkRole(['team_leader', 'officer']);
        $user = $_SESSION['user'] ?? [];
        $branchId = $user['branch_id'] ?? null;
        $organizationId = $user['organization_id'] ?? null;
/*
        $jobs = [];
        if ($branchId) {
            $positionModel = new \App\Models\Position($this->db);
            $jobs = $positionModel->getActiveJobsByBranch($branchId);
        }

        $employees = [];
        if ($organizationId && $branchId) {
            $employeeModel = new EmployeeRegistration($this->db);
            $employees = $employeeModel->getEmployeesByBranch($organizationId, $branchId);
        }
*/
        $data = [
            'title' => 'JCIMS - የሰራተኛ መመዝገቢያ',
        ];

        $this->render('jobseeker-registration', $data);
    }

public function handleRegistration() {
    AuthHelper::checkRole(['officer']);

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/jobseeker-registration");
        exit();
    }

    $user           = $_SESSION['user'] ?? [];
    $organizationId = $user['organization_id'] ?? null;
    $branchId       = $user['branch_id'] ?? null;

    if (!$organizationId || !$branchId || empty($user['id'])) {
        $_SESSION['error'] = 'የሰራተኛውን የድርጅት እና የቅርንጫፍ መረጃ ከስር ያስገቡ።';
        header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/jobseeker-registration");
        exit();
    }

    // ── 1. Server-side validation ────────────────────────────────────────────
    $validationErrors = $this->validateEmployeeData($_POST);
    if (!empty($validationErrors)) {
        $_SESSION['error'] = implode('<br>', $validationErrors);
        header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/jobseeker-registration");
        exit();
    }

    // ── 4. Position fetch + guarantor handling ───────────────────────────────
    try {
        // ── 5. Build data arrays ─────────────────────────────────────────────
        $jobseekerUuid = Uuid::uuid7()->toString();

        $data = [
            'uuid'                  => $jobseekerUuid,
            'first_name'            => trim($_POST['first_name'] ?? ''),
            'father_name'           => trim($_POST['father_name'] ?? ''),
            'g_father_name'         => trim($_POST['g_father_name'] ?? ''),
            'sex'                   => $_POST['sex'] ?? 'ወንድ',
            'organization_id'       => $organizationId,
            'branch_id'             => $branchId,
            'reg_by'                => $user['id'],
        ];
        // ── 6. Persist ───────────────────────────────────────────────────────
        $jobSeekerModel = new JobSeekerModel($this->db);

        if ($jobSeekerModel->createJobseeker($data)) {
            \App\Helpers\AuditHelper::log('jobseeker_registered', 'job_seekers', $jobseekerUuid, null, [
                'first_name'      => $data['first_name'],
                'father_name'     => $data['father_name'],
                'last_name'   => $data['g_father_name'],
                'organization_id' => $data['organization_id'],
                'branch_id'       => $data['branch_id'],
            ]);

            $_SESSION['success'] = 'ስራ ፈላጊ በትክክል ተመዝግቧል።';
        } else {
            $_SESSION['error'] = 'ስራ ፈላጊ መመዝገቢያ ሂደት አልተሳካም።';
        }

    } catch (\PDOException $e) {
        error_log("Employee Registration Error: " . $e->getMessage());
        $_SESSION['error'] = "ምዝገባው አልተሳካም። እንደገና ይሞክሩ።";
    }

    header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/jobseeker-registration");
    exit();
}
 private function validateEmployeeData(array $data): array {
        $errors = [];

        // Required field validations
        $requiredFields = [
            'first_name' => 'ስም',
            'father_name' => 'የአባት ስም',
            'g_father_name' => 'የአያት ስም',
            'sex' => 'ጾታ',
        ];

        foreach ($requiredFields as $field => $label) {
            if (empty(trim($data[$field] ?? ''))) {
                $errors[] = "$label አስፈላጊ ነው።";
            }
        }

        // Length validations
        $lengthValidations = [
            'first_name' => ['min' => 2, 'max' => 50, 'label' => 'ስም'],
            'father_name' => ['min' => 2, 'max' => 50, 'label' => 'የአባት ስም'],
            'g_father_name' => ['min' => 2, 'max' => 50, 'label' => 'የአያት ስም']
        ];

        foreach ($lengthValidations as $field => $config) {
            $value = trim($data[$field] ?? '');
            if (!empty($value)) {
                $length = strlen($value);
                if ($length < $config['min']) {
                    $errors[] = "{$config['label']} ቢያንስ {$config['min']}  ፊደል መሆን አለበት።";
                }
                if ($length > $config['max']) {
                    $errors[] = "{$config['label']} {$config['max']}  ፊደል ከመብለጫ ቀር መሆን አለበት።";
                }
            }
        }
         if (!empty($data['sex']) && !in_array($data['sex'], ['ወንድ', 'ሴት'])) {
            $errors[] = "ጾታ ትክክለኛ መሆን አለበት።";
        }
/*
        // Phone number validation
        if (!empty($data['phone_number'])) {
            if (!preg_match('/^[0-9]{10}$/', $data['phone_number'])) {
                $errors[] = "ስልክ ቁጥር ትክክለኛ 10 አሃዝ መሆን አለበት።";
            }
        }

        // Numeric validations
        if (isset($data['annual_rest']) && $data['annual_rest'] !== '') {
            $annualRest = (int)$data['annual_rest'];
            if ($annualRest < 0 || $annualRest > 365) {
                $errors[] = "የዓመት እረፍት ከ0 እስከ 365 መሆን አለበት።";
            }
        }

        if (isset($data['effeciency']) && $data['effeciency'] !== '') {
            $efficiency = (float)str_replace([',', ' '], ['.', ''], $data['effeciency']);
            if ($efficiency < 0 || $efficiency > 100) {
                $errors[] = "Efficiency ከ0 እስከ 100 መሆን አለበት።";
            }
        }

        if (isset($data['no_of_files_in_folder']) && $data['no_of_files_in_folder'] !== '') {
            $filesCount = (int)$data['no_of_files_in_folder'];
            if ($filesCount < 0) {
                $errors[] = "የማህደር የፋይል ብዛት 0 ወይም ከዚህ በላይ መሆን አለበት።";
            }
        }

        // Sex validation
        if (!empty($data['sex']) && !in_array($data['sex'], ['Male', 'Female'])) {
            $errors[] = "ጾታ ትክክለኛ መሆን አለበት።";
        }

        // Remark length validation
        if (!empty($data['remark']) && strlen(trim($data['remark'])) > 500) {
            $errors[] = "Remark 500  ፊደል መብለጥ የለበትም።";
        }
        if (!empty($_POST['guarantor_phone'])) {
    if (!preg_match('/^[0-9]{10}$/', trim($_POST['guarantor_phone']))) {
        $errors[] = 'የተያዥ ስልክ ቁጥር ትክክለኛ 10 አሃዝ መሆን አለበት።';
    }
}
*/
        return $errors;
    }
public function listofJobseekers() {
         AuthHelper::checkRole(['team_leader', 'officer']);
        $user = $_SESSION['user'] ?? [];
        $branchId = $user['branch_id'] ?? null;
        $organizationId = $user['organization_id'] ?? null;
 $myBranchId  = $_SESSION['user']['branch_id'];
$orgId       = $_SESSION['user']['organization_id'];
 $jobSeekerModel = new JobSeekerModel($this->db);
$jobSeekers  = $jobSeekerModel->getJobSeekersByHierarchy($myBranchId, $orgId);
        $data = [
            'jobSeekers' => $jobSeekers,
            'title' => 'JCIMS - የሰራተኛ መመዝገቢያ',
        ];

        $this->render('jobseekers-list', $data);
    }
    }