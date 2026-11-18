<?php
declare(strict_types=1);
require_once __DIR__ . '/config/app.php';
redirectIfLoggedIn();
?>
<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Create account | LexiScan Pro</title><link rel="stylesheet" href="<?= APP_BASE_URL ?>/assets/css/style.css"></head>
<body class="auth-page"><main class="auth-card compact">
<section class="auth-visual"><div class="brand auth-brand"><span class="brand-mark">&lt;/&gt;</span><span><strong>LEXISCAN</strong><small>PRO</small></span></div>
<h1>Build your<br><span>analysis workspace.</span></h1><p>Save source code, explore tokens, run tests and export reports.</p></section>
<section class="auth-form"><div><p class="eyebrow">Get started</p><h2>Create account</h2>
<form id="registerForm"><input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
<label>Full Name<input name="full_name" required maxlength="120"></label>
<label>Username<input name="username" required maxlength="80"></label>
<label>Email Address<input name="email" type="email" required></label>
<div class="form-grid"><label>Password<input name="password" type="password" required minlength="8"></label><label>Confirm Password<input name="confirm_password" type="password" required minlength="8"></label></div>
<button class="button primary" type="submit">Create account</button></form>
<p class="auth-switch">Already registered? <a href="<?= APP_BASE_URL ?>/login.php">Sign in</a></p><div class="form-message" id="formMessage"></div></div></section>
</main><script>window.LEXISCAN_BASE=<?= json_encode(APP_BASE_URL) ?>;window.LEXISCAN_CSRF=<?= json_encode(csrfToken()) ?>;</script><script src="<?= APP_BASE_URL ?>/assets/js/app.js"></script><script src="<?= APP_BASE_URL ?>/assets/js/auth.js"></script></body></html>
