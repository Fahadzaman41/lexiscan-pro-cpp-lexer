<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/config/app.php';
$user = requireLogin();
$pdo = getDatabaseConnection();
$pageTitle = 'Analysis History';
$pageScripts = ['history.js'];
$search = trim((string)($_GET['search'] ?? ''));
require dirname(__DIR__) . '/includes/layout_start.php';
?>
<div class="welcome-row"><div><p class="eyebrow">Archive</p><h1>Analysis History</h1></div><a class="button primary" href="<?= APP_BASE_URL ?>/analyzer/index.php">+ New Analysis</a></div>
<section class="panel" style="margin-top:20px"><div class="panel-header"><div><p class="eyebrow">Saved work</p><h3>Previous Analyses</h3></div><input id="historySearch" value="<?= h($search) ?>" placeholder="Search analyses"></div>
<div class="table-wrap"><table><thead><tr><th>Title</th><th>Project</th><th>Tokens</th><th>Invalid</th><th>Created</th><th>Actions</th></tr></thead><tbody id="historyTable"></tbody></table></div></section>
<?php require dirname(__DIR__) . '/includes/layout_end.php'; ?>
