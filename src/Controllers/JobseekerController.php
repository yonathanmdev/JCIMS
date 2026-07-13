<?php
namespace App\Controllers;
use App\Helpers\AuthHelper;
use App\Helpers\AmharicNormalizer;
use App\Models\JobSeekerModel;
use App\Models\SectorModel;
use Ramsey\Uuid\Uuid;
use Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Fill;

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

    AuthHelper::checkRole(['team_leader', 'officer']);
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
    AuthHelper::checkRole(['team_leader', 'officer'], [3, 4]);

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/jobseeker-registration");
        exit();
    }

    $mode         = trim($_POST['mode'] ?? 'create');
    $jobseekerId  = trim($_POST['jobseeker_id'] ?? ''); // record's UUID `id`
    $jobseekerIdInt = null; // job_seeker_id (int business ID), populated below for edit/renewal
    $createdAt    = null;

    if ($mode === 'edit' && $jobseekerId === '') {
        $_SESSION['error'] = 'ስህተት፦ የስራ ፈላጊ መለያ አልተገኘም።';
        header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/jobseeker-registration");
        exit();
    }
    if ($mode === 'renewal' && $jobseekerId === '') {
        $_SESSION['error'] = 'ስህተት፦ የስራ ፈላጊ መለያ አልተገኘም።';
        header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/jobseeker-registration");
        exit();
    }

    $jobSeekerModel = new JobSeekerModel($this->db);

    if ($mode === 'renewal' || $mode === 'edit') {
        $jobseekerdata  = $jobSeekerModel->getJobSeekerById($jobseekerId);
        $jobseekerIdInt = $jobseekerdata['job_seeker_id'] ?? null;
        $createdAt      = $jobseekerdata['created_at'] ?? null;

        if ($jobseekerIdInt === null || $createdAt === null) {
            $_SESSION['error'] = 'ስህተት፦ የስራ ፈላጊ መለያ አልተገኘም።';
            header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/jobseeker-registration");
            exit();
        }
    }

    $user     = $_SESSION['user'] ?? [];
    $branchId = $user['branch_id'] ?? null;

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

    $fullNameRaw        = $_POST['first_name'] . ' ' . $_POST['father_name'] . ' ' . $_POST['last_name'];
    $normalizedFullName = AmharicNormalizer::normalize($fullNameRaw);

    // ── Duplicate check — mode-aware exclusion ───────────────────────────
    // create:  no exclusion — any match anywhere is a real duplicate
    // edit:    exclude own row in the ACTIVE table only
    // renewal: exclude own row in the ARCHIVE table only — if the same
    //          job_seeker_id already exists in active, that's a real
    //          duplicate (already renewed / already active) and must
    //          still be flagged.
    $excludeId    = null;
    $excludeTable = null;

    if ($mode === 'edit') {
        $excludeId    = $jobseekerIdInt;
        $excludeTable = 'active';
    } elseif ($mode === 'renewal') {
        $excludeId    = $jobseekerIdInt;
        $excludeTable = 'archive';
    }

    $duplicate = $jobSeekerModel->checkDuplicateJobSeeker([
        'branch_id'            => $_SESSION['user']['branch_id'],
        'kebele_id_no'         => trim($_POST['kebele_id_no']),
        'g8id'                 => trim($_POST['g8id'] ?? ''),
        'Labor_ID'             => trim($_POST['Labor_ID'] ?? ''),
        'FAN'                  => trim($_POST['FAN'] ?? ''),
        'full_name_normalized' => $normalizedFullName,
        'mothername'           => trim($_POST['mothername']),
        'phone_number'         => trim($_POST['phone_number']),
    ], $excludeId, $excludeTable);

    if (!empty($duplicate)) {

        $triggerType = $duplicate['match_type'] ?? 'identity';

        $triggerTypeMap = [
            'kebele'   => 'kebele_id',
            'g8id'     => 'g8id',
            'labor'    => 'labor_id',
            'fan'      => 'fan',
            'identity' => 'identity',
        ];
        $triggerType = $triggerTypeMap[$triggerType] ?? 'identity';

        $jobSeekerModel->logDuplicateAttempt([
            'id'                     => Uuid::uuid7()->toString(),
            'attempted_by'           => $user['id'],
            'branch_id'              => $branchId,
            'trigger_type'           => $triggerType,
            'matched_jobseeker_id'   => $duplicate['job_seeker_id'] ?? null,
            'matched_source_table'   => $duplicate['source_table'] ?? 'active',
            'attempted_kebele_id_no' => trim($_POST['kebele_id_no']),
            'attempted_g8id'         => trim($_POST['g8id'] ?? ''),
            'attempted_labor_id'     => trim($_POST['Labor_ID'] ?? ''),
            'attempted_fan'          => trim($_POST['FAN'] ?? ''),
            'attempted_full_name'    => $normalizedFullName,
            'attempted_phone_number' => trim($_POST['phone_number']),
            'attempted_mothername'   => trim($_POST['mothername']),
            'ip_address'             => $_SERVER['REMOTE_ADDR'] ?? null,
        ]);

        $branchHierarchy = $jobSeekerModel->getBranchHierarchy($duplicate['branch_id']);

        $_SESSION['error'] =
            "ስራ ፈላጊው ከዚህ በፊት <strong>{$branchHierarchy}</strong> ተመዝግቧል።";

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
        $data = [
            // FIX: edit must reuse the record's actual UUID (id), not the
            // int job_seeker_id — was previously assigned $jobseekerIdInt.
            'uuid'                             => $mode === 'edit' ? $jobseekerId : Uuid::uuid7()->toString(),
            'intid'                            => $mode === 'renewal' ? $jobseekerIdInt : null,
            'created_at'                       => $mode === 'renewal' ? $createdAt : null,
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
            'normalizedFullName'               => $normalizedFullName,
            'choice_sector1' => $resolvedSectors[1]['sector_id'],
            'sub_choose1'    => $resolvedSectors[1]['subsector_id'],
            'choice_sector2' => $resolvedSectors[2]['sector_id'],
            'sub_choose2'    => $resolvedSectors[2]['subsector_id'],
            'choice_sector3' => $resolvedSectors[3]['sector_id'],
            'sub_choose3'    => $resolvedSectors[3]['subsector_id'],
            'housewife'      => (int) ($_POST['housewife'] ?? 0),
        ];

        // FIX: chained as if/elseif/else — previously the renewal check was
        // a SEPARATE if/else from the edit check, so every edit submission
        // ran updateJobseeker() AND THEN createJobseeker() too.
        if ($mode === 'edit') {
            $success     = $jobSeekerModel->updateJobseeker($data);
            $auditAction = 'jobseeker_updated';
            $successMsg  = 'ስራ ፈላጊ መረጃ በትክክል ተስተካክሏል።';
        } elseif ($mode === 'renewal') {
            $success     = $jobSeekerModel->createJobseekerRenewal($data);
            $auditAction = 'jobseeker_renewal';
            $successMsg  = 'ስራ ፈላጊ መረጃ በትክክል ታድሷል';
        } else {
            $success     = $jobSeekerModel->createJobseeker($data);
            $auditAction = 'jobseeker_registered';
            $successMsg  = 'ስራ ፈላጊ በትክክል ተመዝግቧል።';
        }

        if ($success) {
            \App\Helpers\AuditHelper::log($auditAction, 'job_seekers', $data['reg_by'], null, [
                'uuid'        => $data['uuid'],
                'first_name'  => $data['first_name'],
                'father_name' => $data['father_name'],
                'last_name'   => $data['last_name'],
                'branch_id'   => $data['branch_id'],
            ]);

            $_SESSION['success'] = $successMsg;
        } else {
            $_SESSION['error'] = 'ስራ ፈላጊ መመዝገቢያ ሂደት አልተሳካም።';
        }

    } catch (\PDOException $e) {
        error_log("Employee Registration Error: " . $e->getMessage());
        $_SESSION['error'] = "ምዝገባው አልተሳካም። እንደገና ይሞክሩ።";
    }

    if ($mode === 'renewal') {
        header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/jobseekers-renewal");
        exit();
    }
    if ($mode === 'edit') {
        header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/jobseekers-list");
        exit();
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
        'kebele'             => 'የቀበሌ ስም',
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
        '8ኛ ያጠናቀቁ',
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
    $myBranchId = $_SESSION['user']['branch_id'];

    $sectorModel = new SectorModel($this->db);
    $sectors = $sectorModel->getSectors();

    $jobSeekerModel = new JobSeekerModel($this->db);
    $limit = 50;
    $currentPage = max(1, (int)($_GET['page'] ?? 1));
    $offset = ($currentPage - 1) * $limit;

    $jobSeekers = $jobSeekerModel->getJobSeekersByHierarchy($myBranchId, $limit, $offset);
    $totalCount = $jobSeekerModel->countJobSeekersByHierarchy($myBranchId);
    $totalPages = (int)ceil($totalCount / $limit);

    $data = [
        'title'       => 'JCIMS - የሰራተኛ መመዝገቢያ',
        'jobSeekers'  => $jobSeekers,
        'sectors'     => $sectors,
        'offset'      => $offset,        // ADD THIS
        'currentPage' => $currentPage,   // ADD THIS
        'totalPages'  => $totalPages,    // ADD THIS
        'totalCount' => $totalCount
    ];

    $this->render('jobseekers-list', $data);
}
  public function renewalPage() {
    AuthHelper::checkRole(['team_leader', 'officer'], [3, 4]);
    $myBranchId = $_SESSION['user']['branch_id'];
 $sectorModel = new SectorModel($this->db);
    $sectors = $sectorModel->getSectors();

    $fiscal_year =AuthHelper::checkFiscalYear();
    $jobSeekerModel = new JobSeekerModel($this->db);
    $limit = 50;
    $currentPage = max(1, (int)($_GET['page'] ?? 1));
    $offset = ($currentPage - 1) * $limit;

    $jobSeekers = $jobSeekerModel->getJobSeekersByHierarchyRenewal($myBranchId, $limit, $offset,$fiscal_year);
    $totalCount = $jobSeekerModel->countJobSeekersByHierarchyRenewal($myBranchId, $fiscal_year);
    $totalPages = (int)ceil($totalCount / $limit);

    $data = [
        'title'       => 'JCIMS - የሰራተኛ መመዝገቢያ',
        'jobSeekers'  => $jobSeekers,
        'sectors'     => $sectors,
        'offset'      => $offset,        // ADD THIS
        'currentPage' => $currentPage,   // ADD THIS
        'totalPages'  => $totalPages,    // ADD THIS
        'totalCount' => $totalCount
    ];

    $this->render('jobseekers-renewal', $data);
}
    
