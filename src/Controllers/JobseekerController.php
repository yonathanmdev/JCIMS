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
$sectorModel = new SectorModel($this->db);
$sectors  = $sectorModel->getSectors();
        $data = [
            'title' => 'JCIMS - የሰራተኛ መመዝገቢያ',
            'sectors' => $sectors
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