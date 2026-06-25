<?php
namespace App\Controllers;
use DateTime;
use App\Models\EmployeeRegistration;
use App\Models\User;
use App\Models\Position;
use App\Models\EmployeeGuarantor;
use App\Helpers\AuthHelper;
use Ramsey\Uuid\Uuid;
use \App\Traits\FileUploadTrait;
class EmployeeRegistrationController extends BaseController {
    use FileUploadTrait;
    public function showForm() {
         AuthHelper::checkRole(['team_leader', 'officer']);
        $user = $_SESSION['user'] ?? [];
        $branchId = $user['branch_id'] ?? null;
        $organizationId = $user['organization_id'] ?? null;

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

        $data = [
            'title' => 'JCIMS - የሰራተኛ መመዝገቢያ',
            'user'  => $user,
            'jobs'  => $jobs,
            'employees' => $employees,
        ];

        $this->render('employee-active', $data);
    }


    public function showEditForm($params = []){
   
           AuthHelper::checkRole(['team_leader', 'officer']);
         $uuid = $params['uuid'] ?? ($_GET['uuid'] ?? null);

    // Get source from URL segment (record_id holds it)
    $source = $params['record_id'] ?? 'employee-registration';
    $allowedSources = ['employee-registration', 'employee-active'];
    if (!in_array($source, $allowedSources)) {
        $source = 'employee-registration';
    }

    $redirectUrl = rtrim($_ENV['BASE_URL'], '/') . '/' . $source;
        if (!$uuid) {
           header("Location: " . $redirectUrl);
        exit();
            exit();
        }

        $user = $_SESSION['user'] ?? [];
        $branchId = $user['branch_id'] ?? null;
        $organizationId = $user['organization_id'] ?? null;

        if (!$organizationId || !$branchId) {
            $_SESSION['error'] = 'የሰራተኛውን የድርጅት እና የቅርንጫፍ መረጃ ከስር ያስገቡ።';
           header("Location: " . $redirectUrl);
        exit();
            exit();
        }

        $employeeModel = new EmployeeRegistration($this->db);
        $employee = $employeeModel->getEmployeeByUuid($uuid);

        if (!$employee) {
            $_SESSION['error'] = 'ሰራተኛ አልተገኘም።';
          header("Location: " . $redirectUrl);
        exit();
            exit();
        }
        $positionModel = new \App\Models\Position($this->db);
        // Get available jobs (active + current job)
        $availableJobs = $positionModel->getActiveJobsByBranch($branchId, $employee['job_property_id']);

        $guarantorModel = new EmployeeGuarantor($this->db);
    $guarantor      = $guarantorModel->getByEmployeeId($uuid); // null if no guarantor

          $data = [
        'title'         => 'JCIMS - የሰራተኛ ማስተካከያ',
        'user'          => $user,
        'employee'      => $employee,
        'availableJobs' => $availableJobs,
        'params'        => $params,   // ← add this
        'source'        => $source,   // ← add this for convenience,
        'guarantor'     => $guarantor, // ← add guarantor data
    ];

        $this->render('employee-edit', $data);
    }

public function handleEdit($params = []) {
    AuthHelper::checkRole(['team_leader', 'officer']);

    $source = $_POST['source'] ?? '';
    $uuid   = $_POST['uuid'] ?? null;

    $allowedSources = ['employee-registration', 'employee-active'];
    if (!in_array($source, $allowedSources)) {
        $source = 'employee-registration';
    }

    $redirectUrl = rtrim($_ENV['BASE_URL'], '/') . '/' . $source;

    if (!$uuid) {
        header("Location: " . $redirectUrl);
        exit();
    }

    $user           = $_SESSION['user'] ?? [];
    $organizationId = $user['organization_id'] ?? null;
    $branchId       = $user['branch_id'] ?? null;

    if (!$organizationId || !$branchId || empty($user['id'])) {
        $_SESSION['error'] = 'የሰራተኛውን የድርጅት እና የቅርንጫፍ መረጃ ከስር ያስገቡ።';
        header("Location: " . $redirectUrl);
        exit();
    }

    $validationErrors = $this->validateEmployeeData($_POST);
    if (!empty($validationErrors)) {
        $_SESSION['error'] = implode('<br>', $validationErrors);
        header("Location: " . $redirectUrl);
        exit();
    }

    $employeeModel   = new EmployeeRegistration($this->db);
    $currentEmployee = $employeeModel->getEmployeeByUuid($uuid);

    if (!$currentEmployee) {
        $_SESSION['error'] = 'ሰራተኛ አልተገኘም።';
        header("Location: " . $redirectUrl);
        exit();
    }

    $oldJobId   = $currentEmployee['job_property_id'];
    $newJobId   = trim($_POST['job_property_id'] ?? '');
    $employeeId = trim($currentEmployee['employee_id']);

    // ── Handle employee_image and file201 uploads (optional on edit) ─────────
    $imageName   = $this->uploadFile('employee_image', 'images')   ?? $currentEmployee['employee_image'];
    $file201Name = $this->uploadFile('employee_file201', 'documents') ?? $currentEmployee['employee_file201'];

    try {
        $positionModel = new Position($this->db);
        $position      = $positionModel->getPositionById($newJobId);

        if (!$position) {
            $_SESSION['error'] = "የተመረጠው የስራ መደብ ሊገኝ አልቻለም።";
            header("Location: " . $redirectUrl);
            exit();
        }

        if (empty(trim($position['job_identifier_no'] ?? ''))) {
            $_SESSION['error'] = "የስራ መደቡ መለያ ቁጥር አልተገኘም። እባክዎ መደቡን ያረጋግጡ።";
            header("Location: " . $redirectUrl);
            exit();
        }

        // ── Guarantor handling ────────────────────────────────────────────────
        $guarantorModel    = new EmployeeGuarantor($this->db);
        $existingGuarantor = $guarantorModel->getByEmployeeId($uuid);
        $guarantorData     = null;

        if ($position['wastna'] === 'ተያዥ የሚያስፈልገዉ') {
            $guarantorName  = trim($_POST['guarantor_name'] ?? '');
            $guarantorPhone = trim($_POST['guarantor_phone'] ?? '');

            if (empty($guarantorName) || empty($guarantorPhone)) {
                $_SESSION['error'] = "የተያዥ ሙሉ ስም እና ስልክ ቁጥር ያስገቡ።";
                header("Location: " . $redirectUrl);
                exit();
            }
// ── ADD: phone format check ───────────────────────────────────────
if (!preg_match('/^[0-9]{10}$/', $guarantorPhone)) {
    $_SESSION['error'] = 'የተያዥ ስልክ ቁጥር ትክክለኛ 10 አሃዝ መሆን አለበት።';
    header("Location: " . $redirectUrl);
    exit();
}
            $newFileUploaded = !empty($_FILES['guarantor_letter']['name']) &&
                               $_FILES['guarantor_letter']['error'] !== UPLOAD_ERR_NO_FILE;

            if ($newFileUploaded) {
                // New file uploaded — replace old one
                $guarantorLetter = $this->uploadFile('guarantor_letter', 'documents');

                if (!$guarantorLetter) {
                    $errorCode = $_FILES['guarantor_letter']['error'] ?? UPLOAD_ERR_NO_FILE;
                    $_SESSION['error'] = 'የተያዥ ፋይል ሊያያዝ አልቻለም። ' . $this->getUploadErrorMessage($errorCode);
                    header("Location: " . $redirectUrl);
                    exit();
                }

                // Delete old file from disk
                if (!empty($existingGuarantor['guarantor_letter'])) {
                    $oldPath = dirname(__DIR__, 2) . '/storage/uploads/documents/' . $existingGuarantor['guarantor_letter'];
                    if (file_exists($oldPath)) unlink($oldPath);
                }

            } elseif (!empty($existingGuarantor['guarantor_letter'])) {
                // No new file — keep existing
                $guarantorLetter = $existingGuarantor['guarantor_letter'];

            } else {
                // No new file and no existing file — required
                $_SESSION['error'] = 'እባክዎ የዋስትና ደብዳቤ ያስገቡ።';
                header("Location: " . $redirectUrl);
                exit();
            }

            $guarantorData = [
                'id'              => $existingGuarantor['id'] ?? Uuid::uuid4()->toString(),
                'employee_id'     => $uuid,
                'guarantor_name'  => $guarantorName,
                'guarantor_phone' => $guarantorPhone,
                'guarantor_letter'=> $guarantorLetter,
            ];

        } else {
            // Job changed to non-guarantor — soft delete and remove file
            if ($existingGuarantor) {
                if (!empty($existingGuarantor['guarantor_letter'])) {
                    $oldPath = dirname(__DIR__, 2) . '/storage/uploads/documents/' . $existingGuarantor['guarantor_letter'];
                    if (file_exists($oldPath)) unlink($oldPath);
                }
                $guarantorModel->softDeleteByEmployeeId($uuid);
            }
        }

        // ── Build $data ───────────────────────────────────────────────────────
        $data = [
            'employee_id'           => ($oldJobId != $newJobId)
                                            ? trim($position['job_identifier_no'])
                                            : $employeeId,
            'pension_number'        => trim($_POST['pension_number'] ?? null) ?: null,
            'first_name'            => trim($_POST['first_name'] ?? ''),
            'father_name'           => trim($_POST['father_name'] ?? ''),
            'g_father_name'         => trim($_POST['g_father_name'] ?? ''),
            'mother_name'           => trim($_POST['mother_name'] ?? ''),
            'sex'                   => $_POST['sex'] ?? 'Male',
            'birth_date'            => trim($_POST['birth_date'] ?? null) ?: null,
            'phone_number'          => trim($_POST['phone_number'] ?? null) ?: null,
            'yegabcha_huneta'       => trim($_POST['yegabcha_huneta'] ?? ''),
            'job_property_id'       => $newJobId,
            'date_of_employed'      => trim($_POST['date_of_employed'] ?? null) ?: null,
            'level_of_education'    => trim($_POST['level_of_education'] ?? ''),
            'department'            => trim($_POST['department'] ?? null) ?: null,
            'employment_situation'  => trim($_POST['employment_situation'] ?? ''),
            'immidate_boss'         => trim($_POST['immidate_boss'] ?? null) ?: null,
            'experience'            => trim($_POST['experience'] ?? null) ?: null,
            'annual_rest'           => isset($_POST['annual_rest']) ? (int) $_POST['annual_rest'] : 0,
            'displin_situation'     => trim($_POST['displin_situation'] ?? ''),
            'competency_situation'  => trim($_POST['competency_situation'] ?? null) ?: null,
            'effeciency'            => $this->normalizeDecimal($_POST['effeciency'] ?? null),
            'level_of_effeciency'   => trim($_POST['level_of_effeciency'] ?? null) ?: null,
            'no_of_files_in_folder' => isset($_POST['no_of_files_in_folder']) ? (int) $_POST['no_of_files_in_folder'] : 0,
            'employee_image'        => $imageName,
            'employee_file201'      => $file201Name,
            'remark'                => trim($_POST['remark'] ?? null) ?: null,
        ];

        // ── Persist ───────────────────────────────────────────────────────────
    if ($employeeModel->updateEmployee($uuid, $data, $guarantorData)) {
    // ── Already there ─────────────────────────────────────────────
    if ($oldJobId != $newJobId) {
        $employeeModel->assignJob($newJobId, $branchId, (string) $oldJobId);

        \App\Helpers\AuditHelper::log(
            action:     'employee_job_changed',
            entityType: 'employee',
            entityId:   $uuid,
            oldValues:  null,
            newValues:  [
                'old_job_id'  => $oldJobId,
                'new_job_id'  => $newJobId,
                'employee_id' => $employeeId,
                'changed_by'  => $user['id'],
            ],
            metadata: ['change_type' => 'job_assignment']
        );
    }

    // ── ADD: Guarantor audit ──────────────────────────────────────
    if ($guarantorData) {
        $action = $existingGuarantor ? 'guarantor_updated' : 'guarantor_added';
        \App\Helpers\AuditHelper::log(
            action:     $action,
            entityType: 'employee',
            entityId:   $uuid,
            oldValues:  $existingGuarantor ? [
                'name'  => $existingGuarantor['guarantor_name'],
                'phone' => $existingGuarantor['guarantor_phone'],
            ] : null,
            newValues:  [
                'name'  => $guarantorData['guarantor_name'],
                'phone' => $guarantorData['guarantor_phone'],
            ],
            metadata: ['changed_by' => $user['id']]
        );
    } elseif ($existingGuarantor && $position['wastna'] !== 'ተያዥ የሚያስፈልገዉ') {
        \App\Helpers\AuditHelper::log(
            action:     'guarantor_removed',
            entityType: 'employee',
            entityId:   $uuid,
            oldValues:  ['name' => $existingGuarantor['guarantor_name']],
            newValues:  null,
            metadata:   ['reason' => 'job_no_longer_requires_guarantor', 'changed_by' => $user['id']]
        );
    }

    // ── ADD: File replacement audit ───────────────────────────────
    if ($imageName !== $currentEmployee['employee_image']) {
        \App\Helpers\AuditHelper::log(
            action:     'employee_image_replaced',
            entityType: 'employee',
            entityId:   $uuid,
            oldValues:  ['file' => $currentEmployee['employee_image']],
            newValues:  ['file' => $imageName],
            metadata:   ['changed_by' => $user['id']]
        );
    }

    if ($file201Name !== $currentEmployee['employee_file201']) {
        \App\Helpers\AuditHelper::log(
            action:     'employee_file201_replaced',
            entityType: 'employee',
            entityId:   $uuid,
            oldValues:  ['file' => $currentEmployee['employee_file201']],
            newValues:  ['file' => $file201Name],
            metadata:   ['changed_by' => $user['id']]
        );
    }

    $_SESSION['success'] = 'ሰራተኛው መረጃ በትክክል ተስተካከለ።';

} else {
    $_SESSION['error'] = 'የሰራተኛ ማስተካከያ ሂደት አልተሳካም።';
} 
    } catch (\PDOException $e) {
        error_log("Employee Edit Error: " . $e->getMessage());
        $_SESSION['error'] = "ማስተካከያው አልተሳካም። እንደገና ይሞክሩ።";
    }

    header("Location: " . $redirectUrl);
    exit();
}
    public function handleRegistration() {
    AuthHelper::checkRole(['team_leader', 'officer']);

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/employee-registration");
        exit();
    }

