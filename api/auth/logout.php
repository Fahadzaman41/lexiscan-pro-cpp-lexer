<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/config/app.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCsrf();
}
$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
}
session_destroy();
jsonResponse(['success' => true, 'redirect' => APP_BASE_URL . '/login.php']);
