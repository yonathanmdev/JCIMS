<?php
namespace App\Controllers;
use App\Helpers\AuthHelper;
use App\Helpers\AmharicNormalizer;
use App\Models\JobSeekerModel;
use App\Models\SectorModel;
use Ramsey\Uuid\Uuid;
use Exception;
class JobseekerController extends BaseController {
   
    public function showRegisterForm() {
           AuthHelper::checkRole(
    ['team_leader','officer'],
    [3, 4]
);
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

    
  public function getJobseekerById()
{
    $t0 = microtime(true);
    header('Content-Type: application/json');

    AuthHelper::checkRole(['officer'], [3, 4]);
    session_write_close();
    $t1 = microtime(true);
    error_log("checkRole took: " . round(($t1 - $t0) * 1000, 2) . "ms");

    $user     = $_SESSION['user'] ?? [];
    $branchId = $user['branch_id'] ?? null;

    if (!$branchId || empty($user['id'])) {
        echo json_encode(['success' => false, 'message' => 'ፍቃድ የለዎትም።']);
        return;
    }

    $jobseekerId = trim($_GET['jobseeker_id'] ?? '');
    if ($jobseekerId === '') {
        echo json_encode(['success' => false, 'message' => 'የስራ ፈላጊ መለያ አልተገኘም።']);
        return;
    }

    $jobSeekerModel = new JobSeekerModel($this->db);

    $t2 = microtime(true);
    $jobseeker = $jobSeekerModel->findById((int)$branchId, $jobseekerId);
    $t3 = microtime(true);
    error_log("findById took: " . round(($t3 - $t2) * 1000, 2) . "ms");

    if (!$jobseeker) {
        echo json_encode(['success' => false, 'message' => 'ስራ ፈላጊው አልተገኘም ወይም በዚህ ቅርንጫፍ አልተመዘገበም።']);
        return;
    }

    $jobseeker['choice_sector1'] = $jobseeker['choice_sector1_uuid'] ?? '';
    $jobseeker['sub_choose1']    = $jobseeker['sub_choose1_uuid'] ?? '';
    $jobseeker['choice_sector2'] = $jobseeker['choice_sector2_uuid'] ?? '';
    $jobseeker['sub_choose2']    = $jobseeker['sub_choose2_uuid'] ?? '';
    $jobseeker['choice_sector3'] = $jobseeker['choice_sector3_uuid'] ?? '';
    $jobseeker['sub_choose3']    = $jobseeker['sub_choose3_uuid'] ?? '';

    echo json_encode(['success' => true, 'jobseeker' => $jobseeker]);

    $t4 = microtime(true);
    error_log("TOTAL request took: " . round(($t4 - $t0) * 1000, 2) . "ms");
}
public function handleRegistration() {
    AuthHelper::checkRole(['officer'],[3, 4]);

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
    $fullNameRaw = $_POST['first_name'] . ' ' . $_POST['father_name'] . ' ' . $_POST['last_name'];
        $normalizedFullName = AmharicNormalizer::normalize($fullNameRaw);

         // ── Persist ───────────────────────────────────────────────────────
        $jobSeekerModel = new JobSeekerModel($this->db);
        // 3. Duplicate check
     $duplicate = $jobSeekerModel->checkDuplicateJobSeeker([
    'branch_id'            => $_SESSION['user']['branch_id'],
    'kebele_id_no'         => trim($_POST['kebele_id_no']),
    'g8id'                 => trim($_POST['g8id'] ?? ''),
    'Labor_ID'             => trim($_POST['Labor_ID'] ?? ''),
    'FAN'                  => trim($_POST['FAN'] ?? ''),
    'full_name_normalized' => $normalizedFullName,
    'mothername'           => trim($_POST['mothername']),
    'phone_number'         => trim($_POST['phone_number']),
]);

if (!empty($duplicate)) {

    $triggerType = 'identity'; // default fallback
    if (!empty($duplicate['kebele_duplicate']))  $triggerType = 'kebele_id';
    elseif (!empty($duplicate['g8id_duplicate']))  $triggerType = 'g8id';
    elseif (!empty($duplicate['labor_duplicate'])) $triggerType = 'labor_id';
    elseif (!empty($duplicate['fan_duplicate']))   $triggerType = 'fan';
    elseif (!empty($duplicate['identity_duplicate'])) $triggerType = 'identity';

    $jobSeekerModel->logDuplicateAttempt([
        'id'                     => Uuid::uuid7()->toString(),
        'attempted_by'           => $user['id'],
        'branch_id'              => $branchId,
        'trigger_type'           => $triggerType,
        'matched_jobseeker_id'   => $duplicate['job_seeker_id'] ?? null,
        'attempted_kebele_id_no' => trim($_POST['kebele_id_no']),
        'attempted_g8id'         => trim($_POST['g8id'] ?? ''),
        'attempted_labor_id'     => trim($_POST['Labor_ID'] ?? ''),
        'attempted_fan'          => trim($_POST['FAN'] ?? ''),
        'attempted_full_name'    => $normalizedFullName,
        'attempted_phone_number' => trim($_POST['phone_number']),
        'attempted_mothername'   => trim($_POST['mothername']),
        'ip_address'             => $_SERVER['REMOTE_ADDR'] ?? null,
    ]);

    $_SESSION['error'] =
        "ስራ ፈላጊው ከዚህ በፊት <strong>{$duplicate['branch_hierarchy']}</strong> ተመዝግቧል።";

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
                $_SESSION['error'] = "የተመረጠው ሙያ {$sectorId} መረጃ አልተገኘም።";
                header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/jobseeker-registration");
                exit();
            }

            $resolvedSectors[$sectorId] = [
                'sector_id'    => $ids['sectorid'],
                'subsector_id' => $ids['sub_sectorid'],
            ];
        }

        // ── Derive education_level_catagory from educational_level ───────
        $educationLevelCategory = $this->resolveEducationLevelCategory(
            trim($_POST['educational_level'] ?? '')
        );

        // ── Build data arrays ─────────────────────────────────────────────
        $jobseekerUuid = Uuid::uuid7()->toString();

        $data = [
            'uuid'                             => $jobseekerUuid,
            'first_name'                       => trim($_POST['first_name'] ?? ''),
            'father_name'                      => trim($_POST['father_name'] ?? ''),
            'last_name'                        => trim($_POST['last_name'] ?? ''),
            'gender'                           => trim($_POST['gender'] ?? ''),
            'age'                              => $_POST['age'] ?? '',
            'educational_level'                => trim($_POST['educational_level'] ?? ''),
            'education_level_catagory'         => $educationLevelCategory,
            'educated_dpt'                     => trim($_POST['educated_dpt'] ?? ''),
            'education_trmnet_finsh_year'      => $_POST['education_trmnet_finsh_year'] ?? '',
            'g8id'                             => trim($_POST['g8id'] ?? ''),
            'CGPA'                             => $_POST['CGPA'] ?? '',
            'school_type'                      => trim($_POST['school_type'] ?? ''),
            'physical_condition'               => $_POST['physical_condition'] ?? '',
            'physical_condition_desc'          => trim($_POST['physical_condition_desc'] ?? ''),
            'kebele'                           => trim($_POST['kebele'] ?? ''),
            'mender'                           => trim($_POST['mender'] ?? ''),
            'kebele_id_no'                     => trim($_POST['kebele_id_no'] ?? ''),
            'phone_number'                     => $_POST['phone_number'] ?? '',
            'meteleya_huneta'                  => trim($_POST['meteleya_huneta'] ?? ''),
            'residence_status'                 => trim($_POST['residence_status'] ?? ''),
            'srafelagi_huneta'                 => trim($_POST['srafelagi_huneta'] ?? ''),
            'graguation_catagory'              => trim($_POST['graguation_catagory'] ?? ''),
            'maritalstatus'                    => trim($_POST['maritalstatus'] ?? ''),
            'haveexp'                          => $_POST['haveexp'] ?? '',
            'workplace'                        => trim($_POST['workplace'] ?? ''),
            'experience'                       => $_POST['experience'] ?? '',
            'profession'                       => trim($_POST['profession'] ?? ''),
            'nameofcountry'                    => trim($_POST['nameofcountry'] ?? ''),
            'language'                         => trim($_POST['language'] ?? ''),
            'wageorself'                       => trim($_POST['wageorself'] ?? ''),
            'mothername'                       => trim($_POST['mothername'] ?? ''),
            'Labor_ID'                         => trim($_POST['Labor_ID'] ?? ''),
            'FAN'                              => $_POST['FAN'] ?? '',
            'fiscal_year'                      => AuthHelper::checkFiscalYear(),
            'agri_business_experience_status'  => $_POST['agri_business_experience_status'] ?? '',
            'agri_business_experience'         => $_POST['agri_business_experience'] ?? '',
            'has_dependents'                   => $_POST['has_dependents'] ?? '',
            'number_of_dependents'             => $_POST['number_of_dependents'] ?? '',
            'children_under_five'              => $_POST['children_under_five'] ?? '',
            'branch_id'                        => $branchId,
            'reg_by'                           => $user['id'],
            'regstration_level'                => $user['level'] ?? '',
            'normalizedFullName'                => $normalizedFullName,

            // ── Resolved sector/subsector bigint FKs ─────────────────────
            'choice_sector1' => $resolvedSectors[1]['sector_id'],
            'sub_choose1'    => $resolvedSectors[1]['subsector_id'],
            'choice_sector2' => $resolvedSectors[2]['sector_id'],
            'sub_choose2'    => $resolvedSectors[2]['subsector_id'],
            'choice_sector3' => $resolvedSectors[3]['sector_id'],
            'sub_choose3'    => $resolvedSectors[3]['subsector_id'],
        ];

       

        if ($jobSeekerModel->createJobseeker($data)) {
            \App\Helpers\AuditHelper::log('jobseeker_registered', 'job_seekers', $jobseekerUuid, null, [
                'uuid'        => $jobseekerUuid,
                'first_name'  => $data['first_name'],
                'father_name' => $data['father_name'],
                'last_name'   => $data['last_name'],
                'branch_id'   => $data['branch_id'],
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

private function resolveEducationLevelCategory(string $educationalLevel): int
{
    $degreeLevels = ['የመጀመሪያ ዲግሪ', 'ሁለተኛ ዲግሪ'];
    $diplomaLevels = ['ደረጃ 2', 'ደረጃ 3', 'ደረጃ 4', 'ደረጃ 5'];

    if (in_array($educationalLevel, $degreeLevels, true)) {
        return 2;
    }

    if (in_array($educationalLevel, $diplomaLevels, true)) {
        return 1;
    }

    return 0;
}
private function validateJobseekerData(array $post): array
{
    $errors = [];

    $get = fn(string $key) => trim((string) ($post[$key] ?? ''));

    // ═══════════════════════════════════════════════════════════════
    // CATEGORY 1: ALWAYS-REQUIRED FIELDS
    // ═══════════════════════════════════════════════════════════════
    $alwaysRequired = [
        'first_name'         => 'ስም',
        'father_name'        => 'የአባት ስም',
        'last_name'      => 'የአያት ስም',
        'age'                => 'እድሜ',
        'gender'             => 'ጾታ',
        'educational_level'  => 'የት/ት ደረጃ',
        'wageorself'         => 'አሁን መስራት የሚፈልጉት',
        'physical_condition' => 'የጉዳት ሁኔታ',
        'kebele_id_no'       => 'የቀበሌ መ/ቁጥር',
        'choice_sector1'     => '1ኛ የዘርፍ ምርጭ',
        'sub_choose1'        => '1ኛ ንዑስ ዘርፍ ምርጭ',
        'choice_sector2'     => '2ኛ የዘርፍ ምርጭ',
        'sub_choose2'        => '2ኛ ንዑስ ዘርፍ ምርጭ',
        'choice_sector3'     => '3ኛ የዘርፍ ምርጭ',
        'sub_choose3'        => '3ኛ ንዑስ ዘርፍ ምርጭ',
        'meteleya_huneta'    => 'መኖሪያ ቤት ሁኔታ',
        'residence_status'   => 'የሚኖሩበት አካባቢ',
        'srafelagi_huneta'   => 'የስራ ፈላጊ ሁኔታ',
        'maritalstatus'      => 'የጋብቻ ሁኔታ',
        'mothername'         => 'የእናት ሙሉ ስም',
        'haveexp'            => 'የስራ ልምድ',
    ];

    foreach ($alwaysRequired as $field => $label) {
        if ($get($field) === '') {
            $errors[] = "{$label} ያስፈልጋል።";
        }
    }

    // ═══════════════════════════════════════════════════════════════
    // CATEGORY 2: NAME FORMAT VALIDATION (no numbers/special chars)
    // ═══════════════════════════════════════════════════════════════
    if (!preg_match('/^[\p{L}\s]+$/u', $get('first_name'))) {
        $errors[] = "ስም ቁጥር ወይም ልዩ ምልክት መያዝ የለበትም።";
    }
    if (!preg_match('/^[\p{L}\s]+$/u', $get('father_name'))) {
        $errors[] = "የአባት ስም ቁጥር ወይም ልዩ ምልክት መያዝ የለበትም።";
    }
    if (!preg_match('/^[\p{L}\s]+$/u', $get('last_name'))) {
        $errors[] = "የአያት ስም ቁጥር ወይም ልዩ ምልክት መያዝ የለበትም።";
    }

    // ═══════════════════════════════════════════════════════════════
    // CATEGORY 3: FAN VALIDATION (16-digit national ID, if provided)
    // ═══════════════════════════════════════════════════════════════
    $fan = $get('FAN') ?? '';
    if ($fan !== '') {
        if (!preg_match('/^\d{16}$/', $fan)) {
            $errors[] = "FAN በትክክል 16 ቁጥር ማካተት አለበት።";
        }
    }

    // ═══════════════════════════════════════════════════════════════
    // CATEGORY 4: GENDER VALIDATION
    // ═══════════════════════════════════════════════════════════════
    if (!empty($get('gender')) && !in_array($get('gender'), ['ወንድ', 'ሴት'])) {
        $errors[] = "ጾታ ትክክለኛ መሆን አለበት።";
    }

    $eduLevel = $get('educational_level');

    // ═══════════════════════════════════════════════════════════════
    // CATEGORY 5: EDUCATIONAL LEVEL CONDITIONAL LOGIC
    // ═══════════════════════════════════════════════════════════════
    $hideSchoolFor = ['ማንበብና መፃፍ የማይችሉ', 'መሰረተ ትምህርት'];
    $educated_dptLevels = ['ደረጃ 2', 'ደረጃ 3', 'ደረጃ 4', 'ደረጃ 5', 'የመጀመሪያ ዲግሪ', 'ሁለተኛ ዲግሪ'];
    $g8idLevels = array_merge(
        ['8ኛ ያጠናቀቁ', 'ከ9-10ኛ', 'ከ11-12ኛ'],
        $educated_dptLevels
    );

    if (!in_array($eduLevel, $hideSchoolFor, true) && $get('education_trmnet_finsh_year') === '') {
        $errors[] = 'የትምህርት ማጠናቀቂያ ዓመት ያስፈልጋል።';
    }

    if (in_array($eduLevel, $educated_dptLevels, true)) {
        if ($get('educated_dpt') === '') {
            $errors[] = 'የትምህርት ክፍል ያስፈልጋል።';
        }
        if ($get('CGPA') === '') {
            $errors[] = 'CGPA ያስፈልጋል።';
        }
        if ($get('school_type') === '') {
            $errors[] = 'የትምህርት ቤት አይነት ያስፈልጋል።';
        }
        if ($get('phone_number') === '') {
            $errors[] = 'ስልክ ቁጥር ያስፈልጋል።';
        }
    }

    if (in_array($eduLevel, $g8idLevels, true) && $get('g8id') === '') {
        $errors[] = 'የ8ኛ ክፍል id ያስፈልጋል።';
    }

    // ═══════════════════════════════════════════════════════════════
    // CATEGORY 6: WORK EXPERIENCE + LANGUAGE LOGIC
    // ═══════════════════════════════════════════════════════════════

    $haveExp = $get('haveexp');
 // CATEGORY 15: EXPERIENCE FLAG VALIDATION
    // ═══════════════════════════════════════════════════════════════
    if (!in_array($haveExp ?? '', ['0', '1'], true)) {
        $errors[] = "እባክዎ የስራ ልምድ መኖር አለመኖሩን ይምረጡ።";
    }
    if ($haveExp === '1') {
        if ($get('workplace') === '') {
            $errors[] = 'የስራ ቦታ ያስፈልጋል።';
        }
        if ($get('profession') === '') {
            $errors[] = 'ሙያ ያስፈልጋል።';
        }

        if ($get('workplace') === 'ከውጭ አገር' && $get('nameofcountry') === '') {
            $errors[] = 'የሀገር ስም ያስፈልጋል።';
        }
         
    // ═══════════════════════════════════════════════════════════════
    // CATEGORY 16: WORKPLACE VALIDATION
    // ═══════════════════════════════════════════════════════════════
    if (!in_array($get('workplace') ?? '', ['ከሀገር ውስጥ', 'ከውጭ አገር'], true)) {
        $errors[] = "እባክዎ ትክክለኛ የስራ ቦታ ይምረጡ።";
    }

    // ═══════════════════════════════════════════════════════════════
    // CATEGORY 17: PROFESSION VALIDATION
    // ═══════════════════════════════════════════════════════════════
    if (
        !in_array($get('profession') ?? '', [
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
        $errors[] = "እባክዎ ትክክለኛ ሙያ ይምረጡ።";
    }

    // ═══════════════════════════════════════════════════════════════
    // CATEGORY 18: COUNTRY NAME VALIDATION (required if workplace abroad)
    // ═══════════════════════════════════════════════════════════════
    if (($get('workplace') ?? '') === 'ከውጭ አገር') {
        $countryName = trim($get('nameofcountry') ?? '');
        if ($countryName === '') {
            $errors[] = "እባክዎ የአገር ስም ያስገቡ።";
        } elseif (!preg_match('/^[\p{L}\s]+$/u', $countryName)) {
            $errors[] = "የአገር ስም ቁጥር ወይም ልዩ ምልክት መያዝ የለበትም።";
        }
    }
    }

    // ═══════════════════════════════════════════════════════════════
    // CATEGORY 7: PHYSICAL CONDITION LOGIC (CONSOLIDATED — was split
    // across two blocks; now a single source of truth)
    // ═══════════════════════════════════════════════════════════════
    if ($get('physical_condition') === '' || !in_array($get('physical_condition'), ['0', '1'], true)) {
        $errors[] = 'የአካል ሁኔታ ትክክለኛ መረጃ ያስገቡ።';
    } elseif ($get('physical_condition') === '1') {
        $physicalConditionDesc = $get('physical_condition_desc');

        if ($physicalConditionDesc === '') {
            $errors[] = 'የጉዳት አይነት ያስገቡ።';
        } elseif (!in_array($physicalConditionDesc, [
            'የአካል ብቃት ወይም የእንቅስቃሴ ጉዳት',
            'ማየት የተሳናቸው',
            'መስማት የተሳናቸው',
            'የአእምሮ እድገት ወይም የመማር ችግር',
            'የስነ-አእምሮ ጤና እክል',
            'ከተጠቀሱት ውጭ',
        ], true)) {
            $errors[] = 'እባክዎ ትክክለኛ የጉዳት አይነት ይምረጡ።'; // Please select a valid disability type.
        }
    }

    // ═══════════════════════════════════════════════════════════════
    // CATEGORY 8: AGRICULTURE SECTOR LOGIC
    // ═══════════════════════════════════════════════════════════════
    $sectorModel = new SectorModel($this->db);

    $sectorIds = array_filter([
        $get('choice_sector1'),
        $get('choice_sector2'),
        $get('choice_sector3'),
    ], fn($v) => $v !== '');

    $isAgriSelected = false;
    foreach ($sectorIds as $sectorId) {
        $sector = $sectorModel->getSectorById($sectorId);
        if ($sector && $sector['sector'] === 'ግብርና') {
            $isAgriSelected = true;
            break;
        }
    }

    if ($isAgriSelected) {
        $agriStatus = $get('agri_business_experience_status');
        if ($agriStatus === '') {
            $errors[] = 'በግብርና ዘርፍ ልምድ መምረጥ ያስፈልጋል።';
        } elseif ($agriStatus === '1' && $get('agri_business_experience') === '') {
            $errors[] = 'የግብርና ስራ ልምድ ያስፈልጋል።';
        }
    }

    // ═══════════════════════════════════════════════════════════════
    // CATEGORY 9: GENDER-DEPENDENT (DEPENDENTS) LOGIC
    // ═══════════════════════════════════════════════════════════════
    if ($get('gender') === 'ሴት') {
        $hasDependents = $get('has_dependents');
        if ($hasDependents === '') {
            $errors[] = 'በስር የሚተዳደር ቤተሰብ መምረጥ ያስፈልጋል።';
        } elseif ($hasDependents === '1' && $get('number_of_dependents') === '') {
            $errors[] = 'የሚተዳደሩ ቤተሰብ ብዛት ያስፈልጋል።';
        } elseif ($hasDependents === '1') {
            $numberOfDependents = (int) $get('number_of_dependents');
            $childrenUnderFive = (int) $get('children_under_five');

            if ($childrenUnderFive > $numberOfDependents) {
                $errors[] = "ከ5 ዓመት በታች ያሉ ልጆች ቁጥር ከጠቅላላ ቤተሰብ ብዛት ({$numberOfDependents}) መብለጥ አይችልም።";
            }
        }
    }

    // ═══════════════════════════════════════════════════════════════
    // CATEGORY 10: EDUCATIONAL LEVEL VALUE VALIDATION (allowed list)
    // ═══════════════════════════════════════════════════════════════
    if (!in_array($eduLevel ?? '', [
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

    // ═══════════════════════════════════════════════════════════════
    // CATEGORY 11: PHONE NUMBER VALIDATION
    // ═══════════════════════════════════════════════════════════════
    if (!empty($get('phone_number'))) {
        if (!preg_match('/^[0-9]{10}$/', $get('phone_number'))) {
            $errors[] = "ስልክ ቁጥር ትክክለኛ 10 አሃዝ መሆን አለበት።";
        }
    }

    // ═══════════════════════════════════════════════════════════════
    // CATEGORY 12: CGPA VALIDATION
    // ═══════════════════════════════════════════════════════════════
    if (isset($data['CGPA']) && trim($data['CGPA']) !== '') {
        $cgpaRaw = trim($data['CGPA']);

        if (!preg_match('/^\d+(\.\d{1,2})?$/', $cgpaRaw)) {
            $errors[] = 'CGPA ቁጥር መሆን አለበት እና ከ2 በላይ የአስርዮሽ ቦታ መያዝ የለበትም።';
        } else {
            $cgpaValue = (float) $cgpaRaw;

            if ($cgpaValue < 2.00 || $cgpaValue > 4.00) {
                $errors[] = 'CGPA ከ2.00 እስከ 4.00 መካከል መሆን አለበት።';
            }
        }
    }

    // ═══════════════════════════════════════════════════════════════
    // CATEGORY 13: JOB SEEKER STATUS VALIDATION
    // ═══════════════════════════════════════════════════════════════
    if (
        !in_array($get('srafelagi_huneta') ?? '', [
            'ስራ ፈላጊ',
            'ተፈናቃይ',
            'ከስደት ተመላሽ',
            'የቤት እመቤት',
            'አካል ጉዳተኛ',
            'ዓለም አቀፍ ፍልሰተኛ'
        ], true)) {
        $errors[] = "እባክዎ ትክክለኛ የስራ ፈላጊ ሁኔታ ይምረጡ።";
    }

    // ═══════════════════════════════════════════════════════════════
    // CATEGORY 14: RESIDENCE STATUS VALIDATION
    // ═══════════════════════════════════════════════════════════════
    if (
        !in_array($get('residence_status') ?? '', [
            'ከተማ',
            'ገጠር'
        ], true)) {
        $errors[] = "እባክዎ ትክክለኛ የመኖሪያ አካባቢ ይምረጡ።";
    }

  

    return $errors;
}


    public function listofJobseekers() {
         AuthHelper::checkRole(['team_leader', 'officer']);
 $myBranchId  = $_SESSION['user']['branch_id'];
 $jobSeekerModel = new JobSeekerModel($this->db);
$jobSeekers  = $jobSeekerModel->getJobSeekersByHierarchy($myBranchId);
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