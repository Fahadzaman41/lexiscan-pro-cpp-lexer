    </div>
  </main>
</div>
<div class="toast-region" id="toastRegion"></div>
<script>window.LEXISCAN_BASE = <?= json_encode(APP_BASE_URL) ?>; window.LEXISCAN_CSRF = <?= json_encode(csrfToken()) ?>;</script>
<script src="<?= APP_BASE_URL ?>/assets/js/app.js"></script>
<?php if (!empty($pageScripts)): foreach ($pageScripts as $script): ?>
<script src="<?= APP_BASE_URL ?>/assets/js/<?= h($script) ?>"></script>
<?php endforeach; endif; ?>
</body>
</html>
