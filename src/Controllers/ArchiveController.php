<?php
namespace App\Controllers;
use App\Helpers\AuthHelper;
use App\Models\ArchiveModel;
use App\Models\User;
use Ramsey\Uuid\Uuid;
use \App\Traits\FileUploadTrait;


class ArchiveController extends BaseController {
    use FileUploadTrait;

    public function attachFile() {
        AuthHelper::checkRole(['team_leader', 'officer']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/employee-archive");
            exit();
        }

        // ─── 1.共通 inputs ──────────────────────────────────────────
        $mode          = $_POST['_mode'] ?? 'upload';
        $employee_uuid = $_POST['employee_uuid'] ?? null;
        $document_id   = $_POST['document_id'] ?? null;
        $registered_by = $_SESSION['user']['id'] ?? null;
        $redirect_back = rtrim($_ENV['BASE_URL'], '/') . "/employee-archive/" . $employee_uuid;

        // ─── 2. Certificate type ─────────────────────────────────────
        $cert_type = trim($_POST['certificate_type'] ?? '');
        if ($cert_type === 'ሌላ') {
            $cert_type = trim($_POST['certificate_type_other'] ?? '');
            if (empty($cert_type)) {
                $_SESSION['error'] = 'እባክዎ የምስክር ወረቀቱን አይነት ይጻፉ።';
                header("Location: " . $redirect_back);
                exit();
            }
        }

        if (empty($cert_type)) {
            $_SESSION['error'] = 'እባክዎ የፋይሉን ዓይነት ይምረጡ።';
            header("Location: " . $redirect_back);
            exit();
        }

        // ─── 3. Validate employee UUID ───────────────────────────────
        if (empty($employee_uuid) || !preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $employee_uuid)) {
            $_SESSION['error'] = 'የሰራተኛ መለያ (UUID) ልክ አይደለም።';
            header("Location: " . rtrim($_ENV['BASE_URL'], '/') . "/employee-archive");
            exit();
        }

        try {
            $model = new ArchiveModel($this->db);

            // ════════════════════════════════════════════════════════
            // EDIT MODE
            // ════════════════════════════════════════════════════════
            if ($mode === 'edit') {

                if (empty($document_id)) {
                    $_SESSION['error'] = 'የሰነዱ መለያ አልተገኘም።';
                    header("Location: " . $redirect_back);
                    exit();
                }

                // File is optional on edit — only upload if a new one was chosen
                $newFileName = null;
                $fileChosen  = !empty($_FILES['certificate_file']['name'])
                               && $_FILES['certificate_file']['error'] !== UPLOAD_ERR_NO_FILE;

                if ($fileChosen) {
                    $newFileName = $this->uploadFile('certificate_file', 'documents');

                    if (!$newFileName) {
                        $fileError = $_FILES['certificate_file']['error'] ?? UPLOAD_ERR_NO_FILE;
                        $_SESSION['error'] = 'ፋይሉን መጫን አልተቻለም። ስህተት፡ ' . $this->getUploadErrorMessage($fileError);
                        header("Location: " . $redirect_back);
                        exit();
                    }
                }

                $editData = [
                    'id'           => $document_id,
                    'cert_type'    => $cert_type,
                    'file_url'     => $newFileName,   // null = keep old file in model
                    'updated_by'   => $registered_by,
                    'employee_id'  => $employee_uuid, // for discipline update
                ];

                $result = $model->update($editData);

                if ($result) {
    $model->updateDisciplineIfNeeded(
        $editData['employee_id'],  // this key exists
        $editData['cert_type']
    );
                    // ─── UPDATE AUDIT LOG ─────────────────────────────────
                    \App\Helpers\AuditHelper::log('archive_document_updated', 'archive', $document_id, null, [
                        'document_id'   => $document_id,
                        'employee_uuid' => $employee_uuid,
                        'cert_type'     => $cert_type,
                        'file_url'      => $newFileName ?? 'የድሮው ፋይል አልተቀየረም',
                        'updated_by'    => $registered_by,
                    ]);

                    // Delete old file from server only if a new one was uploaded
                    if ($newFileName && !empty($editData['old_file_url'])) {
                        $oldPath = dirname(__DIR__, 2) . '/storage/uploads/documents/' . $editData['old_file_url'];
                        if (file_exists($oldPath)) {
                            unlink($oldPath);
                        }
                    }

                    $_SESSION['success'] = 'መረጃው በትክክል ተስተካክሏል!';
                    header("Location: " . $redirect_back);
                    exit();
                }

                throw new \Exception("ማስተካከል አልተቻለም።");
            }

            // ════════════════════════════════════════════════════════
            // UPLOAD MODE
            // ════════════════════════════════════════════════════════

            // File is required on upload
            if (empty($_FILES['certificate_file']['name']) || $_FILES['certificate_file']['error'] === UPLOAD_ERR_NO_FILE) {
                $_SESSION['error'] = 'እባክዎ ፋይል ይምረጡ።';
                header("Location: " . $redirect_back);
                exit();
            }

            $certificateFileName = $this->uploadFile('certificate_file', 'documents');

            if (!$certificateFileName) {
                $fileError = $_FILES['certificate_file']['error'] ?? UPLOAD_ERR_NO_FILE;
                $_SESSION['error'] = 'የምስክር ፋይሉን መጫን አልተቻለም። ስህተት፡ ' . $this->getUploadErrorMessage($fileError);
                header("Location: " . $redirect_back);
                exit();
            }

            $generatedId = Uuid::uuid7()->toString();

            $archiveData = [
                'id'               => $generatedId,
                'employee_id'      => $employee_uuid,
                'owner_type'       => 'employee',
                'owner_id'         => $employee_uuid,
                'cert_type'        => $cert_type,
                'certificate_file' => $certificateFileName,
                'registered_by'    => $registered_by,
            ];

            $result = $model->create($archiveData);

           if ($result) {
    $model->updateDisciplineIfNeeded(
        $archiveData['employee_id'],  // this key exists
        $archiveData['cert_type']
    );
                // ─── CREATE AUDIT LOG ─────────────────────────────────
                \App\Helpers\AuditHelper::log('archive_document_created', 'archive', $generatedId, null, [
                    'document_id'      => $generatedId,
                    'employee_id'      => $employee_uuid,
                    'owner_type'       => 'employee',
                    'owner_id'         => $employee_uuid,
                    'cert_type'        => $cert_type,
                    'certificate_file' => $certificateFileName,
                    'registered_by'    => $registered_by,
                ]);

                $_SESSION['success'] = 'መረጃው በትክክል ተመዝግቧል!';
                header("Location: " . $redirect_back);
                exit();
            }

            throw new \Exception("ዳታቤዝ ላይ መመዝገብ አልተቻለም።");

        } catch (\Exception $e) {
            // Cleanup uploaded file if DB failed
            if (!empty($certificateFileName)) {
                $fullPath = dirname(__DIR__, 2) . '/storage/uploads/documents/' . $certificateFileName;
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
            }
            if (!empty($newFileName)) {
                $fullPath = dirname(__DIR__, 2) . '/storage/uploads/documents/' . $newFileName;
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
            }

            $_SESSION['error'] = 'ስህተት ተፈጥሯል፡ ' . $e->getMessage();
            header("Location: " . $redirect_back);
            exit();
        }
    }

