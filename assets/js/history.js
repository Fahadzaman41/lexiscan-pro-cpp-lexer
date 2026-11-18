document.addEventListener('DOMContentLoaded', () => {
  const historyTable = document.getElementById('historyTable');
  if (historyTable) loadHistory();
  document.getElementById('historySearch')?.addEventListener('input', debounce(loadHistory, 300));
  async function loadHistory() {
    const search = document.getElementById('historySearch')?.value || '';
    const data = await api(window.LEXISCAN_BASE + '/api/analyzer/history.php?search=' + encodeURIComponent(search));
    if (!data.success) return;
    historyTable.innerHTML = data.items.map(row => `<tr><td><a href="${window.LEXISCAN_BASE}/analyzer/view.php?id=${row.id}">${escapeHtml(row.analysis_title)}</a></td><td>${escapeHtml(row.project_name || 'None')}</td><td>${row.total_tokens}</td><td>${row.invalid_count}</td><td>${escapeHtml(row.created_at)}</td><td><button class="button ghost small" data-delete="${row.id}" type="button">Delete</button></td></tr>`).join('') || '<tr><td colspan="6" class="empty-cell">No saved analyses</td></tr>';
  }
  historyTable?.addEventListener('click', async event => {
    const button = event.target.closest('[data-delete]');
    if (!button || !confirm('Delete this analysis?')) return;
    const form = new FormData();
    form.append('id', button.dataset.delete);
    form.append('csrf_token', window.LEXISCAN_CSRF);
    const data = await api(window.LEXISCAN_BASE + '/api/analyzer/delete.php', {method:'POST', body:form});
    if (!data.success) return toast(data.message, 'error');
    toast(data.message);
    loadHistory();
  });
  const selector = document.getElementById('analysisSelector');
  async function loadTokens() {
    if (!selector?.value) return;
    const category = document.getElementById('tokenFilter')?.value || 'ALL';
    const search = document.getElementById('tokenSearch')?.value || '';
    const data = await api(window.LEXISCAN_BASE + `/api/tokens/list.php?analysis_id=${selector.value}&category=${encodeURIComponent(category)}&search=${encodeURIComponent(search)}`);
    if (data.success) renderTokens(data.items);
  }
  selector?.addEventListener('change', loadTokens);
  document.getElementById('tokenFilter')?.addEventListener('change', loadTokens);
  document.getElementById('tokenSearch')?.addEventListener('input', debounce(loadTokens, 300));
});
function debounce(fn, wait) { let timer; return (...args) => { clearTimeout(timer); timer=setTimeout(() => fn(...args), wait); }; }
