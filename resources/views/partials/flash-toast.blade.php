@php
  $pgFlashSuccess = session('success');
  $pgFlashError = session('error');
@endphp

<div id="pg-toast-container" aria-live="polite" aria-atomic="true"></div>

<style>
  #pg-toast-container {
    position: fixed;
    top: 24px;
    right: 24px;
    z-index: 999999;
    display: flex;
    flex-direction: column;
    gap: 12px;
    pointer-events: none;
  }
  .pg-toast {
    pointer-events: auto;
    position: relative;
    min-width: 300px;
    max-width: 420px;
    background: #ffffff;
    border: 1px solid #eef2f6;
    border-radius: 12px;
    padding: 15px 18px 17px;
    box-shadow: 0 12px 40px rgba(17, 24, 39, 0.14), 0 2px 8px rgba(17, 24, 39, 0.06);
    display: flex;
    align-items: flex-start;
    gap: 12px;
    overflow: hidden;
    transform: translateX(120%);
    opacity: 0;
    transition: transform 0.45s cubic-bezier(0.68, -0.55, 0.265, 1.55), opacity 0.35s ease;
  }
  .pg-toast.pg-show {
    transform: translateX(0);
    opacity: 1;
  }
  .pg-toast.pg-hide {
    transform: translateX(120%);
    opacity: 0;
    transition: transform 0.35s ease, opacity 0.3s ease;
  }
  .pg-toast-icon {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 17px;
    font-weight: 700;
    line-height: 1;
  }
  .pg-toast.pg-success .pg-toast-icon { background: #ecfdf5; color: #059669; }
  .pg-toast.pg-error .pg-toast-icon { background: #fef2f2; color: #dc2626; }
  .pg-toast.pg-info .pg-toast-icon { background: #eff6ff; color: #2563eb; }
  .pg-toast.pg-warning .pg-toast-icon { background: #fffbeb; color: #d97706; }

  .pg-toast-body {
    flex: 1;
    min-width: 0;
    padding-top: 1px;
  }
  .pg-toast-title {
    font-weight: 800;
    font-size: 14px;
    color: #111827;
    margin: 0 0 2px;
  }
  .pg-toast-message {
    font-size: 12.5px;
    color: #6b7280;
    line-height: 1.45;
    margin: 0;
    word-wrap: break-word;
  }
  .pg-toast-progress {
    position: absolute;
    left: 0;
    bottom: 0;
    height: 3px;
    width: 100%;
    background: #f1f5f9;
  }
  .pg-toast-progress span {
    display: block;
    height: 100%;
    width: 100%;
    transform-origin: left;
    transform: scaleX(1);
  }
  .pg-toast.pg-success .pg-toast-progress span { background: #10b981; }
  .pg-toast.pg-error .pg-toast-progress span { background: #ef4444; }
  .pg-toast.pg-info .pg-toast-progress span { background: #3b82f6; }
  .pg-toast.pg-warning .pg-toast-progress span { background: #f59e0b; }
</style>

<script>
  (function () {
    if (!document.getElementById('pg-toast-keyframes')) {
      var keyframes = document.createElement('style');
      keyframes.id = 'pg-toast-keyframes';
      keyframes.textContent = '@keyframes pg-toast-progress { from { transform: scaleX(1); } to { transform: scaleX(0); } }';
      document.head.appendChild(keyframes);
    }

    var icons = { success: '✓', error: '!', info: 'i', warning: '!' };

    window.pgShowToast = function (title, message, type, duration) {
      type = type || 'success';
      duration = duration || 4500;

      var container = document.getElementById('pg-toast-container');
      if (!container) return;

      var toast = document.createElement('div');
      toast.className = 'pg-toast pg-' + type;

      var icon = icons[type] || icons.success;

      toast.innerHTML =
        '<div class="pg-toast-icon">' + icon + '</div>' +
        '<div class="pg-toast-body">' +
          '<p class="pg-toast-title"></p>' +
          '<p class="pg-toast-message"></p>' +
        '</div>' +
        '<div class="pg-toast-progress"><span style="animation: pg-toast-progress ' + duration + 'ms linear forwards;"></span></div>';

      toast.querySelector('.pg-toast-title').textContent = title;
      toast.querySelector('.pg-toast-message').textContent = message;

      container.appendChild(toast);

      requestAnimationFrame(function () {
        toast.classList.add('pg-show');
      });

      var closed = false;
      function closeToast() {
        if (closed) return;
        closed = true;
        toast.classList.remove('pg-show');
        toast.classList.add('pg-hide');
        setTimeout(function () { toast.remove(); }, 400);
      }

      setTimeout(closeToast, duration);
    };
  })();
</script>

@if($pgFlashSuccess)
<script>
  document.addEventListener('DOMContentLoaded', function () {
    pgShowToast('Sukses', @json($pgFlashSuccess), 'success');
  });
</script>
@endif

@if($pgFlashError)
<script>
  document.addEventListener('DOMContentLoaded', function () {
    pgShowToast('Peringatan', @json($pgFlashError), 'error');
  });
</script>
@endif
