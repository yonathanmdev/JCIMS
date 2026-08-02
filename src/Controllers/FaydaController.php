<?php
declare(strict_types=1);

namespace App\Controllers;

use PDO;
use App\Services\FaydaHandoffService;
use App\Models\JobSeekerModel;
use App\Helpers\AuditHelper;

class FaydaController extends BaseController
{
    private FaydaHandoffService $handoffService;

    public function __construct(PDO $db)
    {
        parent::__construct($db); // sets $this->db AND calls AuditHelper::init($db)
        $this->handoffService = new FaydaHandoffService($db);
    }

    /** action=fayda-start */
    public function start(): void
    {
        $data = [
            'title' => 'JCIMS - የፋይዳ መረጃ አስመዝግብ',
        ];
        $this->render('fayda-start', $data);
    }

    /** action=fayda-redirect&id_number=... */
    public function redirect(): void
    {
        $idNumber = trim($_GET['id_number'] ?? '');

        if ($idNumber === '') {
            header('Location: ' . rtrim($_ENV['BASE_URL'], '/') . '/fayda-start');
            exit;
        }

        $_SESSION['fayda_id_number'] = $idNumber;

        AuditHelper::log(
            action: 'fayda_flow_started',
            entityType: 'job_seeker',
            entityId: null,
            oldValues: null,
            newValues: null,
            metadata: ['id_number' => $idNumber]
        );

        header('Location: https://nid.bols.gov.et/callback.php?action=login&system=jcims&id_number=' . urlencode($idNumber));
        exit;
    }

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

        $data = [
            'title' => 'JCIMS - የፋይዳ መረጃ አስመዝግብ',
            'existing' => $existing
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

        $data = [
            'title' => 'JCIMS - የፋይዳ መረጃ አስመዝግብ'
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

    /** action=fayda-error */
    public function showError(): void
    {
        $reason = $_GET['reason'] ?? 'unknown';
        $this->renderwithoutlogin('fayda-error', ['reason' => $reason]);
    }
}