<?php
declare(strict_types=1);
require_once __DIR__ . '/config/app.php';
$user = requireLogin();
$pageTitle = 'Dashboard';
$pageScripts = ['dashboard.js'];
require __DIR__ . '/includes/layout_start.php';
?>
<div class="welcome-row"><div><p class="eyebrow">Overview</p><h1>Welcome back, <?= h($user['full_name']) ?></h1></div><a class="button primary" href="<?= APP_BASE_URL ?>/analyzer/index.php">+ New Analysis</a></div>
<section class="metric-grid" id="metricGrid">
  <article class="metric-card"><span class="metric-icon blue">⌁</span><div><small>Total Analyses</small><strong id="statAnalyses">—</strong></div></article>
  <article class="metric-card"><span class="metric-icon purple">≡</span><div><small>Tokens Generated</small><strong id="statTokens">—</strong></div></article>
  <article class="metric-card"><span class="metric-icon green">✓</span><div><small>Tests Passed</small><strong id="statPassed">—</strong></div></article>
  <article class="metric-card"><span class="metric-icon orange">◇</span><div><small>Projects</small><strong id="statProjects">—</strong></div></article>
</section>
<section class="dashboard-grid">
  <article class="panel"><div class="panel-header"><div><p class="eyebrow">Distribution</p><h3>Token Categories</h3></div></div><div class="category-chart" id="categoryChart"></div></article>
  <article class="panel"><div class="panel-header"><div><p class="eyebrow">Recent</p><h3>Latest Analyses</h3></div><a href="<?= APP_BASE_URL ?>/analyzer/history.php">View all</a></div><div class="table-wrap"><table><thead><tr><th>Title</th><th>Tokens</th><th>Date</th></tr></thead><tbody id="recentAnalyses"></tbody></table></div></article>
</section>
<section class="quick-actions"><a class="quick-action" href="<?= APP_BASE_URL ?>/analyzer/index.php"><span>⌁</span><div><strong>Analyze Code</strong><small>Open the lexical workspace</small></div></a><a class="quick-action" href="<?= APP_BASE_URL ?>/projects/index.php"><span>◇</span><div><strong>Create Project</strong><small>Organize source work</small></div></a><a class="quick-action" href="<?= APP_BASE_URL ?>/tests/index.php"><span>✓</span><div><strong>Run Tests</strong><small>Verify analyzer behavior</small></div></a></section>
<?php require __DIR__ . '/includes/layout_end.php'; ?>
