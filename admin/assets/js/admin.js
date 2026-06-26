/* ══════════════════════════════════════════════════════════════
   Çakmaklar İnşaat - Admin Panel JS
   ══════════════════════════════════════════════════════════════ */

(function () {
  'use strict';

  // ── Flash Alert Auto-Dismiss ───────────────────────────────
  document.querySelectorAll('.alert').forEach(function (el) {
    setTimeout(function () {
      el.style.transition = 'opacity .4s';
      el.style.opacity = '0';
      setTimeout(function () { el.remove(); }, 400);
    }, 5000);
  });

  // ── Image Preview on File Select ───────────────────────────
  document.querySelectorAll('input[type="file"][accept*="image"]').forEach(function (input) {
    if (input.multiple) return; // galeri multi-input'u atla
    input.addEventListener('change', function () {
      if (!this.files[0]) return;
      const reader = new FileReader();
      reader.onload = function (e) {
        let preview = input.parentElement.querySelector('.img-preview');
        if (!preview) {
          preview = document.createElement('img');
          preview.className = 'img-preview img-preview-lg';
          preview.style.marginBottom = '10px';
          input.parentElement.insertBefore(preview, input);
        }
        preview.src = e.target.result;
        preview.style.display = 'block';
      };
      reader.readAsDataURL(this.files[0]);
    });
  });

  // ── Auto-Slug from Title ────────────────────────────────────
  const titleInput = document.querySelector('input[name="title"]');
  const slugInput  = document.querySelector('input[name="slug"]');
  if (titleInput && slugInput && !slugInput.value) {
    titleInput.addEventListener('input', function () {
      if (!slugInput.dataset.userEdited) {
        slugInput.value = turkishSlugify(this.value);
      }
    });
    slugInput.addEventListener('input', function () {
      this.dataset.userEdited = '1';
    });
  }

  function turkishSlugify(str) {
    const map = { 'ç':'c','ğ':'g','ı':'i','ö':'o','ş':'s','ü':'u','Ç':'c','Ğ':'g','İ':'i','Ö':'o','Ş':'s','Ü':'u' };
    return str.toLowerCase()
      .replace(/[çğışöüÇĞİŞÖÜ]/g, function (m) { return map[m] || m; })
      .replace(/[^a-z0-9\s-]/g, '')
      .replace(/\s+/g, '-')
      .replace(/-+/g, '-')
      .trim('-');
  }

  // ── Confirm Delete via data-confirm ───────────────────────
  document.addEventListener('click', function (e) {
    const el = e.target.closest('[data-confirm]');
    if (!el) return;
    if (!confirm(el.dataset.confirm)) e.preventDefault();
  });

  // ── Toggle checkbox visual on label click ─────────────────
  // Already handled natively; this ensures label-wrapped toggles reflect state
  document.querySelectorAll('.toggle input[type="checkbox"]').forEach(function (cb) {
    cb.addEventListener('change', function () {
      // visual update handled by CSS :checked selector
    });
  });

  // ── Gallery delete checkbox highlight ─────────────────────
  document.querySelectorAll('input[name="delete_images[]"]').forEach(function (cb) {
    cb.addEventListener('change', function () {
      const wrapper = this.closest('[style*="position:relative"]');
      if (wrapper) {
        wrapper.style.opacity = this.checked ? '0.4' : '1';
        wrapper.style.outline = this.checked ? '2px solid #C0392B' : '';
      }
    });
  });

  // ── Price formatting ───────────────────────────────────────
  const priceInput = document.querySelector('input[name="price"]');
  if (priceInput) {
    priceInput.addEventListener('blur', function () {
      const raw = this.value.replace(/[^\d,.]/g, '').replace(',', '.');
      const num  = parseFloat(raw);
      if (!isNaN(num)) this.value = num.toString();
    });
  }

  // ── Sidebar active link (already server-rendered, but update on SPA if needed)
  // Static HTML sidebar active class is handled by PHP $activeModule comparison

})();
