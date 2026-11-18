<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/config/app.php';
$user = requireLogin();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed.'], 405);
}
requireCsrf();
$name = postString('project_name', 160);
$description = postString('description', 2000);
if ($name === '') {
    jsonResponse(['success' => false, 'message' => 'Project name is required.'], 422);
}
$pdo = getDatabaseConnection();
$stmt = $pdo->prepare('INSERT INTO projects (user_id,project_name,description) VALUES (?,?,?)');
$stmt->execute([$user['id'], $name, $description]);
$id = (int)$pdo->lastInsertId();
logActivity($pdo, (int)$user['id'], 'create_project', 'Created project #' . $id);
jsonResponse(['success' => true, 'message' => 'Project created.', 'id' => $id]);
