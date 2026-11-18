<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/config/app.php';
$user = requireLogin();
$pdo = getDatabaseConnection();
$stmt = $pdo->prepare('SELECT id,analysis_title,created_at FROM analyses WHERE user_id=? ORDER BY created_at DESC');
$stmt->execute([$user['id']]);
$analyses = $stmt->fetchAll();
$pageTitle = 'Token Explorer';
$pageScripts = ['tokens.js','history.js'];
require dirname(__DIR__) . '/includes/layout_start.php';
?>
<div class="welcome-row"><div><p class="eyebrow">Deep inspection</p><h1>Token Explorer</h1></div></div>
<section class="panel" style="margin-top:20px"><div class="panel-header"><div class="table-tools"><select id="analysisSelector"><option value="">Select analysis</option><?php foreach ($analyses as $analysis): ?><option value="<?= h($analysis['id']) ?>"><?= h($analysis['analysis_title']) ?></option><?php endforeach; ?></select><select id="tokenFilter"><option value="ALL">All categories</option><?php foreach (['KEYWORD','IDENTIFIER','CONSTANT','OPERATOR','SEPARATOR','SPECIAL_CHARACTER','UNKNOWN/INVALID'] as $category): ?><option><?= h($category) ?></option><?php endforeach; ?></select><input id="tokenSearch" placeholder="Search lexeme"></div></div><div class="table-wrap"><table><thead><tr><th>#</th><th>Category</th><th>Lexeme</th><th>Line</th><th>Valid</th></tr></thead><tbody id="tokenTableBody"><tr><td colspan="5" class="empty-cell">Select a saved analysis</td></tr></tbody></table></div></section>
<?php require dirname(__DIR__) . '/includes/layout_end.php'; ?>
