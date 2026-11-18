<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/config/app.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed.'], 405);
}
requireCsrf();

$identity = strtolower(postString('identity', 190));
$password = (string)($_POST['password'] ?? '');
if ($identity === '' || $password === '') {
    jsonResponse(['success' => false, 'message' => 'Email/username and password are required.'], 422);
}

$pdo = getDatabaseConnection();
$stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? OR username = ? LIMIT 1');
$stmt->execute([$identity, $identity]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password'])) {
    jsonResponse(['success' => false, 'message' => 'Invalid credentials.'], 401);
}
if ($user['status'] !== 'active') {
    jsonResponse(['success' => false, 'message' => 'This account is inactive.'], 403);
}

session_regenerate_id(true);
$_SESSION['user'] = [
    'id' => (int)$user['id'], 'full_name' => $user['full_name'], 'username' => $user['username'],
    'email' => $user['email'], 'role' => $user['role'], 'status' => $user['status']
];
logActivity($pdo, (int)$user['id'], 'login', 'Signed in.');
jsonResponse(['success' => true, 'message' => 'Login successful.', 'redirect' => APP_BASE_URL . '/dashboard.php']);
