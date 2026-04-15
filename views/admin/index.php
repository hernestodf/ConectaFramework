<?php require dirname(__DIR__) . '/layout/header.php'; ?>

<header id="topbar">
  <div class="tb-left">
    <div class="breadcrumb">
      <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
      </svg>
      <span>Admin</span>
      <span class="sep">/</span>
      <span class="cur">Dashboard</span>
    </div>
  </div>
  <div class="tb-right">
    <button class="icon-btn" onclick="document.getElementById('form-logout').submit()">
      <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
      </svg>
    </button>
    <form id="form-logout" method="POST" action="<?= $baseUrl ?>/auth/logout" style="display:none"></form>
  </div>
</header>

<?php require dirname(__DIR__) . '/layout/sidebar.php'; ?>

<div id="main">
  <div class="content">
    <section class="section active" id="sec-admin">
      <div class="section-header">
        <div class="section-icon" style="background:var(--neon-purple)">
          <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
          </svg>
        </div>
        <div>
          <div class="section-title">Painel Admin</div>
          <div class="section-sub">Bem-vindo, <?= htmlspecialchars($user['name'] ?? 'Admin') ?></div>
        </div>
      </div>
      <div class="divider"></div>

      <div class="alert cyan">
        <div class="alert-ico">
          <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
        <div class="alert-body">
          <div class="alert-title">Área Administrativa</div>
          <div class="alert-desc">Você está logado como <?= htmlspecialchars(\App\Auth\Rbac::getRoleLabel($user['role'] ?? 'user')) ?></div>
        </div>
      </div>

      <div class="col3">
        <div class="custom-card">
          <div class="card-stat">
            <div class="card-stat-icon" style="background:var(--neon-cyan)">
              <svg fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
              </svg>
            </div>
            <div class="card-stat-val" style="color:var(--neon-cyan)">3</div>
            <div class="card-stat-lbl">Usuários</div>
          </div>
        </div>
        <div class="custom-card">
          <div class="card-stat">
            <div class="card-stat-icon" style="background:var(--neon-green)">
              <svg fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <div class="card-stat-val" style="color:var(--neon-green)">Online</div>
            <div class="card-stat-lbl">Status</div>
          </div>
        </div>
        <div class="custom-card">
          <div class="card-stat">
            <div class="card-stat-icon" style="background:var(--neon-purple)">
              <svg fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <div class="card-stat-val" style="color:var(--neon-purple)">v1.0</div>
            <div class="card-stat-lbl">Versão</div>
          </div>
        </div>
      </div>
    </section>
  </div>
</div>

<?php require dirname(__DIR__) . '/layout/footer.php'; ?>