    $user           = $_SESSION['user'] ?? [];
    $organizationId = $user['organization_id'] ?? null;
    $branchId       = $user['branch_id'] ?? null;

    if (!$organizationId || !$branchId || empty($user['id'])) {
        $_SESSION['error'] = 'የሰራተኛውን የድርጅት እና የቅርንጫፍ መረጃ ከስር ያስገቡ።';
        header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/employee-registration");
        exit();
    }

    // ── 1. Server-side validation ────────────────────────────────────────────
    $validationErrors = $this->validateEmployeeData($_POST);
    if (!empty($validationErrors)) {
        $_SESSION['error'] = implode('<br>', $validationErrors);
        header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/employee-registration");
        exit();
    }

    // ── 2. Required file presence checks ────────────────────────────────────
    if (empty($_FILES['employee_image']['name']) || $_FILES['employee_image']['error'] === UPLOAD_ERR_NO_FILE) {
        $_SESSION['error'] = 'እባክዎ የሰራተኛ ፎቶ ይምረጡ።';
        header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/employee-registration");
        exit();
    }

    if (empty($_FILES['employee_file201']['name']) || $_FILES['employee_file201']['error'] === UPLOAD_ERR_NO_FILE) {
        $_SESSION['error'] = 'እባክዎ የሰራተኛ የ201 ፋይል ይምረጡ።';
        header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/employee-registration");
        exit();
    }

