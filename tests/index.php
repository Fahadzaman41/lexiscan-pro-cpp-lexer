<?php
declare(strict_types=1);
require_once dirname(__DIR__).'/config/app.php';$user=requireLogin();$pageTitle='Test Center';$pageScripts=['tests.js'];require dirname(__DIR__).'/includes/layout_start.php';
?>
<div class="welcome-row"><div><p class="eyebrow">Validation</p><h1>Test Center</h1></div><button class="button primary" type="button" id="runAll">Run All Tests</button></div>
<section class="dashboard-grid" style="margin-top:20px"><article class="panel"><div class="panel-header"><h3>University & Edge Cases</h3></div><div id="testList" style="max-height:520px;overflow:auto"></div></article><article class="panel"><div class="panel-header"><h3>Test Results</h3></div><div id="testResults" style="padding:18px"><p class="empty-state">Select a test or run all.</p></div></article></section>
<?php require dirname(__DIR__).'/includes/layout_end.php';?>
