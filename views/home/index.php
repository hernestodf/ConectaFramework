<?php require dirname(__DIR__) . '/layout/header.php'; ?>

<header id="topbar">
  <div class="tb-left">
    <div class="breadcrumb">
      <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
      </svg>
      <span>Início</span>
      <span class="sep">/</span>
      <span class="cur">Dashboard</span>
    </div>
  </div>
  <div class="tb-right">
    <button class="icon-btn" onclick="openRight()" title="Perfil">
      <?php $user = \App\Auth\Rbac::getUser(); ?>
      <?php if ($user): ?>
      <div class="avatar-small"><?= strtoupper(substr($user['name'] ?? 'U', 0, 1)) ?></div>
      <?php else: ?>
      <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
      </svg>
      <?php endif; ?>
    </button>
    <button class="icon-btn" title="Notificações">
      <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
      </svg>
    </button>
  </div>
</header>

<?php require dirname(__DIR__) . '/layout/sidebar.php'; ?>

<div id="main">
  <div class="content">
    <section class="section active" id="sec-dashboard">
      <div class="section-header">
        <div class="section-icon">
          <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
          </svg>
        </div>
        <div>
          <div class="section-title">NovoFramework</div>
          <div class="section-sub">PHP Framework com RBAC</div>
        </div>
      </div>
      <div class="divider"></div>

      <div class="card">
        <div class="card-head">
          <span class="card-title">Status da Conexão</span>
          <?php if ($connection['success']): ?>
          <span class="badge green"><span class="dot pulse"></span>Conectado</span>
          <?php else: ?>
          <span class="badge red"><span class="dot pulse"></span>Erro</span>
          <?php endif; ?>
        </div>
        <div class="card-body">
          <?php if ($connection['success']): ?>
          <div class="alert green">
            <div class="alert-ico">
              <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <div class="alert-body">
              <div class="alert-title"><?= $connection['message'] ?></div>
              <div class="alert-desc">Host: <?= htmlspecialchars($connection['host']) ?> | Database: <?= htmlspecialchars($connection['database']) ?></div>
            </div>
          </div>
          <?php else: ?>
          <div class="alert red">
            <div class="alert-ico">
              <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <div class="alert-body">
              <div class="alert-title">Erro na Conexão</div>
              <div class="alert-desc"><?= htmlspecialchars($connection['message']) ?></div>
            </div>
          </div>
          <?php endif; ?>
        </div>
      </div>

      <div class="col2">
        <div class="custom-card">
          <div class="card-stat">
            <div class="card-stat-icon" style="background:var(--neon-green)">
              <svg fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
              </svg>
            </div>
            <div class="card-stat-val" style="color:var(--neon-cyan)"><?= count($users) ?></div>
            <div class="card-stat-lbl">Usuários no Banco</div>
          </div>
        </div>
        <div class="custom-card">
          <div class="card-stat">
            <div class="card-stat-icon" style="background:var(--neon-cyan)">
              <svg fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <div class="card-stat-val" style="color:var(--neon-cyan)"><?= date('H:i') ?></div>
            <div class="card-stat-lbl">Hora do Servidor</div>
          </div>
        </div>
      </div>

      <?php if (!empty($users)): ?>
      <div class="card" style="margin-top:16px">
        <div class="card-head">
          <span class="card-title">Lista de Usuários</span>
        </div>
        <div class="card-body" style="padding:0">
          <table class="table-default">
            <thead>
              <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Email</th>
                <th>Função</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($users as $u): ?>
              <tr>
                <td><?= $u['id'] ?></td>
                <td><?= htmlspecialchars($u['name']) ?></td>
                <td><?= htmlspecialchars($u['email']) ?></td>
                <td><span class="badge <?= $u['role'] === 'admin' ? 'purple' : 'cyan' ?>"><?= htmlspecialchars($u['role']) ?></span></td>
                <td><span class="badge <?= $u['status'] ? 'green' : 'red' ?>"><?= $u['status'] ? 'Ativo' : 'Inativo' ?></span></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
      <?php else: ?>
      <div class="card" style="margin-top:16px">
        <div class="card-body">
          <div class="table-empty">
            <div class="table-empty-flex">
              <svg class="table-empty-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H3v2c0 .656.126 1.283.356 1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
              </svg>
              <div>Nenhum usuário encontrado</div>
              <div style="font-size:12px;color:var(--text-4)">Crie a tabela users no banco de dados</div>
            </div>
          </div>
        </div>
      </div>
      <?php endif; ?>
    </section>
  </div>
</div>

<?php require dirname(__DIR__) . '/layout/footer.php'; ?>