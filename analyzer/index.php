<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/config/app.php';
$user = requireLogin();
$pageTitle = 'Lexical Analyzer';
$pageScripts = ['analyzer.js','editor.js','tokens.js'];
require dirname(__DIR__) . '/includes/layout_start.php';
?>
<div class="analyzer-layout">
  <section class="editor-panel panel">
    <div class="editor-toolbar"><div class="editor-dots"><i></i><i></i><i></i></div><div class="editor-actions">
      <label class="button ghost small" for="sourceUpload">Upload</label><input id="sourceUpload" type="file" accept=".cpp,.cc,.h,text/plain" hidden>
      <button class="button ghost small" type="button" id="loadSample">Sample</button><button class="icon-button" type="button" id="copyCode" title="Copy">⧉</button>
    </div></div>
    <div class="code-editor"><div class="line-numbers" id="lineNumbers">1</div><textarea id="sourceCode" spellcheck="false" aria-label="C++ source code"></textarea></div>
    <div class="editor-footer"><span id="lineCount">1 line</span><span id="charCount">0 characters</span></div>
  </section>
  <aside class="analysis-side">
    <div class="panel analysis-controls"><p class="eyebrow">Actions</p>
      <button class="button primary wide" type="button" id="analyzeButton">Analyze Code</button>
      <div class="form-grid">
        <input id="analysisTitle" placeholder="Analysis title" value="Untitled analysis">
        <select id="projectSelect"><option value="">No project</option></select>
      </div>
      <button class="button secondary wide" type="button" id="saveButton" disabled>Save Analysis</button>
      <button class="button ghost wide" type="button" id="clearButton">Clear Editor</button>
    </div>
    <div class="panel category-summary" id="categorySummary"><p class="eyebrow">Token Summary</p><div class="empty-summary">Analyze code to view counts</div></div>
  </aside>
</div>
<section class="panel results-panel"><div class="panel-header"><div><p class="eyebrow">Output</p><h3>Tokens</h3></div><div class="table-tools"><input id="tokenSearch" placeholder="Search tokens"><select id="tokenFilter"><option value="ALL">All categories</option><option>KEYWORD</option><option>IDENTIFIER</option><option>CONSTANT</option><option>OPERATOR</option><option>SEPARATOR</option><option>SPECIAL_CHARACTER</option><option>UNKNOWN/INVALID</option></select></div></div>
<div class="table-wrap"><table><thead><tr><th>#</th><th>Category</th><th>Lexeme</th><th>Line</th><th>Valid</th></tr></thead><tbody id="tokenTableBody"><tr><td colspan="5" class="empty-cell">No analysis results yet</td></tr></tbody></table></div></section>
<?php require dirname(__DIR__) . '/includes/layout_end.php'; ?>
