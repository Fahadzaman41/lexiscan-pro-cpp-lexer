<?php
declare(strict_types=1);
require_once __DIR__ . '/config/app.php';
$_SESSION = [];
session_destroy();
header('Location: ' . APP_BASE_URL . '/login.php');
