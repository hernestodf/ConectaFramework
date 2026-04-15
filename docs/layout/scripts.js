// ============ SIDEBAR ============
function toggleSidebar() {
  document.body.classList.toggle('mini');
}

// ============ SUBMENU ============
function toggleSub(id, el) {
  if (document.body.classList.contains('mini')) return;
  var sub = document.getElementById('sub-' + id),
      chv = el.querySelector('.chv'),
      isOpen = sub.classList.contains('open');
  document.querySelectorAll('.sub').forEach(function(s) { s.classList.remove('open'); });
  document.querySelectorAll('.chv').forEach(function(c) { c.classList.remove('open'); });
  if (!isOpen) { sub.classList.add('open'); chv.classList.add('open'); }
}

// ============ SECTION NAVIGATION ============
function showSection(id, el) {
  document.querySelectorAll('.section').forEach(function(s) { s.classList.remove('active'); });
  document.querySelectorAll('.ni,.si').forEach(function(n) { n.classList.remove('active'); });
  document.getElementById('sec-' + id).classList.add('active');
  if (el) el.classList.add('active');
  var titles = {
    dashboard:'Dashboard', accordions:'Accordions', alerts:'Alerts', buttons:'Buttons',
    badges:'Badges', cards:'Cards', carousel:'Carousel', icons:'Icons', listitems:'List Items',
    modals:'Modals', progress:'Progress', popovers:'Popovers', tabs:'Tabs', tooltips:'Tooltips',
    typography:'Typography', forminputs:'Form Inputs', checkboxradio:'Checkbox & Radio',
    fileinput:'File Input', validations:'Validations', datetime:'Date Time',
    invoice:'Invoice', calendar:'Calendar', spinners:'Spinners', stepper:'Stepper',
    timeline:'Timeline', ratings:'Ratings', avatars:'Avatars', tables:'Tables',
    chips:'Chips & Tags', skeleton:'Skeleton'
  };
  document.getElementById('breadcrumb-cur').textContent = titles[id] || id;
  if (id === 'progress') initProgressBars();
}

// ============ MODALS ============
function openModal(id) { document.getElementById(id).classList.add('open'); document.body.style.overflow = 'hidden'; }
function closeModal(id) { document.getElementById(id).classList.remove('open'); document.body.style.overflow = ''; }
function closeModalOutside(e, id) { if (e.target === document.getElementById(id)) closeModal(id); }

// ============ POPOVERS ============
var openPop = null;
function togglePop(id) {
  var el = document.getElementById(id);
  if (openPop && openPop !== el) { openPop.classList.remove('open'); openPop = null; }
  el.classList.toggle('open');
  openPop = el.classList.contains('open') ? el : null;
}
document.addEventListener('click', function(e) {
  if (openPop && !e.target.closest('.popover-wrap')) { openPop.classList.remove('open'); openPop = null; }
});

// ============ TOASTS — FIXED ============
var TOAST_ICONS = {
  red:    '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
  green:  '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
  cyan:   '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
  yellow: '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>',
  purple: '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>',
  orange: '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>'
};

function showToast(type, title, msg, duration) {
  var container = document.getElementById('toast-container');
  if (!container) { container = document.createElement('div'); container.id = 'toast-container'; document.body.appendChild(container); }
  var t = document.createElement('div');
  t.className = 'toast ' + (type || 'cyan');
  t.innerHTML =
    '<div class="toast-icon">' + (TOAST_ICONS[type] || TOAST_ICONS.cyan) + '</div>' +
    '<div class="toast-body"><div class="toast-title">' + (title || 'Notificação') + '</div>' +
    (msg ? '<div class="toast-msg">' + msg + '</div>' : '') + '</div>' +
    '<button class="toast-close" onclick="dismissToast(this.parentElement)">' +
    '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>';
  container.prepend(t);
  requestAnimationFrame(function() { requestAnimationFrame(function() { t.classList.add('show'); }); });
  t._timer = setTimeout(function() { dismissToast(t); }, duration || 4500);
}

function dismissToast(t) {
  if (!t || t._dismissed) return;
  t._dismissed = true;
  clearTimeout(t._timer);
  t.classList.remove('show');
  t.classList.add('hide');
  setTimeout(function() { if (t.parentElement) t.parentElement.removeChild(t); }, 400);
}

// ============ COLORED TABS ============
function switchH(g, i, el, color) {
  var parent = el.parentElement;
  parent.querySelectorAll('[class^="ht-"]').forEach(function(t) {
    t.classList.remove('active');
    ['red','cyan','green','yellow','purple'].forEach(function(c) { t.classList.remove(c); });
  });
  el.classList.add('active');
  if (color) el.classList.add(color);
  var pfx = { c: 'hc' }[g];
  parent.querySelectorAll('[class^="ht-"]').forEach(function(j) {
    var p = document.getElementById(pfx + '-' + Array.from(parent.querySelectorAll('[class^="ht-"]')).indexOf(j));
    if (p) p.classList.remove('active');
  });
  var tgt = document.getElementById(pfx + '-' + i);
  if (tgt) tgt.classList.add('active');
}

// ============ CAROUSEL ============
var carouselIndex = 0;
function moveCarousel(dir) { carouselIndex = (carouselIndex + dir + 3) % 3; updateCarousel(); }
function goToSlide(i) { carouselIndex = i; updateCarousel(); }
function updateCarousel() {
  document.querySelector('#demo-carousel .carousel-track').style.transform = 'translateX(-' + (carouselIndex * 100) + '%)';
  document.querySelectorAll('#demo-carousel .carousel-dot').forEach(function(d, i) { d.classList.toggle('active', i === carouselIndex); });
}

