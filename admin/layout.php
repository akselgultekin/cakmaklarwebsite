<?php
// Admin layout - tüm admin sayfaları bu dosyayı include eder
// $pageTitle ve $activeModule değişkenleri modülden gelmeli

$pageTitle    = $pageTitle ?? 'Admin Panel';
$activeModule = $activeModule ?? 'dashboard';
$adminName    = $_SESSION['admin_name'] ?? 'Admin';
$adminInitial = strtoupper(mb_substr($adminName, 0, 1));
$logoSrc      = setting('logo') ? uploadUrl(setting('logo')) : SITE_URL . '/public/assets/img/site-logo.png';

$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($pageTitle) ?> | <?= e(SITE_NAME) ?> Admin</title>
  <meta name="robots" content="noindex,nofollow">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Syne:wght@600;700;800&family=Elms+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <link rel="stylesheet" href="<?= SITE_URL ?>/admin/assets/css/admin.css">
</head>
<body>
<div class="admin-layout">

  <!-- ═══ SIDEBAR ═══════════════════════════════════════════════ -->
  <aside class="sidebar">
    <div class="sidebar-brand">
      <img src="<?= e($logoSrc) ?>" alt="<?= e(SITE_NAME) ?>" onerror="this.style.display='none'">
      <span><?= e(SITE_NAME) ?><br><small style="font-family:sans-serif;font-size:11px;font-weight:400;color:rgba(255,255,255,.5);">Yönetim Paneli</small></span>
    </div>

    <nav class="sidebar-nav">
      <div class="nav-section">
        <div class="nav-section-title">Genel</div>
        <a class="nav-link <?= $activeModule==='dashboard' ? 'active' : '' ?>" href="<?= ADMIN_URL ?>/?module=dashboard">
          <i class="fa-solid fa-gauge"></i> Dashboard
        </a>
        <a class="nav-link <?= $activeModule==='settings' ? 'active' : '' ?>" href="<?= ADMIN_URL ?>/?module=settings">
          <i class="fa-solid fa-gear"></i> Site Ayarları
        </a>
        <a class="nav-link <?= $activeModule==='sliders' ? 'active' : '' ?>" href="<?= ADMIN_URL ?>/?module=sliders">
          <i class="fa-solid fa-images"></i> Slider
        </a>
      </div>

      <div class="nav-section">
        <div class="nav-section-title">Portföy</div>
        <a class="nav-link <?= $activeModule==='projects' ? 'active' : '' ?>" href="<?= ADMIN_URL ?>/?module=projects">
          <i class="fa-solid fa-building"></i> Projeler
        </a>
        <a class="nav-link <?= $activeModule==='listings' ? 'active' : '' ?>" href="<?= ADMIN_URL ?>/?module=listings">
          <i class="fa-solid fa-house"></i> İlanlar
        </a>
        <a class="nav-link <?= $activeModule==='vehicles' ? 'active' : '' ?>" href="<?= ADMIN_URL ?>/?module=vehicles">
          <i class="fa-solid fa-car"></i> Araç İlanları
        </a>
      </div>

      <div class="nav-section">
        <div class="nav-section-title">İçerik</div>
        <a class="nav-link <?= $activeModule==='news' ? 'active' : '' ?>" href="<?= ADMIN_URL ?>/?module=news">
          <i class="fa-solid fa-newspaper"></i> Haberler
        </a>
        <a class="nav-link <?= $activeModule==='pages' ? 'active' : '' ?>" href="<?= ADMIN_URL ?>/?module=pages">
          <i class="fa-solid fa-file-lines"></i> Sayfa İçerikleri
        </a>
        <a class="nav-link <?= $activeModule==='media' ? 'active' : '' ?>" href="<?= ADMIN_URL ?>/?module=media">
          <i class="fa-solid fa-folder-image"></i> Medya
        </a>
      </div>

      <div class="nav-section">
        <div class="nav-section-title">Sistem</div>
        <a class="nav-link <?= $activeModule==='forms' ? 'active' : '' ?>" href="<?= ADMIN_URL ?>/?module=forms">
          <i class="fa-solid fa-inbox"></i> Form Başvuruları
          <?php
          $unread = Database::queryOne("SELECT COUNT(*) AS cnt FROM contact_messages WHERE is_read=0");
          if ($unread && $unread['cnt'] > 0):
          ?>
          <span style="margin-left:auto;background:var(--teal-2);color:#fff;border-radius:999px;padding:2px 7px;font-size:11px;font-weight:700;">
            <?= $unread['cnt'] ?>
          </span>
          <?php endif; ?>
        </a>
        <a class="nav-link <?= $activeModule==='users' ? 'active' : '' ?>" href="<?= ADMIN_URL ?>/?module=users">
          <i class="fa-solid fa-users-gear"></i> Kullanıcılar
        </a>
      </div>
    </nav>

    <div class="sidebar-footer">
      <a href="<?= SITE_URL ?>/" target="_blank"><i class="fa-solid fa-arrow-up-right-from-square"></i> Siteyi Gör</a>
      <a href="<?= ADMIN_URL ?>/logout.php"><i class="fa-solid fa-right-from-bracket"></i> Çıkış</a>
    </div>
  </aside>

  <!-- ═══ MAIN ══════════════════════════════════════════════════ -->
  <div class="admin-main">
    <!-- Topbar -->
    <header class="topbar">
      <span class="topbar-title"><?= e($pageTitle) ?></span>
      <div class="topbar-actions">
        <div class="topbar-user">
          <div class="topbar-avatar"><?= e($adminInitial) ?></div>
          <span><?= e($adminName) ?></span>
        </div>
      </div>
    </header>

    <!-- Flash mesaj -->
    <?php if ($flash): ?>
    <div style="padding:0 28px;margin-top:18px;">
      <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'error' ?>">
        <i class="fa-solid fa-<?= $flash['type'] === 'success' ? 'circle-check' : 'circle-exclamation' ?>"></i>
        <?= e($flash['message']) ?>
      </div>
    </div>
    <?php endif; ?>

    <!-- Page Content -->
    <div class="page-content">
      <?= $pageContent ?? '' ?>
    </div>
  </div>
</div>

<script src="<?= SITE_URL ?>/admin/assets/js/admin.js"></script>
</body>
</html>
