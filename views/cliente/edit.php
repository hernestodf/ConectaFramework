<?php require dirname(__DIR__) . '/layout/header.php'; ?>

<header id="topbar">
  <div class="tb-left">
    <div class="breadcrumb">
      <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
      </svg>
      <span>Clientes</span>
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
  </div>
</header>

<?php require dirname(__DIR__) . '/layout/sidebar.php'; ?>

<div id="main">
  <div class="content">
    <section class="section active" id="sec-clientes-edit">
      <div class="section-header">
        <div class="section-icon">
          <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
          </svg>
        </div>
        <div>
          <div class="section-title">Editar Cliente</div>
          <div class="section-sub"><?= htmlspecialchars($cliente['nome']) ?></div>
        </div>
      </div>
      <div class="divider"></div>

      <div class="card">
        <div class="card-head">
          <span class="card-title">Dados do Cliente</span>
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
          
          <form method="POST" action="<?= $baseUrl ?>/clientes/update/<?= $cliente['id'] ?>">
            <input type="hidden" name="_csrf_token" value="<?= \App\Core\Csrf::getToken() ?>"/>
            
            <div class="fg">
              <div class="fl">CNPJ</div>
              <input type="text" name="cnpj" id="cnpj" class="fi" data-mask="XX.XXX.XXX/XXXX-XX" value="<?= htmlspecialchars($cliente['cnpj'] ?? '') ?>" placeholder="XX.XXX.XXX/XXXX-XX">
            </div>
            
            <div class="fg">
              <div class="fl">CPF</div>
              <input type="text" name="cpf" id="cpf" class="fi" data-mask="XXX.XXX.XXX-XX" value="<?= htmlspecialchars($cliente['cpf'] ?? '') ?>" placeholder="XXX.XXX.XXX-XX">
            </div>
            
            <div class="fg">
              <div class="fl">Nome / Razão Social *</div>
              <input type="text" name="nome" id="nome" class="fi" value="<?= htmlspecialchars($cliente['nome']) ?>" required>
            </div>
            
            <div class="col2">
              <div class="fg">
                <div class="fl">Telefone</div>
                <input type="text" name="telefone" id="telefone" class="fi" data-mask="(XX) XXXX-XXXX" value="<?= htmlspecialchars($cliente['telefone'] ?? '') ?>" placeholder="(XX) XXXX-XXXX">
              </div>
              <div class="fg">
                <div class="fl">Celular</div>
                <input type="text" name="celular" id="celular" class="fi" data-mask="(XX) XXXXX-XXXX" value="<?= htmlspecialchars($cliente['celular'] ?? '') ?>" placeholder="(XX) XXXXX-XXXX">
              </div>
            </div>
            
            <div class="fg">
              <div class="fl">Email</div>
              <input type="email" name="email" class="fi" value="<?= htmlspecialchars($cliente['email'] ?? '') ?>" placeholder="email@exemplo.com">
            </div>
            
            <div class="divider" style="margin: 20px 0"></div>
            
            <div class="fg">
              <div class="fl">CEP</div>
              <input type="text" name="cep" id="cep" class="fi" data-mask="XXXXX-XXX" value="<?= htmlspecialchars($cliente['cep'] ?? '') ?>" placeholder="XXXXX-XXX">
            </div>
            
            <div class="fg">
              <div class="fl">Logradouro</div>
              <input type="text" name="logradouro" id="logradouro" class="fi" value="<?= htmlspecialchars($cliente['logradouro'] ?? '') ?>" placeholder="Rua,avenida, etc">
            </div>
            
            <div class="col2">
              <div class="fg">
                <div class="fl">Número</div>
                <input type="text" name="numero" id="numero" class="fi" value="<?= htmlspecialchars($cliente['numero'] ?? '') ?>" placeholder="S/N">
              </div>
              <div class="fg">
                <div class="fl">Complemento</div>
                <input type="text" name="complemento" id="complemento" class="fi" value="<?= htmlspecialchars($cliente['complemento'] ?? '') ?>" placeholder="Apto, sala, etc">
              </div>
            </div>
            
            <div class="col2">
              <div class="fg">
                <div class="fl">Bairro</div>
                <input type="text" name="bairro" id="bairro" class="fi" value="<?= htmlspecialchars($cliente['bairro'] ?? '') ?>" placeholder="Bairro">
              </div>
              <div class="fg">
                <div class="fl">Cidade</div>
                <input type="text" name="cidade" id="cidade" class="fi" value="<?= htmlspecialchars($cliente['cidade'] ?? '') ?>" placeholder="Cidade">
              </div>
            </div>
            
            <div class="fg">
              <div class="fl">UF</div>
              <select name="uf" id="uf" class="fi">
                <option value="">Selecione</option>
                <option value="AC" <?= ($cliente['uf'] ?? '') == 'AC' ? 'selected' : '' ?>>AC</option>
                <option value="AL" <?= ($cliente['uf'] ?? '') == 'AL' ? 'selected' : '' ?>>AL</option>
                <option value="AP" <?= ($cliente['uf'] ?? '') == 'AP' ? 'selected' : '' ?>>AP</option>
                <option value="AM" <?= ($cliente['uf'] ?? '') == 'AM' ? 'selected' : '' ?>>AM</option>
                <option value="BA" <?= ($cliente['uf'] ?? '') == 'BA' ? 'selected' : '' ?>>BA</option>
                <option value="CE" <?= ($cliente['uf'] ?? '') == 'CE' ? 'selected' : '' ?>>CE</option>
                <option value="DF" <?= ($cliente['uf'] ?? '') == 'DF' ? 'selected' : '' ?>>DF</option>
                <option value="ES" <?= ($cliente['uf'] ?? '') == 'ES' ? 'selected' : '' ?>>ES</option>
                <option value="GO" <?= ($cliente['uf'] ?? '') == 'GO' ? 'selected' : '' ?>>GO</option>
                <option value="MA" <?= ($cliente['uf'] ?? '') == 'MA' ? 'selected' : '' ?>>MA</option>
                <option value="MT" <?= ($cliente['uf'] ?? '') == 'MT' ? 'selected' : '' ?>>MT</option>
                <option value="MS" <?= ($cliente['uf'] ?? '') == 'MS' ? 'selected' : '' ?>>MS</option>
                <option value="MG" <?= ($cliente['uf'] ?? '') == 'MG' ? 'selected' : '' ?>>MG</option>
                <option value="PA" <?= ($cliente['uf'] ?? '') == 'PA' ? 'selected' : '' ?>>PA</option>
                <option value="PB" <?= ($cliente['uf'] ?? '') == 'PB' ? 'selected' : '' ?>>PB</option>
                <option value="PR" <?= ($cliente['uf'] ?? '') == 'PR' ? 'selected' : '' ?>>PR</option>
                <option value="PE" <?= ($cliente['uf'] ?? '') == 'PE' ? 'selected' : '' ?>>PE</option>
                <option value="PI" <?= ($cliente['uf'] ?? '') == 'PI' ? 'selected' : '' ?>>PI</option>
                <option value="RJ" <?= ($cliente['uf'] ?? '') == 'RJ' ? 'selected' : '' ?>>RJ</option>
                <option value="RN" <?= ($cliente['uf'] ?? '') == 'RN' ? 'selected' : '' ?>>RN</option>
                <option value="RS" <?= ($cliente['uf'] ?? '') == 'RS' ? 'selected' : '' ?>>RS</option>
                <option value="RO" <?= ($cliente['uf'] ?? '') == 'RO' ? 'selected' : '' ?>>RO</option>
                <option value="RR" <?= ($cliente['uf'] ?? '') == 'RR' ? 'selected' : '' ?>>RR</option>
                <option value="SC" <?= ($cliente['uf'] ?? '') == 'SC' ? 'selected' : '' ?>>SC</option>
                <option value="SP" <?= ($cliente['uf'] ?? '') == 'SP' ? 'selected' : '' ?>>SP</option>
                <option value="SE" <?= ($cliente['uf'] ?? '') == 'SE' ? 'selected' : '' ?>>SE</option>
                <option value="TO" <?= ($cliente['uf'] ?? '') == 'TO' ? 'selected' : '' ?>>TO</option>
              </select>
            </div>
            
            <div class="fg">
              <div class="fl">Status</div>
              <select name="status" class="fi">
                <option value="1" <?= $cliente['status'] == 1 ? 'selected' : '' ?>>Ativo</option>
                <option value="0" <?= $cliente['status'] == 0 ? 'selected' : '' ?>>Inativo</option>
              </select>
            </div>
            
            <div class="flex-row-gap" style="margin-top:16px">
              <a href="<?= $baseUrl ?>/clientes" class="btn btn-red">Cancelar</a>
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
  
  // Busca automática de CEP (ViaCEP)
  document.getElementById('cep').addEventListener('blur', function(e) {
    var cep = e.target.value.replace(/\D/g, '');
    if (cep.length === 8) {
      fetch('https://viacep.com.br/ws/' + cep + '/json/')
        .then(function(res) { return res.json(); })
        .then(function(data) {
          if (!data.erro) {
            document.getElementById('logradouro').value = data.logradouro || '';
            document.getElementById('bairro').value = data.bairro || '';
            document.getElementById('cidade').value = data.localidade || '';
            document.getElementById('uf').value = data.uf || '';
          }
        })
        .catch(function(err) { console.error('CEP não encontrado'); });
    }
  });
  
  // Busca automática de CNPJ (BrasilAPI)
  document.getElementById('cnpj').addEventListener('blur', function(e) {
    var cnpj = e.target.value.replace(/\D/g, '');
    if (cnpj.length === 14) {
      fetch('https://brasilapi.com.br/api/cnpj/v1/' + cnpj)
        .then(function(res) { return res.json(); })
        .then(function(data) {
          if (data.razao_social) {
            document.getElementById('nome').value = data.razao_social || '';
            document.getElementById('telefone').value = data.ddd_telefone_1 || '';
            document.getElementById('cep').value = data.cep || '';
            document.getElementById('logradouro').value = data.logradouro || '';
            document.getElementById('numero').value = data.numero || '';
            document.getElementById('complemento').value = data.complemento || '';
            document.getElementById('bairro').value = data.bairro || '';
            document.getElementById('cidade').value = data.municipio || '';
            document.getElementById('uf').value = data.uf || '';
          }
        })
        .catch(function(err) { console.error('CNPJ não encontrado'); });
    }
  });
});
</script>

<?php require dirname(__DIR__) . '/layout/footer.php'; ?>