<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/config/app.php';
$user = requireLogin();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed.'], 405);
}
requireCsrf();
$id = (int)($_POST['test_case_id'] ?? 0);
$pdo = getDatabaseConnection();
$stmt = $pdo->prepare('SELECT * FROM test_cases WHERE id=? AND is_active=1');
$stmt->execute([$id]);
$test = $stmt->fetch();
if (!$test) {
    jsonResponse(['success' => false, 'message' => 'Test case not found.'], 404);
}
require_once dirname(__DIR__, 2) . '/includes/Lexer.php';
$started = hrtime(true);
$result = (new Lexer())->analyze($test['source_code']);
$elapsed = (int)round((hrtime(true) - $started) / 1000000);
$categories = array_fill_keys(['KEYWORD','IDENTIFIER','CONSTANT','OPERATOR','SEPARATOR','SPECIAL_CHARACTER'], 0);
foreach ($result['tokens'] as $token) {
    if (isset($categories[$token['category']])) {
        $categories[$token['category']]++;
    }
}
$passed = 0;
$failed = 0;
$lexemeMap = array_column($result['tokens'], 'lexeme');
$expectations = strtolower($test['expected_behavior']);
$checks = [];
foreach (['KEYWORD','IDENTIFIER','CONSTANT','OPERATOR','SEPARATOR','SPECIAL_CHARACTER'] as $category) {
    if (str_contains($expectations, strtolower($category))) {
        $ok = $categories[$category] > 0;
        $checks[] = ['check' => 'Contains ' . $category, 'passed' => $ok];
        $ok ? $passed++ : $failed++;
    }
}
$operators = ['>=','&&','!=','++','+=','<<','>>','%'];
foreach ($operators as $operator) {
    if (str_contains($expectations, $operator)) {
        $ok = in_array($operator, $lexemeMap, true);
        $checks[] = ['check' => 'Contains lexeme ' . $operator, 'passed' => $ok];
        $ok ? $passed++ : $failed++;
    }
}
if (str_contains($expectations, 'ignore comment') || str_contains($expectations, 'ignore comment text')) {
    $ok = !array_intersect(['comment','one','two'], array_map('strtolower', $lexemeMap));
    $checks[] = ['check' => 'Comments removed', 'passed' => $ok];
    $ok ? $passed++ : $failed++;
}
if (str_contains($expectations, 'unterminated string')) {
    $ok = count($result['errors']) > 0;
    $checks[] = ['check' => 'Reports unterminated string', 'passed' => $ok];
    $ok ? $passed++ : $failed++;
}
if (str_contains($expectations, 'unterminated block comment')) {
    $ok = count($result['errors']) > 0;
    $checks[] = ['check' => 'Reports unterminated block comment', 'passed' => $ok];
    $ok ? $passed++ : $failed++;
}
if ($passed === 0 && $failed === 0) {
    $ok = $result['total_tokens'] > 0;
    $checks[] = ['check' => 'Produces token output', 'passed' => $ok];
    $ok ? $passed++ : $failed++;
}
$status = $failed === 0 ? 'passed' : 'failed';
$run = $pdo->prepare('INSERT INTO test_runs (user_id,test_case_id,total_tokens,passed_checks,failed_checks,status,execution_time_ms) VALUES (?,?,?,?,?,?,?)');
$run->execute([$user['id'], $id, $result['total_tokens'], $passed, $failed, $status, $elapsed]);
logActivity($pdo, (int)$user['id'], 'run_test', 'Ran test #' . $id . ' (' . $status . ')');
jsonResponse(['success' => true, 'status' => $status, 'passed_checks' => $passed, 'failed_checks' => $failed, 'checks' => $checks, 'data' => $result, 'execution_time_ms' => $elapsed]);
