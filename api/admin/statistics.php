<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/config/app.php';
requireAdmin();
$pdo = getDatabaseConnection();
$stats = [
    'users' => (int)$pdo->query('SELECT COUNT(*) FROM users')->fetchColumn(),
    'analyses' => (int)$pdo->query('SELECT COUNT(*) FROM analyses')->fetchColumn(),
    'tokens' => (int)$pdo->query('SELECT COUNT(*) FROM tokens')->fetchColumn(),
    'test_runs' => (int)$pdo->query('SELECT COUNT(*) FROM test_runs')->fetchColumn(),
];
$categories = $pdo->query('SELECT category,COUNT(*) AS count FROM tokens GROUP BY category ORDER BY count DESC')->fetchAll();
$activity = $pdo->query('SELECT a.*,u.username FROM activity_logs a JOIN users u ON u.id=a.user_id ORDER BY a.created_at DESC LIMIT 12')->fetchAll();
jsonResponse(['success' => true, 'stats' => $stats, 'categories' => $categories, 'activity' => $activity]);
