<?php
namespace App\Traits;

trait FileUploadTrait {

    private array $allowedMimes = [
        'images'    => ['image/jpeg', 'image/png'],
        'documents' => ['application/pdf', 'image/jpeg', 'image/png'],
    ];

    protected function uploadFile(string $inputName, string $path): ?string {
        if (!isset($_FILES[$inputName])) {
            return null;
        }

        if ($_FILES[$inputName]['error'] !== UPLOAD_ERR_OK) {
            if ($_FILES[$inputName]['error'] !== UPLOAD_ERR_NO_FILE) {
                error_log("Upload error ({$inputName}): " . $_FILES[$inputName]['error']);
            }
            return null;
        }

        $tmpPath  = $_FILES[$inputName]['tmp_name'];
        $mimeType = mime_content_type($tmpPath);
        $folder   = trim($path, '/');

        if (isset($this->allowedMimes[$folder]) && !in_array($mimeType, $this->allowedMimes[$folder])) {
            error_log("Invalid MIME type ({$inputName}): {$mimeType}");
            return null;
        }

        $ext      = pathinfo($_FILES[$inputName]['name'], PATHINFO_EXTENSION);
        $fileName = bin2hex(random_bytes(16)) . '.' . strtolower($ext);

        $storageRoot = dirname(__DIR__, 2) . '/storage/uploads/';
        $fullPath    = rtrim($storageRoot . $folder, '/') . '/';

        if (!is_dir($fullPath)) {
            mkdir($fullPath, 0755, true);
        }

        if (!move_uploaded_file($tmpPath, $fullPath . $fileName)) {
            error_log("Failed to move uploaded file for {$inputName} to {$fullPath}{$fileName}");
            return null;
        }

        return $fileName;
    }

    protected function getUploadErrorMessage(int $errorCode): string {
        switch ($errorCode) {
            case UPLOAD_ERR_OK:
                return 'Upload successful.';
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return 'The file is too large. Please upload a smaller file.';
            case UPLOAD_ERR_PARTIAL:
                return 'The file was only partially uploaded. Please try again.';
            case UPLOAD_ERR_NO_FILE:
                return 'No file was selected. Please choose a file and try again.';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Temporary storage directory is missing. Please contact support.';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Failed to write the file to disk. Please contact support.';
            case UPLOAD_ERR_EXTENSION:
                return 'File upload was blocked by a server extension. Please contact support.';
            default:
                return 'An unexpected error occurred during file upload. Please try again.';
        }
    }
protected function cleanupUploadedFile(bool $wasUploaded, ?string $fileName, string $path = 'documents'): void {
    if (!$wasUploaded || !$fileName) {
        return;
    }

    $folder = trim($path, '/');
    $storageRoot = dirname(__DIR__, 2) . '/storage/uploads/';
    $fullPath = rtrim($storageRoot . $folder, '/') . '/' . $fileName;

    if (file_exists($fullPath)) {
        unlink($fullPath);
    }
}
    }