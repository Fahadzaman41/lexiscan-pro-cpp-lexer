<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/config/app.php';
$user = requireLogin();
$type = strtolower((string)($_GET['type'] ?? 'csv'));
$id = (int)($_GET['analysis_id'] ?? 0);
$pdo = getDatabaseConnection();
$stmt = $pdo->prepare('SELECT * FROM analyses WHERE id=? AND user_id=?');
$stmt->execute([$id, $user['id']]);
$analysis = $stmt->fetch();
if (!$analysis) {
    jsonResponse(['success' => false, 'message' => 'Analysis not found or access denied.'], 404);
}
$tokens = $pdo->prepare('SELECT token_number,category,lexeme,line_number,is_valid FROM tokens WHERE analysis_id=? ORDER BY token_number');
$tokens->execute([$id]);
$rows = $tokens->fetchAll();

if ($type === 'source') {
    header('Content-Type: text/plain; charset=utf-8');
    header('Content-Disposition: attachment; filename="lexiscan_source_' . $id . '.txt"');
    echo $analysis['source_code'];
    exit;
}
if ($type === 'txt') {
    header('Content-Type: text/plain; charset=utf-8');
    header('Content-Disposition: attachment; filename="lexiscan_report_' . $id . '.txt"');
    echo APP_NAME . "\r\n";
    echo 'Analysis: ' . $analysis['analysis_title'] . "\r\n";
    echo 'Date: ' . $analysis['created_at'] . "\r\n";
    echo 'Total tokens: ' . $analysis['total_tokens'] . ' | Invalid: ' . $analysis['invalid_count'] . "\r\n\r\n";
    echo "SOURCE CODE\r\n" . $analysis['source_code'] . "\r\n\r\nTOKENS\r\n";
    foreach ($rows as $row) {
        echo '#' . $row['token_number'] . ' | ' . $row['category'] . ' | ' . $row['lexeme'] . ' | Line ' . $row['line_number'] . "\r\n";
    }
    exit;
}
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="lexiscan_tokens_' . $id . '.csv"');
$output = fopen('php://output', 'w');
fputs($output, "\xEF\xBB\xBF");
fputcsv($output, ['Token Number','Category','Lexeme','Line Number','Valid']);
foreach ($rows as $row) {
    fputcsv($output, [$row['token_number'],$row['category'],$row['lexeme'],$row['line_number'],$row['is_valid'] ? 'Yes' : 'No']);
}
fclose($output);
