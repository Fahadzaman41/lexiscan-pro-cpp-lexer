<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/config/app.php';
$user=requireLogin();$pageTitle='Projects';$pageScripts=['projects.js'];
require dirname(__DIR__).'/includes/layout_start.php';
?>
<div class="welcome-row"><div><p class="eyebrow">Workspace</p><h1>Projects</h1></div><button class="button primary" type="button" id="newProject">+ New Project</button></div>
<section class="panel" style="margin-top:20px"><div class="panel-header"><h3>My Projects</h3></div><div class="table-wrap"><table><thead><tr><th>Name</th><th>Description</th><th>Analyses</th><th>Actions</th></tr></thead><tbody id="projectTable"></tbody></table></div></section>
<dialog id="projectDialog"><form id="projectForm" method="dialog"><h3>Create Project</h3><input type="hidden" name="csrf_token" value="<?=h(csrfToken())?>"><label>Project name<input name="project_name" required maxlength="160"></label><label>Description<textarea name="description" maxlength="2000"></textarea></label><div style="display:flex;justify-content:flex-end;gap:8px;margin-top:12px"><button class="button ghost" type="button" id="cancelProject">Cancel</button><button class="button primary" type="submit">Create</button></div></form></dialog>
<?php require dirname(__DIR__).'/includes/layout_end.php';?>
