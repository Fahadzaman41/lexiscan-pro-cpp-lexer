<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/config/app.php';

header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed.'], 405);
}
requireCsrf();

$fullName = postString('full_name', 120);
$username = postString('username', 80);
$email = strtolower(postString('email', 190));
$password = (string)($_POST['password'] ?? '');
$confirm = (string)($_POST['confirm_password'] ?? '');

if ($fullName === '' || $username === '' || $email === '' || $password === '') {
    jsonResponse(['success' => false, 'message' => 'All registration fields are required.'], 422);
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    jsonResponse(['success' => false, 'message' => 'Enter a valid email address.'], 422);
}
if (!preg_match('/^[A-Za-z][A-Za-z0-9_.-]{2,79}$/', $username)) {
    jsonResponse(['success' => false, 'message' => 'Username must be 3-80 characters and start with a letter.'], 422);
}
if (strlen($password) < 8) {
    jsonResponse(['success' => false, 'message' => 'Password must contain at least 8 characters.'], 422);
}
if (!hash_equals($password, $confirm)) {
    jsonResponse(['success' => false, 'message' => 'Passwords do not match.'], 422);
}

$pdo = getDatabaseConnection();
$exists = $pdo->prepare('SELECT id FROM users WHERE email = ? OR username = ? LIMIT 1');
$exists->execute([$email, $username]);
if ($exists->fetch()) {
    jsonResponse(['success' => false, 'message' => 'Email address or username is already registered.'], 409);
}

$stmt = $pdo->prepare('INSERT INTO users (full_name,username,email,password,role,status) VALUES (?,?,?,?,\'user\',\'active\')');
$stmt->execute([$fullName, $username, $email, password_hash($password, PASSWORD_DEFAULT)]);
$id = (int)$pdo->lastInsertId();

session_regenerate_id(true);
$_SESSION['user'] = ['id' => $id, 'full_name' => $fullName, 'username' => $username, 'email' => $email, 'role' => 'user', 'status' => 'active'];
logActivity($pdo, $id, 'register', 'Created a new account.');
jsonResponse(['success' => true, 'message' => 'Registration successful.', 'redirect' => APP_BASE_URL . '/dashboard.php']);
