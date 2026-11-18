<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/config/app.php';
$user = requireLogin();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed.'], 405);
}
requireCsrf();
$id = (int)($_POST['id'] ?? 0);
$title = postString('analysis_title', 180);
if ($title === '') {
    jsonResponse(['success' => false, 'message' => 'Analysis title is required.'], 422);
}
$pdo = getDatabaseConnection();
$stmt = $pdo->prepare('UPDATE analyses SET analysis_title=? WHERE id=? AND user_id=?');
$stmt->execute([$title, $id, $user['id']]);
if ($stmt->rowCount() === 0) {
    jsonResponse(['success' => false, 'message' => 'Analysis not found or access denied.'], 404);
}
jsonResponse(['success' => true, 'message' => 'Analysis renamed.']);
