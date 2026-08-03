<?php
declare(strict_types=1);

namespace App\Controllers;

use PDO;
use App\Services\FaydaHandoffService;
use App\Models\JobSeekerModel;
use App\Models\SectorModel;
use App\Helpers\AuditHelper;
use App\Helpers\AuthHelper;
use App\Helpers\AmharicNormalizer;
use Ramsey\Uuid\Uuid;
use Exception;


class FaydaController extends BaseController
{
    private FaydaHandoffService $handoffService;
    private const ENABLE_LEGACY_DUPLICATE_CHECK = true;
  private const NAME_ONLY_PATTERN         = '/^\p{L}*$/u';
    private const TEXT_WITH_SPACES_PATTERN  = '/^[\p{L}]+(\s[\p{L}]+)*$/u';
    private const NUMERIC_PATTERN           = '/^\d*$/';
    private const GENERAL_SAFE_PATTERN      = '/^[\p{L}\d\-\/\s፣]*$/u';
    private const DECIMAL_PATTERN           = '/^\d+(\.\d+)?$/';

    private const AGRI_LABEL = 'ግብርና';
    public function __construct(PDO $db)
    {
        parent::__construct($db); // sets $this->db AND calls AuditHelper::init($db)
        $this->handoffService = new FaydaHandoffService($db);
    }

    /** action=fayda-start — single page, new/renewal radio toggle */
    public function start(): void
    {
        $data = [
            'title' => 'JCIMS - የፋይዳ መረጃ አስመዝግብ',
        ];
        $this->render('fayda-start', $data);
    }

    /**
     * action=fayda-redirect
     * GET params: registration_type=new|renewal, job_seeker_id (renewal only)
     */
    public function redirect(): void
    {
        $type = $_GET['registration_type'] ?? '';
        if ($type === 'new') {
            $this->proceedToFayda('new', null);
            return;
        }

        if ($type === 'renewal') {
            
            $jobSeekerIdRaw = $_GET['job_seeker_id'] ?? '';
            if (!ctype_digit((string) $jobSeekerIdRaw)) {
                header('Location: ' . rtrim($_ENV['BASE_URL'], '/') . '/fayda-error?reason=job_seeker_not_found');
                exit;
            }

            AuthHelper::checkRole(['team_leader', 'officer'], [3, 4]);
            $fiscalYear = AuthHelper::checkFiscalYear();
            $branchId = $_SESSION['user']['branch_id'] ?? null;

            if (!$branchId) {
                http_response_code(403);
                die('Unauthorized');
            }

            $model = new JobSeekerModel($this->db);
            $verified = $model->findExistingForRenewal((int) $jobSeekerIdRaw, (int) $branchId, (int) $fiscalYear);

            if ($verified === null) {
                AuditHelper::log(
                    action: 'fayda_renewal_verification_failed',
                    entityType: 'job_seeker',
                    entityId: (string) $jobSeekerIdRaw,
                    oldValues: null,
                    newValues: null,
                    metadata: []
                );
                header('Location: ' . rtrim($_ENV['BASE_URL'], '/') . '/fayda-error?reason=job_seeker_not_found');
                exit;
            }

            $this->proceedToFayda('renewal', $verified);
            return;
        }

        header('Location: ' . rtrim($_ENV['BASE_URL'], '/') . '/fayda-start');
        exit;
    }

    private function proceedToFayda(string $type, ?array $verifiedRecord): void
    {
        $_SESSION['fayda_registration_type'] = $type;
        $_SESSION['fayda_verified_record']   = $verifiedRecord; // full DB row, or null for new
        $_SESSION['id_number']         = $verifiedRecord['job_seeker_id'] ?? null;
        AuditHelper::log(
            action: 'fayda_flow_started',
            entityType: 'job_seeker',
            entityId: isset($verifiedRecord['job_seeker_id']) ? (string) $verifiedRecord['job_seeker_id'] : null,
            oldValues: null,
            newValues: null,
            metadata: ['registration_type' => $type]
        );

        $carryId = $verifiedRecord['job_seeker_id'] ?? '';
        header('Location: https://nid.bols.gov.et/callback.php?action=login&system=jcims&id_number=' . urlencode((string) $carryId));
        exit;
    }

