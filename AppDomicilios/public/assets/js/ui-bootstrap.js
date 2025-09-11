// Autocierre manual para alert si tiene data-timeout (por si no usas toasts)
document.addEventListener('DOMContentLoaded', function(){
  document.querySelectorAll('.alert[data-timeout]').forEach(function(el){
    const t = parseInt(el.getAttribute('data-timeout') || '0', 10);
    const sticky = el.hasAttribute('data-sticky');
    if (!sticky && t > 0) {
      setTimeout(function(){
        const alert = bootstrap.Alert.getOrCreateInstance(el);
        alert.close();
      }, t);
    }
  });
});

// Botón de carga (click individual con data-loading)
document.addEventListener('click', function(e){
  const btn = e.target.closest('[data-loading]');
  if (!btn) return;

  if (btn.getAttribute('aria-busy') === 'true') {
    e.preventDefault();
    return;
  }

  const originalHtml = btn.innerHTML;
  btn.dataset.originalHtml = originalHtml;

  const label = btn.dataset.loadingText || 'Procesando...';
  const size = btn.classList.contains('btn-sm') ? 'spinner-border-sm' : '';
  btn.innerHTML = `<span class="spinner-border ${size}" role="status" aria-hidden="true"></span>
                   <span class="ms-2">${label}</span>`;
  btn.setAttribute('aria-busy', 'true');
  btn.setAttribute('disabled', 'true');
});

// Envío de formularios → bloquear todos los submit y mostrar spinner
document.addEventListener('submit', function(e){
  const form = e.target;
  if (!(form instanceof HTMLFormElement)) return;
  if (!form.matches('[data-loading-submit]')) return;

  const submits = form.querySelectorAll('button[type="submit"], input[type="submit"]');
  submits.forEach(function(btn){
    if (btn.tagName === 'INPUT') {
      btn.setAttribute('data-original-value', btn.value);
      btn.value = 'Procesando...';
    } else {
      btn.dataset.originalHtml = btn.innerHTML;
      const size = btn.classList.contains('btn-sm') ? 'spinner-border-sm' : '';
      btn.innerHTML = `<span class="spinner-border ${size}" role="status" aria-hidden="true"></span>
                       <span class="ms-2">${btn.dataset.loadingText || 'Procesando...'}</span>`;
    }
    btn.setAttribute('aria-busy', 'true');
    btn.setAttribute('disabled', 'true');
  });
});
