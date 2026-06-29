<?php
namespace App\Controllers;
use App\Helpers\AuthHelper;
use App\Models\SectorModel;
use Ramsey\Uuid\Uuid;

class SectorController extends BaseController {

    public function showRegisterForm() {
        // NOTE: confirm this should match the role in handleSectorRegistration() below.
        // Currently 'system_admin' here vs 'officer' there — pick one.
        AuthHelper::checkRole(['system_admin']);
$sectorModel = new SectorModel($this->db);
$sectors  = $sectorModel->getSectors();
        $data = [
            'title' => 'JCIMS - የዘርፍ መመዝገቢያ',
            'sectors' => $sectors,
        ];

        $this->render('sector-registration', $data);
    }

    public function handleSectorRegistration() {
        AuthHelper::checkRole(['system_admin']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/sector-registration");
            exit();
        }

        $user = $_SESSION['user'] ?? [];

        // ── 1. Server-side validation ────────────────────────────────────────
        $validationErrors = $this->validateSectorData($_POST);
        if (!empty($validationErrors)) {
            $_SESSION['error'] = implode('<br>', $validationErrors);
            header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/sector-registration");
            exit();
        }

        try {
            // ── 2. Build data array ──────────────────────────────────────────
            $sectorUuid = Uuid::uuid7()->toString();

            $data = [
                'uuid'        => $sectorUuid,
                'sector_name' => trim($_POST['sector_name'] ?? ''),
                'reg_by'      => $user['id'],
            ];

            // ── 3. Persist ────────────────────────────────────────────────────
            $sectorModel = new SectorModel($this->db);

            if ($sectorModel->createSector($data)) {
                \App\Helpers\AuditHelper::log(
                    action: 'sector_registered',
                    entityType: 'sector_table',
                    entityId: $sectorUuid,
                    oldValues: null,
                    newValues: [
                        'sector_name' => $data['sector_name'],
                    ],
                    metadata: [
                        'reg_by' => $user['id'],
                    ]
                );

                $_SESSION['success'] = 'ዘርፍ በትክክል ተመዝግቧል።';
            } else {
                $_SESSION['error'] = 'ዘርፍ መመዝገቢያ ሂደት አልተሳካም።';
            }

        } catch (\PDOException $e) {
            error_log("Sector Registration Error: " . $e->getMessage());
            $_SESSION['error'] = "ምዝገባው አልተሳካም። እንደገና ይሞክሩ።";
        }

        header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/sector-registration");
        exit();
    }
public function showSubRegisterForm() {
        // NOTE: confirm this should match the role in handleSectorRegistration() below.
        // Currently 'system_admin' here vs 'officer' there — pick one.
        AuthHelper::checkRole(['system_admin']);
$sectorModel = new SectorModel($this->db);
$sectors  = $sectorModel->getsubSectors();
        $data = [
            'title' => 'JCIMS - የዘርፍ መመዝገቢያ',
            'sectors' => $sectors,
        ];

        $this->render('sub-sector-registration', $data);
    }
    public function handleSubsectorRegistration()
{
    AuthHelper::checkRole(['system_admin']);

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/sub-sector-registration");
        exit();
    }

    $user = $_SESSION['user'] ?? [];

    $sectorUuid = trim($_POST['sector_id'] ?? '');

    // Validate input
    $validationErrors = $this->validateSectorData($_POST);

    if (!empty($validationErrors)) {
        $_SESSION['error'] = implode('<br>', $validationErrors);
        header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/sub-sector-registration");
        exit();
    }

    $sectorModel = new SectorModel($this->db);

    // Fetch sector by UUID
    $sector = $sectorModel->getSectorById($sectorUuid);

    if (!$sector) {
        $_SESSION['error'] = 'Sector id not found'.$sector['sectorid'].''. $sectorUuid;
        header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/sub-sector-registration");
        exit();
    }

    try {

        $subSectorUuid = Uuid::uuid7()->toString();

        $data = [
            'uuid'         => $subSectorUuid,
            'sectorid'     => $sector['sectorid'],   // integer sector id
            'sub_sector'   => trim($_POST['sector_name']),
            'registered_by'=> $user['id'],
        ];

        if ($sectorModel->createSubSector($data)) {

            \App\Helpers\AuditHelper::log(
                action: 'subsector_registered',
                entityType: 'subsector',
                entityId: $subSectorUuid,
                oldValues: null,
                newValues: [
                    'sector_uuid'   => $sectorUuid,
                    'sector_name'   => $sector['sector'],
                    'sub_sector'    => $data['sub_sector']
                ],
                metadata: [
                    'registered_by' => $user['id']
                ]
            );

            $_SESSION['success'] = 'ንዑስ ዘርፍ በተሳካ ሁኔታ ተመዝግቧል።';

        } else {

            $_SESSION['error'] = 'ንዑስ ዘርፍ መመዝገብ አልተሳካም።';

        }

    } catch (\PDOException $e) {

        error_log("Sub Sector Registration Error: " . $e->getMessage());

        $_SESSION['error'] = 'ምዝገባው አልተሳካም። እባክዎ እንደገና ይሞክሩ።';
    }

    header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/sub-sector-registration");
    exit();
}
    private function validateSectorData(array $data): array {
        $errors = [];

        // ── Required field validations ───────────────────────────────────────
        $requiredFields = [
            'sector_name' => 'ዘርፍ ስም',
        ];

        foreach ($requiredFields as $field => $label) {
            if (empty(trim($data[$field] ?? ''))) {
                $errors[] = "$label አስፈላጊ ነው።";
            }
        }

        // ── Length validations ───────────────────────────────────────────────
        // FIX: key was 'v' (typo) — now correctly keyed to 'sector_name' so this
        // check actually runs against the submitted field instead of always
        // silently passing.
        $lengthValidations = [
            'sector_name' => ['min' => 2, 'max' => 50, 'label' => 'የዘርፍ ስም'],
        ];

        foreach ($lengthValidations as $field => $config) {
            $value = trim($data[$field] ?? '');
            if (!empty($value)) {
                $length = mb_strlen($value); // mb_strlen, not strlen — Amharic chars are multi-byte
                if ($length < $config['min']) {
                    $errors[] = "{$config['label']} ቢያንስ {$config['min']} ፊደል መሆን አለበት።";
                }
                if ($length > $config['max']) {
                    $errors[] = "{$config['label']} {$config['max']} ፊደል መብለጥ የለበትም።";
                }
            }
        }

        // ── Character set validation (server-side mirror of client-side filter) ─
        // Matches the data-restrict="letters" pattern used in the form's JS:
        // Latin (A–Z, a–z) + Amharic block (U+1200–U+137F) + spaces only.
        // This is the authoritative check — the client-side filter is just UX,
        // since it can be bypassed entirely (disabled JS, direct POST, etc).
        if (!empty($data['sector_name'] ?? '')) {
            $name = trim($data['sector_name']);
            if (!preg_match('/^[A-Za-z\x{1200}-\x{137F}\s]+$/u', $name)) {
                $errors[] = "የዘርፍ ስም ፊደላት ብቻ መያዝ አለበት፣ ቁጥሮች ወይም ምልክቶች አይፈቀዱም።";
            }
        }

        return $errors;
    }
    
}