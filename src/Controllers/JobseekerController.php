<?php
namespace App\Controllers;
use App\Helpers\AuthHelper;
use App\Helpers\AmharicNormalizer;
use App\Models\JobSeekerModel;
use App\Models\SectorModel;
use Ramsey\Uuid\Uuid;
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
    $fullNameRaw = $_POST['first_name'] . ' ' . $_POST['father_name'] . ' ' . $_POST['g_father_name'];
        $normalizedFullName = AmharicNormalizer::normalize($fullNameRaw);

         // ── Persist ───────────────────────────────────────────────────────
        $jobSeekerModel = new JobSeekerModel($this->db);
        // 3. Duplicate check
     $duplicate = $jobSeekerModel->checkDuplicateJobSeeker([
    'branch_id'            => $_SESSION['user']['branch_id'],
    'kebele_id_no'         => trim($_POST['kebele_id_no']),
    'g8id'                 => trim($_POST['gradeEight'] ?? ''),
    'Labor_ID'             => trim($_POST['Labor_ID'] ?? ''),
    'FAN'                  => trim($_POST['FAN'] ?? ''),
    'full_name_normalized' => $normalizedFullName,
    'mothername'           => trim($_POST['mothername']),
    'phone_number'         => trim($_POST['phone_number']),
]);

if (!empty($duplicate)) {

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
            'last_name'                        => trim($_POST['g_father_name'] ?? ''),
            'gender'                           => trim($_POST['gender'] ?? ''),
            'age'                              => $_POST['age'] ?? '',
            'educational_level'                => trim($_POST['educational_level'] ?? ''),
            'education_level_catagory'         => $educationLevelCategory,
            'educated_dpt'                     => trim($_POST['dpt'] ?? ''),
            'education_trmnet_finsh_year'      => $_POST['education_completion_year'] ?? '',
            'g8id'                             => trim($_POST['gradeEight'] ?? ''),
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
        'g_father_name'      => 'የአያት ስም',
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
    if (!preg_match('/^[\p{L}\s]+$/u', $get('g_father_name'))) {
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
    $dptLevels = ['ደረጃ 2', 'ደረጃ 3', 'ደረጃ 4', 'ደረጃ 5', 'የመጀመሪያ ዲግሪ', 'ሁለተኛ ዲግሪ'];
    $gradeEightLevels = array_merge(
        ['8ኛ ያጠናቀቁ', 'ከ9-10ኛ', 'ከ11-12ኛ'],
        $dptLevels
    );

    if (!in_array($eduLevel, $hideSchoolFor, true) && $get('education_completion_year') === '') {
        $errors[] = 'የትምህርት ማጠናቀቂያ ዓመት ያስፈልጋል።';
    }

    if (in_array($eduLevel, $dptLevels, true)) {
        if ($get('dpt') === '') {
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

    if (in_array($eduLevel, $gradeEightLevels, true) && $get('gradeEight') === '') {
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
        if ($get('workplace') === 'ከውጭ አገር' && $get('language') === '') {
            $errors[] = 'ቋንቋ ያስፈልጋል።';
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
    }