    // ── 3. Upload required files ─────────────────────────────────────────────
    $imageName   = $this->uploadFile('employee_image', 'images');
    $file201Name = $this->uploadFile('employee_file201', 'documents');

    if (!$imageName || !$file201Name) {
        $messages = [];

        if (!$imageName) {
            $messages[] = 'Photo: ' . $this->getUploadErrorMessage($_FILES['employee_image']['error'] ?? UPLOAD_ERR_NO_FILE);
        }
        if (!$file201Name) {
            $messages[] = 'File201: ' . $this->getUploadErrorMessage($_FILES['employee_file201']['error'] ?? UPLOAD_ERR_NO_FILE);
        }

        $_SESSION['error'] = 'ፋይል ሊያያዝ አልቻለም። ' . implode(' | ', $messages);
        header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/employee-registration");
        exit();
    }

    // ── 4. Position fetch + guarantor handling ───────────────────────────────
    try {
        $positionModel = new Position($this->db);
        $position      = $positionModel->getPositionById(trim($_POST['job_property_id'] ?? ''));

        if (!$position) {
            $_SESSION['error'] = "የተመረጠው የስራ መደብ ሊገኝ አልቻለም።";
            header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/employee-registration");
            exit();
        }

        if (empty(trim($position['job_identifier_no'] ?? ''))) {
            $_SESSION['error'] = "የስራ መደቡ መለያ ቁጥር አልተገኘም። እባክዎ መደቡን ያረጋግጡ።";
            header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/employee-registration");
            exit();
        }

        $guarantorName   = null;
        $guarantorPhone  = null;
        $guarantorLetter = null;

        if ($position['wastna'] === 'ተያዥ የሚያስፈልገዉ') {
            $guarantorName  = trim($_POST['guarantor_name'] ?? '');
            $guarantorPhone = trim($_POST['guarantor_phone'] ?? '');

            if (empty($guarantorName) || empty($guarantorPhone)) {
                $_SESSION['error'] = "የተያዥ ሙሉ ስም እና ስልክ ቁጥር ያስገቡ።";
                header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/employee-registration");
                exit();
            }
                        // ── ADD: phone format check ───────────────────────────────────────
if (!preg_match('/^[0-9]{10}$/', $guarantorPhone)) {
    $_SESSION['error'] = 'የተያዥ ስልክ ቁጥር ትክክለኛ 10 አሃዝ መሆን አለበት።';
    header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/employee-registration");
    exit();
}

            if (empty($_FILES['guarantor_letter']['name']) || $_FILES['guarantor_letter']['error'] === UPLOAD_ERR_NO_FILE) {
                $_SESSION['error'] = 'እባክዎ የሰራተኛ ተያዥ ፋይል ይምረጡ።';
                header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/employee-registration");
                exit();
            }

            $guarantorLetter = $this->uploadFile('guarantor_letter', 'documents');

            if (!$guarantorLetter) {
                $errorCode = $_FILES['guarantor_letter']['error'] ?? UPLOAD_ERR_NO_FILE;
                $_SESSION['error'] = 'የተያዥ ፋይል ሊያያዝ አልቻለም። ' . $this->getUploadErrorMessage($errorCode);
                header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/employee-registration");
                exit();
            }

        }

        // ── 5. Build data arrays ─────────────────────────────────────────────
        $employeeUuid = Uuid::uuid7()->toString();

        $data = [
            'uuid'                  => $employeeUuid,
            'employee_id'           => trim($position['job_identifier_no']),
            'pension_number'        => trim($_POST['pension_number'] ?? null) ?: null,
            'first_name'            => trim($_POST['first_name'] ?? ''),
            'father_name'           => trim($_POST['father_name'] ?? ''),
            'g_father_name'         => trim($_POST['g_father_name'] ?? ''),
            'mother_name'           => trim($_POST['mother_name'] ?? ''),
            'sex'                   => $_POST['sex'] ?? 'Male',
            'birth_date'            => trim($_POST['birth_date'] ?? null) ?: null,
            'phone_number'          => trim($_POST['phone_number'] ?? null) ?: null,
            'yegabcha_huneta'       => trim($_POST['yegabcha_huneta'] ?? ''),
            'organization_id'       => $organizationId,
            'branch_id'             => $branchId,
            'job_property_id'       => trim($_POST['job_property_id'] ?? ''),
            'date_of_employed'      => trim($_POST['date_of_employed'] ?? null) ?: null,
            'level_of_education'    => trim($_POST['level_of_education'] ?? ''),
            'department'            => trim($_POST['department'] ?? null) ?: null,
            'employment_situation'  => trim($_POST['employment_situation'] ?? ''),
            'immidate_boss'         => trim($_POST['immidate_boss'] ?? null) ?: null,
            'experience'            => trim($_POST['experience'] ?? null) ?: null,
            'annual_rest'           => isset($_POST['annual_rest']) ? (int) $_POST['annual_rest'] : 0,
            'displin_situation'     => trim($_POST['displin_situation'] ?? ''),
            'competency_situation'  => trim($_POST['competency_situation'] ?? null) ?: null,
            'effeciency'            => $this->normalizeDecimal($_POST['effeciency'] ?? null),
            'level_of_effeciency'   => trim($_POST['level_of_effeciency'] ?? null) ?: null,
            'no_of_files_in_folder' => isset($_POST['no_of_files_in_folder']) ? (int) $_POST['no_of_files_in_folder'] : 0,
            'employee_image'        => $imageName,
            'employee_file201'      => $file201Name,
            'remark'                => trim($_POST['remark'] ?? null) ?: null,
            'reg_by'                => $user['id'],
        ];

        $guarantorData = null;
        if ($position['wastna'] === 'ተያዥ የሚያስፈልገዉ') {
            $guarantorData = [
                'id'              => Uuid::uuid4()->toString(),
                'employee_id'     => $employeeUuid,
                'guarantor_name'  => $guarantorName,
                'guarantor_phone' => $guarantorPhone,
                'guarantor_letter'=> $guarantorLetter,
            ];
        }

        // ── 6. Persist ───────────────────────────────────────────────────────
        $employeeModel = new EmployeeRegistration($this->db);

        if ($employeeModel->createEmployee($data, $guarantorData)) {
            \App\Helpers\AuditHelper::log('employee_registered', 'employee', $employeeUuid, null, [
                'employee_id'     => $data['employee_id'],
                'first_name'      => $data['first_name'],
                'father_name'     => $data['father_name'],
                'g_father_name'   => $data['g_father_name'],
                'job_property_id' => $data['job_property_id'],
                'organization_id' => $data['organization_id'],
                'branch_id'       => $data['branch_id'],
            ]);

            $_SESSION['success'] = 'ሰራተኛው መረጃ በትክክል ተመዝግቧል።';
        } else {
            $this->cleanupFiles($imageName, $file201Name, $guarantorLetter);
            $_SESSION['error'] = 'የሰራተኛ መመዝገቢያ ሂደት አልተሳካም።';
        }

    } catch (\PDOException $e) {
        $this->cleanupFiles($imageName, $file201Name, $guarantorLetter ?? null);
        error_log("Employee Registration Error: " . $e->getMessage());
        $_SESSION['error'] = "ምዝገባው አልተሳካም። እንደገና ይሞክሩ።";
    }

