<?php
$pageTitle = $pageTitle ?? APP_NAME;
$user = currentUser();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= h($pageTitle) ?> | <?= h(APP_NAME) ?></title>
  <link rel="stylesheet" href="<?= APP_BASE_URL ?>/assets/css/style.css">
  <link rel="stylesheet" href="<?= APP_BASE_URL ?>/assets/css/dashboard.css">
  <link rel="stylesheet" href="<?= APP_BASE_URL ?>/assets/css/analyzer.css">
  <link rel="stylesheet" href="<?= APP_BASE_URL ?>/assets/css/responsive.css">
</head>
<body>
<div class="app-shell">
  <aside class="sidebar" id="sidebar">
    <a class="brand" href="<?= APP_BASE_URL ?>/dashboard.php"><span class="brand-mark">&lt;/&gt;</span><span><strong>LEXISCAN</strong><small>PRO</small></span></a>
    <nav class="nav-list">
      <a href="<?= APP_BASE_URL ?>/dashboard.php" data-nav="dashboard"><span>⌂</span>Dashboard</a>
      <a href="<?= APP_BASE_URL ?>/analyzer/index.php" data-nav="analyzer"><span>⌁</span>Analyzer</a>
      <a href="<?= APP_BASE_URL ?>/analyzer/history.php" data-nav="history"><span>◷</span>History</a>
      <a href="<?= APP_BASE_URL ?>/analyzer/tokens.php" data-nav="tokens"><span>≡</span>Token Explorer</a>
      <a href="<?= APP_BASE_URL ?>/projects/index.php" data-nav="projects"><span>◇</span>Projects</a>
      <a href="<?= APP_BASE_URL ?>/tests/index.php" data-nav="tests"><span>✓</span>Test Center</a>
      <?php if (($user['role'] ?? '') === 'admin'): ?>
      <a href="<?= APP_BASE_URL ?>/admin/index.php" data-nav="admin"><span>⚙</span>Admin</a>
      <?php endif; ?>
    </nav>
    <div class="sidebar-profile">
      <div class="avatar"><?= h(strtoupper(substr($user['full_name'] ?? 'U',0,1))) ?></div>
      <div><strong><?= h($user['full_name'] ?? 'User') ?></strong><small><?= h($user['role'] ?? 'user') ?></small></div>
    </div>
  </aside>
  <main class="main-area">
    <header class="topbar">
      <button class="icon-button mobile-menu" id="mobileMenu" type="button" aria-label="Menu">☰</button>
      <div class="topbar-title"><?= h($pageTitle) ?></div>
      <div class="topbar-actions">
        <button class="icon-button" type="button" id="themeToggle" title="Toggle theme">◐</button>
        <form method="post" action="<?= APP_BASE_URL ?>/api/auth/logout.php" id="logoutForm">
          <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
        </form>
        <button class="button ghost" type="button" id="logoutButton">Logout</button>
      </div>
    </header>
    <div class="content">
