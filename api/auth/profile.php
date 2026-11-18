<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/config/app.php';
$user = requireLogin();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed.'], 405);
}
requireCsrf();
$fullName = postString('full_name', 120);
$email = strtolower(postString('email', 190));
$password = (string)($_POST['password'] ?? '');
if ($fullName === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    jsonResponse(['success' => false, 'message' => 'Valid name and email are required.'], 422);
}
$pdo = getDatabaseConnection();
$existing = $pdo->prepare('SELECT id FROM users WHERE email=? AND id<>?');
$existing->execute([$email, $user['id']]);
if ($existing->fetch()) {
    jsonResponse(['success' => false, 'message' => 'Email address is already in use.'], 409);
}
if ($password !== '') {
    if (strlen($password) < 8) {
        jsonResponse(['success' => false, 'message' => 'Password must contain at least 8 characters.'], 422);
    }
    $stmt = $pdo->prepare('UPDATE users SET full_name=?,email=?,password=? WHERE id=?');
    $stmt->execute([$fullName, $email, password_hash($password, PASSWORD_DEFAULT), $user['id']]);
} else {
    $stmt = $pdo->prepare('UPDATE users SET full_name=?,email=? WHERE id=?');
    $stmt->execute([$fullName, $email, $user['id']]);
}
$_SESSION['user']['full_name'] = $fullName;
$_SESSION['user']['email'] = $email;
logActivity($pdo, (int)$user['id'], 'update_profile', 'Updated account profile.');
jsonResponse(['success' => true, 'message' => 'Profile updated.']);