    /** action=fayda-verify/{token} */
    /** action=fayda-verify/{token} */
public function verify(array $params = []): void
{
    $token = $params['uuid'] ?? null;

    if ($token === null) {
        header('Location: ' . rtrim($_ENV['BASE_URL'], '/') . '/fayda-error?reason=missing_token');
        exit;
    }

    $result = $this->handoffService->consume($token);

    if ($result === null) {
        AuditHelper::log(
            action: 'fayda_handoff_consume_failed',
            entityType: 'job_seeker',
            entityId: null,
            oldValues: null,
            newValues: null,
            metadata: ['token_prefix' => substr($token, 0, 8)]
        );
        header('Location: ' . rtrim($_ENV['BASE_URL'], '/') . '/fayda-error?reason=token_invalid_or_expired');
        exit;
    }

    $_SESSION['fayda_profile'] = $result['profile'];

    // Re-derive the verified record independently of whatever survived
    // in session — the handoff row is the only thing guaranteed to
    // have crossed the domain boundary intact.
    $carriedJobSeekerId = $result['job_seeker_id'] ?? null;
    $_SESSION['id_number']         = $result['job_seeker_id'] ?? null;
    $verified = null;

    if ($carriedJobSeekerId !== null && ctype_digit((string) $carriedJobSeekerId)) {
        AuthHelper::checkRole(['team_leader', 'officer'], [3, 4]);
        $fiscalYear = AuthHelper::checkFiscalYear();
        $branchId = $_SESSION['user']['branch_id'] ?? null;

        if ($branchId) {
            $model = new JobSeekerModel($this->db);
            $verified = $model->findExistingForRenewal((int) $carriedJobSeekerId, (int) $branchId, (int) $fiscalYear);
        }
    }

    $_SESSION['fayda_registration_type'] = $verified !== null ? 'renewal' : 'new';
    $_SESSION['fayda_verified_record']   = $verified;

    AuditHelper::log(
        action: 'fayda_handoff_consumed',
        entityType: 'job_seeker',
        entityId: $verified !== null ? (string) $verified['job_seeker_id'] : null,
        oldValues: null,
        newValues: null,
        metadata: ['fayda_sub' => $result['profile']['sub'] ?? null]
    );

    $data = [
        'title'    => 'JCIMS - የፋይዳ መረጃ አስመዝግብ',
        'existing' => $verified,
    ];
    $this->render('fayda-compare', $data);
}

    /** action=fayda-confirm */
    public function confirm(): void
    {
        if (!isset($_SESSION['fayda_profile'])) {
            header('Location: ' . rtrim($_ENV['BASE_URL'], '/') . '/fayda-error?reason=no_profile_in_session');
            exit;
        }
$sectorModel = new \App\Models\SectorModel($this->db);
$sectors  = $sectorModel->getSectors();
        
        $data = [
            'title' => 'JCIMS - የፋይዳ መረጃ አስመዝግብ',
            'sectors' => $sectors

        ];
        $this->render('fayda-form', $data);
    }

