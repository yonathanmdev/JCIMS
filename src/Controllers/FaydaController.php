<?php
declare(strict_types=1);

namespace App\Controllers;

use PDO;
use App\Services\FaydaHandoffService;
use App\Models\JobSeekerModel;
use App\Helpers\AuditHelper;

class FaydaController
{
    private PDO $db;
    private FaydaHandoffService $handoffService;

    public function __construct(PDO $db)
    {
        $this->db = $db;
        $this->handoffService = new FaydaHandoffService($db);
    }

    /** GET ?action=fayda-start — shows the "enter ID" form */
    public function start(): void
    {
        require __DIR__ . '/../../views/fayda-start.php';
    }

    /** GET ?action=fayda-redirect&id_number=... — stashes the ID, sends to bridge */
   public function redirect(): void
{
    $idNumber = trim($_GET['id_number'] ?? '');

    if ($idNumber === '') {
        header('Location: ' . rtrim($_ENV['BASE_URL'], '/') . '/fayda-start');
        exit;
    }

    $_SESSION['fayda_id_number'] = $idNumber;

    // TODO: re-enable once AuditHelper::init() is confirmed in bootstrap
    // AuditHelper::log(
    //     action: 'fayda_flow_started',
    //     ...
    // );

    header('Location: https://nid.bols.gov.et/callback.php?action=login&id_number=' . urlencode($idNumber));
    exit;
}

    /** GET ?action=fayda-verify&token=... */
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
        // prefer the id_number carried through the handoff row; fall back to session
        $_SESSION['fayda_id_number'] = $result['job_seeker_id'] ?? ($_SESSION['fayda_id_number'] ?? null);

        AuditHelper::log(
            action: 'fayda_handoff_consumed',
            entityType: 'job_seeker',
            entityId: null,
            oldValues: null,
            newValues: null,
            metadata: ['fayda_sub' => $result['profile']['sub'] ?? null]
        );

        $jobSeekerModel = new JobSeekerModel($this->db);
        $existing = $jobSeekerModel->findByFaydaSub($result['profile']['sub'] ?? '');

        require __DIR__ . '/../../views/fayda-compare.php';
    }

    /** GET ?action=fayda-confirm */
    public function confirm(): void
    {
        if (!isset($_SESSION['fayda_profile'])) {
            header('Location: ' . rtrim($_ENV['BASE_URL'], '/') . '/fayda-error?reason=no_profile_in_session');
            exit;
        }

        require __DIR__ . '/../../views/form.php';
    }

    /** POST ?action=fayda-register */
    public function register(): void
    {
        if (!isset($_SESSION['fayda_profile'])) {
            header('Location: ' . rtrim($_ENV['BASE_URL'], '/') . '/fayda-error?reason=no_profile_in_session');
            exit;
        }

        $profile = $_SESSION['fayda_profile'];

        $data = [
            'fayda_sub'        => $profile['sub'] ?? null,
            'id_number'        => $_SESSION['fayda_id_number'] ?? null,
            'full_name'        => $_POST['full_name']  ?? '',
            'phone'            => $_POST['phone']      ?? '',
            'gender'           => $_POST['gender']     ?? '',
            'birthdate'        => $_POST['birthdate']  ?? '',
            'education_level'  => $_POST['education_level'] ?? '',
            'sector_id'        => $_POST['sector_id']        ?? null,
        ];

        $model = new JobSeekerModel($this->db);
        $result = $model->createOrUpdateFromFayda($data);

        if ($result['status'] !== true) {
            $_SESSION['form_error'] = $result['message'] ?? 'ምዝገባ አልተሳካም';
            header('Location: ' . rtrim($_ENV['BASE_URL'], '/') . '/fayda-confirm');
            exit;
        }

        AuditHelper::log(
            action: 'job_seeker_registered_via_fayda',
            entityType: 'job_seeker',
            entityId: $result['job_seeker_id'],
            oldValues: null,
            newValues: $data,
            metadata: ['fayda_sub' => $data['fayda_sub'], 'id_number' => $data['id_number']]
        );

        unset($_SESSION['fayda_profile'], $_SESSION['fayda_id_number']);

        header('Location: ' . rtrim($_ENV['BASE_URL'], '/') . '/dashboard?registered=1');
        exit;
    }

    /** GET ?action=fayda-error */
    public function showError(): void
    {
        $reason = $_GET['reason'] ?? 'unknown';
        require __DIR__ . '/../../views/fayda-error.php';
    }
}