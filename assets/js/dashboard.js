document.addEventListener('DOMContentLoaded', async () => {
  if (!document.getElementById('metricGrid')) return;
  const data = await api(window.LEXISCAN_BASE + '/api/dashboard/stats.php');
  if (!data.success) return toast(data.message || 'Could not load dashboard', 'error');
  document.getElementById('statAnalyses').textContent = data.stats.analyses;
  document.getElementById('statTokens').textContent = data.stats.tokens;
  document.getElementById('statPassed').textContent = data.stats.test_passed;
  document.getElementById('statProjects').textContent = data.stats.projects;
  const chart = document.getElementById('categoryChart');
  const entries = Object.entries(data.stats.distribution);
  const total = entries.reduce((sum,[,count]) => sum + count, 0);
  chart.innerHTML = entries.length ? entries.map(([category,count]) => `<div class="chart-row"><span>${escapeHtml(category)}</span><div class="bar-track"><i class="${category.toLowerCase().replace(/[\/_]/g,'-')}" style="width:${total ? count / total * 100 : 0}%"></i></div><strong>${count}</strong></div>`).join('') : '<p class="empty-state">No token data yet</p>';
  document.getElementById('recentAnalyses').innerHTML = data.recent.map(row =>
    `<tr><td><a href="${window.LEXISCAN_BASE}/analyzer/view.php?id=${row.id}">${escapeHtml(row.analysis_title)}</a></td><td>${row.total_tokens}</td><td>${escapeHtml(row.created_at)}</td></tr>`
  ).join('') || '<tr><td colspan="3" class="empty-cell">No analyses yet</td></tr>';
});