    /** POST action=fayda-register */
   public function register(): void
{
    if (!isset($_SESSION['fayda_profile'])) {
        header('Location: ' . rtrim($_ENV['BASE_URL'], '/') . '/fayda-error?reason=no_profile_in_session');
        exit;
    }

    $profile  = $_SESSION['fayda_profile'];
    $type     = $_SESSION['fayda_registration_type'] ?? 'new';
    $verified = $_SESSION['fayda_verified_record'] ?? null;

    if ($type === 'renewal' && empty($verified['job_seeker_id'])) {
        $_SESSION['form_error'] = 'ስህተት፦ የስራ ፈላጊ መለያ አልተገኘም።';
        header('Location: ' . rtrim($_ENV['BASE_URL'], '/') . '/fayda-confirm');
        exit;
    }

    $nameAm = $profile['name#am'] ?? '';
    $nameParts = array_values(array_filter(explode(' ', trim($nameAm))));

    $faydaAge = '';
    $birthdate = $profile['birthdate'] ?? '';
    if ($birthdate !== '') {
        $bd = \DateTime::createFromFormat('Y/m/d', $birthdate);
        if ($bd) {
            $faydaAge = (string) $bd->diff(new \DateTime())->y;
        }
    }

    $faydaData = [
        'fayda_sub'       => $profile['sub'] ?? null,
        'first_name'      => $nameParts[0] ?? '',
        'father_name'     => $nameParts[1] ?? '',
        'last_name'       => $nameParts[2] ?? '',
        'gender'          => $profile['gender#am'] ?? '',
        'phone_number'    => $profile['phone_number'] ?? '',
        'fayda_id_number' => $_SESSION['job_seeker_id'] ?? '',
        'birthdate'       => $birthdate,
        'age'             => $faydaAge,
    ];

    $errors = $this->validate($_POST, $faydaData['gender']);
    if (!empty($errors)) {
        $_SESSION['form_error'] = implode(' | ', $errors);
        header('Location: ' . rtrim($_ENV['BASE_URL'], '/') . '/fayda-confirm');
        exit;
    }

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
            $_SESSION['form_error'] = "የተመረጠው ሙያ {$sectorId} መረጃ አልተገኘም።";
            header('Location: ' . rtrim($_ENV['BASE_URL'], '/') . '/fayda-confirm');
            exit;
        }

