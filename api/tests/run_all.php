<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/config/app.php';
$user = requireLogin();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed.'], 405);
}
requireCsrf();
$pdo = getDatabaseConnection();
$tests = $pdo->query('SELECT * FROM test_cases WHERE is_active=1 ORDER BY id')->fetchAll();
$results = [];
require_once dirname(__DIR__, 2) . '/includes/Lexer.php';
foreach ($tests as $test) {
    $started = hrtime(true);
    $analysis = (new Lexer())->analyze($test['source_code']);
    $elapsed = (int)round((hrtime(true) - $started) / 1000000);
    $categoryCounts = array_fill_keys(['KEYWORD','IDENTIFIER','CONSTANT','OPERATOR','SEPARATOR','SPECIAL_CHARACTER'], 0);
    foreach ($analysis['tokens'] as $token) {
        if (isset($categoryCounts[$token['category']])) {
            $categoryCounts[$token['category']]++;
        }
    }
    $expected = strtolower($test['expected_behavior']);
    $passed = 0;
    $failed = 0;
    foreach (array_keys($categoryCounts) as $category) {
        if (str_contains($expected, strtolower($category))) {
            $categoryCounts[$category] > 0 ? $passed++ : $failed++;
        }
    }
    if (str_contains($expected, 'unterminated') && count($analysis['errors']) > 0) {
        $passed++;
    } elseif (str_contains($expected, 'unterminated')) {
        $failed++;
    }
    if ($passed === 0 && $failed === 0) {
        $analysis['total_tokens'] > 0 ? $passed++ : $failed++;
    }
    $status = $failed === 0 ? 'passed' : 'failed';
    $run = $pdo->prepare('INSERT INTO test_runs (user_id,test_case_id,total_tokens,passed_checks,failed_checks,status,execution_time_ms) VALUES (?,?,?,?,?,?,?)');
    $run->execute([$user['id'], $test['id'], $analysis['total_tokens'], $passed, $failed, $status, $elapsed]);
    $results[] = ['id' => $test['id'], 'test_name' => $test['test_name'], 'status' => $status, 'passed' => $passed, 'failed' => $failed];
}
$passedCount = count(array_filter($results, fn($r) => $r['status'] === 'passed'));
logActivity($pdo, (int)$user['id'], 'run_all_tests', 'Ran all test cases.');
jsonResponse(['success' => true, 'summary' => ['total' => count($results), 'passed' => $passedCount, 'failed' => count($results) - $passedCount], 'results' => $results]);
