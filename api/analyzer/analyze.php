<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/config/app.php';
requireLogin();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed.'], 405);
}
requireCsrf();

$source = '';
if (isset($_FILES['source_file'])) {
    $file = $_FILES['source_file'];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        jsonResponse(['success' => false, 'message' => 'File upload failed.'], 422);
    }
    if ($file['size'] > 1024 * 1024) {
        jsonResponse(['success' => false, 'message' => 'Source file exceeds the 1 MB limit.'], 413);
    }
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if ($extension !== 'cpp' && $extension !== 'cc' && $extension !== 'h') {
        jsonResponse(['success' => false, 'message' => 'Only .cpp, .cc, and .h source files are allowed.'], 422);
    }
    $source = file_get_contents($file['tmp_name']);
} else {
    $source = (string)($_POST['source_code'] ?? '');
}
if (trim($source) === '') {
    jsonResponse(['success' => false, 'message' => 'Source code is required.'], 422);
}
if (strlen($source) > 2 * 1024 * 1024) {
    jsonResponse(['success' => false, 'message' => 'Source code exceeds the size limit.'], 413);
}

require_once dirname(__DIR__, 2) . '/includes/Lexer.php';
$started = hrtime(true);
$result = (new Lexer())->analyze($source);
$elapsed = (int)round((hrtime(true) - $started) / 1000000);
$result['execution_time_ms'] = $elapsed;
jsonResponse(['success' => true, 'source_code' => $source, 'data' => $result]);