        $resolvedSectors[$sectorId] = [
            'sector_id'    => $ids['sectorid'],
            'subsector_id' => $ids['sub_sectorid'],
        ];
    }

    $educationLevelCategory = $this->resolveEducationLevelCategory(
        trim($_POST['educational_level'] ?? '')
    );

    $data = array_merge($faydaData, [
        'branch_id'        => $_SESSION['user']['branch_id'] ?? null,
        'srafelagi_huneta' => $_POST['srafelagi_huneta'] ?? '',
        'Labor_ID'         => $_POST['Labor_ID'] ?? null,
        'maritalstatus'    => $_POST['maritalstatus'] ?? '',
        'kebele'           => $_POST['kebele'] ?? '',
        'housewife'        => $_POST['housewife'] ?? null,
        'mender'           => $_POST['mender'] ?? null,
        'kebele_id_no'     => $_POST['kebele_id_no'] ?? '',
        'residence_status' => $_POST['residence_status'] ?? '',

        'educational_level'           => $_POST['educational_level'] ?? '',
        'education_level_catagory'    => $educationLevelCategory,
        'school_type'                 => $_POST['school_type'] ?? null,
        'educated_dpt'                => $_POST['educated_dpt'] ?? null,
        'education_trmnet_finsh_year' => $_POST['education_trmnet_finsh_year'] ?? null,
        'g8id'                        => $_POST['g8id'] ?? null,
        'graguation_catagory'         => $_POST['graguation_catagory'] ?? null,
        'CGPA'                        => $_POST['CGPA'] ?? null,
        'meteleya_huneta'             => $_POST['meteleya_huneta'] ?? '',
        'physical_condition'          => $_POST['physical_condition'] ?? '',
        'physical_condition_desc'     => $_POST['physical_condition_desc'] ?? null,
        'haveexp'                     => $_POST['haveexp'] ?? '',
        'experience'                  => $_POST['experience'] ?? null,
        'workplace'                   => $_POST['workplace'] ?? null,
        'profession'                  => $_POST['profession'] ?? null,
        'nameofcountry'               => $_POST['nameofcountry'] ?? null,
        'language'                    => $_POST['language'] ?? null,
        'wageorself'                  => $_POST['wageorself'] ?? '',
        'mothername'                  => $_POST['mothername'] ?? '',

        'sector1_id' => $resolvedSectors[1]['sector_id'],
        'subsector1_id' => $resolvedSectors[1]['subsector_id'],
        'sector2_id' => $resolvedSectors[2]['sector_id'],
        'subsector2_id' => $resolvedSectors[2]['subsector_id'],
        'sector3_id' => $resolvedSectors[3]['sector_id'],
        'subsector3_id' => $resolvedSectors[3]['subsector_id'],

        'agri_business_experience_status' => $_POST['agri_business_experience_status'] ?? null,
        'agri_business_experience'        => $_POST['agri_business_experience'] ?? null,
        'has_dependents'                  => $_POST['has_dependents'] ?? null,
        'number_of_dependents'            => $_POST['number_of_dependents'] ?? null,
        'children_under_five'             => $_POST['children_under_five'] ?? null,
    ]);

    $data['uuid'] = Uuid::uuid7()->toString();

    if ($type === 'renewal' && $verified !== null) {
        $data['job_seeker_id'] = (int) $verified['job_seeker_id'];
    }

    $model = new JobSeekerModel($this->db);

    // ── Exclude-self scoping for duplicate checks ──────────────────────
    $excludeId    = ($type === 'renewal' && $verified !== null) ? (int) $verified['job_seeker_id'] : null;
    $excludeTable = ($type === 'renewal' && $verified !== null) ? 'archive' : null;

    // ── Permanent check: fayda_sub uniqueness ───────────────────────────
    $faydaSubCheck = $model->checkDuplicateFaydaSub(
        $data['fayda_sub'] ?? '',
        $excludeId,
        $excludeTable
    );

    if (!empty($faydaSubCheck)) {
        $_SESSION['form_error'] = 'ይህ የፋይዳ መታወቂያ ቀድሞ ተመዝግቧል።';
        header('Location: ' . rtrim($_ENV['BASE_URL'], '/') . '/fayda-confirm');
        exit;
    }

    // ── TEMPORARY: legacy field-based check — remove once all job ───────
    // ── seekers are expected to have fayda_sub on file.                ──
    if (self::ENABLE_LEGACY_DUPLICATE_CHECK) {
        $fullNameRaw        = $data['first_name'] . ' ' . $data['father_name'] . ' ' . $data['last_name'];
        $normalizedFullName = AmharicNormalizer::normalize($fullNameRaw);
        $data['full_name_normalized'] = $normalizedFullName;

        $legacyCheck = $model->checkDuplicateFaydaLegacyFields([
            'branch_id'            => $_SESSION['user']['branch_id'] ?? null,
            'kebele_id_no'         => $data['kebele_id_no'],
            'g8id'                 => $data['g8id'] ?? '',
            'Labor_ID'             => $data['Labor_ID'] ?? '',
            'full_name_normalized' => $normalizedFullName,
            'phone_number'         => $data['phone_number'],
            'mothername'           => $data['mothername'],
        ], $excludeId, $excludeTable);

        if (!empty($legacyCheck)) {
            $_SESSION['form_error'] = 'ተመሳሳይ መረጃ ያለው ስራ ፈላጊ ቀድሞ ተመዝግቧል።';
            header('Location: ' . rtrim($_ENV['BASE_URL'], '/') . '/fayda-confirm');
            exit;
        }
    } else {
        // still need full_name_normalized stored even if not used for matching
        $fullNameRaw = $data['first_name'] . ' ' . $data['father_name'] . ' ' . $data['last_name'];
        $data['full_name_normalized'] = AmharicNormalizer::normalize($fullNameRaw);
    }

    $data['reg_by'] = $_SESSION['user']['id'] ?? null;

    $result = $model->createJobseekerwithFayda($data);

    if ($result['status'] !== true) {
        $_SESSION['form_error'] = $result['message'] ?? 'ምዝገባ አልተሳካም';
        header('Location: ' . rtrim($_ENV['BASE_URL'], '/') . '/fayda-confirm');
        exit;
    }

    AuditHelper::log(
        action: $type === 'renewal' ? 'job_seeker_renewed_via_fayda' : 'job_seeker_registered_via_fayda',
        entityType: 'job_seeker',
        entityId: (string) $result['job_seeker_id'],
        oldValues: null,
        newValues: $data,
        metadata: ['fayda_sub' => $data['fayda_sub'], 'registration_type' => $type]
    );

    unset(
        $_SESSION['fayda_profile'],
        $_SESSION['fayda_registration_type'],
        $_SESSION['fayda_verified_record'],
        $_SESSION['job_seeker_id']
    );

    header('Location: ' . rtrim($_ENV['BASE_URL'], '/') . '/dashboard?registered=1');
    exit;
}
private function validate(array $post, string $gender): array
    {
        $errors = [];

        $required = function (string $field, string $label) use ($post, &$errors) {
            if (trim((string) ($post[$field] ?? '')) === '') {
                $errors[] = "{$label} መሞላት አለበት።";
            }
        };

        $pattern = function (string $field, string $regex, string $label) use ($post, &$errors) {
            $value = $post[$field] ?? '';
            if ($value !== '' && !preg_match($regex, $value)) {
                $errors[] = "{$label} ልክ ያልሆነ ቅርጸት ይዟል።";
            }
        };

        // ── Step 1 — always required ──
        $required('srafelagi_huneta', 'የስራ ፈላጊ ሁኔታ');
        $required('maritalstatus', 'የጋብቻ ሁኔታ');
        $required('kebele', 'ቀበሌ');
        $required('kebele_id_no', 'የቀበሌ መታወቂያ ቁጥር');
        $required('residence_status', 'የሚኖርበት አካባቢ');

        $pattern('Labor_ID', self::GENERAL_SAFE_PATTERN, 'Labor_ID');
        if (!empty($post['Labor_ID']) && strlen($post['Labor_ID']) !== 10) {
            $errors[] = 'Labor_ID በትክክል 10 ፊደል/ቁጥር መሆን አለበት።';
        }

        // housewife: required only for female applicants
        if ($gender === 'ሴት') {
            $required('housewife', 'የቤት እመቤት');
        }

        // ── Step 2 ──
        $required('educational_level', 'የትምህርት ደረጃ');
        $required('meteleya_huneta', 'የመኖሪያ ቤት ሁኔታ');
        $required('physical_condition', 'የአካል ጉዳት');
        $required('haveexp', 'ከዚህ ቀደም የስራ ልምድ');
        $required('wageorself', 'አሁን መስራት የሚፈልጉት');
        $required('mothername', 'የእናት ሙሉ ስም');
        $pattern('mothername', self::TEXT_WITH_SPACES_PATTERN, 'የእናት ሙሉ ስም');

        $eduLevel = $post['educational_level'] ?? '';
        $hideFor = ['ማንበብና መፃፍ የማይችሉ', 'መሰረተ ትምህርት'];
        $educatedDptLevels = ['ደረጃ 2', 'ደረጃ 3', 'ደረጃ 4', 'ደረጃ 5', 'የመጀመሪያ ዲግሪ', 'ሁለተኛ ዲግሪ'];
        $g8idLevels = ['8ኛ ያጠናቀቁ', 'ከ9-10ኛ', 'ከ11-12ኛ', 'ደረጃ 2', 'ደረጃ 3', 'ደረጃ 4', 'ደረጃ 5', 'የመጀመሪያ ዲግሪ', 'ሁለተኛ ዲግሪ'];

        if (!in_array($eduLevel, $hideFor, true)) {
            $required('education_trmnet_finsh_year', 'ትምህርት ያጠናቀቀበት ዓመት');
        }

        if (in_array($eduLevel, $educatedDptLevels, true)) {
            $required('educated_dpt', 'የተመረቀበት ዲፓርትመንት');
            $required('CGPA', 'CGPA');
            $required('school_type', 'የት/ቤቱ/የኮሌጁ ዓይነት');
            $pattern('educated_dpt', self::TEXT_WITH_SPACES_PATTERN, 'የተመረቀበት ዲፓርትመንት');
            $pattern('CGPA', self::DECIMAL_PATTERN, 'CGPA');
            if (!empty($post['CGPA']) && strlen($post['CGPA']) !== 4) {
                $errors[] = 'CGPA በትክክል 4 digits መሆን አለበት።';
            }
        }

        if (in_array($eduLevel, $g8idLevels, true)) {
            $required('g8id', 'የ8ኛ ክፍል መለያ ቁጥር');
            $pattern('g8id', self::NUMERIC_PATTERN, 'የ8ኛ ክፍል መለያ ቁጥር');
        }

        if (($post['physical_condition'] ?? '') === '1') {
            $required('physical_condition_desc', 'የአካል ጉዳቱ አይነት');
        }

        if (($post['haveexp'] ?? '') === '1') {
            $required('workplace', 'የሰሩበት ሀገር');
            $required('profession', 'የሰሩበት የሙያ መደብ');
            $pattern('profession', self::GENERAL_SAFE_PATTERN, 'የሰሩበት የሙያ መደብ');

            if (($post['workplace'] ?? '') === 'ከውጭ አገር') {
                $required('nameofcountry', 'የሀገሩ ስም');
                $pattern('nameofcountry', self::TEXT_WITH_SPACES_PATTERN, 'የሀገሩ ስም');
            }
        }

        // ── Step 3 ──
        $required('choice_sector1', 'የዘርፍ ምርጫ 1');
        $required('sub_choose1', 'የንዑስ ዘርፍ ምርጫ 1');
        $required('choice_sector2', 'የዘርፍ ምርጫ 2');
        $required('sub_choose2', 'የንዑስ ዘርፍ ምርጫ 2');
        $required('choice_sector3', 'የዘርፍ ምርጫ 3');
        $required('sub_choose3', 'የንዑስ ዘርፍ ምርጫ 3');

        // agri status required only if any chosen sector's *label* is "ግብርና"
        // NOTE: needs sector-id → label lookup since we only have IDs server-side.
        $sectorIds = array_filter([
            $post['choice_sector1'] ?? null,
            $post['choice_sector2'] ?? null,
            $post['choice_sector3'] ?? null,
        ]);
        if (!empty($sectorIds) && $this->anySectorIsAgriculture($sectorIds)) {
            $required('agri_business_experience_status', 'በግብርና ዘርፍ ልምድ');
            if (($post['agri_business_experience_status'] ?? '') === '1') {
                $required('agri_business_experience', 'በግብርና ዘርፍ ያለው ልምድ');
            }
        }

        // dependents block: required only for female applicants
        if ($gender === 'ሴት') {
            $required('has_dependents', 'በስር የሚተዳደር ቤተሰብ');
            if (($post['has_dependents'] ?? '') === '1') {
                $required('number_of_dependents', 'የሚተዳደረው ቤተሰብ ብዛት');

                $maxDependents = (int) ($post['number_of_dependents'] ?? 0);
                $children = (int) ($post['children_under_five'] ?? 0);
                if (($post['children_under_five'] ?? '') !== '' && $children > $maxDependents) {
                    $errors[] = "ከ5 ዓመት በታች ያሉ ልጆች ቁጥር ከጠቅላላ ቤተሰብ ብዛት ({$maxDependents}) መብለጥ አይችልም።";
                }
            }
        }

        return $errors;
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
private function anySectorIsAgriculture(array $sectorIds): bool
{
    $sectorModel = new SectorModel($this->db);

    foreach ($sectorIds as $id) {
        if ($id === null || $id === '') {
            continue;
        }

        $sector = $sectorModel->getSectorById((string) $id);

        if ($sector !== null && trim($sector['sector']) === self::AGRI_LABEL) {
            return true;
        }
    }

    return false;
}
    /** action=fayda-error */
    public function showError(): void
    {
        $reason = $_GET['reason'] ?? 'unknown';
        $this->renderwithoutlogin('fayda-error', ['reason' => $reason]);
    }
}