public function delete(): void
{
    AuthHelper::checkRole(['team_leader', 'officer']);
    header('Content-Type: application/json');

    $data    = json_decode(file_get_contents('php://input'), true);
    $id      = trim((string) ($data['id'] ?? ''));
    $employeeId = trim((string) ($data['employeeId'] ?? ''));
    $reason = trim($data['reason']      ?? '');
    $password = $data['confirm_password'] ?? '';
    $source = 'INDIVIDUAL';
    $adminId = (string) ($_SESSION['user']['id'] ?? '');

     // Validate input
        if (!$id || !$reason || !$password || !$source) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'ሁሉም መስኮች አስፈላጊ ናቸው።'
            ]);
            return;
        }

        $user           = $_SESSION['user'] ?? [];
        $organizationId = $user['organization_id'] ?? null;
        $branchId = $user['branch_id'] ?? null;

        if (!$organizationId || !$branchId) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'ያልተፈቀደ ድርጊት።'
            ]);
            return;
        }

        // Verify password
        $userModel = new User($this->db);
        if (!$userModel->verifyPassword($user['id'], $password)) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'ፓስዋርዱ ትክክል አይደለም።'
            ]);
            return;
        }

    try {
        $model  = new ArchiveModel($this->db);
        $result = $model->deleteRecord($id, $employeeId, $adminId, $reason, $source);

        if ($result['status'] === 'success') {
            \App\Helpers\AuditHelper::log(
                action:     'document_deleted',
                entityType: 'document',
                entityId:   $id,
                oldValues:  $result['oldRecord'],   // snapshot of deleted record
                newValues:  null,                   // nothing after delete
                metadata:   [
                    'deleted_type'          => 'soft',
                    'deleted_documents'     => $result['deletedDocumentCount'] ?? 0,
                    'deletion_source'       => 'INDIVIDUAL_ACTION',
                    'reason'          => $reason,
                    'performed_by'          => $adminId,
                ]
            );

            // strip internal fields before sending to client
            unset($result['oldRecord'], $result['deletedDocumentCount']);
        }

        echo json_encode($result);

    } catch (\Exception $e) {
        error_log('ArchiveController::delete - ' . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'ስህተት ተፈጥሯል፤ እባክዎ በድጋሚ ይሞክሩ።']);
    }
}
    }