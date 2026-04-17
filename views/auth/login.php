<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login - NovoFramework</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="<?= $baseUrl ?>/css/styles.css"/>
  <style>
    :root {
      --neon-cyan: <?= $theme['primary'] ?? '#0B6E8C' ?>;
      --neon-cyan-glow: <?= $theme['primary_glow'] ?? 'rgba(11,110,140,0.28)' ?>;
      --bg-darkest: <?= $theme['background'] ?? '#FDF2F8' ?>;
      --bg-surface: <?= $theme['surface'] ?? '#FCE7F3' ?>;
      --bg-card: #FFFFFF;
      --text-1: <?= $theme['text'] ?? '#1E1B4B' ?>;
      --text-2: <?= $theme['text'] ?? '#1E1B4B' ?>;
      --text-3: <?= $theme['text_light'] ?? '#4C1D4E' ?>;
    }
    body {
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      background: linear-gradient(135deg, var(--bg-darkest) 0%, var(--bg-surface) 100%);
      font-family: 'Montserrat', sans-serif;
      margin: 0;
    }
    .login-wrap {
      width: 100%;
      max-width: 400px;
      padding: 20px;
    }
    .login-card {
      background: var(--bg-card);
      border-radius: 20px;
      box-shadow: 0 8px 40px rgba(15,23,42,0.15);
      overflow: hidden;
    }
    .login-header {
      background: var(--neon-cyan);
      padding: 40px 32px 30px;
      text-align: center;
      position: relative;
      overflow: hidden;
    }
    .login-header::before {
      content: '';
      position: absolute;
      top: -50%;
      left: -50%;
      width: 200%;
      height: 200%;
      background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 60%);
    }
    .login-logo {
      width: 60px;
      height: 60px;
      border-radius: 16px;
      background: #fff;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 16px;
      box-shadow: 0 8px 24px var(--neon-cyan-glow);
    }
    .login-logo svg {
      width: 28px;
      height: 28px;
      color: var(--neon-cyan);
    }
    .login-title {
      font-size: 22px;
      font-weight: 700;
      color: #fff;
      margin-bottom: 4px;
    }
    .login-sub {
      font-size: 13px;
      color: rgba(255,255,255,0.8);
    }
    .login-body {
      padding: 32px;
    }
    .form-group {
      margin-bottom: 20px;
    }
    .form-label {
      display: block;
      font-size: 11px;
      font-weight: 700;
      color: var(--text-3);
      text-transform: uppercase;
      letter-spacing: 0.7px;
      margin-bottom: 8px;
    }
    .form-input {
      width: 100%;
      background: var(--bg-surface);
      border: 2px solid var(--bg-surface);
      color: var(--text-1);
      padding: 14px 16px;
      border-radius: 12px;
      font-family: 'Montserrat', sans-serif;
      font-size: 14px;
      outline: none;
      transition: border-color 0.2s, box-shadow 0.2s;
      box-sizing: border-box;
    }
    .form-input:focus {
      border-color: var(--neon-cyan);
      box-shadow: 0 0 0 3px var(--neon-cyan-glow);
    }
    .form-input::placeholder {
      color: var(--text-3);
    }
    .btn {
      padding: 12px 24px;
      border-radius: 10px;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      border: none;
      transition: all 0.2s;
      font-family: 'Montserrat', sans-serif;
    }
    .btn-cyan {
      background: var(--neon-cyan);
      color: #fff;
      box-shadow: 0 4px 20px var(--neon-cyan-glow);
    }
    .btn-cyan:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 28px var(--neon-cyan-glow);
    }
    .btn-cyan:active {
      transform: translateY(0);
    }
    .btn-block {
      width: 100%;
    }
    .error-msg {
      background: #FEE2E2;
      border: 1px solid #FECACA;
      color: #991B1B;
      padding: 12px 16px;
      border-radius: 10px;
      margin-bottom: 20px;
      font-size: 13px;
      font-weight: 500;
      display: none;
    }
    .error-msg.show {
      display: block;
    }
    .login-footer {
      text-align: center;
      padding: 0 32px 28px;
      font-size: 13px;
      color: var(--text-3);
    }
  </style>
</head>
<body>
  <div class="login-wrap">
    <div class="login-card">
      <div class="login-header">
        <div class="login-logo">
          <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
          </svg>
        </div>
        <div class="login-title">NovoFramework</div>
        <div class="login-sub">Faça login para continuar</div>
      </div>
      
      <div class="login-body">
        <div id="errorMsg" class="error-msg"></div>
        
        <form id="loginForm" method="POST" action="<?= $baseUrl ?>/auth/login">
          <input type="hidden" name="_csrf_token" value="<?= \App\Core\Csrf::getToken() ?>"/>
          
          <div class="form-group">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-input" placeholder="seu@email.com" required>
          </div>
          
          <div class="form-group">
            <label class="form-label">Senha</label>
            <input type="password" name="password" class="form-input" placeholder="••••••••" required>
          </div>
          
          <button type="submit" class="btn btn-cyan btn-block">
            Entrar
          </button>
        </form>
      </div>
      
      <div class="login-footer">
        Demo: admin@teste.com / 123456
      </div>
    </div>
  </div>

  <script>
    document.getElementById('loginForm').addEventListener('submit', async function(e) {
      e.preventDefault();
      
      const form = new FormData(this);
      const errorDiv = document.getElementById('errorMsg');
      
      errorDiv.classList.remove('show');
      
      try {
        const response = await fetch('<?= $baseUrl ?>/auth/login', {
          credentials: 'same-origin',
          method: 'POST',
          body: form
        });
        
        const data = await response.json();
        
        if (data.success) {
          window.location.href = data.redirect || '<?= $baseUrl ?>/products';
        } else {
          errorDiv.textContent = data.message || 'Erro ao fazer login';
          errorDiv.classList.add('show');
        }
      } catch (err) {
        errorDiv.textContent = 'Erro de conexão';
        errorDiv.classList.add('show');
      }
    });
  </script>
</body>
</html>