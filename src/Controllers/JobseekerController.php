<?php
namespace App\Controllers;
use App\Helpers\AuthHelper;
use App\Models\JobSeekerModel;
use App\Models\SectorModel;
use Ramsey\Uuid\Uuid;
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
 $jobSeekerModel = new JobSeekerModel($this->db);
$jobSeekers  = $jobSeekerModel->getJobSeekersByHierarchy($myBranchId);
        $data = [
            'jobSeekers' => $jobSeekers,
            'title' => 'JCIMS - የሰራተኛ መመዝገቢያ',
        ];

        $this->render('jobseekers-list', $data);
    }
    }