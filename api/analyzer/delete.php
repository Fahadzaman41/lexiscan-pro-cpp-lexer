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
$stmt = $pdo->prepare('SELECT analysis_title FROM analyses WHERE id = ? AND user_id = ?');
$stmt->execute([$id, $user['id']]);
$analysis = $stmt->fetch();
if (!$analysis) {
    jsonResponse(['success' => false, 'message' => 'Analysis not found or access denied.'], 404);
}
$delete = $pdo->prepare('DELETE FROM analyses WHERE id = ? AND user_id = ?');
$delete->execute([$id, $user['id']]);
logActivity($pdo, (int)$user['id'], 'delete_analysis', 'Deleted analysis #' . $id);
jsonResponse(['success' => true, 'message' => 'Analysis deleted.']);
