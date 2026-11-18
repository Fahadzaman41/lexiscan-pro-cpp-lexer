<?php
declare(strict_types=1);

function jsonResponse(array $data, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function h(mixed $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function postString(string $key, int $maxLength = 65535): string
{
    $value = $_POST[$key] ?? '';
    return trim(is_string($value) ? mb_substr($value, 0, $maxLength) : '');
}

function logActivity(PDO $pdo, int $userId, string $action, string $description): void
{
    $stmt = $pdo->prepare('INSERT INTO activity_logs (user_id,action,description) VALUES (?,?,?)');
    $stmt->execute([$userId, $action, $description]);
}
