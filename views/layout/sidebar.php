<aside id="sidebar">
  <div class="sb-head">
    <div class="logo-wrap" onclick="toggleSidebar()" title="Alternar menu">
      <?php if (!empty($appLogo)): ?>
      <img src="<?= $appLogo ?>" class="logo-img" alt="<?= $appName ?>"/>
      <?php else: ?>
      <div class="logo-ico">
        <?= $appLogoText ?? 'A' ?>
      </div>
      <?php endif; ?>
      <div class="logo-text">
        <div class="logo-name"><?= $appName ?></div>
        <div class="logo-ver">v<?= $appVersion ?? '1.0.0' ?></div>
      </div>
    </div>
  </div>
  
  <div class="sb-scroll">
    <nav>
      <!-- Dashboard -->
      <div class="nav-item" data-section="dashboard" data-tooltip="Dashboard">
        <a href="<?= $baseUrl ?>/" class="nav-main">
          <div class="nav-left">
            <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
              <rect x="3" y="3" width="7" height="7" rx="1.5"/>
              <rect x="14" y="3" width="7" height="7" rx="1.5"/>
              <rect x="3" y="14" width="7" height="7" rx="1.5"/>
              <rect x="14" y="14" width="7" height="7" rx="1.5"/>
            </svg>
            <span class="nav-label">Dashboard</span>
          </div>
        </a>
      </div>

      <!-- Grupo: Sistema -->
      <div class="nav-lbl">Sistema</div>
      
      <!-- Usuários com Submenu -->
      <div class="nav-item has-submenu" data-tooltip="Usuários">
        <div class="nav-main" onclick="toggleSubMenu(this)">
          <div class="nav-left">
            <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            <span class="nav-label">Usuários</span>
          </div>
          <svg class="nav-chevron" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
          </svg>
        </div>
        <div class="submenu">
          <a href="<?= $baseUrl ?>/users" class="submenu-item">
            <span class="submenu-dot"></span>
            Listar Usuários
          </a>
          <a href="<?= $baseUrl ?>/users/create" class="submenu-item">
            <span class="submenu-dot"></span>
            Criar Usuário
          </a>
        </div>
      </div>

      

      <!-- Relatórios com Submenu -->
      <div class="nav-item has-submenu" data-tooltip="Relatórios">
        <div class="nav-main" onclick="toggleSubMenu(this)">
          <div class="nav-left">
            <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <span class="nav-label">Relatórios</span>
          </div>
          <svg class="nav-chevron" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
          </svg>
        </div>
        <div class="submenu">
          <a href="#" class="submenu-item">
            <span class="submenu-dot"></span>
            Financeiro
          </a>
          <a href="#" class="submenu-item">
            <span class="submenu-dot"></span>
            Atividades
          </a>
          <a href="#" class="submenu-item">
            <span class="submenu-dot"></span>
            Exportar
          </a>
        </div>
      </div>

      <!-- Configurações -->
      <div class="nav-item has-submenu" data-tooltip="Configurações">
        <div class="nav-main" onclick="toggleSubMenu(this)">
          <div class="nav-left">
            <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
              <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <span class="nav-label">Configurações</span>
          </div>
          <svg class="nav-chevron" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
          </svg>
        </div>
        <div class="submenu">
          <a href="#" class="submenu-item">
            <span class="submenu-dot"></span>
            Geral
          </a>
          <a href="#" class="submenu-item">
            <span class="submenu-dot"></span>
            Sistema
          </a>
          <a href="#" class="submenu-item">
            <span class="submenu-dot"></span>
            Appearance
          </a>
        </div>
      </div>

      <!-- Grupo: Segurança -->
      <div class="nav-lbl">Segurança</div>

      <!-- Logs -->
      <div class="nav-item has-submenu" data-tooltip="Logs">
        <div class="nav-main" onclick="toggleSubMenu(this)">
          <div class="nav-left">
            <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <span class="nav-label">Logs</span>
          </div>
          <svg class="nav-chevron" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
          </svg>
        </div>
        <div class="submenu">
          <a href="#" class="submenu-item">
            <span class="submenu-dot"></span>
            Acesso
          </a>
          <a href="#" class="submenu-item">
            <span class="submenu-dot"></span>
            Erros
          </a>
        </div>
      </div>

      <!-- API -->
      <div class="nav-item has-submenu" data-tooltip="API">
        <div class="nav-main" onclick="toggleSubMenu(this)">
          <div class="nav-left">
            <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
              <path stroke-linecap="round" stroke-linejoin="round" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <span class="nav-label">API</span>
          </div>
          <svg class="nav-chevron" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
          </svg>
        </div>
        <div class="submenu">
          <a href="#" class="submenu-item">
            <span class="submenu-dot"></span>
            Endpoints
          </a>
          <a href="#" class="submenu-item">
            <span class="submenu-dot"></span>
            Chaves API
          </a>
        </div>
      </div>
    </nav>
  </div>
  
  <div class="sb-foot">
    <div class="user-card" onclick="openRight()">
      <?php $user = \App\Auth\Rbac::getUser(); ?>
      <?php if ($user): ?>
      <div class="avatar"><?= strtoupper(substr($user['name'] ?? 'U', 0, 2)) ?></div>
      <div class="user-texts">
        <div class="u-name"><?= htmlspecialchars($user['name'] ?? 'Usuário') ?></div>
        <div class="u-role"><?= \App\Auth\Rbac::getRoleLabel($user['role'] ?? 'user') ?></div>
      </div>
      <?php else: ?>
      <div class="avatar">?</div>
      <div class="user-texts">
        <div class="u-name">Visitante</div>
        <div class="u-role">Faça login</div>
      </div>
      <?php endif; ?>
    </div>
  </div>
</aside>

<!-- Mobile Overlay -->
<div class="sidebar-overlay" id="sidebar-overlay" onclick="closeMobileSidebar()"></div>