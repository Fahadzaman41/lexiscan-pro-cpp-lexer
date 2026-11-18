<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/config/app.php';
$user = requireLogin();
$pdo = getDatabaseConnection();
$stmt = $pdo->query('SELECT id,test_name,description,source_code,expected_behavior,category,is_active FROM test_cases WHERE is_active=1 ORDER BY id');
jsonResponse(['success' => true, 'items' => $stmt->fetchAll()]);
