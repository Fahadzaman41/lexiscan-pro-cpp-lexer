<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/config/app.php';
$admin = requireAdmin();
$pdo = getDatabaseConnection();
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $search = trim((string)($_GET['search'] ?? ''));
    if ($search !== '') {
        $stmt = $pdo->prepare("SELECT id,full_name,username,email,role,status,created_at FROM users WHERE full_name LIKE ? OR username LIKE ? OR email LIKE ? ORDER BY created_at DESC");
        $like = '%' . $search . '%';
        $stmt->execute([$like,$like,$like]);
    } else {
        $stmt = $pdo->query('SELECT id,full_name,username,email,role,status,created_at FROM users ORDER BY created_at DESC');
    }
    jsonResponse(['success' => true, 'items' => $stmt->fetchAll()]);
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed.'], 405);
}
requireCsrf();
$id = (int)($_POST['id'] ?? 0);
$action = (string)($_POST['action'] ?? '');
if ($id <= 0) {
    jsonResponse(['success' => false, 'message' => 'User ID is required.'], 422);
}
if ($action === 'delete' && $id === (int)$admin['id']) {
    jsonResponse(['success' => false, 'message' => 'You cannot delete your own account.'], 409);
}
if ($action === 'delete') {
    $stmt = $pdo->prepare('DELETE FROM users WHERE id=?');
    $stmt->execute([$id]);
} elseif ($action === 'activate' || $action === 'deactivate') {
    $status = $action === 'activate' ? 'active' : 'inactive';
    $stmt = $pdo->prepare('UPDATE users SET status=? WHERE id=?');
    $stmt->execute([$status,$id]);
} elseif ($action === 'role') {
    $role = (string)($_POST['role'] ?? 'user');
    if (!in_array($role,['admin','user'],true)) {
        jsonResponse(['success' => false, 'message' => 'Invalid role.'], 422);
    }
    if ($id === (int)$admin['id'] && $role !== 'admin') {
        jsonResponse(['success' => false, 'message' => 'You cannot remove your own administrator role.'], 409);
    }
    $stmt = $pdo->prepare('UPDATE users SET role=? WHERE id=?');
    $stmt->execute([$role,$id]);
} else {
    jsonResponse(['success' => false, 'message' => 'Invalid action.'], 422);
}
jsonResponse(['success' => true, 'message' => 'User updated.']);