public function liveSearch(): void
{
    AuthHelper::checkRole(['team_leader', 'officer']);

    $myBranchId = $_SESSION['user']['branch_id'] ?? null;
    if (!$myBranchId) {
        http_response_code(403);
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }

    $query = $_GET['q'] ?? '';
    $query = trim($query);

    header('Content-Type: application/json; charset=utf-8');

    if (mb_strlen($query) < 2) {
        echo json_encode(['results' => []]);
        exit;
    }

    $searchModel = new JobSeekerModel($this->db);
    $results = $searchModel->search($query, $myBranchId, 20);

    echo json_encode([
        'results' => $results,
        'query' => $query,
        'count' => count($results),
    ]);
    exit;
}

public function renewalSearch()
{
    AuthHelper::checkRole(['team_leader', 'officer'], [3, 4]);
 $fiscal_year =AuthHelper::checkFiscalYear();
    $myBranchId = $_SESSION['user']['branch_id'] ?? null;
    if (!$myBranchId) {
        http_response_code(403);
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }

    $query = $_GET['q'] ?? '';
    $query = trim($query);

    header('Content-Type: application/json; charset=utf-8');

    if (mb_strlen($query) < 2) {
        echo json_encode(['results' => []]);
        exit;
    }

    $searchModel = new JobSeekerModel($this->db);
    $results = $searchModel->searchArchive($query, $myBranchId, $fiscal_year,20);

    echo json_encode([
        'results' => $results,
        'query' => $query,
        'count' => count($results),
    ]);
    exit;
}
public function renewalData()
{
    $jobSeekerId = $_GET['job_seeker_id'] ?? null;
    $myBranchId  = $_SESSION['user']['branch_id'] ?? null;

    header('Content-Type: application/json');

    if (!$jobSeekerId || !$myBranchId) {
        echo json_encode(['success' => false, 'message' => 'ልክ ያልሆነ ጥያቄ']);
        return;
    }

    $jobSeekerModel = new JobSeekerModel($this->db);
    $jobseeker = $jobSeekerModel->findArchiveByJobSeekerId($myBranchId, $jobSeekerId);

    if (!$jobseeker) {
        echo json_encode(['success' => false, 'message' => 'ስራ ፈላጊ አልተገኘም']);
        return;
    }

    // Permanently employed job seekers cannot be renewed — block before
    // any remap/population happens.
    if ((int)($jobseeker['employment_status'] ?? 0) === 1) {
        echo json_encode([
            'success' => false,
            'message' => 'ቋሚ የስራ እድል ስለተፈጠረላቸው ማደስ አይችሉም።'
        ]);
        return;
    }

    $jobseeker['choice_sector1'] = $jobseeker['choice_sector1_uuid'] ?? '';
    $jobseeker['sub_choose1']    = $jobseeker['sub_choose1_uuid'] ?? '';
    $jobseeker['choice_sector2'] = $jobseeker['choice_sector2_uuid'] ?? '';
    $jobseeker['sub_choose2']    = $jobseeker['sub_choose2_uuid'] ?? '';
    $jobseeker['choice_sector3'] = $jobseeker['choice_sector3_uuid'] ?? '';
    $jobseeker['sub_choose3']    = $jobseeker['sub_choose3_uuid'] ?? '';

    echo json_encode(['success' => true, 'jobseeker' => $jobseeker]);
}
 public function exportJobSeekersExcel(): void
    {
        AuthHelper::checkRole(['team_leader', 'officer']);

        $myBranchId = $_SESSION['user']['branch_id'] ?? null;
        if (!$myBranchId) {
            http_response_code(403);
            exit('Unauthorized');
        }

        ini_set('memory_limit', '512M');

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Job Seekers');

        $headers = [
            'ስራ ፈላጊ መ/ቁ', 'የተመዘገበበት', 'ስም', 'የአባት ስም', 'የአያት ስም',
            'ጾታ', 'እድሜ', 'የት/ት ደረጃ',
            'የተመረቀበት ሙያ', 'የተመረቁበት ዓመት', 'CGPA', 'የተቋሙ ዓይነት',
            'የጉዳቱ ዓይነት', 'ቀበሌ', 'መንደር', 'የመታወቂያ ቁጥር',
            'ስቁ', 'መጠለያ ሁኔታ', 'የሚኖሩበት አካባቢ',
            'የስራ ፈላጊ ሁኔታ', 'የቤት እመቤት', 'የተመረቁበት ዘርፍ', 'የጋብቻ ሁኔታ', 'የሰሩበት ቦታ', 'የስራ ልምድ', 
            'የሙያ መስክ', 'የሰሩበት ሀገር', 'ቋንቋ',
            'መስራት የሚፈልጉበት', 'Labor ID', 'በጀት ዓመት',
            'የስራ እድል', 'ግንዛቤ', 'የግብርና የስራ ልምድ', 'የሚያስተዳድሩት ቤተሰብ ብዛት',
            'ከ5 ዓመት በታች', 'የተመዘገበበት ቀን',
        ];

        $totalCols = count($headers);
        $lastCol = Coordinate::stringFromColumnIndex($totalCols);

        $sheet->fromArray($headers, null, 'A1');

        $sheet->getStyle("A1:{$lastCol}1")->getFont()->setBold(true);
        $sheet->getStyle("A1:{$lastCol}1")->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('D9E1F2');

        $columnOrder = [
            'job_seeker_id', 'branch_name', 'first_name', 'father_name', 'last_name',
            'gender', 'age', 'educational_level',
            'educated_dpt', 'education_trmnet_finsh_year', 'CGPA', 'school_type',
             'physical_condition_desc', 'kebele', 'mender', 'kebele_id_no',
            'phone_number', 'meteleya_huneta', 'residence_status',
            'srafelagi_huneta', 'housewife', 'graguation_catagory', 'maritalstatus',
             'workplace', 'experience', 'profession', 'nameofcountry', 'language',
            'wageorself', 'Labor_ID', 'fiscal_year',
            'employment_status', 'awareness',
            'agri_business_experience', 'number_of_dependents',
            'children_under_five', 'created_at',
        ];

        $model = new JobSeekerModel($this->db);
        $rowNum = 2;
        $count = 0;

        foreach ($model->streamJobSeekersByHierarchy($myBranchId) as $row) {
    $row = $this->formatRowForExport($row); // <-- added

    $values = array_map(fn($col) => $row[$col] ?? '', $columnOrder);
    $sheet->fromArray($values, null, 'A' . $rowNum);
    $rowNum++;
    $count++;
}
        if ($count < 5000) {
            for ($i = 1; $i <= $totalCols; $i++) {
                $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($i))->setAutoSize(true);
            }
        } else {
            for ($i = 1; $i <= $totalCols; $i++) {
                $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($i))->setWidth(16);
            }
        }

        $sheet->freezePane('A2');

        $filename = 'job_seekers_export_' . date('Y-m-d_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');

        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);

        \App\Helpers\AuditHelper::log(
            action: 'job_seekers_exported',
            entityType: 'job_seeker',
            entityId: null,
            oldValues: null,
            newValues: null,
            metadata: ['branch_id' => $myBranchId, 'row_count' => $count, 'format' => 'xlsx']
        );

        exit;
    }
    private function formatRowForExport(array $row): array
{
    if (isset($row['employment_status'])) {
        $row['employment_status'] = match ((string) $row['employment_status']) {
            '1' => 'ቋሚ',
            '0' => 'ያልተፈጠረለት',
            '2' => 'ጊዜያዊ',
            default => $row['employment_status'],
        };
    }

    if (isset($row['awareness'])) {
        $row['awareness'] = match ((string) $row['awareness']) {
            '1' => 'የተፈጠረላቸው',
            '0' => 'ያልተፈጠረላቸው',
            default => $row['awareness'],
        };
    }

    return $row;
}
    }