    header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/employee-registration");
    exit();
}

// ── Helper: delete uploaded files on failure ─────────────────────────────────
private function cleanupFiles(?string $imageName, ?string $file201Name, ?string $guarantorLetter): void {
    $map = [
        'images'    => $imageName,
        'documents' => $file201Name,
        'documents' => $guarantorLetter,
    ];

    foreach ($map as $folder => $fileName) {
        if (!empty($fileName)) {
            $path = dirname(__DIR__, 2) . '/storage/uploads/' . $folder . '/' . $fileName;
            if (file_exists($path)) {
                unlink($path);
            }
        }
    }
}

    private function normalizeDecimal($value): ?string {
        if ($value === null || trim($value) === '') {
            return null;
        }

        $normalized = str_replace([',', ' '], ['.', ''], trim($value));
        return is_numeric($normalized) ? $normalized : null;
    }


    private function validateEmployeeData(array $data): array {
        $errors = [];

        // Required field validations
        $requiredFields = [
            'first_name' => 'ስም',
            'father_name' => 'የአባት ስም',
            'g_father_name' => 'የአያት ስም',
            'mother_name' => 'የእናት ሙሉ ስም',
            'sex' => 'ጾታ',
            'birth_date' => 'የትውልድ ቀን',
            'eth_birth_date'=> 'የትውልድ ቀን',
            'phone_number' => 'ስልክ ቁጥር',
            'yegabcha_huneta' => 'የጋብቻ ሁኔታ',
            'job_property_id' => 'የስራ መደብ',
            'level_of_education' => 'የትምህርት ደረጃ',
            'employment_situation' => 'የቅጥር ሁኔታ',
            'immidate_boss' => 'የቅርብ ተጠሪ',
            'displin_situation' => 'የዲሲፕሊን ሁኔታ',
            'date_of_employed' => 'የቅጥር ቀን',
            'eth_date_of_employed' => 'የቅጥር ቀን',
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
            'g_father_name' => ['min' => 2, 'max' => 50, 'label' => 'የአያት ስም'],
            'mother_name' => ['min' => 2, 'max' => 100, 'label' => 'የእናት ሙሉ ስም'],
            'yegabcha_huneta' => ['min' => 2, 'max' => 50, 'label' => 'የጋብቻ ሁኔታ'],
            'level_of_education' => ['min' => 2, 'max' => 100, 'label' => 'የትምህርት ደረጃ'],
            'employment_situation' => ['min' => 2, 'max' => 100, 'label' => 'Employment Situation'],
            'displin_situation' => ['min' => 2, 'max' => 100, 'label' => 'የዲሲፕሊን ሁኔታ']
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

        // Date validations
        if (!empty($data['birth_date'])) {
            if (!strtotime($data['birth_date'])) {
                $errors[] = "የትውልድ ቀን ትክክለኛ ቀን መሆን አለበት።";
            } else {
                $birthDate = new DateTime($data['birth_date']);
                $today = new DateTime();
                $age = $today->diff($birthDate)->y;
                if ($age < 18 || $age > 65) {
                    $errors[] = "የሰራተኛ እድሜ ከ18 እስከ 65 አመት መሆን አለበት።";
                }
            }
        }

      if (!empty($data['date_of_employed']) && !empty($data['birth_date'])) {
    if (!strtotime($data['date_of_employed'])) {
        $errors[] = "የቅጥር ቀን ትክክለኛ ቀን መሆን አለበት።";
    } elseif (strtotime($data['date_of_employed']) > strtotime('today')) {
        $errors[] = "የቅጥር ቀን ወደፊት ሊሆን አይችልም።";
    } elseif (strtotime($data['date_of_employed']) <= strtotime($data['birth_date'])) {
        $errors[] = "የቅጥር ቀን ከልደት ቀን በኋላ መሆን አለበት።";
    } elseif (strtotime($data['date_of_employed']) < strtotime('+18 years', strtotime($data['birth_date']))) {
        $errors[] = "ሰራተኛው ሲቀጠር ቢያንስ 18 ዓመት መሆን አለበት።";
    }
}

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

        return $errors;
    }
    public function employeeDetails($params = []) {
           AuthHelper::checkRole(['team_leader', 'officer']);
        $uuid = $params['uuid'] ?? $_GET['uuid'] ?? null;
        if (!$uuid) {
            header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/employee-registration");
            exit();
        }

        $user = $_SESSION['user'] ?? [];
        $branchId = $user['branch_id'] ?? null;
        $organizationId = $user['organization_id'] ?? null;

        if (!$organizationId || !$branchId) {
            $_SESSION['error'] = 'የሰራተኛውን የድርጅት እና የቅርንጫፍ መረጃ ከስር ያስገቡ።';
            header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/employee-registration");
            exit();
        }

        $employeeModel = new EmployeeRegistration($this->db);
        $employee = $employeeModel->getEmployeeByUuid($uuid);

        if (!$employee) {
            $_SESSION['error'] = 'ሰራተኛ አልተገኘም።';
            header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/employee-registration");
            exit();
        }
$guarantorModel = new EmployeeGuarantor($this->db);
    $guarantor      = $guarantorModel->getByEmployeeId($uuid); // null if no guarantor

        $data = [
            'title' => 'JCIMS - የሰራተኛ ማስተካከያ',
            'user'  => $user,
            'employee' => $employee,
            'guarantor' => $guarantor,
        ];

        $this->render('employee-views', $data);
    }
 public function onboardingEmployees() {
    $user = $_SESSION['user'] ?? [];
    $organizationId = $user['organization_id'] ?? null;
    $branchId = $user['branch_id'] ?? null;
    AuthHelper::checkRole(['team_leader']);
    $employeeModel = new EmployeeRegistration($this->db);
    $count = $employeeModel->countOnboardingEmployees($organizationId, $branchId);

    header('Content-Type: application/json'); // <-- must be here
    echo json_encode(['count' => $count]);
    exit(); // <-- add this to stop any extra output
}
 public function listofOnboardingEmployees() {
         AuthHelper::checkRole(['team_leader', 'officer']);
        $user = $_SESSION['user'] ?? [];
        $branchId = $user['branch_id'] ?? null;
        $organizationId = $user['organization_id'] ?? null;

        $employees = [];
        if ($organizationId && $branchId) {
            $employeeModel = new EmployeeRegistration($this->db);
            $employees = $employeeModel->getOnboardingEmployees($organizationId, $branchId);
        }
$jobs = [];
        if ($branchId) {
            $positionModel = new \App\Models\Position($this->db);
            $jobs = $positionModel->getActiveJobsByBranch($branchId);
        }
        $data = [
            'title' => 'JCIMS - የሰራተኛ መመዝገቢያ',
            'user'  => $user,
            'jobs'  => $jobs,
            'employees' => $employees,
        ];

        $this->render('employee-registration', $data);
    }
public function showOnBoardingForm($params = []) {
           AuthHelper::checkRole(['team_leader', 'officer']);
        $uuid = $params['uuid'] ?? $_GET['uuid'] ?? null;
        if (!$uuid) {
            header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/login");
            exit();
        }

        $user = $_SESSION['user'] ?? [];
        $branchId = $user['branch_id'] ?? null;
        $organizationId = $user['organization_id'] ?? null;

        if (!$organizationId || !$branchId) {
            $_SESSION['error'] = 'የሰራተኛውን የድርጅት እና የቅርንጫፍ መረጃ ከስር ያስገቡ።';
            header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/login");
            exit();
        }

        $employeeModel = new EmployeeRegistration($this->db);
        $employee = $employeeModel->getEmployeeByUuid($uuid);

        if (!$employee) {
            $_SESSION['error'] = 'ሰራተኛ አልተገኘም።';
            header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/employee-registration");
            exit();
        }
 $guarantorModel = new EmployeeGuarantor($this->db);
    $guarantor      = $guarantorModel->getByEmployeeId($uuid); // null if no guarantor

        $data = [
            'title' => 'JCIMS - የሰራተኛ ማስተካከያ',
            'user'  => $user,
            'employee' => $employee,
            'guarantor' => $guarantor,
        ];

        $this->render('employee-onboarding-views', $data);
    }
    
     public function handleOnboardingApproval() {
           AuthHelper::checkRole(['team_leader']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/employee-onboarding");
            exit();
        }

        $uuid = $_POST['uuid'] ?? null;
        if (!$uuid) {
            header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/employee-onboarding");
            exit();
        }

        $user = $_SESSION['user'] ?? [];
        $userID = $user['id'] ?? null;
        $organizationId = $user['organization_id'] ?? null;
        $branchId = $user['branch_id'] ?? null;

        if (!$organizationId || !$branchId || empty($user['id'])) {
            $_SESSION['error'] = 'የሰራተኛውን የድርጅት እና የቅርንጫፍ መረጃ ከስር ያስገቡ።';
            header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/employee-registration");
            exit();
        }


        // Get current employee to check for job change
        $employeeModel = new EmployeeRegistration($this->db);
        $currentEmployee = $employeeModel->getEmployeeByUuid($uuid);

        if (!$currentEmployee) {
            $_SESSION['error'] = 'ሰራተኛ አልተገኘም።';
            header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/employee-registration");
            exit();
        }
        if ($employeeModel->approveOnBoardingEmployee($uuid, $userID)) {
            
               \App\Helpers\AuditHelper::log('employee_hiring_approved', 'employee', $uuid, null, [], ['change_type' => 'hiring_approved']);
          

            $_SESSION['success'] = 'ሰራተኛው መረጃ በትክክል ተስተካከለ።';
        } else {
            $_SESSION['error'] = 'የሰራተኛ ማስተካከያ ሂደት አልተሳካም።';
        }

        header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/employee-registration");
        exit();
    }
// ================================================================
    // STAGE 1 — Officer requests deletion
    // ================================================================
    public function requestDeletion($params = []): void
    {
        AuthHelper::checkRole(['officer', 'team_leader']);

        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true);
        $uuid   = $input['id']               ?? null;
        $reason = trim($input['reason']      ?? '');
        $source = 'INDIVIDUAL';
        $password = $input['confirm_password'] ?? '';

        // Validate input
        if (!$uuid || !$reason || !$password || !$source) {
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

        // Verify password
        $userModel = new User($this->db);
        if (!$userModel->verifyPassword($user['id'], $password)) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'ፓስዋርዱ ትክክል አይደለም።'
            ]);
            return;
        }

        $model           = new EmployeeRegistration($this->db);
        $currentEmployee = $model->getEmployeeByUuid($uuid);

        if (!$currentEmployee) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'ሰራተኛ አልተገኘም።'
            ]);
            return;
        }

        if ($currentEmployee['is_deleted'] !== 0) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'ሰራተኛው አስቀድሞ ለመሰረዝ ቀርቧል።'
            ]);
            return;
        }


        try {
            $success = $model->requestDeletion($uuid, [
                'deleted_by'       => $user['id'],
                'deletion_reason'  => $reason,
                'deletion_source'  => $source,
            ]);

            if (!$success) {
                throw new \Exception('የመሰረዝ ጥያቄ አልተሳካም።');
            }

            // Audit log — full employee data in oldValues
            $oldValues = $currentEmployee;
            unset($oldValues['password']); // never log passwords

            \App\Helpers\AuditHelper::log(
                action:     'deletion_requested',
                entityType: 'employee',
                entityId:   $uuid,
                oldValues:  $oldValues,
                newValues:  [
                    'is_deleted'      => 1,
                    'deleted_by'      => $user['id'],
                    'deletion_reason' => $reason,
                    'deletion_source' => $source,
                    'deleted_at'      => date('Y-m-d H:i:s'),
                ],
                metadata: [
                    'requested_by_name' => $user['first_name'] . ' ' . $user['grand_father_name'],
                    'requested_by_role' => $user['role'],
                    'ip_address'        => $_SERVER['REMOTE_ADDR'] ?? null,
                ]
            );


            echo json_encode([
                'status'  => 'success',
                'message' => 'የመሰረዝ ጥያቄ ለቡድን መሪ ተልኳል።'
            ]);

        } catch (\Exception $e) {
       
            echo json_encode([
                'status'  => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

function countPendingDeletions() {
    AuthHelper::checkRole(['team_leader']);
    $user = $_SESSION['user'] ?? [];
    $branchId = $user['branch_id'] ?? null;
    $employeeModel = new EmployeeRegistration($this->db);
    $count = $employeeModel->countPendingDeletions($branchId);

    header('Content-Type: application/json'); // <-- must be here
    echo json_encode(['count' => $count]);
    exit(); // <-- add this to stop any extra output
}
  // ================================================================
    // Show pending deletions page (Director)
    // ================================================================
    public function showPendingDeletions($params = []): void
    {
        AuthHelper::checkRole(['team_leader']);

        $user           = $_SESSION['user'] ?? [];
        $branchId = $user['branch_id'] ?? null;

        $model    = new EmployeeRegistration($this->db);
        $pending  = $model->getPendingDeletions($branchId);
        $approved = $model->getApprovedDeletions($branchId);
        $rejected = $model->getRejectedDeletions($branchId);

        $this->render('employee-deletion-requests', [
            'title'    => 'JCIMS - የመሰረዝ ጥያቄዎች',
            'user'     => $user,
            'pending'  => $pending,
            'approved' => $approved,
            'rejected' => $rejected,
        ]);
    }

 // ================================================================
    // STAGE 2 — Director approves deletion
    // ================================================================
    public function approveDeletion($params = []): void
    {
        AuthHelper::checkRole(['team_leader']);

        header('Content-Type: application/json');

        $input    = json_decode(file_get_contents('php://input'), true);
        $uuid     = $input['id']               ?? null;
        $password = $input['confirm_password'] ?? '';
        $reason   = trim($input['reason']      ?? '');

        if (!$uuid || !$password) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'ሁሉም መስኮች አስፈላጊ ናቸው።'
            ]);
            return;
        }

        $user = $_SESSION['user'] ?? [];

        // Verify director password
        $userModel = new User($this->db);
        if (!$userModel->verifyPassword($user['id'], $password)) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'ፓስዋርዱ ትክክል አይደለም።'
            ]);
            return;
        }

        $model           = new EmployeeRegistration($this->db);
        $currentEmployee = $model->getEmployeeByUuid($uuid);

        if (!$currentEmployee || $currentEmployee['is_deleted'] !== 1) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'ሰራተኛ አልተገኘም ወይም ጥያቄው ትክክል አይደለም።'
            ]);
            return;
        }


        try {
            $success = $model->approveDeletion($uuid, [
                'approved_by' => $user['id'],
            ]);

            if (!$success) {
                throw new \Exception('ማፅደቅ አልተሳካም።');
            }

            // Audit log
            $oldValues = $currentEmployee;
            unset($oldValues['password']);

            \App\Helpers\AuditHelper::log(
                action:     'deletion_approved',
                entityType: 'employee',
                entityId:   $uuid,
                oldValues:  $oldValues,
                newValues:  [
                    'is_deleted'             => 2,
                    'deletion_approved_by'   => $user['id'],
                    'deletion_approved_at'   => date('Y-m-d H:i:s'),
                ],
                metadata: [
                    'approved_by_name'    => $user['first_name'] . ' ' . $user['father_name'],
                    'approved_by_role'    => $user['role'],
                    'original_deleted_by' => $currentEmployee['deleted_by'],
                    'original_reason'     => $currentEmployee['deletion_reason'],
                    'ip_address'          => $_SERVER['REMOTE_ADDR'] ?? null,
                ]
            );

           
            echo json_encode([
                'status'  => 'success',
                'message' => 'ሰራተኛው በቋሚነት ተሰርዟል።'
            ]);

        } catch (\Exception $e) {
            echo json_encode([
                'status'  => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    // ================================================================
    // STAGE 3 — Director rejects deletion
    // ================================================================
    public function rejectDeletion($params = []): void
    {
        AuthHelper::checkRole(['team_leader']);

        header('Content-Type: application/json');

        $input            = json_decode(file_get_contents('php://input'), true);
        $uuid             = $input['id']               ?? null;
        $password         = $input['confirm_password'] ?? '';
        $rejectionReason  = trim($input['reason']      ?? '');

        if (!$uuid || !$password || !$rejectionReason) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'ሁሉም መስኮች አስፈላጊ ናቸው።'
            ]);
            return;
        }

        $user = $_SESSION['user'] ?? [];

        // Verify director password
        $userModel = new User($this->db);
        if (!$userModel->verifyPassword($user['id'], $password)) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'ፓስዋርዱ ትክክል አይደለም።'
            ]);
            return;
        }

        $model           = new EmployeeRegistration($this->db);
        $currentEmployee = $model->getEmployeeByUuid($uuid);

        if (!$currentEmployee || $currentEmployee['is_deleted'] !== 1) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'ሰራተኛ አልተገኘም ወይም ጥያቄው ትክክል አይደለም።'
            ]);
            return;
        }


        try {
            $success = $model->rejectDeletion($uuid, [
                'approved_by'      => $user['id'],
                'rejection_reason' => $rejectionReason,
            ]);

            if (!$success) {
                throw new \Exception('መቃወም አልተሳካም።');
            }

            // Audit log
            \App\Helpers\AuditHelper::log(
                action:     'deletion_rejected',
                entityType: 'employee',
                entityId:   $uuid,
                oldValues:  [
                    'is_deleted' => 1,
                ],
                newValues:  [
                    'is_deleted'                => 3,
                    'deletion_approved_by'      => $user['id'],
                    'deletion_approved_at'      => date('Y-m-d H:i:s'),
                    'deletion_rejection_reason' => $rejectionReason,
                ],
                metadata: [
                    'rejected_by_name'    => $user['first_name'] . ' ' . $user['father_name'],
                    'rejected_by_role'    => $user['role'],
                    'original_deleted_by' => $currentEmployee['deleted_by'],
                    'original_reason'     => $currentEmployee['deletion_reason'],
                    'ip_address'          => $_SERVER['REMOTE_ADDR'] ?? null,
                ]
            );


            echo json_encode([
                'status'  => 'success',
                'message' => 'የመሰረዝ ጥያቄው ውድቅ ተደርጓል። ሰራተኛው ወደ ስርዓቱ ተመልሷል።'
            ]);

        } catch (\Exception $e) {
            echo json_encode([
                'status'  => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }


}
