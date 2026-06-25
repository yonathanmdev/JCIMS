<?php
namespace App\Controllers;
use App\Helpers\AuthHelper;
// 1. BaseControllerን እንዲወርስ (Extends) እናደርጋለን
class FileController {
 public function serveFile() {
    // comment out auth temporarily to test
    // AuthHelper::checkRole(['team_leader', 'officer']);
    
    $filename = $_GET['file'] ?? null;
    $type     = $_GET['type'] ?? null;

    if (!$filename || !$type) {
        http_response_code(404);
        exit('File not found');
    }

    $filename = basename($filename);

    $allowedTypes = [
        'image'    => 'uploads/images/',
        'document' => 'uploads/documents/',
        
        
    ];

    if (!isset($allowedTypes[$type])) {
        http_response_code(400);
        exit('Invalid file type');
    }

    $filePath = __DIR__ . '/../../storage/' . $allowedTypes[$type] . $filename;

    if (!file_exists($filePath)) {
        http_response_code(404);
        exit('File not found');
    }

    // clear any output before headers
    ob_clean();
    
    $mimeType = mime_content_type($filePath);
    header('Content-Type: ' . $mimeType);
    header('Content-Disposition: inline; filename="' . $filename . '"');
    header('Content-Length: ' . filesize($filePath));
    readfile($filePath);
    exit();
}

}