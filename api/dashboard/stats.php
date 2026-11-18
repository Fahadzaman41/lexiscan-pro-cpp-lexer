<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/config/app.php';
$user = requireLogin();
$pdo = getDatabaseConnection();
$userId = (int)$user['id'];

$analyses = $pdo->prepare('SELECT COUNT(*) FROM analyses WHERE user_id=?');
$analyses->execute([$userId]);
$recent = $pdo->prepare('SELECT id,analysis_title,total_tokens,created_at FROM analyses WHERE user_id=? ORDER BY created_at DESC LIMIT 6');
$recent->execute([$userId]);
$tokens = $pdo->prepare('SELECT COUNT(*) FROM tokens t JOIN analyses a ON a.id=t.analysis_id WHERE a.user_id=?');
$tokens->execute([$userId]);
$counts = $pdo->prepare('SELECT category,COUNT(*) AS count FROM tokens t JOIN analyses a ON a.id=t.analysis_id WHERE a.user_id=? GROUP BY category');
$counts->execute([$userId]);
$testRuns = $pdo->prepare('SELECT status,COUNT(*) AS count FROM test_runs WHERE user_id=? GROUP BY status');
$testRuns->execute([$userId]);
$projects = $pdo->prepare('SELECT COUNT(*) FROM projects WHERE user_id=?');
$projects->execute([$userId]);

$distribution = [];
foreach ($counts->fetchAll() as $row) {
    $distribution[$row['category']] = (int)$row['count'];
}
$testStatus = ['passed' => 0, 'failed' => 0];
foreach ($testRuns->fetchAll() as $row) {
    $testStatus[$row['status']] = (int)$row['count'];
}

jsonResponse([
    'success' => true,
    'stats' => [
        'analyses' => (int)$analyses->fetchColumn(),
        'tokens' => (int)$tokens->fetchColumn(),
        'projects' => (int)$projects->fetchColumn(),
        'test_passed' => $testStatus['passed'],
        'test_failed' => $testStatus['failed'],
        'distribution' => $distribution
    ],
    'recent' => $recent->fetchAll()
]);