// ============ LEFT OFF-CANVAS ============
function openLeft() { document.getElementById('left-canvas').classList.add('open'); document.getElementById('overlay-left').classList.add('active'); document.body.style.overflow = 'hidden'; }
function closeLeft() { document.getElementById('left-canvas').classList.remove('open'); document.getElementById('overlay-left').classList.remove('active'); document.body.style.overflow = ''; }
function selectChip(el) { var g = el.parentElement; g.querySelectorAll('.lc-chip').forEach(function(c) { c.classList.remove('active'); }); el.classList.add('active'); }

// ============ RIGHT OFF-CANVAS ============
function openRight() { document.getElementById('right-canvas').classList.add('open'); document.getElementById('overlay-right').classList.add('active'); document.body.style.overflow = 'hidden'; }
function closeRight() { document.getElementById('right-canvas').classList.remove('open'); document.getElementById('overlay-right').classList.remove('active'); document.body.style.overflow = ''; }
function switchRcTab(id, el) {
  document.querySelectorAll('.rc-tab').forEach(function(t) { t.classList.remove('active'); });
  document.querySelectorAll('.rc-pane').forEach(function(p) { p.classList.remove('active'); });
  el.classList.add('active');
  document.getElementById('rc-' + id).classList.add('active');
}

// ============ PROGRESS BARS ============
function initProgressBars() {
  document.querySelectorAll('.prog-bar[data-w]').forEach(function(b) {
    b.style.transition = 'none';
    b.style.width = '0';
    requestAnimationFrame(function() {
      setTimeout(function() {
        b.style.transition = 'width 1.1s cubic-bezier(.25,.8,.25,1)';
        b.style.width = b.dataset.w;
      }, 80);
    });
  });
}

// ============ STEPPER ============
var stepperCurrent = 1;
var stepperTotal = 4;

function stepNext() {
  if (stepperCurrent < stepperTotal) { stepperCurrent++; updateStepper(); }
  else { showToast('green','Stepper Concluído!','Todos os passos foram completados.'); stepperCurrent = 1; updateStepper(); }
}

function stepPrev() { if (stepperCurrent > 1) { stepperCurrent--; updateStepper(); } }

function updateStepper() {
  document.querySelectorAll('.step-item').forEach(function(item, idx) {
    var n = idx + 1;
    item.classList.remove('active','done');
    if (n < stepperCurrent) item.classList.add('done');
    else if (n === stepperCurrent) item.classList.add('active');
  });
  document.querySelectorAll('.step-pane').forEach(function(p, idx) {
    p.classList.toggle('active', idx + 1 === stepperCurrent);
  });
  var prevBtn = document.getElementById('step-prev-btn');
  var nextBtn = document.getElementById('step-next-btn');
  if (prevBtn) prevBtn.disabled = stepperCurrent === 1;
  if (nextBtn) nextBtn.textContent = stepperCurrent === stepperTotal ? 'Concluir ✓' : 'Próximo →';
}

// ============ RATING ============
function setRating(el, val) {
  var group = el.closest('.rating-group');
  group.querySelectorAll('.star').forEach(function(s, i) { s.classList.toggle('active', i < val); });
  group.dataset.rating = val;
}
function hoverRating(el, val) {
  var group = el.closest('.rating-group');
  group.querySelectorAll('.star').forEach(function(s, i) { s.classList.toggle('hover', i < val); });
}
function leaveRating(el) { el.closest('.rating-group').querySelectorAll('.star').forEach(function(s) { s.classList.remove('hover'); }); }

// ============ TABLE SORT ============
function sortTable(colIdx, btn) {
  var table = btn.closest('table');
  var tbody = table.querySelector('tbody');
  var rows = Array.from(tbody.querySelectorAll('tr'));
  var asc = btn.dataset.sort !== 'asc';
  table.querySelectorAll('.th-sort').forEach(function(b) { b.dataset.sort = ''; b.querySelector('.sort-ico').textContent = '↕'; });
  btn.dataset.sort = asc ? 'asc' : 'desc';
  btn.querySelector('.sort-ico').textContent = asc ? '↑' : '↓';
  rows.sort(function(a, b) {
    var va = a.cells[colIdx].textContent.trim(), vb = b.cells[colIdx].textContent.trim();
    var na = parseFloat(va.replace(/[^0-9.,]/g,'').replace(',','.')), nb = parseFloat(vb.replace(/[^0-9.,]/g,'').replace(',','.'));
    if (!isNaN(na) && !isNaN(nb)) return asc ? na - nb : nb - na;
    return asc ? va.localeCompare(vb,'pt-BR') : vb.localeCompare(va,'pt-BR');
  });
  rows.forEach(function(r) { tbody.appendChild(r); });
}

// ============ CHIPS ============
function toggleChipSel(el) { el.classList.toggle('selected'); }
function removeChip(btn) { btn.closest('.chip-removable').remove(); }

// ============ SKELETON LOADER ============
function loadSkeleton() {
  var btn = document.getElementById('skeleton-load-btn');
  btn.disabled = true; btn.textContent = 'Carregando...';
  document.getElementById('skeleton-demo').classList.add('loading');
  setTimeout(function() {
    document.getElementById('skeleton-demo').classList.remove('loading');
    btn.disabled = false; btn.textContent = 'Simular Carregamento';
    showToast('green','Conteúdo carregado!','Os dados foram exibidos com sucesso.');
  }, 2200);
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
  initProgressBars();
  updateStepper();
});
