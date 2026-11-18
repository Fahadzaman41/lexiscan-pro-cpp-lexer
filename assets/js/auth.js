document.addEventListener('DOMContentLoaded', () => {
  const login = document.getElementById('loginForm');
  login?.addEventListener('submit', async event => {
    event.preventDefault();
    const message = document.getElementById('formMessage');
    message.textContent = 'Signing in...';
    const data = await api(window.LEXISCAN_BASE + '/api/auth/login.php', {method:'POST', body:new FormData(login)});
    message.textContent = data.message || '';
    message.className = 'form-message ' + (data.success ? 'success' : 'error');
    if (data.success) location.href = data.redirect;
  });
  const register = document.getElementById('registerForm');
  register?.addEventListener('submit', async event => {
    event.preventDefault();
    const message = document.getElementById('formMessage');
    message.textContent = 'Creating account...';
    const data = await api(window.LEXISCAN_BASE + '/api/auth/register.php', {method:'POST', body:new FormData(register)});
    message.textContent = data.message || '';
    message.className = 'form-message ' + (data.success ? 'success' : 'error');
    if (data.success) location.href = data.redirect;
  });
});
