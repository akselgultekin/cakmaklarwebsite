<?php
$phone      = setting('phone', '0 374 000 00 00');
$email      = setting('email', '');
$address    = setting('address', 'Bolu Merkez, Türkiye');
$whatsapp   = setting('whatsapp', '');
$facebook   = setting('facebook', '');
$instagram  = setting('instagram', '');
$twitter    = setting('twitter', '');
$youtube    = setting('youtube', '');
$footerText = setting('footer_text', '© ' . date('Y') . ' Çakmaklar İnşaat. Tüm hakları saklıdır.');
?>

<?php if ($whatsapp): ?>
<a href="<?= e(whatsappUrl($whatsapp)) ?>" target="_blank" rel="noopener" class="wa-float" aria-label="WhatsApp ile iletişim">
  <i class="fa-brands fa-whatsapp"></i>
</a>
<?php endif; ?>

<?php if ($whatsapp): ?>
<a href="<?= e(whatsappUrl($whatsapp)) ?>" target="_blank" rel="noopener" class="wa-float" aria-label="WhatsApp ile iletişime geçin">
  <span class="wa-tooltip">WhatsApp ile iletişime geçin</span>
  <i class="fa-brands fa-whatsapp"></i>
</a>
<?php endif; ?>

<!-- Flash Mesaj -->
<?php $flash = getFlash(); if ($flash): ?>
<div id="flashMsg" class="toast toast-<?= e($flash['type']) ?>" style="position:fixed;bottom:24px;right:24px;z-index:999;background:<?= $flash['type']==='success' ? '#138C65' : '#C0392B' ?>;color:#fff;padding:14px 22px;border-radius:12px;font-weight:600;box-shadow:0 14px 32px rgba(0,0,0,.2);max-width:360px;">
  <?= e($flash['message']) ?>
</div>
<script>setTimeout(()=>{const t=document.getElementById('flashMsg');if(t)t.remove();},4200);</script>
<?php endif; ?>

<section class="footer-cta">
  <div class="container footer-cta-grid">
    <div>
      <span class="eyebrow">Bizimle İletişime Geçin</span>
      <h2>Projeler, ilanlar ve araçlar için tek adres: Çakmaklar İnşaat.</h2>
      <p>Satılık, kiralık, 3D tur ve araç portföyünüzü incelemek için bizi arayın.</p>
    </div>
    <a class="btn" href="<?= SITE_URL ?>/iletisim">
      <i class="fa-solid fa-calendar-check"></i> İletişime Geç
    </a>
  </div>
</section>

<footer class="footer">
  <div class="container">
    <div class="footer-grid">
      <div>
        <div class="logo">
          <span class="logo-mark">Ç</span>
          <span><?= e(SITE_NAME) ?></span>
        </div>
        <p style="margin-top:18px; max-width:340px;"><?= e(setting('site_slogan', 'Güvenilir yapılar, kalıcı değerler.')) ?></p>
        <div class="socials">
          <?php if ($facebook): ?><a href="<?= e($facebook) ?>" target="_blank" rel="noopener" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a><?php endif; ?>
          <?php if ($instagram): ?><a href="<?= e($instagram) ?>" target="_blank" rel="noopener" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a><?php endif; ?>
          <?php if ($twitter): ?><a href="<?= e($twitter) ?>" target="_blank" rel="noopener" aria-label="Twitter/X"><i class="fa-brands fa-x-twitter"></i></a><?php endif; ?>
          <?php if ($youtube): ?><a href="<?= e($youtube) ?>" target="_blank" rel="noopener" aria-label="YouTube"><i class="fa-brands fa-youtube"></i></a><?php endif; ?>
          <?php if ($whatsapp): ?><a href="<?= e(whatsappUrl($whatsapp)) ?>" target="_blank" rel="noopener" aria-label="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a><?php endif; ?>
        </div>
      </div>

      <div class="footer-links">
        <strong>Menü</strong>
        <a href="<?= SITE_URL ?>/biz-kimiz">Biz Kimiz</a>
        <a href="<?= SITE_URL ?>/projeler">Projeler</a>
        <a href="<?= SITE_URL ?>/satilik">Satılık İlanlar</a>
        <a href="<?= SITE_URL ?>/kiralik">Kiralık İlanlar</a>
        <a href="<?= SITE_URL ?>/3d-ev-gez">3D Ev Gez</a>
        <a href="<?= SITE_URL ?>/haberler">Haberler</a>
      </div>

      <div class="footer-links">
        <strong>Portföy</strong>
        <a href="<?= SITE_URL ?>/satilik">Satılık Konut</a>
        <a href="<?= SITE_URL ?>/kiralik">Kiralık Konut</a>
        <a href="<?= SITE_URL ?>/ticari">Dükkan / Ofis / Arsa</a>
        <a href="<?= SITE_URL ?>/arac-ilanlari">Araç İlanları</a>
        <a href="<?= SITE_URL ?>/kat-planlari">Kat Planları</a>
        <a href="<?= SITE_URL ?>/iletisim">İletişim</a>
      </div>

      <div class="footer-contact">
        <strong>İletişim</strong>
        <?php if ($phone): ?>
        <a href="tel:<?= e(preg_replace('/[^0-9+]/', '', $phone)) ?>">
          <i class="fa-solid fa-phone"></i> <?= e($phone) ?>
        </a>
        <?php endif; ?>
        <?php if ($whatsapp): ?>
        <a href="<?= e(whatsappUrl($whatsapp)) ?>" target="_blank" rel="noopener">
          <i class="fa-brands fa-whatsapp"></i> WhatsApp
        </a>
        <?php endif; ?>
        <?php if ($email): ?>
        <a href="mailto:<?= e($email) ?>">
          <i class="fa-solid fa-envelope"></i> <?= e($email) ?>
        </a>
        <?php endif; ?>
        <?php if ($address): ?>
        <span><i class="fa-solid fa-location-dot"></i> <?= e($address) ?></span>
        <?php endif; ?>
        <span><i class="fa-regular fa-clock"></i> Hafta içi 09:00 – 18:30</span>
      </div>
    </div>

    <div class="credit">
      <span><?= e($footerText) ?></span>
      <a href="https://simetrisoft.com" target="_blank" rel="noopener" class="credit-agency">
        <i class="fa-solid fa-code"></i> Web Tasarım &amp; Yazılım: <strong>Simetri Soft</strong>
      </a>
    </div>
  </div>
</footer>
