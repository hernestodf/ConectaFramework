<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= $title ?? ($appTitle ?? 'App') ?></title>
  <meta name="description" content="<?= $appName ?? 'App' ?>"/>
  
  <!-- CSS Variables do Tema -->
  <?php if (!empty($theme)): ?>
  <style>
    :root {
      --tb-h: 60px;
      --neon-cyan: <?= $theme['primary'] ?> !important;
      --neon-cyan-glow: <?= $theme['primary_glow'] ?> !important;
      --bg-dark: <?= $theme['sidebar_bg'] ?> !important;
      --bg-surface: <?= $theme['surface'] ?> !important;
      --bg-darkest: <?= $theme['background'] ?> !important;
      --text-1: <?= $theme['text'] ?> !important;
      --text-2: <?= $theme['text'] ?> !important;
      --text-3: <?= $theme['text_light'] ?> !important;
      --bg-hover: <?= $theme['sidebar_hover'] ?> !important;
    }
    .ni:hover, .nav-item:hover, .si:hover { background: <?= $theme['sidebar_hover'] ?> !important; }
  </style>
  <?php endif; ?>
  
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
  <meta name="theme-color" content="<?= $theme['primary'] ?? '#1D4ED8' ?>"/>
  <link rel="stylesheet" href="<?= $baseUrl ?>/css/styles.css"/>
  <script>
const BASE_URL = '<?= $baseUrl ?>';
const CSRF_TOKEN = '<?= \App\Core\Csrf::getToken() ?>';

function toggleSidebar() { document.body.classList.toggle('mini'); }
function toggleSub(el) { 
  var sub = el.nextElementSibling;
  if (document.body.classList.contains('mini')) return;
  sub.classList.toggle('open'); 
}

function openRight() { document.getElementById('offcanvas-right').classList.add('open');document.getElementById('overlay-right').classList.add('open'); }
function closeRight() { document.getElementById('offcanvas-right').classList.remove('open');document.getElementById('overlay-right').classList.remove('open'); }

function openMobileSidebar() { document.getElementById('offcanvas-left').classList.add('open');document.getElementById('overlay-left').classList.add('open'); }
function closeMobileSidebar() { document.getElementById('offcanvas-left').classList.remove('open');document.getElementById('overlay-left').classList.remove('open'); }

function closeLeft() { closeMobileSidebar(); }
function handleResponsive() {}
</script>

function toggleUser(id, btn) {
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
}
  </script>
  
  <!-- Favicon dinâmico baseado no tema -->
  <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect width='100' height='100' rx='20' fill='<?= $theme['primary'] ?? '#0B6E8C' ?>'/><text x='50' y='65' font-size='50' text-anchor='middle' fill='white' font-family='Arial'><?= $appLogoText ?? 'A' ?></text></svg>"/>
</head>
<body>

<!-- Mobile Menu Button -->
<button class="mobile-menu-btn" onclick="openMobileSidebar()" id="mobile-menu-btn">
  <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
  </svg>
</button>

<!-- Overlays -->
<div class="sidebar-overlay" id="overlay-left" onclick="closeLeft()"></div>
<div class="sidebar-overlay" id="overlay-right" onclick="closeRight()"></div>

<!-- Off-canvas Left (Mobile Sidebar) -->
<div class="offcanvas offcanvas-left" id="offcanvas-left">
  <div class="offcanvas-header">
    <div class="logo-wrap">
      <?php if (!empty($appLogo)): ?>
      <img src="<?= $appLogo ?>" class="logo-img" alt="<?= $appName ?>"/>
      <?php else: ?>
      <div class="logo-ico">
        <?= $appLogoText ?? 'A' ?>
      </div>
      <?php endif; ?>
      <div>
        <div class="logo-name"><?= $appName ?></div>
        <div class="logo-ver">v<?= $appVersion ?? '1.0.0' ?></div>
      </div>
    </div>
    <button class="offcanvas-close" onclick="closeMobileSidebar()">
      <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
      </svg>
    </button>
  </div>
  <div class="offcanvas-body">
    <!-- Quick Links Mobile -->
    <div class="mobile-quick-links">
      <a href="/" class="quick-link active">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
        Home
      </a>
      <a href="/test" class="quick-link">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
          <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
        </svg>
        API Test
      </a>
    </div>
    <div class="mobile-divider"></div>
  </div>
</div>