<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/config/app.php';
$user = requireLogin();
$pdo = getDatabaseConnection();
$stmt = $pdo->prepare('SELECT p.*, (SELECT COUNT(*) FROM analyses a WHERE a.project_id=p.id) AS analysis_count FROM projects p WHERE p.user_id=? ORDER BY p.updated_at DESC');
$stmt->execute([$user['id']]);
jsonResponse(['success' => true, 'items' => $stmt->fetchAll()]);
