<?php
declare(strict_types=1);require_once dirname(__DIR__).'/config/app.php';requireAdmin();$pageTitle='Test Management';require dirname(__DIR__).'/includes/layout_start.php';
$pdo=getDatabaseConnection();$tests=$pdo->query('SELECT id,test_name,description,source_code,expected_behavior,category,is_active FROM test_cases ORDER BY id')->fetchAll();
?>
<div class="welcome-row"><div><p class="eyebrow">Administration</p><h1>Test Case Management</h1></div></div>
<section class="panel" style="margin-top:20px"><div class="table-wrap"><table><thead><tr><th>Name</th><th>Category</th><th>Active</th><th>Actions</th></tr></thead><tbody><?php foreach($tests as $t):?><tr><td><?=h($t['test_name'])?></td><td><?=h($t['category'])?></td><td><?= $t['is_active']?'Yes':'No'?></td><td><a class="button ghost small" href="<?=APP_BASE_URL?>/tests/index.php">Run</a></td></tr><?php endforeach;?></tbody></table></div></section>
<?php require dirname(__DIR__).'/includes/layout_end.php';
