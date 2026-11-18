<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/config/app.php';
$user = requireLogin();
$analysisId = (int)($_GET['analysis_id'] ?? 0);
$pdo = getDatabaseConnection();
$own = $pdo->prepare('SELECT id FROM analyses WHERE id = ? AND user_id = ?');
$own->execute([$analysisId, $user['id']]);
if (!$own->fetch()) {
    jsonResponse(['success' => false, 'message' => 'Analysis not found or access denied.'], 404);
}
$category = trim((string)($_GET['category'] ?? ''));
$search = trim((string)($_GET['search'] ?? ''));
$params = [$analysisId];
$where = 'analysis_id = ?';
if ($category !== '' && $category !== 'ALL') {
    $where .= ' AND category = ?';
    $params[] = $category;
}
if ($search !== '') {
    $where .= ' AND lexeme LIKE ?';
    $params[] = '%' . $search . '%';
}
$sort = ($_GET['sort'] ?? 'token_number') === 'line_number' ? 'line_number' : 'token_number';
$stmt = $pdo->prepare("SELECT * FROM tokens WHERE $where ORDER BY $sort");
$stmt->execute($params);
jsonResponse(['success' => true, 'items' => $stmt->fetchAll()]);
