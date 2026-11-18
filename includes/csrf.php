<?php
declare(strict_types=1);

function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrf(): bool
{
    $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    return is_string($token) && hash_equals($_SESSION['csrf_token'] ?? '', $token);
}

function requireCsrf(): void
{
    if (!verifyCsrf()) {
        jsonResponse(['success' => false, 'message' => 'Invalid or missing CSRF token.'], 403);
    }
}
