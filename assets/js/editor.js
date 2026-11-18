document.addEventListener('DOMContentLoaded', () => {
  const editor = document.getElementById('sourceCode');
  const lines = document.getElementById('lineNumbers');
  if (!editor) return;
  function updateEditor() {
    const text = editor.value;
    const count = text.split('\n').length;
    lines.textContent = Array.from({length:count}, (_,i) => i + 1).join('\n');
    document.getElementById('lineCount').textContent = count + (count === 1 ? ' line' : ' lines');
    document.getElementById('charCount').textContent = text.length + ' characters';
  }
  editor.addEventListener('input', updateEditor);
  editor.addEventListener('scroll', () => lines.scrollTop = editor.scrollTop);
  document.getElementById('clearButton')?.addEventListener('click', () => {
    editor.value = '';
    updateEditor();
  });
  document.getElementById('copyCode')?.addEventListener('click', async () => {
    await navigator.clipboard.writeText(editor.value);
    toast('Source copied to clipboard');
  });
  document.getElementById('loadSample')?.addEventListener('click', () => {
    editor.value = '#include <iostream>\nusing namespace std;\n\nint main() {\n    int n = 10;\n    if (n >= 5 && n != 0) {\n        cout << "n is positive";\n    }\n    return 0;\n}';
    updateEditor();
  });
  document.getElementById('sourceUpload')?.addEventListener('change', async event => {
    const file = event.target.files[0];
    if (!file) return;
    const form = new FormData();
    form.append('source_file', file);
    form.append('csrf_token', window.LEXISCAN_CSRF);
    document.getElementById('analyzeButton').textContent = 'Reading file...';
    const data = await api(window.LEXISCAN_BASE + '/api/analyzer/analyze.php', {method:'POST', body:form});
    document.getElementById('analyzeButton').textContent = 'Analyze Code';
    if (!data.success) return toast(data.message, 'error');
    editor.value = data.source_code;
    updateEditor();
    window.renderTokens?.(data.data.tokens);
  });
  updateEditor();
});
