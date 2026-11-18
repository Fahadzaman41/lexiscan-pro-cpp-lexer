<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/config/app.php';
$user = requireLogin();
$pdo = getDatabaseConnection();
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 20;
$offset = ($page - 1) * $limit;
$search = trim((string)($_GET['search'] ?? ''));
$params = [(int)$user['id']];
$where = 'a.user_id = ?';
if ($search !== '') {
    $where .= ' AND (a.analysis_title LIKE ? OR a.source_filename LIKE ?)';
    $params[] = '%' . $search . '%';
    $params[] = '%' . $search . '%';
}
$count = $pdo->prepare("SELECT COUNT(*) FROM analyses a WHERE $where");
$count->execute($params);
$total = (int)$count->fetchColumn();
$params[] = $limit;
$params[] = $offset;
$stmt = $pdo->prepare("SELECT a.*, p.project_name FROM analyses a LEFT JOIN projects p ON p.id=a.project_id WHERE $where ORDER BY a.created_at DESC LIMIT ? OFFSET ?");
$stmt->execute($params);
jsonResponse(['success' => true, 'items' => $stmt->fetchAll(), 'total' => $total, 'page' => $page]);
