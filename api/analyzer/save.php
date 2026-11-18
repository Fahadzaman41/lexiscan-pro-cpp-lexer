<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/config/app.php';
$user = requireLogin();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed.'], 405);
}
requireCsrf();

$source = (string)($_POST['source_code'] ?? '');
$title = postString('analysis_title', 180);
$filename = postString('source_filename', 255);
$projectId = isset($_POST['project_id']) && ctype_digit((string)$_POST['project_id']) ? (int)$_POST['project_id'] : null;
if (trim($source) === '') {
    jsonResponse(['success' => false, 'message' => 'Source code is required.'], 422);
}
if ($title === '') {
    $title = 'Untitled analysis';
}

require_once dirname(__DIR__, 2) . '/includes/Lexer.php';
$result = (new Lexer())->analyze($source);
$pdo = getDatabaseConnection();

try {
    $pdo->beginTransaction();
    if ($projectId !== null) {
        $ownership = $pdo->prepare('SELECT id FROM projects WHERE id = ? AND user_id = ?');
        $ownership->execute([$projectId, $user['id']]);
        if (!$ownership->fetch()) {
            throw new RuntimeException('Invalid project selection.');
        }
    }
    $counts = $result['counts'];
    $stmt = $pdo->prepare(
        'INSERT INTO analyses (user_id,project_id,analysis_title,source_code,source_filename,total_tokens,keyword_count,
identifier_count,constant_count,operator_count,separator_count,special_character_count,invalid_count,analysis_status)
VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,\'completed\')'
    );
    $stmt->execute([
        $user['id'], $projectId, $title, $source, $filename ?: null, $result['total_tokens'],
        $counts['KEYWORD'], $counts['IDENTIFIER'], $counts['CONSTANT'], $counts['OPERATOR'],
        $counts['SEPARATOR'], $counts['SPECIAL_CHARACTER'], $result['invalid_count']
    ]);
    $analysisId = (int)$pdo->lastInsertId();

    $tokenStmt = $pdo->prepare(
        'INSERT INTO tokens (analysis_id,token_number,category,lexeme,line_number,is_valid) VALUES (?,?,?,?,?,?)'
    );
    foreach ($result['tokens'] as $token) {
        $tokenStmt->execute([
            $analysisId, $token['token_number'], $token['category'], $token['lexeme'],
            $token['line_number'], $token['is_valid'] ? 1 : 0
        ]);
    }
    logActivity($pdo, (int)$user['id'], 'analyze', 'Saved analysis #' . $analysisId . ': ' . $title);
    $pdo->commit();
    jsonResponse(['success' => true, 'message' => 'Analysis saved.', 'analysis_id' => $analysisId, 'data' => $result]);
} catch (Throwable $exception) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    jsonResponse(['success' => false, 'message' => $exception->getMessage()], 400);
}
