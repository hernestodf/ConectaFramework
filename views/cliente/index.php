<?php require dirname(__DIR__) . '/layout/header.php'; ?>

<header id="topbar">
  <div class="tb-left">
    <div class="breadcrumb">
      <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
      </svg>
      <span>Clientes</span>
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
  </div>
</header>

<?php require dirname(__DIR__) . '/layout/sidebar.php'; ?>

<div id="main">
  <div class="content">
    <section class="section active" id="sec-clientes">
      <div class="section-header">
        <div class="section-icon">
          <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
          </svg>
        </div>
        <div>
          <div class="section-title">Gerenciar Clientes</div>
          <div class="section-sub">Cadastro de clientes com CEP/CNPJ</div>
        </div>
      </div>
      <div class="divider"></div>

      <div class="card">
        <div class="card-head">
          <span class="card-title">Lista de Clientes <small>(<?= count($clientes) ?> <?= count($clientes) === 1 ? 'cliente' : 'clientes' ?>)</small></span>
          <a href="<?= $baseUrl ?>/clientes/create" class="btn btn-sm btn-cyan">Novo</a>
        </div>
        <div class="card-body" style="padding:0">
          <?php if (empty($clientes)): ?>
          <div class="table-empty">
            <div class="table-empty-flex">
              <svg class="table-empty-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
              </svg>
              <div>Nenhum cliente encontrado</div>
              <div style="font-size:12px;color:var(--text-4)">Clique em "Novo" para adicionar</div>
            </div>
          </div>
          <?php else: ?>
          <table class="table-default">
            <thead>
              <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>CNPJ/CPF</th>
                <th>Email</th>
                <th>Cidade</th>
                <th>Status</th>
                <th>Ações</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($clientes as $c): ?>
              <tr>
                <td><?= $c['id'] ?></td>
                <td><?= htmlspecialchars($c['nome']) ?></td>
                <td><?= !empty($c['cnpj']) ? htmlspecialchars($c['cnpj']) : htmlspecialchars($c['cpf'] ?? '-') ?></td>
                <td><?= htmlspecialchars($c['email'] ?? '-') ?></td>
                <td><?= htmlspecialchars($c['cidade'] ?? '-') ?></td>
                <td>
                  <button type="button" class="btn btn-sm <?= $c['status'] ? 'btn-green' : 'btn-red' ?>" onclick="toggleCliente(<?= $c['id'] ?>, this)">
                    <?= $c['status'] ? 'Ativo' : 'Inativo' ?>
                  </button>
                </td>
                <td>
                  <a href="<?= $baseUrl ?>/clientes/edit/<?= $c['id'] ?>" class="btn btn-sm btn-cyan">Editar</a>
                  <form method="POST" action="<?= $baseUrl ?>/clientes/delete/<?= $c['id'] ?>" style="display:inline">
                    <input type="hidden" name="_csrf_token" value="<?= \App\Core\Csrf::getToken() ?>"/>
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

<script>
function toggleCliente(id, btn) {
  fetch(BASE_URL + '/clientes/toggle/' + id, {
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
}
</script>

<?php require dirname(__DIR__) . '/layout/footer.php'; ?>