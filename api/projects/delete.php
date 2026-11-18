<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/config/app.php';
$user = requireLogin();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed.'], 405);
}
requireCsrf();
$id = (int)($_POST['id'] ?? 0);
$pdo = getDatabaseConnection();
$stmt = $pdo->prepare('DELETE FROM projects WHERE id=? AND user_id=?');
$stmt->execute([$id, $user['id']]);
if ($stmt->rowCount() === 0) {
    jsonResponse(['success' => false, 'message' => 'Project not found or access denied.'], 404);
}
jsonResponse(['success' => true, 'message' => 'Project deleted. Its analyses remain in your history.']);
