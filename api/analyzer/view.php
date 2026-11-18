<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/config/app.php';
$user = requireLogin();
$id = (int)($_GET['id'] ?? 0);
$pdo = getDatabaseConnection();
$stmt = $pdo->prepare('SELECT * FROM analyses WHERE id = ? AND user_id = ?');
$stmt->execute([$id, $user['id']]);
$analysis = $stmt->fetch();
if (!$analysis) {
    jsonResponse(['success' => false, 'message' => 'Analysis not found or access denied.'], 404);
}
$tokens = $pdo->prepare('SELECT * FROM tokens WHERE analysis_id = ? ORDER BY token_number');
$tokens->execute([$id]);
$analysis['tokens'] = $tokens->fetchAll();
jsonResponse(['success' => true, 'data' => $analysis]);
