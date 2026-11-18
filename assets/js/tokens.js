window.renderTokens = function(tokens) {
  const body = document.getElementById('tokenTableBody');
  if (!body) return;
  const search = (document.getElementById('tokenSearch')?.value || '').toLowerCase();
  const filter = document.getElementById('tokenFilter')?.value || 'ALL';
  const filtered = tokens.filter(token => (filter === 'ALL' || token.category === filter) && (!search || token.lexeme.toLowerCase().includes(search) || token.category.toLowerCase().includes(search)));
  body.innerHTML = filtered.map(token => `<tr><td>${token.token_number}</td><td><span class="badge ${token.category.toLowerCase().replace(/[\/_]/g,'-')}">${escapeHtml(token.category)}</span></td><td><code>${escapeHtml(token.lexeme)}</code></td><td>${token.line_number}</td><td>${token.is_valid ? 'Yes' : 'No'}</td></tr>`).join('') || '<tr><td colspan="5" class="empty-cell">No matching tokens</td></tr>';
};
document.addEventListener('DOMContentLoaded', () => {
  document.getElementById('tokenSearch')?.addEventListener('input', () => window.currentTokens && renderTokens(window.currentTokens));
  document.getElementById('tokenFilter')?.addEventListener('change', () => window.currentTokens && renderTokens(window.currentTokens));
  const original = window.renderTokens;
  window.renderTokens = function(tokens) {
    window.currentTokens = tokens;
    original(tokens);
  };
});
