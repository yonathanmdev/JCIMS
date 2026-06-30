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
    'last_name'                         => trim($_POST['last_name'] ?? ''),
    'gender'                            => trim($_POST['gender'] ?? ''),
    'age'                               => $_POST['age'] ?? '',
    'educational_level'                 => trim($_POST['educational_level'] ?? ''),
    'educated_dpt'                      => trim($_POST['educated_dpt'] ?? ''),
    'education_trmnet_finsh_year'       => $_POST['education_trmnet_finsh_year'] ?? '',
    'g8id'                              => trim($_POST['g8id'] ?? ''),
    'CGPA'                              => $_POST['CGPA'] ?? '',
    'school_type'                       => trim($_POST['school_type'] ?? ''),
    'physical_condition'                => $_POST['physical_condition'] ?? '',
    'physical_condition_desc'           => trim($_POST['physical_condition_desc'] ?? ''),
    'kebele'                            => trim($_POST['kebele'] ?? ''),
    'mender'                            => trim($_POST['mender'] ?? ''),
    'kebele_id_no'                      => trim($_POST['kebele_id_no'] ?? ''),
    'phone_number'                      => $_POST['phone_number'] ?? '',
    'choice_sector1'                    => $_POST['choice_sector1'] ?? '',
    'sub_choose1'                       => $_POST['sub_choose1'] ?? '',
    'choice_sector2'                    => $_POST['choice_sector2'] ?? '',
    'sub_choose2'                       => $_POST['sub_choose2'] ?? '',
    'choice_sector3'                    => $_POST['choice_sector3'] ?? '',
    'sub_choose3'                       => $_POST['sub_choose3'] ?? '',
    'meteleya_huneta'                   => trim($_POST['meteleya_huneta'] ?? ''),
    'residence_status'                  => trim($_POST['residence_status'] ?? ''),
    'srafelagi_huneta'                  => trim($_POST['srafelagi_huneta'] ?? ''),
    'graguation_catagory'               => trim($_POST['graguation_catagory'] ?? ''),
    'maritalstatus'                     => trim($_POST['maritalstatus'] ?? ''),
    'noofmonth'                         => $_POST['noofmonth'] ?? '',
    'haveexp'                           => $_POST['haveexp'] ?? '',
    'workplace'                         => trim($_POST['workplace'] ?? ''),
    'experience'                        => $_POST['experience'] ?? '',
    'profession'                        => trim($_POST['profession'] ?? ''),
    'nameofcountry'                     => trim($_POST['nameofcountry'] ?? ''),
    'language'                          => trim($_POST['language'] ?? ''),
    'wageorself'                        => trim($_POST['wageorself'] ?? ''),
    'typeofemployment'                  => trim($_POST['typeofemployment'] ?? ''),
    'mothername'                        => trim($_POST['mothername'] ?? ''),
    'Labor_ID'                          => trim($_POST['Labor_ID'] ?? ''),
    'FAN'                               => $_POST['FAN'] ?? '',
    'fiscal_year' => AuthHelper::checkFiscalYear(),
    'agri_business_experience_status'   => $_POST['agri_business_experience_status'] ?? '',
    'agri_business_experience'          => $_POST['agri_business_experience'] ?? '',
    'has_dependents'                    => $_POST['has_dependents'] ?? '',
    'number_of_dependents'              => $_POST['number_of_dependents'] ?? '',
    'children_under_five'               => $_POST['children_under_five'] ?? '',
    'branch_id'             => $branchId,
    'reg_by'                => $user['id'],
    'regstration_level'                 => $user['level'] ?? '',
];
        // ── 6. Persist ───────────────────────────────────────────────────────
        $jobSeekerModel = new JobSeekerModel($this->db);

        if ($jobSeekerModel->createJobseeker($data)) {
            \App\Helpers\AuditHelper::log('jobseeker_registered', 'job_seekers', $jobseekerUuid, null, [
                'first_name'      => $data['first_name'],
                'father_name'     => $data['father_name'],
                'last_name'   => $data['last_name'],
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
            'last_name' => 'የአያት ስም',
            'age' => 'እድሜ',
            'sex' => 'ጾታ',
            'educational_level' => 'የት/ት ደረጃ',
            'physical_condition' => 'የጉዳት ሁኔታ',
            'kebele_id_no' => 'የቀበሌ መ/ቁጥር',
            'choice_sector1' => '1ኛ የዘርፍ ምርጭ',
            'sub_choose1' => '1ኛ ንዑስ ዘርፍ ምርጭ',
            'choice_sector2' => '2ኛ የዘርፍ ምርጭ',
            'sub_choose2' => '2ኛ ንዑስ ዘርፍ ምርጭ',
            'choice_sector3' => '3ኛ የዘርፍ ምርጭ',
            'sub_choose3' => '3ኛ ንዑስ ዘርፍ ምርጭ',
            'meteleya_huneta' => 'መኖሪያ ቤት ሁኔታ',
            'residence_status' => 'የሚኖሩበት አካባቢ',
            'srafelagi_huneta' => 'የስራ ፈላጊ ሁኔታ',
            'maritalstatus' => 'የጋብቻ ሁኔታ',
            'mothername' => 'የእናት ሙሉ ስም',
            


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
            'last_name' => ['min' => 2, 'max' => 50, 'label' => 'የአያት ስም'],

        ];

        foreach ($lengthValidations as $field => $config) {
            $value = trim($data[$field] ?? '');
            if (!empty($value)) {
                $length = strlen($value);
                if ($length < $config['min']) {
                    $errors[] = "{$config['label']} ቢያንስ {$config['min']}  ፊደል መሆን አለበት።";
                }
                if ($length > $config['max']) {
                    $errors[] = "{$config['label']} {$config['max']}  ፊደል መብለጥ የለበትም።";
                }
            }
        }
         if (!empty($data['sex']) && !in_array($data['sex'], ['ወንድ', 'ሴት'])) {
            $errors[] = "ጾታ ትክክለኛ መሆን አለበት።";
        }
// Validate educational level
if (!in_array($data['educational_level'] ?? '', [
    'መሰረተ ትምህርት',
    'ማንበብና መፃፍ የማይችሉ',
    'ከ1-7ኛ',
    '8ኛ ያጠናቀቀ/ች',
    'ከ9-10ኛ',
    'ከ11-12ኛ',
    'ደረጃ 2',
    'ደረጃ 3',
    'ደረጃ 4',
    'ደረጃ 5',
    'የመጀመሪያ ዲግሪ',
    'ሁለተኛ ዲግሪ'
], true)) {
    $errors[] = "ትክክለኛ የት/ት መረጃ ያስገቡ።";
}


if (
    in_array($data['educational_level'] ?? '', [
        'ከ1-7ኛ',
        '8ኛ ያጠናቀቀ/ች',
        'ከ9-10ኛ',
        'ከ11-12ኛ',
        'ደረጃ 2',
        'ደረጃ 3',
        'ደረጃ 4',
        'ደረጃ 5',
        'የመጀመሪያ ዲግሪ',
        'ሁለተኛ ዲግሪ'
    ], true)
    && empty(trim($data['education_trmnet_finsh_year'] ?? ''))
) {
    $errors[] = "ት/ት የጠናቀቁበትን ዓም ያስገቡ።";
}
// If the educational level requires a department, it must not be empty
if (
    in_array($data['educational_level'] ?? '', [
        'ደረጃ 2',
        'ደረጃ 3',
        'ደረጃ 4',
        'ደረጃ 5',
        'የመጀመሪያ ዲግሪ',
        'ሁለተኛ ዲግሪ'
    ], true)
    && empty(trim($data['school_type'] ?? ''))
) {
    $errors[] = "የተማሩበትን የተቋም ዓይነት ያስገቡ።";
}
// If the educational level requires a department, it must not be empty
if (
    in_array($data['educational_level'] ?? '', [
        'ደረጃ 2',
        'ደረጃ 3',
        'ደረጃ 4',
        'ደረጃ 5',
        'የመጀመሪያ ዲግሪ',
        'ሁለተኛ ዲግሪ'
    ], true)
    && empty($data['CGPA'] ?? '')
) {
    $errors[] = " CGPA ያስገቡ።";
}

// If the educational level requires a department, it must not be empty
if (
    in_array($data['educational_level'] ?? '', [
        '8ኛ ያጠናቀቀ/ች',
        'ከ9-10ኛ',
        'ከ11-12ኛ',
        'ደረጃ 2',
        'ደረጃ 3',
        'ደረጃ 4',
        'ደረጃ 5',
        'የመጀመሪያ ዲግሪ',
        'ሁለተኛ ዲግሪ'
    ], true)
    && empty(trim($data['g8id'] ?? ''))
) {
    $errors[] = "የ8ኛ ክፍል መለያ ያስገቡ።";
}
// If the educational level requires a department, it must not be empty
if (
    in_array($data['educational_level'] ?? '', [
        'ደረጃ 2',
        'ደረጃ 3',
        'ደረጃ 4',
        'ደረጃ 5',
        'የመጀመሪያ ዲግሪ',
        'ሁለተኛ ዲግሪ'
    ], true)
    && empty(trim($data['educated_dpt'] ?? ''))
) {
    $errors[] = "ዲፓርትመንት ያስገቡ።";
}
// If the educational level requires a department, it must not be empty
if (
    in_array($data['educational_level'] ?? '', [
        'ደረጃ 2',
        'ደረጃ 3',
        'ደረጃ 4',
        'ደረጃ 5',
        'የመጀመሪያ ዲግሪ',
        'ሁለተኛ ዲግሪ'
    ], true)
    && empty($data['phone_number'] ?? '')
) {
    $errors[] = "ስልክ ቁጥር ያስገቡ።";
}
if (
    in_array($data['educational_level'] ?? '', [
        'ደረጃ 2',
        'ደረጃ 3',
        'ደረጃ 4',
        'ደረጃ 5',
        'የመጀመሪያ ዲግሪ',
        'ሁለተኛ ዲግሪ'
    ], true)
    && empty($data['graguation_catagory'] ?? '')
) {
    $errors[] = "የተመረቁበት ሙያ ምድብ ይምረጡ";
}


// Phone number validation
        if (!empty($data['phone_number'])) {
            if (!preg_match('/^[0-9]{10}$/', $data['phone_number'])) {
                $errors[] = "ስልክ ቁጥር ትክክለኛ 10 አሃዝ መሆን አለበት።";
            }
        }
// ── CGPA validation ───────────────────────────────────────────────────
if (isset($data['CGPA']) && trim($data['CGPA']) !== '') {
    $cgpaRaw = trim($data['CGPA']);

    // Must be numeric, and match up to 2 decimal places only
    if (!preg_match('/^\d+(\.\d{1,2})?$/', $cgpaRaw)) {
        $errors[] = 'CGPA ቁጥር መሆን አለበት እና ከ2 በላይ የአስርዮሽ ቦታ መያዝ የለበትም።';
    } else {
        $cgpaValue = (float) $cgpaRaw;
        if ($cgpaValue < 2 || $cgpaValue > 4) {
            $errors[] = 'CGPA ከ2 እስከ 4 መካከል መሆን አለበት።';
        }
    }
}

// ── Physical condition validation ─────────────────────────────────────

if ($data['physical_condition'] === '' || !in_array($data['physical_condition'], ['0', '1'], true)) {
    $errors[] = 'የአካል ሁኔታ ትክክለኛ መረጃ ያስገቡ።';
} elseif ($$data['physical_condition'] === '1') {
    $physicalConditionDesc = trim($data['physical_condition_desc'] ?? '');
    if ($physicalConditionDesc === '') {
        $errors[] = 'የጉዳት አይነት ያስገቡ።';
    }
}

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