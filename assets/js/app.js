(function () {
  'function' === typeof window.fetch && window.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.nav-list a').forEach(link => {
      if (link.getAttribute('href') === location.pathname || location.pathname.endsWith(link.dataset.nav + '.php')) link.classList.add('active');
    });
    const mobileMenu = document.getElementById('mobileMenu');
    const sidebar = document.getElementById('sidebar');
    mobileMenu?.addEventListener('click', () => sidebar?.classList.toggle('open'));
    document.getElementById('logoutButton')?.addEventListener('click', async () => {
      const form = document.getElementById('logoutForm');
      await fetch(window.LEXISCAN_BASE + '/api/auth/logout.php', {method:'POST', body:new FormData(form)});
      location.href = window.LEXISCAN_BASE + '/login.php';
    });
    document.getElementById('themeToggle')?.addEventListener('click', () => document.body.classList.toggle('dark-mode'));
  });
  window.api = async function (url, options = {}) {
    options.headers = Object.assign({}, options.headers || {}, {'X-CSRF-Token': window.LEXISCAN_CSRF || ''});
    const response = await fetch(url, options);
    const contentType = response.headers.get('content-type') || '';
    if (!contentType.includes('application/json')) return {success: response.ok};
    const data = await response.json();
    if (!response.ok && !data.message) data.message = 'Request failed.';
    return data;
  };
  window.escapeHtml = value => String(value ?? '').replace(/[&<>"']/g, char => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[char]));
  window.toast = function(message, type='success') {
    const region = document.getElementById('toastRegion');
    if (!region) return;
    const item = document.createElement('div');
    item.className = 'toast ' + type;
    item.textContent = message;
    region.appendChild(item);
    setTimeout(() => item.remove(), 4000);
  };
})();
