document.addEventListener('DOMContentLoaded', () => {
  const editor = document.getElementById('sourceCode');
  if (!editor) return;
  let latest = null;
  const analyzeButton = document.getElementById('analyzeButton');
  analyzeButton.addEventListener('click', async () => {
    const form = new FormData();
    form.append('source_code', editor.value);
    form.append('csrf_token', window.LEXISCAN_CSRF);
    analyzeButton.disabled = true;
    analyzeButton.textContent = 'Analyzing...';
    const data = await api(window.LEXISCAN_BASE + '/api/analyzer/analyze.php', {method:'POST', body:form});
    analyzeButton.disabled = false;
    analyzeButton.textContent = 'Analyze Code';
    if (!data.success) return toast(data.message, 'error');
    latest = data.data;
    window.renderTokens?.(latest.tokens);
    renderSummary(latest.counts);
    document.getElementById('saveButton').disabled = false;
  });
  document.getElementById('saveButton').addEventListener('click', async () => {
    if (!latest) return;
    const form = new FormData();
    form.append('source_code', editor.value);
    form.append('analysis_title', document.getElementById('analysisTitle').value);
    form.append('project_id', document.getElementById('projectSelect').value);
    form.append('source_filename', '');
    form.append('csrf_token', window.LEXISCAN_CSRF);
    const data = await api(window.LEXISCAN_BASE + '/api/analyzer/save.php', {method:'POST', body:form});
    if (!data.success) return toast(data.message, 'error');
    toast('Analysis saved');
  });
  api(window.LEXISCAN_BASE + '/api/projects/list.php').then(data => {
    if (!data.success) return;
    const select = document.getElementById('projectSelect');
    data.items.forEach(project => {
      const option = document.createElement('option');
      option.value = project.id;
      option.textContent = project.project_name;
      select.appendChild(option);
    });
  });
  function renderSummary(counts) {
    const container = document.getElementById('categorySummary');
    container.innerHTML = '<p class="eyebrow">Token Summary</p>' + Object.entries(counts).filter(([key]) => key !== 'UNKNOWN/INVALID').map(([key,value]) =>
      `<div class="summary-row"><span><i class="category-dot ${categoryClass(key)}"></i>${escapeHtml(key)}</span><strong>${value}</strong></div>`
    ).join('');
  }
  function categoryClass(category) {
    return {'KEYWORD':'blue','IDENTIFIER':'purple','CONSTANT':'green','OPERATOR':'orange','SEPARATOR':'cyan','SPECIAL_CHARACTER':'pink'}[category] || 'red';
  }
});
