<?php require dirname(__DIR__) . '/layout/header.php'; ?>

<header id="topbar">
  <div class="tb-left">
    <div class="breadcrumb">
      <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
      </svg>
      <span>Usuários</span>
      <span class="sep">/</span>
      <span class="cur">Lista</span>
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
    <section class="section active" id="sec-users">
      <div class="section-header">
        <div class="section-icon">
          <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
          </svg>
        </div>
        <div>
          <div class="section-title">Gerenciar Usuários</div>
          <div class="section-sub">Cadastro e controle de acesso</div>
        </div>
      </div>
      <div class="divider"></div>

      <div class="card">
        <div class="card-head">
          <span class="card-title">Lista de Usuários <small>(<?= count($users) ?> <?= count($users) === 1 ? 'usuário' : 'usuários' ?>)</small></span>
          <a href="<?= $baseUrl ?>/users/create" class="btn btn-sm btn-cyan">Novo</a>
        </div>
        <div class="card-body" style="padding:0">
          <?php if (empty($users)): ?>
          <div class="table-empty">
            <div class="table-empty-flex">
              <svg class="table-empty-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
              </svg>
              <div>Nenhum usuário encontrado</div>
              <div style="font-size:12px;color:var(--text-4)">Clique em "Novo" para adicionar</div>
            </div>
          </div>
          <?php else: ?>
          <table class="table-default">
            <thead>
              <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Email</th>
                <th>Telefone</th>
                <th>Função</th>
                <th>Status</th>
                <th>Ações</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($users as $u): ?>
              <tr>
                <td><?= $u['id'] ?></td>
                <td><?= htmlspecialchars($u['name']) ?></td>
                <td><?= htmlspecialchars($u['email']) ?></td>
                <td><?= htmlspecialchars($u['telefone'] ?? '-') ?></td>
                <td><span class="badge <?= $u['role'] === 'admin' ? 'purple' : 'cyan' ?>"><?= htmlspecialchars($u['role']) ?></span></td>
                <td>
                  <button type="button" class="btn btn-sm <?= $u['status'] ? 'btn-green' : 'btn-red' ?>" onclick="toggleUser(<?= $u['id'] ?>, this)">
                    <?= $u['status'] ? 'Ativo' : 'Inativo' ?>
                  </button>
                </td>
                <td>
                  <a href="<?= $baseUrl ?>/users/edit/<?= $u['id'] ?>" class="btn btn-sm btn-cyan">Editar</a>
                  <form method="POST" action="<?= $baseUrl ?>/users/delete/<?= $u['id'] ?>" style="display:inline">
                    <input type="hidden" name="_csrf_token" value="<?= \App\Core\Csrf::getToken() ?>" style="display:none"/>
                    <button type="submit" class="btn btn-sm btn-red" onclick="return confirm('Excluir?')">Excluir</button>
                  </form>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
          <?php endif; ?>
        </div>
      </div>
    </section>
  </div>
</div>



<script>function toggleUser(id, btn) {
  fetch(BASE_URL + '/users/toggle/' + id, {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: '_csrf_token=' + CSRF_TOKEN
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      btn.className = 'btn btn-sm ' + (data.status ? 'btn-green' : 'btn-red');
      btn.textContent = data.status ? 'Ativo' : 'Inativo';
    }
  });
}</script>

<?php require dirname(__DIR__) . '/layout/footer.php'; ?>