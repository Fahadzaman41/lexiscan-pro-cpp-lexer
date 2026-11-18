<?php
declare(strict_types=1);
require_once dirname(__DIR__).'/config/app.php';requireAdmin();$pageTitle='Administration';$pageScripts=['admin.js'];require dirname(__DIR__).'/includes/layout_start.php';
?>
<div class="welcome-row"><div><p class="eyebrow">Control panel</p><h1>Administration</h1></div><a class="button ghost" href="<?=APP_BASE_URL?>/admin/tests.php">Manage Tests</a></div>
<section class="metric-grid" id="adminStats"><article class="metric-card"><div><small>Users</small><strong id="adminUsers">—</strong></div></article><article class="metric-card"><div><small>Analyses</small><strong id="adminAnalyses">—</strong></div></article><article class="metric-card"><div><small>Tokens</small><strong id="adminTokens">—</strong></div></article><article class="metric-card"><div><small>Test Runs</small><strong id="adminRuns">—</strong></div></article></section>
<section class="dashboard-grid"><article class="panel"><div class="panel-header"><h3>Users</h3><input id="userSearch" placeholder="Search users"></div><div class="table-wrap"><table><thead><tr><th>Name</th><th>Username</th><th>Role</th><th>Status</th><th>Actions</th></tr></thead><tbody id="adminUsersTable"></tbody></table></div></article><article class="panel"><div class="panel-header"><h3>Recent Activity</h3></div><div id="adminActivity" style="padding:18px"></div></article></section>
<?php require dirname(__DIR__).'/includes/layout_end.php';?>
