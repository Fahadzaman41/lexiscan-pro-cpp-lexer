<?php
declare(strict_types=1);
require_once __DIR__ . '/config/app.php';
redirectIfLoggedIn();
?>
<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Login | LexiScan Pro</title><link rel="stylesheet" href="<?= APP_BASE_URL ?>/assets/css/style.css"></head>
<body class="auth-page"><main class="auth-card">
<section class="auth-visual"><div class="brand auth-brand"><span class="brand-mark">&lt;/&gt;</span><span><strong>LEXISCAN</strong><small>PRO</small></span></div>
<h1>Analyze.<br><span>Tokenize.</span><br>Understand.</h1><p>Turn C++ source code into structured compiler insight.</p></section>
<section class="auth-form"><div><p class="eyebrow">Welcome back</p><h2>Sign in to LexiScan</h2>
<form id="loginForm"><input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
<label>Email or Username<input name="identity" type="text" required autocomplete="username"></label>
<label>Password<input name="password" type="password" required autocomplete="current-password"></label>
<button class="button primary" type="submit">Sign in</button></form>
<p class="auth-switch">New to LexiScan? <a href="<?= APP_BASE_URL ?>/register.php">Create account</a></p><div class="form-message" id="formMessage"></div></div></section>
</main><script>window.LEXISCAN_BASE=<?= json_encode(APP_BASE_URL) ?>;window.LEXISCAN_CSRF=<?= json_encode(csrfToken()) ?>;</script><script src="<?= APP_BASE_URL ?>/assets/js/app.js"></script><script src="<?= APP_BASE_URL ?>/assets/js/auth.js"></script></body></html>
