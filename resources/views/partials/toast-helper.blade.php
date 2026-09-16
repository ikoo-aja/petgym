<!-- Custom Toast Notification System (bukan popup bawaan browser) -->
<div id="toast-container" style="position: fixed; top: 20px; right: 20px; z-index: 999999; display: flex; flex-direction: column; gap: 12px;"></div>

<style>
  .custom-toast {
    min-width: 320px;
    max-width: 400px;
    background: #fff;
    border-radius: 8px;
    padding: 16px 20px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    display: flex;
    align-items: flex-start;
    gap: 12px;
    transform: translateX(120%);
    transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    border-left: 5px solid #888;
  }
  .custom-toast.show { transform: translateX(0); }
  .custom-toast.toast-success { border-left-color: #28a745; }
  .custom-toast.toast-error { border-left-color: #dc3545; }
  .custom-toast.toast-warning { border-left-color: #f59e0b; }
  .custom-toast.toast-info { border-left-color: #17a2b8; }

  .custom-toast-icon { font-size: 20px; margin-top: 2px; }
  .toast-success .custom-toast-icon { color: #28a745; }
  .toast-error .custom-toast-icon { color: #dc3545; }
  .toast-warning .custom-toast-icon { color: #f59e0b; }
  .toast-info .custom-toast-icon { color: #17a2b8; }

  .custom-toast-content { flex: 1; }
  .custom-toast-title { font-weight: 800; color: #1a1a1a; font-size: 14px; margin-bottom: 2px; }
  .custom-toast-message { color: #666; font-size: 12px; line-height: 1.4; word-wrap: break-word; }
  .custom-toast-close { background: none; border: none; color: #aaa; cursor: pointer; font-size: 18px; padding: 0; line-height: 1; transition: color 0.2s; }
  .custom-toast-close:hover { color: #666; }
</style>

<script>
  // 1. Toast Notification System (custom, bukan window.alert bawaan browser)
  function showToast(title, message, type = 'success') {
    const container = document.getElementById('toast-container');
    if (!container) return;

    let iconClass = 'icon-check';
    if (type === 'error') iconClass = 'icon-exclamation-circle';
    if (type === 'warning') iconClass = 'icon-exclamation-circle';
    if (type === 'info') iconClass = 'icon-search';

    const toast = document.createElement('div');
    toast.className = `custom-toast toast-${type}`;
    toast.innerHTML = `
      <span class="${iconClass} custom-toast-icon"></span>
      <div class="custom-toast-content">
        <div class="custom-toast-title">${title}</div>
        <div class="custom-toast-message">${message}</div>
      </div>
      <button class="custom-toast-close">&times;</button>
    `;

    container.appendChild(toast);

    // Trigger animation
    setTimeout(() => toast.classList.add('show'), 50);

    const closeToast = () => {
      toast.classList.remove('show');
      setTimeout(() => toast.remove(), 450);
    };

    toast.querySelector('.custom-toast-close').addEventListener('click', closeToast);

    // Auto remove after 4.5 seconds
    setTimeout(closeToast, 4500);
  }

  // Catatan: flash session (success/error/warning) ditampilkan oleh
  // partials/flash-toast — di sini hanya definisi fungsi showToast.
</script>