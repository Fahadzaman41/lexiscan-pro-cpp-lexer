<?php
declare(strict_types=1);
require_once __DIR__.'/config/app.php';

$token = getenv('LEXISCAN_SETUP_TOKEN');
if ($token === false || $token === '' || !hash_equals($token, $_GET['token'] ?? '')) {
    http_response_code(403);
    exit('Setup denied.');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo '<form method="post"><input name="full_name" required placeholder="Full name"><input name="username" required placeholder="Username"><input name="email" type="email" required placeholder="Email"><input name="password" type="password" required minlength="8"><button>Create administrator</button></form>';
    exit;
}

$pdo=getDatabaseConnection();
$fullName=trim((string)($_POST['full_name']??''));$username=trim((string)($_POST['username']??''));$email=strtolower(trim((string)($_POST['email']??'')));$password=(string)($_POST['password']??'');
if($fullName===''||$username===''||!filter_var($email,FILTER_VALIDATE_EMAIL)||strlen($password)<8){exit('Invalid administrator details.');}
$stmt=$pdo->prepare('INSERT INTO users(full_name,username,email,password,role,status)VALUES(?,?,?,?,\'admin\',\'active\')');
$stmt->execute([$fullName,$username,$email,password_hash($password,PASSWORD_DEFAULT)]);
echo 'Administrator created. Remove LEXISCAN_SETUP_TOKEN and delete or restrict this setup file.';
