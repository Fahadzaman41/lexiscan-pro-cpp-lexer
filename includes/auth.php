<?php
declare(strict_types=1);

function currentUser(): ?array
{
    return isset($_SESSION['user']) && is_array($_SESSION['user']) ? $_SESSION['user'] : null;
}

function requireLogin(): array
{
    $user = currentUser();
    if (!$user) {
        jsonResponse(['success' => false, 'message' => 'Authentication required.'], 401);
    }
    return $user;
}

function requireAdmin(): array
{
    $user = requireLogin();
    if (($user['role'] ?? '') !== 'admin') {
        jsonResponse(['success' => false, 'message' => 'Administrator access required.'], 403);
    }
    return $user;
}

function redirectIfLoggedIn(): void
{
    if (currentUser()) {
        header('Location: ' . APP_BASE_URL . '/dashboard.php');
        exit;
    }
}
