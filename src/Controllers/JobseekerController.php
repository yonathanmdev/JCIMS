<?php
namespace App\Controllers;
use App\Helpers\AuthHelper;
use App\Models\JobSeekerModel;
use App\Models\SectorModel;
use Ramsey\Uuid\Uuid;
use Exception;
class JobseekerController extends BaseController {
   
    public function showRegisterForm() {
         AuthHelper::checkRole(['team_leader', 'officer']);
        $user = $_SESSION['user'] ?? [];
        $branchId = $user['branch_id'] ?? null;
        $userId = $user['id'] ?? null;
$sectorModel = new SectorModel($this->db);
$sectors  = $sectorModel->getSectors();
        
        $jobSeekerModel = new JobSeekerModel($this->db);
$jobSeekers  = $jobSeekerModel->getLast24HoursCount($branchId, $userId);
        $data = [
            'title' => 'JCIMS - የሰራተኛ መመዝገቢያ',
            'sectors' => $sectors,
            'jobSeekers' => $jobSeekers
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

    if (!$branchId || empty($user['id'])) {
        $_SESSION['error'] = 'የሰራተኛውን የድርጅት እና የቅርንጫፍ መረጃ ከስር ያስገቡ።';
        header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/jobseeker-registration");
        exit();
    }

    // ── Validation ──────────────────────────────────────────────────────
$validationErrors = $this->validateJobseekerData($_POST);
if (!empty($validationErrors)) {
    $_SESSION['error'] = implode('<br>', $validationErrors);
    header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/jobseeker-registration");
    exit();
}

try {
    // ── Resolve sector/subsector UUIDs to bigint FK values ────────────
    $sectorModel = new SectorModel($this->db);

    $sectorChoicePairs = [
        1 => ['sector' => $_POST['choice_sector1'] ?? '', 'sub' => $_POST['sub_choose1'] ?? ''],
        2 => ['sector' => $_POST['choice_sector2'] ?? '', 'sub' => $_POST['sub_choose2'] ?? ''],
        3 => ['sector' => $_POST['choice_sector3'] ?? '', 'sub' => $_POST['sub_choose3'] ?? ''],
    ];

    $resolvedSectors = [];

    foreach ($sectorChoicePairs as $sectorId => $pair) {
        if ($pair['sub'] === '') {
            $resolvedSectors[$sectorId] = ['sector_id' => null, 'subsector_id' => null];
            continue;
        }

        $ids = $sectorModel->getSubsectorBigIntIds($pair['sub']);

        if (!$ids) {
            // Shouldn't happen if validation already confirmed the relationship,
            // but guard against a race condition (e.g. record deleted mid-request)
            $_SESSION['error'] = "የተመረጠው ሙያ {$sectorId} መረጃ አልተገኘም።";
            header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/jobseeker-registration");
            exit();
        }

        $resolvedSectors[$sectorId] = [
            'sector_id'    => $ids['sectorid'],
            'subsector_id' => $ids['sub_sectorid'],
        ];
    }
   
        // ── 5. Build data arrays ─────────────────────────────────────────────
        $jobseekerUuid = Uuid::uuid7()->toString();

       $data = [
    'uuid'                  => $jobseekerUuid,
    'first_name'            => trim($_POST['first_name'] ?? ''),
    'father_name'           => trim($_POST['father_name'] ?? ''),
    'last_name'                         => trim($_POST['g_father_name'] ?? ''),
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

     // ── Resolved sector/subsector bigint FKs ─────────────────────
        'choice_sector1'                  => $resolvedSectors[1]['sector_id'],
        'sub_choose1'               => $resolvedSectors[1]['subsector_id'],
        'choice_sector2'                  => $resolvedSectors[2]['sector_id'],
        'sub_choose2'               => $resolvedSectors[2]['subsector_id'],
        'choice_sector3'                  => $resolvedSectors[3]['sector_id'],
        'sub_choose3'               => $resolvedSectors[3]['subsector_id'],
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
 private function validateJobseekerData(array $data): array {
        $errors = [];

        // Required field validations
        $requiredFields = [
            'first_name' => 'ስም',
            'father_name' => 'የአባት ስም',
            'g_father_name' => 'የአያት ስም',
            'age' => 'እድሜ',
            'gender' => 'ጾታ',
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
            'noofmonth' => 'ስራ አጥ ሆነው የቆዩበት ወር',
            


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
    // ── father_name: required, no numbers or special characters ───────────
if (!preg_match('/^[\p{L}\s]+$/u', $$data['father_name'])) {
    $errors[] = "የአባት ስም ቁጥር ወይም ልዩ ምልክት መያዝ የለበትም።"; // Father's name must not contain numbers or special characters.
}
 if (!empty($data['sex']) && !in_array($data['sex'], ['ወንድ', 'ሴት'])) {
            $errors[] = "ጾታ ትክክለኛ መሆን አለበት።";
}
// ── FAN: numeric, exactly 16 digits, if provided ────────────────────────
$fan = $data['FAN'] ?? '';
if ($fan !== '') {
    if (!preg_match('/^\d{16}$/', $fan)) {
        $errors[] = "FAN በትክክል 16 ቁጥር ማካተት አለበት።"; // FAN must be exactly 16 digits.
    }
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

if (
    !in_array($data['srafelagi_huneta'] ?? '', [
        'ስራ ፈላጊ',
        'ተፈናቃይ',
        'ከስደት ተመላሽ',
        'የቤት እመቤት',
        'አካል ጉዳተኛ',
        'ዓለም አቀፍ ፍልሰተኛ'
    ], true)) {
    $errors[] = "እባክዎ ትክክለኛ የስራ ፈላጊ ሁኔታ ይምረጡ።"; // "Please select a valid job seeker status."
}
if (
    !in_array($data['residence_status'] ?? '', [
        'ከተማ',
        'ገጠር'
    ], true)) {
    $errors[] = "እባክዎ ትክክለኛ የመኖሪያ አካባቢ ይምረጡ።"; // "Please select a valid residence status."
}

// haveexp: must be 0 or 1
if (!in_array($data['haveexp'] ?? '', ['0', '1'], true)) {
    $errors[] = "እባክዎ የስራ ልምድ መኖር አለመኖሩን ይምረጡ።"; // Please indicate whether you have experience.
}

// experience: required and must be numeric if haveexp == 1
if (($data['haveexp'] ?? '') === '1') {
    if (!isset($data['experience']) || $data['experience'] === '') {
        $errors[] = "እባክዎ የስራ ልምድ ዓመት ያስገቡ።"; // Please enter years of experience.
    } elseif (!is_numeric($data['experience'])) {
        $errors[] = "የስራ ልምድ ቁጥር መሆን አለበት።"; // Experience must be a number.
    }
}

// workplace: must be one of the two allowed values
if (!in_array($data['workplace'] ?? '', ['ከሀገር ውስጥ', 'ከውጭ አገር'], true)) {
    $errors[] = "እባክዎ ትክክለኛ የስራ ቦታ ይምረጡ።"; // Please select a valid workplace.
}

// profession: must be one of the allowed occupation categories
if (
    !in_array($data['profession'] ?? '', [
        'የሥራ ኃላፊዎች ከፍተኛ ባለስልጣኖች፣ ሥራ አስኪያጆች',
        'ፕሮፌሽናሎች',
        'ቴክኒሻያን ተባባሪ ፕሮፌሽናሎች',
        'ክለርክ ሰራተኞች',
        'የአገልግሎት ሰጭ ሠራተኞች፣ ሱቆች የገበያ ሽያጭ ሰራተኞች',
        'የሰለጠነ የግብርና ዓሳ ምርት ሰራተኞች',
        'የዕደ ጥበብ (ክራፍትስ እና የመሳሰሉት ሰራተኞች)',
        'የፋብሪካ ማሽን ኦፕሬተርና ገጣጣሚዎች',
        'ኢለመንታሪ (አነስተኛ የእጅ መሳሪያ) ሙያዎች'
    ], true)
) {
    $errors[] = "እባክዎ ትክክለኛ ሙያ ይምረጡ።"; // Please select a valid profession.
}

// nameofcountry: required if workplace is ከውጭ አገር, must not contain numbers or special characters
if (($data['workplace'] ?? '') === 'ከውጭ አገር') {
    $countryName = trim($data['nameofcountry'] ?? '');
    if ($countryName === '') {
        $errors[] = "እባክዎ የአገር ስም ያስገቡ።"; // Please enter the country name.
    } elseif (!preg_match('/^[\p{L}\s]+$/u', $countryName)) {
        $errors[] = "የአገር ስም ቁጥር ወይም ልዩ ምልክት መያዝ የለበትም።"; // Country name must not contain numbers or special characters.
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
     
    public function listofJobseekersfortransfer() {
         AuthHelper::checkRole(['team_leader', 'officer']);
 $myBranchId  = $_SESSION['user']['branch_id'];
 $jobSeekerModel = new JobSeekerModel($this->db);
$jobSeekers  = $jobSeekerModel->getJobSeekersByHierarchy($myBranchId);
        $data = [
            'jobSeekers' => $jobSeekers,
            'title' => 'JCIMS - የሰራተኛ መመዝገቢያ',
        ];

         $this->render('jobseeker-transfer', $data);
    }
    
    public function getBranchesByHierarchyAjax() {
        header('Content-Type: application/json; charset=utf-8');
        
        // የSession መኖርን ማረጋገጥ
        if (!isset($_SESSION['user']['id'])) {
            echo json_encode(['success' => false, 'data' => [], 'message' => 'ያልተፈቀደ መዳረሻ!']);
            exit;
        }

        $level = isset($_GET['level']) ? (int)$_GET['level'] : 0;
        // parent_id በሕጋዊ መንገድ መቀበል (string ወይም int ሊሆን ይችላል)
        $parentId = isset($_GET['parent_id']) && $_GET['parent_id'] !== '' ? trim($_GET['parent_id']) : null;

        // የግብዓት ማረጋገጫ ጥንቃቄ
        if ($level <= 0 || ($level > 1 && $parentId === null)) {
            echo json_encode(['success' => false, 'data' => [], 'message' => 'የተሳሳተ የተዋረድ ጥያቄ።']);
            exit;
        }
         $jobSeekerModel = new JobSeekerModel($this->db);
        $branches = $jobSeekerModel->getBranchesByLevel($level, $parentId);
        
        echo json_encode(['success' => true, 'data' => $branches]);
        exit;
    }
    private function generateUuidV7() {
        // የአሁኑን ጊዜ በሚሊሰከንድ ማግኘት
        $time = (int) floor(microtime(true) * 1000);
        
        // 10 የዘፈቀደ ባይቶች ማመንጨት
        $randomBytes = random_bytes(10);
        
        $timestampHex = str_pad(dechex($time), 12, '0', STR_PAD_LEFT);
        
        $ver = (7 << 12) | (ord($randomBytes[0]) & 0x0fff);
        $verHex = str_pad(dechex($ver), 4, '0', STR_PAD_LEFT);
        
        $var = (0x8000) | ((ord($randomBytes[1]) << 8) | ord($randomBytes[2])) & 0x3fff;
        $varHex = str_pad(dechex($var), 4, '0', STR_PAD_LEFT);
        
        $restHex = bin2hex(substr($randomBytes, 3));
        
        return sprintf('%s-%s-%s-%s-%s',
            substr($timestampHex, 0, 8),
            substr($timestampHex, 8, 4),
            $verHex,
            $varHex,
            $restHex
        );
    }

    /**
     * የሥራ ፈላጊ ዝውውርን የሚያስተናግድ ዋናው ፈንክሽን
     */
    public function processJobSeekerTransfer() {
    // ምላሹ JSON መሆኑን ማሳወቂያ
    header('Content-Type: application/json; charset=utf-8');

    // 💡 1. ላኪው ቅርንጫፍ (Sender Branch) ከሴሽን መኖሩን ማረጋገጥ
    $sender_branch_id = isset($_SESSION['user']['branch_id']) ? (int)$_SESSION['user']['branch_id'] : 0;
    $user_id          = isset($_SESSION['user']['id']) ? (int)$_SESSION['user']['id'] : 0;
    
    if ($sender_branch_id <= 0 || $user_id <= 0) {
        echo json_encode([
            'success' => false, 
            'message' => 'የሎግድ-ኢን ተጠቃሚው መረጃ (Branch ID/User ID) በሴሽን ውስጥ አልተገኘም። እባክዎ እንደገና ይግቡ።'
        ]);
        exit;
    }
 
    // 💡 2. ከቪው (POST) የመጡትን መረጃዎች በደህንነት መቀበል
    $job_seeker_id = filter_input(INPUT_POST, 'job_seeker_id', FILTER_VALIDATE_INT);
    $transfer_type = filter_input(INPUT_POST, 'transfer_level_type', FILTER_DEFAULT); // 'center' ወይም 'woreda'

    // የቦታ ተዋረዶችን መቀበልና ወደ Integer መቀየር
    $region_id = isset($_POST['region_id']) ? (int)$_POST['region_id'] : null;
    $zone_id   = isset($_POST['zone_id']) ? (int)$_POST['zone_id'] : null;
    $woreda_id = isset($_POST['woreda_id']) ? (int)$_POST['woreda_id'] : null;

    // 💡 3. የመዳረሻ ቅርንጫፍ (Final Receiver Branch ID) ሎጂክ መወሰኛ
    $final_receiver_branch_id = 0;

    if ($transfer_type === 'center') {
        $destination_branch_id = isset($_POST['destination_branch_id']) ? (int)$_POST['destination_branch_id'] : 0;
        if ($destination_branch_id <= 0) {
            echo json_encode(['success' => false, 'message' => 'እባክዎ ዝውውሩ የሚፈጸምበትን ማዕከል ይምረጡ።']);
            exit;
        }
        $final_receiver_branch_id = $destination_branch_id;
    } elseif ($transfer_type === 'woreda') {
        if (!$woreda_id || $woreda_id <= 0) {
            echo json_encode(['success' => false, 'message' => 'እባክዎ ዝውውሩ የሚፈጸምበትን ወረዳ ይምረጡ።']);
            exit;
        }
        $final_receiver_branch_id = $woreda_id;
    } else {
        echo json_encode(['success' => false, 'message' => 'የማይታወቅ የዝውውር ዓይነት ተመርጧል።']);
        exit;
    }

    // 💡 4. የዳታ ሙሉነት ማረጋገጫ (Validation)
    if (!$job_seeker_id || !$region_id || !$zone_id || $final_receiver_branch_id <= 0) {
        echo json_encode([
            'success' => false, 
            'message' => 'ያልተሟላ መረጃ ተልኳል። እባክዎ ሁሉንም ሳጥኖች በትክክል መሙላትዎን ያረጋግጡ!'
        ]);
        exit;
    }

    // ⚠️ የነበረው የሙከራ 'echo ... exit;' መስመር ኮዱ ወደ ታች እንዳይወርድ ስለሚያደርገው እዚህ ጋር እንዲወገድ ተደርጓል

    // 💡 5. የዳታቤዝ ስራዎችን በደህንነት ማከናወን
 // 💡 5. የዳታቤዝ ስራዎችን በደህንነት ማከናወን
    try {
        // የ UUID V7 መለያ ማመንጫ
        
 $jobseekerUuidtra = Uuid::uuid7()->toString();
      $uuidV7= $jobseekerUuidtra;
       // 🔐 ከሞዴሉ (insertTransferLog) የፕሌስሆልደር ስሞች ጋር ፍጹም የተጣጣመ አሬይ
        $logData = [
            'id'               => $uuidV7,
            'job_seeker_id'    => $job_seeker_id,
            'sender_branch_id' => $sender_branch_id,
            'recver_branch_id' => $final_receiver_branch_id,
            'transfer_status'  => '0',     // ለቪው ሰንጠረዥ እንዲመች
            'transferby'       => $user_id,        // 💡 ወደ (int)$data['transferby'] የሚቀየረው ቁልፍ
            'acceptorrejectby' => null           // የውጭ ቁልፍ (Foreign Key) ስህተትን ለመከላከል NULL
        ];

        // የሞዴል ኢንስታንስ መፍጠር
        $model = new JobSeekerModel($this->db);
        

        // 💡 ሥራ ፈላጊው በተመሳሳይ መዳረሻ ላይ 'pending (0)' ዝውውር ካለው አስቀድሞ መፈተሽ
        $isDuplicate = $model->checkDuplicateTransfer($job_seeker_id, $final_receiver_branch_id);
        
        if ($isDuplicate) {
            echo json_encode([
                'success' => false, 
                'message' => 'ይህ ሥራ ፈላጊ ቀደም ሲል ወደዚህ መዳረሻ የተላከ እና ውሳኔ ያልተሰጠው (Pending) ዝውውር ስላለው በድጋሚ መላክ አይቻልም!'
            ]);
            exit;
        }
        // መረጃውን ማስገባት
        $inserted = $model->insertTransferLog($logData);

        if ($inserted) {
            echo json_encode([
                'success' => true, 
                'message' => 'የሥራ ፈላጊ ዝውውሩ በተሳካ ሁኔታ ተመዝግቧል።'
            ]);
        } else {
            echo json_encode([
                'success' => false, 
                'message' => 'ዝውውሩን መመዝገብ አልተቻለም። እባክዎ የዳታቤዝ ግንኙነቱን ያረጋግጡ።'
            ]);
        }

    } catch (Exception $e) {
        // 💡 የጃቫስክሪፕት ስህተትን ለማስቀረት እዚህ ጋርም ቢሆን የ JSON ምላሽ መላክ ግድ ነው
        echo json_encode([
            'success' => false, 
            'message' => 'የሲስተም ስህተት አጋጥሟል፦ ' . $e->getMessage()
        ]);
    }
    
}
public function showTransferTracking() {
        $branch_id = isset($_SESSION['user']['branch_id']) ? (int)$_SESSION['user']['branch_id'] : 0;
        
        $model = new JobSeekerModel($this->db);
        $transfers = $model->getTransferTrackingList($branch_id);
      
        $data = [
            'transfers' => $transfers,
            'title' => 'JCIMS - የሥራ ፈላጊ ዝውውር መከታተያ',
        ];
        $this->render('jobseeker-transfers-list', $data);
    }
    public function updateJobseekerTransferStatus() {
    // ጥያቄው የመጣው በ POST መሆኑን ማረጋገጥ
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'ልክ ያልሆነ የጥያቄ ዘዴ!']);
        exit;
    }

    header('Content-Type: application/json');

    try {
        // ከፎርሙ የመጡትን መረጃዎች በጥንቃቄ መቀበል
        $transfer_log_id = isset($_POST['transfer_log_id']) ? (int)$_POST['transfer_log_id'] : 0;
        $action_status = isset($_POST['action_status']) ? (int)$_POST['action_status'] : null;
        $current_user_id = $_SESSION['user']['user_id'] ?? null; // ውሳኔ የሰጠው አካል መለያ

        if ($transfer_log_id === 0 || $action_status === null) {
            echo json_encode(['success' => false, 'message' => 'እባክዎ የተሟላ መረጃ ያቅርቡ!']);
            exit;
        }

        // 1. መጀመሪያ ይህ የዝውውር መዝገብ መኖሩን እና ሁኔታው '0' (Pending) መሆኑን ማረጋገጥ
        $checkSql = "SELECT transfer_status, job_seeker_id, recver_branch_id FROM `transfer_logs` WHERE id = :id";
        $stmtCheck = $this->db->prepare($checkSql);
        $stmtCheck->execute(['id' => $transfer_log_id]);
        $log = $stmtCheck->fetch(\PDO::FETCH_ASSOC);

        if (!$log) {
            echo json_encode(['success' => false, 'message' => 'የዝውውር መረጃው አልተገኘም!']);
            exit;
        }

        if ((int)$log['transfer_status'] !== 0) {
            echo json_encode(['success' => false, 'message' => 'ይህ ዝውውር ቀድሞውኑ ውሳኔ አግኝቷል!']);
            exit;
        }

        // 🔐 የደህንነት ማረጋገጫ፦ ውሳኔ እየሰጠ ያለው ቅርንጫፍ መዳረሻ ቅርንጫፍ መሆኑን ማረጋገጥ
        $current_branch_id = isset($_SESSION['user']['internal_id']) ? (int)$_SESSION['user']['internal_id'] : 0;
        if ((int)$log['recver_branch_id'] !== $current_branch_id) {
            echo json_encode(['success' => false, 'message' => 'ለዚህ ቅርንጫፍ ውሳኔ የመስጠት ፈቃድ የለዎትም!']);
            exit;
        }

        // ዳታቤዙን በአንድ ላይ ለማዘመን ትራንዛክሽን እንጀምር (Transaction)
        $this->db->beginTransaction();

        // 2. የ `transfer_logs` ሰንጠረዥን ሁኔታ ማዘመን (Approved=1, Rejected=2)
        // ተቀባዩ ቅርንጫፍ ውሳኔ ስለሰጠ የዕይታ ሁኔታውን (reciver_view_status) '1' እናደርገዋለን
        $updateLogSql = "UPDATE `transfer_logs` 
                         SET transfer_status = :status, 
                             reciver_view_status = 1,
                             action_by = :user_id,
                             action_date = NOW()
                         WHERE id = :id";
        
        $stmtLog = $this->db->prepare($updateLogSql);
        $stmtLog->execute([
            'status'  => $action_status,
            'user_id' => $current_user_id,
            'id'      => $transfer_log_id
        ]);

        // 3. ውሳኔው "የጸደቀ" (Approve=1) ከሆነ ብቻ፣ በ `job_seekers` ሰንጠረዥ ላይ የሥራ ፈላጊውን ቅርንጫፍ (branch_id) እንለውጣለን
        if ($action_status === 1) {
            $job_seeker_id = $log['job_seeker_id'];
            
            $updateSeekerSql = "UPDATE `job_seekers` 
                                SET branch_id = :new_branch_id 
                                WHERE job_seeker_id = :seeker_id";
            
            $stmtSeeker = $this->db->prepare($updateSeekerSql);
            $stmtSeeker->execute([
                'new_branch_id' => $current_branch_id,
                'seeker_id'     => $job_seeker_id
            ]);
        }

        // ሁሉንም ለውጦች በሰላም ማጽደቅ
        $this->db->commit();

        $msg = ($action_status === 1) ? 'ዝውውሩ በተሳካ ሁኔታ ጽድቋል፣ የሥራ ፈላጊው ቅርንጫፍ ተቀይሯል።' : 'ዝውውሩ ውድቅ ተደርጓል።';
        echo json_encode(['success' => true, 'message' => $msg]);

    } catch (\PDOException $e) {
        // ስህተት ካለ ወደ ኋላ መመለስ (Rollback)
        if ($this->db->inTransaction()) {
            $this->db->rollBack();
        }
        echo json_encode(['success' => false, 'message' => 'የዳታቤዝ ስህተት አጋጥሟል፦ ' . $e->getMessage()]);
    }
    exit;
}
    }