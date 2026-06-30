<?php
$phone     = setting('phone', '0 374 000 00 00');
$whatsapp  = setting('whatsapp', '');
$logoPath  = setting('logo', '');
$logoSrc   = $logoPath ? uploadUrl($logoPath) : SITE_URL . '/public/assets/img/site-logo.png';
$currentUri = strtok($_SERVER['REQUEST_URI'] ?? '/', '?');
?>
<header class="header" id="header">
  <nav class="nav" aria-label="Ana menü">
    <a href="<?= SITE_URL ?>/" class="logo" aria-label="<?= e(SITE_NAME) ?> ana sayfa">
      <img class="site-logo" src="<?= e($logoSrc) ?>" alt="<?= e(SITE_NAME) ?>">
    </a>

    <div class="menu">
      <a href="<?= SITE_URL ?>/biz-kimiz" class="<?= isActivePage('/biz-kimiz') ?>">Biz Kimiz</a>

      <div class="menu-item">
        <a href="<?= SITE_URL ?>/projeler">Projeler</a>
        <div class="mega-panel" aria-label="Projeler menüsü">
          <div>
            <span class="mega-title">Proje Portföyü</span>
            <div class="mega-groups">
              <?php
              $featuredProjects = Database::query(
                "SELECT title, slug FROM projects WHERE is_active=1 AND status='satiasta' ORDER BY sort_order, id LIMIT 4"
              );
              $completedProjects = Database::query(
                "SELECT title, slug FROM projects WHERE is_active=1 AND status='teslim_edildi' ORDER BY sort_order, id LIMIT 4"
              );
              ?>
              <div class="mega-group">
                <strong>Aktif Projeler</strong>
                <?php foreach ($featuredProjects as $mp): ?>
                <a href="<?= SITE_URL ?>/projeler/<?= e($mp['slug']) ?>"><?= e($mp['title']) ?></a>
                <?php endforeach; ?>
              </div>
              <div class="mega-group">
                <strong>Tamamlanan</strong>
                <?php foreach ($completedProjects as $mp): ?>
                <a href="<?= SITE_URL ?>/projeler/<?= e($mp['slug']) ?>"><?= e($mp['title']) ?></a>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
          <a class="mega-visual" href="<?= SITE_URL ?>/projeler" aria-label="Tüm projeleri incele">
            <img src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=700&q=80" alt="Projeler" id="megaProjectImg">
          </a>
          <div class="mega-footer">
            <a href="<?= SITE_URL ?>/projeler">Tüm Projelerimiz <i class="fa-solid fa-arrow-right"></i></a>
          </div>
        </div>
      </div>

      <div class="menu-item">
        <a href="<?= SITE_URL ?>/satilik">İlanlar</a>
        <div class="mega-panel" aria-label="İlanlar menüsü">
          <div>
            <span class="mega-title">Portföy</span>
            <div class="mega-links">
              <a href="<?= SITE_URL ?>/satilik"><i class="fa-solid fa-house"></i> Satılık Konut</a>
              <a href="<?= SITE_URL ?>/kiralik"><i class="fa-solid fa-key"></i> Kiralık Konut</a>
              <a href="<?= SITE_URL ?>/ticari"><i class="fa-solid fa-store"></i> Dükkan / Ofis / Arsa</a>
              <a href="<?= SITE_URL ?>/arac-ilanlari"><i class="fa-solid fa-car"></i> Araç İlanları</a>
            </div>
          </div>
          <div class="mega-visual" style="background:var(--navy); display:flex; align-items:center; justify-content:center; color:white; font-family:Syne,sans-serif; font-size:22px; font-weight:700; text-align:center; padding:20px;">
            Satılık &amp;<br>Kiralık Portföy
          </div>
          <div class="mega-footer">
            <a href="<?= SITE_URL ?>/satilik">Tüm İlanlar <i class="fa-solid fa-arrow-right"></i></a>
          </div>
        </div>
      </div>

      <a href="<?= SITE_URL ?>/satilik" class="<?= isActivePage('/satilik') ?>">Satılık</a>
      <a href="<?= SITE_URL ?>/kiralik" class="<?= isActivePage('/kiralik') ?>">Kiralık</a>
      <a href="<?= SITE_URL ?>/arac-ilanlari" class="<?= isActivePage('/arac-ilanlari') ?>">Araç İlanları</a>
      <a href="<?= SITE_URL ?>/3d-ev-gez" class="<?= isActivePage('/3d-ev-gez') ?>">3D Ev Gez</a>
<a href="<?= SITE_URL ?>/iletisim" class="<?= isActivePage('/iletisim') ?>">İletişim</a>
    </div>

    <div class="nav-actions">
      <a href="tel:<?= e(preg_replace('/[^0-9+]/', '', $phone)) ?>" class="phone">
        <i class="fa-solid fa-phone"></i> <?= e($phone) ?>
      </a>
      <a class="btn header-projects" href="<?= SITE_URL ?>/projeler">
        <i class="fa-solid fa-building"></i> Projelerimiz
      </a>
      <button class="icon-btn hamburger" id="hamburger" aria-label="Mobil menüyü aç">☰</button>
    </div>
  </nav>

  <div class="mobile-panel" id="mobilePanel">
    <a href="<?= SITE_URL ?>/biz-kimiz">Biz Kimiz</a>
    <a href="<?= SITE_URL ?>/projeler">Projeler</a>
    <a href="<?= SITE_URL ?>/satilik">Satılık İlanlar</a>
    <a href="<?= SITE_URL ?>/kiralik">Kiralık İlanlar</a>
    <a href="<?= SITE_URL ?>/ticari">Dükkan / Ofis / Arsa</a>
    <a href="<?= SITE_URL ?>/arac-ilanlari">Araç İlanları</a>
    <a href="<?= SITE_URL ?>/3d-ev-gez">3D Ev Gez</a>
    <a href="<?= SITE_URL ?>/haberler">Haberler</a>
    <a href="<?= SITE_URL ?>/iletisim">İletişim</a>
    <a href="tel:<?= e(preg_replace('/[^0-9+]/', '', $phone)) ?>" class="phone">
      <i class="fa-solid fa-phone"></i> <?= e($phone) ?>
    </a>
    <?php if ($whatsapp): ?>
    <a class="btn" href="<?= e(whatsappUrl($whatsapp, 'Merhaba, bilgi almak istiyorum.')) ?>" target="_blank">
      <i class="fa-brands fa-whatsapp"></i> WhatsApp
    </a>
    <?php endif; ?>
  </div>
</header>
