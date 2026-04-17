<?php $baseUrl = \App\Core\Env::get('BASE_URL', ''); ?>
</div>

<!-- Right Off-canvas (User Profile) -->
<div class="offcanvas offcanvas-right" id="offcanvas-right">
  <div class="offcanvas-header">
    <div class="offcanvas-title">Perfil</div>
    <button class="offcanvas-close" onclick="closeRight()">
      <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
      </svg>
    </button>
  </div>
  <div class="offcanvas-body">
    <?php $user = \App\Auth\Rbac::getUser(); ?>
    <?php if ($user): ?>
    <div class="profile-hero">
      <div class="profile-avatar">
        <?= strtoupper(substr($user['name'] ?? 'U', 0, 2)) ?>
      </div>
      <div class="profile-name"><?= htmlspecialchars($user['name'] ?? 'Usuário') ?></div>
      <div class="profile-role"><?= \App\Auth\Rbac::getRoleLabel($user['role'] ?? 'user') ?></div>
      <div class="profile-email"><?= htmlspecialchars($user['email'] ?? '') ?></div>
    </div>
    <div class="profile-actions">
      <a href="/admin" class="btn btn-cyan btn-block">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" width="16" height="16">
          <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
          <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        Painel Admin
      </a>
      <button class="btn btn-ghost btn-block" onclick="document.getElementById('form-logout').submit()">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" width="16" height="16">
          <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
        </svg>
        Sair
      </button>
    </div>
    <form id="form-logout" method="POST" action="<?= $baseUrl ?>/auth/logout" style="display:none"></form>
    <?php else: ?>
    <div class="profile-hero">
      <div class="profile-avatar">?</div>
      <div class="profile-name">Visitante</div>
      <div class="profile-role">Faça login para continuar</div>
    </div>
    <div class="profile-actions">
      <a href="/auth/login" class="btn btn-cyan btn-block">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" width="16" height="16">
          <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
        </svg>
        Login
      </a>
    </div>
    <?php endif; ?>
  </div>
</div>

<!-- Toast Container -->
<div id="toast-container"></div>

<script>
// Global functions already in header
</script>
</body>
</html>