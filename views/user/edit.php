<?php require dirname(__DIR__) . '/layout/header.php'; ?>

<header id="topbar">
  <div class="tb-left">
    <div class="breadcrumb">
      <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
      </svg>
      <span>Usuários</span>
      <span class="sep">/</span>
      <span class="cur">Editar</span>
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
    <section class="section active" id="sec-users-edit">
      <div class="section-header">
        <div class="section-icon">
          <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
          </svg>
        </div>
        <div>
          <div class="section-title">Editar Usuário</div>
          <div class="section-sub"><?= htmlspecialchars($user['name']) ?></div>
        </div>
      </div>
      <div class="divider"></div>

      <div class="card">
        <div class="card-head">
          <span class="card-title">Dados do Usuário</span>
        </div>
        <div class="card-body">
          <?php if (!empty($error)): ?>
          <div class="alert red">
            <div class="alert-ico">
              <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <div class="alert-body">
              <div class="alert-title">Erro</div>
              <div class="alert-desc"><?= htmlspecialchars($error) ?></div>
            </div>
          </div>
          <?php endif; ?>
          
          <form method="POST" action="<?= $baseUrl ?>/users/update/<?= $user['id'] ?>">
            <input type="hidden" name="_csrf_token" value="<?= \App\Core\Csrf::getToken() ?>"/>
            
            <div class="fg">
              <div class="fl">Nome *</div>
              <input type="text" name="nome" class="fi" value="<?= htmlspecialchars($user['name']) ?>" required>
            </div>
            
            <div class="fg">
              <div class="fl">Email *</div>
              <input type="email" name="email" class="fi" value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>
            
            <div class="col2">
              <div class="fg">
                <div class="fl">Telefone</div>
                <input type="text" name="telefone" class="fi" data-mask="(XX) XXXX-XXXX" value="<?= htmlspecialchars($user['telefone'] ?? '') ?>" placeholder="(XX) XXXX-XXXX">
              </div>
              <div class="fg">
                <div class="fl">Celular</div>
                <input type="text" name="celular" class="fi" data-mask="(XX) XXXXX-XXXX" value="<?= htmlspecialchars($user['celular'] ?? '') ?>" placeholder="(XX) XXXXX-XXXX">
              </div>
            </div>
            
            <div class="fg">
              <div class="fl">CEP</div>
              <input type="text" name="cep" class="fi" data-mask="XXXXX-XXX" value="<?= htmlspecialchars($user['cep'] ?? '') ?>" placeholder="XXXXX-XXX">
            </div>
            
            <div class="fg">
              <div class="fl">Nova Senha</div>
              <input type="password" name="password" class="fi" placeholder="Deixe em branco para manter atual">
            </div>
            
            <div class="col2">
              <div class="fg">
                <div class="fl">Função</div>
                <select name="role" class="fi">
                  <option value="user" <?= $user['role'] == 'user' ? 'selected' : '' ?>>Usuário</option>
                  <option value="manager" <?= $user['role'] == 'manager' ? 'selected' : '' ?>>Gerente</option>
                  <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                </select>
              </div>
              <div class="fg">
                <div class="fl">Status</div>
                <select name="status" class="fi">
                  <option value="1" <?= $user['status'] == 1 ? 'selected' : '' ?>>Ativo</option>
                  <option value="0" <?= $user['status'] == 0 ? 'selected' : '' ?>>Inativo</option>
                </select>
              </div>
            </div>
            
            <div class="flex-row-gap" style="margin-top:16px">
              <a href="<?= $baseUrl ?>/users" class="btn btn-red">Cancelar</a>
              <button type="submit" class="btn btn-cyan">Salvar</button>
            </div>
          </form>
        </div>
      </div>
    </section>
  </div>
</div>

<script>
// Máscaras automáticas
document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('[data-mask]').forEach(function(input) {
    input.addEventListener('input', function(e) {
      let value = e.target.value.replace(/\D/g, '');
      const mask = e.target.dataset.mask;
      let result = '';
      let i = 0;
      
      for (let char of mask) {
        if (char === 'X') {
          if (value[i] !== undefined) {
            result += value[i];
            i++;
          }
        } else {
          result += char;
        }
      }
      e.target.value = result;
    });
  });
});
</script>

<?php require dirname(__DIR__) . '/layout/footer.php'; ?>