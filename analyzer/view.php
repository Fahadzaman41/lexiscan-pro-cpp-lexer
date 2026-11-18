<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/config/app.php';
$user = requireLogin();
$id = (int)($_GET['id'] ?? 0);
$pdo = getDatabaseConnection();
$stmt = $pdo->prepare('SELECT a.*,p.project_name FROM analyses a LEFT JOIN projects p ON p.id=a.project_id WHERE a.id=? AND a.user_id=?');
$stmt->execute([$id,$user['id']]);
$analysis = $stmt->fetch();
if (!$analysis) { http_response_code(404); echo 'Analysis not found or access denied.'; exit; }
$tokenStmt = $pdo->prepare('SELECT * FROM tokens WHERE analysis_id=? ORDER BY token_number');
$tokenStmt->execute([$id]);
$tokens = $tokenStmt->fetchAll();
$pageTitle = 'View Analysis';
$pageScripts = ['tokens.js'];
require dirname(__DIR__) . '/includes/layout_start.php';
?>
<div class="welcome-row"><div><p class="eyebrow">Saved analysis</p><h1><?= h($analysis['analysis_title']) ?></h1></div><div><a class="button ghost" href="<?= APP_BASE_URL ?>/api/reports/export.php?type=csv&analysis_id=<?= $id ?>">CSV</a> <a class="button ghost" href="<?= APP_BASE_URL ?>/api/reports/export.php?type=txt&analysis_id=<?= $id ?>">TXT</a></div></div>
<section class="dashboard-grid" style="margin-top:20px"><article class="panel"><div class="panel-header"><h3>Source Code</h3></div><pre style="margin:0;padding:18px;overflow:auto;max-height:480px;background:#111827;color:#d7e2f2"><?= h($analysis['source_code']) ?></pre></article><article class="panel"><div class="panel-header"><h3>Statistics</h3></div><div style="padding:18px"><?php foreach (['total_tokens','keyword_count','identifier_count','constant_count','operator_count','separator_count','special_character_count','invalid_count'] as $key): ?><div class="summary-row"><span><?= h(str_replace('_',' ',ucwords($key))) ?></span><strong><?= h($analysis[$key]) ?></strong></div><?php endforeach; ?></div></article></section>
<section class="panel results-panel"><div class="panel-header"><h3>Token List</h3></div><div class="table-wrap"><table><thead><tr><th>#</th><th>Category</th><th>Lexeme</th><th>Line</th><th>Valid</th></tr></thead><tbody><?php foreach ($tokens as $token): ?><tr><td><?= h($token['token_number']) ?></td><td><span class="badge <?= h(strtolower(str_replace(['UNKNOWN/','/','_'],'-',$token['category']))) ?>"><?= h($token['category']) ?></span></td><td><code><?= h($token['lexeme']) ?></code></td><td><?= h($token['line_number']) ?></td><td><?= $token['is_valid'] ? 'Yes' : 'No' ?></td></tr><?php endforeach; ?></tbody></table></div></section>
<?php require dirname(__DIR__) . '/includes/layout_end.php'; ?>
