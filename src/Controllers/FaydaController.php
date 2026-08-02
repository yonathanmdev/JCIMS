<?php
declare(strict_types=1);

namespace App\Controllers;

use PDO;
use App\Services\FaydaHandoffService;
use App\Models\JobSeekerModel;
use App\Helpers\AuditHelper;
use App\Helpers\AuthHelper;

class FaydaController extends BaseController
{
    private FaydaHandoffService $handoffService;

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

        $profile = $_SESSION['fayda_profile'];
        $type = $_SESSION['fayda_registration_type'] ?? 'new';
        $verified = $_SESSION['fayda_verified_record'] ?? null;

        $data = [
            'fayda_sub'        => $profile['sub'] ?? null,
            'full_name'        => $_POST['full_name']  ?? '',
            'phone'            => $_POST['phone']      ?? '',
            'gender'           => $_POST['gender']     ?? '',
            'birthdate'        => $_POST['birthdate']  ?? '',
            'education_level'  => $_POST['education_level'] ?? '',
            'sector_id'        => $_POST['sector_id']        ?? null,
        ];

        $model = new JobSeekerModel($this->db);

        $result = ($type === 'renewal' && $verified !== null)
            ? $model->updateExisting((int) $verified['job_seeker_id'], $data)
            : $model->createNew($data);

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

        unset($_SESSION['fayda_profile'], $_SESSION['fayda_registration_type'], $_SESSION['fayda_verified_record']);

        header('Location: ' . rtrim($_ENV['BASE_URL'], '/') . '/dashboard?registered=1');
        exit;
    }

    /** action=fayda-error */
    public function showError(): void
    {
        $reason = $_GET['reason'] ?? 'unknown';
        $this->renderwithoutlogin('fayda-error', ['reason' => $reason]);
    }
}